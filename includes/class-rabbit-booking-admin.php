<?php

class Rabbit_Booking_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_update_booking_status', array($this, 'update_booking_status'));
        add_action('wp_ajax_update_payment_status', array($this, 'update_payment_status'));
        add_action('wp_ajax_delete_booking', array($this, 'delete_booking'));
        add_action('wp_ajax_update_cage_type', array($this, 'update_cage_type'));
        add_action('wp_ajax_add_cage_type', array($this, 'add_cage_type'));
        add_action('wp_ajax_delete_cage_type', array($this, 'delete_cage_type'));
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Rabbit Booking', 'rabbit-booking'),
            __('Rabbit Booking', 'rabbit-booking'),
            'manage_options',
            'rabbit-booking',
            array($this, 'display_bookings_page'),
            'dashicons-calendar-alt',
            30
        );

        add_submenu_page(
            'rabbit-booking',
            __('Bookings', 'rabbit-booking'),
            __('Bookings', 'rabbit-booking'),
            'manage_options',
            'rabbit-booking',
            array($this, 'display_bookings_page')
        );

        add_submenu_page(
            'rabbit-booking',
            __('Cage Types', 'rabbit-booking'),
            __('Cage Types', 'rabbit-booking'),
            'manage_options',
            'rabbit-booking-cage-types',
            array($this, 'display_cage_types_page')
        );

        add_submenu_page(
            'rabbit-booking',
            __('Settings', 'rabbit-booking'),
            __('Settings', 'rabbit-booking'),
            'manage_options',
            'rabbit-booking-settings',
            array($this, 'display_settings_page')
        );
    }

    public function enqueue_scripts($hook) {
        if (strpos($hook, 'rabbit-booking') === false) {
            return;
        }

        wp_enqueue_style('rabbit-booking-admin', RABBIT_BOOKING_PLUGIN_URL . 'admin/css/rabbit-booking-admin.css', array(), RABBIT_BOOKING_VERSION);
        wp_enqueue_script('rabbit-booking-admin', RABBIT_BOOKING_PLUGIN_URL . 'admin/js/rabbit-booking-admin.js', array('jquery'), RABBIT_BOOKING_VERSION, true);

        wp_localize_script('rabbit-booking-admin', 'rabbit_booking', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rabbit_booking_admin_nonce'),
            'confirm_delete' => __('Are you sure you want to delete this item?', 'rabbit-booking')
        ));

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
    }

    public function display_bookings_page() {
        // Get filter parameters
        $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
        $date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
        $date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';

        // Build query
        global $wpdb;
        $query = "SELECT b.*, ct.name as cage_type_name
                 FROM {$wpdb->prefix}rabbit_bookings b
                 JOIN {$wpdb->prefix}rabbit_cage_types ct ON b.cage_type_id = ct.id";

        $where_clauses = array();
        $query_params = array();

        if ($status_filter) {
            $where_clauses[] = "b.booking_status = %s";
            $query_params[] = $status_filter;
        }

        if ($date_from) {
            $where_clauses[] = "b.start_date >= %s";
            $query_params[] = $date_from;
        }

        if ($date_to) {
            $where_clauses[] = "b.end_date <= %s";
            $query_params[] = $date_to;
        }

        if (!empty($where_clauses)) {
            $query .= " WHERE " . implode(" AND ", $where_clauses);
        }

        $query .= " ORDER BY b.start_date DESC";

        if (!empty($query_params)) {
            $bookings = $wpdb->get_results($wpdb->prepare($query, $query_params));
        } else {
            $bookings = $wpdb->get_results($query);
        }

        // Display the bookings page
        include RABBIT_BOOKING_PLUGIN_DIR . 'admin/views/bookings-page.php';
    }

    public function display_cage_types_page() {
        global $wpdb;
        $cage_types = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rabbit_cage_types ORDER BY id");

        include RABBIT_BOOKING_PLUGIN_DIR . 'admin/views/cage-types-page.php';
    }

    public function display_settings_page() {
        if (isset($_POST['rabbit_booking_settings_nonce']) && wp_verify_nonce($_POST['rabbit_booking_settings_nonce'], 'rabbit_booking_save_settings')) {
            // Save settings
            update_option('rabbit_booking_swish_number', sanitize_text_field($_POST['swish_number']));
            update_option('rabbit_booking_email_sender', sanitize_text_field($_POST['email_sender']));

            echo '<div class="notice notice-success"><p>' . __('Settings saved successfully.', 'rabbit-booking') . '</p></div>';
        }

        $swish_number = get_option('rabbit_booking_swish_number', '');
        $email_sender = get_option('rabbit_booking_email_sender', get_bloginfo('admin_email'));

        include RABBIT_BOOKING_PLUGIN_DIR . 'admin/views/settings-page.php';
    }

    public function update_booking_status() {
        check_ajax_referer('rabbit_booking_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to perform this action.', 'rabbit-booking'));
            return;
        }

        $booking_id = intval($_POST['booking_id']);
        $status = sanitize_text_field($_POST['status']);

        global $wpdb;
        $updated = $wpdb->update(
            $wpdb->prefix . 'rabbit_bookings',
            array('booking_status' => $status),
            array('id' => $booking_id)
        );

        if ($updated) {
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Failed to update booking status.', 'rabbit-booking'));
        }
    }

    public function update_payment_status() {
        check_ajax_referer('rabbit_booking_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to perform this action.', 'rabbit-booking'));
            return;
        }

        $booking_id = intval($_POST['booking_id']);
        $status = sanitize_text_field($_POST['status']);

        global $wpdb;
        $updated = $wpdb->update(
            $wpdb->prefix . 'rabbit_bookings',
            array('payment_status' => $status),
            array('id' => $booking_id)
        );

        if ($updated) {
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Failed to update payment status.', 'rabbit-booking'));
        }
    }

    public function delete_booking() {
        check_ajax_referer('rabbit_booking_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to perform this action.', 'rabbit-booking'));
            return;
        }

        $booking_id = intval($_POST['booking_id']);

        global $wpdb;
        $deleted = $wpdb->delete(
            $wpdb->prefix . 'rabbit_bookings',
            array('id' => $booking_id)
        );

        if ($deleted) {
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Failed to delete booking.', 'rabbit-booking'));
        }
    }

    public function update_cage_type() {
        check_ajax_referer('rabbit_booking_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to perform this action.', 'rabbit-booking'));
            return;
        }

        $cage_id = intval($_POST['cage_id']);
        $name = sanitize_text_field($_POST['name']);
        $description = sanitize_textarea_field($_POST['description']);
        $price_per_day = floatval($_POST['price_per_day']);
        $capacity = intval($_POST['capacity']);
        $total_available = intval($_POST['total_available']);

        global $wpdb;
        $updated = $wpdb->update(
            $wpdb->prefix . 'rabbit_cage_types',
            array(
                'name' => $name,
                'description' => $description,
                'price_per_day' => $price_per_day,
                'capacity' => $capacity,
                'total_available' => $total_available
            ),
            array('id' => $cage_id)
        );

        if ($updated !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Failed to update cage type.', 'rabbit-booking'));
        }
    }

    public function add_cage_type() {
        check_ajax_referer('rabbit_booking_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to perform this action.', 'rabbit-booking'));
            return;
        }

        $name = sanitize_text_field($_POST['name']);
        $description = sanitize_textarea_field($_POST['description']);
        $price_per_day = floatval($_POST['price_per_day']);
        $capacity = intval($_POST['capacity']);
        $total_available = intval($_POST['total_available']);

        global $wpdb;
        $inserted = $wpdb->insert(
            $wpdb->prefix . 'rabbit_cage_types',
            array(
                'name' => $name,
                'description' => $description,
                'price_per_day' => $price_per_day,
                'capacity' => $capacity,
                'total_available' => $total_available
            )
        );

        if ($inserted) {
            wp_send_json_success(array('id' => $wpdb->insert_id));
        } else {
            wp_send_json_error(__('Failed to add cage type.', 'rabbit-booking'));
        }
    }

    public function delete_cage_type() {
        check_ajax_referer('rabbit_booking_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to perform this action.', 'rabbit-booking'));
            return;
        }

        $cage_id = intval($_POST['cage_id']);

        // Check if cage type is in use
        global $wpdb;
        $is_in_use = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}rabbit_bookings WHERE cage_type_id = %d",
            $cage_id
        ));

        if ($is_in_use > 0) {
            wp_send_json_error(__('Cannot delete this cage type because it is used in existing bookings.', 'rabbit-booking'));
            return;
        }

        $deleted = $wpdb->delete(
            $wpdb->prefix . 'rabbit_cage_types',
            array('id' => $cage_id)
        );

        if ($deleted) {
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Failed to delete cage type.', 'rabbit-booking'));
        }
    }
}
