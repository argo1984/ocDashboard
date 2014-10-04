<table>
<tr><th>&nbsp;</th></tr>
<?php
foreach ($additionalparams['bookmarks'] as $bookmark) {
    if ($bookmark['title'] == "") {
        $titel = $bookmark['url'];
    } else {
        $titel = $bookmark['title'];
    } ?>

    <tr><td><a target="_blank" href="<?php p($bookmark['url']); ?>"><?php p($titel); ?></a></td></tr>

<?php
} ?>

</table>