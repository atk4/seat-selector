<?php

require 'init.php';
require 'database.php';

$app->add([
              new \atk4\seat_selector\SeatWizard(),
              'seatSelector' => $app->factory([
                                                  new \atk4\seat_selector\SeatSelector(),
                                                  'venue' => dirname(__DIR__).'/public/theater.svg',
                                                  'settings' => ['zoomable' => '#theater']
                                              ])
          ]);
