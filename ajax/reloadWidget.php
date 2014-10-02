<?php

namespace OCA\ocDashboard\Ajax;

use OC;
use OCA\ocDashboard\Factory;
use OCA\ocDashboard\Widgets;
use OCP\App;
use OCP\Config;
use OCP\JSON;
use OCP\Template;
use OCP\User;

User::checkLoggedIn();
App::checkAppEnabled('ocDashboard');
JSON::callCheck();

OC::$CLASSPATH['ocdWidgets'] = 'ocDashboard/appinfo/widgetConfigs.php';
$id = str_replace(array('/', '\\'), '',  $_GET['widget']);
$user = User::getUser();

$widgetArray = Widgets::getWidgetConfigById($id);

OC::$CLASSPATH['ocdFactory'] = 'ocDashboard/lib/factory.php';

if (Config::getUserValue($user, "ocDashboard", "ocDashboard_".$id) == "yes") {
	
	$widgetData = Factory::getWidget($widgetArray)->getData();
	$tpl = new Template("ocDashboard", "main", "user");
	$tpl->assign('widgets', Array($widgetData));
	$tpl->assign('singleOutput', true);
	$widgetHtml = $tpl->fetchPage();
	$tmp = explode('###?###', $widgetHtml);
	$html = $tmp[1];
	
	$RESPONSE['data'] = "";
	if($html) {
		$RESPONSE["success"] = true;
		$RESPONSE["HTML"] = $html;
		$RESPONSE['STATUS'] = $widgetData['status'];
	} else {
		$RESPONSE["success"] = false;
	}
} else {
	$RESPONSE["success"] = false;
}

$RESPONSE["id"] = $id;
die(json_encode($RESPONSE));