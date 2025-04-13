<?php

class Rabbit_Booking_Public {

    public function enqueue_scripts() {
        // Add your enqueue scripts code here if needed
        wp_enqueue_script('jquery');

        // Localize script for AJAX
        wp_localize_script('jquery', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }

public function register_shortcodes() {
    add_shortcode('rabbit_booking_form', array($this, 'render_booking_form'));
}

public function render_booking_form() {
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
    wp_enqueue_script('rabbit-booking-script', RABBIT_BOOKING_PLUGIN_URL . 'public/js/rabbit-booking.js', array('jquery', 'jquery-ui-datepicker'), RABBIT_BOOKING_VERSION, true);
    wp_enqueue_style('rabbit-booking-style', RABBIT_BOOKING_PLUGIN_URL . 'public/css/rabbit-booking.css', array(), RABBIT_BOOKING_VERSION);

    // Get cage types for dropdown
    global $wpdb;
    $cage_types = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rabbit_cage_types ORDER BY price_per_day");

    ob_start();
    include RABBIT_BOOKING_PLUGIN_DIR . 'public/views/booking-form.php';
    return ob_get_clean();
}

public function register_ajax_handlers() {
    add_action('wp_ajax_check_cage_availability', array($this, 'check_cage_availability'));
    add_action('wp_ajax_nopriv_check_cage_availability', array($this, 'check_cage_availability'));

    add_action('wp_ajax_submit_rabbit_booking', array($this, 'submit_rabbit_booking'));
    add_action('wp_ajax_nopriv_submit_rabbit_booking', array($this, 'submit_rabbit_booking'));
}

public function check_cage_availability() {
    check_ajax_referer('rabbit_booking_form', 'nonce');

    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $number_of_rabbits = intval($_POST['number_of_rabbits']);
    $shared_cage = intval($_POST['shared_cage']);
    $cage_type_id = intval($_POST['cage_type']);

    // Validate dates
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $today = new DateTime();

    if ($start < $today) {
        wp_send_json_error(array('message' => __('Start date cannot be in the past', 'rabbit-booking')));
        return;
    }

    if ($end <= $start) {
        wp_send_json_error(array('message' => __('End date must be after start date', 'rabbit-booking')));
        return;
    }

    // Get cage type details
    global $wpdb;
    $cage_type = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}rabbit_cage_types WHERE id = %d",
        $cage_type_id
    ));

    if (!$cage_type) {
        wp_send_json_error(array('message' => __('Invalid cage type selected', 'rabbit-booking')));
        return;
    }

    // Calculate required cages
    $required_cages = $shared_cage ? ceil($number_of_rabbits / $cage_type->capacity) : $number_of_rabbits;

    // Check if enough cages are available for the selected dates
    $available = $this->check_cage_availability_for_dates($cage_type_id, $start_date, $end_date, $required_cages);

    if ($available) {
        // Calculate price
        $interval = $start->diff($end);
        $days = $interval->days > 0 ? $interval->days : 1;
        $total_price = $required_cages * $cage_type->price_per_day * $days;

        wp_send_json_success(array(
            'message' => sprintf(
                __('We have %d cages available for your booking! Total cost: %d kr', 'rabbit-booking'),
                $required_cages,
                $total_price
            ),
            'required_cages' => $required_cages,
            'days' => $days,
            'price_per_day' => $cage_type->price_per_day,
            'total_price' => $total_price
        ));
    } else {
        wp_send_json_error(array(
            'message' => sprintf(
                __('Sorry, we don\'t have enough %s cages available for the selected dates. Please choose different dates or a different cage type.', 'rabbit-booking'),
                $cage_type->name
            )
        ));
    }
}

