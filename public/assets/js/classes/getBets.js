
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
        this.runnerId = market.runner[0].id;
        this.marketId = market.market_id;
        this.store();
    },
    store: function(){
        var self = this;
        $.ajax('/update-books', {
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
                /*
                var table = self.btn.closest('.panel').find('.panel-body').find('table');
                if(data[0].runners){
                    for(var i = 0; i < data[0].runners.length; ++i){

                        var availableToLay = data[0].runners[i].ex.availableToLay;
                        if(data[0].runners[i].selectionId == self.runnerId){
                            if(availableToLay.length){
                                for(var a = 0; a < availableToLay.length; ++a){

                                    var html = '<tr><td>'+ data[0].runners[i].selectionId+'</td>';
                                    html = html+'<td></td>';
                                    html = html+'<td>'+availableToLay[a].size+'</td>';
                                    html = html+'<td>'+availableToLay[a].price+'</td>';
                                    html = html+'<td>'+ data[0].runners[i].status+'</td></tr>';
                                    table.append(html);
                                };
                            }else{
                                 var html = '<tr><td>'+ data[0].runners[i].selectionId+'</td>';
                                    html = html+'<td>'+'</td>';
                                    html = html+'<td>'+'</td>';
                                    html = html+'<td>'+'</td>';
                                    html = html+'<td>'+ data[0].runners[i].status+'</td></tr>';
                                    table.append(html);
                    
                    
                            };    
                        }
                       
                    }
                
                
                }
                */
                
                self.pressedBtn.prop('disabled', true);
            },

            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(XMLHttpRequest + ' - ' + textStatus + ' - ' + errorThrown);
            },

            complete: function () {
                // on success
                //self.pressedBtn.prop('disabled', false);
            }
        });
    },
}