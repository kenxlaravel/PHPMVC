<?php
if($google_response){
	echo $google_response;
}
else{
?>
<div id="google-wallet-wrapper">

	<p class="h3 h3-rev pad-left-10">Google Wallet: Shipping ZIP Code</p>
	<?php
		$link=new Page('gcart');
		$url=new Page('cart');
	?>

	<div class="pad-left-10 prepend-top google-wallet-inner">

		<p class="h4">Please enter your US shipping ZIP code to continue.</p>
		<form accept-charset="utf-8" action="<?php print $link->getUrl;?>" method="post" name="cartups" id="shippingrate">
			<input type="text" name="zip" value="<?php print htmlspecialchars($_SESSION['zip'],ENT_QUOTES,"UTF-8");?>" maxlength="11" class="text zip-input" />
			<input type="submit" value="Continue To Google Wallet" class="button orange" />
		</form>
		<?php

			if($error_count==1){
			  print '<p class="span-9 error">Please enter a valid US ZIP code.</p> ';
			}
		 ?>
		<p class="prepend-top">Shipping options and sales tax will be displayed in Google Wallet.</p>
		<p class="append-bottom">Google Wallet is currently available for US customers and orders only.</p>

	</div>

	<div class="pad-left-10 clear prepend-top top-space">
		<p class="left-side last-margin top-space">Outside the US or prefer to use our checkout?</p>
		<a href="<?php print $url->getUrl(); ?>" class="button">Return To Shopping Cart</a>
	</div>

</div>
<?php
}