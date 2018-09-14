<?php

namespace atk4\seat_selector\Model;

use \atk4\data\Model;

class Showtime extends Model {

    function init()
    {
        parent::init();

        // Where does this showtime take place
        $this->hasOne('venue_id', new Venue());

        $this->addField('date_time', ['type'=>'datetime', 'required'=>true]);

        $this->hasMany('Tickets', new Ticket());
    }

}