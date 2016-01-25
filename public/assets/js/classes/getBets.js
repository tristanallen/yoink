
var getBets = function (market) {
    this.url = ''
    this.btn = market;
    this.pressedBtn = null;
    this.addListeners();
}

getBets.prototype = {
    constructor: getBets,
    addListeners: function(){
        this.btn.on( 'click', $.proxy(this.getBets, this));
    },
    getBets : function(e){
        this.pressedBtn = $(e.target);
        var market = this.pressedBtn.data('market'); 
        this.marketId = market.market_id;
        this.store();
    },
    store: function(){
        var self = this;
        $.ajax('/get-bets', {
            type: "post",
            data: {
                marketId : self.marketId
            },
            dataType: 'json',

            beforeSend: function () {
                // before post
            },

            success: function (data) {
                console.log(data);
                self.pressedBtn.prop('disabled', true);
            },

            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(XMLHttpRequest + ' - ' + textStatus + ' - ' + errorThrown);
            },

            complete: function () {
                // on success
                self.pressedBtn.prop('disabled', false);
            }
        });
    },
}