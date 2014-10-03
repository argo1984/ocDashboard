<?php

namespace OCA\ocDashboard;

use OC;
use OC_L10N;
use OCA\ocDashboard\Appinfo\WidgetConfigs;
use OCP\App;
use OCP\Config;
use OCP\Template;
use OCP\User;
use OCP\Util;

User::checkLoggedIn();
App::checkAppEnabled('ocDashboard');
App::setActiveNavigationEntry( 'ocDashboard' );

Util::addscript('ocDashboard', 'ocDashboard');
Util::addStyle('ocDashboard', 'ocDashboard');

$user = User::getUser();

$w = Array();
foreach (WidgetConfigs::$widgets as $widget) {
	// if widget is enabled
	if (Config::getUserValue($user, "ocDashboard", "ocDashboard_".$widget['id']) == "yes") {
		$w[] = Factory::getWidget($widget)->getData();
	}
}

//if all deactivated
if(empty($w)) {
	$l = new OC_L10N('ocDashboard');
	$w[0]['error'] = "You can configure this site in your personal settings.";
	$w[0]['id'] = "none";
	$w[0]['name'] = "";
	$w[0]['status'] = "3";
	$w[0]['interval'] = "0";
	$w[0]['icon'] = "";
}

$tpl = new Template("ocDashboard", "main", "user");
$tpl->assign('widgets', $w);
$tpl->printPage();