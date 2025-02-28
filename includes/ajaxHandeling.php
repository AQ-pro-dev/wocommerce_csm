<?php
//////////////////////////// Payment Method ////////////////////////////////

add_action('wp_ajax_update_payment_session', 'update_payment_session');
add_action('wp_ajax_nopriv_update_payment_session', 'update_payment_session');

function update_payment_session() {
    if (isset($_POST['payment_method'])) {
        $payment_method = sanitize_text_field($_POST['payment_method']);
        if ($payment_method === 'contact_for_payment') {
            WC()->session->set('remove_custom_shipping_cost', false);
        } else if($payment_method === 'in_person') {
            WC()->session->set('remove_custom_shipping_cost', true);
        }
    }
    
    wp_die();
}



//////////////////////////////// Municipality ////////////////////////////////

add_action('wp_ajax_get_shipping_type', 'get_shipping_type');
add_action('wp_ajax_nopriv_get_shipping_type', 'get_shipping_type');

function get_shipping_type() {

        // Retrieve and sanitize the province ID from the POST request
        $province_id = $_POST['province_id'];
        if (empty($province_id)) {
            echo json_encode(array('error' => 'ID de provincia no válido'));
            exit;
        }

        // Prepare the query arguments to get the province post
        $args = array(
            'post_type'  => 'province',
            'numberposts' => -1,
            'meta_query' => array(
                array(
                    'key'     => 'state_key',
                    'value'   => $province_id,
                    'compare' => '='
                )
            )
        );

        // Fetch the province posts
        $provinces = get_posts($args);
        //echo '<pre>';print_r($province_id);exit;
        if (empty($provinces)) {
            echo json_encode(array('error' => 'No se encontraron provincias'));
            exit;
        }

        // Extract the first province ID
        $province_ID = $provinces[0]->ID;

        // Retrieve the municipalities associated with the province
        $courierServices = get_field('select_shipping_types', $province_ID);
        if (empty($courierServices)) {
            echo json_encode(array('error' => 'No se encontró ningún servicio de mensajería'));
            exit;
        }

        // Prepare the options array
        $options = array();
        foreach ($courierServices as $courierService) {
            $options[$courierService->ID] = $courierService->post_title;
        }

        // Prepare the response array
        $response_array = array(
            'province' => $province_ID,
            'options' => $options
        );

        // Return the response as JSON
        echo json_encode($response_array);
        exit;

}


//////////////////////////////// Municipality District ////////////////////////////////

// add_action('wp_ajax_get_district', 'get_district');
// add_action('wp_ajax_nopriv_get_district', 'get_district');

// function get_district() {
//     $municipality_id = intval($_POST['municipality_id']);

//     $districts = get_field('select_district',$municipality_id);
    
//     $options = array();
    
//     foreach ($districts as $district) {
//         $options[$district->ID] = $district->post_title;
//     }
    
//     print(json_encode($options));
//     exit;;
// }

//////////////////////////////// Shipping Type ////////////////////////////////

// add_action('wp_ajax_get_shipping_type', 'get_shipping_type');
// add_action('wp_ajax_nopriv_get_shipping_type', 'get_shipping_type');

// function get_shipping_type() {
//     $shipping_type_id = intval($_POST['district_id']);

//     $shipping_types = get_field('select_shipping_types',$shipping_type_id);
//     //echo '<pre>';print_r($shipping_types);
//     $options = array();
    
//     foreach ($shipping_types as $shipping_type) {
//         $options[$shipping_type->ID] = $shipping_type->post_title;
//     }
    
//     print(json_encode($options));
//     exit;
// }

//////////////////////////////// Pickup Locations ////////////////////////////////

add_action('wp_ajax_get_store', 'get_store');
add_action('wp_ajax_nopriv_get_store', 'get_store');

function get_store() {
    ///////////////////////////////////////////////////
    $shipping_type_id = intval($_POST['shipping_type_id']);
    $province_id = intval($_POST['provincefield']);

    // Get the related stores from the 'province' post type using the ACF relationship field 'select_stores'
    $related_stores = get_field('select_stores', $province_id);

    $validated_store_ids = array(); // Array to hold valid store IDs

    if ($related_stores) {
        foreach ($related_stores as $store) {
            $store_id = $store->ID;

            // Check if this store is related to the selected shipping type through the 'select_store_location' field
            $shipping_type_stores = get_field('select_store_location', $shipping_type_id);

            if ($shipping_type_stores) {
                foreach ($shipping_type_stores as $shipping_store) {
                    if ($shipping_store->ID == $store_id) {
                        $validated_store_ids[] = $store_id; // Add to valid store IDs if there's a match
                        break; // No need to check further if a match is found
                    }
                }
            }
        }
    }

    // Check if there are any validated store IDs
    if (!empty($validated_store_ids)) {
        // Prepare WP_Query to get these validated stores
        $args = array(
            'post_type' => 'store',
            'post__in' => $validated_store_ids, // Only get stores that are validated
        );

        $query = new WP_Query($args);

        $options = array();
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                // Output store data
                $options[get_the_ID()] = get_the_title();
            }
            wp_reset_postdata();
        } else {
            echo 'No se encontraron tiendas para la provincia y el tipo de envío seleccionados.';
        }
    } else {
        echo 'No se encontraron tiendas para la provincia y el tipo de envío seleccionados.';
    }
    
    print(json_encode($options));
    exit;
}
//////////////////////////////// Store Data ////////////////////////////////

add_action('wp_ajax_get_store_data', 'get_store_data');
add_action('wp_ajax_nopriv_get_store_data', 'get_store_data');

function get_store_data() {
    global $woocommerce;
    $store_id = intval($_POST['store_id']);
    //echo '<pre>';print_r($woocommerce);
    $getShippingCost = get_field('shipping_cost', $store_id);

    if ($getShippingCost < 0) {
        $getShippingCost = get_field('default_cost', 'option');
    }
    
    $store_data = array(
        'store_location' => get_field('store_address', $store_id),
        'store_phone' => get_field('store_phone', $store_id)
    );
    
    if(!WC()->session->get('remove_custom_shipping_cost')){
        $store_data['shipping_cost'] = $getShippingCost;
    }
    //echo 'Session = '.WC()->session->get('remove_custom_shipping_cost');exit;
    echo json_encode($store_data);
    wp_die();
}

