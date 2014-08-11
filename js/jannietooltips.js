var JannieTooltips;
(function($){
    JannieTooltips = {
        active: -1,
        activeTooltip: -1,
        container: 0,
        trackInterval: -1,
        track: function(){
            var elmt = this.active;
            if(!elmt.is(":visible"))
                this.dismiss();
            else{
                var elmtOffset = elmt.offset();
                this.activeTooltip.css({
                    //top: elmtOffset.top + elmt.outerHeight() / 2 - this.activeTooltip.outerHeight() / 2,
                    //left: elmtOffset.left - this.activeTooltip.outerWidth()
                    top: elmtOffset.top + elmt.outerHeight(),
                    left: elmtOffset.left + elmt.outerWidth()/2 - this.activeTooltip.outerWidth()/2
                });
            }
        },
        startTrack: function(){
            this.trackInterval = window.setInterval(function(){JannieTooltips.track();}, 150);
            $(document).on({
                'scroll.jt': function(){
                    JannieTooltips.track();
                }
            });
        },
        dismiss: function(){
            if(this.trackInterval!=-1){
                window.clearInterval(this.trackInterval);
                this.trackInterval = -1;
            }
            $(document).off('.jt');
            var tooltip = this.activeTooltip;
            tooltip.stop().animate({
                opacity: 0
            },{
                duration: 200,
                complete: function(){
                    $(this).remove();
                }
            });
            this.active = this.activeTooltip = -1;
        },
        isActive: function(elmt){
            if(this.active!=-1)
                return this.active[0] === elmt[0];
            return false;
        }
    };
    $.fn.jtLink = function(html, showOn, hideOn){
        var elmt = this;
        // console.log(elmt);
        elmt.data('jt', {
            html: html
        });
        for(var n in showOn)
            elmt.on(showOn[n]+".jt", function(){
                $(this).jtShow();
            });
        for(var n in hideOn)
            elmt.on(showOn[n]+".jt", function(){
                $(this).jtHide();
            })
    };
    $.fn.jtUnlink = function(){
        var elmt = this;
        elmt.jtHide();
        elmt.removeData('jt');
        elmt.removeData('jt-tt');
        elmt.off('.jt');
    }
    $.fn.jtShow = function(){
        var elmt = this;
        if(!JannieTooltips.isActive(elmt)){
            if(JannieTooltips.active != -1)
                JannieTooltips.dismiss();
            JannieTooltips.active = elmt;
            var newTooltip = $('<div class="jt-wrapper"><div class="jt-arrow"></div><div class="jt-inner">' + elmt.data('jt').html + '</div></div>').css({
                opacity: 0
            }).stop().animate({
                opacity: 0.95
            },{
                duration: 200
            });
            $('body').append(newTooltip);
            var elmtOffset = elmt.offset();
            newTooltip.css({
                top: elmtOffset.top + elmt.outerHeight(),
                left: elmtOffset.left + elmt.outerWidth()/2 - newTooltip.outerWidth()/2
            });
            JannieTooltips.activeTooltip = newTooltip;
            JannieTooltips.startTrack();
        }
        return this;
    }
    $.fn.jtHide = function(){
        var elmt = this;
        if(JannieTooltips.isActive(elmt))
            JannieTooltips.dismiss();
        return this;
    }
})(jQuery);