<?php

use jannieforms\FormResults;
use jannieforms\JF;

define('WP_USE_THEMES', false);

$wpLocations = array(
    __DIR__ . '/../../../wp-blog-header.php',
    __DIR__ . '/../../../wp/wp-blog-header.php'
);
foreach ($wpLocations as $wpLocation) {
    if (file_exists($wpLocation)) {
        require($wpLocation);
        break;
    }
}

header("HTTP/1.1 200 OK");

$data   = new FormResults($_POST);
$method = JF::getAjaxMethod($_GET['method']);

$response = $method->run($data);

echo json_encode($response);