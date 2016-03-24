Your SafetySign.com order has shipped!
---------------------------------------------------------------------------------------------------------------------------

Order No: <?=$orderno.PHP_EOL?>
Shipped Date: <?=$shipdate.PHP_EOL?>
Shipping Method: <?=$shipping_carrier?> <?=$shipmethod.PHP_EOL?>
<? if ( !empty($shipping_account) ) : ?>
Shipping Account: <?=$shipping_account.PHP_EOL?>
<? endif; ?>
Status: <?=$status.PHP_EOL?>
<? if ( mb_strtolower($shipping_carrier) == 'ups' ) : ?>
UPS Tracking Number: <?=$trackingnumber.PHP_EOL?>
<? elseif ( mb_strtolower($shipping_carrier) == 'fedex' ) : ?>
FedEx Tracking Number #<?=$trackingnumber.PHP_EOL;?>
<? endif; ?>

---------------------------------------------------------------------------------------------------------------------------

You can track the status of this order by going to: <?=$track_url?>

<? if ( mb_strtolower($value['shipping_carrier']) =='fedex' && mb_strtolower($value['shipping_services'])!= 'smartpost' ) : ?>
To schedule delivery and sign up for text alerts visit http://www.fedex.com/us/delivery/.
<? endif; ?>

---------------------------------------------------------------------------------------------------------------------------
Ship To Address
---------------------------------------------------------------------------------------------------------------------------
<?=$shipaddress['ship_name'].PHP_EOL?>
<? if ( !empty($shipaddress['shipping_company']) ) : ?>
<?=$shipaddress['shipping_company'].PHP_EOL?>
<? endif; ?>
<?=$shipaddress['shipping_street_address'].PHP_EOL?>
<? if ( !empty($shipaddress['shipping_suburb']) ) : ?>
<?=$shipaddress['shipping_suburb'].PHP_EOL?>
<? endif; ?>
<? if ( mb_strtolower($shipaddress['shipping_country_code']) == 'us' || mb_strtolower($shipaddress['shipping_country_code']) == 'ca' ) : ?>
<?=$shipaddress['shipping_city']?>, <?=$shipaddress['shipping_state']?> <?=$shipaddress['shipping_postcode'].PHP_EOL?>
<? else : ?>
<?=$shipaddress['shipping_city']?> <?=$shipaddress['shipping_postcode'].PHP_EOL?>
<? endif; ?>

<?=$shipaddress['shipping_country'].PHP_EOL?>
<?=$shipaddress['shipping_phone'].PHP_EOL?>
<? if ( !empty($shipaddress['shipping_fax']) ) : ?>
<?=$shipaddress['shipping_fax'].PHP_EOL?>
<? endif; ?>

---------------------------------------------------------------------------------------------------------------------------
Bill To Address
---------------------------------------------------------------------------------------------------------------------------
<?=$billaddress['bill_name'].PHP_EOL?>
<? if ( !empty($billaddress['billing_company']) ) : ?>
<?=$billaddress['billing_company'].PHP_EOL?>
<? endif; ?>
<?=$billaddress['billing_street_address'].PHP_EOL?>
<? if ( !empty($billaddress['billing_suburb']) ) : ?>
<?=$billaddress['billing_suburb'].PHP_EOL?>
<? endif; ?>

<? if ( mb_strtolower($billaddress['billing_country_code']) == 'us' || mb_strtolower($billaddress['billing_country_code']) == 'ca' ) : ?>
<?=$billaddress['billing_city']?>, <?=$billaddress['billing_state']?> <?=$billaddress['billing_postcode'].PHP_EOL?>
<? else : ?>
<?=$billaddress['billing_city']?> <?=$billaddress['billing_postcode'].PHP_EOL?>
<? endif; ?>

<?=$billaddress['billing_country'].PHP_EOL?>
<?=$billaddress['billing_phone'].PHP_EOL?>
<?=$customner_email.PHP_EOL?>

