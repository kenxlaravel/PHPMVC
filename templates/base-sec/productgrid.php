<?php

/**
 * @author  Abu Bah <abu@brimar.com>
 * @since   03.18.2015
 */

?>

<?php if ( $pageType != 'landing' && !isset($search) && $firstGrid == true) : ?>

    <div id="column-2" class="span-18 last">

<?php endif; ?>

<?php if ( $pageType == 'landing' ) : ?>

    <?php if ( !empty($landingTopGridListings) ) : ?>

        <div class="container clear append-bottom landing-feature<?= ($objLandingPage->getGridAlternate() ? ' alternate' : ''); ?>">

            <div class="row clear">

                <?php foreach ( $landingTopGridListings as $listing ) : ?>

                    <?php if ( $gridListingsCount % 3 == 0 && $gridListingsCount != 0 ) : ?>

                        </div>

                        <div class="row clear">

                    <?php endif; ?>

                    <div class="display-signs-by-category">
                        <a title="<?= $listing['name']; ?>" href="<?= $listing['link']; ?>"><img alt="<?= $listing['name']; ?>" class="right" src="<?= IMAGE_URL_PREFIX.Encoder::html($listing['thumbnail']); ?>"></a>
                        <h2><a href="<?= $listing['link']; ?>" title="<?= $listing['name']; ?>"><?= $listing['name']; ?></a></h2>
                        <p><?= Encoder::html($listing['snippet']); ?></p>
                    </div>

                    <?php if ( $gridListingsCount == $topGridListingsTotal ) : ?>

                        </div>

                    <?php endif; ?>

                <?php $gridListingsCount++; endforeach; ?>

            </div>

    <?php endif; ?>

    <div class="span-24 append-bottom landing-content">

<?php endif; ?>

<?php if ( isset($search) ) : ?>

    <?php if ( $result['result_found'] ) : ?>

        <div id='column-2' class='column last search-results'>

            <?php //Display search grid header title and sorting, along with pagination ?>

            <p class="h3 h3-rev pad-left-10">
                <span>Search Results</span>
                <span id='relevance' class='right-side font-12 normal-weight top-space last-margin'>
                    <span>Sort by:</span>
                    <span>

                        <?php foreach ( $manualSortBys as $msb ) : ?>

                            <?php $msba = explode(':', $msb); ?>

                            <?php if ( $msb == 'Relevance' ) : ?>

                                <a class='reversed-link<?= $sortTypePosition == 0 ? 'bold' : '' ?>' href='<?= Encoder::html($result['sortUrl']) ?>'><?= $msba[0] ?></a> |

                            <?php else : ?>

                                <a class='reversed-link<?= $sortAsc > 0 || $sortDesc > 0 ? 'bold' : '' ?>' href='<?= Encoder::html($result['sortUrl']) . Encoder::html('&') . "sort_by_field=" . $msb . ":" . $sortType ?>'>
                                    <span><?= $msb ?></span>

                                    <?php if ( $sortType == 'ASC' && $sortTypePosition != 0 ) : ?>

                                        <i class='sprite sprite-up-white-small right-side'></i>

                                    <?php endif; ?>

                                    <?php if ( $sortType == 'DESC' && $sortTypePosition != 0 ) : ?>

                                        <i class='sprite sprite-down-white-small right-side'></i>

                                    <?php endif; ?>

                                </a>

                            <?php endif; ?>

                        <?php endforeach; ?>

                    </span>
                </span>
            </p>

            <div class="search-navigation-wrapper clear">
                <span class="normal-weight pad-left-5">
                    <span>Viewing: </span>
                    <span class="bold"><?= Encoder::html($result['product_min']); ?>- <?= Encoder::html($result['product_max']); ?></span>
                    <span>&nbsp;of </span>
                    <span class="bold"><?= Encoder::html($result['total_products']); ?></span>
                </span>

                <?php if ( $result['total_pages'] > 1 ) : ?>

                    <div class='results-pagination last'>
                        <span>Pages:&nbsp;</span>

                        <div class="bold right-side">

                            <?php if ( !empty($PreviousPageUrl) ) : ?>

                                <a href='<?= Encoder::html($PreviousPageUrl); ?>' class="search-page-nav-link"><i class="sprite sprite-search-left-blue"></i></a>

                            <?php endif; ?>

                            <?php if ( !empty($pagesList) ) : ?>

                                <?php foreach ( $pagesList as $page ) : ?>

                                    <?php if ( $page != $result['current_page'] ) : ?>

                                        <a href='<?= Encoder::html($page[0]); ?>'><?= $page[1]; ?></a>

                                    <?php else : ?>

                                        <?= $page; ?>

                                    <?php endif; ?>

                                <?php endforeach; ?>

                            <?php endif; ?>

                            <?php if ( !empty($NextPageUrl) ) : ?>

                                <a href='<?= Encoder::html($NextPageUrl); ?>' class="search-page-nav-link"><i class="sprite sprite-search-right-blue"></i></a>

                            <?php endif; ?>

                        </div>
                    </div>

                <?php endif; ?>

            </div>

            <div id='results' class='last'>
            <div class='product-subcategory-group'>

    <?php else : ?>

        <p class='h3 h3-rev pad-left-15'>Search Results - None Found</p>

        <div class="prepend-top pad-left-10">
            <p class='bold font-16'>
                We're sorry. There were no products that contained
                    <span class="text-italic">
                        <?= Encoder::html($_REQUEST['keywords']); ?>
                    </span>.
            </p>

            <p>
                <?= $result['sugg_string']; ?> Please try again:
            </p>

            <div class='search-box append-bottom'>
                <form accept-charset="utf-8" class="site_search" name="search_form" method="GET" action="<?= Encoder::html($links['search']); ?>">
                    <input type="text" class="text" name="keywords" placeholder="Search by Keyword or Item #" value="<?= Encoder::html($_GET['keywords']) ?>" size="30" id="search-input">
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

            //Display our Home Category Grid template
            echo Template::generate('base-sec/home-grid', array(
                    'ObjPageHomepage' => $ObjPageHomepage
                )
            );

        ?>

    <?php endif; ?>

