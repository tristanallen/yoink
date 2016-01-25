
var Tracker = function (tracker) {
    this.url = ''
    this.btn = tracker;
    this.pressedBtn = null;
    this.addListeners();

}

Tracker.prototype = {
    constructor: Tracker,
    addListeners: function(){
        this.btn.on( 'click', $.proxy(this.track, this));
    },
    track : function(e){
        this.pressedBtn = $(e.target);
        this.market = this.pressedBtn.data('market'); 
        this.store();
    },
    store: function(){
        var tracker = this;
        $.ajax('/store-market', {
            type: "post",
            data: {
                market : tracker.market
            },
            dataType: 'json',

            beforeSend: function () {
                // before post
            },

            success: function (data) {
                console.log(data);
                tracker.pressedBtn.prop('disabled', true);
                tracker.pressedBtn.text('tracked');
            },

            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(XMLHttpRequest + ' - ' + textStatus + ' - ' + errorThrown);
            },

            complete: function () {
                // on success
            }
        });
    },
}