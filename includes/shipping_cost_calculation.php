<?php
add_action('woocommerce_cart_calculate_fees', 'custom_shipping_cost' , 1);

function custom_shipping_cost($cart) {
    global $woocommerce;
    if (isset($_POST['post_data'])) {
        parse_str($_POST['post_data'], $post_data);
        $store_id = $post_data['storelocation'];
        
        if ($store_id) {
            $shipping_cost = $post_data['shipping_cost_hidden'];
            //if(!WC()->session->get('remove_custom_shipping_cost')){
            if ($shipping_cost && (!WC()->session->get('remove_custom_shipping_cost'))) {
                //$cart->add_fee(__('Custom Shipping', 'woocommerce'), $shipping_cost, true, 'standard');
                $woocommerce->cart->add_fee(__('Envío personalizado', 'woocommerce'), $shipping_cost, true, 'standard');
                WC()->session->set('custom_shipping_cost', $shipping_cost);
            }
        }
    }
}
