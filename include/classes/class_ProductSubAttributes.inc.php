<?php


class ProductSubAttributes {

	function ProductGetSubAttributes($sku_code,$product_no) {
		 $result = "";
		$sql = Connection::getHandle()->prepare("SELECT pt.*
									FROM bs_products p
									INNER JOIN bs_product_skus ps ON (ps.product_id = p.id)
									INNER JOIN bs_skus sku ON (sku.id = ps.sku_id AND sku.active = TRUE)
									LEFT JOIN bs_pricing pr ON (pr.id = sku.pricing_id)
									LEFT JOIN bs_pricing_tiers pt ON (pt.pricing_id = pr.id)
									WHERE p.active = TRUE
									AND p.product_number = ?
									AND sku.name = ?
									ORDER BY pt.minimum_quantity;");
			$sql->execute(array($product_no ,$sku_code));

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$result[] = $row;
		}

		return $result;
	}



	function ProductSubAttributes($sub_attributes_id = NULL) {

		$sql = Connection::getHandle()->prepare("SELECT pt.*
												FROM bs_pricing pr
												LEFT JOIN bs_pricing_tiers pt ON (pt.pricing_id = pr.id)
												WHERE pr.material_code = ?
												LIMIT 1;");
		$sql->execute(array($sub_attributes_id));
		$row = $sql->fetch(PDO::FETCH_ASSOC);

		return $row;

	}



	function ProductAttributesSize($sku_code) {

		$sql = Connection::getHandle()->prepare("SELECT s.name AS size, mg.description AS material_description FROM bs_skus sku
								INNER JOIN bs_sizes s ON (s.id = sku.size_id)
								INNER JOIN bs_materials m ON (m.id = sku.material_id)
								INNER JOIN bs_material_groups mg ON (mg.id = m.material_group_id)
							    WHERE sku.name= ?
							    AND sku.active= TRUE
							    LIMIT 0, 1");
	 	$sql->execute(array(":sku_code"=>$sku_code));



		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$result[] = $row;
		}

		return $result;
	}



	function streetsignProductGetSubAttributes($sku_code, $product_no) {

		$result = array();
		$sql = Connection::getHandle()->prepare("SELECT pr.material_code AS material_code, pt.minimum_quantity AS quantity, sku.shipping_weight, pt.price
									FROM bs_products p
									LEFT JOIN bs_product_skus ps ON (ps.product_id = p.id)
									LEFT JOIN bs_skus sku ON (sku.id = ps.sku_id)
									LEFT JOIN bs_pricing pr ON (pr.id = sku.pricing_id)
									LEFT JOIN bs_pricing_tiers pt ON (pt.pricing_id = pr.id)
									WHERE p.product_number = ?
									AND sku.name = ?
									AND pt.active = TRUE
									AND sku.streetsign_accessory_display = 1
									ORDER BY pt.minimum_quantity");

		$sql->execute(array($product_no , $sku_code));

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$result[] = $row;
		}

		return $result;

	}




} //end of class
