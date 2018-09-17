
function SeatSelector (element, options) {
  this.$el = $(element);
  this.settings = options;
  this.seats = null;
  this.takenSeats = this.settings.takenSeats;
  this.qty = this.settings.qty;
  this.selected = 0;

  this.main();
}

SeatSelector.prototype.call = function(fn, args) {
  return this[fn](args);
}

SeatSelector.prototype.clearSeats = function() {
  this.selected = 0;
  $('.selected').toggleClass('selected');
}

SeatSelector.prototype.main = function() {
  var that = this;

  this.seats = this.$el.find('.seat');

  this.takenSeats.forEach(function(seat) {
    $('[data-place="'+seat+'"]').toggleClass('reserved');
  })

  this.seats.on('click', function(){

    if (that.selected < that.qty && !$(this).hasClass('selected')) {
      $(this).toggleClass('selected');
      that.selected++;
    } else if ($(this).hasClass('selected')) {
      $(this).toggleClass('selected');
      that.selected--;
    } else {
      alert('All you seat are set.')
    }
  });
}

SeatSelector.DEFAULTS = {
  takenSeats: [],
  qty: 0,
}

atk.registerPlugin('SeatSelector', SeatSelector);