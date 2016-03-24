<?php


class ProductGrid
{

    /**
     * ID of the current page
     *
     * @var int $id
     */
    private $page_id;

    /**
     * The product listing data
     *
     * @var array $data
     */
    private $data = array();

    public function __construct( $page_id, $refid = NULL, $target = NULL, $federal = FALSE, $detailed = FALSE, $detailed_products = FALSE )
    {

        $this->setPageId($page_id);

        $data = array();

        if ( !is_null( $this->getPageId() ) ) {

            //This LEFT JOIN groups products on a SKU level to ensure that we get the correct results.
            //It is used in every product grid's SQL query hence why it is used dynamically instead of
            //repeating it several times.
            $sql_join_skus = ' LEFT JOIN
                                (
                                SELECT DISTINCT
                                    products.id AS product_id,
                                    IF (COUNT(products.id) > 0, TRUE, FALSE) AS validity,
                                    ifnull(sum(ifnull(sku.inventory, 0)), 0) AS inventory,
                                     min(sku.limited_inventory) AS limited_inventory,
                                    (SUM(IF (
                                    `sku`.`limited_inventory` = TRUE
                                    AND `sku`.`inventory` > 0,
                                    1,
                                    0
                                    )
                                    ) > 0
                                    ) AS `is_limited`,
                                    (SUM(
                                    IF (
                                        (
                                            `sku`.`inventory` > 0
                                            OR `sku`.`limited_inventory` = FALSE
                                        ),
                                        1,
                                        0
                                        )
                                        ) > 0
                                    ) AS `is_stocked`
                                    FROM
                                    bs_products AS products
                                    INNER JOIN
                                    bs_product_skus AS product_skus
                                    ON
                                    products.id = product_skus.product_id
                                    INNER JOIN
                                    bs_skus AS sku
                                    ON
                                    product_skus.sku_id = sku.id
                                    GROUP BY
                                    products.id
                                    HAVING
                                    SUM(IF((sku.inventory > 0 OR sku.limited_inventory = FALSE), 1, 0)) > 0
                                ) AS sku
                                ON c.id = sku.product_id ';

            //If this is not a geotargeted page OR this is a federal signs request
            if ( PAGE_TYPE != 'geotarget' || $federal == true ) {

                //If this is a subcategory page OR this is a federal signs request
                if ( PAGE_TYPE == 'subcategory' || $federal == true ) {

                    //Get products from this subcategory that are NOT geotargeted
                    $sql_where = ' AND sub.id = :id AND sp.geotarget_id IS NULL ';

                    //If we are NOT requesting detailed products
                    if ( $detailed_products == false ) {

                        //Gets only products that are NOT detailed
                        $sql_where .= ' AND sp.subcategory_detail_id IS NULL ';

                    }

                    //If this is not a detailed signs request
                    if ( $detailed == false ) {

                        $sql = "SELECT DISTINCT
                                    c.id AS products_id,
                                    c.display_number AS display_number,
                                    sp.image AS image,
                                    sp.id AS product_subcategory_id,
                                    coalesce(sp.product_name, c.default_product_name) AS name,
                                    coalesce(sp.product_name, c.default_product_name) AS nickname,
                                    coalesce(sp.subtitle, c.default_subtitle) AS title,
                                    sp.preconfigured_sku_id,
                                    sp.best_seller,
                                    c.on_sale AS on_sale,
                                    c.custom AS is_custom,
                                    sub.id AS subcategory_id,
                                    CASE
                                      WHEN c.new_until > CURDATE() THEN c.new_until
                                      ELSE '0'
                                    END AS expiration,
                                    CASE
                                      WHEN c.builder_tweak_tool_id > 0 AND tt.name IS NOT NULL THEN '1'
                                      ELSE '0'
                                    END AS is_tweakable,
                                    sp.subcategory_detail_id AS subcategory_detailed_id,
                                    sp.translation_family_id AS translation_family_id,
                                    bb.id AS builder_id,
                                    sp.flash_tool_id AS flash_tool_id,
                                    sp.streetsign_tool_id AS streetsign_tool_id,
                                    sp.tool_type_id AS tool_type_id,
                                    tt.name AS tool_type_name

                                FROM bs_products c

                                $sql_join_skus
                                LEFT JOIN bs_subcategory_products sp ON (sp.product_id = c.id)
                                LEFT JOIN bs_subcategories sub ON (sub.id = sp.subcategory_id AND sub.active = TRUE)
                                LEFT JOIN bs_subcategories_detailed sd ON (sd.subcategory_id = sub.id AND sd.active = TRUE)
                                LEFT JOIN bs_groupings grp ON (grp.id = sub.grouping_id AND grp.active = TRUE)
                                LEFT JOIN bs_categories cat ON (cat.id = grp.category_id AND cat.active = TRUE)
                                LEFT JOIN bs_tool_types tt ON (sp.tool_type_id = tt.id)
                                LEFT JOIN bs_builders bb ON (bb.builder_ref = sp.builder_ref)

                                WHERE c.active = 1
                                AND (
                                  c.expiration IS NULL
                                  OR c.expiration = '0000-00-00'
                                  OR c.expiration > CURDATE()
                                )
                                $sql_where

                                ORDER BY sp.position, c.product_number ASC ";

                    } else {

                        $sql = 'SELECT id AS products_id,
                                       name,
                                       image,
                                       grid_subhead,
                                       description,
                                       more_info_text,
                                       more_info_href,
                                       grid_per_row AS per_row,
                                       grid_size,
                                       show_product_number,
                                       show_quickview,
                                       show_filter,
                                       show_sort
                                FROM   bs_subcategories_detailed
                                WHERE  subcategory_id = :id
                                       AND active = true
                                ORDER  BY position ';

                    }

                } else if ( PAGE_TYPE == 'landing' ) {

                    $sql = "SELECT DISTINCT
                                c.id AS products_id,
                                c.display_number AS display_number,
                                c.custom AS is_custom,
                                lp.image AS image,
                                lp.id AS product_landing_id,
                                coalesce(lp.product_name, c.default_product_name) AS name,
                                coalesce(lp.product_name, c.default_product_name) AS nickname,
                                coalesce(lp.subtitle, c.default_subtitle) AS title,
                                lp.preconfigured_sku_id,
                                lp.landing_id AS landing_id,
                                lp.best_seller,
                                c.on_sale AS on_sale,
                                CASE
                                  WHEN c.new_until > CURDATE() THEN c.new_until
                                  ELSE '0'
                                END AS expiration,
                                CASE
                                  WHEN c.builder_tweak_tool_id > 0 AND tt.name IS NOT NULL THEN '1'
                                  ELSE '0'
                                END AS is_tweakable,
                                lp.translation_family_id AS translation_family_id,
                                bb.id AS builder_id,
                                lp.flash_tool_id AS flash_tool_id,
                                lp.streetsign_tool_id AS streetsign_tool_id,
                                lp.tool_type_id AS tool_type_id,
                                tt.name AS tool_type_name

                            FROM bs_products c

                            $sql_join_skus
                            LEFT JOIN bs_landing_products lp ON (lp.product_id = c.id)
                            LEFT JOIN bs_tool_types tt ON (lp.tool_type_id = tt.id)
                            LEFT JOIN bs_builders bb ON (bb.builder_ref = lp.builder_ref)

                            WHERE c.active = 1
                            AND (
                              c.expiration IS NULL
                              OR c.expiration = '0000-00-00'
                              OR c.expiration > CURDATE()
                            )
                            AND lp.landing_id = :id

                            ORDER BY lp.position, c.product_number ASC ";

                }

            } else {

                $sql = "SELECT DISTINCT
                            c.id AS products_id,
                            c.display_number AS display_number,
                            c.custom AS is_custom,
                            sp.image AS image,
                            sp.id AS product_subcategory_id,
                            sub.id AS subcategory_id,
                            coalesce(sp.product_name, c.default_product_name) AS name,
                            coalesce(sp.product_name, c.default_product_name) AS nickname,
                            coalesce(sp.subtitle, c.default_subtitle) AS title,
                            sp.preconfigured_sku_id,
                            sp.best_seller,
                            c.on_sale AS on_sale,
                            CASE
                              WHEN c.new_until > CURDATE() THEN c.new_until
                              ELSE '0'
                            END AS expiration,
                            CASE
                              WHEN c.builder_tweak_tool_id > 0 AND tt.name IS NOT NULL THEN '1'
                              ELSE '0'
                            END AS is_tweakable,
                            sp.subcategory_detail_id AS subcategory_detailed_id,
                            sp.translation_family_id AS translation_family_id,
                            bb.id AS builder_id,
                            sp.flash_tool_id AS flash_tool_id,
                            sp.streetsign_tool_id AS streetsign_tool_id,
                            sp.tool_type_id AS tool_type_id,
                            tt.name AS tool_type_name

                        FROM bs_subcategories_geotargeted g

                        LEFT JOIN bs_subcategories sub ON (sub.id = g.subcategory_id AND sub.active = TRUE)
                        LEFT JOIN bs_subcategory_products sp ON (sp.subcategory_id = sub.id AND sp.geotarget_id = g.id)
                        LEFT JOIN bs_products c ON (c.id = sp.product_id)
                        $sql_join_skus
                        LEFT JOIN bs_subcategories_detailed sd ON (sd.subcategory_id = sub.id AND sd.active = TRUE)
                        LEFT JOIN bs_groupings grp ON (grp.id = sub.grouping_id AND grp.active = TRUE)
                        LEFT JOIN bs_categories cat ON (cat.id = grp.category_id AND cat.active = TRUE)
                        LEFT JOIN bs_tool_types tt ON (sp.tool_type_id = tt.id)
                        LEFT JOIN bs_builders bb ON (bb.builder_ref = sp.builder_ref)

                        WHERE c.active = 1
                        AND (
                          c.expiration IS NULL
                          OR c.expiration = '0000-00-00'
                          OR c.expiration > CURDATE()
                        )
                        AND g.id = :id AND g.target = :target AND (sd.id = 0 OR sd.id IS NULL)

                        ORDER BY sp.position, c.product_number ASC ";

            }

            $query = Connection::getHandle()->prepare( $sql );

            if ( PAGE_TYPE != 'geotarget' || $federal == true ) {

                $query->bindParam( ":id", $this->getPageId(), PDO::PARAM_INT );

            } else {

                //Special parameters for geotargeted pages
                $query->bindParam(":id", $refid, PDO::PARAM_INT);
                $query->bindParam(":target", $target, PDO::PARAM_STR);

            }

            if ( $query->execute() ) {

                while ( $rows = $query->fetch( PDO::FETCH_ASSOC ) ) {

                    //Making sure we have data
                    if( !empty( $rows['products_id'] ) && $rows['products_id'] > 0 ) {

                        $data[] = $rows;

                    }
                }

            }

            //Store returned data to "data" attribute for later use
            $this->setListings($data);

        }

    }

