<?php

// Require all the classes from bs_common, plus the Thumb_image class, and lift memory limitations.
ini_set('memory_limit', '-1');

include_once dirname(dirname(dirname(__FILE__)))."/include/config.php";
include_once dirname(dirname(dirname(__FILE__)))."/process/global-controller.php";
include('image.class.php');

// Extract the product ID, action, special instructions and design ID from the POST data.
$productStateParameters = (!empty($_GET['s'])? $_GET['s'] : NULL);

$extradata = array();
parse_str($_REQUEST['extradata'], $extradata);
$pid = (int)$extradata['product_id'];
$comments = $extradata['specialinstructions'];
$sql = Connection::getHandle()->prepare("SELECT id FROM bs_tool_types WHERE name = 'flash' AND active = TRUE ");
$sql->execute();
$row = $sql->fetch(PDO::FETCH_ASSOC);
$toolTypeId = (int) $row['id'];
$ObjFlashDesign = $resultsOfGlobalController['ObjFlashDesign'];



// Determine whether or not an existing design should be edited.
if ( !empty($extradata['design_id']) && CartProductFlash::checkDesignProductPair($extradata['design_id'], $pid) ) {

	$prev_design_id = $extradata['design_id'];
	$edit_existing = true;

} else {

	$edit_existing = false;

}


// Instantiate the other classes we'll need to use
$ObjPageProduct = Product::create($pid,NULL,$productStateParameters);

$aCustomProduct = new CustomProduct();

// Create a new unique design ID.
$design_id = $aCustomProduct->getUniqueDesignId();

$layout = $ObjPageProduct->getFlashToolId();



