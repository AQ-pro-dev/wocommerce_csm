<?php 
function register_custom_post_types() {
        
    // Register Shipping Type
    register_post_type('shipping_type', array(
        'label' => 'Shipping Types',
        'public' => true,
        'supports' => array('title'),
    ));
    
    // Register Pickup Location
    register_post_type('store', array(
        'label' => 'Pickup Locations',
        'public' => true,
        'supports' => array('title','editor'),
    ));
}
add_action('init', 'register_custom_post_types');
