
<?php

$landing = $objSiteMap->getLandingPages();
$staticpage = $objSiteMap->getStaticPages();
$category = $objSiteMap->getCategoryAndSubCategoryPages();
$column = ceil(count($category)/3);

?>

<div class="span-24 last">
	<div id="browse-product-categories" class="container">
		<?php if (HTML_COMMENTS) { ?><!--SEO landing pages starts here --><?php } ?>
		<div class="span-6 sitemap-column1 append-bottom">
			<p class="h3">Popular Categories</p>
			<div class="span-5">
				<ul>

					<?php foreach ($landing as $key => $value) { ?>
						<li><a href="<?=htmlspecialchars($value['url'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8')?></a></li>
					<? } ?>

				</ul>
			</div>
			<?php if (HTML_COMMENTS) { ?><!--pages starts here --><?php } ?>
			<p class="h3">Tools &amp; Information</p>
			<div class="span-5">
				<ul>

					<?php foreach ($staticpage as $key => $value) { ?>
						<li><a href="<?=htmlspecialchars($value['url'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8')?></a></li>
					<? } ?>

				</ul>
			</div>
		</div>
		<?php if (HTML_COMMENTS) { ?><!-- List of categories starts here--><?php } ?>
		<div class="span-17 sitemap-column2 last prepend-top">
			<div>
				<?php $count = 0; foreach ($category as $key =>$value) { ++$count; ?>

					<p class="h3"><a href="<?=htmlspecialchars($value['url'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8')?></a></p>
					<ul>

						<? foreach ($value['subcategories'] as $subcategory_key => $subcategory_value) { ?>
							<li><a href="<?=htmlspecialchars($subcategory_value['url'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($subcategory_value['name'], ENT_QUOTES, 'UTF-8')?></a></li>
						<? } ?>

					</ul>

					<?php if ($count == $column) { $count=0; ?>
						</div><div>
					<?php } ?>

				<?php } ?>
			</div>
		</div>
	</div>
</div>
