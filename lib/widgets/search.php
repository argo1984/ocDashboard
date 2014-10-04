<?php

namespace OCA\ocDashboard\lib\widgets;

use OCA\ocDashboard\interfaceWidget;
use OCA\ocDashboard\Widget;
use OCP\Config;


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
class search extends Widget implements interfaceWidget {
    private $searchEngines = Array("Google","Yahoo","Bing","DuckDuckGo","Ask","eBay","Amazon","Youtube");

	// ======== INTERFACE METHODS ================================

	/*
	 * @return Array of all data for output
	 * this array will be routed to the subtemplate for this widget 
	 */
	public function getWidgetData() {
        $lang = Config::getUserValue($this->user, "ocDashboard", "ocDashboard_search_defaultSearchEngineLanguage","0");
        $engine = Config::getUserValue($this->user, "ocDashboard", "ocDashboard_search_defaultSearchEngine","0");

		return Array(
            "lang" => $lang,
            "engine" => $engine,
            "engines" => $this->searchEngines
        );
	}
	
	// ======== END INTERFACE METHODS =============================
	
}