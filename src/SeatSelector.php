<?php
namespace atk4\seat_selector;

use atk4\data\Exception;
use atk4\data\Model;
use atk4\ui\Callback;
use atk4\ui\Template;
use atk4\ui\View;

class SeatSelector extends View {

    public $seatSelectorTemplate = 'seat-selector.html';

    public $seatViewTemplate = '<div id="{$_id}" class="{$_class} atk-seat-svg">{$svg}</div>';

    /**
     * @var Callback
     */
    public $callback;

    /**
     * @var array JavaScript settings
     */
    public $settings = [];

    /**
     * @throws \atk4\ui\Exception
     */
    function init() {
        $this->defaultTemplate = dirname(__DIR__).'/template/'.$this->seatSelectorTemplate;
        parent::init();

        $btn = $this->add(['Button', 'Clear Seat'], 'button');

        if (!$this->seatView) {
            $this->seatView = $this->add(['View', 'template' => new Template($this->seatViewTemplate)], 'venue');
        }

        $svg = file_get_contents(dirname(__DIR__).'/svg/example1.svg');

        $this->seatView->template->setHTML('svg', $svg);
        $btn->on('click', $this->seatView->js()->atkSeatSelector('clearSeats'));

        $this->app->requireCSS('/vendor/atk4/seat-selector/public/seat-selector.css');
        $this->app->requireJS('/vendor/atk4/seat-selector/public/seat-selector.js');

    }

    function renderView() {
        $this->seatView->js(true)->atkSeatSelector(['qty' => 2, 'takenSeats' => ['S4', 'S5']]);
        return parent::renderView();
    }

    /**
     * Provided with a ticket model, this will set up the necessary callback functions for the SeatPicker
     * which will create a new ticket once the seats are allocated.
     *
     * @param Model $ticket
     * @return Model
     */
    function setModel(Model $ticket) {
        $model = parent::setModel($ticket);

        $this->callback->set(function($arg) use($model) {

            // Callback will be executed when the seats are locked in
            // and confirmed by the user. We want the whole operation
            // atomic.

            $model->persistence->atomic(function() use($model, $arg) {

                // make sure nobody else got the ticket for those places yet.
                // by trying to find tickets for selected places.
                $model->tryLoadBy('place', $arg);
                if ($model->loaded()) {
                    throw new Exception(['Selected seats are not available', 'allocated_ticket'=>$model]);
                }

                // Next convert array of places into array of associative arrays
                $data = [];
                foreach($arg as $place) {
                    $data[] = ['palce'=>$place];
                }

                // Now import data. This should create tickets (in a draft state)
                $model->import($data);
            });


        });


        return $model;
    }

}