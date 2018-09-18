<?php

namespace atk4\seat_selector\Model;

use atk4\data\Model;

class Event extends Model
{
    public $table = 'event';

    public function init()
    {
        parent::init();

        $this->addField('name', ['required' => true]);

        $this->hasOne('venue_id', new Venue());
        $this->hasMany('Showtimes', new Showtime());
    }
}
