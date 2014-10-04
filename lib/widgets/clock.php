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
 * @version 0.1
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
        $date = new \DateTime();
        $t = $date->format( $this->l->t('l, d-M-Y') );
		return Array( 'date' => $t );
	}
	
	// ======== END INTERFACE METHODS =============================
	
}