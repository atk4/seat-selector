# seat-picker

If you are building a type of website for a cinema, theatre or arena, where it's important to allocate seats on the tickets. This add-on is designed to offer you a seat allocation widget:

``` php
$seats = $app->add(new \atk4\venue_tickets\SeatSelector('venue.svg', 5));
$seats->setModel($event->ref('Tickets'));
```

This will display this widget for you (depending on your SVG):

![seats-selection](/Users/rw/Sites/venue-tickets/docs/images/seats-selection.png)

User would have to select up to 5 seats with the widget and confirm. The JavaScript part of this add-on will communicate with ATK View object on the server site to mark those seats as "reserved". 

Next your application takes over, confirms user payment, prints tickets - it's up to you.

## SVG image

Create the image using any imaging applicaiton that can output SVG. Open the file and find a corresponding object(s) for the seats:

``` svg
<circle id="seat_12_" fill="#FFFFFF" stroke="#000000" stroke-miterlimit="10" cx="307.476" cy="385.708" r="11.979"/>
```

Depending on how fancy your seat design is, the shape may be different.

1. Add `class="seat"` to the seat shapes.
2. Add `"data-place="9B"` with to each seat.
3. If you have a text-object corresponding to of seats to select, add `class="remaining"` to it.

Store your SVG file anywhere. It will be embedded into your page directly.

## Model: Ticket

The addon comes with a Ticket model, but you can use your own model too. Here are the requirements for the model:

- `"place"` - will contain the corresponding place.
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