private function check_cage_availability_for_dates($cage_type_id, $start_date, $end_date, $required_cages) {
    global $wpdb;

    // Get the total cages of this type
    $total_cages = $wpdb->get_var($wpdb->prepare(
        "SELECT total_available FROM {$wpdb->prefix}rabbit_cage_types WHERE id = %d",
        $cage_type_id
    ));

    // Find overlapping bookings
    $bookings = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}rabbit_bookings
         WHERE cage_type_id = %d
         AND (
             (start_date <= %s AND end_date >= %s) OR
             (start_date <= %s AND end_date >= %s) OR
             (start_date >= %s AND end_date <= %s)
         )
         AND booking_status != 'cancelled'",
        $cage_type_id, $end_date, $start_date, $end_date, $start_date, $start_date, $end_date
    ));

    // Calculate maximum cages needed on any day
    $date_cages = array();
    $current_date = new DateTime($start_date);
    $end = new DateTime($end_date);

    while ($current_date <= $end) {
        $current_date_str = $current_date->format('Y-m-d');
        $date_cages[$current_date_str] = 0;

        foreach ($bookings as $booking) {
            $booking_start = new DateTime($booking->start_date);
            $booking_end = new DateTime($booking->end_date);

            if ($current_date >= $booking_start && $current_date <= $booking_end) {
                $date_cages[$current_date_str] += $booking->number_of_cages;
            }
        }

        $current_date->modify('+1 day');
    }

    // Check if enough cages are available for all days
    foreach ($date_cages as $date => $booked_cages) {
        $available_cages = $total_cages - $booked_cages;
        if ($available_cages < $required_cages) {
            return false;
        }
    }

    return true;
}

public function submit_rabbit_booking() {
    check_ajax_referer('rabbit_booking_form', 'nonce');

    $customer_name = sanitize_text_field($_POST['customer_name']);
    $customer_email = sanitize_email($_POST['customer_email']);
    $customer_phone = sanitize_text_field($_POST['customer_phone']);
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $number_of_rabbits = intval($_POST['number_of_rabbits']);
    $shared_cage = intval($_POST['shared_cage']);
    $cage_type_id = intval($_POST['cage_type']);
    $number_of_cages = intval($_POST['number_of_cages']);
    $total_price = floatval($_POST['total_price']);

    // Check availability again to be sure
    $available = $this->check_cage_availability_for_dates($cage_type_id, $start_date, $end_date, $number_of_cages);

    if (!$available) {
        wp_send_json_error(array(
            'message' => __('Sorry, the cages are no longer available. Please try again.', 'rabbit-booking')
        ));
        return;
    }

    // Create booking reference
    $booking_reference = 'RB-' . date('Ymd') . '-' . substr(uniqid(), -5);

    // Insert the booking
    global $wpdb;
    $inserted = $wpdb->insert(
        $wpdb->prefix . 'rabbit_bookings',
        array(
            'customer_name' => $customer_name,
            'customer_email' => $customer_email,
            'customer_phone' => $customer_phone,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'number_of_rabbits' => $number_of_rabbits,
            'shared_cage' => $shared_cage,
            'cage_type_id' => $cage_type_id,
            'number_of_cages' => $number_of_cages,
            'total_price' => $total_price,
            'payment_status' => 'pending',
            'payment_reference' => $booking_reference,
            'booking_status' => 'pending',
            'created_at' => current_time('mysql')
        )
    );

    if ($inserted) {
        $booking_id = $wpdb->insert_id;

        // Generate Swish payment link/QR code
        $swish_number = get_option('rabbit_booking_swish_number', '123456789');
        $swish_message = $booking_reference;

        // Generate QR code URL for Swish payment
        $swish_data = "C{$swish_number};{$total_price};{$swish_message};0";
        $qr_code_url = 'https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=' . urlencode($swish_data);

        // Prepare confirmation HTML
        $confirmation_html = '
        <div class="booking-confirmation">
            <h2>' . __('Booking Confirmation', 'rabbit-booking') . '</h2>
            <p>' . __('Thank you for your booking!', 'rabbit-booking') . '</p>
            <p>' . __('Your booking reference is:', 'rabbit-booking') . ' <strong>' . $booking_reference . '</strong></p>
            <div class="booking-details">
                <h3>' . __('Booking Details', 'rabbit-booking') . '</h3>
                <p><strong>' . __('Drop-off Date:', 'rabbit-booking') . '</strong> ' . $start_date . '</p>
                <p><strong>' . __('Pick-up Date:', 'rabbit-booking') . '</strong> ' . $end_date . '</p>
                <p><strong>' . __('Number of Rabbits:', 'rabbit-booking') . '</strong> ' . $number_of_rabbits . '</p>
                <p><strong>' . __('Number of Cages:', 'rabbit-booking') . '</strong> ' . $number_of_cages . '</p>
                <p><strong>' . __('Total Price:', 'rabbit-booking') . '</strong> ' . $total_price . ' kr</p>
            </div>
            <div class="payment-info">
                <h3>' . __('Payment Information', 'rabbit-booking') . '</h3>
                <p>' . __('Please pay via Swish to complete your booking:', 'rabbit-booking') . '</p>
                <p><strong>' . __('Swish Number:', 'rabbit-booking') . '</strong> ' . $swish_number . '</p>
                <p><strong>' . __('Amount:', 'rabbit-booking') . '</strong> ' . $total_price . ' kr</p>
                <p><strong>' . __('Message:', 'rabbit-booking') . '</strong> ' . $booking_reference . '</p>
                <div class="qr-code">
                    <p>' . __('Scan this QR code with your Swish app:', 'rabbit-booking') . '</p>
                    <img src="' . $qr_code_url . '" alt="Swish QR Code">
                </div>
            </div>
            <p class="booking-note">' . __('A confirmation email with all details has been sent to your email address.', 'rabbit-booking') . '</p>
        </div>';

        // Send confirmation email
        $this->send_booking_confirmation_email($booking_id);

        wp_send_json_success(array(
            'confirmation_html' => $confirmation_html
        ));
    } else {
        wp_send_json_error(array(
            'message' => __('Failed to create booking. Please try again.', 'rabbit-booking')
        ));
    }
}

