/**
 * Plugin Constructor
 *
 * @param element
 * @param options
 * @constructor
 */
function SeatSelector (element, options) {
  this.$el = $(element);
  this.settings = options;
  this.seats = null;
  this.takenSeats = this.settings.takenSeats;
  this.qty = parseInt(this.settings.qty);
  this.qtySelected = 0;
  this.selectedSeats = [];

  this.main();
}

/**
 * Utility function in order to allow for calling plugin function directly.
 *
 * @param fn
 * @param args
 * @returns {*}
 */
SeatSelector.prototype.call = function(fn, args) {
  return this[fn](args);
}

/**
 * Clear all seat selected.
 */
SeatSelector.prototype.clearSeats = function() {
  this.qtySelected = 0;
  this.selectedSeats = [];
  $('.selected').toggleClass('selected');

}

/**
 * Confirm seat selection.
 * Will execute callback function and return selected seat.
 */
SeatSelector.prototype.confirmSeats = function() {
  if (this.selectedSeats.length === this.qty) {
    this.$el.api({
      on: 'now',
      url: this.settings.uri,
      data: {seats: this.selectedSeats.toString()},
      method: 'POST',
    });
  } else {
    this.$el.atkNotify({
      color: 'red',
      content: this.settings.confirmErrorMsg,
      position: 'topCenter',
      openTransition: 'slide down'
    });
  }
}

/**
 * Will notify user of a seat error.
 * Ex: When user trying to confirm a seat already reserved by somone else.
 *
 * @param args An array: args[0] - contains an array of seat value. args[1] - contains the error msg to user.
 */
SeatSelector.prototype.seatError = function(args) {
  var that = this;
  args[0].forEach(function (seat){
    $('[data-'+that.settings.dataAttr+'="'+seat+'"]').removeClass('selected').addClass('reserved');
    var index = that.selectedSeats.indexOf(seat);
    if (index > -1) {
      that.selectedSeats.splice(index, 1);
      that.qtySelected--;
    }
  });

  this.seats.off('click');
  this.setSeatHandler();

  this.$el.atkNotify({
    color: 'red',
    content: args[1],
    position: 'topCenter',
    openTransition: 'slide down'
  });
}

/**
 * Set click handler to available seats.
 */
SeatSelector.prototype.setSeatHandler = function() {
  var that = this;

  this.seats = this.getAvailableSeats();

  this.seats.on('click', function(){
    if (that.qtySelected < that.qty && !$(this).hasClass('selected')) {
      //user adding a seat.
      $(this).toggleClass('selected');
      that.selectedSeats.push(($(this).data(that.settings.dataAttr)));
      that.qtySelected++;
    } else if ($(this).hasClass('selected')) {
      //user removing a seat.
      $(this).toggleClass('selected');
      that.qtySelected--;
      var index = that.selectedSeats.indexOf($(this).data(that.settings.dataAttr));
      if (index > -1) {
        that.selectedSeats.splice(index, 1);
      }
    } else {
      that.$el.atkNotify({
        color: 'red',
        content: that.settings.maxQtyErrorMsg,
        position: 'topCenter',
        openTransition: 'slide down'
      });
    }
  });
}

/**
 * Return jQuery object with all available seats.
 *
 * @returns {T[]}
 */
SeatSelector.prototype.getAvailableSeats = function () {
  return this.$el.find('.seat').filter(':not(.reserved)');
}

/**
 * Plugin main initializer function.
 */
SeatSelector.prototype.main = function() {
  var that = this;

  this.takenSeats.forEach(function(seat) {
    $('[data-'+that.settings.dataAttr+'="'+seat+'"]').toggleClass('reserved');
  })

  this.setSeatHandler();
}

/**
 * Plugin defaults.
 */
SeatSelector.DEFAULTS = {
  uri: null,
  uri_options: null,
  takenSeats: [],
  dataAttr: 'place',
  qty: 0,
  confirmErrorMsg: 'Please select all your tickets prior to confirm your seat.',
  maxQtyErrorMsg: 'All of your seats are already select.',
}

atk.registerPlugin('SeatSelector', SeatSelector);
