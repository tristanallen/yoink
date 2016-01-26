
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
                var table = self.btn.closest('.panel').find('.panel-body').find('table');
                for(var i = 0; i < data[0].runners.length; ++i){

                    var availableToLay = data[0].runners[i].ex.availableToLay;
                   console.log( data[0].runners[i]);
                    for(var a = 0; a < availableToLay.length; ++a){

                        table.append('<tr><td>'+ data[0].runners[i].selectionId+'</td><td>'+availableToLay[a].size+'</td><td>'+availableToLay[a].price+'</td><td>'+ data[0].runners[i].status+'</td></tr>');
                    };
                    
                }
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