<?php
/**
 * Plugin Name: Rabbit Booking System
 * Plugin URI: https://www.gunnesbo4h.se/
 * Description: Booking system for rabbit cages at 4H farm of Gunnesbo
 * Version: 1.0.0
 * Author: 4H Gunnesbo
 * Author URI: https://www.gunnesbo4h.se/
 * Text Domain: rabbit-booking
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Load plugin text domain for translations
function rabbit_booking_load_textdomain() {
    load_plugin_textdomain('rabbit-booking', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'rabbit_booking_load_textdomain');

// Define plugin constants
define('RABBIT_BOOKING_VERSION', '1.0.0');
define('RABBIT_BOOKING_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RABBIT_BOOKING_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once RABBIT_BOOKING_PLUGIN_DIR . 'includes/class-rabbit-booking.php';

function activate_rabbit_booking() {
    require_once RABBIT_BOOKING_PLUGIN_DIR . 'includes/class-rabbit-booking-database.php';
    $database = new Rabbit_Booking_Database();
    $database->create_tables();
}

function deactivate_rabbit_booking() {
    // Deactivation tasks if needed
}

// Start the plugin
function run_rabbit_booking() {
    $plugin = new Rabbit_Booking();
    $plugin->run();
}

// Activation and deactivation hooks
register_activation_hook(__FILE__, 'activate_rabbit_booking');
register_deactivation_hook(__FILE__, 'deactivate_rabbit_booking');

run_rabbit_booking();