// If the product ID wasn't supplied.
if ( empty($pid) ) {

 	// Update the product_empty session variable.
	$_SESSION['product_empty'] = TRUE;

	header('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;

}

// Base64 decode the image portion of the POST data, and save it to /design/save/previews as the design ID plus ".jpg".
$thumbnail = fopen('previews/' . $design_id . '.jpg', 'wb');
$image = $design_id . '.jpg';

fwrite($thumbnail, base64_decode($_REQUEST['image'][0]));

// Base64 the payload portion of the POST data, and save it to /design/save/zips as the design ID plus ".zip".
$zips = fopen('zips/' . $design_id . '.zip', 'wb');
$xmlpdf = $design_id . '.zip';

fwrite($zips, base64_decode($_REQUEST['payload'][0]));

// Store the name of the canvas.xml file as the design ID plus "/canvas.xml".
$xml = $design_id . '/canvas.xml';

// The path of the just-written JPEG file, relative to the current directory.
$imagepng = '/previews/' . $image;

// The filename of the just-written JPEG file.
$imagepng1 = $image;

// Defaults for a bunch of variables.
$save_to_file = true;
$image_quality = 100;
$image_type = -1;
$max_xm = 200;
$max_ym = 200;
$cut_x = 0;
$cut_y = 0;
$images_folder = 'previews/';
$thumbs_folder = 'previews/small/';
$thumbs_medium_folder = 'previews/medium/';
$to_name = '';

// Set the save_to_file variable to the f value from the POST data, if supplied. Default true (above).
if ( isset($_REQUEST['f']) ) {
	$save_to_file = intval($_REQUEST['f']) == 1;
}

// Set the from_name variable to the src value from the POST data, if supplied. Default the just-written JPEG file.
if (isset($_REQUEST['src'])) {
	$from_name = urldecode($_REQUEST['src']);
} else {
	$from_name = urldecode($image);
}

// Set the to_name variable to the dest value from the POST data, if supplied. Default the just-written JPEG file.
$to_name = urldecode(isset($_REQUEST['dest']) ? $_REQUEST['dest'] : $image);

// Set the image_quality variable to the q value from the POST data, if supplied. Default 100.
$image_quality = isset($_REQUEST['q']) ? intval($_REQUEST['q']) : 100;

// Set the image_type variable to the t value from the POST data, if supplied. Default -1 (above).
if ( isset($_REQUEST['t']) ) {
	$image_type = intval($_REQUEST['t']);
}

// Set the max_x variable to the x value from the POST data, if supplied. Default 150.
$max_x = isset($_REQUEST['x']) ? intval($_REQUEST['x']) : 150;

// Set the max_y variable to the y value from the POST data, if supplied. Default 100.
$max_y = isset($_REQUEST['y']) ? intval($_REQUEST['y']) : 100;

// If the /design/save/previews directory doesn't exist, exit with an error.
if ( !file_exists($images_folder) ) {
	die('Images folder does not exist (update $images_folder in the script)');
}

// If save_to_file is true and the /design/save/previews/small directory doesn't exist, exit with an error.
if ( $save_to_file && !file_exists($thumbs_folder) ) {
	die('Thumbnails folder does not exist (update $thumbs_folder in the script)');
}

// Instantiate the Thumb_image class (no constructor function).
$img = new Thumb_image;

// Pass the just-determined variables to the Thumb_image object.
$img->max_x = $max_x;
$img->max_y = $max_y;
$img->max_xm = $max_xm;
$img->max_ym = $max_ym;
$img->cut_x = $cut_x;
$img->cut_y = $cut_y;
$img->quality = $image_quality;
$img->save_to_file = $save_to_file;
$img->image_type = $image_type;

// Resize or copy (if save_to_file is true) the just-created JPEG image or an arbitrary file name or URL from the POST data (!) to the /design/save/previews/small directory using the max_x and max_y variables.
$img->GenerateThumbFile($images_folder . $from_name, $thumbs_folder . $to_name);

// Resize or copy (if save_to_file is true) the just-created JPEG image or an arbitrary file name or URL from the POST data (!) to the /design/save/previews/medium directory using the max_xm and max_ym variables.
$img->GenerateMediumThumbFile($images_folder . $from_name, $thumbs_medium_folder . $to_name);


// If the action in the extradata part of the POST data was cart...
if ( $extradata['action'] == 'cart' ) {

	// If editing an existing design's files...
	if ( $edit_existing ) {

		// Delete the images and zip files from bs_products_custom in the row with the design ID supplied in the extradata part of the POST data.
		$sql_item_unlink = Connection::getHandle()->prepare('SELECT * FROM bs_product_custom WHERE design_id = ?');
		$sql_item_unlink->execute(array($prev_design_id));
		$unlink = $sql_item_unlink->fetch(PDO::FETCH_ASSOC);

		unlink('previews/small/' . $unlink['custom_image']);
		unlink('previews/medium/' . $unlink['custom_image']);
		unlink('previews/' . $unlink['custom_image']);
		unlink('zips/' . $unlink['pdf_file']);

		$sql_update = Connection::getHandle()->prepare('UPDATE bs_product_custom
		                             SET custom_image = :custom_image,
									custom_xml = :custom_xml,
		                                 pdf_file = :pdf_file,
									product_id = :product_id,
		                                 comments = :comments,
		                                 design_id = :design_id
		                             WHERE design_id = :prev_design_id');

		$sql_update->execute(array(
			':custom_image'   => $image,
			':custom_xml'     => $xml,
			':pdf_file'       => $xmlpdf,
			':product_id'     => $pid,
			':comments'       => $comments,
			':design_id'      => $design_id,
			':prev_design_id' => $prev_design_id
								));

		$sql_custom_item = Connection::getHandle()->prepare('SELECT * FROM bs_product_custom WHERE design_id = ?');
		$sql_custom_item->execute(array($design_id));
		$data = $sql_custom_item->fetch(PDO::FETCH_ASSOC);
		$cpi = $data['custom_product_id'];

	// If not editing an existing design's files...
	} else {

		$cdate = date('Y-m-d');

		$sql = Connection::getHandle()->prepare('INSERT INTO bs_product_custom (
		                          `design_id`,
		                          `custom_image`,
		                          `custom_xml`,
		                          `pdf_file`,
		                          `active`,
		                          `product_id`,
		                          `session_id`,
		                          `ip`,
		                          `comments`,
		                          `created_date`,
		                          `last_modified`,
		                          `tool_type_id`,
		                          `flash_tool_id`
		                      ) VALUES (
		                          :design_id,
		                          :custom_image,
		                          :custom_xml,
		                          :pdf_file,
		                          :active,
		                          :products_id,
		                          :session_id,
		                          :ip,
		                          :comments,
		                          :cdate,
		                          :modified,
		                          :tool_type_id,
		                          :flash_tool_id
		                      )');

		$sql->execute(array(
			':design_id'        => $design_id,
			':custom_image'     => $image,
			':custom_xml'       => $xml,
			':pdf_file'         => $xmlpdf,
			':active'           => TRUE,
			':products_id'      => $pid,
			':session_id'       => session_id(),
			':ip'               => $_SERVER['REMOTE_ADDR'],
			':comments'         => $comments,
			':cdate'            => $cdate,
			':modified'         => $img->custom_tool_last_modified(),
			':tool_type_id'     => $toolTypeId,
            ':flash_tool_id'    => $layout
		));

		$cpi = Connection::getHandle()->lastInsertId();

	}
	// Execute our flash tool function which extracts the zip, reads the canvas, and imports all the data into our DB as a saved design.

    $ObjFlashDesign->extract_canvas_data('zips/' . $xmlpdf, $design_id, $cpi);

	// Update the session's cpi variable.
	$_SESSION['cpi'] = $cpi;
	$save_product = new Page('product', $pid);

	// Redirect to the product, appending the cpi argument.
	header('Location: ' . $save_product->getUrl() . '?' . http_build_query(array('cpi' => $_SESSION['cpi'])).'&s=' .$productStateParameters);
	exit();

}
