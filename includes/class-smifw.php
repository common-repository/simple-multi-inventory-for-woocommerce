<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://yourwcninja.com
 * @since      1.0.0
 *
 * @package    Smifw
 * @subpackage Smifw/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Smifw
 * @subpackage Smifw/includes
 * @author     Your WC Ninja <yourwcninja@gmail.com>
 */
class Smifw {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Smifw_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SMIFW_VERSION' ) ) {
			$this->version = SMIFW_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'simple-multi-inventory-for-woocommerce';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Smifw_Loader. Orchestrates the hooks of the plugin.
	 * - Smifw_i18n. Defines internationalization functionality.
	 * - Smifw_Admin. Defines all hooks for the admin area.
	 * - Smifw_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smifw-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smifw-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-smifw-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-smifw-public.php';

		$this->loader = new Smifw_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Smifw_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Smifw_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Smifw_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_notices', $plugin_admin, 'check_woocommerce_is_active' );

		$this->loader->add_action( 'woocommerce_product_options_stock_fields', $plugin_admin, 'stock_fields' );
		$this->loader->add_action( 'woocommerce_variation_options_inventory', $plugin_admin, 'variable_stock_fields', 10, 3 );
		$this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin, 'process_product_meta' );
		$this->loader->add_action( 'woocommerce_save_product_variation', $plugin_admin, 'save_product_variation', 10, 2 );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Smifw_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_public, 'add_location_taxonomy' );
		$this->loader->add_action( 'woocommerce_before_add_to_cart_button', $plugin_public, 'show_location_dropdown' );
		$this->loader->add_filter( 'woocommerce_add_cart_item_data', $plugin_public, 'add_location_to_cart_item', 10, 3 );
		$this->loader->add_filter( 'woocommerce_get_item_data', $plugin_public, 'show_location_in_cart', 10, 2 );
		$this->loader->add_action( 'woocommerce_checkout_create_order_line_item', $plugin_public, 'add_location_to_order', 10, 4 );
		$this->loader->add_filter( 'woocommerce_order_item_display_meta_key', $plugin_public, 'change_order_item_meta_key', 10, 3 );
		$this->loader->add_filter( 'woocommerce_order_item_display_meta_value', $plugin_public, 'change_order_item_meta_value', 10, 3 );
		$this->loader->add_filter( 'woocommerce_available_variation', $plugin_public, 'add_location_stocks_to_variant', 10, 3 );
		$this->loader->add_filter( 'woocommerce_product_supports', $plugin_public, 'remove_ajax_add_to_cart_for_locations', 10, 3 );
		$this->loader->add_filter( 'woocommerce_product_add_to_cart_url', $plugin_public, 'change_add_to_cart_url', 20, 2 );
		$this->loader->add_filter( 'woocommerce_product_add_to_cart_text', $plugin_public, 'change_add_to_cart_text', 20, 2 );
		$this->loader->add_action( 'woocommerce_reduce_order_stock', $plugin_public, 'reduce_location_stock' );

		$this->loader->add_filter( 'woocommerce_product_get_regular_price', $plugin_public, 'stock_regular_price', 10, 2 );
		$this->loader->add_filter( 'woocommerce_product_get_sale_price', $plugin_public, 'stock_sale_price', 10, 2 );

		$this->loader->add_filter( 'woocommerce_email_actions', $plugin_public, 'email_actions' );
		$this->loader->add_action( 'smifw_no_stock_notification', $plugin_public, 'low_stock' );
		$this->loader->add_action( 'smifw_low_stock_notification', $plugin_public, 'no_stock' );

		$this->loader->add_filter( 'woocommerce_hidden_order_itemmeta', $plugin_public, 'hide_stock_reduce_meta' );

		$this->loader->add_action( 'woocommerce_before_calculate_totals', $plugin_public, 'add_custom_price' );
		$this->loader->add_filter( 'woocommerce_cart_item_price', $plugin_public, 'cart_item_price', 10, 3 );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Smifw_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

}
