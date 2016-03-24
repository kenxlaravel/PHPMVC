
<div id="landing-categories-grid" class="container" >
	<div class="landing-categories-links">
		<div class="row clearfix">
			<div class="row clearfix">
				<?php foreach (FeaturedPage::fetchData() as $k => $w): ?>

					<div class='display-signs-by-category <?=(($k % 3) == 2)?'last':"";?>'>
						<a title='<?=htmlspecialchars($w->header, ENT_QUOTES, 'UTF-8');?>' href='<?=htmlspecialchars ($w->pageUrl, ENT_QUOTES, 'UTF-8');?>'>
						<img alt='<?=htmlspecialchars ($w->header, ENT_QUOTES, 'UTF-8');?>' class='right' src='<?=IMAGE_URL_PREFIX.htmlspecialchars ($w->image, ENT_QUOTES, 'UTF-8');?>' /></a>
						<h2><a href='<?=htmlspecialchars ($w->pageUrl, ENT_QUOTES, 'UTF-8');?>'><?=htmlspecialchars ($w->header, ENT_QUOTES, 'UTF-8');?></a></h2>
						<p><?=htmlspecialchars (mb_substr ($w->snippet, 0, 255), ENT_QUOTES, 'UTF-8');?></p>
				 	</div>

					<?php if (($k % 3) === 2): ?>

					</div><div class='row clearfix'>

					<?php endif; ?>
			 	<?php endforeach;?>
			</div>
		</div>
	</div>
</div>