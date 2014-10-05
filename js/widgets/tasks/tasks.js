;ocDashboard.tasks = {

    initialize: function() {
        $('.app-ocDashboard .widget.tasks .addTask').live('click', function() {
            $('.app-ocDashboard .widget.tasks .newTask').slideDown();
            $('.app-ocDashboard .widget.tasks .addTask').slideUp();
            $('#addTaskSummary').focus();
            event.preventDefault();
        });

        $('#addTaskSubmit').live('click', function(event) {
            event.preventDefault();
            ocDashboard.tasks.createNewTask();
        });

        ocDashboard.tasks.bindMarkAsRead();
    },

    // ajax action for adding new task
    createNewTask: function () {
        ocDashboard.showWaitSymbol('tasks');
        var jsonValue = '[{"summary":"' + $("#addTaskSummary").val() + '"},{"calendarId":"' + $("#addTaskCalendarId").val() + '"}]';
        alert(jsonValue);
        ocDashboard.ajaxService('tasks',
            'newTask',
            jsonValue,
            function(res) {
                ocDashboard.loadWidget('tasks');
            }
        );
    },

    // bind mark as read action
    bindMarkAsRead: function () {
        $('.app-ocDashboard .widget.tasks .content .tasks span').each(function(i, current) {
                tmp = current.id.split("-");
                id = tmp[1];
                $("#task-" + id).unbind('click');
                ocDashboard.tasks.bindSingleMarkAsRead(id);
            }
        );
    },

    bindSingleMarkAsRead: function (id) {
        $("#task-" + id).live('click',function() {
                ocDashboard.tasks.markAsRead(id);
            }
        );
    },

    // ajax action for mar as read
    markAsRead: function (id) {
        ocDashboard.showWaitSymbol('tasks');
        //$("#task-" + id).parent().fadeOut();
        alert(id);
        ocDashboard.ajaxService('tasks',
            'markAsDone',
            id,
            function(res) {
                //bindMarkAsRead();
            }
        );
        ocDashboard.hideWaitSymbol('tasks');
    }
}

$(document).ready(function() {
    ocDashboard.tasks.initialize();
});

/*
$(document).ready(function() {
    bindMarkAsRead();
    bindNewTask();
});


// bind mark as read action
function bindMarkAsRead() {
	$('.ocDashboard.tasks.item span').each(function(i, current) {
			tmp = current.id.split("-");
			id = tmp[1];
            // TODO use on() instead of live()
            $("#task-" + id).unbind('click');
            bindSingleMarkAsRead(id);
        }
	);
}


function bindSingleMarkAsRead(id) {
    $("#task-" + id).live('click',function() {
            markAsRead(id);
        }
    );
}


// bind function for adding new tasks
function bindNewTask() {
    $('#addTask').live('click', function(event) {
        $(".newtask").slideDown();
        $('#addTaskSummary').focus();
    });

    $('#addTaskSubmit').live('click', function(event) {
        newTask();
        event.preventDefault();
    });
}


// ajax action for mar as read
function markAsRead(id) {
	showWaitSymbol('tasks');
	$("#task-" + id).parent().fadeOut();
	ajaxService('tasks',
				'markAsDone',
				id,
				function(res) {
                    //bindMarkAsRead();
				}
	);
    hideWaitSymbol('tasks');
}


// ajax action for adding new task
function newTask() {
    showWaitSymbol('tasks');
    var value = $("#addTaskSummary").val() + "#|#" + $("input[name=addTaskStarred]:checked").val() + "#|#" + $("#addTaskCalendarId").val();
    alert(value);
    ajaxService('tasks',
        'newTask',
        value,
        function(res) {
            loadWidget('tasks');
            setTimeout(function(){ bindMarkAsRead(); },500);
        }
    );
}
*/