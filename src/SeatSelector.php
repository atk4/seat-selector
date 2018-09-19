<?php
namespace atk4\seat_selector;

use atk4\data\Exception;
use atk4\data\Model;
use atk4\ui\Callback;
use atk4\ui\Template;
use atk4\ui\View;

class SeatSelector extends View {

    /**
     * Template to use with this view.
     * @var string
     */
    public $seatSelectorTemplate = 'seat-selector.html';

    /**
     * The seat view to embed svg file.
     * @var null
     */
    public $seatView = null;

    public $seatViewTemplate = null;

    /**
     * @var Callback
     */
    public $callback;

    /**
     * SeatSelector jQuery plugin setup.
     * see seat-selector.js file for all settings.
     *
     * @var array JavaScript settings
     */
    public $settings = [];

    /**
     * @var null Venue svg file indluding full path.
     */
    public $venue = null;

    /**
     * @var int qty allow to buy during seat selection,
     */
    public $qty = 1;

    /**
     * An array of already selected seats.
     *
     * @var array
     */
    public $takenSeats = [];

    /**
     * Notify user about seat error.
     *
     * @var string
     */
    public $seatErrorMsg = 'Some of your selected seat are no longer available. Please select new seats.';

    /**
     * Add confirm button or not.
     * If not, you need to handle seat confirmation action
     * via jsConfirmSeat method.
     *
     * @var bool
     */
    public $hasConfirmBtn = false;

    public $btnClearLabel = 'Clear seats';
    public $btnConfirmLabel = 'Confirm seats';

    /**
     * @throws \atk4\ui\Exception
     */
    public function init() {
        $this->defaultTemplate = dirname(__DIR__).'/template/'.$this->seatSelectorTemplate;
        parent::init();

        $this->callback = $this->add('jsCallback');

        if (!$this->seatView) {
            if (!$this->seatViewTemplate) {
                $this->seatViewTemplate = '<div id="{$_id}" class="{$_class} ui basic segment atk-seat-svg">{$svg}</div>';
            }
            $this->seatView = $this->add(['View', 'template' => new Template($this->seatViewTemplate)], 'venue');
        }

        if (!$this->venue) {
            throw new \atk4\ui\Exception('You need to supply a venue svg file.');
        }

        //setup svg.
        if (file_exists($this->venue)) {
            $svg = file_get_contents($this->venue);
        } else {
            throw new \atk4\ui\Exception('Unable to open venue file: '. $this->venue);
        }
        $this->seatView->template->setHTML('svg', $svg);

        //setup button.
        $bar = $this->add(['ui' => 'horizontal buttons'], 'buttons');
        $clr = $bar->add(['Button', $this->btnClearLabel]);
        $clr->on('click', $this->seatView->js()->atkSeatSelector('clearSeats'));

        if ($this->hasConfirmBtn) {
            $conf =  $bar->add(['Button', $this->btnConfirmLabel]);
            $conf->on('click', $this->jsConfirmSeat());
        }

        //todo change for cdn file.
        $this->app->requireCSS('/vendor/atk4/seat-selector/public/seat-selector.css');
        $this->app->requireJS('/vendor/atk4/seat-selector/public/seat-selector.js');
    }

    public function renderView() {
        $this->seatView->js(true)->atkSeatSelector(array_merge([
            'qty'        => $this->qty,
            'takenSeats' => $this->takenSeats,
            'uri'        => $this->callback->getJSURL(),
        ], $this->settings));

        return parent::renderView();
    }

    /**
     *  Return js confirm action.
     *  This action will trigger callback for seat reservation.
     *  if a jsFunction is set, the function will execute after seat are confirm.
     *  without any error.
     *
     * @param null $cb jsFunction to execute after seat are confirm.
     *
     * @return mixed
     */
    public function jsConfirmSeat($cb = null)
    {
        return $this->seatView->js()->atkSeatSelector('confirmSeats', $cb);
    }

    /**
     * Provided with a ticket model, this will set up the necessary callback functions for the SeatPicker
     * which will create a new ticket once the seats are allocated.
     *
     * @param Model $ticket
     * @return Model
     */
    public function setModel(Model $ticket) {
        $model = parent::setModel($ticket);

        $this->takenSeats = $model->getTakenSeats();

        $this->callback->set(function($j, $seats) use($model) {

            // Callback will be executed when the seats are locked in
            // and confirmed by the user. We want the whole operation
            // atomic.
            $seats = explode(',', $seats);
            $error = $model->persistence->atomic(function() use($model, $j, $seats) {

                // make sure nobody else got the ticket for those places yet.
                // by trying to find tickets for selected places.
                $reserved = [];
                foreach ($seats as $seat) {
                    $model->tryLoadBy('seat', $seat);
                    if ($model->loaded()) {
                        $reserved[] = $seat;
                    }
                }

                //abort if some seat are selected by someone else.
                if (!empty($reserved)) {
                    return $reserved;
                }

                // Next convert array of places into array of associative arrays
                $data = [];
                foreach($seats as $seat) {
                    $data[] = ['seat' => $seat, 'status' => 'reserved'];
                }

                // Now import data. This should create tickets (in a draft state)
                $model->import($data);

                return false;
            });

            if (is_array($error) && !empty($error) ) {
                $this->app->terminate(json_encode(['success' => true, 'error' => true, 'seats'=> $error, 'message' => $this->seatErrorMsg, 'atkjs' => '']));
            }

        }, ['seats' => 'seats']);

        return $model;
    }
}
