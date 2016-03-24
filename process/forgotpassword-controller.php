<?php

//Handle reset form submission
if( isset($_POST['signinsubmit']) && $_POST['signinsubmit'] == 'Reset My Password' ) {
    $objUser->forgotPassword($_POST['username'], $ObjEmail);
}
//Handle new password form submission
if( isset($_POST['newpasswordsubmit']) && $_POST['newpasswordsubmit'] == 'Reset Password' ) {
    $reset = $objUser->resetPassword($_POST['pass1'], $_POST['pass2'], $_POST['confirm']);
}

//$forgotPasswordPage = new Page('forgotpassword');

//If the user has a cookie, retrieve their username
if( isset($_COOKIE['credentials']) && $_COOKIE['credentials'] != '' ) {
    $username = $objUser->getUsernameFromCookie($_COOKIE['credentials']);
}

$forgotPasswordPage = new Page('forgotpassword');
$objOrder = new Orders();
$order = $objOrder->getCustomerOrder($orderno);

$templateInfo = array (
    'objUser'                   => $objUser,
    'ObjSession'                => $ObjSession,
    'ObjEmail'                  => $ObjEmail,
    'forgotPasswordPage'        => $forgotPasswordPage,
    'Path_Templates_Base'       => $Path_Templates_Base,
    'page'                      => $page,
    'order'                     => $order,
    'objOrder'                  => $objOrder,
    'chatStatus'                => $chatStatus,
    'breadcrumbs'               => $page->getBreadCrumbs(),
    'result'                    => $result,
    'URL_PREFIX'                => URL_PREFIX,
    'URL_PREFIX_HTTP'           => URL_PREFIX_HTTP,
    'URL_PREFIX_HTTPS'          => URL_PREFIX_HTTPS,
    'ENT_QUOTES'                => ENT_QUOTES,
    'PAGE_TYPE'                 => PAGE_TYPE,
    'PAGE_ID'                   => PAGE_ID,
    'HTML_COMMENTS'             => HTML_COMMENTS,
    'NEXTOPIA_PUBLIC_ID'        => NEXTOPIA_PUBLIC_ID,
    'SHOPPER_APPROVED_SITE_ID'  => SHOPPER_APPROVED_SITE_ID,
    'IMAGE_URL_PREFIX'          => IMAGE_URL_PREFIX,
    'CUSTOM_IMAGE_URL_PREFIX'   => CUSTOM_IMAGE_URL_PREFIX,
    'website'                   => website,
    'links'                     => $links,
    'ObjShoppingCart'           => $ObjShoppingCart,
    'ObjMenu'                   => $ObjMenu,
    'ObjPageProduct'            => $ObjPageProduct,
    'FrontEndTemplateIncluder'  => $FrontEndTemplateIncluder
);

if( !empty($resultsOfGlobalController) ) {

    foreach ($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string) $name] = $value;
    }
}
echo Template::generate('signin-sec/forgotpassword-template', $templateInfo);

//include_once($PathTemplates."head.php");
//include_once($PathTemplates."header.php");
//include_once($PathTemplates."openwrap.php");
//include($Path_Templates_Signin."forgotpassword.php");
//include_once($PathTemplates."closewrap.php");
//include_once($PathTemplates."header-content.php");
//include_once($PathTemplates."footer.php");
//include_once($PathTemplates."foot.php");