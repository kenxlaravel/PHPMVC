<?php

$order = $objOrder->getCustomerOrder($orderno);

if( isset($_POST['formaction']) ) {

    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $pass1 = strip_tags(trim($_POST['pass1']));
    $pass2 = isset($_POST['pass2']) ? strip_tags(trim($_POST['pass2'])) : NULL;
    $remember = isset($_POST['remember']) && $_POST['remember'] == 'Y' ? 'Y' : NULL;

    //If the user is returning, try to validate them
    if( $_POST['formaction'] == 'returning' ) {

        $objUser->userLogin($email, $pass1, $remember, $ObjSession);
    }

    //If the user is new, register them
    if( $_POST['formaction'] == 'new' ) {
        $objUser->userRegister($email, $pass1, $pass2, $ObjEmail, $ObjSession);
    }

    //If the user is a guest, give them a temporary account
    if( $_POST['formaction'] == 'guest' ) {
        $objUser->userGuest($email);
    }
}

$forgotPasswordPage = new Page('forgotpassword');

//If the user has a cookie, retrieve their username
if( isset($_COOKIE['credentials']) && $_COOKIE['credentials'] != '' ) {
    $username = $objUser->getUsernameFromCookie($_COOKIE['credentials']);
}

$objOrder = new Orders();
$order = $objOrder->getCustomerOrder($orderno);

$templateInfo = array (
    'page'                          => $page,
    'objUser'                       => $objUser,
    'order'                         => $order,
    'objOrder'                      => $objOrder,
    'chatStatus'                    => $chatStatus,
    'breadcrumbs'                   => $page->getBreadCrumbs(),
    'result'                        => $result,
    'URL_PREFIX'                    => URL_PREFIX,
    'URL_PREFIX_HTTP'               => URL_PREFIX_HTTP,
    'URL_PREFIX_HTTPS'              => URL_PREFIX_HTTPS,
    'ENT_QUOTES'                    => ENT_QUOTES,
    'PAGE_TYPE'                     => PAGE_TYPE,
    'PAGE_ID'                       => PAGE_ID,
    'HTML_COMMENTS'                 => HTML_COMMENTS,
    'NEXTOPIA_PUBLIC_ID'            => NEXTOPIA_PUBLIC_ID,
    'SHOPPER_APPROVED_SITE_ID'      => SHOPPER_APPROVED_SITE_ID,
    'IMAGE_URL_PREFIX'              => IMAGE_URL_PREFIX,
    'CUSTOM_IMAGE_URL_PREFIX'       => CUSTOM_IMAGE_URL_PREFIX,
    'website'                       => website,
    'links'                         => $links,
    'ObjShoppingCart'               => $ObjShoppingCart,
    'ObjMenu'                       => $ObjMenu,
    'ObjPageProduct'                => $ObjPageProduct,
    'ObjSession'                    => $ObjSession,
    'ObjEmail'                      => $ObjEmail,
    'forgotPasswordPage'            => $forgotPasswordPage,
    'FrontEndTemplateIncluder'      => $FrontEndTemplateIncluder
);

if( !empty($resultsOfGlobalController) ) {

    foreach ($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string) $name] = $value;
    }
}
echo Template::generate('signin-sec/signin-template', $templateInfo);