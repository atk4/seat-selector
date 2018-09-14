<?php
namespace atk4\seat_selector;

use atk4\data\Model;
use atk4\ui\Callback;
use atk4\ui\View;

class SeatSelector extends View {

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
        parent::init();

        $this->callback = $this->add('Callback');


        // $this->js(true)->seatSelector($this->settings);
    }

    function renderView() {
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