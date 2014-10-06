<?php
$hideDate = Array('now','today');
$l = new OC_L10N('ocDashboard');

// now, today, tomorrow, soon
foreach ($additionalparams['events'] as $groupname => $group) { ?>
<table>
    <?php
    if( !empty($group['allDay']) || !empty($group['events']) ) {
        ?>
        <tr>
            <th>
                <h1>
                    <?php
                    switch ($groupname) {
                        case 'now':
                            print_unescaped($l->t('Now'));
                            break;
                        case 'today':
                            print_unescaped($l->t('Today'));
                            break;
                        case 'tomorrow':
                            print_unescaped($l->t('Tomorrow'));
                            break;
                        case 'soon':
                            print_unescaped($l->t('Soon'));
                            break;
                    }
                    ?>
                </h1>
            </th>
        </tr>
    <?php
    } ?>
    <?php
    foreach( $group['allDay'] as $event) { ?>
    <tr>
        <td>
            <div class="name">
                <?php
                // add birthday icon
                if( $event['calendarid'] == 'contact_birthdays' ) {
                    print_unescaped('<img src="'.link_to( 'ocDashboard', 'img/icons/43.png', array()).'" height="15" width="15" />');
                }
                if(
                    isset($additionalparams['calendars'][$event['calendarid']]['calendarcolor']) &&
                    $additionalparams['calendars'][$event['calendarid']]['calendarcolor'] != ''
                ) {
                    $color = $additionalparams['calendars'][$event['calendarid']]['calendarcolor'];
                } else {
                    $color = '#9fc6e7';
                }
                print_unescaped($event['summary'].' <span style="color:'.$color.';" data-opacitynormal="0.5" class="hoverInfo">'.$additionalparams['calendars'][$event['calendarid']].'</span>'); ?>
            </div>
            <?php
            // add location
            if( isset($event['location']) ) { ?>
                <div class="location hoverInfo" data-opacitynormal="0.5">
                    <?php
                    print_unescaped( $event['location'] ); ?>
                </div>
            <?php
            } ?>
            <?php
            // add time
            if( !in_array($groupname, $hideDate) ) { ?>
                <div class="time hoverInfo" data-opacitynormal="0.5">
                    <?php
                    $l = new OC_L10N('ocDashboard');
                    $timestamp = strtotime($event['startdate']);
                    print_unescaped($l->l('date', $timestamp)); ?>
                </div>
            <?php
            } ?>
        </td>
    </tr>
    <?php
    }
    foreach( $group['events'] as $calid => $event) { ?>
    <tr>
        <td>
            <div class="name">
                <?php
                if(
                    isset($additionalparams['calendars'][$event['calendarid']]['calendarcolor']) &&
                    $additionalparams['calendars'][$event['calendarid']]['calendarcolor'] != ''
                ) {
                    $color = $additionalparams['calendars'][$event['calendarid']]['calendarcolor'];
                } else {
                    $color = '#9fc6e7';
                }
                print_unescaped($event['summary'].' <span style="color:'.$color.';" data-opacitynormal="0.5" class="hoverInfo">'.$additionalparams['calendars'][$event['calendarid']].'</span>'); ?>
            </div>
            <?php
            // add location
            if( isset($event['location']) ) { ?>
                <div class="location hoverInfo" data-opacitynormal="0.5">
                    <?php
                    print_unescaped( $event['location'] ); ?>
                </div>
            <?php
            } ?>
            <div class="time hoverInfo" data-opacitynormal="0.5">
                <?php

                // display start time
                if( in_array($groupname, $hideDate) ) {
                    $mode = 'time';
                } else {
                    $mode = 'datetime';
                }
                $startTimestamp = strtotime($event['startdate']);
                print_unescaped($l->l($mode, $startTimestamp));

                print_unescaped(' - ');

                if (date('Y-m-d', strtotime($event['startdate'])) == date('Y-m-d', strtotime($event['enddate']))) {
                    $mode = 'time';
                } else {
                    $mode = 'datetime';
                }
                $endTimestamp = strtotime($event['enddate']);
                print_unescaped($l->l($mode, $endTimestamp)); ?>
            </div>
        </td>
    </tr>
    <?php
    }
    ?>
</table>
<?php
} ?>
