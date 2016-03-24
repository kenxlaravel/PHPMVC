<div id="column-2" class="span-18 last">
<?php
	/**
	 * product_list.php is included on the subcategory page and lists products. It also allows
	 * products to be sorted and filtered
	 *
	 * @author  Daniel Hennion <daniel@brimar.com>
	 * @since   10.03.2012
	 */

	require_once($Path_Templates_Base."product-list.php");

	//Geotarget template
	if (PAGE_TYPE == 'subcategory' && $objSubcategoryPage->getTemplate() == 'geotarget') {

        include_once($Path_Templates_Base."geotarget_state_list.php");

		listZones('full');
	}

	if (PAGE_TYPE == 'geotarget' && count($row) <= 0 && count($row2) <= 0) {

		include_once($Path_Templates_Base."geotarget_state_list.php");

        listZones('full-green');
	}

	//Check if there are detailed sections
	if ($objSubcategoryPage->getSubcategoryDetail()) {

		$detailed = $objSubcategoryPage->getListings('detailed');
		$products = $objSubcategoryPage->getListings('detailed_products', PAGE_ID);

		//Loop through each detailed section
		foreach($detailed AS $key => $detailed_section) {

			//Clear out any products from previous grids
			$section_products = NULL;

			//Loop through products and only grab the ones for that detailed section
			foreach($products AS $product) {

				//Check to see if this product goes in this section
				if ($product['subcategory_detailed_id']) {
					$section_products[] = $product;
				}
			}

			// Render all the products to the page
			listProducts($section_products, 'detailed', $detailed_section);

		}
	}

	//Check if there are products
	if (count($row) > 0) {

		if (PAGE_TYPE == 'geotarget') {

            $detail['grid_size'] = $objGeotargetPage->getGridSize();
            $detail['show_quickview'] = $objGeotargetPage->getShowQuickview();
            $detail['show_product_number'] = $objGeotargetPage->getShowProductNumber();
            $detail['per_row'] = (int) 5;
            $detail['show_filter'] = $objGeotargetPage->getShowFilter();
            $detail['show_sort'] = $objGeotargetPage->getShowSort();

			// Render all the products to the page
			listProducts($row,'',$detail);

		} else if (PAGE_TYPE == 'subcategory') {

			$detail['grid_size'] = $objSubcategoryPage->getGridSize();
			$detail['show_quickview'] = $objSubcategoryPage->getShowQuickview();
			$detail['show_product_number'] = $objSubcategoryPage->getShowProductNumber();
			$detail['per_row'] = (int) 5;
			$detail['show_filter'] = $objSubcategoryPage->getShowFilter();
			$detail['show_sort'] = $objSubcategoryPage->getShowSort();

			// Render all the products to the page
			listProducts($row, '', $detail);

		} else {
			// Render all the products to the page
			listProducts($row);

		}

	}



	//If this is a geotargeted page, call our function again for the federal signs
	if (count($row2) > 0 && $objSubcategoryPage->federal_enabled == TRUE) {

		if (PAGE_TYPE == 'geotarget') {

			$detail['grid_size'] = $objSubcategoryPage->grid_size;
			$detail['show_quickview'] = $objSubcategoryPage->show_quickview;
			$detail['show_product_number'] = $objSubcategoryPage->show_product_number;
			$detail['per_row'] = (int) 5;
			$detail['show_filter'] = $objSubcategoryPage->show_filter;
			$detail['show_sort'] = $objSubcategoryPage->show_sort;

			// Render all the products to the page
			listProducts($row2, 'federal', $detail);

		}

	}
?>

</div>