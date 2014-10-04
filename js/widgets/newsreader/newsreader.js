$(document).ready(function() {

    // bind mark as read actions
    function bindMarkNewsAsRead() {
        $(".app-ocDashboard .widget.newsreader .content .counter span").live('click',function(){
                markNewsAsRead();
            }
        );
    }


    // send ajax request for mark as read action
    function markNewsAsRead() {
        ocDashboard.showWaitSymbol('newsreader');
        ocDashboard.ajaxService(
            'newsreader',
            'markAsRead',
            '',
            function() {
                ocDashboard.loadWidget('newsreader');
            }
        );
    }


    bindMarkNewsAsRead();

});


