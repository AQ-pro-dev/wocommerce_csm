jQuery(document).ready(function($) {
    updatePaymentSession();
    $('#billing_state').change(function() {
        var province_id = $(this).val();
        $.ajax({
            url: wcheckout_params.ajax_url,
            type: 'POST',
            data: {
                action: 'get_shipping_type',
                province_id: province_id
            },
            success: function(response) {
                var data = JSON.parse(response);
                // Set the province value into the hidden field
                $('#provincefield').val(data.province);

                // Build the options for the municipalities dropdown
                var options = '<option value="">Seleccione un servicio de mensajería</option>';
                $.each(data.options, function(index, value) {
                    options += '<option value="' + index + '">' + value + '</option>';
                });

                // Populate the municipalities dropdown
                $('#shipping_typefield').html(options);
            }
        });
    });


    // $('#municipalityfield').change(function() {
    //     var municipality_id = $(this).val();
    //     $.ajax({
    //         url: wcheckout_params.ajax_url,
    //         type: 'POST',
    //         data: {
    //             action: 'get_district',
    //             municipality_id: municipality_id
    //         },
    //         success: function(response) {
    //             var districts = JSON.parse(response);
    //             var options = '<option value="">Select a municipality district</option>';
    //             $.each(districts, function(index, value) {
    //                 options += '<option value="' + index + '">' + value + '</option>';
    //             });
    //             $('#districtfield').html(options);
    //         }
    //     });
    // });

    // $('#districtfield').change(function() {
    //     var district_id = $(this).val();
    //     $.ajax({
    //         url: wcheckout_params.ajax_url,
    //         type: 'POST',
    //         data: {
    //             action: 'get_shipping_type',
    //             district_id: district_id
    //         },
    //         success: function(response) {
    //             var shipping_types = JSON.parse(response);
    //             var options = '<option value="">Select a shipping type</option>';
    //             $.each(shipping_types, function(index, value) {
    //                 options += '<option value="' + index + '">' + value + '</option>';
    //             });
    //             $('#shipping_typefield').html(options);
    //         }
    //     });
    // });

    $('#shipping_typefield').change(function() {
        var shipping_type_id = $(this).val();
        var provincefield = $("#provincefield").val();
        
        $.ajax({
            url: wcheckout_params.ajax_url,
            type: 'POST',
            data: {
                action: 'get_store',
                shipping_type_id: shipping_type_id,
                provincefield: provincefield
            },
            success: function(response) {
                var stores = JSON.parse(response);
                var options = '<option value="">Selecciona una tienda</option>';
                $.each(stores, function(index, value) {
                    options += '<option value="' + index + '">' + value + '</option>';
                });
                $('#storelocation').html(options);
            }
        });
    });

    function updateStoreData() {
        var store_id = $('#storelocation').val();
        $.ajax({
            url: wcheckout_params.ajax_url,
            type: 'POST',
            data: {
                action: 'get_store_data',
                store_id: store_id
            },
            success: function(response) {
                var storeData = JSON.parse(response);
                // Update the checkout page with store data
                if (storeData && storeData.shipping_cost !== undefined) {
                    $('#shipping_cost').text('Costo de envío: ' + storeData.shipping_cost);
                    $('#shipping_cost').show();
                } else {
                    $('#shipping_cost').hide();
                }
                if(
                    (storeData.store_location !== undefined && storeData.store_phone !==null) && 
                    (storeData.store_phone !== undefined && storeData.store_phone !==null)
                ){
                    $('#store_info').show();
                    // Update hidden fields and other data
                    $('#shipping_cost_hidden').val(storeData.shipping_cost);
                    $('#shippingcost').val(storeData.shipping_cost);
                    $('#store_location').text('Lugar de recogida: ' + storeData.store_location);
                    $('#store_phone').text('Teléfono de la tienda:' + storeData.store_phone);    
                }
                
    
                $(document.body).trigger('update_checkout');
            }
        });
    }
    $('#storelocation').change(function() {
        updatePaymentSession();
        //updateStoreData();
    });
    
    // $('#storelocation').change(function() {
    //     var store_id = $(this).val();
    //     $.ajax({
    //         url: wcheckout_params.ajax_url,
    //         type: 'POST',
    //         data: {
    //             action: 'get_store_data',
    //             store_id: store_id
    //         },
    //         success: function(response) {

    //             var storeData = JSON.parse(response);
    //             // Update the checkout page with store data
    //             if (storeData && storeData.shipping_cost !== undefined) {
    //                 $('#shipping_cost').text('Shipping Cost: ' + storeData.shipping_cost);
    //                 $('#shipping_cost').show();
    //             }else{
    //                 $('#shipping_cost').hide();
    //             }
    //             $('#store_info').show();
    //             //shipping_cost_hidden
    //             $('#shipping_cost_hidden').val(storeData.shipping_cost);
    //             $('#shippingcost').val(storeData.shipping_cost);
    //             $('#store_location').text('Pickup Location: ' + storeData.store_location);
    //             $('#store_phone').text('Store Phone: ' + storeData.store_phone);

    //             $(document.body).trigger('update_checkout');
    //             // Update the shipping cost in WooCommerce cart
    //             //checkoutHandel();
    //         }
    //     });
    // });

    function toggleCustomOptions() {
        var selectedCountry = $('#billing_country').val();
        if (selectedCountry === 'DO') { 
            // DO is the country code for Dominican Republic
            $('#custom_shipping_fields').show();
            $('#custom_shipping_fields select').prop('disabled', false);            
        } else {
            $('#custom_shipping_fields').hide();
            $('#custom_shipping_fields select').prop('disabled', true);
        }
    }

    // Run on page load
    toggleCustomOptions();

    // Run when country changes
    $('#billing_country').change(function() {
        toggleCustomOptions();
    });

    //municipality    district   shipping_type   store
    // Repeat similar event handlers for municipality, district, shipping type, and store dropdowns.

    // Function to display validation message
    function showValidationMessage(element, message) {
        element.css('border', '1px solid red');
        element.next('.validation-message').remove(); // Remove any existing messages
        element.after('<div class="validation-message" style="color: red; font-size: 12px;">' + message + '</div>');
    }

    // Function to clear validation message
    function clearValidationMessage(element) {
        element.css('border', '');
        element.next('.validation-message').remove();
    }

    $('form.checkout').on('submit', function(e) {
        var selectedCountry = $('#billing_country').val();
        if (selectedCountry === 'DO') { 
            // Initialize a variable to track validation status
            let isValid = true;

            //// Custom select option validation
            // const province = $('#provincefield');
            // if (province.val() === '' || province.val() === null) {
            //     isValid = false;
            //     showValidationMessage(province, 'Por favor seleccione Provincia.');
            // } else {
            //     clearValidationMessage(province);
            // }

            //// Custom select option validation
            // const municipality = $('#municipalityfield');
            // if (municipality.val() === '' || municipality.val() === null) {
            //     isValid = false;
            //     showValidationMessage(municipality, 'Please select Municipality.');
            // } else {
            //     clearValidationMessage(municipality);
            // }

            //// Custom select option validation
            // const district = $('#districtfield');
            // if (district.val() === '' || district.val() === null) {
            //     isValid = false;
            //     showValidationMessage(district, 'Please select Municipality District.');
            // } else {
            //     clearValidationMessage(district);
            // }

            // Custom select option validation
            const shipping_type = $('#shipping_typefield');
            if (shipping_type.val() === '' || shipping_type.val() === null) {
                isValid = false;
                showValidationMessage(shipping_type, 'Seleccione Tipo de envío.');
            } else {
                clearValidationMessage(shipping_type);
            }

            // Custom select option validation
            const storelocation = $('#storelocation');
            if (storelocation.val() === '' || storelocation.val() === null) {
                isValid = false;
                showValidationMessage(storelocation, 'Seleccione el lugar de recogida.');
            } else {
                clearValidationMessage(storelocation);
            }

            // Prevent form submission if validation fails
            if (!isValid) {
                e.preventDefault();
            }
        }
    });
    //////////////////////////////////////////////////////////////////////////////////////
   // Define the function
    function updatePaymentSession() {
        var payment_method = $('input[name="payment_method"]:checked').val();
        $.ajax({
            type: 'POST',
            url: wcheckout_params.ajax_url,
            data: {
                action: 'update_payment_session',
                payment_method: payment_method
            },
            success: function(response) {
                // Handle success
                updateStoreData();
            }
        });
    }
    // Bind the function to the change event   updateStoreData();
    $('form.checkout').on('change', 'input[name="payment_method"]', updatePaymentSession);

    

});
