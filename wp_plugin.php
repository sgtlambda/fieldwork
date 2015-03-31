<?php
/*
  Plugin Name: Fieldwork
  Description: WordPress plugin wrapper for the Fieldwork library
  Version: 4.2.0
  Author: JM Versteeg
 */

function wp_sanity_checks ()
{
    if (!file_exists(__DIR__ . '/vendor')) {
        throw new Exception('vendor directory not found in ' . __DIR__ . '. Did you forget to run composer install?');
    }
}

try {
    wp_sanity_checks();
} catch (Exception $e) {
    die($e->getMessage());
}

require 'vendor/autoload.php';

$loader = new \Aura\Autoload\Loader();
$loader->addPrefix('fieldwork', __DIR__ . '/src/fieldwork');
$loader->register();

function fieldworkEnqueueScripts ()
{
    $deps = array(
        'jquery',
    );

    wp_enqueue_script('fieldwork', plugins_url('dist/fieldwork.min.js', __FILE__), $deps);
    wp_enqueue_style('fieldwork', plugins_url('dist/fieldwork.css', __FILE__));
}

add_action('wp_enqueue_scripts', 'fieldworkEnqueueScripts');
add_action('admin_enqueue_scripts', 'fieldworkEnqueueScripts');

add_action('plugins_loaded', function () {

    do_action('fieldwork-register-forms');

});