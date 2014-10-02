<?php

define('WP_USE_THEMES', false);
require('../../../wp-blog-header.php');
header("HTTP/1.1 200 OK");

$data = new JannieFormResults($_POST);
$method = JannieForms::getAjaxMethod($_GET['method']);

$response = $method->run($data);

echo json_encode($response);