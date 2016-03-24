<?php


require("../include/config.php");
require ("global-controller.php");
//dummy date - proper functions must be used here - just for show currently
$sku_code = (!empty($_REQUEST['sku_code']) ? $_REQUEST['sku_code'] : NULL);
$ObjSku = Sku::create(14704);
$currentTime = date("m/d/Y");
$delay = 1;
$productionDelay = Settings::getSettingValue('productiondelay');
$customProductDelay = Settings::getSettingValue('customproductdelay');
$freightShipmentDelay = Settings::getSettingValue('freightdelay');

if ($ObjSku->isInStock()) {
    $shipDate = date("m/d/Y", strtotime($currentTime . ' + ' . ($delay + $productionDelay) . ' days'));
} else {

    if ($ObjSku->getRequiresFreight()) {
        $shipDate = date("m/d/y", strtotime($currentTime . ' + ' . ($delay + $productionDelay + $customProductDelay + $freightShipmentDelay) . ' days'));
    } elseif ($ObjSku->getLeadTime() > 0) {
        $shipDate = date("m/d/y", strtotime($currentTime . ' + ' . ($delay + $productionDelay + $customProductDelay + $ObjSku->getLeadTime()) . ' days'));
    } else {
        $shipDate = date("m/d/y", strtotime($currentTime . ' + ' . ($delay + $productionDelay + $customProductDelay) . ' days'));
    }

}

// todo add additional logic for if shipdate is holiday or weekend

echo $shipDate;

?>
