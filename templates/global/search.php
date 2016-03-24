<?php

require_once($Path_Templates_Base."product-list.php");

$keywords=(string) $_REQUEST['keywords'];
$current_page= (int)$result['current_page'];
$tracking_id= NEXTOPIA_PUBLIC_ID;
$refine_sub = '';

if($result['result_found']) {

	if(strpos($_SERVER['QUERY_STRING'],'Subcategory') > 0 ){
		$refine_sub = strpos($_SERVER['QUERY_STRING'],'Subcategory');
	}
	if(count($result['sidebar_array']) == 0) {
		$refine_sub = 1;
	}
 //for side bar in case of subcategory or no sidebar
	if ($refine_sub == 0 && count($result['sidebar_array']) > 0  ) {
?>

		<div id='column-1' class='column span-6 sidebar-nav search-results'>
			<p id='sign-category' class='h3 h3-rev pad-left-10'>Refine Your Results</p>

			<div id='vertical-list-of-categories' class="append-bottom">
				<div class="pad-left-10 top-space bottom-space">
<?php
					if(!empty($result['suggested_spelling'])) {
?>
						<p class="h4 first-margin-prepend-top-5">More search suggestions: </p>
						<p><?php echo $result['sugg_string'];?></p>
<?php
					}
?>
				</div>

<?php
		//Loop through each element for side bar
		foreach ($result['sidebar_array'] as $result_key => $result_value) {

			foreach ($result_value as $sub_key => $sub_value) {
?>
				<div class="search-sidebar-submenu">
					<p class='h4 first-margin prepend-top-5'>Filter by <?php print $sub_value['name'];?></p>
					<ul class="alpha-list-of-categories">

<?php
					foreach ($sub_value as $categories_key => $categories_value) {

						if( is_array($categories_value) ) {

							foreach ($categories_value as $key => $value) {  ?>

									<li>
										<a href='<?php print htmlspecialchars($value['url'], ENT_QUOTES, 'UTF-8'); ?>'>
											<?php print htmlspecialchars($value['sub_name'], ENT_QUOTES, 'UTF-8'); ?>
											<span class='special-note'> (<?php print htmlspecialchars(
													$value['num'], ENT_QUOTES, 'UTF-8'
												); ?>)</span>
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
?>			</div>
		</div>
<?php
	}

	// product list along with filters & sorting starts here
?>
			<div id='column-2' class='column last search-results'>
				<p class="h3 h3-rev pad-left-10">
					<span>Search Results</span>
					<span id='relevance' class='right-side font-12 normal-weight top-space last-margin'>
						<span>Sort by:</span>
						<span>
<?php
							// Sorting ---------------
							$manual_sort_bys = array("Relevance", "Name");
							$sort_position=strpos($_SERVER['QUERY_STRING'],'sort_by_field');
							$sort_type_position=strpos($_SERVER['QUERY_STRING'],'SC',$sort_position);

							if ($sort_type_position==0) {
								$sort_type='ASC';
							}

							$sort_asc=strpos($_SERVER['QUERY_STRING'],'ASC',$sort_position);

							if ($sort_asc>0) {
								$sort_type='DESC';
							}

							$sort_desc=strpos($_SERVER['QUERY_STRING'],'DESC',$sort_position);

							if ($sort_desc>0) {
								$sort_type='ASC';
							}

							foreach ($manual_sort_bys as $msb) {
								$msba = explode(":", $msb);
								if ($msb == "Relevance") {
									echo "<a class='reversed-link";
									if ($sort_type_position==0) { echo "bold"; }
									echo "' href='" . htmlspecialchars($result['sortUrl'],ENT_QUOTES,'UTF-8') . "'>" . $msba[0] . "</a> | ";
								} else {
									echo "<a class='reversed-link";
									if ($sort_asc > 0 || $sort_desc > 0) { echo "bold"; }
									echo "' href='" . htmlspecialchars($result['sortUrl'],ENT_QUOTES,'UTF-8') . htmlspecialchars('&',ENT_QUOTES,'UTF-8')."sort_by_field=" . $msb . ":".$sort_type."'><span>" . $msb . "</span>";

										if ($sort_type == "ASC" && $sort_type_position != 0) {
											echo "<i class='sprite sprite-up-white-small right-side'></i>";
										} elseif ($sort_type == "DESC" && $sort_type_position != 0) {
											echo "<i class='sprite sprite-down-white-small right-side'></i>";
										}

									echo "</a>";
								}
							}
?>
						</span>
					</span>
				</p>

				<div class="search-navigation-wrapper clear">
					<span class="normal-weight pad-left-5">
						<span>Viewing: </span>
						<span class="bold">
							<?php print htmlspecialchars($result['product_min'],ENT_QUOTES,'UTF-8');?> - <?php print htmlspecialchars($result['product_max'],ENT_QUOTES,'UTF-8') ;?>
						</span>
						<span>&nbsp;of </span>
						<span class="bold"><?php print htmlspecialchars($result['total_products'],ENT_QUOTES,'UTF-8');?></span>
					</span>

<?php
					if ($result['total_pages'] > 1) {
?>
						<div class='results-pagination last'>
							<span>Pages:&nbsp;</span>
							<div class="bold right-side">
<?php
								// Pagination-----------------------
								// First and previous pages
								if ($result['total_pages'] > 1 && $result['current_page'] > 1) {
									$CurrUrl = preg_replace($result['replace_page'], "page=" . ($result['current_page']-1), $result['PageOneUrl']);
?>
									<a href='<?php print htmlspecialchars($CurrUrl,ENT_QUOTES,'UTF-8'); ?>' class="search-page-nav-link">
										<i class="sprite sprite-search-left-blue"></i>
									</a>
<?php
								}

								//-- The Pages
								if ($result['total_pages'] < 10  && $result['total_pages'] > 1) {
									for ($i = 1; $i <= $result['total_pages']; $i++) {
										if ($i == $result['current_page']) {
											print $i;
										} else {
											$CurrUrl = preg_replace($result['replace_page'], "page=" . $i,  $result['PageOneUrl']);
											?>
											<a href='<?php print htmlspecialchars($CurrUrl,ENT_QUOTES,'UTF-8');?>'><?php print $i;?> </a>
											<?php
										}
									}

								} elseif ($result['total_pages'] > 1) {

									$PStart = $result['current_page'] - 4;

									if ($PStart < 1) {
										$PStart = 1;
									}

									for ($i = $PStart; $i < $result['current_page']; $i++) {
										$CurrUrl = preg_replace($result['replace_page'], "page=" . $i,  $result['PageOneUrl']);
?>
										<a href='<?php print htmlspecialchars($CurrUrl,ENT_QUOTES,'UTF-8');?>'>
											<?php print $i;?>
										</a>
<?php
									}

									print $result['current_page'] ;
									$PEnd = $result['current_page'] + 5;

									if ($PEnd > $result['total_pages']) {
										$PEnd = $result['total_pages'];
									}

									for ($i = $result['current_page']+1; $i < $PEnd; $i++) {
										$CurrUrl = preg_replace($result['replace_page'], "page=" . $i,  $result['PageOneUrl']);
?>
										<a href='<?php print htmlspecialchars($CurrUrl,ENT_QUOTES,'UTF-8');?>'> <?php print $i;?> </a>
<?php
									}

								}


								//-- Next and last pages
								if ($result['total_pages'] > 1 && $result['current_page'] < $result['total_pages']) {
									$CurrUrl = preg_replace($result['replace_page'], "page=" . ($result['current_page']+1),  $result['PageOneUrl']);
?>
									<a href='<?php print htmlspecialchars($CurrUrl,ENT_QUOTES,'UTF-8');?>' class="search-page-nav-link"><i class="sprite sprite-search-right-blue"></i></a>
<?php
									$CurrUrl = preg_replace($result['replace_page'], "page=" . $result['total_pages'],  $result['PageOneUrl']);

								}
?>
							</div>
						</div>

<?php
					}
?>
				</div>

				<div id='results' class='last'>
					<div class='product-subcategory-group'>
					<?php
						if ($refine_sub!=0) {
							$detail['per_row'] = 6;
						} else {
							$detail['per_row'] = 5;
						}

						$detail['grid_size'] ='large';
						$detail['show_quickview'] =TRUE;
						$detail['show_product_number'] = TRUE;
						$detail['show_filter'] = TRUE;
						$detail['show_sort'] = TRUE;

						listProducts($result['product'],"",$detail,$refine_sub,$search=true,$keywords,$tracking_id,$result['page_limit'],$current_page);
					?>
					</div>
				</div>

<?php
				if ($result['total_pages'] > 1) {
?>
					<div class='span-8 results-pagination bottom-space'>
						<span>Pages:&nbsp;</span>
						<div class="bold right-side">
<?php

						//-- Previous and first pages
						if ($result['total_pages'] > 1 && $result['current_page'] > 1) {
							$CurrUrl = preg_replace($result['replace_page'], "page=" . ($result['current_page']-1), $result['PageOneUrl']);
?>
							<a href='<?php print htmlspecialchars($CurrUrl,ENT_QUOTES,'UTF-8'); ?>' class="search-page-nav-link"><i class="sprite sprite-search-left-blue"></i></a>
<?php
						}

						//-- The Pages
						if ($result['total_pages'] < 10  && $result['total_pages'] > 1) {
								for ($i = 1; $i <= $result['total_pages']; $i++) {
									if ($i == $result['current_page']) {
										print $i;
									}
									else {
										$CurrUrl = preg_replace($result['replace_page'], "page=" . $i,  $result['PageOneUrl']);
										?>
										<a href='<?php print htmlspecialchars($CurrUrl,ENT_QUOTES,'UTF-8');?>'><?php print $i;?> </a>
										<?php
									}
								}
						} elseif ($result['total_pages'] > 1) {

							$PStart = $result['current_page'] - 4;

							If ($PStart < 1) {
								$PStart = 1;
							}

							for ($i = $PStart; $i < $result['current_page']; $i++) {

								$CurrUrl = preg_replace($result['replace_page'], "page=" . $i,  $result['PageOneUrl']);
?>
								<a href='<?php print htmlspecialchars($CurrUrl,ENT_QUOTES,'UTF-8');?>'>
									<?php print $i;?>
								</a>
<?php
							}

							print $result['current_page'] ;

							$PEnd = $result['current_page'] + 5;

							if ($PEnd > $result['total_pages']) {
								$PEnd = $result['total_pages'];
							}

							for ($i = $result['current_page']+1; $i < $PEnd; $i++) {
								$CurrUrl = preg_replace($result['replace_page'], "page=" . $i,  $result['PageOneUrl']);
?>
								<a href='<?php print htmlspecialchars($CurrUrl,ENT_QUOTES,'UTF-8');?>'>
									<?php print $i;?>
								</a>
<?php
							}

						}

						//-- The next and last pages
						if ($result['total_pages'] > 1 && $result['current_page'] < $result['total_pages']) {
							$CurrUrl = preg_replace($result['replace_page'], "page=" . ($result['current_page']+1),  $result['PageOneUrl']);
?>
							<a href='<?php print htmlspecialchars($CurrUrl,ENT_QUOTES,'UTF-8');?>' class="search-page-nav-link"><i class="sprite sprite-search-right-blue"></i></a>
<?php
							$CurrUrl = preg_replace($result['replace_page'], "page=" . $result['total_pages'],  $result['PageOneUrl']);
						}
?>
						</div>
					</div>
<?php
				}
?>

			</div>

<?php
		//no result page starts
} else {
?>

			<p class='h3 h3-rev pad-left-15'>Search Results - None Found</p>

			<div class="prepend-top pad-left-10">
				<p class='bold font-16'>
					We're sorry. There were no products that contained
					<span class="text-italic">
						<?php print htmlspecialchars($_REQUEST['keywords'],ENT_QUOTES,'UTF-8');?>
					</span>.
				</p>

				<p>
					<?php echo $result['sugg_string'];?> Please try again:
				</p>

				<div class='search-box append-bottom'>
					<form accept-charset="utf-8" class="site_search" name="search_form" method="GET" action="<?php echo htmlspecialchars($links['search'], ENT_QUOTES, 'UTF-8'); ?>">
						<input type="text" class="text" name="keywords" placeholder="Search by Keyword or Item #" value="<?php if ( isset($_GET['keywords']) && $_GET['keywords'] != '' ) { print htmlspecialchars($_GET['keywords'],ENT_QUOTES,'UTF-8');}?>" size="30" id="search-input">
						<button id="search" type="submit" class="button green">Search</button>
					</form>
				</div>

				<p class='font-14 bold clear no-margin-bottom'>Suggestions to help your search:</p>
				<ul>
					<li>Make sure all words are spelled correctly.</li>
					<li>Try different keywords.</li>
					<li>Try more general keywords.</li>
					<li>You can narrow your search results later.</li>
				</ul>
			</div>

			<div class='span-24 prepend-top'><p class='h4 h4-rev pad-left-15'>Or Browse Our Categories:</p></div>
<?php

			include($Path_Templates_Base."home-grid.php");
		}
?>




