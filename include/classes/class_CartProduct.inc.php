<?php

class CartProduct {

    // Database handle
    protected $dbh;

    // From bs_cart_products
    public $id;

    public $cartId;

    public $productId;

    public $materialCode;

    public $savingsPercentage;

    public $quantity;

    public $unitPrice;

    public $totalPrice;

    public $creationTime;

    public $modificationTime;

    // From bs_products
    public $isCustom;

    public $productNumber;

    public $expirationDate;

    public $subtitle;

    public $name;

    public $productImage;

    public $active;

    public $subcategoryName;

    // From bs_products_sku_description
    public $freightShipping;

    public $dimCharges;

    public $skuCode;

    public $inventory;

    public $limitedInventory;

    public $size;

    public $materialDescription;

    public $skuId;

    // From bs_products_price
    public $weight;

    public $trueWeight;

    public $numberOfPackages;

    public $dimWeightUps;

    public $dimChargesUps;

    public $dimWeightFedex;

    public $dimChargesFedex;

    // Extras
    public  $cartObj;

    public  $productLink;

    public $stateParameters;

    private $tables = array (
        'bs_carts',
        'bs_cart_skus',
        'bs_cart_sku_data',
        'bs_products_custom',
        'bs_designs',
        'bs_builder_uploads',
        's_customer_file_upload'
    );


    /**
     * If a cart_products id alone is passed in, our constructor will look up the product info
     * and populate the class properties. If an ID AND data are passed in, the class properties
     * will be populated with the data.
     *
     * @param [object] $ObjCart [Handle for the cart object that this object is contained within]
     * @param [int]    $id      [ID of the cart product]
     * @param [array]  $data    [Optional array of class properties so we can avoid querying for them]
     */
    public function __construct($id, $objCart) {

        //Establish a database connection
        $this->dbh = Connection::getHandle();
        // Set the class properties we will always need
        $this->cartObj = $objCart;
        $this->cartId = $objCart->id;
        $this->id = $id;

    }

