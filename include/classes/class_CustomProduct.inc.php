<?php

class CustomProduct {

	function GetCustomProductList($CID) {

        $results = array();

		$sql = Connection::getHandle()->prepare(
                    "SELECT * FROM bs_product_custom WHERE customers_id = ? AND active = TRUE AND save_design = TRUE ");

		$sql->execute(array($CID));

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row;
		}

		return $results;
	}



	function GetCustomProduct($cpi = NULL) {

		$results = NULL;

		if ( !empty($cpi) ) {

			$sql = Connection::getHandle()->prepare(
					'SELECT custom_product_id AS custom_product_id, design_id AS design_id,
					custom_image AS custom_image, product_id AS products_id, pdf_file AS pdf_file,
					comments AS comments FROM bs_product_custom WHERE custom_product_id = :cpi'
			);

			$sql->bindParam(':cpi', $cpi, PDO::PARAM_INT);

			if( $sql->execute() ) $results = $sql->fetch (PDO::FETCH_ASSOC);
		}

		return $results;
	}




	function GetCustomProductByDesignID($design_id) {

		$sql = Connection::getHandle()->prepare("SELECT custom_product_id AS custom_product_id,
								   	design_id AS design_id,
								  	custom_image AS custom_image,
								  	product_id AS products_id,
								  	pdf_file AS pdf_file,
								  	comments AS comments
									FROM bs_product_custom
									WHERE design_id = ?");
		if(isset($_GET["design_id"])) {

			$sql->execute(array($_GET['design_id']));
		}

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row;
		}

		// Pull the comment from the shopping cart if this item is in a cart.
		// Cart comments will be more up-to-date
		$sql = Connection::getHandle()->prepare("SELECT comments FROM bs_cart_skus WHERE custom_image_id = ? AND comments != '' AND comments IS NOT NULL");
		$sql->execute(array($results[0]['custom_product_id']));

		$row2 = $sql->fetch(PDO::FETCH_ASSOC);

		if (!empty($row2['comments'])) {

			$results[0]['comments'] = $row2['comments'];

			$sql = $this->dbh->prepare("UPDATE bs_product_custom SET comments = ? WHERE design_id = ?");

			if(isset($_GET["design_id"])) {
				$sql->execute(array($_GET['design_id']));
			}

		}

		return $results;

	}



	function getCustomHeader($layout) {

		$sql = Connection::getHandle()->prepare("SELECT
												p.id AS pid,
												p.product_number AS product_no,
												b.cover AS frontdrops,
												b.mask AS masks,
												b.overlay AS overlays,
												b.swf AS pdf,
												b.tooltip AS tooltip,
												b.pixel_to_pdf AS pixel_to_pdf,
												b.default_font_size AS default_font_size,
												b.x AS x,
												b.y AS y,
												b.height AS height,
												b.width AS width,
												b.surface_x AS surface_x,
												b.surface_y AS surface_y,
												b.surface_height AS surface_height,
												b.surface_width AS surface_width
											FROM
												bs_products p
											LEFT JOIN bs_flash_backgrounds b ON (b.product_id = p.id)
											WHERE
												p.custom = TRUE
											AND p.default_flash_tool_id = ?
											AND p.active = TRUE
											AND b.active = TRUE
											GROUP BY
												p.product_number
											ORDER BY
												pid");
		$sql->execute(array($layout));

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row;
		}

		return $results;
	}



	function getCustomHeaderContent($layout) {

		$sql = Connection::getHandle()->prepare("SELECT color As color FROM bs_flash_tools WHERE id = ? LIMIT 1");
		$sql->execute(array($layout));

		return $sql->fetch(PDO::FETCH_ASSOC);
	}



	function saveCustomDesign() {

		//Make sure they are logged in and have a custom product to save
		if ($_SESSION['cpi'] > 0) {

			// Create a new unique design ID.

			$ObjOrders = new Orders();
			$design = $ObjOrders->duplicateCustom($_SESSION['cpi'], true);

			//Set a session flag so the user knows what happened
			if ($_SESSION['successes'] === null) {

				$_SESSION['successes'][] = "Your design has successfully been saved.";

			} else {

				$singular_notice_key = array_search("Your design has successfully been saved.", $_SESSION['successes']);
				$plural_notice_key = array_search("Your designs have successfully been saved.", $_SESSION['successes']);

				if ($singular_notice_key === false && $plural_notice_key === false) {
					$_SESSION['successes'][] = "Your design has successfully been saved.";
				} else if ($singular_notice_key !== false && $plural_notice_key === false) {
					$_SESSION['successes'][$singular_notice_key] = "Your designs have successfully been saved.";
				}

			}

			return true;

		} else {

			$_SESSION['errors'][] = "Your design could not be saved; an unknown error was encountered.";
			return false;

		}

	}

	function getSaveDesign($products_id,$cpi){

		//Make sure they are logged in and have a custom product to save
		if ($_SESSION['CID'] > 0) {
			$save_design= new Page('product',$products_id);

			if($save_design->getValidity()){
 				// Create a new unique design ID.

				$ObjOrders = new Orders();
				$design= $ObjOrders->duplicateCustom($cpi, false);

				header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
				header("Location:". $save_design->getUrl()."?cpi=".$design[0]);
				exit;

			}else{

				$_SESSION['errors'][] = "Your design could not be saved; an unknown error was encountered.";
				return false;

			}

		} else {

			$_SESSION['errors'][] = "Your design could not be saved; an unknown error was encountered.";
			return false;

		}

	}

	function deleteCustomDesign() {

		$sql = Connection::getHandle()->prepare("SELECT * FROM bs_product_custom
									WHERE save_design = TRUE
									AND active = TRUE
									AND customers_id = ?
									AND custom_product_id = ?");
		$sql->execute(array($_SESSION['CID'], $_REQUEST['custom_product_id']));
		$row = $sql->fetch(PDO::FETCH_ASSOC);

		if (!empty($row)) {
			$sql2 = Connection::getHandle()->prepare("DELETE FROM bs_cart_skus
										 WHERE custom_image_id = ?");
			$sql2->execute(array($_REQUEST['custom_product_id']));


			$preview = "design/save/previews/".$row["custom_image"];
			$preview_small = "design/save/previews/small/".$row["custom_image"];
			$preview_medium = "design/save/previews/medium/".$row["custom_image"];
			$zips = "design/save/zips/".$row["pdf_file"];
			unlink($preview);
			unlink($preview_small);
			unlink($preview_medium);
			unlink($zips);


			$sql3 = Connection::getHandle()->prepare("DELETE FROM bs_product_custom
										 WHERE save_design = TRUE
										 AND active = TRUE
										 AND customers_id = ?
										 AND custom_product_id = ?");
			$sql3->execute(array($_SESSION['CID'], $_REQUEST['custom_product_id']));

			if( $_SESSION['successes']!== NULL ){
				//Set a session flag so the user knows what happened
				$singular_notice_key = array_search("Your design has successfully been deleted.", $_SESSION['successes']);
				$plural_notice_key = array_search("Your designs have successfully been deleted.", $_SESSION['successes']);

				if  ($singular_notice_key === false && $plural_notice_key === false)  {
					$_SESSION['successes'][] = "Your design has successfully been deleted.";
				} else if ($singular_notice_key !== false && $plural_notice_key === false) {
					$_SESSION['successes'][$singular_notice_key] = "Your designs have successfully been deleted.";
				}

			}else{
				$_SESSION['successes'][] = "Your design has successfully been deleted.";

			}
			$link = new Page('my-account');
			header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
			header("Location: ".$link->getUrl());
			exit;




		} else {

			$_SESSION['errors'][] = "Your design could not be deleted; an unknown error was encountered.";
		}



		$link = new Page('my-account');
		header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
		header("Location: ".$link->getUrl());
		exit;

	}



	/**
	 * Generates a random unique design ID
	 * @return    string    unique design ID
	 */
	public function getUniqueDesignId() {

		//Seed the random generator
		mt_srand($this->makeSeed());

		//Alphanumeric upper/lower array
		$alfa = "1234567890qwrtypsdfghjklzxcvbnm";
		$design = "";

		//Loop through and generate the random design id
		for($i = 0; $i < 32; $i ++) {
		  $design .= $alfa[mt_rand(0, strlen($alfa)-1)];
		}

		//If there is a duplicate, run this function recursively
		if(!$this->isDesignIdUnique($design)) {
			$design = $this->getUniqueDesignId();
		}

		//Return the hash
		return $design;
	}



	/**
	 * This function takes a generated design id and checks to verify that it is unique
	 * @param     string    $design    [description]
	 * @return    bool                 true if unique, false if not
	 */
	private function isDesignIdUnique($design) {

		$sql = Connection::getHandle()->prepare("SELECT count(*) AS count FROM bs_product_custom
									WHERE design_id = ?");
		$sql->execute(array($design));
		$row = $sql->fetch(PDO::FETCH_ASSOC);

		return ($row['count'] > 0 ? false : true);
	}



	/**
	 * Seeds the random generator
	 * @return    Float    Microtime
	 */
	private function makeSeed() {
		list($usec, $sec) = explode(' ', microtime());
		return (float) $sec + ((float) $usec * 100000);
	}

}