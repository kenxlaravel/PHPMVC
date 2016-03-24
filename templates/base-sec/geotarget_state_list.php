<?php

	/**
	 * Displays a list of available zones for the current subcategory
	 * @param    string    $type    The type of list. Options are 'dropdown' and 'full'
	 */
	function listZones($type) {

        global $row;

        $flag = 0;

		$objGeotargetPage = GeotargetPage::create(PAGE_ID);
		$objSubcategoryPage = SubcategoryPage::create(PAGE_ID);

		//Get the current page zone, and a list of all zones
		$zone_id = (PAGE_TYPE == 'subcategory' ? PAGE_ID : $objGeotargetPage->getSubcategoryId());
		$link = new Page('subcategory', $zone_id);
		$subcategory_link = $link->getUrl();
		$zones = $objGeotargetPage->getGeotargetList($zone_id);
		$total_zones = count($zones);



		//If there are 15 or less zones uses a three column layout
		if ($total_zones <= 15) {

			//Number of zones in each column
			$per_column = ceil($total_zones / 3);

		//More than 15 zones uses a five column layout
		} else {

			//Number of zones in each column
			$per_column = ceil($total_zones / 5);

			//Special case for numbers that break the column logic (16 is the only confirmed case)
			if (ceil($total_zones / $per_column) < 5) {
				$flag = 1;
			}

		}


		//Display the wrappers based on the list type and the number of zones
		if ($type == 'dropdown') {

			if ($total_zones <= 15) {
?>
				<div class="geo-wrapper">
					<p class="button green">
						<span class="button-text left-side">Change State</span> <span class="sprite-wrapper"><i class="sprite sprite-down-white left-side"></i></span>
					</p>
					<div class="dropdown">
						<div class="clearfix">
<?php
			} else {
?>
				<div class="geo-wrapper">
					<p class="button green">
						<span class="button-text left-side">Change State</span> <span class="sprite-wrapper"><i class="sprite sprite-down-white left-side"></i></span>
					</p>
					<div class="dropdown">
						<div class="clearfix">
<?php
			}

		}

		if ($type == 'full') {

			if (PAGE_TYPE == 'subcategory' && count($row) <= 0) {

				//Layout for handling 15 or less zones (3 column)
				if ($total_zones <= 15) {
?>
					<div class="append-bottom clearfix">
					<div class="product-filters">
					<div class="sort-filter span-18 last geo-product-wrapper">
					<h2 class="h4 h4-rev pad-left-15"><?php echo htmlspecialchars($objSubcategoryPage->getGeotargetStateListHeader(), ENT_QUOTES, 'UTF-8'); ?></h2>
					<p class="prepend-top span-6 left-side"><?php echo htmlspecialchars($objSubcategoryPage->getGeotargetStateListIntro(), ENT_QUOTES, 'UTF-8'); ?></p>

					<div class="span-11 last first-margin prepend-top left-side">
<?php
				} else {
?>					<div class="append-bottom clearfix">
					<div class="product-filters">
					<div class="sort-filter span-18 last geo-product-wrapper" >
					<h2 class="h4 h4-rev pad-left-15"><?php echo htmlspecialchars($objSubcategoryPage->getGeotargetStateListHeader(), ENT_QUOTES, 'UTF-8'); ?></h2>
					<p class="first-margin prepend-top last-margin"><?php echo htmlspecialchars($objSubcategoryPage->getGeotargetStateListIntro(), ENT_QUOTES, 'UTF-8'); ?></p>

					<div class="span-18 last first-margin prepend-top">
<?php
				}

			}


			if (PAGE_TYPE == 'subcategory' && count($row) > 0) {

				//Layout for handling 15 or less zones (3 column)
				if ($total_zones <= 15) {
?>					<div class="append-bottom clearfix">
					<div class="product-filters">
					<div class="sort-filter span-18 last geo-product-wrapper" >
					<h2 class="h4 h4-rev pad-left-15"><?php echo htmlspecialchars($objSubcategoryPage->getGeotargetStateListHeader(), ENT_QUOTES, 'UTF-8'); ?></h2>
					<p class="prepend-top span-6 left-side"><?php echo htmlspecialchars($objSubcategoryPage->getGeotargetStateListIntro(), ENT_QUOTES, 'UTF-8'); ?></p>

					<div class="span-11 last first-margin prepend-top left-side">
<?php
				} else {
?>					<div class="append-bottom clearfix">
					<div class="product-filters">
					<div class="sort-filter span-18 last geo-product-wrapper" >
					<h2 class="h4 h4-rev pad-left-15"><?php echo htmlspecialchars($objSubcategoryPage->getGeotargetStateListHeader(), ENT_QUOTES, 'UTF-8'); ?></h2>
					<p class="first-margin prepend-top last-margin"><?php echo htmlspecialchars($objSubcategoryPage->getGeotargetStateListIntro(), ENT_QUOTES, 'UTF-8'); ?></p>

					<div class="span-18 last first-margin prepend-top">
<?php
				}

			}

		}


		if ($type == 'full-green') {
?>			<div class="append-bottom clearfix">
			<div class="product-filters">
			<div class="sort-filter span-18 last geo-product-wrapper" >
			<h2 class="h4 h4-rev pad-left-15"><?php echo htmlspecialchars($objSubcategoryPage->getGeotargetStateListHeader(), ENT_QUOTES, 'UTF-8'); ?></h2>
			<p class="first-margin prepend-top last-margin"><?php echo htmlspecialchars($objSubcategoryPage->getGeotargetStateListIntro(), ENT_QUOTES, 'UTF-8'); ?></p>

			<div class="span-18 last first-margin prepend-top">

<?php
		}

		//Reset the counts before the loop
		$count1 = 1;
		$count2 = 1;

		//Loop through all the zones
		foreach ($zones AS $zone) {

			//If this is the first zone, start a column
			if ($count1 == 1) {
?>
				<ul class="left-side">
<?php
			}

			//Instantiate a new page for the current zone
			$link = new Page('geotarget', $zone['id']);

			//Output the zone link
			$current = ($zone['id'] == PAGE_ID && PAGE_TYPE == 'geotarget' ? " class='current'" : "");
			echo "<li" . $current . "><a href='" . $link->getUrl() . "'>" . htmlspecialchars($zone['zone_name'], ENT_QUOTES, 'UTF-8') . "</a></li>";

			//End a column and start a new one
			if ($count2 == $per_column) {
?>
				</ul>
				<ul class="left-side">
<?php
				$count2 = 0;
			}

			//Case for 16 zones - The number that breaks everything
			if (($count1 == $per_column) && $flag == 1) {
				$per_column--;
				$flag = 0;
			}

			//Last zone, end
			if ($count1 == $total_zones) {
?>
				</ul>
<?php
			}

			//Increment the counters
			$count1++;
			$count2++;

		}


		if ($type == 'dropdown') {
?>

					</div>
					<div class="clearfix button-wrap">
						<p class="left-side bold last-margin"><?php echo htmlspecialchars($objSubcategoryPage->getGeotargetDropdownSnippet(), ENT_QUOTES, 'UTF-8'); ?></p>
						<a href="<?php echo htmlspecialchars($subcategory_link, ENT_QUOTES, 'UTF-8'); ?>" class="button green first-margin"><span class="left-side"><?php echo htmlspecialchars($objSubcategoryPage->getGeotargetDropdownButton(), ENT_QUOTES, 'UTF-8'); ?></span><i class="sprite sprite-right-white"></i></a>
					</div>
				</div>
			</div>
<?php
		}

		if ($type == 'full') {
?>
			</div>
			</div>
					</div>
					</div>
<?php
		}

		if ($type == 'full-green') {
?>
			</div>
			<div class="clearfix button-wrap">
				<p class="left-side bold"><?php echo htmlspecialchars($objSubcategoryPage->getGeotargetDropdownSnippet(), ENT_QUOTES, 'UTF-8'); ?></p>
				<a href="<?php echo htmlspecialchars($subcategory_link, ENT_QUOTES, 'UTF-8'); ?>" class="button green first-margin"><span class="left-side"><?php echo htmlspecialchars($objSubcategoryPage->getGeotargetDropdownButton(), ENT_QUOTES, 'UTF-8'); ?></span><i class="sprite sprite-right-white"></i></a>
			</div>
					</div>
					</div>
					</div>
<?php
		}
	}
?>