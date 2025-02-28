<?php 
/*
    Plugin Name: Woocommerce CSM
    Description: Adds Woocommerce custom shipping methods to WooCommerce and allows for dynamic shipping costs based on selected Pickup Location.
    Version: 1.0
    Author: Abdul Qadeer
*/

if (!defined('ABSPATH')) {
    exit;
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$cpts = plugin_dir_path( __FILE__ ) .'includes/cpts.php';
if ( file_exists( $cpts ) ) {
    require ($cpts);
}
$checkout_fields = plugin_dir_path( __FILE__ ) .'includes/checkout_fields.php';
if ( file_exists( $checkout_fields ) ) {
    require ($checkout_fields);
}
$ajaxHandeling = plugin_dir_path( __FILE__ ) .'includes/ajaxHandeling.php';
if ( file_exists( $ajaxHandeling ) ) {
    require ($ajaxHandeling);
}
$shipping_cost_calculation = plugin_dir_path( __FILE__ ) .'includes/shipping_cost_calculation.php';
if ( file_exists( $shipping_cost_calculation ) ) {
    require ($shipping_cost_calculation);
}
$save_shipping_cost = plugin_dir_path( __FILE__ ) .'includes/save_shipping_cost.php';
if ( file_exists( $save_shipping_cost ) ) {
    require ($save_shipping_cost);
}

$checkout_fields_validate = plugin_dir_path( __FILE__ ) .'includes/checkout_fields_validate.php';
if ( file_exists( $checkout_fields_validate ) ) {
    require ($checkout_fields_validate);
}
//in_person_payment
$in_person_payment = plugin_dir_path( __FILE__ ) .'includes/in_person_payment.php';
if ( file_exists( $in_person_payment ) ) {
    require ($in_person_payment);
}

$contact_for_payment = plugin_dir_path( __FILE__ ) .'includes/contact_for_payment.php';
if ( file_exists( $contact_for_payment ) ) {
    require ($contact_for_payment);
}

$csm_functions = plugin_dir_path( __FILE__ ) .'includes/csm_functions.php';
if ( file_exists( $csm_functions ) ) {
    require ($csm_functions);
}


function custom_shipping_scripts() {
    wp_enqueue_style( 'style-csm', plugin_dir_url(__FILE__) . 'assets/css/style.css', false, '1.0', 'all' ); // Inside a parent theme
    wp_enqueue_script('custom-shipping', plugin_dir_url(__FILE__) . 'assets/js/csm_script.js', array('jquery'), null, true);
    wp_localize_script('custom-shipping', 'wcheckout_params', array('ajax_url' => admin_url('admin-ajax.php')));
}

add_action('wp_enqueue_scripts', 'custom_shipping_scripts');

