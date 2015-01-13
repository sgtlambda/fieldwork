<?php
/*
  Plugin Name: JannieForms
  Description: WordPress plugin wrapper for the JannieForms library
  Version: 3.0.0
  Author: JM Versteeg
 */

require __DIR__ . '/autoload.php';

add_action('wp_enqueue_scripts', function () {
    $bower_components = plugins_url('bower_components/', __FILE__);

    wp_register_script('jquery-maskedinputs', $bower_components . 'jquery-maskedinputs/dist/jquery.maskedinput.min.js', array('jquery'), '1.4.0');
    wp_register_script('underscore', $bower_components . 'underscore/underscore-min.js', '1.7.0');
    wp_register_script('datetimepicker', $bower_components . 'datetimepicker/jquery.datetimepicker.js', array('jquery'), '2.4.1');
    wp_register_script('sweetalert', $bower_components . 'sweetalert/lib/sweet-alert.min.js', array(), '0.3.3');

    $deps = array(
        'jquery',
        'jquery-maskedinputs',
        'underscore',
        'datetimepicker',
        'sweetalert'
    );

    wp_enqueue_script('jannieforms', plugins_url('dist/jannieforms.min.js', __FILE__), $deps);
    wp_enqueue_style('jannieforms', plugins_url('dist/jannieforms.css', __FILE__));
});

add_action('plugins_loaded', function () {

    do_action('jannieforms-register-forms');

});