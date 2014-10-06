<?php

namespace OCA\ocDashboard\lib\widgets;

use OCA\Contacts\App as ContactApp;
use OCA\ocDashboard\interfaceWidget;
use OCA\ocDashboard\Widget;
use OCP\Config;
use OCP\User;


/*
 * shows the next meetings from the cownCloud calender app
 * copyright 2013
 * 
 * @version 0.2
 * @date 01-08-2013
 * @author Florian Steffens (flost@live.no)
 */
class calendar extends Widget implements interfaceWidget {

    /**
     *
     *
     * Array
     *  today
     *      allDay
     *      events
     *  now
     *      allDay
     *      events
     *  tomorrow
     *      allDay
     *      events
     *  soon
     *      allDay
     *      events
     *
     *
     * soo = 7 days
     * allDay includes birthdays
     *
     */
    private $_events = Array(
        'now'     => Array(
            'allDay'    => Array(),
            'events'    => Array()
        ),
        'today'       => Array(
            'allDay'    => Array(),
            'events'    => Array()
        ),
        'tomorrow'       => Array(
            'allDay'    => Array(),
            'events'    => Array()
        ),
        'soon'       => Array(
            'allDay'    => Array(),
            'events'    => Array()
        )
    );

    private $_allCalendars;


    // ======== INTERFACE METHODS ================================

    /*
     * @return Array of all data for output
     * this array will be routed to the subtemplate for this widget
     */
    public function getWidgetData() {
        $this->loadCalendars();
        $this->loadBirthdays();
        $this->loadEvents();
        $this->sortAllEvents();


        $calendars = Array();
        foreach ($this->_allCalendars as $cal) {
            if( isset($cal['displaynamename']) ) {
                $calendars[$cal['id']] = $cal['displaynamename'];
            } else {
                $calendars[$cal['id']] = $cal['displayname'];
            }
        }

        /*
        print_r(
            Array(
                'events'    => $this->_events,
                'calendars' => $this->_allCalendars
            )
        );
*/

        return Array(
            'events'        => $this->_events,
            'calendars'     => $calendars
        );
    }

    // ======== END INTERFACE METHODS =============================


    /**
     * sort all events
     */
    private function sortAllEvents() {
        usort( $this->_events['now']['allDay'],       Array($this, 'sortEvents'));
        usort( $this->_events['now']['events'],       Array($this, 'sortEvents'));
        usort( $this->_events['today']['allDay'],     Array($this, 'sortEvents'));
        usort( $this->_events['today']['events'],     Array($this, 'sortEvents'));
        usort( $this->_events['tomorrow']['allDay'],  Array($this, 'sortEvents'));
        usort( $this->_events['tomorrow']['events'],  Array($this, 'sortEvents'));
        usort( $this->_events['soon']['allDay'],      Array($this, 'sortEvents'));
        usort( $this->_events['soon']['events'],      Array($this, 'sortEvents'));
    }

    /**
     * function for usort
     * sort event objects
     *
     * @param $a
     * @param $b
     * @return int
     */
    private function sortEvents($a, $b) {
        if( $a['startdate'] == $b['startdate'] ) {
            return 0;
        }

        return ( $a['startdate'] < $b['startdate'] ) ? -1: 1;
    }


    /**
     * loads all calendards
     * inclusive shared calendars
     */
    private function loadCalendars()
    {
        $this->_allCalendars = \OC_Calendar_Calendar::allCalendars($this->user, true);
    }


    /**
     * loads all birthdays for user and
     * uses contact app
     *
     * @throws \Exception
     */
    private function loadBirthdays() {
        $app = new ContactApp();
        $addressBooks = $app->getAddressBooksForUser();
        foreach($addressBooks as $addressBook) {
            foreach($addressBook->getChildren() as $contact) {
                $tmp    = $contact->getBirthdayEvent();
                $first  = array_slice($tmp->select('VEVENT'), 0, 1);
                $vevent = $first[0];

                if(is_null($vevent)) {
                    continue;
                }

                $start = $vevent->DTSTART;
                /** @var $start \Sabre\VObject\Property\DateTime */
                $startDateTime = $start->getDateTime();
                /** @var $startDateTime \DateTime */

                $end = \OC_Calendar_Object::getDTEndFromVEvent($vevent);
                /** @var $end \Sabre\VObject\Property\DateTime */
                $endDateTime = $end->getDateTime();
                /** @var $endDateTime \DateTime */

                $data = [
                    'id'            => 0,
                    'calendarid'    => 'contact_birthdays',
                    'uri'           => $addressBook->getBackend()->name.'::'.$addressBook->getId().'::'.$contact->getId().'.ics',
                    'lastmodified'  => $contact->lastModified(),
                    'summary'       => $vevent->SUMMARY->value,
                    'calendardata'  => $vevent->serialize(),
                    'startdate'     => $startDateTime->format('Y-m-d H:i:s'),
                    'enddate'       => $endDateTime->format('Y-m-d H:i:s')
                ];
                $this->addEventToGroup($data);
            }
        }
    }