private function send_booking_confirmation_email($booking_id) {
    global $wpdb;

    $booking = $wpdb->get_row($wpdb->prepare(
        "SELECT b.*, ct.name as cage_type_name
         FROM {$wpdb->prefix}rabbit_bookings b
         JOIN {$wpdb->prefix}rabbit_cage_types ct ON b.cage_type_id = ct.id
         WHERE b.id = %d",
        $booking_id
    ));

    if (!$booking) {
        return false;
    }

    $to = $booking->customer_email;
    $subject = __('Your Rabbit Boarding Booking Confirmation', 'rabbit-booking');

    $swish_number = get_option('rabbit_booking_swish_number', '123456789');

    $message = sprintf(
        __('
        <h2>Booking Confirmation</h2>
        <p>Dear %s,</p>
        <p>Thank you for booking rabbit boarding with 4H Farm of Gunnesbo!</p>
        <p>Your booking reference is: <strong>%s</strong></p>

        <h3>Booking Details</h3>
        <p>Drop-off Date: %s</p>
        <p>Pick-up Date: %s</p>
        <p>Number of Rabbits: %d</p>
        <p>Cage Type: %s</p>
        <p>Number of Cages: %d</p>
        <p>Total Price: %s kr</p>

        <h3>Payment Information</h3>
        <p>Please pay via Swish to complete your booking:</p>
        <p>Swish Number: %s</p>
        <p>Amount: %s kr</p>
        <p>Message: %s</p>

        <p>For any questions, please contact us.</p>

        <p>Best regards,<br>4H Farm of Gunnesbo</p>
        ', 'rabbit-booking'),
        $booking->customer_name,
        $booking->payment_reference,
        $booking->start_date,
        $booking->end_date,
        $booking->number_of_rabbits,
        $booking->cage_type_name,
        $booking->number_of_cages,
        $booking->total_price,
        $swish_number,
        $booking->total_price,
        $booking->payment_reference
    );

    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail($to, $subject, $message, $headers);

    // Also send a notification to the admin
    $admin_email = get_option('admin_email');
    $admin_subject = __('New Rabbit Boarding Booking', 'rabbit-booking');

    wp_mail($admin_email, $admin_subject, $message, $headers);

    return true;
}
}
