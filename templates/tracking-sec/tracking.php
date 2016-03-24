
<?php
$load_count=0;

if(isset($_REQUEST['orderno'])){

	$orderno=$_REQUEST['orderno'];
	$load_count=1;

	$order=$ObjOrder->GetCustomerOrder($orderno);
	$msg='';
	$success_msg='';

    if( !empty($order) ) {

        foreach ($order as $key => $value) {

            $date_pickup = $ObjOrder->GetDatePickup($value['order_no']);

            if( $value['orders_status'] == '2' || $value['orders_status'] == '4' ) {
                $msg = "Order ".$orderno." is currently in production.";
            }

            if( $value['tracking_number'] == '' && $value['shipping_services'] != 'Customer Pickup' && $value['orders_status'] == '3' ) {
                $msg = "Order ".$orderno." has not yet shipped.";
            }

            if( ($value['tracking_number'] == '') && ($value['shipping_services'] == 'Customer Pickup') && ($value['orders_status'] != '6' || $value['orders_status'] != '7') ) {
                $msg = "Order ".$orderno." is not yet available for pickup.";
            }

            if( $value['tracking_number'] == '' && $value['shipping_services'] == 'Customer Pickup' && $value['orders_status'] == '6' ) {
                $success_msg = "Order ".$orderno." is available for pickup!";
            }

            if( $value['tracking_number'] == '' && $value['shipping_services'] == 'Customer Pickup' && $value['orders_status'] === '7' ) {
                $smsg = "Order ".$orderno." was picked up on ".date("F jS, Y", strtotime($date_pickup)).".";
            }

            if( $value['tracking_number'] == '' && $value['shipping_services'] == 'LTL / Freight Carrier' && $value['orders_status'] === '3' ) {
                $msg = "Order ".$orderno." has been shipped, but cannot be tracked online. Contact Customer Service for more information.";
            }

        }
    }

	if(!count($order)>0){
		$error_msg='Order '. $orderno . ' not found. Please enter a valid order number.';
	}

}

$order_history=new Page('orderhistory');
$url=$order_history->getUrl();

