<?php
/**
 * @author  Jason Hodulik  <jasonh@brimar.com>
 * @author  Daniel Hennion <daniel@brimar.com>
 * @since   09.28.2012
 */

?>
<?php if( HTML_COMMENTS ): ?><!-- BEGIN HEADER --><?php endif; ?>
    <div id="all-content-wrapper"> <!-- Required for sticky footer -->
    <div id="meta-bar">
        <div class="container">
            <div id="meta-header">

                <?php if( $page->getNickname() == 'home' ): ?>
                    <p><?php echo htmlspecialchars($page->getHeading(), ENT_QUOTES, 'UTF-8'); ?></p>
                <?php else: ?>
                    <h1><?php echo htmlspecialchars($page->getHeading(), ENT_QUOTES, 'UTF-8'); ?></h1>
                <?php endif; ?>
                <div class="g-plusone" data-size="small"></div>
            </div>
        </div>
    </div>
<?php //This is neccesary for all the grouping, landing.php, category.php etc... for the breadcrumbs and sidebar to show
if( HTML_COMMENTS ): ?><!-- END HEADER --><?php endif; ?>