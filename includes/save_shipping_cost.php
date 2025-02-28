<?php


//Save Custom Shipping Cost to Order Meta

add_action('woocommerce_checkout_create_order', 'save_custom_shipping_cost_to_order', 20, 2);

function save_custom_shipping_cost_to_order($order, $data) {

    if(!WC()->session->get('remove_custom_shipping_cost')){

       $custom_shipping_cost = WC()->session->get('custom_shipping_cost');
    
        if ($custom_shipping_cost) {
            $order->update_meta_data('custom_shipping_cost', $custom_shipping_cost);
            // Add the custom shipping cost to the order total
            $order->set_shipping_total($custom_shipping_cost);
        } 
    }
    
    
    
}

// Display custom shipping cost in admin order totals
add_action('woocommerce_admin_order_totals_after_shipping', 'display_custom_shipping_cost_in_admin_order_totals');

function display_custom_shipping_cost_in_admin_order_totals($order_id) {
    $order = wc_get_order($order_id);
    if (is_object(WC()->session) && !WC()->session->get('remove_custom_shipping_cost')) {

        $custom_shipping_cost = $order->get_meta('custom_shipping_cost');

        if ($custom_shipping_cost) {
            echo '<tr>
                <td class="label">' . __('Costo de envío:', 'woocommerce') . '</td>
                <td width="1%"></td>
                <td class="total">' . wc_price($custom_shipping_cost) . '</td>
            </tr>';
        }
    }
}
add_action('woocommerce_admin_order_data_after_shipping_address', 'display_custom_shipping_in_admin_order', 10, 1);

function display_custom_shipping_in_admin_order($order) {
    $custom_shipping_cost = $order->get_meta('_custom_shipping_cost');
    
    if ($custom_shipping_cost) {
        echo '<p><strong>' . __('Costo de envío:') . '</strong> ' . wc_price($custom_shipping_cost) . '</p>';
    }
}
// Display custom shipping cost in order item totals
add_filter('woocommerce_get_order_item_totals', 'add_custom_shipping_cost_to_order_totals', 10, 3);

function add_custom_shipping_cost_to_order_totals($total_rows, $order, $tax_display) {

    if(!WC()->session->get('remove_custom_shipping_cost')){
        $custom_shipping_cost = $order->get_meta('custom_shipping_cost');

        if ($custom_shipping_cost) {
            $total_rows['custom_shipping_cost'] = array(
                'label' => __('Costo de envío:', 'woocommerce'),
                'value' => wc_price($custom_shipping_cost)
            );
        }

        return $total_rows;
    }

}

// Display custom shipping cost in emails
add_action('woocommerce_email_order_meta', 'add_custom_shipping_cost_to_order_emails', 20, 4);

function add_custom_shipping_cost_to_order_emails($order, $sent_to_admin, $plain_text, $email) {
    if(!WC()->session->get('remove_custom_shipping_cost')){
        $custom_shipping_cost = $order->get_meta('custom_shipping_cost');

        if ($custom_shipping_cost) {
            if ($plain_text) {
                echo 'Costo de envío: ' . wc_price($custom_shipping_cost) . "\n";
            } else {
                echo '<p><strong>' . __('Costo de envío:', 'woocommerce') . '</strong> ' . wc_price($custom_shipping_cost) . '</p>';
            }
        }
    }
}

add_action('woocommerce_checkout_order_processed', 'add_custom_shipping_cost_to_order_total');

function add_custom_shipping_cost_to_order_total($order_id) {
    if(!WC()->session->get('remove_custom_shipping_cost')){
        $order = wc_get_order($order_id);
        $custom_shipping_cost = WC()->session->get('custom_shipping_cost');
        //$custom_shipping_cost = $order->get_meta('_custom_shipping_cost');
        
        if ($custom_shipping_cost) {
            $new_total = $order->get_total() + $custom_shipping_cost;
            $X = $order->set_total($new_total);
            $order->save();
        }
    }
}

add_action('woocommerce_checkout_create_order_line_item', 'add_custom_shipping_cost_to_order_items', 10, 4);

function add_custom_shipping_cost_to_order_items($item, $cart_item_key, $values, $order) {
    if(!WC()->session->get('remove_custom_shipping_cost')){
        if (WC()->session->get('custom_shipping_cost')) {
            $custom_shipping_cost = WC()->session->get('custom_shipping_cost');
            $item->add_meta_data('Custom Shipping Cost', $custom_shipping_cost);
        }
    }
}


