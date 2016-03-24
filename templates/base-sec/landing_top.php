<?php
	/**
	 * @author  Abu Bah <abu@brimar.com>
	 * @since   03.20.2015
	 */

    $grid_listings = $objLandingPage->getListings();
    $grid_listings_total = count($grid_listings) - 1;
    $count = 0;

if (count($grid_listings) > 0) :

?>


		<div class="container clear append-bottom landing-feature<?php echo ($objLandingPage->getGridAlternate() ? ' alternate' : ''); ?>">
<?php
			foreach($grid_listings as $listing) {

                if( !isset($grid_listings_count) ) $grid_listings_count = 0;

				if ( $grid_listings_count == 0) {
?>
					<div class="row clear">
<?php
				}
				if ($grid_listings_count % 3 == 0 && $grid_listings_count != $grid_listings_total && $grid_listings_count != 0) {
?>
					</div>
					<div class="row clear">
<?php
				}

				$link = new Page($listing['type'], $listing['ref_id']);
?>
				<div class="display-signs-by-category"> <a title="<?php echo $listing['name']; ?>" href="<?php echo $link->getUrl(); ?>"><img alt="<?php echo $listing['name']; ?>" class="right" src="<?php echo IMAGE_URL_PREFIX.htmlspecialchars($listing['thumbnail'], ENT_QUOTES, 'UTF-8'); ?>"></a>
					<h2><a href="<?php echo $link->getUrl(); ?>" title="<?php echo $listing['name']; ?>"><?php echo $listing['name']; ?></a></h2>
					<p><?php echo htmlspecialchars($listing['snippet'], ENT_QUOTES, 'UTF-8'); ?></p>
				</div>
<?php
				if ($grid_listings_count == $grid_listings_total) {
?>
					</div>
<?php
				}

				$grid_listings_count++;
			}
?>
		</div>

<?php
	endif;
?>