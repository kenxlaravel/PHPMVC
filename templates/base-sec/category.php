<?php

	$landing_product = $objCategoryPage->getListings('grid');
	$category_count = count($landing_product);


	if (sizeof($landing_product) > 0) {

?>

<div id="landing-content" class="span-18 last">
	<h2 id="default-group-heading-text" class="h4 h4-rev pad-left-15"><span><?php echo htmlspecialchars($page->getName(), ENT_QUOTES, 'UTF-8');?></span></h2>
	<div class="product-specific-group">
<?php

			$i=-1;
			foreach($landing_product as $key => $product_data) {
				$i++;
				$link = Page::create('grouping', $product_data['id'], $product_data);


				$ObjGroupingPage = GroupingPage::create($product_data['id']);
				$link = Page::create('grouping', $product_data['id']);
?>
		<a class="t-nail" href="<?php echo htmlspecialchars($link->getUrl(), ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlspecialchars($link->getName(), ENT_QUOTES, 'UTF-8'); ?>">
			<div class="product-container">
				<img src="<?php echo IMAGE_URL_PREFIX . htmlspecialchars($product_data['image'], ENT_QUOTES, 'UTF-8');?>" class="product-thumbnail" alt="<?php echo htmlspecialchars($link->getName(), ENT_QUOTES, 'UTF-8');?>" />
				<h2><?php echo htmlspecialchars($link->getName(), ENT_QUOTES, 'UTF-8');?></h2>
				<p><?php echo htmlspecialchars($product_data['snippet'], ENT_QUOTES, 'UTF-8'); ?></p>

			</div>
		</a>
<?php
			}
?>
	</div>
</div>
<?php
	}

?>