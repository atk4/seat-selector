<?php

namespace atk4\seat_selector\Model;

use \atk4\data\Model;

class Venue extends Model {

    public $table = 'venue';

    function init()
    {
        parent::init();

        // Name of the venue or location
        $this->addField('name');

        $this->hasMany('Events', new Event());
    }
}
