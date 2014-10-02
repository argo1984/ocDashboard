<?php
/**
 * Created by PhpStorm.
 * User: flost
 * Date: 02.10.14
 * Time: 21:00
 */

namespace OCA\ocDashboard;


use OCP\Config;

class Helper {

    /**
     *
     * return true, if $check is lower or equal
     * than the number of the main oc version
     *
     * @param $check
     * @return bool
     */
    public function isLowerThanVersion($check) {
        $parts = explode('.', Config::getSystemValue('version'));
        return ($parts[0] <= $check);
    }
} 