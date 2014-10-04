<?php

    function getNiceSmallText($string) {
        $maxStringLen = 40;
        $startStringLen = 5;

        if(strlen($string) >= $maxStringLen) {
            $lastCharacter = -1 * ($maxStringLen-$startStringLen);
            $return = substr($string,0,$startStringLen);
            $return .= "...";
            $return .= substr($string,$lastCharacter);
        } else {
            $return = $string;
        }
        return $return;
    }

?>

<table>
<tr><th>&nbsp;</th></tr>
    <?php

    /** @var $additionalparams array */
    foreach ($additionalparams['activitys'] as $activity)
    {
        print_unescaped(
            "<tr><td><div class='priority".$activity['priority']." activity-entry'>
                <a href='".$activity["link"]."' title='".$activity['subject']."'>".\OC_Util::sanitizeHTML(getNiceSmallText($activity['subject'])).
                "</a><br /><span> ".
                    \OCP\relative_modified_date(date("U",strtotime($activity['date']))).
                "</span>
            </div></td></tr>
        ");
    }

    ?>

</table>