<?php endif; ?>

<?php if ( $pageType == 'subcategory' && $objSubcategoryPage->getTemplate() == 'geotarget' ) : ?>

    <div class="append-bottom clearfix">
    <div class="product-filters">
        <div class="sort-filter span-18 last geo-product-wrapper">
            <h2 class="h4 h4-rev pad-left-15"><?= Encoder::html($objSubcategoryPage->getGeotargetStateListHeader()); ?></h2>

            <?php if ( $totalZones <= 15 ) : ?>

                <p class="prepend-top span-6 left-side"><?= Encoder::html($objSubcategoryPage->getGeotargetStateListIntro()); ?></p>
                <div class="span-11 last first-margin prepend-top left-side">

            <?php else : ?>

                <p class="first-margin prepend-top last-margin"><?= Encoder::html($objSubcategoryPage->getGeotargetStateListIntro()); ?></p>
                <div class="span-18 last first-margin prepend-top">

            <?php endif; ?>

                    <?php $columnCount = 1; ?>

                    <ul class="left-side">

                        <?php foreach ( $zonesList as $zone ) : ?>

                            <li><a href="<?= $zone['link']; ?>"><?= Encoder::html($zone['zone_name']); ?></a></li>

                            <?php if ( $columnCount == $perColumn ) : ?>

                                </ul>
                                <ul class="left-side">

                                <?php $columnCount = 0; ?>

                            <?php endif; ?>

                            <?php $columnCount++; ?>

                        <?php endforeach; ?>

                    </ul>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php if ( !empty($products) ) : ?>

    <div class="product-grid-wrap append-bottom" data-product-grid-rowlimit=<?= json_encode($perRow); ?>

        <?php if ( isset($search) ) : ?>

            data-product-grid-search-query=<?= json_encode($keywords); ?> data-product-grid-tracking-id =<?= json_encode($trackingId) ;?>
             data-product-grid-limit=<?= json_encode($pageLimit);?> data-product-grid-page-num =<?= json_encode($pageNum); ?>

        <?php endif ?>

    >

        <?php if ( !isset($search) ) : ?>

            <div class="product-filters">
                <div class="sort-filter <?= ($pageType == 'landing' ? 'span-24' : 'span-18'); ?> last<?php ($pageType == 'geotarget' && count($productRow) <= 0 ? " federal-only" : '') ?>">
                    <div class="clearfix">

                        <?php if ( !empty($gridHeader) ) : ?>

                            <h2 class="h4 h4-rev pad-left-15"><?= Encoder::html($gridHeader); ?></h2>

                        <?php endif; ?>

                        <?php if ( $count > $sortMoreThan && $showSort == TRUE ) : ?>

                            <div class="filter-wrap product-grid-sort">
                                <select>
                                    <option value="position" selected>Sort by Relevance</option>
                                    <option value="name-asc"> Sort by Name</option>
                                    <option value="num-asc"> Sort by Item #</option>
                                </select>
                            </div>

                        <?php endif; ?>

                        <?php if ( $showFilter == TRUE && $hasFilters ) : ?>

                            <div class="filter-wrap product-grid-dropdown product-grid-filter">
                                <div class="filter-label lt-blue button narrow product-grid-dropdown-trigger">Filters</div>
                                <fieldset>
                                    <ul class="selectoption">

                                        <?php if ( $tweakable == TRUE ) : ?>

                                            <li><label><input type="checkbox" name="tweakable" value="tweakable" /> Tweakable <span class="filter-count"></span></label></li>

                                        <?php endif; ?>

                                        <?php if ( $multilingual == TRUE ) :?>

                                            <li><label><input type="checkbox" name="multilingual" value="multilingual" /> Multilingual <span class="filter-count"></span></label></li>

                                        <?php endif; ?>

                                        <?php if ( $bilingual == TRUE && $multilingual == FALSE) : ?>

                                            <li><label><input type="checkbox" name="bilingual" value="bilingual" /> Bilingual <span class="filter-count"></span></label></li>

                                        <?php endif; ?>

                                        <?php if ( $bestSeller == TRUE ) : ?>

                                            <li><label><input type="checkbox" name="bestseller" value="bestseller" /> Best Seller <span class="filter-count"></span></label></li>

                                        <?php endif; ?>

                                        <?php if ( $onSale == TRUE ) : ?>

                                            <li><label><input type="checkbox" name="onsale" value="onsale" /> On Sale <span class="filter-count"></span></label></li>

                                        <?php endif; ?>

                                        <?php if ( $luminous == TRUE ) : ?>

                                            <li><label><input type="checkbox" name="glow" value="glow" /> Glow-In-The-Dark <span class="filter-count"></span></label></li>

                                        <?php endif; ?>

                                        <?php //Compliance List Filters ?>

                                        <?php if ( !empty($productComplianceList) ) : ?>

                                            <?php $complianceCount = 0; ?>

                                            <?php foreach ( $productComplianceList as $compliance ) : ?>

                                                <li><label><input type="checkbox" name="type<?= $objSubcategoryPage->convertNumberToWords($complianceCount) ?>" value="type<?= $objSubcategoryPage->convertNumberToWords($complianceCount) ?>" /> <?= $compliance->getName() ?> <span class="filter-count"></span></label></li>

                                            <?php $complianceCount++; endforeach; ?>

                                        <?php endif; ?>

                                    </ul>
                                    <div class="right-side">
                                        <p class="button lt-blue product-grid-showall">Clear Filters</p>
                                        <p class="button blue first-margin-5 product-grid-closedropdown">Done</p>
                                    </div>
                                </fieldset>
                            </div>

                            <div class="feature-filter <?= ( $pageType == 'landing' ? 'span-24' : 'span-18' ); ?> last">
                                <p class="filter-pointer product-grid-count"></p>
                                <ul class="product-grid-filterlist"></ul>
                                <p class="button lt-blue narrow product-grid-clearfilters">Clear Filters</p>
                            </div>

                        <?php endif; ?>

                    </div>
                </div>
            </div>

        <?php endif; ?>

        <?php if ( !empty($gridIntro) ) : ?>

            <p class="first-margin last-margin product-filters"><?= Encoder::html($gridIntro); ?></p>

        <?php endif; ?>

        <?php if ( $type == 'detailed' ) : ?>

            <div class="product-grid <?= $detailedSection['grid_size']; ?> <?= ( $featuresDiv ? ' detailed-products ' : ' ' ); ?> <?= $objSubcategoryPage->convertNumberToWords( $perRow ); ?>-products-across">

                <?php if ( $featuresDiv ) : ?>

                    <div class="detailed-feature">

                <?php endif; ?>

                    <?php if ( !empty($detailedSection['image']) && $perRow < 4 ) : ?>

                        <img src="<?= $objSubcategoryPage->imagePath['description'] . Encoder::html($detailedSection['image']); ?>" alt="<?= Encoder::html($detailedSection['name']); ?>" />

                    <?php endif; ?>

                    <?php if ( $detailsDiv ) : ?>

                        <div>

                    <?php endif; ?>

                        <?php if ( !empty($detailedSection['grid_subhead']) && $perRow < 4 ) : ?>

                            <p class="h5"><?= Encoder::html($detailedSection['grid_subhead']); ?></p>

                        <?php endif; ?>

                        <?php if ( !empty($detailedSection['description']) && $perRow < 4 ) : ?>

                            <p><?= $detailedSection['description']; ?></p>

                        <?php endif; ?>

                        <?php if ( !empty($detailedSection['more_info_text']) && !empty($detailedSection['more_info_href']) && $perRow < 4 ) : ?>

                            <p><a href="<?= Encoder::html($detailedSection['more_info_href']); ?>"><?= Encoder::html($detailedSection['more_info_text']); ?></a></p>

                        <?php endif; ?>

                    <?php if ( $detailsDiv ) : ?>

                        </div>

                    <?php endif; ?>

                <?php if ( $featuresDiv ) : ?>

                    </div>

                <?php endif; ?>

        <?php endif; ?>

        <?php if ( $type == 'detailed' && $featuresDiv ) : ?>

            <div class="product-grid-rows">

        <?php endif; ?>

        <?php if ( $type != 'detailed' ) : ?>

            <div class="product-grid <?= $gridSize; ?>">

        <?php endif; ?>

        <div class="row">

            <?php $count = 0; $index = 0; ?>

            <?php foreach ( (array) $productRow as $key => $value ) : ?>

                <?php if ( $count > ( $perRow - 1 ) ) : ?>

                    <?php $count = 0; ?>

                    </div>
                    <div class="row">

                <?php endif; ?>

                <a href="<?= Encoder::html($value['productURL']); ?>" class="product" title="<?= Encoder::html($value['name']); ?>"
                    data-product-id=<?= json_encode( (int) $value['products_id'] ); ?>

                    <?php if ( !empty($productComplianceList) ) : ?>

                        <?php $complianceCount = 0; ?>

                        <?php foreach ( $productComplianceList as $compliance ) : ?>

                            data-product-type-type<?= $objSubcategoryPage->convertNumberToWords($complianceCount); ?>=<?= ( in_array($products[$index]->getId().'_'.$compliance->getId(), $productComplianceChecklist ) ? 'true' : 'false' ); ?>

                        <?php $complianceCount++; endforeach; ?>

                    <?php endif; ?>

                    data-product-type-bestseller=<?= json_encode( $value['best_seller'] ? 'true' : 'false' ); ?>
                    data-product-type-tweakable=<?= json_encode( $value['is_tweakable'] ? 'true' : 'false' ); ?>
                    data-product-type-multilingual=<?= json_encode( $products[$index]->isMultiLingual() ? 'true' : 'false' ); ?>
                    data-product-type-bilingual=<?= json_encode( $products[$index]->isBiLingual() && !$products[$index]->isMultiLingual() ? 'true' : 'false' ); ?>
                    data-product-type-onsale=<?= json_encode( $products[$index]->getOnSale() ? 'true' : 'false' ); ?>
                    data-product-type-glow=<?= json_encode( ( in_array($products[$index]->getId(), $luminousChecklist) ) ? 'true' : 'false' ); ?>
                >

                    <div class="product-info">

                        <?php if ( $products[$index]->getOnSale() ) : ?>

                            <span class="sale-tag">On Sale</span>

                        <?php endif; ?>

                        <div class="product-preview">
                            <div class="product-image">
                                <img src="<?= Encoder::html($products[$index]->getImagePath('grid') . $value['image']); ?>" alt="<?= Encoder::html($value['name']); ?>"/>
                            </div>

                            <?php if ( $showQuickview ) : ?>

                                <div class="quick-view">
                                    <span class="label button alt-blue square-button"><i class="sprite sprite-zoom-small-white"></i> <span>Quick View</span></span>
                                </div>

                            <?php endif; ?>

                        </div>

                        <?php if ( !empty($value['name']) ) : ?>

                            <div class="product-name">

                                <?php if ( $value['expiration'] ) : ?>

                                    <span class="new-product-inline">New! </span>

                                <?php endif; ?>

                                <span class="product-name-inner"><?= Encoder::html($value['name']); ?></span>
                            </div>

                        <?php endif; ?>

                        <?php if ( !empty($value['title']) ) : ?>

                            <div class="product-mutcd"><?= Encoder::html($value['title']); ?></div>

                        <?php endif; ?>

                        <?php if ( $showProductNumber && !empty($value['display_number']) ) : ?>

                            <div class="product-sku">Item <span class="product-num"><?= Encoder::html($value['display_number']); ?></span></div>

                        <?php endif; ?>

                        <?php if ( !empty($productBanners[$index]) ) : ?>

                            <ul class="product-banners product-flags <?php echo ( count($productBanners[$index]) == 1 && ($productBanners[$index][0][0] == 'Best Seller' || $productBanners[$index][0][0] == 'On Sale') ? 'no-border' : '' ) ?>">
                                <?php for ( $i = 0; $i < count($productBanners[$index]); $i++  ) : ?>

                                    <li class="<?= $productBanners[$index][$i][1]; ?>"><?= $productBanners[$index][$i][0]; ?></li>

                                <?php endfor; ?>

                            </ul>

                        <?php endif; ?>

                    </div>
                </a>

                <?php $count++; $index++; ?>

            <?php endforeach; ?>

        <?php if ( $type != 'detailed' || ( $type == 'detailed' && $featuresDiv ) ) : ?>

            </div>

        <?php endif; ?>

        <div class="product-grid-hider"></div>

        <?php if ( $type == 'detailed' ) : ?>

                    </div>
                </div>
            </div>

        <?php endif; ?>

        <?php if ( $type != 'detailed' ) :?>

                </div>
            </div>

        <?php endif; ?>

