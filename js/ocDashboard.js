;ocDashboard = {

    refreshIds: [],
    hoverWidgetId: null,

    // set or unset the refreshBlockId
    setHoverWidgetId: function (id) {
        //console.log("start setHoverWidgetId (id = " + id + ")");
        if(ocDashboard.hoverWidgetId == id) {
            ocDashboard.hoverWidgetId = null;
        } else {
            ocDashboard.hoverWidgetId = id;
        }
        //console.log("setHoverWidgetId = " + ocDashboard.setHoverWidgetId);
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
        if(ocDashboard.hoverWidgetId != id) {
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
        }
    },


    // shows the wait symbol on the left bottom corner
    showWaitSymbol: function (id) {
        $('.ocDashboard.inAction.' + id).fadeIn();
    },


    // hides the wait symbol on the left bottom corner
    hideWaitSymbol: function (id) {
        $('.ocDashboard.inAction.' + id).fadeOut();
    },


    // automatic reload for widgets with interval > 0
    initialize: function () {
        $('.dashboardItem').each(function(i, current){
            if( $("#" + current.id ).data('interval') != 0) {
                // set refreshs
                ocDashboard.refreshIds[i] = setInterval(
                    function() {
                        ocDashboard.loadWidget(current.id);
                    },
                    $('#' + current.id).data('interval')
                );

                //set status at start
                if( $("#" + current.id ).data('interval') != 0) {
                    ocDashboard.setBgShadowColor(
                        current.id,
                        $('#' + current.id).data('status')
                    );
                }

                // bind reload button actions
                $('#' + current.id + ' .ocDashboard.head span').live(
                    'click',
                    function () {
                        ocDashboard.showWaitSymbol(current.id);
                        ocDashboard.loadWidget(current.id);
                    }
                );

            }
            // bind refreshBlock hover action
            $('#' + current.id).live(
                'hover',
                function() {
                    ocDashboard.setHoverWidgetId(current.id);
                    ocDashboard.hideOrShowWidgetInformation();
                }
            );
        })
    },


    // on mouse over a widget, show all div with class hoverInfo
    // on mouse out hide the divs
    hideOrShowWidgetInformation: function () {
        if( ocDashboard.hoverWidgetId == null) {
            $('.ocDashboardWidget .hoverInfo').css('visibility', 'hidden');
        } else {
            $('#' + ocDashboard.hoverWidgetId + '.ocDashboardWidget .hoverInfo').css('visibility', 'visible');
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

	// fade in widgets
	$(".dashboardItem").fadeIn();

	ocDashboard.initialize();

});
