<?php
$main_category = $ObjPageHomepage->getListings('category_grid');

$count_main_category = count($main_category);

if($count_main_category>0) {
?>
<?php if (HTML_COMMENTS) {?><!-- BEGIN HOME CATEGORIES --><?php } ?>

<div id="browse-product-categories" class="container three-across">
	<div class="row clearfix">
		<?php

			$row=0;
			$img=0;

			foreach($main_category as $key => $main_category_value) {

				$CategoryPage = new CategoryPage($main_category_value['id']);

				if($row=="4") { echo "</div><div class='row clearfix'>"; $row=0;}

				$img++;

?>
		<div class="display-signs-by-category <?php if($row=="3") { echo "last"; }?>">

			<a href="<?php echo htmlspecialchars($CategoryPage->getUrl(), ENT_QUOTES, 'UTF-8');?>" title="<?php echo htmlspecialchars($CategoryPage->getName(), ENT_QUOTES, 'UTF-8');?>">
				<img src="<?php
				echo htmlspecialchars(IMAGE_URL_PREFIX . $CategoryPage->getImage(), ENT_QUOTES, 'UTF-8');
				?>" class="right" alt="<?php echo htmlspecialchars($CategoryPage->getName(), ENT_QUOTES, 'UTF-8')?>"/>
			</a>

			<h2>
				<a href="<?php echo htmlspecialchars($CategoryPage->getUrl(), ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars($CategoryPage->getName(), ENT_QUOTES, 'UTF-8');?></a>
			</h2>

			<ul class="append-bottom">
				<?php
					$main_category_id=$main_category_value['id'];
					$sub_category = $ObjPageHomepage->getListings('category_gridsub', $main_category_id);


					foreach($sub_category as $key => $category_value) {

						$SubPage = Page::getPageByTypeAndId($category_value['type'], $category_value['ref_id']);

?>
						<li><a href="<?php echo htmlspecialchars($SubPage->getUrl(), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($SubPage->getName(), ENT_QUOTES, 'UTF-8');?></a></li>
<?php
					}
?>
			</ul>
			<a class="alt-green button" href="<?php echo htmlspecialchars($CategoryPage->getUrl(), ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlspecialchars($CategoryPage->getNickname(), ENT_QUOTES, 'UTF-8');?>"><span><?php echo htmlspecialchars($CategoryPage->getName(), ENT_QUOTES, 'UTF-8');?></span> <i class="sprite sprite-right-white"></i></a></div>
		<?php
				$row++;
			}
?>
	</div>
</div>
<?php if (HTML_COMMENTS) {?>
<!-- END HOME CATEGORIES -->
<?php } ?>
<?php
}
?>
