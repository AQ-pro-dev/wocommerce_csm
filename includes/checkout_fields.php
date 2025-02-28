<?php
// Remove required status from the ZIP/postcode field in WooCommerce checkout
add_filter('woocommerce_default_address_fields', 'remove_checkout_postcode_field');

function remove_checkout_postcode_field($address_fields) {
    // Remove the required attribute
    $address_fields['postcode']['required'] = false;

    // Optionally, hide the field completely
    // $address_fields['postcode']['class'] = array('hidden');
    // $address_fields['postcode']['label'] = false;

    return $address_fields;
}

add_action('woocommerce_checkout_fields', 'custom_shipping_fields');

function custom_shipping_fields($fields) {

    // // Remove billing state
    // unset($fields['billing']['billing_state']);
    // // Remove shipping state
    // unset($fields['shipping']['shipping_state']);    
    
    return $fields;
    
}

// function get_province_options() {
//     //$provinces = get_posts(array('post_type' => 'province', 'numberposts' => -1));
//     // Get the customer's shipping state
//     $chosen_state = WC()->customer->get_shipping_state();

//     // Query to get provinces where state_key matches the chosen state
//     $args = array(
//         'post_type'  => 'province',
//         'numberposts' => -1,
//         'meta_query' => array(
//             array(
//                 'key'     => 'state_key',
//                 'value'   => $chosen_state,
//                 'compare' => '='
//             )
//         )
//     );

//     $provinces = get_posts($args);
//     $options = array('' => __('Select a province'));
    
//     foreach ($provinces as $province) {
//         $options[$province->ID] = $province->post_title;
//     }
    
//     return $options;
// }


add_action('woocommerce_after_order_notes', 'custom_shipping_data');

function custom_shipping_data($checkout) {
    echo '<div id="custom_shipping_fields"><h3>' . __('Información de envío') . '</h3>';
    
    // Province Dropdown
    // woocommerce_form_field('provincefield', array(
    //     'type' => 'select',
    //     'class' => array('form-row-wide'),
    //     'label' => __('Province'),
    //     'required'    => true,
    //     'clear'       => true,
    //     'priority'    => 35,
    //     'options' => get_province_options(),
    // ), $checkout->get_value('province'));
    
    // Municipality Dropdown (Initially empty, will be populated via AJAX)
    // woocommerce_form_field('municipalityfield', array(
    //     'type' => 'select',
    //     'class' => array('form-row-wide'),
    //     'label' => __('Municipality'),
    //     'required'    => true,
    //     'clear'       => true,
    //     'priority'    => 35,
    //     'options' => array('' => __('Select a province first')),
    // ), $checkout->get_value('municipality'));
    
    // Municipal District Dropdown (Initially empty, will be populated via AJAX)
    // woocommerce_form_field('districtfield', array(
    //     'type' => 'select',
    //     'class' => array('form-row-wide'),
    //     'label' => __('Municipal District'),
    //     'required'    => true,
    //     'clear'       => true,
    //     'priority'    => 35,
    //     'options' => array('' => __('Select a municipality first')),
    // ), $checkout->get_value('district'));
    
    // Shipping Type Dropdown (Initially empty, will be populated via AJAX)
    woocommerce_form_field('shipping_typefield', array(
        'type' => 'select',
        'class' => array('form-row-wide'),
        'label' => __('Servicio de mensajería'),
        'required'    => true,
        'clear'       => true,
        'priority'    => 35,
        'options' => array('' => __('Seleccione una provincia primero')),
    ), $checkout->get_value('shipping_type'));
    
    // Pickup Location Dropdown (Initially empty, will be populated via AJAX)
    woocommerce_form_field('storelocation', array(
        'type' => 'select',
        'class' => array('form-row-wide'),
        'label' => __('Ubicación de recogida'),
        'required'    => true,
        'clear'       => true,
        'priority'    => 35,
        'options' => array('' => __('Seleccione un servicio de mensajería')),
    ), $checkout->get_value('store'));
    
    
    echo '<div id="store_info">
            <h3>' . __('Store details') . '</h3>    
            <p id="shipping_cost">Costo de envío: </p>
            <p id="store_location">Ubicación de recogida: </p>
            <p id="store_phone">Teléfono de la tienda: </p>
          </div>
          <input type="hidden" id="provincefield" name="provincefield" value="">
          <input type="hidden" id="shipping_cost_hidden" name="shipping_cost_hidden" value="">';          
    echo '</div>';
}
//////////////////////////////////////////////////////////////////////////////

