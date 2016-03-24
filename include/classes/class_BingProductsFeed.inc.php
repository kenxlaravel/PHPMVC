<?php

class BingProductsFeed {

    public function updateFeed() {

        // Creating the Bing products ads file
        $bingFile = APP_ROOT."/bing/products/feed.txt";
        $txtfile = fopen($bingFile, 'w') or die("can't create the file");

        // Create Headers and write to the file
        $headers = "MPID\tTitle\tBrand\tProductURL\tPrice\tDescription\tImageURL\tMerchantSKU\tShippingWeight\tCondition\tBingCategory\tMerchantCategory\tStockStatus\n";

        fwrite($txtfile, $headers);

        // For each canonical product (except Flash products)...
        $query = 'select distinct
                        `p`.`id` as `id`
                    ,   `s`.`name` as `sku_code`
                    ,   `p`.`product_number` as `product_number`
                    ,   `canonicals`.`min_price` as `lowest_price_in_product`
                    ,   if(`p`.default_best_seller = 1, "Best Seller", "Not Best Seller") as `best_seller`
                    ,   `p`.`default_product_name` as `bing_title`
                    ,   if(COUNT(`p`.`id`) > 0, TRUE, FALSE) as `validity`
                    ,   `p`.`default_product_name` as `name`
                    ,   `u`.`url` as `short_url`
                    ,   FALSE as `secure`
                    ,   `p`.url_slug as `slug`
                    ,   `p`.`id` as `canonical`
                    ,   `p`.default_subtitle as `title`
                    ,   `p`.`meta_description` as `meta_description`
                    ,   `p`.`meta_keywords` as `meta_keywords`
                    ,   `h`.`name` as `heading`
                    ,   `p`.`sitemap_show` as `visibility`
                    ,   `p`.`page_priority` as `priority`
                    ,   `cf`.`name` as `change_frequency`
                    ,   `pt`.`price` as `price`
                    ,   `p`.`by_legend` as `description`
                    ,   "SafetySign.com" as `manufacturer`
                    ,   `p`.`search_thumbnail` as `image_link`
                    ,   "new" as `condition`
                    ,   IF(`s`.`inventory` > 0 OR `s`.`limited_inventory` = 0, "in stock", "out of stock") as `availability`
                    ,   `s`.weight as `weight`
                    ,   `mg`.`description` as `material`
                    ,   `sz`.`name` as `size`
                    ,   `s`.`id` as `sku_id`
                    ,   `ss`.`name` as `subcategory`
                    ,   `gg`.`name` as `grouping`
                    ,   `cc`.`name` as `category`
                from
                        (   select distinct
                                    min(`p`.`id`) as `canonical_id`
                                ,   min(`pt`.`price`) `min_price` 
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
                group by
                            `p`.`product_number`
                order by 
                            `p`.`product_number` asc';


        // Loop through each product and add to the XML feed
        foreach (Connection::getHandle()->query($query) as $product) {
            // Instantiate the page object.
            $product_page = new Page('product', $product['id'], $product);
            // Create all the necessary data
            // MPID
            $data = 'ss'.$product['id']."\t";
            // Title
            $data .= $product['bing_title']." (".$product['product_number'].")\t";
            // Brand
            $data .= $product['manufacturer']."\t";
            // ProductURL
            $data .= $product_page->getUrl()."\t";
            // Price
            $data .= number_format($product['lowest_price_in_product'], 2)."\t";
            // Description
            $data .= $product['title']."\t";
            // ImageURL
            $data .= IMAGE_URL_PREFIX_FULL.'/images/catlog/product/large/'.$product['image_link']."\t";
            // MerchantSKU
            $data .= preg_replace('/[^a-zA-Z0-9]/', '', $product['sku_code'])."\t";
            // ShippingWeight
            $data .= $product['weight']."\t";
            // Condition
            $data .= $product['condition']."\t";
            // BingCategory
            $data .= 'Tools & Hardware|Hardware|Signage'."\t";
            // MerchantCategory
            $data .= preg_replace('/[^a-zA-Z0-9]+/', ' ', $product['category']).' > '.preg_replace(
                    '/[^a-zA-Z0-9]+/', ' ', $product['grouping']
                )."\t";
            // StockStatus
            $data .= $product['availability']."\n";

            fwrite($txtfile, $data);

        }

        fclose($txtfile);
    }
}