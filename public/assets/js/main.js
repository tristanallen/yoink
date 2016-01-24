$.fn.exists = function(callback) {
    var args = [].slice.call(arguments, 1);

    if (this.length) {
        callback.call(this, args);
    }

    return this;
};


$(function(){

    // initialise nav bar is nav bar exists
    $('.tracker').exists(function() {
        new Tracker( this );
    })
})
