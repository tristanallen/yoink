$.fn.exists = function(callback) {
    var args = [].slice.call(arguments, 1);

    if (this.length) {
        callback.call(this, args);
    }

    return this;
};

$.ajaxSetup({
    headers: {
        'X-CSRF-Token': TOKEN
    }
});


$(function(){

   
    $('.tracker').exists(function() {
        new Tracker( this );
    })

     $('.get-bets').exists(function() {
        new getBets( this );
    })
})