<?php endif; ?>

<?php if ( $lastGrid ) : ?>

    </div>

<?php endif; ?>

<?php if ( isset($search) ) : ?>

    <?php if ( $result['total_pages'] > 1 ) : ?>

        <div class='span-8 results-pagination bottom-space'>
            <span>Pages:&nbsp;</span>

            <div class="bold right-side">

                <?php if ( !empty($PreviousPageUrl) ) : ?>

                    <a href='<?= Encoder::html($PreviousPageUrl); ?>' class="search-page-nav-link"><i class="sprite sprite-search-left-blue"></i></a>

                <?php endif; ?>

                <?php if ( !empty($pagesList) ) : ?>

                    <?php foreach ( $pagesList as $page ) : ?>

                        <?php if ( $page != $result['current_page'] ) : ?>

                            <a href='<?= Encoder::html($page[0]); ?>'> <?= $page[1]; ?> </a>

                        <?php else : ?>

                            <?= $page; ?>

                        <?php endif; ?>

                    <?php endforeach; ?>

                <?php endif; ?>

                <?php if ( !empty($NextPageUrl) ) : ?>

                    <a href='<?= Encoder::html($NextPageUrl); ?>' class="search-page-nav-link"><i class="sprite sprite-search-right-blue"></i></a>

                <?php endif; ?>

            </div>
        </div>

    <?php endif; ?>

            </div>
        </div>
    </div>

