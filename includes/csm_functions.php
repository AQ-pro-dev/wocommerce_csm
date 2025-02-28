<?php 
// Save Payment Method as Order Meta
add_action('woocommerce_checkout_update_order_meta', 'save_payment_method_order_meta');

function save_payment_method_order_meta($order_id) {
    if ($order = wc_get_order($order_id)) {
        $payment_method = $order->get_payment_method_title();
        update_post_meta($order_id, '_payment_method_title', $payment_method);
    }
}

// Display Payment Method in Admin Order Page
add_action('woocommerce_admin_order_data_after_order_details', 'display_payment_method_order_meta_in_admin');

function display_payment_method_order_meta_in_admin($order) {
    $payment_method = get_post_meta($order->get_id(), '_payment_method_title', true);
    if (!empty($payment_method)) {
        echo '<p style="display: inline-block;margin-top: 15px;"><strong>' . __('Payment Method:', 'woocommerce') . '</strong> ' . esc_html($payment_method) . '</p>';
    }
}

add_filter( 'default_checkout_billing_country', 'set_default_checkout_country' );
function set_default_checkout_country() {
    return 'DO'; // 'DO' is the country code for the Dominican Republic
}