//check for page load/any error for ups or customre pickup
if(empty($orderno) || (!empty($error_msg)) || (!empty($msg)) && (empty($success_msg))){
?>

<p class="h3 h3-rev pad-left-10">Track An Order</p>

<div class="pad-left-10 span-14">

	<p class="prepend-top h4">Enter Your SafetySign.com Order Number</p>
	<p>Your 11-digit order number appears on your confirmation email. The subject line includes your order number (e.g. Order Confirmation #SS1111111111).</p>
	<?php
		if(($_SESSION['UserType']=='U') || ($_SESSION['UserType']=='R' )){ ?>
	<p>You can also find track order from your <a href="<?php print htmlspecialchars($url,ENT_QUOTES,"UTF-8");?>" class="underline">Order History</a>.</p>
<?php } ?>
<div class="order-tracking-picker">
	<form accept-charset="utf-8" action='/process/tracking.php' method="post" name="tracking">
		<input type="text" name="orderno" value="" id="ordernoList" autocomplete="off" />
		<input type="submit" name="submit-tracking" class="button orange" value='Track An Order'/>


<?php
if(isset($_SESSION['CID'])){ ?>
	<div class="dropdown clearfix hidden">
			<ul>
	<?php //Instantiate class

  //get tracking number for respective orderno
  $trackingNUM_list = $obj_tracking_NUM->order_number_list($_SESSION['CID']);

		foreach($trackingNUM_list as $array){ ?>

			<li><span class="track-order-num"><?php echo htmlspecialchars($array['order_no'], ENT_QUOTES,"UTF-8");?></span>

			<span class="track-price">$<?php echo htmlspecialchars($array['total_amount'], ENT_QUOTES,"UTF-8");?></span>


			<span class="track-date"><?php echo date("m-d-Y", strtotime($array['date_purchased']));?></span></li>
				<?php } ?>
			</ul>
		</div>
  <?php } ?>

	</form>
</div>
	<p class="prepend-top special-note append-bottom">If you have trouble locating your order number, please contact Customer Service for assistance.</p>

<?php 	//check for invalid order no or empty orderno
if(!empty($error_msg)){

	 print "<p class='error'>" . htmlspecialchars($error_msg, ENT_QUOTES, 'UTF-8') . "</p>";
}
if( $msg && empty($smsg)  ) {

?>
	<p class="notice-neutral span-14"><?php print htmlspecialchars($msg,ENT_QUOTES,"UTF-8");?></p> <?php }

	if( isset($smsg) && empty($msg) ){
?>
	<p class="success span-14"><?php print htmlspecialchars($smsg,ENT_QUOTES,"UTF-8");?></p> <?php }?>
</div>

<?php }
//Load when customer pickup available
if(!empty($success_msg))
{

?>

<p class="h3 h3-rev pad-left-10">Customer Pickup</p>
<div class="pad-left-10 clear span-14">
		<p class="prepend-top left-side pad-right-10 bold">Track another order:</p>
		<div class="order-tracking-picker">
		<form accept-charset="utf-8" action='/process/tracking.php' method="post" name="tracking" class="append-bottom">
		<input type="text" name="orderno" value=""/>
		<input type="submit" name="submit-tracking" class="button orange" value='Track An Order'/>
		<?php
if(isset($_SESSION['CID'])){ ?>
	<div class="dropdown clearfix hidden" style="left: 0px">
			<ul>
	<?php //Instantiate class
  // $obj_tracking_NUM = new Tracking();
  //get tracking number for respective orderno
  $trackingNUM_list = $obj_tracking_NUM->order_number_list($_SESSION['CID']); 


		foreach($trackingNUM_list as $array){ ?>

			<li><span class="track-order-num"><?php echo htmlspecialchars($array['order_no'], ENT_QUOTES,"UTF-8");?></span>

			<span class="track-price"><?php echo htmlspecialchars($array['total_amount'], ENT_QUOTES,"UTF-8");?></span>


			<span class="track-date"><?php echo date("m-d-Y", strtotime($array['date_purchased']));?></span></li>
				<?php } ?>
			</ul>
		</div>
  <?php } ?>
	</form>
</div>
	<p class="success"><?php echo htmlspecialchars($success_msg, ENT_QUOTES, 'UTF-8'); ?></p>
</div>
<div class="span-16 pad-left-10">
	<div class="span-5 append-bottom">
		<p class="h5">Customer Pickup Address</p>
		<p>64 Outwater Lane <br />
			Garfield, NJ 07026</p>
	</div>
	<div class="span-4 append-bottom">
		<p class="h5">Hours of Operation</p>
		<p>9am - 5pm Eastern <br>
			Monday - Friday</p>
	</div>
	<div class="span-5 last append-bottom">
		<p class="h5">Phone Numbers</p>
		<p>Toll Free Phone: 800-274-6271<br/>
		Local Phone: 973-340-7889</p>
	</div>
	<iframe width="650" height="380" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=Brimar+Inc,+Outwater+Lane,+Garfield,+NJ&amp;aq=1&amp;oq=brimar&amp;sll=40.07304,-74.724323&amp;sspn=8.681886,14.304199&amp;ie=UTF8&amp;hq=Brimar+Inc,&amp;hnear=Outwater+Ln,+Garfield,+Bergen,+New+Jersey&amp;t=m&amp;cid=239895803142426408&amp;ll=40.89204,-74.114285&amp;spn=0.019465,0.025749&amp;z=14&amp;iwloc=A&amp;output=embed"></iframe>
	<br />
	<small><a href="https://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=Brimar+Inc,+Outwater+Lane,+Garfield,+NJ&amp;aq=1&amp;oq=brimar&amp;sll=40.07304,-74.724323&amp;sspn=8.681886,14.304199&amp;ie=UTF8&amp;hq=Brimar+Inc,&amp;hnear=Outwater+Ln,+Garfield,+Bergen,+New+Jersey&amp;t=m&amp;cid=239895803142426408&amp;ll=40.89204,-74.114285&amp;spn=0.019465,0.025749&amp;z=14&amp;iwloc=A" style="color:#0000FF;text-align:left">View Larger Map</a></small>
</div>


<?php
}

if( !empty($orderno) || (empty($error_msg)) || (empty($msg)) ) {

	//Instantiate class
	$obj_tracking = new Tracking();

	//get tracking number for respective orderno

    if( isset($orderno) ) {

        $tracking = $obj_tracking->getTracknum($orderno);

        if( mb_strtolower($tracking['shipping_carrier']) == 'ups' ) {
            //get UPS tracking setting for returned tracking num
            $obj_tracking->setUpsXml($tracking['tracking_number']);

        }else if( mb_strtolower($tracking['shipping_carrier']) == 'fedex' ) {
            //get FedEx tracking setting for returned tracking num
            $obj_tracking->setFedExXml($tracking['tracking_number']);

        }
        //get display array retrived from upsresponse and xml
        $track_display = $obj_tracking->getDisplay($tracking, $tracking['shipping_carrier']);
    }
}

if(!empty($track_display["Error"])) {
?>
	<p class="h3 h3-rev pad-left-10">Track An Order</p>

	<div class="pad-left-10 span-14">
		<p class="prepend-top h4">Enter Your SafetySign.com Order Number</p>
		<p>Your 11-digit order number appears on your confirmation email. The subject line includes your order number (e.g. Order Confirmation #SS1111111111).</p>
		<?php
			if(($_SESSION['UserType']=='U') || ($_SESSION['UserType']=='R' )){ ?>
		<p>You can also find track order from your <a href="<?php print htmlspecialchars($url,ENT_QUOTES,"UTF-8");?>" class="underline">Order History</a>.</p>
<?php } ?>
		<div class="order-tracking-picker">
		<form accept-charset="utf-8" action='/process/tracking.php' method="post" name="tracking" >
			<input type="text" name="orderno" value=""/>
			<input type="submit" name="submit-tracking" class="button orange" value='Track An Order'/>
			<?php
if(isset($_SESSION['CID'])){ ?>
	<div class="dropdown clearfix hidden" style="left: 0px">
			<ul>
	<?php //Instantiate class
   $obj_tracking_NUM = new Tracking();
  //get tracking number for respective orderno
  $trackingNUM_list = $obj_tracking_NUM->order_number_list($_SESSION['CID']); 


		foreach($trackingNUM_list as $array){ ?>

			<li><span class="track-order-num"><?php echo htmlspecialchars($array['order_no'], ENT_QUOTES,"UTF-8");?></span>

			<span class="track-price"><?php echo htmlspecialchars($array['total_amount'], ENT_QUOTES,"UTF-8");?></span>


			<span class="track-date"><?php echo date("m-d-Y", strtotime($array['date_purchased']));?></span></li>
				<?php } ?>
			</ul>
		</div>
  <?php } ?>
		</form>
	</div>
		<p class="prepend-top special-note append-bottom">If you have trouble locating your order number, please contact Customer Service for assistance.</p>

<?php
	if($track_display["Error"]=='Hard'  && mb_strtolower($value['shipping_carrier']) == 'ups' ){
		print "<p class='notice-neutral'>UPS has not processed your order yet. Please try again later.</p>";
	}else if(!empty($track_display['Error']) && mb_strtolower($value['shipping_carrier']) == 'fedex' && $track_display['Error']!='api') {
		print "<p class='notice-neutral'>FedEx has not processed your order yet. Please try again later.</p>";
	}else if($track_display['Error']=='api'){
		print "<p class='notice-neutral'>Unable to retrieve results. Please try again later.</p>";
	}
?>
	</div>
<?php
	}

//check & display , if retriving display values from ups for trackin
if(!empty($track_display) && (!empty($tracking['tracking_number'])) && (empty($track_display["Error"])) ) {

?>

	<div class="tracking-information">
		<p class="h3 h3-rev pad-left-10 append-bottom">Order Tracking Information</p>
		<div class="pad-left-10 append-bottom clearfix">
		<p class="prepend-top left-side pad-right-10 bold">Track another order:</p>
		<div class="order-tracking-picker left-side">
		<form accept-charset="utf-8" action='/process/tracking.php' method="post" name="tracking" >
		<input type="text" name="orderno" value="" autocomplete="off" />
		<input type="submit" name="submit-tracking" class="button orange" value='Track An Order'/>


<?php
if(isset($_SESSION['CID'])){ ?>
	<div class="dropdown clearfix hidden">
			<ul>
	<?php //Instantiate class
   $obj_tracking_NUM = new Tracking();
  //get tracking number for respective orderno
  $trackingNUM_list = $obj_tracking_NUM->order_number_list($_SESSION['CID']);


		foreach($trackingNUM_list as $array){ ?>

			<li><span class="track-order-num"><?php echo htmlspecialchars($array['order_no'], ENT_QUOTES,"UTF-8");?></span>

			<span class="track-price">$<?php echo htmlspecialchars($array['total_amount'], ENT_QUOTES,"UTF-8");?></span>


			<span class="track-date"><?php echo date("m-d-Y", strtotime($array['date_purchased']));?></span></li>
				<?php } ?>
			</ul>
		</div>
  <?php } ?>

		</form>
	</div>
</div>
	<p class="h4 pad-left-10">Tracking Detail</p>

		<div class="span-12 append-bottom pad-left-10">

				<table class="tracking-details">
					<thead>
					<tr class="data-row">
						<th colspan=2 >Shipment Information</th>
					</tr>
				</thead>
			 		<tbody>
			 			<tr class="data-row">
				 				<td class="bold">Order Number</td>
				 				<td><?php print $orderno;?></td>
				 			</tr>
				 			<tr class="data-row">
				 				<td class="bold" >Tracking Number</td>
				 				<td><?php print $tracking['tracking_number'];?></td>
				 			</tr>
				 			<tr class="data-row">
				 				<td class="bold">Service</td>
				 				<td><?php print htmlspecialchars($value['shipping_carrier']." ".$value['shipping_services'],ENT_QUOTES,"UTF-8"); ?></td>
				 			</tr>
				 			<tr class="data-row">
				 				<td class="bold">Weight</td>
				 				<td><?php print htmlspecialchars($track_display['packageWeight'],ENT_QUOTES,"UTF-8"). htmlspecialchars($track_display['uoMeasurement'],ENT_QUOTES,"UTF-8") ;?></td>
				 			</tr>
				 			<tr class="data-row">
				 				<td class="bold">Signed By</td>
				 				<td><?php

						 				if($track_display['activity_status_code'] =="D" || $track_display['activity_status_code'] == true )
						 					print htmlspecialchars($track_display['activity_signedfor'],ENT_QUOTES,"UTF-8");

						 				else
						 					print "N/A";
					 				?>
					 			</td>
				 			</tr>
				 		</tbody>

				</table>
			</div>

			<div class="tracking-details-wrapper">
				<table class="tracking-details">
					<thead>
					<tr class="data-row">
						<th colspan=2>Status Detail</th>
					</tr>
					</thead>
					<tbody>
				 	<tr  class="data-row">
				 		<td class="bold">Status</td>
				 		<td><?php print htmlspecialchars($track_display['stat'],ENT_QUOTES,"UTF-8"); ?></td>
				 	</tr>
				 	<tr  class="data-row">
				 		<td class="bold">Schedule Delivery Date</td>
				 		<?php if(empty($track_display['schedule_date'])){?>
				 		<td>N/A</td>
				 		<?php } else { ?>
				 		<td><?php print htmlspecialchars(date("F jS, Y",strtotime($track_display['schedule_date'])),ENT_QUOTES,"UTF-8"); ?></td>
				 		<?php } ?>
				 	</tr>
<?php
						if(!empty($track_display['detailed_activity'][0]['detailed_city'])){
?>
				 	<tr  class="data-row">
				 		<td class="bold">Current Location</td>
				 		<td><?php  echo trim($track_display['detailed_activity'][0]['detailed_city']). ", " . $track_display['detailed_activity'][0]['detailed_state']; ?></td>
				 	</tr>
<?php
					}
?>
				 	<tr rowspan=2  class="data-row">
				 		<td class="bold">Shipped To</td>
				 		<td>
						<?php

							$shipping_country=$objCountry->CountryCodeList($value['shipping_country']);

							print htmlspecialchars($value['shipping_first_name']. " ".$value['shipping_last_name'], ENT_QUOTES, "UTF-8")."<br/>";
							if($value['shipping_street_address']) print htmlspecialchars($value['shipping_street_address'],ENT_QUOTES,"UTF-8")."<br/>";
							if($value['shipping_suburb']) print htmlspecialchars($value['shipping_suburb'],ENT_QUOTES,"UTF-8")."<br/>";
							if(mb_strtolower($value['shipping_country']) == 'us' || mb_strtolower($value['shipping_country']) =='ca' ) {
				 				if($value['shipping_city']) print htmlspecialchars($value['shipping_city'],ENT_QUOTES,"UTF-8").", ";
							 	if($value['shipping_state']) print htmlspecialchars($value['shipping_state'],ENT_QUOTES,"UTF-8")." ";
								if($value['shipping_postcode']) print htmlspecialchars($value['shipping_postcode'],ENT_QUOTES,"UTF-8");
							}else{
								if($value['shipping_city']) print htmlspecialchars($value['shipping_city'],ENT_QUOTES,"UTF-8")." ";
								if($value['shipping_postcode']) print htmlspecialchars($value['shipping_postcode'],ENT_QUOTES,"UTF-8")."<br/>";
								if($shipping_country['countries_name']) print htmlspecialchars($shipping_country['countries_name'],ENT_QUOTES,"UTF-8");
							}

						?>
						</td>
				 	</tr>
				</tbody>
				</table>
			</div>

	<p class="h4 pad-left-10 clear">Shipment History</p>
<?php
	if(mb_strtolower($value['shipping_carrier']) =='fedex' && mb_strtolower($value['shipping_services'])!= 'smartpost'){
?>
	<p class="first-margin pad-right-10 prepend-top-5">Use <a href="http://www.fedex.com/us/delivery/" class="underline" target="_blank">FedEx Delivery Manager</a> to schedule delivery and sign up for text alerts. To register and learn more, visit <a href="http://www.fedex.com/us/delivery/" class="underline" target="_blank">FedEx.com</a>.</p>
<?php
	}
?>
	<table class="shipment-hist-table">

	<thead><tr>
			<th>Location</th>
			<th>Date</th>
			<th>Time</th>
			<th>Description</th>
		</tr></thead>
<tbody>
<?php
	for($i=0;$i<count($track_display['detailed_activity']);$i++){
?>
	<tr class="data-row">
		<td><?php print htmlspecialchars(isset($track_display['detailed_activity'][$i]['detailed_city']) ? $track_display['detailed_activity'][$i]['detailed_city'] : NULL, ENT_QUOTES,"UTF-8")." ". htmlspecialchars(isset($track_display['detailed_activity'][$i]['detailed_state']) ? $track_display['detailed_activity'][$i]['detailed_state'] : NULL, ENT_QUOTES,"UTF-8")." ". htmlspecialchars(isset($track_display['detailed_activity'][$i]['detailed_countyr']) ? $track_display['detailed_activity'][$i]['detailed_countyr'] : NULL, ENT_QUOTES,"UTF-8");?></td>
		<td><?php print htmlspecialchars(date("F jS, Y",strtotime($track_display['detailed_activity'][$i]['date'])), ENT_QUOTES,"UTF-8");?></td>
		<td><?php print htmlspecialchars(isset($track_display['detailed_activity'][$i]['time']) ? $track_display['detailed_activity'][$i]['time'] : NULL, ENT_QUOTES,"UTF-8");?></td>
		<td><?php print htmlspecialchars(isset($track_display['detailed_activity'][$i]['detailed_description']) ? $track_display['detailed_activity'][$i]['detailed_description'] : NULL, ENT_QUOTES,"UTF-8");?></td>
	</tr>
<?php
	}

	?>
</tbody></table>
</div>
<?php

}

