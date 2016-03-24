<?php

require_once '../bs_common.php';

$ObjUserAddress = new Addresses();

if ( $_POST['type'] == 'billing' ) {

	$formdata = array(
		'default_billing' => $_POST['id']
	);

} elseif ( $_POST['type'] == 'shipping' ) {

	$formdata = array(
		'default_shipping' => $_POST['id']
	);

}

$ObjUserAddress->setDefaultAddress($formdata, $_SESSION['CID']);
