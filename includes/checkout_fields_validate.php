<?php

add_action('woocommerce_checkout_process', 'custom_checkout_field_process');

function custom_checkout_field_process() {

    if (!empty($_POST['provincefield']) && $_POST['billing_country'] == 'DO') {
        if (empty($_POST['provincefield'])) {
            wc_add_notice(__('Por favor seleccione la provincia.'), 'error');
        }
        // if (empty($_POST['municipalityfield'])) {
        //     wc_add_notice(__('Please Select the Municipality.'), 'error');
        // }
        // if (empty($_POST['districtfield'])) {
        //     wc_add_notice(__('Please Select the Municipality District.'), 'error');
        // }
        if (empty($_POST['shipping_typefield'])) {
            wc_add_notice(__('Seleccione el tipo de envío.'), 'error');
        }
        if (empty($_POST['storelocation'])) {
            wc_add_notice(__('Seleccione el lugar de recogida.'), 'error');
        }
    }   
    
}
