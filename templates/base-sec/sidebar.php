<?php

//If we are not on the search results page

if ( !isset($search) ) :
	//If we are on any page other than category, we have to instantiate the category class
	if (!($objCategoryPage instanceof CategoryPage)) {

        $objCategoryPage = CategoryPage::create($breadcrumbs[0]['id']);
	}

	//Get the listings of all sidebar category IDs
	$sidebar_groupings = $objCategoryPage->getListings('sidebar');

?>
<div id="column-1" class="column span-6 sidebar-nav">
	<h2 id="sign-category" class="h4 h4-rev pad-left-15">
		<a id="view-all" class="js" href="<?php echo Encoder::create( $breadcrumbs[2]['link'] )->getHtml();?>"><?php echo Encoder::create( $breadcrumbs[2]['text'] )->getHtml();?></a>
	</h2>

	<div id="vertical-list-of-categories" class="append-bottom">
		<ul class="alpha-list-of-categories">

<?php
		$total = count($sidebar_groupings);
		$count = 1;

		//Loop through the sidebar category IDs
		foreach($sidebar_groupings as $grouping) {

			//Instantiate a page and grouping
			$link = new Page('grouping', $grouping['id'], $grouping);
			$TmpObjPageGrouping = GroupingPage::create($grouping['id']);

			if ($grouping['accessory'] == 1) {
?>
				</ul>
				<div class="accessories-list-of-categories">
					<ul class="alpha-list-of-categories" >
<?php
			}

			//Check whether the grouping is current
			$current_grouping = (((PAGE_TYPE == 'grouping' || PAGE_TYPE == 'subcategory' || PAGE_TYPE == 'geotarget' || PAGE_TYPE == 'product') && $breadcrumbs[3]['id'] == $grouping['id']) ? TRUE : FALSE);
?>
			<li class="category<?php echo ($current_grouping ? ' current' : ''); ?>">
				<a href="<?php echo Encoder::create($link->getUrl() )->getHtml(); ?>" class="js groupfilter">
					<?php echo Encoder::create($link->getName() )->getHtml(); ?>
				</a>
				<ul id="hiddendes_<?php echo Encoder::create($count )->getHtml();?>" style="<?php echo ($current_grouping ? '' : 'display: none;'); ?>">
<?php
				//Get the list of groupings
				$sidebar_subcategories = $TmpObjPageGrouping->getListings('sidebar');

				//Loop through and output all the groupings
				foreach($sidebar_subcategories as $subcategory) {

					//Instantiate a page
					$link2 = new Page('subcategory', $subcategory['id'],$subcategory);

                    $geo_subcategory = FALSE; //$objSubcategoryPage->getId();

					//Check whether the subcategory is current
                    if( isset($breadcrumbs[4]) ) {
                        if( $breadcrumbs[4]['type'] == 'geotarget' ) {
                            $geo_subcategory = $objSubcategoryPage->getGeoTargetSubcategoryId();
                        }
                    }

					$current_subcategory = ((((PAGE_TYPE == 'subcategory' || PAGE_TYPE == 'product' || PAGE_TYPE == 'geotarget') && $breadcrumbs[4]['id'] == $subcategory['id']) || ($geo_subcategory == $subcategory['id'])) ? TRUE : FALSE);
?>
					<li><a class="subgroup<?php echo ($current_subcategory ? ' subgroupcategorybg' : ''); ?>" href="<?php echo Encoder::create($link2->getUrl() )->getHtml(); ?>"><?php echo Encoder::create($link2->getName() )->getHtml();?></a></li>
<?php
				}
?>
				</ul>
			</li>
<?php
			$count++;

		}
?>
		</ul>

<?php

if ($grouping['accessory'] > 0) {
 ?>
 </div>
<?php
}
?>


    </div>

<?php
$related = $page->getRelated();

if (count($related) > 0) {
    ?>
	<div id="related-categories" class="span-6">
		<p class="h5 h5-rev">Related Categories</p>
		<div>
			<ul>
<?php
    foreach ($related as $item) {

        $link = new Page($item['pagetype'], $item['pageid']);
        ?>
        <li>
            <a href='<?php echo Encoder::create($link->getUrl())->getHtml(); ?>'><?php echo Encoder::create($link->getName())->getHtml(); ?></a>
        </li>
    <?php
    }
    ?>
			</ul>
		</div>
	</div>
<?php
}
?>

</div>

<?php

//Display the search page sidebar
else :

    $keywords       = (string) $_REQUEST['keywords'];
    $current_page   = (int) $result['current_page'];
    $tracking_id    = NEXTOPIA_PUBLIC_ID;

    //For side bar in case of subcategory or no sidebar
    if ($refine_sub == 0 && count($result['sidebar_array']) > 0  ) :
?>

        <div id='column-1' class='column span-6 sidebar-nav search-results'>
            <p id='sign-category' class='h3 h3-rev pad-left-10'>Refine Your Results</p>

            <div id='vertical-list-of-categories' class="append-bottom">
                <div class="pad-left-10 top-space bottom-space">
                    <?php
                    if( ! empty($result['suggested_spelling']) ) {
                        ?>
                        <p class="h4 first-margin-prepend-top-5">More search suggestions: </p>
                        <p><?php echo $result['sugg_string'];?></p>
                    <?php
                    }
                    ?>
                </div>

                <?php

                //Loop through each element for side bar
                foreach ( $result['sidebar_array'] as $result_key => $result_value ) {

                    foreach ( $result_value as $sub_key => $sub_value ) {
                ?>
                        <div class="search-sidebar-submenu">
                            <p class='h4 first-margin prepend-top-5'>Filter by <?php print $sub_value['name'];?></p>
                            <ul class="alpha-list-of-categories">

                                <?php
                                foreach ( $sub_value as $categories_key => $categories_value ) {

                                    if( is_array($categories_value) ) {

                                        foreach ( $categories_value as $key => $value ) {  ?>

                                            <li>
                                                <a href='<?php print Encoder::create( $value['url'] )->getHtml(); ?>'>
                                                    <?php print Encoder::create( $value['sub_name'] )->getHtml(); ?>
                                                    <span class='special-note'> (<?php print Encoder::create( $value['num'] )->getHtml(); ?>)</span>
                                                </a>
                                            </li>
                                        <?php
                                        }

                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    <?php
                    }

                }
                ?>
            </div>
        </div>

<?php

    endif;

?>
<?php endif; ?>