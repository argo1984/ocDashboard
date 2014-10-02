<?php

namespace OCA\ocDashboard;

use OC;
use OCP\App;
use OCP\Config;
use OCP\Template;
use OCP\User;
use OCP\Util;

User::checkLoggedIn();
App::checkAppEnabled('ocDashboard');

Util::addscript('ocDashboard', 'settings');
Util::addstyle('ocDashboard', 'ocDashboardSettings');

$user = User::getUser();

$tmpl = new Template('ocDashboard', 'settings');


$w = Array();
OC::$CLASSPATH['ocdWidgets'] = 'ocDashboard/appinfo/widgetConfigs.php';
OC::$CLASSPATH['ocdFactory'] = 'ocDashboard/lib/factory.php';

foreach (Widgets::$widgets as $widget) {
	$confs = json_decode($widget['conf'], true);
	if(isset($confs) && !empty($confs)) {
		foreach ($confs as $k => $config) {
			if( $config['type'] != 'password') {
				$confs[$k]['value'] = Config::getUserValue($user, "ocDashboard", "ocDashboard_".$widget['id']."_".$config['id'],"");
			}
		}
	}				
	$enable = Config::getUserValue($user, "ocDashboard", "ocDashboard_".$widget['id']);
	$w[] = Array( "widget" => $widget, "enable" => $enable, "conf" => $confs);
}

//print_r($w);

$tmpl->assign('widgets', $w);

return $tmpl->fetchPage();
