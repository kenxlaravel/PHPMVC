<?php
require("../include/config.php");
require ("global-controller.php");
$stateParameters = ProductStateParameter::encode($resultsOfGlobalController['ObjShoppingCart']->products[$_REQUEST['id']]->stateParameters);
// Form the edit URL
$url = $resultsOfGlobalController['ObjShoppingCart']->products[$_REQUEST['id']]->productLink . '?cpi=' . $resultsOfGlobalController['ObjShoppingCart']->products[$_REQUEST['id']]->customImage['customImageId']. '&s=' .$stateParameters;

// Remove the product from the cart (this will be added again once the design is resaved)
$resultsOfGlobalController['ObjShoppingCart']->removeProduct($_REQUEST['id']);

// Redirect to the editor
header("Location: " . $url);