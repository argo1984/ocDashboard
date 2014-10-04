<table>
    <tr>
        <td>
            <div class="addTask">
                <?php
                print_unescaped($l->t('Add new task')); ?>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="newTask">
                <form action="" method="">
                    <input type="text" size="30" id="addTaskSummary" name="addTaskSummary" /><br />
                    <select id="addTaskCalendarId" name="addTaskCalendarId">
                    <?php
                    foreach ($additionalparams['calendars'] as $key => $cal) {
                            print_unescaped('<option value="'.$key.'">'.$cal.'</option>');
                        }
                    ?>
                    </select><br />
                    <input type="submit" value="Add" id="addTaskSubmit">
                </form>
            </div>
        </td>
    </tr>

    <?php
	foreach ($additionalparams['tasks'] as $task) {
        if($task['completed'] != 1) {
            if(count($additionalparams['tasks']) > 0 && isset($additionalparams['calendars'][$task['calendarid']])) {
                print_unescaped('<tr><td><h3 style="color: '.$task['calendarcolor'].'">'.$additionalparams['calendars'][$task['calendarid']].'</h3></td></tr>');
                unset($additionalparams['calendars'][$task['calendarid']]);
            }
            ?>

            <tr>
                <td>
                    <div class='tasks' >
                        <span id="task-<?php p($task['id']); ?>" >&#10003;&nbsp;</span>
                        <?php if($task['starred'] == 1) {print_unescaped('&#10038;&nbsp;');} ?><?php p($task['name']); ?>
                    </div>
                </td>
            </tr>
            <?php
        }
    }
	?>
</div>
</table>
<!--<div id="addTask">+</div>-->