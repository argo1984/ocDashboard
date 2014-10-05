<script>
    ocDashboard.newsreader.resizeContentImg();
</script>

<table>
    <tr>
        <th>
        <?php
        // add number of shown news and "mark as read" click
        if( isset($additionalparams['count']) && $additionalparams['count'] != "" ) { ?>

             <div class="counter">
                 <?php
                 p($additionalparams['actual'].'/'.$additionalparams['count']); ?>
                 <span class="icon-toggle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
             </div>

        <?php
        } else { ?>

            &nbsp;

        <?php
        }?>
        </th>
    </tr>

    <?php
    // add header with link if available
    if(isset($additionalparams['title']) && $additionalparams['title'] != "") { ?>
        <tr>
            <th>
                <h1>
                    <?php
                    if(isset($additionalparams["url"]) && $additionalparams["url"] != "") { ?>
                        <a target='_blank' href="<?php print_unescaped($additionalparams["url"]); ?>"><?php p($additionalparams['title']); ?></a>
                    <?php
                    } else {
                        p($additionalparams['title']);
                    }
                    ?>
                </h1>
            </th>
        </tr>
    <?php
    } ?>

    <?php
    // add pubdate if available
    if( isset($additionalparams['pubDate']) && $additionalparams['pubDate'] != '' ) { ?>
        <tr>
            <td>
                <div class="pubdate">
                    <?php
                    $l          = new OC_L10N('ocDashboard');
                    $timestamp  = intval($additionalparams['pubDate']);
                    $time       = new DateTime();
                    $time->setTimestamp($timestamp);
                    print_unescaped( \OCP\Util::formatDate($time->getTimestamp()) );
                    ?>
                </div>
            </td>
        </tr>
    <?php
    } ?>


    <?php
    // news content
    if(isset($additionalparams['body']) && $additionalparams['body'] != "") { ?>
        <tr>
            <td>
                <?php
                print_unescaped($additionalparams['body']);
                ?>
            </td>
        </tr>
    <?php
    } ?>

</table>