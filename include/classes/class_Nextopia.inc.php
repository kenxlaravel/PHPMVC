<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

class Nextopia {

	public function updateFeed() {

		global $dbh;

        $dbh = Connection::getHandle();

		if ( $dbh instanceof PDO ) {

			// Open the feed file.
			$fh = fopen(APP_ROOT . '/nextopia/feed.txt', 'w');

			if ( $fh ) {

				// Query the database for a list of all active AND searchable product numbers (plus data associated with those product numbers).
				$sql = "select distinct
                            `p`.`id` as `id`
                        ,   `p`.`id` as `products_id`
                        ,   `p`.`id` as `canonical`
                        ,    sku.`validity` as  validity
                        ,   `p`.`product_number` as `product_number`
                        ,   `p`.`default_product_name` as `nickname`
                        ,   `p`.`default_product_name` as `product_nickname`
                        ,   `p`.`default_product_name` as `name`
                        ,   `p`.`default_subtitle` as `title`
                        ,   `p`.`meta_description` as `meta_description`
                        ,   `p`.`meta_keywords` as `meta_keywords`
                        ,   `h`.`name` as `heading`
                        ,   `p`.`url_slug` as `slug`
                        ,   `p`.`by_legend` as `by_legend`
                        ,   `p`.`custom` as `stock_custom`
                        ,   `p`.`search_thumbnail` as `image1_thumbnail`
                        ,   ifnull(item_sold.quantity , 0) as `item_sold`
                        ,   `p`.`meta_keywords` as `keywords`
                        ,   `p`.`default_subtitle` as `subtitle`
                        ,   `p`.`sitemap_show` as `visibility`
                        ,   `p`.`page_priority` as `priority`
                        ,   `cf`.`name` as `change_frequency`
                        ,   `sku`.`name` as `sku_codes`
                        ,   ifnull(`s`.`name` , '') as `subcategory_name`
                        ,   ifnull(`c`.`name` , '') as `category_name`
                        ,   `t`.`template_secure` as `secure`
                        ,   `t`.`template_filename` as `filename`
                        ,   `t`.`allow_target` as `allow_target`
                        ,   `t`.`requires_login` as `requires_login`
                        ,   `t`.`disallow_guests` as `disallow_guests`
                        ,   `u`.`url` as `short_url`
                    from
                            `bs_products` `p`
                            inner join 
                                (
                                    select 
                                            bs_product_skus.product_id 
                                        ,   if (count(bs_product_skus.product_id) > 0, true, false) as validity
                                        ,   bs_skus.active
                                        ,   group_concat(bs_skus.`name` order by bs_skus.`name`) as `name`
                                    from
                                            bs_product_skus 
                                            inner join
                                            bs_skus 
                                            on
                                            bs_product_skus.sku_id = bs_skus.id
                                    where
                                            bs_skus.active = 1
                                    group by
                                            bs_product_skus.product_id
                                        ,   bs_skus.active
                                ) as sku                    
                            on 
                            (`p`.`id` = `sku`.`product_id`)
                            left outer join
                            (
                                select distinct
                                    bs_cart_skus.product_id
                                ,   sum(bs_cart_skus.quantity) as quantity          

                                from
                                    bs_carts
                                    inner join
                                    bs_cart_skus
                                    on
                                    bs_carts.id = bs_cart_skus.cart_id
                                    inner join
                                    bs_orders
                                    on
                                    bs_cart_skus.cart_id = bs_orders.cart_id
                                where
                                    bs_orders.orders_status <> 5
                                group by
                                    bs_cart_skus.product_id
                            )
                            as item_sold
                            on
                            p.id = item_sold.product_id
                            left join
                            bs_subcategories s
                            on
                            p.default_subcategory_id = s.id
                            left outer join
                            bs_groupings g
                            on
                            s.grouping_id = g.id
                            left outer join
                            bs_categories c
                            on      
                            g.category_id = c.id
                            left join `bs_change_frequencies` cf 
                            on 
                            ( p.change_frequency_id = cf.id and cf.active = 1)
                            left join `bs_headers` h 
                            on 
                            (p.header_id = h.`id` and h.active = 1)
                            left join bs_tool_types tt 
                            on (
                            p.default_tool_type_id = tt.id and tt.active = 1)
                            left join `bs_page_urls` `u` 
                            on 
                            (`p`.`canonical_page_url_id` = `u`.`id` and `u`.`pagetype` = 'product' and `p`.`id` = `u`.`pageid`)
                            cross join 
                            (
                                select * from `bs_pagetypes` where `pagetype` = 'product'
                            ) as `t` 
                    where
                            `p`.`active` = 1
                            and 
                            `p`.searchable = 1
                            and 
                            `sku`.`active` = 1
                            and 
                            (
                                p.expiration is null
                                or p.expiration = '0000-00-00'
                                or p.expiration > curdate()
                            )";

				// Write the field names to the first line of the file.
				$stringData = "SKU\tName\tCode\tDescription\tcategory\tsubcategory\tImage\tUrl\tunitsold\tkeywords\tsubtitle\n";

				// Loop through the results from the database.
				foreach ( $dbh->query($sql) AS $row ) {

					$product_page = new Page('product', $row['products_id'], $row);

					$properties = array(

                        $row['product_number'],
						$row['product_nickname'],
						$row['sku_codes'],
						$row['by_legend'],
						$row['category_name'],
						$row['subcategory_name'],
						IMAGE_URL_PREFIX_FULL . '/images/catlog/product/small/' . $row['image1_thumbnail'],
						$product_page->getUrl(),
						$row['item_sold'] > 0 ? (int) $row['item_sold'] : 0,
						$row['keywords'].', '.$row['by_legend'],
						$row['subtitle']
					);

					end($properties);

                    $finalKey = key($properties);

                    foreach ( $properties AS $key => $property ) {

						$stringData .= trim(preg_replace('/\s+/', ' ', $property)) . ($key === $finalKey ? "\n" : "\t");
					}

				}

				// Write the output to the feed file, AND then close it.
				fwrite($fh, utf8_encode($stringData));
				fclose($fh);


				return true;

			} else {

				return false;

			}

		} else {

			return false;

		}

	}
}
