<?php

namespace atk4\seat_selector;

use atk4\seat_selector\Model\Event;
use atk4\seat_selector\Model\Venue;
use atk4\ui\FormField\AutoComplete;
use atk4\ui\jsExpression;
use atk4\ui\jsReload;
use atk4\ui\View;
use atk4\ui\Wizard;

class SeatWizard extends View
{
    public $wizard = null;

    public $qty = 1;
    public $venue = null;

    public function init()
    {
        parent::init();

        $this->wizard = $this->add('Wizard');

        $this->wizard->addStep(['Select Event', 'icon'=>'calendar outline', 'description'=>'Select an event.'], function ($p) {

            $f = $p->add('Form');

            $f->addField('event', [
                'Autocomplete', 'model' => new Event($p->app->db)
            ]);
            $f->onSubmit(function ($f) use ($p) {
                $p->memorize('event', $f->model['event']);

                return $p->jsNext();
            });
        });

        $this->wizard->addStep(['Select Showtime', 'description'=>'Select showtime for the event.', 'icon'=>'calendar'], function ($p) {

            $m = (new Event($p->app->db))->load($p->recall('event'))->ref("Showtimes");
            $m->addExpression('name', ' DATE_FORMAT([date_time], "%M %d, %Y @ %H:%I")');
            $m->title_field = 'name';
            $f = $p->add('Form');

            $f->addField('showtime', [
                'Autocomplete', 'model' => $m
            ]);
            $f->addField('qty', ['inputType' => 'number']);
            $f->onSubmit(function ($f) use ($p) {
                $p->memorize('showtime', $f->model['showtime']);
                $p->memorize('qty', $f->model['showtime']);

                return $p->jsNext();
            });
        });

        $this->wizard->addStep(['Select Seats', 'description'=>'Select seats for the event.', 'icon'=>'calendar'], function ($p) {

            $m = (new Event($p->app->db))->load($p->recall('event'))->ref("Showtimes")->load($p->recall('showtime'))->ref('Tickets');

            $seat = $p->add([new SeatSelector(), 'venue' => '/var/www/html/public/example1.svg', 'qty' => $p->recall('qty')]);
            $seat->setModel($m);
        });
    }
}
