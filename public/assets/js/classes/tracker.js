
var Tracker = function (tracker) {
    this.url = ''
    this.btn = tracker;
    this.addListeners();
}

Tracker.prototype = {
    constructor: Tracker,
    addListeners: function(){
        this.btn.on( 'click', $.proxy(this.track, this));
    },
    track : function(e){
        this.market = $(e.target).data('market'); 
        this.store();
    },
    store: function(){
        var  self = this;
        $.ajax('/store-market', {
            type: "post",
            data: {
                market : self.market
            },
            dataType: 'json',

            beforeSend: function () {
                // before post
            },

            success: function (data) {
                console.log(data);
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