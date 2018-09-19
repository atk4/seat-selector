<?php
require 'vendor/autoload.php';

$app = new \atk4\ui\App('Seat selector');
$app->initLayout('Centered');

class Seat_place extends \atk4\ui\View
{
    public $defaultTemplate = __DIR__.'/svg/seat_place.html';
    public $x;
    public $y;
    function init()
    {
      parent::init();
      //$this->template['x'] = $x;
      //$this->template['y'] = $y;
    }
}

class Screen extends \atk4\ui\View
{
    public $defaultTemplate = __DIR__.'/svg/screen.svg';
    function init()
    {
      parent::init();
    }
}

//$app->add([new Screen]);

$max_width = 16;
$max_height = 8;

//довести расчеты до конца и попробовать

session_start();
$_SESSION['x'] = '238.499';

//$app->add([new Seat_place]);

for ($x=1; $x <= $max_width; $x++) {
  for ($y=1; $y <= $max_height ; $y++) {
    $app->add([new Seat_place]);
  }
  $app->add(['ui'=>'hidden divider']);
}


/*for ($x=238.499; $x <= $max_width; $x+34.488) {
  for ($y=238.499; $y <= $max_height ; $y+36.979) {
    $app->add([new Seat_place($x,$y)]);
  }
} */
