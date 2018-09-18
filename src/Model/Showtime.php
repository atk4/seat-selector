<?php

namespace atk4\seat_selector\Model;

use \atk4\data\Model;

class Showtime extends Model {

    public $table = 'showtime';
    public $title_field = 'date_time';

    function init()
    {
        parent::init();

        // Where does this showtime take place
        $this->hasOne('event_id', new Event());


        $this->addField('date_time', ['type'=>'datetime', 'required'=>true]);

        $this->hasMany('Tickets', new Ticket());
    }

}