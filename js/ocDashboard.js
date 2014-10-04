;ocDashboard = {

    refreshIds: [],
    hoverWidgetId: null,

    // set or unset the refreshBlockId
    setHoverWidgetId: function (id) {
        if(ocDashboard.hoverWidgetId == id) {
            ocDashboard.hoverWidgetId = null;
        } else {
            ocDashboard.hoverWidgetId = id;
        }
    },

    //set bg color for widgetItem
    setBgShadowColor: function (id, status) {
        colors = ["rgba(0, 0, 0, 0.5)","rgba(0, 0, 0, 0.5)","darkgreen","#FF8000","red"];
        $('#' + id).css('-webkit-box-shadow','0px 5px 15px -7px ' + colors[status]);
        $('#' + id).css('-moz-box-shadow','0px 5px 15px -7px ' + colors[status]);
        $('#' + id).css('box-shadow','0px 5px 15px -7px ' + colors[status]);
        return true;
    },


    //load widget via ajax and set in html
    loadWidget: function (id) {
        //alert(OC.filePath('ocDashboard', 'ajax', 'reloadWidget.php') + '?widget=' + id);
        $.ajax({
            dataType: "json",
            url:  OC.filePath('ocDashboard', 'ajax', 'reloadWidget.php') + '?widget=' + id,
            success: function(res) {
                if (res.success) {
                    $('#' + res.id).children().fadeOut("fast", function () {
                        $('#' + res.id).children().remove();
                        $('#' + res.id).append(res.HTML);
                        $('#' + res.id).children().fadeIn("fast", function () {
                            ocDashboard.hideWaitSymbol(res.id);
                        });
                    });

                    //set new status
                    $('#' + id).data('status',res.STATUS);
                    ocDashboard.setBgShadowColor(id,$('#' + id).data('status'));
                } else {
                    // set error color
                    ocDashboard.setBgShadowColor(id,4);
                    console.log("no success from server");
                    ocDashboard.hideWaitSymbol(id);
                }
            },
            error: function(xhr, status, error) {
                // set error color
                ocDashboard.setBgShadowColor(id,4);
                console.log("ajax error");
                ocDashboard.hideWaitSymbol(id);
            }
        });
    },


    // shows the wait symbol on the left bottom corner
    showWaitSymbol: function (id) {
        $('.app-ocDashboard .widget.' + id + ' .waitSymbol').fadeIn();
    },


    // hides the wait symbol on the left bottom corner
    hideWaitSymbol: function (id) {
        $('.app-ocDashboard .widget.' + id + ' .waitSymbol').fadeOut();
    },


    // automatic reload for widgets with interval > 0
    initialize: function () {
        $('.app-ocDashboard .widget').each(
            function(i, current){
                var id = $(this).data('id');

                // set refreshs
                if( $('.app-ocDashboard .widget.' + id ).data('interval') != 0) {
                    ocDashboard.refreshIds[i] = setInterval(
                        function() {
                            if(ocDashboard.hoverWidgetId != id) {
                                ocDashboard.loadWidget(id);
                            }
                        },
                        $('.app-ocDashboard .widget.' + id).data('interval')
                    );
                }

                // set status at start
                ocDashboard.setBgShadowColor( id, $('.app-ocDashboard .widget.' + id).data('status') );

                // bind reload button actions
                $('.app-ocDashboard .widget.' + id + ' span').live(
                    'click',
                    function () {
                        ocDashboard.showWaitSymbol(id);
                        ocDashboard.loadWidget(id);
                    }
                );

                // bind refreshBlock hover action
                $('.app-ocDashboard .widget.' + id).live(
                    'hover',
                    function() {
                        ocDashboard.setHoverWidgetId(id);
                        ocDashboard.hideOrShowWidgetInformation();
                    }
                );

            }
        );
    },


    // on mouse over a widget, show all div with class hoverInfo
    // on mouse out hide the divs
    hideOrShowWidgetInformation: function () {
        if( ocDashboard.hoverWidgetId == null) {
            $('.app-ocDashboard .widget .hoverInfo').each( function (i, current) {
               var opacity = jQuery.parseJSON( '{ "opacity": "' + $(this).data('opacitynormal') + '"}' );
               $(this).animate(
                   opacity,
                   100
               );
            });

        } else {
            $('.app-ocDashboard .widget.' + ocDashboard.hoverWidgetId + ' .hoverInfo').each( function (i, current) {
                var opacity = jQuery.parseJSON( '{ "opacity": "' + $(this).data('opacityhover') + '"}' );
                $(this).animate(
                    opacity,
                    100
                );
            });
        }
    },


    // ajax service for widgets
    ajaxService: function (widget,method,value,callback) {
        data  = "value="+value+"&";
        data += "id="+widget+"&";
        data += "method="+method+"&";
        $.post(
            OC.filePath('ocDashboard', 'ajax', 'ajaxService.php'),
            data,
            function(result){
                if(result.success){
                    //alert(result.debug);
                    if(callback){
                        callback(result.response);
                    }
                }
            },
            'json'
        );
    }

}


$(document).ready(function() {

	ocDashboard.initialize();

});
