# venue-tickets

If you are building a type of website for a cinema, theatre or arena, where it's important to allocate seats on the tickets, then this add-on should be able to do a heavy-lifting for you:

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

1. Add `class="seat"` to the shape.
2. Add `"data-place="9B"` with the corresponding place label.



## Model: Ticket

The addon comes with a Ticket model, but you can use your own model too. Here are the requirements for the model:

- `"status"`- column as a list of values: `reserved`, `unavailable` or `purchased`. You can add more statusses and they be applied to the seat objects as a class.
- `"position"`- 