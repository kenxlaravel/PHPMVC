<?php
require "../include/config.php";

$PathClasses1="../include/classes/";


$ups = new UpsAddress($key, $user, $password, $url);

if (!empty($_REQUEST["shipaddress1"]) &&  !empty($_REQUEST["shipcity"]) && !empty($_REQUEST["sstate"]) && !empty($_REQUEST["shipzip"]) && !empty($_REQUEST["shipcountry"]))
{
	$ups->setCompany($_REQUEST["shipcompany"]);
	$ups->setAddress1($_REQUEST["shipaddress1"]);
	$ups->setAddress2($_REQUEST["shipaddress2"]);
	$ups->setCity($_REQUEST["shipcity"]);
    $ups->setState($_REQUEST["sstate"]);
    $ups->setZip($_REQUEST["shipzip"]);
    $ups->setCountry($_REQUEST["shipcountry"]);

    $properties['company']  = $_REQUEST["shipcompany"];
	$properties['address1'] = $_REQUEST["shipaddress1"];
	$properties['address2'] = $_REQUEST["shipaddress2"];
	$properties['city']     = $_REQUEST["shipcity"];
    $properties['state']    = $_REQUEST["sstate"];
    $properties['zipcode']  = $_REQUEST["shipzip"];
    $properties['country']  = $_REQUEST["shipcountry"];

	$fedex = new FedExAddress($properties);
	$fedex_address = $fedex->validateAddress();

	if (!empty($fedex_address['address_line1']) || !empty($fedex_address['city']) || !empty($fedex_address['state']) || !empty($fedex_address['zipcode'])) {

		$fedex_address_formatted = $fedex_address['address_line1'] . ' ' . $fedex_address['city'] . ' ' . $fedex_address['state'] . ' ' . $fedex_address['zipcode'];

	} else {

		$fedex_address_formatted = null;
	}

	$response = $ups->getResponse();

	$listArray = $response->list;

	foreach($listArray as $list) {
		$ups_address_line = $list->addressline;
		$ups_city = $list->politicaldivisions2;
		$ups_state = $list->politicaldivisions1;
		$ups_zipcode = $list->postcodeprimarylow;
	}


	if (!empty($ups_address_line) || !empty($ups_city) || !empty($ups_state) || !empty($ups_zipcode)) {

		$ups_address_formatted = $ups_address_line ." ". $ups_city ." ". $ups_state ." ". $ups_zipcode;

	} else {

		$ups_address_formatted = null;
	}

	if(strcmp($ups_address_formatted ,$fedex_address_formatted) == 0){

?>

		<p class="bold" style="margin-top:0;">Please choose an address from the selection below to confirm the correct shipping address.</p>
		<ul style="padding-bottom:20px; list-style-position:inside;margin-top:0;">
			<li><a class="buttonlink lightblue_button" href="javascript:changeShippingAddress('<?php print $ups_address_line;?>|<?php print $_REQUEST["shipaddress2"];?>|<?php print $ups_city;?>|<?php print $ups_state;?>|<?php print $ups_zipcode;?>')">
			<?php print $ups_address_formatted;?>
		</a></li>
		</ul>

<?php	
	} else {
?>

		<p class="bold" style="margin-top:0;">Please choose an address from the selection below to confirm the correct shipping address.</p>
		<ul style="padding-bottom:20px; list-style-position:inside;margin-top:0;">
<?php
			if (!empty($ups_address_formatted)) {
?>
			<li>
				<a class="buttonlink lightblue_button" href="javascript:changeShippingAddress('<?php print $ups_address_line;?>|<?php print $_REQUEST["shipaddress2"];?>|<?php print $ups_city;?>|<?php print $ups_state;?>|<?php print $ups_zipcode;?>')">
					<?php print $ups_address_formatted;?>
				</a>
			</li>

<?php
			}
			if (!empty($fedex_address_formatted)) {
?>
			<li>
				<a class="buttonlink lightblue_button" href="javascript:changeShippingAddress('<?php print $fedex_address['address_line1'];?>|<?php print $_REQUEST["shipaddress2"];?>|<?php print $fedex_address['city'];?>|<?php print $fedex_address['state'];?>|<?php print $fedex_address['zipcode'];?>')">
					<?php print '"' . $fedex_address_formatted . '"';?>
				</a>
			</li>
<?php
			}
?>
		</ul>


<?php
	}
}else{
	print "<p class='notice'>Please enter your shipping address before validating.</p>";
}

?>