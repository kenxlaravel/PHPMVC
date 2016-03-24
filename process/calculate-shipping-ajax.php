<?php
session_start();
require "../include/config.php";

$objUPS = new UpsRateService();

// Set global variables.
$c_zip 		= $_REQUEST['shipzip'];
$t_state 	= ( isset($_REQUEST['shipstate']) ? $_REQUEST['shipstate'] : $ObjShippingCharges->ZipcodeSearch($c_zip) );
$t_country 	= $_REQUEST['shipcountry'];

$ObjShoppingCart = Cart::getFromSession(FALSE);
$ObjShippingCharges = new ShippingCharges();


//if shipping number provided , validate ship account number
$ObjShippingCharges->setShippingAccount($_REQUEST['shipaccount']);
$shipping_account = $ObjShippingCharges->ValidateShippingAccount();

// If a shipping account number was provided, we are going to ignore the rates we get back from shippingCalc()
if (!empty($shipping_account)) {
	$ignore_rates = true;
} else {
	$ignore_rates = false;
}

//Grab the estimated pickup and freight arrival dates
$pickup_timestamp=$ObjShoppingCart->getEstimatedDate(1);


/*** Getting estimated arrival date for freight shipment method ***
 * Cart::getEstimatedDate() takes into account freightdelay ONLY if cart required freight, at that point we only need to
 * pass through the freightshiptime to display the estimated arrival date.
 * In the case freight shipment is chosen when freight is not required we need to pass through both freightdelay and
 * freightshiptime to diplay the estimated arrival date.
*/
if( $ObjShoppingCart->requiresFreight() ){

	$freight_timestamp = $ObjShoppingCart->getEstimatedDate( (int)  Settings::getSettingValue('freightshiptime') );
} else {

	$freight_timestamp = $ObjShoppingCart->getEstimatedDate( (int) ( Settings::getSettingValue('freightshiptime')  + Settings::getSettingValue('freightdelay') ) );
}

$estimated_date = strtotime($freight_timestamp['estimated_date']);


$pickup_date = substr($pickup_timestamp['shipdate_formatted'], 6, 2);
$pickup_month = substr($pickup_timestamp['shipdate_formatted'], 4, 2);
$pickup_year = substr($pickup_timestamp['shipdate_formatted'], 0, 4);
$pickup_date = mktime(0, 0, 0, $pickup_month, $pickup_date, $pickup_year);

//check from listcoutries
$objUserAddress=new Addresses();
$result=$objUserAddress->listCountries();

foreach($result as $key => $value){
	 if($value['countries_iso_code_2']==$t_country) {
		if($value['zone']=='false')
			$t_state='';
	}
 }

//get freight message
$message=$ObjShoppingCart->getMessage();

