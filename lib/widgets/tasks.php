<?php

namespace OCA\ocDashboard\lib\widgets;

use OCA\ocDashboard\interfaceWidget;
use OCA\ocDashboard\Widget;
use OCA\Tasks\AppInfo\Application as TaskApp;
use OCA\Tasks\Controller\TasksController;


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
        return Array(
            "tasks" => $this->getTasks(),
            "calendars" => $this->getCalendars()
        );
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

        $params = Array(
            'name'          => $d['summary'],
            'calendarID'    => $d['calendarId'],
            'starred'       => false,
            'due'           => null,
            'start'         => date('c', time())
        );
        // TODO: does not work
        $tasksController = $this->getTasksController($params);
        $tasksController->addTask();
        return false;
    }


	/*
	 * called by ajaxService
	 * 
	 * @param $id of task
	 * @return boolean if success
	 */
	public function markAsDone($id) {
        $params = Array(
            'taskID'    => $id
        );
        // TODO: does not work
        $tasksController = $this->getTasksController($params);
        $tasksController->completeTask();
        return "id: ".$id;
	}


    /**
     * @return Array with tasks as array
     */
    private function getTasks() {
        $tasksController = $this->getTasksController();
        $tasks = $tasksController->getTasks()->getData();
        return $tasks['data']['tasks'];
    }


    /**
     * fetch a instance of the TasksController from the tasks app
     *
     * @param array with values for the DIContainer
     * @return TasksController
     */
    private function getTasksController($params = null) {
        $taskApp = new TaskApp($params);
        $taskContainer = $taskApp->getContainer();
        /** @var  $tasksController TasksController */
        $tasksController = $taskContainer->query('TasksController');
        return $tasksController;
    }
	
}