SafetySign.com (http://www.safetysign.com)

----------------------------------------------------------------------------

<?php echo (!empty($customerName) ? $customerName : $customerEmail) . (count($products) > 1 ? " shared some items" : " shared an item") . " from SafetySign.com with you." . PHP_EOL ; ?>

<?php echo (!empty($greeting) ?  $greeting  . PHP_EOL: ""); ?>

----------------------------------------------------------------------------

<?php echo $cartName. PHP_EOL; ?>

<?php foreach($products as $product): ?>

---------------------------------------------

<?php
	print 'Item #: ' . $product['sku_code'] . PHP_EOL;
	print 'Size: '.$product['size'] . PHP_EOL;
	print 'Material: '.stripslashes($product['material']) . PHP_EOL;
	foreach($product['attributes'] AS $key => $attribute):
		echo "" . $key . ": " . $attribute . PHP_EOL;
	endforeach;
?>
Qty: <?php echo $product['quantity'] . PHP_EOL; ?>
Each: $<?php echo number_format($product['price'], 2) . PHP_EOL; ?>
Price: $<?php echo number_format($product['total'], 2) . PHP_EOL; ?>
<?php endforeach; ?>

----------------------------------------------------------------------------

Subtotal: $<?php print number_format($subtotal,2) . PHP_EOL;?>


----------------------------------------------------------------------------

<?php if (!empty($cartNote)): ?>
Notes:
<?php echo $cartNote. PHP_EOL; ?>

<?php endif; ?>Production Time Table:
<?php echo (count($products) > 1 ? "These items " : "This item "); ?>usually ship from our warehouse in <?php echo $delay . ($delay > 1 ? ' business days.' : ' business day.') . PHP_EOL; ?>

----------------------------------------------------------------------------

Prices and quantities on this shopping cart are valid as of <?php echo $todayDate; ?>. All pricing and availability are subject to change. Subject to SafetySign.com's Terms and Conditions (<?php print $termsUrl;?>).

<?php echo (!empty($customerName) ? $customerName : $customerEmail); ?> requested that we send this e-mail. If you have questions about SafetySign.com, please visit our Customer Service Department (<?php echo $customerServiceUrl; ?>).

----------------------------------------------------------------------------

SAFETYSIGN.COM
----------------------------------
Brimar Industries
P.O. Box 467
64 Outwater Lane
Garfield, NJ 07026

Contact Customer Service
----------------------------------
Phone: 800-274-6271
Fax: 800-279-6897
E-mail: <?php print EMAIL_SERVICE."\n";?>

Hours of Operation
----------------------------------
9am - 5pm Eastern
Monday - Friday