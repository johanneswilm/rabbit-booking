<?php
/**
 * The core plugin class.
 *
 * @since      1.0.0
 */
class Rabbit_Booking {

    /**
     * The admin instance.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Rabbit_Booking_Admin    $admin    The admin instance.
     */
    protected $admin;

    /**
     * The public instance.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Rabbit_Booking_Public    $public    The public instance.
     */
    protected $public;

    /**
     * Initialize the plugin.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        require_once RABBIT_BOOKING_PLUGIN_DIR . 'includes/class-rabbit-booking-admin.php';
        require_once RABBIT_BOOKING_PLUGIN_DIR . 'includes/class-rabbit-booking-public.php';
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $this->admin = new Rabbit_Booking_Admin();
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $this->public = new Rabbit_Booking_Public();

        add_action('wp_enqueue_scripts', array($this->public, 'enqueue_scripts'));
        add_action('init', array($this->public, 'register_shortcodes'));
        $this->public->register_ajax_handlers();
    }

    /**
     * Run the plugin.
     *
     * @since    1.0.0
     */
    public function run() {
        // The plugin is now running
    }
}