    /**
     * load all events from calendar app
     */
    private function loadEvents() {
        $startOfToday = strtotime("midnight", time());
        $startOfTodayObject = new \DateTime();
        $startOfTodayObject->setTimestamp($startOfToday);
        $endOfToday = strtotime("tomorrow", $startOfToday)-1;
        $endOfToday = $endOfToday + (7*86400); // add 7 days
        $endOfTodayObject = new \DateTime();
        $endOfTodayObject->setTimestamp($endOfToday);

        foreach( $this->_allCalendars as $calendar ) {
            $events = \OC_Calendar_Object::allInPeriod($calendar['id'], $startOfTodayObject, $endOfTodayObject);
            foreach( $events as $event ) {
                $location = $this->getProperty('LOCATION', $event['calendardata']);
                if( $location ) {
                    $event['location'] = $location;
                }
                $this->addEventToGroup($event);
            }
        }
    }


    /**
     * sort event to its group by date
     *
     * @param $vevent
     */
    private function addEventToGroup($vevent) {
        $timeZoneCorrection = (int)Config::getUserValue($this->user, "ocDashboard", "ocDashboard_calendar_timezoneAdd",0);
        $time = time() + ($timeZoneCorrection*60*60);

        if ( strtotime($vevent['startdate']) <= $time && strtotime($vevent['enddate']) > $time && !$this->isAllDayEvent($vevent) ) {
            $group = 'now';
        } elseif (date('m-d') == date('m-d', strtotime($vevent['startdate']))) {
            $group = 'today';
        } elseif (date('m-d') == date('m-d', (strtotime($vevent['startdate'])-86399) )) {
            $group = 'tomorrow';
        } elseif (
            date('m-d') == date('m-d', strtotime($vevent['startdate']) - (2*86400) ) ||
            date('m-d') == date('m-d', strtotime($vevent['startdate']) - (3*86400) ) ||
            date('m-d') == date('m-d', strtotime($vevent['startdate']) - (4*86400) ) ||
            date('m-d') == date('m-d', strtotime($vevent['startdate']) - (5*86400) ) ||
            date('m-d') == date('m-d', strtotime($vevent['startdate']) - (6*86400) ) ||
            date('m-d') == date('m-d', strtotime($vevent['startdate']) - (7*86400) )
        ) {
            $group = 'soon';
        } else {
            $group = '';
        }

        //echo $vevent['summary'].' '.strtotime($vevent['startdate']).' <br>'.$vevent['startdate'].' <br>'.$group.' <br>date(\'m-d\'):'.date('m-d').' <br>strtotime($vevent[\'startdate\']-86399 ):'.date('m-d', (strtotime($vevent['startdate'])-86399 )).'<br><br><br>';

        if( $group ) {
            if( $this->isAllDayEvent($vevent) ) {
                $this->_events[$group]['allDay'][] = $vevent;
            } else {
                $this->_events[$group]['events'][] = $vevent;
            }
        }
    }


    /**
     * @param vevent
     * @return bool
     */
    private function isAllDayEvent($vevent) {
        if( $vevent['calendarid'] == 'contact_birthdays' ) {
            return true;
        }

        $start = strtotime($vevent['startdate']);
        $end   = strtotime($vevent['enddate']);
        $end = (isset($end) && $end != "") ? $end: $start+60*24*24;

        if(
            $start == ($end-60*60*24) &&
            date("H:i:s", $start) == "00:00:00" &&
            date("H:i:s", $end) == "00:00:00"
        ) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * get property from "calendardata"
     * (from serialized string)
     *
	 * @param propertyname
	 * @param search string (separated by "\n", property:value)
	 * @return string property value
	 */
    private function getProperty($property, $searchstring) {
        foreach (explode("\n", $searchstring) as $line) {
            $parts = explode(":", $line);
            if($parts[0] == $property) {
                return $parts[1];
            }
        }
        return "";
    }

}