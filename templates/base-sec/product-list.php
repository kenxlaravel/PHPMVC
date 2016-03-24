<?php

/**
 * This file is used for every product grid across multiple page
 * types:
 *
 * 		Subcategory pages
 * 		Detailed subcategory pages
 * 		Geotargeted subcategory pages (both federal and targeted product grids)
 * 		Landing page grids
 *
 * The function is called with the following parameters, but only the first one is mandatory for a
 * regular product grid:
 *
 * @param  array    $product_row    An array of all product IDs to appear in the grid
 * @param  string   $type           On geotargeted pages, this can be 'federal' or 'geotarget', and on detailed pages this is 'detailed'. Otherwise this should be omitted.
 * @param  array    $details        On detailed pages, an array of attributes specific to the detailed section of the grid. Otherwise this should be omitted.
 */

function listProducts( $product_row, $type = NULL, $details = NULL, $search = NULL, $keywords = NULL, $tracking_id = NULL, $page_limit = NULL, $page_num = NULL ) {

    global $page;

    $objSubcategoryPage = SubcategoryPage::create( $page->getId() );
    $objGeotargetPage = GeotargetPage::create( $page->getId() );
    $objLandingPage = LandingPage::create( $page->getId() );

    $grid_size              = $details['grid_size'];
    $show_quickview         = $details['show_quickview'];
    $show_product_number    = $details['show_product_number'];
    $per_row                = (int) $details['per_row'];
    $show_filter            = $details['show_filter'];
    $show_sort              = $details['show_sort'];
    
    $count = 0;

    //We are going to loop through all the products once, and create a list of all the filters we
    //need. We will then render the proper filter bars, and then loop through the products again
    //for the final listing.
    foreach ( (array) $product_row as $key => $value ) {

        $tmpProduct     = ProductPage::create( (int) $value['products_id'] );

        //Set boolean values for possible product flags
        if ( $tmpProduct->isMultiLingual() )        $multilingual = TRUE;   //Whether or not the product is multilingual
        if ( $tmpProduct->isTweakAble() )           $tweakable = TRUE;      //Whether or not the  product is tweakable
        if ( $tmpProduct->getDefaultBestSeller() )  $best_seller = TRUE;    //Whether or not the product is a best seller
        if ( $tmpProduct->getOnSale() )             $on_sale = TRUE;        //Whether or not the product is on on sale

        $count++;

    }

?>
        <div class="product-grid-wrap append-bottom" data-product-grid-rowlimit="<?php echo json_encode( $per_row ); ?>"
<?php 		if ( isset($search) ) : ?>

            data-product-grid-search-query=<?php echo json_encode( $keywords ); ?> data-product-grid-tracking-id =<?php echo json_encode( $tracking_id ) ;?>
            data-product-grid-limit="<?php echo json_encode( $page_limit );?>" data-product-grid-page-num ="<?php echo json_encode( $page_num ); ?>"
<?php
        endif
?>
        >

        <?php if ( !isset($search) ) : ?>
            <div class="product-filters">
                <div class="sort-filter <?php echo (PAGE_TYPE == 'landing' ? 'span-24' : 'span-18'); ?> last<?php if ( PAGE_TYPE == 'geotarget' && count($product_row) <= 0 ) { echo " federal-only"; } ?>">
                    <div class="clearfix">

<?php
                $grid_header = '';

                //Get the grid header, if there is one
                if ( PAGE_TYPE == 'subcategory' ) :

                    if ( $type != 'detailed' ) :

                        $grid_header = $objSubcategoryPage->getGridHeader();

                    elseif ( $type == 'detailed' ) :

                        $grid_header = $details['name'];

                    endif;

                elseif ( $type == 'federal' ) :

                    $grid_header = $objGeotargetPage->getGridHeader();

                elseif ( $type == 'geotarget' ) :

                    $grid_header = SubcategoryPage::create( $objGeotargetPage->getId(), true )->getGeoGridHeader();

                elseif ( PAGE_TYPE == 'landing' ) :

                    $grid_header = $objLandingPage->getProductGridHeader();

                endif;

                //If there is a grid header, display it
                if ( !empty( $grid_header ) ) :
?>
                    <h2 class="h4 h4-rev pad-left-15"><?php echo Encoder::create( $grid_header )->getHtml(); ?></h2>
<?php
                endif;


                //Grab the "sort more than X" value from the [bs_config] table
                $sort_more_than = Settings::getSettingValue( 'productsorting' );

                //If there are enough products on the page, display the sorting controls
                if ( $count > $sort_more_than && $show_sort == TRUE ) :
?>
                    <div class="filter-wrap product-grid-sort">
                            <select>
                                <option value="position" selected>Sort by Relevance</option>
                                <option value="name-asc"> Sort by Name</option>
                                <option value="num-asc"> Sort by Item #</option>
                        </select>
                    </div>

<?php
                endif;

                //If this page enables filters and we have product types OR product features, display the filter bar
                if ( $show_filter == TRUE && ( $tweakable == TRUE || $multilingual == TRUE || $on_sale == TRUE || $best_seller == TRUE ) ) :
?>
                    <div class="filter-wrap product-grid-dropdown product-grid-filter">
                        <div class="filter-label lt-blue button narrow product-grid-dropdown-trigger">Filters</div>
                        <fieldset>
                            <ul class="selectoption">
<?php

                                //If any product is tweakable, show on the dropdown
                                if ( $tweakable == TRUE ) :
?>
                                    <li><label><input type="checkbox" name="tweakable" value="tweakable" /> Tweakable <span class="filter-count"></span></label></li>
<?php
                                endif;

                                //If any product is multilingual, show on the dropdown
                                if ( $multilingual == TRUE ) :
?>
                                    <li><label><input type="checkbox" name="multilingual" value="multilingual" /> Multilingual <span class="filter-count"></span></label></li>
<?php
                                endif;

                                //If any product is on on_sale, show on the dropdown
                                if ( $on_sale == TRUE ) :
?>
                                    <li><label><input type="checkbox" name="onsale" value="onsale" /> On Sale <span class="filter-count"></span></label></li>
<?php
                                endif;

                                //If any product is a best seller, show in the dropdown
                                if ( $best_seller == TRUE ) :
?>
                                    <li><label><input type="checkbox" name="bestseller" value="bestseller" /> Best Seller <span class="filter-count"></span></label></li>
<?php
                                endif;

?>
                            </ul>
                            <div class="right-side">
                             <p class="button lt-blue product-grid-showall">Clear Filters</p> <p class="button blue first-margin-5 product-grid-closedropdown">Done</p>
                             </div>
                        </fieldset>
                    </div>

                    <div class="feature-filter <?php echo ( PAGE_TYPE == 'landing' ? 'span-24' : 'span-18' ); ?> last">
                        <p class="filter-pointer product-grid-count"></p>
                        <ul class="product-grid-filterlist">
                        </ul>
                        <p class="button lt-blue narrow product-grid-clearfilters">Clear Filters</p>
                    </div>

<?php
                endif;
?>

                </div>
            </div>
        </div>

<?php
  endif
?>

<?php

    $grid_into = '';

    //Get the grid intro if there is one
    if ( ( ( PAGE_TYPE == 'geotarget' && $type == 'federal' ) ) ) :

        $grid_into = $objGeotargetPage->getGridIntro();

    elseif ( PAGE_TYPE == 'geotarget' && $type != 'federal' ) :

        $grid_into = SubcategoryPage::create( $objGeotargetPage->getId(), true )->getGeoGridIntro();

    elseif ( PAGE_TYPE == 'subcategory' ) :

        $grid_into = $objSubcategoryPage->getGridIntro();

    elseif ( PAGE_TYPE == 'landing'  && !is_null( $page->getProductGridIntro() ) ) :

        $grid_into = $page->getProductGridIntro();

    endif;

    //If there is a grid intro, display it
    if ( !empty( $grid_into ) ) :
?>
        <p class="first-margin last-margin product-filters"><?php echo Encoder::create( $grid_into )->getHtml(); ?></p>
<?php
    endif;

    if ( $type == 'detailed' ) :

        //Whether or not to display the details div
        $details_div = ( (!empty( $details['grid_subhead'] ) || !empty( $details['description'] ) || ( !empty( $details['more_info_text'] ) && !empty( $details['more_info_href'] ) ) ) && $per_row < 4 ? TRUE : FALSE);

        //Whether or not to display the features div
        $feature_div = ( $details_div || !empty( $details['image'] ) ? TRUE : FALSE );

?>
            <div class="product-grid <?php echo $details['grid_size']; echo ( $feature_div ? ' detailed-products ' : ' ' ); echo $objSubcategoryPage->convertNumberToWords( $per_row ); ?>-products-across">

<?php

                echo ( $feature_div ? '<div class="detailed-feature">' : '' );

                if ( !empty( $details['image'] ) && $per_row < 4 ) :
?>
                    <img src="<?php echo $objSubcategoryPage->imagePath['description'] . Encoder::create( $details['image'] )->getHtml(); ?>" alt="<?php echo Encoder::create( $details['name'] )->getHtml(); ?>" />
<?php
                endif;

                echo ( $details_div ? '<div>' : '' );

                if ( !empty( $details['grid_subhead'] ) && $per_row < 4 ) :
?>
                    <p class="h5"><?php echo Encoder::create( $details['grid_subhead'] )->getHtml(); ?></p>
<?php
                endif;

                if ( !empty( $details['description'] ) && $per_row < 4 ) :
?>
                    <p><?php echo $details['description']; ?></p>
<?php
                endif;

                if ( !empty( $details['more_info_text'] ) && !empty( $details['more_info_href'] ) && $per_row < 4 ) :
?>
                    <p><a href="<?php echo Encoder::create( $details['more_info_href'] )->getHtml(); ?>"><?php echo Encoder::create( $details['more_info_text'] )->getHtml(); ?></a></p>
<?php
                endif;

                echo ( $details_div ? '</div>' : '' );
                echo ( $feature_div ? '</div>' : '' );
    endif;



    if ( $type == 'detailed' && $feature_div ) :
?>
        <div class="product-grid-rows">
<?php
    elseif ( $type != 'detailed' ) :
?>
        <div class="product-grid <?php echo $grid_size; ?>">
<?php
    endif;
?>
        <div class="row">
<?php
            //Keeps track of how many products we have displayed, so we can limit each row
            $count = 0;

            foreach ( (array) $product_row as $key => $value ) :

                //Instantiate a new Product
                $tmpProduct = ProductPage::create( (int) $value['products_id'] );

                //Get some product info
                $productno = $value['display_number'];
                $productname = $value['name'] ;
                $productmutcd = $value['title'];

                //If we need to start a new row
                if ( $count > ( $per_row - 1 ) ) :

                    $count = 0;

                ?>

                    </div>
                    <div class="row">

                <?php endif; ?>

                <a href="<?php echo Encoder::create( $tmpProduct->getUrl() )->getHtml(); ?>"
                   class="product"
                   title="<?php echo Encoder::create( $productname )->getHtml(); ?>"
                   data-product-id=<?php echo json_encode( (int) $value['products_id'] ); ?>
                   data-product-type-bestseller=<?php echo json_encode( $tmpProduct->getDefaultBestSeller() ? 'true' : 'false' ); ?>
                   data-product-type-newitem=<?php echo json_encode( 'false' ); ?>
                   data-product-type-tweakable=<?php echo json_encode( $tmpProduct->isTweakAble() ? 'true' : 'false' ); ?>
                   data-product-type-multilingual=<?php echo json_encode( $tmpProduct->isMultiLingual() ? 'true' : 'false' ); ?>
                   data-product-type-glow=<?php echo json_encode( 'false' ); ?>
                   data-product-type-bilingual=<?php echo json_encode( $tmpProduct->isBiLingual() ? 'true' : 'false' ); ?>
                   data-product-type-onsale=<?php echo json_encode( $tmpProduct->getOnSale() ? 'true' : 'false' ); ?>
                   data-product-type-ulrecognized=<?php echo json_encode( 'false' ); ?>
                >

                    <div class="product-info">
                        <? echo ( $value['on_sale'] ? '<span class="sale-tag">On Sale</span>' : '' ); ?>
                        <div class="product-preview">
                            <div class="product-image">
                                <img src="<?php echo Encoder::create( $tmpProduct->getImagePath('grid') . $value['image'] )->getHtml(); ?>" alt="<?php echo Encoder::create( $productname )->getHtml(); ?>"/>
                            </div>
                            <?php if ( $show_quickview ) : ?>
                                <div class="quick-view">
                                    <span class="label button alt-blue square-button"><i class="sprite sprite-zoom-small-white"></i> <span>Quick View</span></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ( !empty( $productname ) ) : ?>
                                <div class="product-name">
                                    <?php if ( $value['expiration'] ) : ?>
                                        <span class="new-product-inline">New! </span>
                                    <?php endif; ?>
                                    <span class="product-name-inner"><?php echo Encoder::create( $productname)->getHtml(); ?></span>
                                </div>
                        <?php endif; ?>

                        <?php if ( !empty( $productmutcd ) ) : ?>
                            <div
                                class="product-mutcd"><?php echo Encoder::create( $productmutcd )->getHtml(); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( $show_product_number && !empty( $productno ) ) : ?>
                            <div class="product-sku">Item <span
                                class="product-num"><?php echo Encoder::create( $productno )->getHtml(); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php

                            //Check if this product has any product banners to show
                            if
                            (
                                $tmpProduct->isTweakAble() ||
                                $tmpProduct->isMultiLingual() ||
                                $tmpProduct->getOnSale()
                            ) :

                        ?>

                            <ul class="product-banners product-flags">

                        <?php
                                //Set up our product-banners array
                                $product_banners = array(
                                    array( 'Tweakable',     'tweakable',        $tmpProduct->isTweakAble() ),
                                    array( 'Multilingual',  'multilingual',     $tmpProduct->isMultiLingual() ),
                                    array( 'On Sale',       'on-sale',          $tmpProduct->getOnSale() )
                                );

                                for ( $i = 0; $i <= count ( $product_banners ); $i++  ) :

                                    if ( $product_banners[$i][2] ) :

                        ?>

                                        <li class="<?php echo $product_banners[$i][1]; ?>"><?php echo $product_banners[$i][0]; ?></li>

                        <?php

                                    endif;
                                endfor;

                        ?>
                            </ul>
                        <?php endif; ?>

                    </div>
                </a>
        <?php

                $count++;

            endforeach;

        if ( $type != 'detailed' || ( $type == 'detailed' && $feature_div ) ) :
?>
        </div>
<?php
        endif;
?>
        <div class="product-grid-hider"></div>
<?php

        if ( $type == 'detailed' ) :
?>
        </div>
    </div>
    </div>
<?php
        endif;

        if ( $type != 'detailed' ) :
?>
    </div>

</div>

<?php
        endif;


} //End of function