//calculate shipping rates if not freight item
if(empty($message['freight_item'])){

	$ship_rate=$ObjShippingCharges->getShippingChargesBySession();

	$ObjShippingCharges = new ShippingCharges($c_zip,null,$_REQUEST['shipaddress1'],$_REQUEST['shipaddress2'],$_REQUEST['shipcity'],$t_state,$t_country);

	$ObjShippingCharges->setFreeShipping($free_shipping);

	// Get the the rate/time estimates for shipping.
	$ups_estimates = $ObjShippingCharges->shippingCalc(true, $ignore_rates);

	 // Default the shipping charge and service to none.
	$shipping_charge = 0;
	$shipping_service = '';
	$shipping_carrier = '';

	// Check if results were returned by UPS.
	if(empty($ups_estimates['errors'])){

	?>
	<table id="ups-rates">
	<tr>
		<th id="shipping-method-header" class="header">Shipping Method<br /></th>
		<th class="header">Freight</th>
		<th class="header shipping-dates">Est. Arrival Date*</th>
	</tr>

	<?php

	//Keep track of whether anything has already been selected or not.
	$selected = NULL;
	//Keep track if admin checkout available
	$admin = false;
	$customer_pickup = false;
	if($_SESSION['admin'] === true && $_SESSION['adminID'] > 0 && $_SESSION['adminID']!=$_SESSION['CID'] && empty($ship_rate['shipping_services_pre']))
	{
		$admin= true;
	}

	//Loop through the rates
	foreach ($ups_estimates['shipping_rates'] as $key => $ups_estimate ) {

		if ($key == 0 && $ship_rate['shipping_services_pre'] == '' && $selected === NULL && $admin !== true) {

			$shipping_charge = $ups_estimate['price'];
			$shipping_service = $ups_estimate['name'];
			$shipping_carrier = $ups_estimate['carrier'];
			$shipping_date = $ups_estimate['arrivalDate'];
		 	$selected = $key;

		}

		if($ship_rate['shipping_services_pre'] == $ups_estimate['name'] && $ship_rate['shipping_carrier_pre'] == $ups_estimate['carrier'] && $selected === NULL  && $admin !== true) {

			$shipping_charge = $ups_estimate['price'];
			$shipping_service = $ups_estimate['name'];
			$shipping_carrier = $ups_estimate['carrier'];
			$shipping_date =  $ups_estimate['arrivalDate'];
			$selected = $key;

		}
		if($ship_rate['shipping_services_pre'] == 'Customer Pickup' && $ship_rate['shipping_services_pre'] != $ups_estimate['name']){
			$customer_pickup =true;
		}
	}


	//Loop through the rates
	foreach ($ups_estimates['shipping_rates'] as $ups_key => $ups_estimate ) {
		// Update the shipping charge and service if this is the basic option.
		if($ups_key == 0 && $selected === NULL  && $admin !== true && $customer_pickup!== true) {

			$shipping_charge = $ups_estimate['price'];
			$shipping_service = $ups_estimate['name'];
			$shipping_carrier = $ups_estimate['carrier'];
			$shipping_date =  $ups_estimate['arrivalDate'];
			$selected = $ups_key;

		}


	?>


		<tr class="data-row">
			<td class="shipping-method">
				<input type="radio" value="<?php print $ups_estimate['name']; ?>" name="shippingmethod" <?php if ($key == 0) { echo "validate=\"required:true\""; } if ($selected === $ups_key && $admin !== true) { echo "checked=\"checked\""; } ?>
				onclick="setChangeShipMethod(<?php echo htmlspecialchars(json_encode( (string) $ups_estimate['name'] ) . ',' . json_encode( $ups_estimate['price'] ) . ',' . json_encode( (string) $ups_estimate['carrier'] ). ',' . json_encode( (string) $ups_estimate['arrivalDate'] ), ENT_QUOTES, 'UTF-8'); ?>);" />

				<span><?php print $ups_estimate['carrier']." ".$ups_estimate['name']; ?></span>


			</td>
			<td class="price">
				<span class="shipping-price">$<?php print number_format($ups_estimate['price'], 2); ?></span>
			</td>
			<td class="shipping-dates">
				<span class="estimated-arrival">

				<?php
					//added for customer pickup
					if(empty($ups_estimate['hint']))
						print date("F jS, Y", $ups_estimate['arrivalDate']);
					else if(!empty($ups_estimate['hint'])) {


?>
					<a href="#<?php echo 'hint' . $ups_key; ?>" class="pickup-details"> Click Here for Details </a>
					<div class="hidden">
					<div id="<?php echo 'hint' . $ups_key; ?>">
						<div style="margin:20px;padding:20px;width:450px;height:150px;">
							<h4 style="float:none;margin-bottom:0;"><?php echo $ups_estimate['carrier'] . ' ' . $ups_estimate['name'] . ' Information'; ?></h4>
							<p><?php echo $ups_estimate['hint']; ?></p>
							<div style="float:none;padding:5px 10px;margin:10px 0;background-color:#e6e6e6;text-align:center;">
								<p style="margin-bottom:0;"><strong>Estimated Arrival Date</strong> <?php print date("F jS, Y", $ups_estimate['arrivalDate']); ?></p>
							</div>
						  </div>
						</div>
					</div>
<?php
				}
?>

				</span>
			</td>
		</tr>


<?php
	}


	//Add customer pickup to the end of the list if available
	if ($ups_estimates["pickupAvailable"] === true) {
?>
		<tr class="data-row">
			<td class="shipping-method">
				<input type="radio" value="<?php print 'Customer Pickup'; ?>" name="shippingmethod" <?php if ($key == 0) { echo "validate=\"required:true\""; }
				if ($selected === NULL && $customer_pickup === true) { echo "checked=\"checked\""; } ?>
				onclick="setChangeShipMethod(<?php echo htmlspecialchars(json_encode( (string) 'Customer Pickup') . ',' . json_encode( number_format(0, 2) ) . ',' . json_encode( (string)'' ). ',' . json_encode( (string) $pickup_date ), ENT_QUOTES, 'UTF-8'); ?>);" />
				<span>Customer Pickup</span>
			</td>
			<td class="price">
				<span class="shipping-price">$0.00</span>
			</td>
			<td class="shipping-dates">
				<span class="estimated-arrival">
					<a href="#customer-pickup" class="pickup-details"> Click Here for Details </a>
				</span>
			</td>
		</tr>
<?php
		if($customer_pickup === true){

			$shipping_charge = 0.00;
			$shipping_service = 'Customer Pickup';
			$shipping_carrier = '';
			$shipping_date = $pickup_date;
		}

	}

	//Truck pick up for Admin with existing customer account or new customer account
	if($_SESSION['admin'] === true && $_SESSION['adminID'] > 0 && $_SESSION['adminID']!=$_SESSION['CID'])
	{
?>
		<tr class="data-row">
			<td><input <?php if( ($selected === NULL || $ship_rate['shipping_services_pre'] == 'LTL / Freight Carrier' ) && $customer_pickup !== true ) { echo "checked=\"checked\"" ;} ?> value="LTL / Freight Carrier" name="shippingmethod" onclick="setChangeShipMethod('LTL / Freight Carrier',$('#truck-pickup').val(), '',<?=htmlspecialchars(json_encode( (string) $estimated_date ), ENT_QUOTES, 'UTF-8' ) ?>);" type="radio" >
					<span>LTL / Freight Carrier</span></td>
				<td class="price">$ <input name="truck_pickup" id="truck-pickup" value ="<?php if( $ship_rate['shipping_services_pre'] == 'LTL / Freight Carrier') { echo $ship_rate['shipping_charges_pre']; } ?>" class="text numeric-only" style="display:inline;width:45px;" size="8" maxlength="8" type="text" onkeyup="setChangeShipMethod('LTL / Freight Carrier', $('#truck-pickup').val(), '','');"></td>
				<td class="shipping-dates"><span class="estimated-arrival"><?=date("F jS, Y", $estimated_date)?></span></td>
		</tr>
<?php
		//set session on failure if pre selected LTL / Freight shipment
		if($ship_rate['shipping_services_pre']== 'LTL / Freight Carrier' || $selected === NULL && $customer_pickup !== true ){
			$shipping_charge =$ship_rate['shipping_charges_pre'];
			$shipping_service = 'LTL / Freight Carrier';
			$shipping_carrier = '';
			$shipping_date = $estimated_date;
		}

	}
?>
	</table>

<?php
	} else if (!empty($ups_estimates['errors'])) {
?>
		<ul class="error clearfix">
<?php
			foreach($ups_estimates['errors'] as $error){
				print '<li>'.$error.'</li>';
			}
?>
		</ul>
<?php
	}
?>



<?php
} else {

	// Instantiate class to check zipcode validation and state
 	$ObjShippingCharges = new ShippingCharges();
	$zipcode_detail= $ObjShippingCharges ->zipcodeSearch($c_zip);

	//date calculation for freight shipment
	$freight_date= date("F jS, Y",$estimated_date);

	//get shipping rate if pre selected from session
	$ship_rate=$ObjShippingCharges->getShippingChargesBySession();

	//Keep track of admin checkout
	$admin = false;

	if($_SESSION['admin'] === true && $_SESSION['adminID'] > 0 && $_SESSION['adminID']!=$_SESSION['CID'] && empty($ship_rate['shipping_services_pre']))
	{
		$admin= true;

	}
?>
<table id="ups-rates">
	<tr>
		<th id="shipping-method-header" class="header">Shipping Method<br /></th>
		<th class="header">Freight</th>
		<th class="header shipping-dates">Est. Arrival Date*</th>
	</tr>
	<tr class="data-row">
		<td class="shipping-method">
			<input type="radio" value="<?php print 'Freight Shipment (Actual Cost TBD)'; ?>" name="shippingmethod" onclick="setChangeShipMethod('Freight Shipment (Actual Cost TBD)','1.00', '',<?php echo htmlspecialchars(json_encode($estimated_date),ENT_QUOTES,'UTF-8') ;?>);" <?php if ($key == 0) { echo "validate=\"required:true\""; }  ?>
			 <?php if( ($ship_rate['shipping_services_pre'] == 'Freight Shipment (Actual Cost TBD)' || $selected == NULL) && $admin == false) {echo "checked=\"checked\"";} ?>/>
			<span><?php print "Freight Shipment <span class='special-note'>(Actual Cost TBD)</span>"; ?></span>
		</td>
		<td class="price">
			<span class="shipping-price">$<?php print "1.00"; ?></span>
		</td>
		<td class="shipping-dates"><?php print $freight_date;?></td>
	</tr>
<?php
	//set freight shipment to default if nothing is selected
	if(empty($ship_rate['shipping_services_pre'])){
		$shipping_charge = '1.00';
		$shipping_service = 'Freight Shipment (Actual Cost TBD)';
		$shipping_carrier = '';
		$shipping_date = $estimated_date;

	}
	 	//Add customer pickup to the end of the list if available
		if($zipcode_detail['state']=='NJ') {
	?>
			<tr class="data-row">
				<td class="shipping-method">
					<input type="radio" value="<?php print 'Customer Pickup'; ?>" name="shippingmethod" <?php if ($key == 0) { echo "validate=\"required:true\""; }
					if($ship_rate['shipping_services_pre'] == 'Customer Pickup') {echo "checked=\"checked\"";}  ?> onclick="setChangeShipMethod(<?php echo htmlspecialchars(json_encode( (string) "Customer Pickup" ) . ',' . json_encode( number_format(0, 2) ) . ',' . json_encode( (string) '' ).','.json_encode( (string) $pickup_date ), ENT_QUOTES, 'UTF-8'); ?>);" />
					<span>Customer Pickup</span>
				</td>
				<td class="price">
					<span class="shipping-price">$0.00</span>
				</td>
				<td class="shipping-dates">
					<span class="estimated-arrival">
						<a href="#customer-pickup" class="pickup-details"> Click Here for Details </a>
					</span>
				</td>
			</tr>
	<?php
		}

//Truck pick up for Admin with existing customer account or new customer account
	if($_SESSION['admin'] === true && $_SESSION['adminID'] > 0 && $_SESSION['adminID']!=$_SESSION['CID'])
	{
?>
		<tr class="data-row">
			<td><input <?php if($ship_rate['shipping_services_pre'] == 'LTL / Freight Carrier' || $admin == true) {echo "checked=\"checked\"";}  ?> value="LTL / Freight Carrier" name="shippingmethod" onclick="setChangeShipMethod('LTL / Freight Carrier',$('#truck-pickup').val(), '' ,<?=htmlspecialchars(json_encode( (string) $estimated_date ), ENT_QUOTES, 'UTF-8' ) ?>);" type="radio" >
					<span>LTL / Freight Carrier</span></td>
				<td class="price">$ <input name="truck_pickup" value = "<?php if ($ship_rate['shipping_services_pre'] == 'LTL / Freight Carrier') { echo $ship_rate['shipping_charges_pre']; } ?>" id="truck-pickup" class="text numeric-only"
					style="display:inline;width:45px;" size="8" maxlength="8" type="text" onkeyup="setChangeShipMethod('LTL / Freight Carrier', $('#truck-pickup').val(), '' ,'');"></td>
				<td class="shipping-dates"><span class="estimated-arrival"><?=date("F jS, Y", $estimated_date) ?></span></td>
		</tr>
<?php
		//set session to LTL / Freight if admin checkout available
		if(empty($ship_rate['shipping_services_pre'])){
			$shipping_charge =0.00;
			$shipping_service = 'LTL / Freight Carrier';
			$shipping_carrier = '';
			$shipping_date = $estimated_date;

		}

	}
?>

	<tr>
		<td colspan=3><p class ='success'><?php print $message['freight_item'];?></p></td>
	</tr>
</table>
<?php
	//set & update session on failure to appropriate shipping method
	if($ship_rate['shipping_services_pre'] == 'Freight Shipment (Actual Cost TBD)' ){
		$shipping_charge = '1.00';
		$shipping_service = 'Freight Shipment (Actual Cost TBD)';
		$shipping_carrier = '';
		$shipping_date = $estimated_date;
	}else if($ship_rate['shipping_services_pre'] == 'Customer Pickup') {
		$shipping_charge = '0.00';
		$shipping_service = 'Customer Pickup';
		$shipping_carrier = '';
		$shipping_date = $pickup_date;

	}else if($ship_rate['shipping_services_pre'] == 'LTL / Freight Carrier'){
		$shipping_charge = $ship_rate['shipping_charges_pre'];
		$shipping_service = 'LTL / Freight Carrier';
		$shipping_carrier = '';
		$shipping_date = $estimated_date;
	}


}

	// Update the user's session with the shipping info
	$_SESSION['shipping_services_pre'] = $shipping_service;
	$_SESSION['shipping_charges_pre'] = $shipping_charge;
	$_SESSION['shipping_carrier_pre'] = $shipping_carrier;
	$_SESSION['shipping_arrival_date'] = $shipping_date;
?>


<script type="text/javascript">

	var
	method = <?php echo json_encode( (string) $shipping_service ); ?>,
	cost = <?php echo json_encode( $shipping_charge ); ?>,
	carrier = <?php echo json_encode( (string) $shipping_carrier ); ?>;
	arrivalDate =<?php echo json_encode( $shipping_date ); ?>;

	setChangeShipMethod(method, cost, carrier, arrivalDate );

</script>
