<?php
foreach ( $_['widgets'] as $widget) {
?>

    <div class="widget <?php print_unescaped($widget['id']); ?>" data-interval="<?php print_unescaped($widget['interval']); ?>" data-id="<?php print_unescaped($widget['id']); ?>" data-status="<?php print_unescaped($widget['status']); ?>">

        <?php
        // add icon if availible
        if($widget['icon'] != "") { ?>
            <div class="icon hoverInfo" data-opacitynormal="0.25" data-opacityhover="1">
                <img
                    src="<?php
                    print_unescaped(
                        link_to( 'ocDashboard', 'img/'.$widget['icon'], array() )
                    );
                    ?>"
                />
            </div>
        <?php
        } ?>

        <?php
        // add wait symbol
        ?>
        <div class="waitSymbol icon-loading-small">&nbsp;</div>

        <?php
        // add heading (if availible with link)
        if(isset($widget['link']) AND $widget['link'] != '') { ?>
            <div class="heading hoverInfo" data-opacitynormal="0.6">
                <a href="<?php print_unescaped($widget['link']); ?>">
                    <?php print_unescaped($l->t($widget['name'])); ?>
                </a>
        <?php
        } else { ?>
            <div class="heading hoverInfo" data-opacitynormal="0.4">
                <?php print_unescaped($l->t($widget['name'])); ?>
        <?php
        }

        // add reload button
        if ( isset($widget['interval']) && $widget['interval'] != '' ) { ?>
            <span class="hoverInfo icon-history" data-opacityhover="0.4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <?php
        } ?>
        </div>


        <?php
        // for single widget output --------------------------------------------------------------------------
        if(isset($_['singleOutput']) && $_['singleOutput']) {
            print_unescaped("###?###");
        } ?>




        <?php
        // if error, just show error message
        if(isset($widget['error']) && $widget['error'] != "") { ?>
            <div class="error">
                <?php print_unescaped($l->t($widget['error'])); ?>
            </div>
        <?php
        } else {
            print_unescaped($this->inc('/widgets/'.$widget['id'].'.inc', $widget));
        } ?>




        <?php
            // for single widget output ----------------------------------------------------------------------------
            if(isset($_['singleOutput']) && $_['singleOutput']) {
                print_unescaped("###?###");
            }
        ?>

    </div>

<?php
} ?>