/////////////////////////////////////////////////////////////////////////////////////////

add_action('woocommerce_checkout_update_order_meta', 'save_user_selections_to_order_meta');

function save_user_selections_to_order_meta($order_id) {
    //echo '<pre>';print_r($_POST);exit;
    if (isset($_POST['provincefield'])) {
        update_post_meta($order_id, '_province', sanitize_text_field($_POST['provincefield']));
    }
    // if (isset($_POST['municipalityfield'])) {
    //     update_post_meta($order_id, '_municipality', sanitize_text_field($_POST['municipalityfield']));
    // }
    // if (isset($_POST['districtfield'])) {
    //     update_post_meta($order_id, '_municipal_district', sanitize_text_field($_POST['districtfield']));
    // }
    if (isset($_POST['shipping_typefield'])) {
        update_post_meta($order_id, '_custom_shipping', sanitize_text_field($_POST['shipping_typefield']));
    }
    if (isset($_POST['storelocation'])) {
        update_post_meta($order_id, '_store', sanitize_text_field($_POST['storelocation']));
    }
}

add_action('woocommerce_thankyou', 'display_user_selections_on_thank_you_page', 20);

function display_user_selections_on_thank_you_page($order_id) {
    $order = wc_get_order($order_id);
    WC()->session->__unset('custom_shipping_cost');
    WC()->session->__unset('remove_custom_shipping_cost');
    $province = get_post_meta($order_id, '_province', true);
    //$municipality = get_post_meta($order_id, '_municipality', true);
    //$municipal_district = get_post_meta($order_id, '_municipal_district', true);
    $shipping_type = get_post_meta($order_id, '_custom_shipping', true);
    $store = get_post_meta($order_id, '_store', true);
    if($province && $shipping_type && $store){//&& $municipality && $municipal_district 
        echo '<h3>' . __('Opciones de entrega', 'woocommerce') . '</h3>';
        echo '<p><strong>' . __('Provincia:', 'woocommerce') . '</strong> ' . esc_html(get_the_title( $province )) . '</p>';
        //echo '<p><strong>' . __('Municipality:', 'woocommerce') . '</strong> ' . esc_html(get_the_title($municipality)) . '</p>';
        //echo '<p><strong>' . __('Municipal District:', 'woocommerce') . '</strong> ' . esc_html(get_the_title($municipal_district)) . '</p>';
        echo '<p><strong>' . __('Tipo de envío:', 'woocommerce') . '</strong> ' . esc_html(get_the_title($shipping_type)) . '</p>';
        echo '<p><strong>' . __('Lugar de recogida:', 'woocommerce') . '</strong> ' . esc_html(get_the_title($store)) . '</p>';
    }
}

add_action('woocommerce_admin_order_data_after_billing_address', 'display_user_selections_in_admin_order_page', 20);

function display_user_selections_in_admin_order_page($order) {
    $order_id = $order->get_id();

    $province = get_post_meta($order_id, '_province', true);
    //$municipality = get_post_meta($order_id, '_municipality', true);
    //$municipal_district = get_post_meta($order_id, '_municipal_district', true);
    $custom_shippingtype = get_post_meta($order_id, '_custom_shipping', true);
    $store = get_post_meta($order_id, '_store', true);
    if($province && $custom_shippingtype && $store){//&& $municipality && $municipal_district 
        echo '<h3>' . __('Opciones de entrega', 'woocommerce') . '</h3>';
        echo '<p><strong>' . __('Provincia:', 'woocommerce') . '</strong> ' . esc_html(get_the_title($province)) . '</p>';
        //echo '<p><strong>' . __('Municipality:', 'woocommerce') . '</strong> ' . esc_html(get_the_title($municipality)) . '</p>';
        //echo '<p><strong>' . __('Municipal District:', 'woocommerce') . '</strong> ' . esc_html(get_the_title($municipal_district)) . '</p>';
        echo '<p><strong>' . __('Tipo de envío:', 'woocommerce') . '</strong> ' . esc_html(get_the_title($custom_shippingtype)) . '</p>';
        echo '<p><strong>' . __('Lugar de recogida:', 'woocommerce') . '</strong> ' . esc_html(get_the_title($store)) . '</p>';
    }
}



