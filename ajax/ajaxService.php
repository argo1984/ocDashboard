<?php

namespace OCA\ocDashboard\Ajax;

use OC;
use OCA\ocDashboard\Appinfo\WidgetConfigs;
use OCA\ocDashboard\Factory;
use OCP\App;
use OCP\Config;
use OCP\JSON;
use OCP\User;

JSON::callCheck();
User::checkLoggedIn();
App::checkAppEnabled('ocDashboard');


OC::$CLASSPATH['ocdWidgets'] = 'ocDashboard/appinfo/widgetConfigs.php';
OC::$CLASSPATH['ocdFactory'] = 'ocDashboard/lib/factory.php';

$user = User::getUser();
$id=isset($_GET['id'])?$_GET['id']:$_POST['id'];
$method=isset($_GET['method'])?$_GET['method']:$_POST['method'];
$value=isset($_GET['value'])?$_GET['value']:$_POST['value'];
$widgetEnabled = (Config::getUserValue($user, "ocDashboard", "ocDashboard_".$id) == "yes");



$RESPONSE["debug"] =    "user: ".$user." | ".
                        "widget: ".$id." | ".
                        "method: ".$method." | ".
                        "value: ".$value." | ".
                        "widgetEnabled: ".$widgetEnabled;



// if widget is enabled
if ($widgetEnabled) {
	$widgetConf = WidgetConfigs::getWidgetConfigById($id);
	$widget = Factory::getWidget($widgetConf);
	$result = "";
	if (method_exists($widget, $method)) {
		$result = $widget->$method($value);
	}
	
	if ($result) {
		$RESPONSE["response"] = $result;
		$RESPONSE["success"] = true;
	} else {
		$RESPONSE["success"] = false;
	}
} else {
	$RESPONSE["success"] = false;
}

die(json_encode($RESPONSE));