=== WooCommerce CSM Plugin ===
Contributors: Abdul Qadeer
Tags: woocommerce, shipping, payment gateway, custom shipping
Requires at least: 5.0
Tested up to: 6.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This custom WooCommerce plugin adds custom shipping methods and payment gateways to your WooCommerce store.

== Description ==

This custom WooCommerce plugin adds custom shipping methods and payment gateways to your WooCommerce store. It includes features for allowing customers to place orders and contact them to share bank details for payment. Additionally, it allows configuring a specific in-person payment method for specific states and dynamically manages shipping costs based on the selected payment method.

== Installation ==

1. Download the plugin files and upload them to your WordPress site.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure the plugin settings in the WooCommerce settings area.

== Configuration ==

### Custom Shipping Methods

This plugin supports the following custom shipping methods:
- Vimenpaq
- Domex

### Payment Gateways

#### Contact for Payment

This payment gateway allows customers to place an order and contact them to share bank details for payment. Shipping cost is included.

#### In Person Payment

This payment gateway allows customers to pick up products from the store and pay in person. This method is only enabled for specific states of the Dominican Republic. The list of allowed states can be configured in the WooCommerce settings.

== Usage ==

### Enabling and Configuring Custom Shipping Methods

1. Navigate to WooCommerce > Settings > Shipping.
2. Add and configure the custom shipping methods as required.

### Enabling and Configuring Payment Gateways

1. Navigate to WooCommerce > Settings > Payments.
2. Enable and configure the "Contact for Payment" and "In Person Payment" gateways as required.
3. For "In Person Payment," configure the allowed states under the gateway settings.

### Handling Payment Method Selection

This plugin uses AJAX to dynamically manage sessions based on the selected payment method. Ensure that the `csm_script.js` file is properly enqueued and included in your theme's `js` directory.

== Customization ==

You can customize the plugin by editing the following files:

- `includes/ajax_handling.php`: Defines the functions to handle AJAX requests.
- `includes/checkout_fields_validate.php`: Defines the logic to validate custom checkout fields.
- `includes/checkout_fields.php`: Adds custom fields on the checkout page.
- `includes/contact_for_payment.php`: Defines the "Contact for Payment" gateway.
- `includes/class-wc-gateway-in-person.php`: Defines the "In Person Payment" gateway.
- `includes/cpts.php`: Adds custom CPTs.
- `includes/save_shipping_cost.php`: Creates sessions for shipping.
- `includes/shipping_cost_calculation.php`: Handles the shipping cost calculations.
- `assets/js/csm_script.js`: Handles the JS and AJAX requests.
- `assets/css/style.css`: Defines the custom CSS.

== Troubleshooting ==

- Ensure that the plugin is activated.
- Ensure that the `csm_script.js` file is properly enqueued and included in the `js` directory of your theme.
- Check the WooCommerce settings to ensure that the payment gateways are enabled and configured correctly.
- If you encounter issues with the session handling, ensure that the AJAX requests are being processed correctly by checking the browser console for errors.

== Changelog ==

### 1.0.0
- Initial release with custom shipping methods and payment gateways.

== License ==

This plugin is licensed under the GNU General Public License v2.0.
