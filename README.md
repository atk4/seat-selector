# seat-selector - Cinema, Theatre and Arena seat allocation widget for ATK

If you are building a type of website for a cinema, theatre or arena, where it's important to allocate seats on the tickets. This add-on is designed to offer you a seat allocation widget:

``` php
$seats = $app->add([new \atk4\venue_tickets\SeatSelector(), 'venue' => 'path/to/svg/file.svg, qty => 5]);
$seats->setModel($event->ref('Tickets'));
```

This will display this widget for you (depending on your SVG):

![seats-selection](docs/images/seats-demo.png)

User would have to select up to 5 seats with the widget and confirm. The JavaScript part of this add-on will communicate with ATK View object on the server site to mark those seats as "reserved". 

Next your application takes over, confirms user payment, prints tickets - it's up to you.

## SVG image

Create the image using any imaging applicaiton that can output SVG. Open the file and find a corresponding object(s) for the seats:

``` svg
<path class="seat" data-place="S1" fill="#FFFFFF" stroke="#000000" stroke-linejoin="bevel" stroke-miterlimit="10" d="M128.829,281.623
    l2.764-10.522l10.684,1.777l-0.87,11.343c0,0-3.288,1.659-7.068,0.853C130.748,284.306,128.829,281.623,128.829,281.623z"/>
```

Depending on how fancy your seat design is, the shape may be different.

1. Add `class="seat"` to the seat shapes.
2. Add `"data-place="9B"` with to each seat.
3. If you have a text-object corresponding to of seats to select, add `class="remaining"` to it.

Store your SVG file anywhere. It will be embedded into your page directly.

## Model: Ticket

The addon comes with a Ticket model, but you can use your own model too. Here are the requirements for the model:

- `"seat"` - will contain the corresponding place.
- `"status"`- (Optional) column as a list of values: `reserved`, `unavailable` or `purchased`. You can add more statusses and they be applied to the seat objects as a class.

Initially the data-set for the Ticket model would be empty. SeatSelector will create 5 (or whichever number you specify to the constructor) new ticket records and will fill-in "place".

It will also look a the existing ticket records and display them as "unavailable". If your model has a "status" field then the status will also be assigned to your SVG objects.

I recommend you to also have a `reservation_timestamp` added to your ticket and automatically clear reservation status when it expires:

``` php
§model->addField(
    'reservation_timestamp', 
    ['type'=>'datetime', 'default'=>new \DateTime()]
);
```

