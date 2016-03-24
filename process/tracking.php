<?php
require "../include/config.php";

$track = new Page('tracking');

header("Location:". $track->getUrl(). '?' . http_build_query(array('orderno' => $_REQUEST['orderno'])));

