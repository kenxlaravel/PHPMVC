<?php
	//Get the sidebar listings
	$sidebar_listings = $ObjHelp->getListings('sidebar');
	$link = new Page('help');
?>
<div id="column-1" class="column span-6 sidebar-nav prepend-top-5 help-sidebar">
	<h2 id="sign-category" class="h4 h4-rev pad-left-15">
		<a id="view-all" class="js" href="<?php echo $link->getUrl(); ?>" >
			Help Center</a>
	</h2>

	<div id="vertical-list-of-categories" class="append-bottom">
		<ul class="alpha-list-of-categories">

<?php
			foreach ($sidebar_listings AS $listing) {
?>
				<li class="category current">
					<div class="js groupfilter">
						<?php echo $listing['name']; ?>
					</div>
					<ul>
<?php
						foreach ($listing['sections'] AS $value) {

							if ($value['name'] != 'Help') {
								$link = new Page('help', $value['id']);
?>
								<li><a class="subgroup<?php echo ($value['id'] == PAGE_ID ? ' subgroupcategorybg' : ''); ?>" href="<?php echo $link->getUrl(); ?>"><?php echo $value['name']; ?></a></li>
<?php
							}
						}
?>
					</ul>
				</li>

<?php
			}
?>

		</ul>
	</div>
</div>