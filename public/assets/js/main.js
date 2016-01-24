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

    // initialise nav bar is nav bar exists
    $('.tracker').exists(function() {
        new Tracker( this );
    })
})
