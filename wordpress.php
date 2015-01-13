<?php

/*
  Plugin Name: JannieForms
  Description: WordPress plugin wrapper for the JannieForms library
  Version: 3.0.0
  Author: JM Versteeg
 */

require __DIR__ . '/autoload.php';

add_action('wp_enqueue_scripts', function () {
    $vendorPath = plugins_url('bower_components/', __FILE__);

    wp_register_script('underscore', $vendorPath . 'underscore/underscore-min.js', '1.7.0');
    wp_register_script('jquery-maskedinputs', $vendorPath . 'jquery-maskedinputs/dist/jquery.maskedinputs.min.js', array('jquery'), '1.4.0');
    wp_register_script('datetimepicker', $vendorPath . 'datetimepicker/jquery.datetimepicker.js', array('jquery'), '2.4.1');
    wp_register_script('sweetalert', $vendorPath . 'sweetalert/lib/sweet-alert.min.js', array(), '0.3.3');

    $deps = array(
        'jquery',
        'jannietooltips',
        'jquery-maskedinputs',
        'underscore',
        'datetimepicker',
        'sweet-alert'
    );

    wp_register_script('jannieforms', plugins_url('dist/jannieforms.js', __FILE__), $deps, '3.0', true);

    wp_enqueue_script('jannieforms');
    wp_enqueue_style('jannieforms', plugins_url('dist/jannieforms.css', __FILE__));
});

add_action('plugins_loaded', function () {

    do_action('jannieforms-register-forms');

});