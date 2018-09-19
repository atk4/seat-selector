<?php

require 'init.php';
require 'database.php';

//$seatSelector = $app->add()
$app->add([
              new \atk4\seat_selector\SeatWizard(),
              'seatSelector' => $app->factory([
                                                  new \atk4\seat_selector\SeatSelector(),
                                                  'venue' => dirname(__DIR__).'/public/theater.svg'
                                              ])
          ]);
