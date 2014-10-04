<table>
    <tr>
        <th><b><?php p($l->t('From')); ?></b></th>
        <th><b><?php  p($l->t('About')); ?></b></th>
    </tr>

    <?php

    for($i = 0; $i < count($additionalparams['mails']); $i++){
    ?>

        <tr>
            <td><?php p($additionalparams['mails'][$i]['from']); ?></td>
            <td><?php p($additionalparams['mails'][$i]['subject']); ?></td>
        </tr>

    <?php
    }

    if($additionalparams['numberAllMails'] != "") {
    ?>

        <tr><td colspan='2'><span><?php p($l->t("There are %d new mails...", $additionalparams['numberAllMails'])); ?></span></td></tr>

    <?php
    }
    ?>
</table>