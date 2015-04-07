<?php

use fieldwork\FW;

function loadWordpressCore ()
{
    foreach (array(
                 __DIR__ . '/../wp/wp-blog-header.php',
                 __DIR__ . '/../web/wp/wp-blog-header.php',
                 __DIR__ . '/../../../wp-blog-header.php',
                 __DIR__ . '/../../../wp/wp-blog-header.php'
             ) as $wpLocation)
        if (file_exists($wpLocation)) {
            define('DOING_AJAX', true);
            define('WP_USE_THEMES', false);
            require($wpLocation);
            header("HTTP/1.1 200 OK");
            return true;
        }
    return false;
}

if (!loadWordpressCore()) {
    die('The WordPress core could not be located');
}

$response = FW::handleAjaxRequest();

echo json_encode($response);