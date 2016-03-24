<?php
// Load configuration.
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/config.php';

// Parse the URL.
$url = parse_url($_SERVER['REQUEST_URI']);

$path = trim($url['path'], '/');

$querystring = empty($url['query']) ? '' : ( '?' . $url['query'] );

// If not already set (through legacyproduct.php, for instance), attempt to find the page type and ID from the URL.
if ( !isset($pagetype, $pageid) ) {

	foreach (array(

	     'category'    => '/^categories\\/c([0-9]+)(\\/[^\\/.]+)?$/',
	     'grouping'    => '/^categories\\/g([0-9]+)(\\/[^\\/.]+)?$/',
	     'subcategory' => '/^categories\\/s([0-9]+)(\\/[^\\/.]+)?$/',
	     'landing'     => '/^categories\\/l([0-9]+)(\\/[^\\/.]+)?$/',
	     'product'     => '/^products\\/([0-9]+)(\\/[^\\/.]+)?$/',
	     'help'        => '/^help\\/h([0-9]+)(\\/[^\\/.]+)?$/',
	     'geotarget'   => '/^categories\\/t([0-9]+)(\\/[^\\/.]+)?$/'

	) as $type         => $pattern) {

		$matches = array();

		if ( preg_match($pattern, $path, $matches) === 1 ) {

			$pagetype = $type;
			$pageid   = (int) $matches[1];
		}
	}

	// If the page type and ID still haven't been found, do a lookup in bs_page_urls and bs_pages.
	if ( !isset($pagetype, $pageid) ) {

		$sth = Connection::getHandle()->prepare(
                        'SELECT * FROM ( ( SELECT pagetype AS pagetype, pageid AS pageid FROM
                         bs_page_urls WHERE url = :full_url OR url = :url ORDER BY url = :full_url_order DESC, url = :url_order DESC ) UNION ALL (
                         SELECT \'page\' AS pagetype, id AS pageid FROM bs_pages WHERE filename = :filename ) ) results LIMIT 1');

        $fullUrl = $path.$querystring;

        $sth->bindParam("full_url", $fullUrl, PDO::PARAM_STR);
        $sth->bindParam("full_url_order", $fullUrl, PDO::PARAM_STR);
        $sth->bindParam("url", $path, PDO::PARAM_STR);
        $sth->bindParam("url_order", $path, PDO::PARAM_STR);
        $sth->bindParam("filename", $path, PDO::PARAM_STR);

		if( $sth->execute () ) {

            $result = $sth->fetch(PDO::FETCH_ASSOC);
            $pagetype = trim($result['pagetype']);
            $pageid = (int) $result['pageid'];
        }
	}
}

// Prepare the page.
define('PAGE_TYPE', $pagetype);
define('PAGE_ID', 	$pageid);

// Instantiate the page
$page = Page::getPageByTypeAndId(PAGE_TYPE, PAGE_ID);

// Serve the content.
if ( !$page->getValidity() ) {

    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
	include APP_ROOT . '/404.php';
	exit;

} elseif ( !$page->checkUrl() ) {


	header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently', true, 301);
	header('Location: ' . $page->getUrl() . $querystring);

	exit;

} else {

	//	if ( PAGE_TYPE === "product" || PAGE_TYPE === "home") {

	$controllerName = $page->getType() === 'page' ? $page->getNickname() : $page->getType();

	Controller::make($controllerName, array('page' => $page));

}