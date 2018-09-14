<?php

namespace atk4\seat_selector\Model;

use \atk4\data\Model;

class Ticket extends Model {

    function init()
    {
        parent::init();

        // Ticket status initially is "reselved". Once the payment is taken for the ticket, it should change do "paid"
        // Use "vip" for manually "blocked" seats.
        $this->addField('status', ['enum'=>['reserved', 'vip', 'paid'], 'default'=>'reserved']);

        // Stores seat location (row/seat) such as "6b"
        $this->addField('place');

        // Reservation timestamp. You may want to auto-clear those after 15 minutes
        $this->addField('reservation_timestamp', ['type'=>'datetime', 'default'=>new \DateTime()]);


        $this->hasOne('showtime_id', new Showtime());
    }

}