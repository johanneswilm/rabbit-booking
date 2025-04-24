jQuery(document).ready(function($) {
    $.datepicker.regional['sv'] = {
        closeText: 'Stäng',
        prevText: '&#xAB;Förra',
        nextText: 'Nästa&#xBB;',
        currentText: 'Idag',
        monthNames: ['Januari','Februari','Mars','April','Maj','Juni',
        'Juli','Augusti','September','Oktober','November','December'],
        monthNamesShort: ['Jan','Feb','Mar','Apr','Maj','Jun',
        'Jul','Aug','Sep','Okt','Nov','Dec'],
        dayNamesShort: ['Sön','Mån','Tis','Ons','Tor','Fre','Lör'],
        dayNames: ['Söndag','Måndag','Tisdag','Onsdag','Torsdag','Fredag','Lördag'],
        dayNamesMin: ['Sö','Må','Ti','On','To','Fr','Lö'],
        weekHeader: 'Ve',
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };

    // Set datepicker to Swedish
    $.datepicker.setDefaults($.datepicker.regional['sv']);

    // Initialize datepickers
    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd',
        minDate: 0,
        onSelect: calculatePrice
    });



    // Calculate price when inputs change
    $('#number_of_rabbits, #cage_type, input[name="shared_cage"]').on('change', calculatePrice);

    // Check availability button
    $('#check-availability').on('click', function(e) {
        e.preventDefault();

        if (!validateForm()) {
            return false;
        }

        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'check_cage_availability',
                nonce: $('#rabbit_booking_nonce').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                number_of_rabbits: $('#number_of_rabbits').val(),
                shared_cage: $('input[name="shared_cage"]:checked').val(),
                cage_type: $('#cage_type').val()
            },
            success: function(response) {
                if (response.success) {
                    $('#availability-message').html('<div class="success-message">' + response.data.message + '</div>');
                    $('#check-availability').hide();
                    $('#submit-booking').show();
                    $('.calculated-info').show();
                } else {
                    $('#availability-message').html('<div class="error-message">' + response.data.message + '</div>');
                }
            }
        });
    });

    // Submit booking button
    $('#submit-booking').on('click', function(e) {
        e.preventDefault();

        if (!validateForm()) {
            return false;
        }

        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'submit_rabbit_booking',
                nonce: $('#rabbit_booking_nonce').val(),
                customer_name: $('#customer_name').val(),
                customer_email: $('#customer_email').val(),
                customer_phone: $('#customer_phone').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                number_of_rabbits: $('#number_of_rabbits').val(),
                shared_cage: $('input[name="shared_cage"]:checked').val(),
                cage_type: $('#cage_type').val(),
                number_of_cages: $('#number_of_cages').text(),
                total_price: $('#total_price').text()
            },
            success: function(response) {
                if (response.success) {
                    // Replace the form with the confirmation message
                    $('.rabbit-booking-form-container').html(response.data.confirmation_html);
                } else {
                    $('#availability-message').html('<div class="error-message">' + response.data.message + '</div>');
                }
            }
        });
    });

    // Calculate the price and number of cages needed
    function calculatePrice() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        var numberOfRabbits = parseInt($('#number_of_rabbits').val()) || 0;
        var sharedCage = $('input[name="shared_cage"]:checked').val() === '1';
        var selectedCage = $('#cage_type option:selected');
        var pricePerDay = parseFloat(selectedCage.data('price')) || 0;
        var cageCapacity = parseInt(selectedCage.data('capacity')) || 1;

        if (startDate && endDate && numberOfRabbits > 0) {
            // Calculate number of days
            var start = new Date(startDate);
            var end = new Date(endDate);
            var timeDiff = Math.abs(end.getTime() - start.getTime());
            var numberOfDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

            // Calculate number of cages needed
            var numberOfCages;
            if (sharedCage) {
                numberOfCages = Math.ceil(numberOfRabbits / cageCapacity);
            } else {
                numberOfCages = numberOfRabbits;
            }

            // Calculate total price
            var totalPrice = numberOfCages * pricePerDay * numberOfDays;

            // Update the display
            $('#number_of_cages').text(numberOfCages);
            $('#number_of_days').text(numberOfDays);
            $('#price_per_day').text(pricePerDay);
            $('#total_price').text(totalPrice);

            $('.calculated-info').show();
        } else {
            $('.calculated-info').hide();
        }
    }

    // Validate the form
    function validateForm() {
        var valid = true;

        // Basic validation, you can enhance this
        $('#rabbit-booking-form input[required], #rabbit-booking-form select[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('error');
                valid = false;
            } else {
                $(this).removeClass('error');
            }
        });

        return valid;
    }


});
