<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://yourwcninja.com
 * @since      1.0.0
 *
 * @package    Smifw
 * @subpackage Smifw/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Smifw
 * @subpackage Smifw/admin
 * @author     Your WC Ninja <yourwcninja@gmail.com>
 */
class Smifw_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	private static $locations;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smifw_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smifw_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/smifw-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smifw_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smifw_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/smifw-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function check_woocommerce_is_active() {
		if ( ! class_exists( 'woocommerce' ) ) {
			include_once 'partials/woocommerce-is-not-active.php';
		}
	}

	public function stock_fields() {
		global $post;
		$market = apply_filters( 'smifw_enable_market', false );
		if ( ! self::$locations ) {
			$p = array(
				'taxonomy'   => 'smifw_location',
				'hide_empty' => false,
			);
			if ($market) {
				$p['parent'] = 0;
			}
			self::$locations = get_terms( $p );
		}
		$locations       = self::$locations;
		$location_stocks = get_post_meta( $post->ID, 'smifw_location_stocks', true );
		if ( ! $location_stocks || ( $location_stocks && ! is_array( $location_stocks ) ) ) {
			$location_stocks = array();
		}
		$location_price_enabled = apply_filters('smifw_price_enabled', true);
		include_once __DIR__ . '/partials/admin-location-stock-edit.php';
	}

	public function variable_stock_fields( $loop, $variation_data, $variation ) {
		$market = apply_filters( 'smifw_enable_market', false );
		if ( ! self::$locations ) {
			$p = array(
				'taxonomy'   => 'smifw_location',
				'hide_empty' => false,
			);
			if ($market) {
				$p['parent'] = 0;
			}
			self::$locations = get_terms( $p );
		}
		$locations       = self::$locations;
		$location_stocks = get_post_meta( $variation->ID, 'smifw_location_stocks', true );
		if ( ! $location_stocks || ( $location_stocks && ! is_array( $location_stocks ) ) ) {
			$location_stocks = array();
		}
		$location_price_enabled = apply_filters('smifw_price_enabled', true);
		if ( is_array( $locations ) && count( $locations ) ) {
			foreach ( $locations as $index => $location ) {
				woocommerce_wp_text_input(
					array(
						'id'                => "variable_location_stock{$loop}-{$location->slug}",
						'name'              => "variable_smifw_location_stock[{$loop}][{$location->slug}][stock]",
						'value'             => wc_stock_amount( isset( $location_stocks[ $location->slug ] ) && isset( $location_stocks[ $location->slug ]['stock'] ) ? wc_stock_amount( $location_stocks[ $location->slug ]['stock'] ) : '' ),
						'label'             => sprintf( __( 'Stock of %s', 'simple-multi-inventory-for-woocommerce' ), $location->name ),
						'desc_tip'          => true,
						'description'       => sprintf( __( 'Stock quantity of %s', 'simple-multi-inventory-for-woocommerce' ), $location->name ),
						'type'              => 'number',
						'custom_attributes' => array(
							'step' => 'any',
						),
						'data_type'         => 'stock',
						'wrapper_class'     => 'form-row form-row-first',
					)
				);
				if ($location_price_enabled) {
					woocommerce_wp_text_input(
						array(
							'id'            => "variable_location_regular_price{$loop}-{$location->slug}",
							'name'          => "variable_smifw_location_stock[{$loop}][{$location->slug}][regular_price]",
							'value'         => isset( $location_stocks[ $location->slug ] ) && isset( $location_stocks[ $location->slug ]['regular_price'] ) ? $location_stocks[ $location->slug ]['regular_price'] : '',
							'label'         => sprintf( __( 'Regular price of %s', 'simple-multi-inventory-for-woocommerce' ), $location->name ),
							'data_type'     => 'price',
							'wrapper_class' => 'form-row form-row-last'
						)
					);

					woocommerce_wp_text_input(
						array(
							'id'            => "variable_location_sale_price{$loop}-{$location->slug}",
							'name'          => "variable_smifw_location_stock[{$loop}][{$location->slug}][sale_price]",
							'value'         => isset( $location_stocks[ $location->slug ] ) && isset( $location_stocks[ $location->slug ]['sale_price'] ) ? $location_stocks[ $location->slug ]['sale_price'] : '',
							'label'         => sprintf( __( 'Sale price of %s', 'simple-multi-inventory-for-woocommerce' ), $location->name ),
							'data_type'     => 'price',
							'wrapper_class' => 'form-row form-row-first',
						)
					);
				}
			}
		}
	}

	public function process_product_meta() {
		global $post;
		$location_price_enabled = apply_filters('smifw_price_enabled', true);
		if ( isset( $_POST['smifw_location_stocks'] ) && is_array( $_POST['smifw_location_stocks'] ) ) {
			$location_stocks = array_map( function ( $location_stock ) use ($location_price_enabled) {
				$location_stock['stock'] = wc_stock_amount( $location_stock['stock'] );
				if ($location_price_enabled) {
					$location_stock['regular_price'] = wc_clean( $location_stock['regular_price'] );
					$location_stock['sale_price'] = wc_clean( $location_stock['sale_price'] );
				}

				return $location_stock;
			}, (array) wp_unslash( $_POST['smifw_location_stocks'] ) );
			$total_stock     = array_reduce( $location_stocks, function ( $carry, $item ) {
				return $carry + $item['stock'];
			}, 0 );
			update_post_meta( $post->ID, '_stock', $total_stock );
			update_post_meta( $post->ID, 'smifw_location_stocks', $location_stocks );
		}
	}

	public function save_product_variation( $variation_id, $i ) {
		$location_price_enabled = apply_filters('smifw_price_enabled', true);

		if ( isset( $_POST['variable_smifw_location_stock'] ) && isset( $_POST['variable_smifw_location_stock'][ $i ] ) && is_array( $_POST['variable_smifw_location_stock'][ $i ] ) ) {
			$location_stocks = array_map( function ( $location_stock ) use ($location_price_enabled) {
				$location_stock['stock'] = wc_stock_amount( $location_stock['stock'] );
				if ($location_price_enabled) {
					$location_stock['regular_price'] = wc_clean( $location_stock['regular_price'] );
					$location_stock['sale_price'] = wc_clean( $location_stock['sale_price'] );
				}

				return $location_stock;
			}, (array) wp_unslash( $_POST['variable_smifw_location_stock'][ $i ] ) );
			$total_stock     = array_reduce( $location_stocks, function ( $carry, $item ) {
				return $carry + $item['stock'];
			}, 0 );
			update_post_meta( $variation_id, '_stock', $total_stock );
			update_post_meta( $variation_id, 'smifw_location_stocks', $location_stocks );
		}
	}
}