---------------------------------------------------------------------------------------------------------------------------
Payment Info
---------------------------------------------------------------------------------------------------------------------------
Payment Type: <?=$paymentinfo['cardtype'].PHP_EOL?>
<? if ( mb_strtolower($paymentinfo['cardtype']) != 'paypal' ) : ?>
<?=( mb_strtolower($paymentinfo['cardtype']) == 'credit' ? 'Card No' : 'Account Number' )?>: **********<?=$paymentinfo['cardNum'].PHP_EOL?>
<? endif; ?>
<? if ( mb_strtolower($paymentinfo['cardtype']) == 'credit' ) : ?>
Card Expiration: <?=$paymentinfo['expiration'].PHP_EOL?>
<? endif; ?>
Transaction: Approved
Amount Charged: <?=$paymentinfo['total_amount'].PHP_EOL?>
---------------------------------------------------------------------------------------------------------------------------
<? foreach ( $cart as $key => $value ) : ?>

<? if ( $value['sku_code'] ) : ?>
Item #<?=$value['sku_code'].PHP_EOL?>
<? endif; ?>
<? foreach ( $value as $cart_key => $cvalue ) : ?>
<? if ( $cart_key == 'size' ) : ?>Description & Size: <?=$cvalue.PHP_EOL?><? endif; ?>
<? if ( $cart_key == 'material' ) : ?><?=$cvalue.PHP_EOL?><? endif; ?>
<? if ( $cart_key == 'sale_percentage' && !empty($cvalue) ) : ?>You saved <?=$cvalue?>%<?=PHP_EOL?><? endif; ?>
<? if ( $cart_key == 'attribute' && !empty($cvalue) ) : ?>
<? foreach ( $cvalue as $sub_key => $att_value ) : ?>
<?=$sub_key?>: <?=$att_value.PHP_EOL?>
<? endforeach; ?>
<? endif; ?>
<? if ( $cart_key == 'builder_attributes' && !empty($cvalue) ) : ?>
<? foreach ( $cvalue as $subkey => $builder ) : ?>
<? if ( $builder['builder_setting_display'] == 'Y' && in_array($builder['builder_subsetting'], array('mountingoptions', 'antigraffiti', 'scheme', 'layout', 'text', 'artwork')) ) : ?>
<?=$builder['builder_label']?>: <?=$builder['builder_value_text'].PHP_EOL?>
<? endif; ?>
<? if ( $builder['builder_subsetting'] == 'upload') : ?>
<?=$builder['builder_label']?>: <?=$builder['upload_name'].PHP_EOL?>
<? endif; ?>
<? endforeach; ?>
<? endif; ?>
<? if ( $cart_key=='design_service' && $value['stock_custom'] == 'C' ) : ?>
Design Adjustment: <?=( $cvalue == TRUE ? 'We will adjust your design for best appearance.' : 'We will print your design as shown.' ).PHP_EOL?>
<? endif; ?>
<? if ( ($cart_key == 'comment' || $cart_key == 'builder_comment') && !empty($cvalue) ) : ?>
Instructions: <?=$cvalue.PHP_EOL?>
<? endif; ?>
<? endforeach; ?>
<? if ( $value['quantity'] ) : ?>Quantity: <?=$value['quantity'].PHP_EOL?><? endif; ?>
<? if ( $value['price'] ) : ?>Price: $<?=$value['price'].PHP_EOL?><? endif; ?>
<? if ( $value['total'] ) : ?>Total: $<?=$value['total'].PHP_EOL?><? endif; ?>

---------------------------------------------------------------------------------------------------------------------------

<? endforeach; ?>
---------------------------------------------------------------------------------------------------------------------------

Order Subtotal: $<?=$subtotal.PHP_EOL?>
Shipping Charge: $<?=$shippingcharge.PHP_EOL?>
Sales Tax: $<?=$salestax.PHP_EOL?>
Invoice Total: $<?=$invoicetotal.PHP_EOL?>

<? if ( $comments != '' ) : ?>
Your Comments: <?=$comments.PHP_EOL?>
<? endif; ?>

---------------------------------------------------------------------------------------------------------------------------

Thank you and please come visit us again at www.SafetySign.com

Please check this order for accuracy and contact us immediately if any information is incorrect.

Note: Because orders are processed immediately, we are unable to accomodate order changes or cancellations; erroneously ordered items must be returned after delivery.

You can access the following information on our website:

<? if ( !$guest ) : ?>
My Account - Change any of your personal information
<?=$account_url.PHP_EOL?>
<? endif; ?>

Order Tracking - Check online the status of your order.
<?=$track_url.PHP_EOL?>

----------------------------------------------------------------------------

We thank you for your business and welcome questions or comments.

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
E-mail: <?=EMAIL_SERVICE.PHP_EOL?>

Hours of Operation
----------------------------------
9am - 5pm Eastern
Monday - Friday
