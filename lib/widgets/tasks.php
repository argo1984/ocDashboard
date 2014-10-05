<?php

namespace OCA\ocDashboard\lib\widgets;

use OCA\ocDashboard\interfaceWidget;
use OCA\ocDashboard\Widget;
use OCA\Tasks_enhanced\Dispatcher;


/*
 * displays tasks from Taskapp by ownCloud
 * copyright 2013
 *
 * @version 0.1
 * @date 01-08-2013
 * @author Florian Steffens (flost@live.no)
 */
class tasks extends Widget implements interfaceWidget {


	// ======== INTERFACE METHODS ================================
	
	/*
	 * @return Array of all data for output
	 * this array will be routed to the subtemplate for this widget 
	 */
	public function getWidgetData() {
        //$this->newTask("neu#|#0#|#1");
        $return = Array(
            "tasks" => $this->getTasks(),
            "calendars" => $this->getCalendars()
        );
        return $return;
	}
	
	// ======== END INTERFACE METHODS =============================


    private function getCalendars() {
        $calendars = Array();
        foreach( \OC_Calendar_Calendar::allCalendars($this->user, true) as $cal ) {
            $calendars[$cal['id']] = $cal['displayname'];
        }
        return $calendars;
    }


    /*
     * called by ajaxService
     *
     * @NoAdminRequired
     * @param data for new task
     * @return boolean if success
     */
    public function newTask($data) {
        $d = json_decode($data);

        $param = Array(
            'name'          => $d['summary'],
            'calendarID'    => $d['calendarId'],
            'starred'       => false,
            'due'           => null,
            'start'         => date('c', time())
        );

        $tasksApp = new Dispatcher($param);
        $tasksApp->dispatch('TasksController', 'addTask');

        if( true ) {
            return true;
        }
        return false;
    }


	/*
	 * called by ajaxService
	 * 
	 * @param $id of task
	 * @return boolean if success
	 */
	public function markAsDone($id) {
        $param = Array( "taskID" => $id );
        $dispatcher = new Dispatcher($param);
        $dispatcher->dispatch('TasksController', 'completeTask');
        return true;
	}


    /**
     * @return Array with tasks as array
     */
    private function getTasks() {
        $tasksApp = new Dispatcher(null);
        $tasksContainer = $tasksApp->getContainer();
        $tasksController = $tasksContainer->query('TasksController');
        $data = $tasksController->getTasks()->getData();
        return $data
        ['data']['tasks'];
    }
	
}