<?php


class ProductAttributes {

	function ProductAttributesList($pid) {
		$result = array();
		$sql = Connection::getHandle()->prepare("SELECT * FROM bs_skus s
									INNER JOIN bs_product_skus ps ON (ps.sku_id = s.id)
							  		INNER JOIN bs_products p ON (p.id = ps.product_id)
							  		WHERE p.id=?
							  		AND s.active= TRUE
							  		ORDER BY ps.position");
		$sql->execute(array($pid));

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$result[] = $row;
		}

		return $result;

	}



	function ProductAttributesAccessoriesList($apid) {
		$result = array();
		$sql = Connection::getHandle()->prepare("SELECT p.*, s.*, s.id as sku_id, sz.name AS size, m.name AS material, pr.material_code
												FROM bs_products p INNER JOIN bs_product_skus ps ON (ps.product_id = p.id)
												INNER JOIN bs_skus s ON (s.id = ps.sku_id AND s.active = TRUE)
												INNER JOIN bs_materials m ON (m.id = s.material_id AND m.active = TRUE)
												INNER JOIN bs_sizes sz ON (sz.id = s.size_id AND sz.active = TRUE)
												LEFT JOIN bs_pricing pr ON (pr.id = s.pricing_id)
												WHERE p.product_number=? AND p.active = TRUE");
		$sql->execute(array($apid));

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$result[] = $row;
		}

		return $result;

	}



	function ProductAttributesFirstList($pid) {
		$result = array();
		$sql = $this->dbh->prepare("SELECT pr.material_code AS material_code ,s.`name` AS sku_code ,p.product_number AS product_number
							  		FROM bs_skus s
									INNER JOIN bs_product_skus ps ON s.id = ps.sku_id
							  		INNER JOIN bs_products p ON (ps.product_id = p.id)
									LEFT JOIN bs_pricing pr on s.pricing_id = pr.id
							  		WHERE p.id=?
							  		AND s.active=1
							  		ORDER BY ps.position
							  		LIMIT 0,1");
		$sql->execute(array($pid));

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$result[] = $row;
		}

		return $result;
	}




	function ProductAttributesAccessoriesFirstList($apid) {
		$row = array();
		$sql = Connection::getHandle()->prepare("SELECT pr.material_code AS material_code , s.name AS sku_code
							  		FROM bs_products p
							  		INNER JOIN bs_product_skus ps ON (ps.product_id = p.id)
							  		INNER JOIN bs_skus s ON (s.id = ps.sku_id AND s.active = TRUE)
							  		INNER JOIN bs_pricing pr ON (pr.id = s.pricing_id)
							  		WHERE p.product_number=?
							  		AND p.active=1
							  		ORDER BY ps.position
							  		LIMIT 0,1");
		$sql->execute(array($apid));

		$row = $sql->fetch(PDO::FETCH_ASSOC);

		return $row;

	}



	function ProductAttributesFirstListsearchFeed($pid) {
		$result = array();
		$sql = Connection::getHandle()->prepare("SELECT s.name AS sku_code
									FROM bs_products p
							  		INNER JOIN bs_product_skus ps ON (ps.product_id = p.id)
							  		INNER JOIN bs_skus s ON (s.id = ps.sku_id AND s.active = TRUE)
							  		WHERE p.product_number=?
							  		AND p.active=1
							  		ORDER BY ps.position
							  		LIMIT 0,1");
		$sql->execute(array($pid));

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$result[] = $row;
		}

		return $result;

	}


/*  No Longer Used
	function LandingProductAttributesAccessoriesList($apid) {
		$row = array();
		$sql = Connection::getHandle()->prepare("SELECT * FROM bs_products_sku_description
							  WHERE product_number=?
							  AND active='Y'
							  AND landing_page_show='Y'
							  ORDER BY position");
		$sql->execute(array($apid));

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$result[] = $row;
		}

		return $row;

	}

*/

/*  No Longer Used	

	function LandingProductAttributesAccessoriesFilterList($apid,$sku_filter) {
		$result = array();
		$sql = Connection::getHandle()->prepare("SELECT * FROM bs_products_sku_description
						 	  WHERE product_number=?
						 	  AND active='Y'
						 	  AND landing_page_show='Y'
						 	  AND sku_filter=?
						 	  ORDER BY position");
		$sql->execute(array($apid, $sku_filter));

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$result[] = $row;
		}

		return $result;

	}
*/

	function getCustomProductSizeFilterList($productno) {
		$result = array();
		$sql = Connection::getHandle()->prepare("SELECT * FROM bs_products p 
								INNER JOIN bs_product_skus ps on p.id = ps.product_id 
								INNER JOIN bs_skus s on ps.sku_id = s.id 
								LEFT JOIN bs_sizes sz on s.size_id = sz.id
							  	WHERE p.product_number=?
							  	AND p.active = 1
							  	AND s.active = 1
								AND sz.active = 1
							   	GROUP BY s.size_id
							  	ORDER BY ps.position");
		$sql->execute(array($productno));

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$result[] = $row;
		}

		return $result;

	}



	function getCustomProductSkuCodeFilterBySizeList($size) {
		$result = array();
		$sql = Connection::getHandle()->prepare("SELECT s.* FROM bs_products p
							  INNER JOIN bs_product_skus ps ON (ps.product_id = p.id)
							  INNER JOIN bs_skus s ON (s.id = ps.sku_id AND s.active = TRUE)
							  WHERE s.size_id =?
							  AND p.product_number=?
							  AND p.active= TRUE
							  ORDER BY s.size_id");
		$sql->execute(array($size, $_REQUEST['productno']));

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$result[] = $row;
		}

	}


} //end of class
?>