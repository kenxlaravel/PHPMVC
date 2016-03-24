<?php
	/**
	 * landing.php functions as a unified landing page, combining functionality
	 * from advanced and more advanced landing pages that previously existed
	 *
	 * @author  Daniel Hennion <daniel@brimar.com>
	 * @since   10.03.2012
	 */

	require_once($Path_Templates_Base."product-list.php");

    $product_listings = $objLandingPage->getListings('products');
    $grid_listings = $objLandingPage->getListings('grid');
    $grid_listings_total = count($grid_listings) - 1;
    $count = 0;

if (count($grid_listings) > 0) {
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
	}
?>


<div class="span-24 append-bottom landing-content">

<?php
	$detail['grid_size'] = $objLandingPage->getProductGridSize();
	$detail['show_quickview'] = $objLandingPage->getShowQuickview();
	$detail['show_product_number'] = $objLandingPage->getShowProductNumber();
	$detail['per_row'] = 6;
	$detail['show_filter'] = $objLandingPage->getShowFilter();
	$detail['show_sort'] = $objLandingPage->getShowSort();

	listProducts($product_listings,'',$detail);
?>

</div>