    /*************************************************
     * Start Setters
     **************************************************/

    /**
     * Set the current page id
     *
     * @param $id
     * @return ProductList()
     */
    private function setPageId($id) {

        $this->page_id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;

        return $this;

    }

    /**
     * Set the product listing data
     *
     * @param $data
     * @return ProductList()
     */
    private function setListings($data) {

        $this->data = isset($data) && count($data) > 0 ? (array)$data : NULL;

        return $this;

    }

    /*************************************************
     * Start Getters
     **************************************************
     * Get the current page Ids
     *
     * @return array
     */

    /**
     * @return int
     */
    public function getPageId() {

        return $this->page_id;

    }

    /**
     * * Get the current page product listings
     *
     * @return array
     */
    public function getListings() {

        return $this->data;

    }

    /**
     * Create a self object of the current product listing without an instance
     *
     * @param null $page_id
     * @param null $refid
     * @param null $target
     * @param bool $federal
     * @param bool $detailed
     * @param bool $detailed_products
     * @return ProductGrid
     */
    public static function create( $page_id = NULL, $refid = NULL, $target = NULL, $federal = FALSE, $detailed = FALSE, $detailed_products = FALSE ) {

        return new self( $page_id, $refid, $target, $federal, $detailed, $detailed_products );

    }

} //End of class ProductGrid