    /**
     * Takes a camelCase string and converts it to underscores
     *
     * @param  string $string [cameCase string]
     * @return string         [underscored_string]
     */
    public function toUnderscored($string) {

        $name = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));

        return $name;
    }


    /**
     * Updates the quantity of an item in the cart, and recalculates prices accordingly
     *
     * @param  int $quantity [the new quantity]
     * @return bool          [true/false on success or failure]
     */
    public function updateQuantity($quantity, $cart_data) {

        $cart_data = (array) $cart_data;
        // If the quantity is greater than zero we will recalculate price and update the cart
        if( $quantity > 0 ) {
            //Instantiate the product so we can work with it
            $TmpObjProduct = ProductPage::create($this->productId);
            // Get the new price
            $price_row = $TmpObjProduct->getFromProductsPriceWithQuantity(
                $this->productNumber, $quantity, $this->skuCode
            );
            //Change Query
            // Sum up any upcharges from attributes
            $sql = Connection::getHandle()->prepare(
                "SELECT SUM(upcharge_price) as upcharge_price FROM bs_cart_sku_data
					WHERE cart_sku_id = :cp_id GROUP BY cart_sku_id"
            );
            $sql->execute(array (":cp_id" => $this->id));
            // Price calculations
            $attributes_data = $sql->fetch(PDO::FETCH_ASSOC);
            $upcharge_price = $attributes_data['upcharge_price'];
            //Check if we got any price data. If we did not, then use the current price in the cart
            if( !empty($price_row) ) {
                $unit_price = $price_row['price'] + $upcharge_price;

            }else{
                $unit_price = $cart_data['unitPrice'] + $upcharge_price;

            }
            $total_price = $unit_price * $quantity;
            // Do the update
            $sql_update_cart = Connection::getHandle()->prepare(
                "UPDATE bs_cart_skus SET quantity = :qty, unit_price = :price, total_price = :total
						WHERE id = :id"
            );
            $return = $sql_update_cart->execute(
                array (
                    ":qty"   => (int) $quantity,
                    ":price" => (float) $unit_price,
                    ":total" => (float) $total_price,
                    ":id"    => (int) $this->id
                )
            );

            // Return updated info if the update was successful
            return ($return == TRUE ? array ("newQty" => $quantity, "unitPrice" => $unit_price) : FALSE);

            // If the quantity was zero, call the remove function
        }else{
            return ($this->remove() ? 'removed' : 'failed to remove');

        }

    }


    /**
     * Updates a table
     *
     * @param  string $table [the table to be updated e.g.: 'bs_cart_products']
     * @param  array  $set   [Array of set clauses]
     * @param  array  $where [Array of where clause statements]
     * @return bool          [true/false on success/failure]
     */
    public function update($table, $set, $where) {

        // Takes an array of 'sets' like:
        // $set = array('builder_font' => 'some_font',
        // 				'builder_color' => 'some_color')
        //
        // And an array of 'wheres' like:
        // $where = array('id' => 5,
        // 				  'cart_products_id' => 2314)
        //
        // And builds an update query from it
        // Ensure that this table is in our allowed
        if( in_array($table, $this->tables) ) {
            $executeArray = array ();
            $query = "UPDATE ".$table." SET ";
            $i = 0;
            foreach ($set as $key => $value) {
                $i++;
                $query .= $key." = :".$key.($i < count($set) ? ", " : " ");
                $executeArray[":".$key] = $value;
            }
            $query .= " WHERE ";
            $i = 0;
            foreach ($where as $key => $value) {
                $i++;
                $query .= $key." = :".$key.($i < count($where) ? " AND " : " ");
                $executeArray[":".$key] = $value;
            }
            $sql = $this->dbh->prepare($query);
            $sql->execute($executeArray);
            //Update the cart object
            $this->cartObj->setProperties();

            return TRUE;

        }else{
            return FALSE;
        }

    }


    /**
     * Removes the product from bs_cart_skus (cascades to attributes)
     * Additional custom tables should be removed from by tool-specific child classes before calling this
     *
     * @return bool [true/false on success/failure]
     */
    public function remove() {

		$cartId = (int) $this->cartObj->id;

		if( $_REQUEST['action'] != "replace" ) {
			//Check if we are removing everything in the cart
			if( count($_REQUEST['id']) > 1 ) {

                // First remove the data from bs_cart_sku_data
                $sql = Connection::getHandle()->prepare(
                    "DELETE cd FROM bs_cart_sku_data cd
							INNER JOIN bs_cart_skus ck ON (ck.id = cd.cart_sku_id)
            				WHERE ck.cart_id = :cart_id"
                );

                $sql->bindParam(":cart_id", $cartId, PDO::PARAM_INT);

                if( !$sql->execute() ) {

                    return FALSE;

                }else{

                    //Now delete from bs_cart_skus and bs_carts
                    $sql = Connection::getHandle()->prepare(
                        "DELETE cs FROM bs_cart_skus cs
                        LEFT JOIN bs_carts c ON (cs.cart_id = c.id)
                        WHERE c.id = :cart_id "
                    );

                    $sql->bindParam(":cart_id", $cartId, PDO::PARAM_INT);

                    if( !$sql->execute() ) {

                        return FALSE;

                    }else{

                        return TRUE;
                    }

                }

			} else{

				$productSku = $this->id;

				// First remove the data from bs_cart_sku_data
				$sql = Connection::getHandle()->prepare(
							"DELETE cd FROM bs_cart_sku_data cd
							INNER JOIN bs_cart_skus ck ON (ck.id = cd.cart_sku_id)
            				WHERE cd.cart_sku_id = :sku_id AND ck.cart_id = :cart_id"
				);

				$sql->bindParam(":sku_id", $productSku, PDO::PARAM_INT);
				$sql->bindParam(":cart_id", $cartId, PDO::PARAM_INT);

				if( $sql->execute() ) {

                    //Now delete from bs_cart_skus and bs_carts
					$sql = Connection::getHandle()->prepare("DELETE FROM bs_cart_skus WHERE id = :sku_id AND cart_id = :cart_id "
					);

					$sql->bindParam(":sku_id", $productSku, PDO::PARAM_INT);
					$sql->bindParam(":cart_id", $cartId, PDO::PARAM_INT);

					if( $sql->execute() ) {

						return TRUE;

					}else{

						return FALSE;
					}

				} else{

					return FALSE;

				}
			}

		//We are replacing items in the user's cart
		} else if( isset($_REQUEST['action']) ) {

            if( $_REQUEST['action'] == "replace" ) {

                // First remove the data from bs_cart_sku_data
                $sql = Connection::getHandle()->prepare(
                    "DELETE cd FROM bs_cart_skus cs
                    LEFT JOIN bs_cart_sku_data cd ON (cs.id = cd.cart_sku_id)
                    LEFT JOIN bs_carts c ON (cs.cart_id = c.id)
                    WHERE c.id = :cart_id "
                );

                $sql->bindParam(":cart_id", $cartId, PDO::PARAM_INT);

                if( !$sql->execute() ) {

                    return FALSE;

                }else{

                    //Now delete from bs_cart_skus and bs_carts
                    $sql = Connection::getHandle()->prepare(
                        "DELETE cs FROM bs_cart_skus cs
                        LEFT JOIN bs_carts c ON (cs.cart_id = c.id)
                        WHERE c.id = :cart_id "
                    );

                    $sql->bindParam(":cart_id", $cartId, PDO::PARAM_INT);

                    if( !$sql->execute() ) {

                        return FALSE;

                    }else{

                        return TRUE;
                    }
                }

            }
        }
    }

    /**
     * Seeds the random generator
     *
     * @return    Float    Microtime
     */
    static function makeSeed() {

        list($usec, $sec) = explode(' ', microtime());

        return (float) $sec + ((float) $usec * 100000);
    }

    /**
     * Generates a random unique design ID
     *
     * @return    string    unique design ID
     */
    static function getUniqueDesignId() {

        //Seed the random generator
        mt_srand(self::makeSeed());
        //Alphanumeric upper/lower array
        $alfa = "1234567890qwrtypsdfghjklzxcvbnm";
        $design = "";
        //Loop through and generate the random design id
        for ($i = 0; $i < 32; $i++) {
            $design .= $alfa[mt_rand(0, strlen($alfa) - 1)];
        }
        //If there is a duplicate, run this function recursively
        if( !self::isDesignIdUnique($design) ) {
            $design = self::getUniqueDesignId();
        }

        //Return the hash
        return $design;
    }


    /**
     * This function takes a generated design id and checks to verify that it is unique
     *
     * @param     string $design [description]
     * @return    bool                 true if unique, false if not
     */
    static function isDesignIdUnique($design) {

        //Establish a database connection
        $dbh = Connection::getHandle();
        $sql = $dbh->prepare(
            "SELECT count(*) AS count FROM bs_products_custom
									WHERE design_id = ?"
        );
        $sql->execute(array ($design));
        $row = $sql->fetch(PDO::FETCH_ASSOC);

        return ($row['count'] > 0 ? FALSE : TRUE);
    }


    /**
     * Returns hash of product based on design id
     *
     * @param  int  id from bs_designs
     * @return hash
     */
    protected function getHashByDesignID($design_id) {

        $stmt = Connection::getHandle()->prepare("SELECT hash FROM bs_designs WHERE id=:design_id LIMIT 1");
        $stmt->execute(array (":design_id" => $design_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hash = $row['hash'];
        }

        return $hash;
    }

}
