<?php

include(dirname(dirname(__FILE__)).'/include/config.php');
include('global-controller.php');

//check if this is an AJAX or non-AJAX request so we can handle informing the customer differently
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && mb_strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
	$ajax = true;
} else {
	$ajax = false;
}

$ObjCustomProduct = new CustomProduct();
$products_id = $_REQUEST['pid'];
$cpi = $_REQUEST['cpi'];

//Check if the user is logged in
if ($_SESSION['CID'] > 0) {

	//If we already have a CPI, the customer is reordering a saved design and we'll have to duplicate it and then generate a new CPI
	if ($cpi > 0) {

		$ObjCustomProduct->getSaveDesign($products_id,$cpi);

	//Otherwise they are saving for the first time
	} else {

		//Save the design
		$saved = $ObjCustomProduct->saveCustomDesign();

		//Check if the design saved successfully
		if ($saved) {

			$account_page = new Page('my-account');
			$account_url = $account_page->getUrl();

			//If this was an AJAX request, display a success message
			if ($ajax) {

				$response=array(
						'success'=>true,
						'accounturl'=>$account_url
						);
				//Success!
				header("Content-Type: application/json");
				echo json_encode($response);
				exit;

			//Otherwise it wasn't an AJAX request. We'll head them to their account page and throw a flag that
			//the design was successfully saved
			} else {

				//Send them to their account page
				header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
				header("Location: " . $account_url);
				exit;

			}

		} else {

			//If this was an AJAX request, display an error so the user knows their design has not been saved
			if ($ajax) {
			 	//There was a problem saving the design
				header("Content-Type: application/json");
				echo json_encode(array( "success"=> false,"errors"=>"There was a problem saving the design."));
				exit;

			//Otherwise this was not an AJAX request. Redirect them to the page they were just on and throw a flag
			} else {
				 //Throw a flag to notify the user that their design did not save
				$_SESSION['notices'][] = 'savedesign-failed';

				//Head them back to the product they were just on
				header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
				header("Location: " . $_SESSION['target']);
				exit;

			}

		}
	}

} else {

	//Instantiate the sign in page
	$signin_page = new Page('sign-in');
	$signin_url = $signin_page->getUrl() . "?action=savedesign";

	//If this was an AJAX request, we will tell them they need to sign in to save a design
	if ($ajax) {

		$_SESSION['savedesign-signed-in'] = true;

		//The user is not signed in
		header("Content-Type: application/json");
		echo json_encode(array(
		"signedin"=> false,
		"signinurl"=> $signin_url
		));
		exit;

	//Otherwise it wasn't AJAX, head them straight to the sign in page, and throw a flag there that tells them
	//they need to signed in before they may save the design
	} else {

		//Throw a flag on the signin page so the user knows they need to sign in
		$_SESSION['notices'][] = 'savedesign-signin';

		//Send them to the sign in page
		header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
		header("Location: " . $signin_url);
		exit;

	}

}