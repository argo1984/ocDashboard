;ocDashboard.newsreader = {

    initialize: function () {

        // bind mark as read actions
        $(".app-ocDashboard .widget.newsreader .content .counter span").live('click',function(){
                ocDashboard.newsreader.markNewsAsRead();
            }
        );

        ocDashboard.newsreader.resizeContentImg();
    },


    // send ajax request for mark as read action
    markNewsAsRead: function () {
        ocDashboard.showWaitSymbol('newsreader');
        ocDashboard.ajaxService(
            'newsreader',
            'markAsRead',
            ''
        );
    },


    // resize big images
    resizeContentImg: function () {
        $('.app-ocDashboard .widget.newsreader .content img').each(function() {
            var maxWidth    = $('.app-ocDashboard .widget.newsreader .content').width() - 10;
            var ratio       = 0;
            var width       = $(this).width();
            var height      = $(this).height();

            // Check if width is larger than maxWidth
            if(width > maxWidth){
                ratio   = maxWidth / width;
                $(this).css("width", maxWidth);
                $(this).css("height", height * ratio);
                height  = height * ratio;
                width   = width  * ratio;
            }
        });
    }

}


$(document).ready(function() {

    ocDashboard.newsreader.initialize();

});


