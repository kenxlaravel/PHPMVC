<?php

class GoogleProducts {

	const GOOGLE_XML_NAMESPACE = 'http://base.google.com/ns/1.0';
	const CURRENCY_SUFFIX = ' USD';
	const WEIGHT_SUFFIX = ' lb';

	public function updateFeed() {

		// Instantiate the XML object.
		$XML = new DOMDocument('1.0', 'utf-8');

		// Create the root rss element.
		$rss = $XML->appendChild($XML->createElement('rss'));
		$rss->setAttribute('version', '2.0');
		$rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:g', self::GOOGLE_XML_NAMESPACE);

		// Create the channel element.
		$channel = $rss->appendChild($XML->createElement('channel'));

		// Add the meta title.
		$meta_title = $XML->createElement('title');
		$meta_title->appendChild($XML->createTextNode('SafetySign.com Data Feed'));
		$channel->appendChild($meta_title);

		// Add the meta description.
		$meta_description = $XML->createElement('description');
		$meta_description->appendChild($XML->createTextNode('SafetySign.com Complete Products'));
		$channel->appendChild($meta_description);

		// Add the meta link.
		$meta_link = $XML->createElement('link');
		$meta_link->appendChild($XML->createTextNode(URL_PREFIX_HTTP . '/'));
		$channel->appendChild($meta_link);

		// For each canonical product (except Flash products since Google Shopping doesn't like their product pages and will suspend our Merchant Center account if they are included)...
		$query = 'SELECT
					p.id AS id,
					s.`name` AS sku_code,
					p.product_number AS product_number,

				IF (
					canonicals.min_price = pt.price,
					"Lowest Price",
					"Not Lowest Price"
				) AS lowest_price_in_product,

				IF (
					p.default_best_seller = 1,
					"Best Seller",
					"Not Best Seller"
				) AS best_seller,
				 p.default_product_name AS google_title,

				IF (
					COUNT(p.id) > 0,
					TRUE,
					FALSE
				) AS validity,
				 p.default_product_name AS `name`,
				 u.url AS short_url,
				 FALSE AS secure,
				 p.url_slug AS slug,
				 p.id AS canonical,
				 p.page_title AS title,
				 p.meta_description AS meta_description,
				 p.meta_keywords AS meta_keywords,
				 p.page_subtitle AS heading,
				 CASE
				WHEN p.sitemap_show = 1 THEN
					"Y"
				ELSE
					"N"
				END AS visibility,
				 p.page_priority AS priority,
				 cf.`name` AS change_frequency,
				 pt.price AS price,
				 p.by_legend AS description,
				 "SafetySign.com" AS manufacturer,
				 p.search_thumbnail AS image_link,
				 "new" AS `condition`,

				IF (
					s.inventory > 0
					OR s.limited_inventory = 0,
					"in stock",
					"out of stock"
				) AS availability,
				 s.`weight` AS `weight`,
				 m.`name` AS material,
				 sz.`name` AS size,
				 s.id AS sku_id,
				 ss.`name` AS subcategory,
				 gg.`name` AS grouping,
				 cc.`name` AS category,
				 bs_advertising_categories.`name` AS google_category
				from
						( 	select distinct
									min(`p`.`id`) as `canonical_id`
								, 	min(`pt`.`price`) `min_price` 
							from
									`bs_products` `p`
									inner join
									`bs_product_skus` ps 
									on
									p.id = ps.product_id 
				                    inner join
									`bs_skus` `s` 
									on
									`ps`.`sku_id` = `s`.`id` 
									and
									`s`.`active` = 1
				                    and
										(
											`s`.`inventory` > 0 
											or
											`s`.`limited_inventory` = FALSE
										)
									left outer join
									`bs_pricing` `pr` 
									on
									`s`.`pricing_id` = `pr`.`id`
				                    left outer join
				                        (
				                            select bs_pricing_tiers.*
				                            from
				                                    bs_pricing_tiers
				                                    inner join
				                                    ( select min(minimum_quantity) as minimum_quantity, pricing_id from bs_pricing_tiers group by pricing_id )
				                                    as bs_pricing_tiers_minimum_quantity
				                                    on
				                                    bs_pricing_tiers.pricing_id = bs_pricing_tiers_minimum_quantity.pricing_id
				                                    and
				                                    bs_pricing_tiers.minimum_quantity = bs_pricing_tiers_minimum_quantity.minimum_quantity
				                        ) as `pt` 
									on 
									( `pr`.`id` = `pt`.`pricing_id` ) 
				                where
									`p`.`active` = 1 
									and 
									`p`.`advertise` = 1 
									and 
										( 
											`p`.`expiration` is null
											or
											`p`.`expiration` = "0000-00-00" 
											or
											`p`.`expiration` > CURDATE() 
										)
									and
									(
										`s`.inventory > 0 
										or 
										`s`.limited_inventory = 0 
									)
							group by 
									`p`.`id`
							) as `canonicals`
				            inner join
							`bs_products` `p` 
							on
							(`canonicals`.`canonical_id` = `p`.`id`)
							inner join
							`bs_product_skus` ps 
							on
							(p.id = ps.product_id)
				            inner join
							`bs_skus` `s` 
							on
							`p`.`default_preconfigured_sku_id` = `s`.`id` 
							and 
							`s`.`active` = 1
							left outer join
							`bs_headers` h 
							on 
							p.header_id = h.id 
							and
							h.active = 1
							left outer join `bs_pricing` `pr` on (`s`.`pricing_id` = `pr`.`id`)
							left outer join`bs_pricing_tiers` `pt` on (`pr`.`id` = `pt`.`pricing_id`)
							left outer join `bs_sizes` `sz` on (`s`.size_id = `sz`.id  and sz.active = 1)
							left outer join bs_materials `m` on (s.material_id = m.id  and m.active = 1)
							left outer join bs_material_groups `mg` on (m.material_group_id = mg.id and mg.active = 1)
							left outer join `bs_change_frequencies` `cf` on (p.change_frequency_id = cf.id )
							left outer join `bs_subcategories` `ss` on (`p`.`default_subcategory_id` = `ss`.`id` and `ss`.`active` = 1 )
							left outer join `bs_groupings` `gg` on ( `ss`.`grouping_id` = `gg`.`id` and `gg`.`active` = 1 )
							left outer join `bs_categories` `cc` on ( `gg`.`category_id` = `cc`.`id` and `cc`.`active` = 1 )
							left outer join `bs_page_urls` `u` on ( `p`.`canonical_page_url_id` = `u`.`id` and `u`.`pagetype` = "product" and `p`.`id`= `u`.`pageid`) 
							left outer join bs_advertising_categories on s.advertising_category_id = bs_advertising_categories.id

				group by
							`p`.`id`
				order by 
							`p`.`product_number` asc';

        $dbh = Connection::getHandle()->prepare($query);

        $dbh->execute();

		// Loop through each product and add to the XML feed
		while ($product = $dbh->fetch(PDO::FETCH_ASSOC) ) {

			// Add in some static property values so page class instantiation goes smoothly
			$product['type'] = 'product';
			$product['filename'] = NULL;
			$product['allow_target'] = 1;
			$product['requires_login'] = 0;
			$product['disallow_guests'] = 0;

		    // Create an item element.
		    $item = $channel->appendChild($XML->createElement('item'));

		    // Instantiate the page object.
		    $product_page = new Page('product', isset($product['id']) ? $product['id'] : NULL, $product);

		    // Add the product ID.
		    $id = $XML->createElement('g:id');
		    $id->appendChild($XML->createTextNode('ss' . $product['sku_id']));
		    $item->appendChild($id);

		    // Add the product variant group.
		    $product_number = $XML->createElement('g:item_group_id');
		    $product_number->appendChild($XML->createTextNode($product['product_number']));
		    $item->appendChild($product_number);

		    // Add the product title.
		    $title = $XML->createElement('title');
		    $title->appendChild($XML->createTextNode(isset($product['google_title']) ? $product['google_title'] : NULL));
		    $item->appendChild($title);

		    // Add the product link.
		    $link = $XML->createElement('link');
		    $link->appendChild($XML->createTextNode($product_page->getUrl()));
		    $item->appendChild($link);

		    // Add the product price.
		    $price = $XML->createElement('g:price');
		    $price->appendChild($XML->createTextNode(number_format($product['price'], 2) . self::CURRENCY_SUFFIX));
		    $item->appendChild($price);

		    // Add the product description.
		    $description = $XML->createElement('description');
		    $description->appendChild($XML->createTextNode($product['description']));
		    $item->appendChild($description);

		    // Add the product manufacturer.
		    $manufacturer = $XML->createElement('g:manufacturer');
		    $manufacturer->appendChild($XML->createTextNode($product['manufacturer']));
		    $item->appendChild($manufacturer);

		    // Add the product image link (if available).
		    if ( !empty($product['image_link']) ) {
		        $image_link = $XML->createElement('g:image_link');
		        $image_link->appendChild($XML->createTextNode(IMAGE_URL_PREFIX_FULL . '/images/catlog/product/small/' . $product['image_link']));
		        $item->appendChild($image_link);
		    }

		    // Add the product condition.
		    $condition = $XML->createElement('g:condition');
		    $condition->appendChild($XML->createTextNode($product['condition']));
		    $item->appendChild($condition);

		    // Add the product availability.
		    $availability = $XML->createElement('g:availability');
		    $availability->appendChild($XML->createTextNode($product['availability']));
		    $item->appendChild($availability);

		    // Tell Google that this product has no UPC.
		    $identifier = $XML->createElement('g:identifier_exists');
		    $identifier->appendChild($XML->createTextNode('FALSE'));
		    $item->appendChild($identifier);

		    // Tell Google that this product has no UPC.
		    $brand = $XML->createElement('g:brand');
		    $brand->appendChild($XML->createTextNode('SafetySign.com'));
		    $item->appendChild($brand);

		    // Add the product weight.
		    $weight = $XML->createElement('g:shipping_weight');
		    $weight->appendChild($XML->createTextNode($product['weight'] . self::WEIGHT_SUFFIX));
		    $item->appendChild($weight);

		     // Add XML custom_label_0 element to hold the product number.
		    $product_number_label = $XML->createElement('g:custom_label_0');
		    $product_number_label->appendChild($XML->createTextNode($product['product_number']));
		    $item->appendChild($product_number_label);

		    // Add Lowest Price or Not Lowest Price per sku
		    $product_min_price = $XML->createElement('g:custom_label_1');
		    $product_min_price->appendChild($XML->createTextNode($product['lowest_price_in_product']));
		    $item->appendChild($product_min_price);


		    // Add indication of best seller sku per product
		    $best_seller = $XML->createElement('g:custom_label_2');
		    $best_seller->appendChild($XML->createTextNode($product['best_seller']));
		    $item->appendChild($best_seller);


			// Add the product size.
		    $size = $XML->createElement('g:size');
		    $size->appendChild($XML->createTextNode($product['size']));
		    $item->appendChild($size);

		    // Add the product material.
		    $material = $XML->createElement('g:material');
		    $material->appendChild($XML->createTextNode($product['material']));
		    $item->appendChild($material);

		    // Add the product type (our categorization).
		    $material = $XML->createElement('g:product_type');
		    $material->appendChild($XML->createTextNode(
                isset($product['category']) ? $product['category'] : NULL . ' > ' . $product['grouping'] . ' > ' . $product['subcategory']));
		    $item->appendChild($material);

		    // Add the product category (Google's categorization).
		    $material = $XML->createElement('g:google_product_category');
		    $material->appendChild($XML->createTextNode(isset($product['google_category']) ? $product['google_category'] : NULL));
		    $item->appendChild($material);

		}


		if ($XML->save(APP_ROOT . '/google/products/feed.xml') !== FALSE) {

			return true;

		} else {

			return false;

		}
	}
}