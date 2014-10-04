<?php

namespace OCA\ocDashboard\lib\widgets;

use OCA\ocDashboard\interfaceWidget;
use OCA\ocDashboard\Widget;


/*
 * analog clock by javascript
 * copyright 2013
 * 
 * use for non-commercial only
 * more infos: 
 * 	randomibis.com/coolclock/
 * 
 * @version 0.2
 * @date 01-08-2013
 * @author Florian Steffens (flost@live.no)
 */
class clock extends Widget implements interfaceWidget {


	// ======== INTERFACE METHODS ================================

	/*
	 * @return Array of all data for output
	 * this array will be routed to the subtemplate for this widget 
	 */
	public function getWidgetData() {
		return Array( 'date' => $this->l->l('date', time()) );
	}
	
	// ======== END INTERFACE METHODS =============================
	
}