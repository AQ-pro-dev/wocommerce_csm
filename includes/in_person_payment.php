<?php 
add_action('plugins_loaded', 'init_in_person_payment_gateway');

function init_in_person_payment_gateway() {
    if (!class_exists('WC_Payment_Gateway')) return;

    class WC_Gateway_In_Person extends WC_Payment_Gateway {

        public function __construct() {
            $this->id                 = 'in_person';
            $this->icon               = ''; // URL to an icon image
            $this->has_fields         = false;
            $this->method_title       = __('In Person', 'woocommerce');
            $this->method_description = __('Allow customers to pick up products from the store and pay in person.', 'woocommerce');

            // Load settings
            $this->init_form_fields();
            $this->init_settings();

            // Define user settings
            $this->title           = $this->get_option('title');
            $this->description     = $this->get_option('description');
            $this->enabled         = $this->get_option('enabled');
            $this->allowed_states  = $this->get_option('allowed_states', array());

            // Hooks
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_checkout_update_order_review', array($this, 'remove_custom_shipping_cost_if_in_person_payment'), 10, 1);
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title'   => __('Enable/Disable', 'woocommerce'),
                    'type'    => 'checkbox',
                    'label'   => __('Enable In Person Payment', 'woocommerce'),
                    'default' => 'yes',
                ),
                'title' => array(
                    'title'       => __('Title', 'woocommerce'),
                    'type'        => 'text',
                    'description' => __('Title to be displayed on the checkout page.', 'woocommerce'),
                    'default'     => __('In Person Payment', 'woocommerce'),
                ),
                'description' => array(
                    'title'       => __('Description', 'woocommerce'),
                    'type'        => 'textarea',
                    'description' => __('Description to be displayed on the checkout page.', 'woocommerce'),
                    'default'     => __('Pay in person at the store when you pick up your order.', 'woocommerce'),
                ),
                'allowed_states' => array(
                    'title'       => __('Allowed States', 'woocommerce'),
                    'type'        => 'multiselect',
                    'description' => __('Select states where In Person Payment is allowed.', 'woocommerce'),
                    'options'     => $this->get_states(),
                    'default'     => array(),
                ),
            );
        }

        public function is_available() {
            if ($this->enabled === 'no') {
                return false;
            }

            // Fallback for when WC()->customer is not available
            if (!WC()->customer) {
                return true;
            }

            $chosen_state = WC()->customer->get_shipping_state();
            return in_array($chosen_state, $this->allowed_states);
        }

        private function get_states() {
            $states = WC()->countries->get_states('DO'); // Get states for Dominican Republic
            return $states;
            // $provinces = 
            //     get_posts(
            //         array(
            //             'post_type'      => 'province', 
            //             'numberposts'    => -1,
            //             'orderby'        => 'title',
            //             'order'          => 'ASC' 
            //         )
            //     );
            // $options = array();
            
            // foreach ($provinces as $province) {
            //     $options[$province->ID] = $province->post_title;
            // }
            
            // return $options;
        }

        public function process_payment($order_id) {
            $order = wc_get_order($order_id);

            // Mark as on-hold (we're awaiting the customer to pay in person)
            $order->update_status('on-hold', __('Awaiting in person payment', 'woocommerce'));

            // Reduce stock levels
            wc_reduce_stock_levels($order_id);

            // Remove cart
            WC()->cart->empty_cart();

            // Return thank you page redirect
            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url($order),
            );
        }

        public function remove_custom_shipping_cost_if_in_person_payment($post_data) {
            parse_str($post_data, $data);
            if (isset($data['payment_method']) && $data['payment_method'] === 'in_person') {
                WC()->session->__unset('custom_shipping_cost');
            }
        }
    }
}

add_filter('woocommerce_payment_gateways', 'add_in_person_payment_gateway');

function add_in_person_payment_gateway($gateways) {
    $gateways[] = 'WC_Gateway_In_Person';
    return $gateways;
}

add_filter('woocommerce_available_payment_gateways', 'filter_available_payment_gateways_by_state');

function filter_available_payment_gateways_by_state($available_gateways) {
    if (is_admin() && !defined('DOING_AJAX')) return $available_gateways;
    // $states = WC()->countries->get_states('DO');
    // echo '<pre>';print_r($states);exit;
    if (isset($available_gateways['in_person'])) {
        
        $chosen_state = WC()->customer->get_shipping_state();
        $allowed_states = get_option('woocommerce_in_person_settings', array());
        $allowed_states = isset($allowed_states['allowed_states']) ? $allowed_states['allowed_states'] : array();
        if (!in_array($chosen_state, $allowed_states)) {
            unset($available_gateways['in_person']);

         }
    }


    return $available_gateways;
}


    // //$chosen_state = WC()->customer->get_shipping_state();

    // Query to get provinces where state_key matches the chosen state
    
