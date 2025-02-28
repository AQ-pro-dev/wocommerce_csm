<?php 

add_action('plugins_loaded', 'init_contact_for_payment_gateway');

function init_contact_for_payment_gateway() {
    if (!class_exists('WC_Payment_Gateway')) return;

    class WC_Gateway_Contact_For_Payment extends WC_Payment_Gateway {

        public function __construct() {
            $this->id                 = 'contact_for_payment';
            $this->icon               = ''; // URL to an icon image
            $this->has_fields         = false;
            $this->method_title       = __('Contacto para pago', 'woocommerce');
            $this->method_description = __('Permita que los clientes realicen un pedido y comuníquese con ellos para compartir sus datos bancarios para el pago.
', 'woocommerce');

            // Load settings
            $this->init_form_fields();
            $this->init_settings();

            // Define user settings
            $this->title       = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->enabled     = $this->get_option('enabled');

            // Hooks
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title'   => __('Activar/Desactivar', 'woocommerce'),
                    'type'    => 'checkbox',
                    'label'   => __('Habilitar contacto para pago', 'woocommerce'),
                    'default' => 'yes',
                ),
                'title' => array(
                    'title'       => __('Title', 'woocommerce'),
                    'type'        => 'text',
                    'description' => __('Título que se mostrará en la página de pago.', 'woocommerce'),
                    'default'     => __('Contacto para pago', 'woocommerce'),
                ),
                'description' => array(
                    'title'       => __('Description', 'woocommerce'),
                    'type'        => 'textarea',
                    'description' => __('Descripción que se mostrará en la página de pago.', 'woocommerce'),
                    'default'     => __('Nos pondremos en contacto con usted con los datos bancarios para el pago. El costo de envío se agregará a su pedido.', 'woocommerce'),
                ),
            );
        }

        public function process_payment($order_id) {
            $order = wc_get_order($order_id);

            // Mark as on-hold (we're awaiting the customer to pay through bank details)
            $order->update_status('on-hold', __('Awaiting bank payment', 'woocommerce'));

            // Reduce stock levels
            wc_reduce_stock_levels($order_id);

            // Add order note with bank details
            $order->add_order_note(__('Por favor contáctenos para obtener los datos bancarios para completar el pago.', 'woocommerce'));

            // Return thank you page redirect
            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url($order),
            );
        }
        public function update_order_review($posted_data) {
            if (WC()->session->get('remove_custom_shipping_cost')) {
                // Perform any additional actions if needed
                WC()->session->__unset('remove_custom_shipping_cost');
            }
        }
    }
}

add_filter('woocommerce_payment_gateways', 'add_contact_for_payment_gateway');

function add_contact_for_payment_gateway($gateways) {
    $gateways[] = 'WC_Gateway_Contact_For_Payment';
    return $gateways;
}

