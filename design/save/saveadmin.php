<?php
require "../../bs_common.php";

$extradata=$_REQUEST[extradata];
$data = explode("&", $extradata);

parse_str($_REQUEST['extradata'], $extradata);

$ordersid= $extradata['ordersid'];
$pid = $extradata['product_id'];
$comments = $extradata['specialinstructions'];
$design_id=$extradata['design_id'];
$action=$extradata['action'];

$ObjPageProduct = new BSPageProduct();
$ObjConnection=new Connection();
$ObjConnection->DB_Connection();

$ObjShoppingCart = Cart::getFromSession(true);

// Determine whether or not an existing design should be edited.
if (!empty($extradata['design_id'])) {
	$prev_design_id = $extradata['design_id'];
	$edit_existing = true;
}else {
	$edit_existing = false;
}


$product = $ObjPageProduct->ProductCustomDisplay($pid);

$layout=$product['layout'];
$font_layout_data=$ObjFlash->GetCustomProductFontLayoutByID($layout);

$new_design_id = $ObjShoppingCart->getUniqueDesignId();

$url=website."ssctl/orders/orderdetail.php?orderid=";
$url.=urlencode($ordersid);

$thumbnail = fopen( "previews/".$new_design_id.'.jpg', 'wb' );
$image=$new_design_id.'.jpg';
fwrite( $thumbnail, base64_decode($_REQUEST['image'][0]));

$zips = fopen( "zips/".$new_design_id.'.zip', 'wb' );
$xmlpdf=$new_design_id.'.zip';
$xml=$new_design_id.'/canvas.xml';
fwrite( $zips, base64_decode($_REQUEST['payload'][0]));

$imagepng="previews/".$image; // the path with the file name where the file will be stored, previews is the directory name.
$imagepng1=$image; // the path with the file name where the file will be stored, previews is the directory name.


$save_to_file = true;
$image_quality = 100;
$image_type = -1;
$max_xm = 200;
$max_ym = 200;
$max_x = 95;
$max_y = 95;
$cut_x = 0;
$cut_y = 0;
$images_folder = 'previews/';
$thumbs_folder = 'previews/small/';
$thumbs_medium_folder = 'previews/medium/';
$to_name = '';

if (isset($_REQUEST['f'])) {$save_to_file = intval($_REQUEST['f']) == 1;}

if (isset($_REQUEST['src'])) {  $from_name = urldecode($_REQUEST['src']);}
else{$from_name = urldecode($image);}

if (isset($_REQUEST['dest'])) {$to_name = urldecode($_REQUEST['dest']);}
else{ $to_name = urldecode($image);}

if (isset($_REQUEST['q'])) {$image_quality = intval($_REQUEST['q']);}
else{$image_quality = intval(100);}

if (isset($_REQUEST['t'])) {  $image_type = intval($_REQUEST['t']);}

if (isset($_REQUEST['x'])) {  $max_x = intval($_REQUEST['x']);}
else{ $max_x = intval(150);}

if (isset($_REQUEST['y'])) {$max_y = intval($_REQUEST['y']);}
else{$max_y = intval(100);}

if (!file_exists($images_folder)) die('Images folder does not exist (update $images_folder in the script)');
if ($save_to_file && !file_exists($thumbs_folder)) die('Thumbnails folder does not exist (update $thumbs_folder in the script)');

ini_set('memory_limit', '-1');
include('image.class.php');
$img = new Thumb_image;
// initialize
$img->max_x        = $max_x;
$img->max_y        = $max_y;
$img->max_xm       = $max_xm;
$img->max_ym       = $max_ym;
$img->cut_x        = $cut_x;
$img->cut_y        = $cut_y;
$img->quality      = $image_quality;
$img->save_to_file = $save_to_file;
$img->image_type   = $image_type;
// generate small and medium thumbnail
$img->GenerateThumbFile($images_folder . $from_name, $thumbs_folder . $to_name);
$img->GenerateMediumThumbFile($images_folder . $from_name, $thumbs_medium_folder . $to_name);


if($action=="cart") {

	if($edit_existing) {

		$sql_item_unlink="select * from bs_products_custom where design_id='".mysql_real_escape_string($prev_design_id)."'";

		$result_unlink=mysql_query($sql_item_unlink);
		$unlink=mysql_fetch_array($result_unlink);

		unlink("previews/small/".$unlink['custom_image']);
		unlink("previews/medium/".$unlink['custom_image']);
		unlink("previews/".$unlink['custom_image']);
		unlink("zips/".$unlink['pdf_file']);


		$cpi=$unlink['custom_product_id'];

		$sql="update bs_products_custom set custom_image='".mysql_real_escape_string($image)."',
		custom_xml='".mysql_real_escape_string($xml)."',pdf_file='".mysql_real_escape_string($xmlpdf)."',
		products_id='".mysql_real_escape_string($pid)."',
		comments='".mysql_real_escape_string($comments)."',
		design_id ='".mysql_real_escape_string($new_design_id)."'
		where design_id='".mysql_real_escape_string($prev_design_id)."'";

		mysql_query($sql);


		$sql_id = mysql_query("SELECT id FROM bs_cart_products WHERE custom_image_id= ".mysql_real_escape_string($unlink['custom_product_id']) );
		$result_id = mysql_fetch_array($sql_id);

		$old_id= $result_id['id'];

		//update cart custom image
		$sql_perm = "UPDATE bs_products_custom SET
					custom_image = '".mysql_real_escape_string($image)."'
					WHERE custom_image_id = ". mysql_real_escape_string($cpi);
		$result_perm=mysql_query($sql_perm);

		// If there is a comment, update the cart entry with it
		if (!empty($comments)) {
			mysql_query("UPDATE bs_cart_products SET comments = '".mysql_real_escape_string($comments)."'
						 WHERE custom_image_id = '".mysql_real_escape_string($cpi)."' ");
		}

	}


	$file = "zips/" . $xmlpdf;

	// Execute our flash tool function which extracts the zip, reads the canvas, and
	// Imports all the data into our DB as a saved design.
	$ObjFlash->extract_canvas_data($file, $new_design_id, $cpi);


	// Redirect to the product, appending the cpi argument.
	header('Location:'.$url);
	exit();

}