<?php endif; ?>




<div class="ghost">
<div id="quick-view-container">
 

        <div class="product-header-area">
            <h2 class="product-title-holder push-left-30">Heavy Duty Nylon Ties</h2>
            <div class="product-see-full-page-quickbuy">
                <a href="">See Full Product Page ></a>
            </div>
        </div>
        <div class="product-dual-holder">
            <div class="product-image-details-column">
                <div class="product-image-container"><div class="on-sale-image-overlay ghost"></div><div class="out-of-stock">OUT OF STOCK</div><div class="product-image-zoom-hover" href="/new_images/product-page/accessory-example.png"></div><img itemprop="image" src="/new_images/product-page/accessory-example.png"></div>
                <meta itemprop="description" content="Construction Area Authorized Only Sign" />
                <div class="product-info-list-accessory">
                    <dl class="pdata2">
                        <dt class="pdata2">Item #:</dt>
                        <dd class="pdata2">NT8</dd>
                    </dl>
                </div>
            </div>
            <form class="product-page-form">
            <div class="product-sku-column">
                <div class="product-sku-holder">
                    <!-- <div class="product-sku-out-of-stock"></div> -->
                    <div class="sku-area-container">
                        <span class="sku-title-inline"><span class="product-sku-single-title">Size:</span>8" long</span>
                        <div class="sku-contents-empty">
                        </div>
                    </div>
                    <div class="sku-area-container">
                        <span class="sku-title-inline"><span class="product-sku-single-title">Material:</span>Nylon</span>
                        <div class="sku-contents-empty">
                        </div>
                    </div>

                    <div class="sku-area-container">
                        <span class="sku-title-inline"><span class="product-sku-single-title">Packaging:</span>Package of 100</span>
                        <span itemprop="sku" class="product-sku-number-mini">NT8</span>                     
                        <div class="sku-contents">
                        </div>
                    </div>
                </div>
                <div class="product-message-area">
                    <b>Need a quote for a large order?</b>&nbsp;Call 800-274-6271 or
                    <!-- <div id="cifpFm" style="z-index:100;position:absolute"></div><div id="scfpFm" style="display:inline"></div><div id="sdfpFm" style="display:none"></div><script type="text/javascript">var sefpFm=document.createElement("script");sefpFm.type="text/javascript";var sefpFms=(location.protocol.indexOf("https")==0?"https":"http")+"://image.providesupport.com/js/1hx9685lbev3n13jtak217mktq/safe-textlink.js?ps_h=fpFm&ps_t="+new Date().getTime()+"&online-link-html=chat%20live&offline-link-html=email%20us";setTimeout("sefpFm.src=sefpFms;document.getElementById('sdfpFm').appendChild(sefpFm)",1)</script><noscript><div style="display:inline"><a href="http://www.providesupport.com?messenger=1hx9685lbev3n13jtak217mktq">Online Customer Support</a></div></noscript> -->
                </div>
                <div class="product-under-sku">
                    <div class="product-under-sku-qty-label">Qty:</div>
                    <div class="product-under-sku-quantity">
                    <input type="number" min="1" value="1">
    <!--                                <div class="product-quantity-input"><input value="1" /></div>
                            <div class="product-quantity-arrows">
                                <div></div><div></div>
                            </div>
    -->         </div>
                    <div class="product-under-sku-total">
                        <div class="product-under-sku-total-label">Total Price:&nbsp;<span id="total-price">$20.50</span></div>
                        <div class="product-under-sku-total-lowprice">Guaranteed Low Price<div class="question-mark-gray"></div></div>
                    </div>
                    <div class="product-under-sku-total-cartbtn"><input type="submit" class="green-cart-button" value="+ Add to Cart"></div>
                </div>
                <a name="tab-table"></a>                
                <span class="product-instock">In Stock. Ships Today!</span><span class="product-special-freight">Special freight arrangements are neccessary. <a href="#" id="demo-html">Learn more</a></span>
            </div>
            </form>
            </div>
        





</div></div>