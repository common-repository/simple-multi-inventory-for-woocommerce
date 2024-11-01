<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://yourwcninja.com
 * @since      1.0.0
 *
 * @package    Smifw
 * @subpackage Smifw/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Smifw
 * @subpackage Smifw/public
 * @author     Your WC Ninja <yourwcninja@gmail.com>
 */
class Smifw_Public {

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
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/smifw-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/smifw-public.js', array(
			'jquery',
			'wp-i18n'
		), $this->version, false );

		$market_enabled         = apply_filters( 'smifw_enable_market', false );
		$location_price_enabled = apply_filters( 'smifw_price_enabled', true );

		wp_localize_script( $this->plugin_name, 'smifw', array(
			'price_enabled'  => $location_price_enabled ? 'yes' : 'no',
			'market_enabled' => $market_enabled ? 'yes' : 'no'
		) );
		wp_set_script_translations( $this->plugin_name, 'woocommerce' );
		wp_set_script_translations( $this->plugin_name, 'simple-multi-inventory-for-woocommerce' );

	}

	public function add_location_taxonomy() {
		$market = apply_filters( 'smifw_enable_market', false );
		$labels = array(
			'name'                       => _x( 'Stock Locations', 'Taxonomy General Name', 'simple-multi-inventory-for-woocommerce' ),
			'singular_name'              => _x( 'Stock Location', 'Taxonomy Singular Name', 'simple-multi-inventory-for-woocommerce' ),
			'menu_name'                  => __( 'Stock Location', 'simple-multi-inventory-for-woocommerce' ),
			'all_items'                  => __( 'All Locations', 'simple-multi-inventory-for-woocommerce' ),
			'parent_item'                => $market ? __( 'Market', 'simple-multi-inventory-for-woocommerce' ) : __( 'Parent Location', 'simple-multi-inventory-for-woocommerce' ),
			'parent_item_colon'          => __( 'Parent Location:', 'simple-multi-inventory-for-woocommerce' ),
			'new_item_name'              => __( 'New Location Name', 'simple-multi-inventory-for-woocommerce' ),
			'add_new_item'               => __( 'Add New Location', 'simple-multi-inventory-for-woocommerce' ),
			'edit_item'                  => __( 'Edit Location', 'simple-multi-inventory-for-woocommerce' ),
			'update_item'                => __( 'Update Location', 'simple-multi-inventory-for-woocommerce' ),
			'view_item'                  => __( 'View Location', 'simple-multi-inventory-for-woocommerce' ),
			'separate_items_with_commas' => __( 'Separate locations with commas', 'simple-multi-inventory-for-woocommerce' ),
			'add_or_remove_items'        => __( 'Add or remove locations', 'simple-multi-inventory-for-woocommerce' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'simple-multi-inventory-for-woocommerce' ),
			'popular_items'              => __( 'Popular Locations', 'simple-multi-inventory-for-woocommerce' ),
			'search_items'               => __( 'Search Locations', 'simple-multi-inventory-for-woocommerce' ),
			'not_found'                  => __( 'Not Found', 'simple-multi-inventory-for-woocommerce' ),
			'no_terms'                   => __( 'No locations', 'simple-multi-inventory-for-woocommerce' ),
			'items_list'                 => __( 'Locations list', 'simple-multi-inventory-for-woocommerce' ),
			'items_list_navigation'      => __( 'Locations list navigation', 'simple-multi-inventory-for-woocommerce' ),
		);
		$args   = array(
			'labels'            => $labels,
			'hierarchical'      => $market,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => false,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
		);
		register_taxonomy( 'smifw_location', array( 'product' ), $args );

	}

	public function stock_managing_key() {
		return apply_filters('smifw_stock_managing_key', array(
			'cart' => '_stock_location',
			'order' => '_stock_location',
			'default' => 'smifw_default_location',
		));
	}

	public function stock_regular_price( $price, $product ) {
		$keys = $this->stock_managing_key();
		$location_price_enabled = apply_filters( 'smifw_price_enabled', true );
		if ( ! $location_price_enabled ) {
			return $price;
		}

		$selected_location = apply_filters( $keys['default'], null, $product->get_id() );
		$selected_location = $selected_location ? get_term( $selected_location, 'smifw_location' ) : null;
		if ( ! $selected_location instanceof WP_Term ) {
			return $price;
		}

		$location_stocks = get_post_meta( $product->get_id(), 'smifw_location_stocks', true );
		if ( ! $location_stocks || ( $location_stocks && ! is_array( $location_stocks ) ) ) {
			$location_stocks = array();
		}

		return isset( $location_stocks[ $selected_location->slug ] ) && isset( $location_stocks[ $selected_location->slug ]['regular_price'] ) ? $location_stocks[ $location->slug ]['regular_price'] : $price;
	}

	public function stock_sale_price( $price, $product ) {
		$keys = $this->stock_managing_key();
		$location_price_enabled = apply_filters( 'smifw_price_enabled', true );
		if ( ! $location_price_enabled ) {
			return $price;
		}

		$selected_location = apply_filters( $keys['default'], null, $product->get_id() );
		$selected_location = $selected_location ? get_term( $selected_location, 'smifw_location' ) : null;
		if ( ! $selected_location instanceof WP_Term ) {
			return $price;
		}

		$location_stocks = get_post_meta( $product->get_id(), 'smifw_location_stocks', true );
		if ( ! $location_stocks || ( $location_stocks && ! is_array( $location_stocks ) ) ) {
			$location_stocks = array();
		}

		return isset( $location_stocks[ $selected_location->slug ] ) && isset( $location_stocks[ $selected_location->slug ]['sale_price'] ) ? $location_stocks[ $location->slug ]['sale_price'] : $price;
	}

	/**
	 * @param string $html
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	public function show_location_dropdown() {
		global $product;
		$show_dropdown = apply_filters( 'smifw_single_product_location_selector', true, $product->get_id() );
		if ( $show_dropdown && ! $product->is_type( 'variable' ) ) {
			echo $this->generate_select_box_for_product( $product );
		}
	}

	public function add_location_to_cart_item( $cart_item_data, $product_id, $variation_id ) {
		$default_location = apply_filters( 'smifw_default_location', null, ( $variation_id ?? $product_id ) );
		if ( isset( $_POST['_stock_location'] ) && ! empty( $_POST['_stock_location'] ) ) {
			$cart_item_data['_stock_location'] = wc_clean( $_POST['_stock_location'] );
		} elseif ( $default_location ) {
			$cart_item_data['_stock_location'] = wc_clean( $default_location );
		}

		if (!empty($cart_item_data['_stock_location']))  {
			$location = get_term_by('slug', esc_attr($cart_item_data['_stock_location']), 'smifw_location');
			if ($location instanceof WP_Term) {
				$cart_item_data['_stock_location_id'] = $location->term_id;
			}
		}

		return $cart_item_data;
	}

	public function show_location_in_cart( $item_data, $cart_item_data ) {
		if ( isset( $cart_item_data['_stock_location'] ) && ! empty( $cart_item_data['_stock_location'] ) ) {
			$location = get_term_by( 'slug', wc_clean( $cart_item_data['_stock_location'] ), 'smifw_location' );
			if ( $location instanceof WP_Term ) {
				$item_data[] = array(
					'key'   => __( 'Stock Location', 'simple-multi-inventory-for-woocommerce' ),
					'value' => esc_attr( $location->name )
				);
			}
		}

		return $item_data;
	}

	public function add_location_to_order( WC_Order_Item_Product $item, $cart_item_key, $values, $order ) {
		if ( isset( $values['_stock_location'] ) && ! empty( $values['_stock_location'] ) ) {
			$location = get_term_by( 'slug', wc_clean( $values['_stock_location'] ), 'smifw_location' );
			if ( $location instanceof WP_Term ) {
				$item->add_meta_data( '_stock_location_id', esc_attr( $location->term_id ) );
				$item->add_meta_data( '_stock_location', esc_attr( $location->slug ) );
			}
		}
	}

	public function remove_ajax_add_to_cart_for_locations( $is_supported, $feature, WC_Product $product ) {
		$keys = $this->stock_managing_key();
		$show_dropdown    = apply_filters( 'smifw_single_product_location_selector', true, $product->get_id() );
		$default_location = apply_filters( $keys['default'], null, $product->get_id() );

		if ( $show_dropdown && ! $default_location && ! $product->is_type( 'variable' ) && $feature === 'ajax_add_to_cart' && $is_supported && $product->managing_stock() ) {
			return false;
		}

		return $is_supported;
	}

	public function change_add_to_cart_url( $add_to_cart_url, WC_Product $product ) {
		$keys = $this->stock_managing_key();
		$show_dropdown    = apply_filters( 'smifw_single_product_location_selector', true, $product->get_id() );
		$default_location = apply_filters( $keys['default'], null, $product->get_id() );

		if ( $show_dropdown && ! $default_location && ! $product->is_type( 'variable' ) && $product->managing_stock() ) {
			return $product->get_permalink();
		}

		return $add_to_cart_url;
	}

	public function change_add_to_cart_text( $text, WC_Product $product ) {
		$keys = $this->stock_managing_key();
		$show_dropdown    = apply_filters( 'smifw_single_product_location_selector', true, $product->get_id() );
		$default_location = apply_filters( $keys['default'], null, $product->get_id() );

		if ( $show_dropdown && ! $default_location && ! $product->is_type( 'variable' ) && $product->managing_stock() ) {
			return __( 'Select location', 'simple-multi-inventory-for-woocommerce' );
		}

		return $text;
	}

	public function email_actions( $actions ) {
		$actions[] = 'smifw_no_stock';
		$actions[] = 'smifw_low_stock';
		$actions[] = 'smifw_product_on_backorder';

		return $actions;
	}

	/**
	 * Low stock notification email.
	 *
	 * @param WC_Product $product Product instance.
	 */
	public function low_stock( $product, $location, $new_quantity ) {
		if ( 'no' === get_option( 'woocommerce_notify_low_stock', 'yes' ) ) {
			return;
		}

		/**
		 * Determine if the current product should trigger a low stock notification
		 *
		 * @param int $product_id - The low stock product id
		 *
		 * @since 4.7.0
		 */
		if ( false === apply_filters( 'woocommerce_should_send_low_stock_notification', true, $product->get_id() ) ) {
			return;
		}

		$subject = sprintf( '[%s] %s', $this->get_blogname(), sprintf( __( 'Product low at %s', 'simple-multi-inventory-for-woocommerce' ), $location->name ) );
		$message = sprintf(
		/* translators: 1: product name 2: items in stock */
			__( '%1$s is low at %s. There are %2$d left.', 'simple-multi-inventory-for-woocommerce' ),
			html_entity_decode( wp_strip_all_tags( $product->get_formatted_name() ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
			html_entity_decode( wp_strip_all_tags( $location->name ) ),
			html_entity_decode( wp_strip_all_tags( $new_quantity ) )
		);

		wp_mail(
			apply_filters( 'woocommerce_email_recipient_low_stock', get_option( 'woocommerce_stock_email_recipient' ), $product, null ),
			apply_filters( 'woocommerce_email_subject_low_stock', $subject, $product, null ),
			apply_filters( 'woocommerce_email_content_low_stock', $message, $product ),
			apply_filters( 'woocommerce_email_headers', '', 'low_stock', $product, null ),
			apply_filters( 'woocommerce_email_attachments', array(), 'low_stock', $product, null )
		);
	}

	/**
	 * No stock notification email.
	 *
	 * @param WC_Product $product Product instance.
	 */
	public function no_stock( $product, $location ) {
		if ( 'no' === get_option( 'woocommerce_notify_no_stock', 'yes' ) ) {
			return;
		}

		/**
		 * Determine if the current product should trigger a no stock notification
		 *
		 * @param int $product_id - The out of stock product id
		 *
		 * @since 4.6.0
		 */
		if ( false === apply_filters( 'woocommerce_should_send_no_stock_notification', true, $product->get_id() ) ) {
			return;
		}

		$subject = sprintf( '[%s] %s', $this->get_blogname(), sprintf( __( 'Product out of stock at %s', 'simple-multi-inventory-for-woocommerce' ), $location->name ) );
		/* translators: %s: product name */
		$message = sprintf( __( '%s is out of stock at %s.', 'simple-multi-inventory-for-woocommerce' ), html_entity_decode( wp_strip_all_tags( $product->get_formatted_name() ), ENT_QUOTES, get_bloginfo( 'charset' ) ), html_entity_decode( wp_strip_all_tags( $location->name ), ENT_QUOTES, get_bloginfo( 'charset' ) ) );

		wp_mail(
			apply_filters( 'woocommerce_email_recipient_no_stock', get_option( 'woocommerce_stock_email_recipient' ), $product, null ),
			apply_filters( 'woocommerce_email_subject_no_stock', $subject, $product, null ),
			apply_filters( 'woocommerce_email_content_no_stock', $message, $product ),
			apply_filters( 'woocommerce_email_headers', '', 'no_stock', $product, null ),
			apply_filters( 'woocommerce_email_attachments', array(), 'no_stock', $product, null )
		);
	}

	/**
	 * @param WC_Order $order
	 *
	 * @return void
	 */
	public function reduce_location_stock( $order ) {
		$keys = $this->stock_managing_key();
		$changes = array();
		foreach ( $order->get_items() as $item ) {
			if ( ! $item->is_type( 'line_item' ) ) {
				continue;
			}

			// Only reduce stock once for each item.
			$product            = $item->get_product();
			$item_stock_reduced = $item->get_meta( '_reduced_location_stock', true );
			$stockLocation      = $item->get_meta( $keys['order'], true );
			if ( $item_stock_reduced || ! $product || ! $product->managing_stock() || ! $stockLocation ) {
				continue;
			}

			$location_stocks = get_post_meta( $product->get_id(), 'smifw_location_stocks', true );
			if ( ! $location_stocks || ( $location_stocks && ! is_array( $location_stocks ) ) ) {
				continue;
			}
			if ( isset( $location_stocks[ $stockLocation ] ) && isset( $location_stocks[ $stockLocation ]['stock'] ) ) {
			} else {
				continue;
			}

			$item_name = $product->get_formatted_name();
			$qty       = apply_filters( 'woocommerce_order_item_quantity', $item->get_quantity(), $order, $item );
			$new_stock = $location_stocks[ $stockLocation ]['stock'] - $qty;

			$location_stocks[ $stockLocation ]['stock'] = $new_stock;
			$update                                     = update_post_meta( $product->get_id(), 'smifw_location_stocks', $location_stocks );

			if ( ! $update ) {
				$order->add_order_note( sprintf( __( 'Unable to reduce location stock for item %s.', 'simple-multi-inventory-for-woocommerce' ), $item_name ) );
				continue;
			}

			$item->add_meta_data( '_reduced_location_stock', $qty, true );
			$item->save();

			$changes[] = array(
				'product'  => $product,
				'location' => $stockLocation,
				'from'     => $new_stock + $qty,
				'to'       => $new_stock,
			);
		}
		$this->trigger_stock_change_notifications( $order, $changes );
	}

	public function hide_stock_reduce_meta( $meta_keys ) {
		$meta_keys[] = '_reduced_location_stock';

		return $meta_keys;
	}

	public function change_order_item_meta_key( $display_key, $meta, WC_Order_Item $item ) {
		if ( $display_key === '_stock_location' ) {
			return __( 'Stock Location', 'simple-multi-inventory-for-woocommerce' );
		}

		return $display_key;
	}

	public function change_order_item_meta_value( $display_value, $meta, WC_Order_Item $item ) {
		if ( $meta->key === '_stock_location' ) {
			$term = get_term_by( 'slug', $meta->value, 'smifw_location' );
			if ( $term instanceof WP_Term ) {
				return esc_attr( $term->name );
			}
		}

		return $display_value;
	}

	// Variable Product
	public function add_location_stocks_to_variant( $variation_data, $product, $variation ) {
		$show_dropdown = apply_filters( 'smifw_single_product_location_selector', true, $variation->get_id() );
		if ( $show_dropdown ) {
			$variation_data['location_stocks_html'] = '<div class="smifw-location-and-market-selector">' . $this->generate_select_box_for_product( $variation ) . '</div>';
		}

		return $variation_data;
	}

	public function add_custom_price( WC_Cart $cart ) {
		$keys = $this->stock_managing_key();
		$location_price_enabled = apply_filters( 'smifw_price_enabled', true );

		if ( ! $location_price_enabled ) {
			return;
		}

		foreach ( $cart->get_cart_contents() as $cart_content ) {
			/* @var WC_Product_Simple|WC_Product_Variation $product */
			$product         = $cart_content['data'];
			$stockLocation   = $cart_content[$keys['cart']];
			$location_stocks = get_post_meta( $product->get_id(), 'smifw_location_stocks', true );
			if ( ! $location_stocks || ( $location_stocks && ! is_array( $location_stocks ) ) ) {
				continue;
			}
			if (
				$product->managing_stock() &&
				$stockLocation &&
				isset( $location_stocks[ $stockLocation ] )
			) {
				if ( isset( $location_stocks[ $stockLocation ]['sale_price'] ) && is_numeric( $location_stocks[ $stockLocation ]['sale_price'] ) ) {
					$cart_content['data']->set_price( $location_stocks[ $stockLocation ]['sale_price'] );
				} else if ( isset( $location_stocks[ $stockLocation ]['regular_price'] ) && is_numeric( $location_stocks[ $stockLocation ]['regular_price'] ) ) {
					$cart_content['data']->set_price( $location_stocks[ $stockLocation ]['regular_price'] );
				}
			}
		}
	}

	public function cart_item_price( $price, $cart_item, $cart_item_key ) {
		return floatval( $cart_item['data']->get_price() );
	}

	private function trigger_stock_change_notifications( $order, $changes ) {
		if ( empty( $changes ) ) {
			return;
		}

		$order_notes     = array();
		$no_stock_amount = absint( get_option( 'woocommerce_notify_no_stock_amount', 0 ) );

		foreach ( $changes as $change ) {
			$location         = get_term_by( 'slug', $change['location'], 'smifw_location' );
			$order_notes[]    = $change['product']->get_formatted_name() . '(' . $location->name . ') ' . $change['from'] . '&rarr;' . $change['to'];
			$low_stock_amount = absint( wc_get_low_stock_amount( wc_get_product( $change['product']->get_id() ) ) );
			if ( $change['to'] <= $no_stock_amount ) {
				do_action( 'smifw_no_stock', wc_get_product( $change['product']->get_id() ), $location, $change['to'] );
			} elseif ( $change['to'] <= $low_stock_amount ) {
				do_action( 'smifw_low_stock', wc_get_product( $change['product']->get_id() ), $location, $change['to'] );
			}

			if ( $change['to'] < 0 ) {
				do_action(
					'smifw_product_on_backorder',
					array(
						'product'  => wc_get_product( $change['product']->get_id() ),
						'location' => $location,
						'order_id' => $order->get_id(),
						'quantity' => abs( $change['from'] - $change['to'] ),
					)
				);
			}
		}

		$order->add_order_note( __( 'Location Stock levels reduced:', 'simple-multi-inventory-for-woocommerce' ) . ' ' . implode( ', ', $order_notes ) );
	}

	private function generate_select_box_for_product( WC_Product $product ) {
		$html = '';
		if ( $product->managing_stock() ) {
			$market = apply_filters( 'smifw_enable_market', false );
			if ( ! self::$locations ) {
				$p = array(
					'taxonomy'   => 'smifw_location',
					'hide_empty' => false,
				);
				if ( $market ) {
					$p['parent'] = 0;
				}
				self::$locations = get_terms( $p );

				if ( $market ) {
					self::$locations = array_map( function ( WP_Term $term ) use ( $p ) {
						$p['parent'] = $term->term_id;
						$term->terms = get_terms( $p );
						return $term;
					}, self::$locations );
				}
			}
			$locations       = self::$locations;
			$location_stocks = get_post_meta( $product->get_id(), 'smifw_location_stocks', true );
			if ( ! $location_stocks || ( $location_stocks && ! is_array( $location_stocks ) ) ) {
				$location_stocks = array();
			}

			$location_price_enabled = apply_filters( 'smifw_price_enabled', true );
			if ( is_array( $locations ) && count( $locations ) ) {
				$hide = apply_filters( 'smifw_hide_selector', false, $product->get_id() );
				if ($market) {
					ob_start();
					do_action('smifw_market_selector_on_product', $product, $locations, $location_stocks);
					$html .= ob_get_clean();
				}

				$select   = '<div class="stock-location-selector location-selector-' . $product->get_id() . '" data-product="' . $product->get_id() . '" data-product_type="' . $product->get_type() . '"' . ( $hide ? 'style="display: none"' : '' ) . '>';
				if (! $market) {
					$select .= self::generate_location_selector($product, $locations, $location_stocks);
				}
				$select .= '</div>';

				$html .= $select;
			}
		}

		return $html;
	}

	public static function generate_location_selector(WC_Product $product, $locations, $location_stocks) {
		$location_price_enabled = apply_filters( 'smifw_price_enabled', true );
		$price_container = '';
		if ($location_price_enabled) {
			$price_container = '<div class="smifw-prices-' . esc_attr($product->get_id()) . '" style="display: none;">';
		}
		$select   = '<label for="_stock_location">' . __( 'Stock Location', 'simple-multi-inventory-for-woocommerce' ) . '</label><select id="_stock_location" class="stock_location" name="_stock_location">';
		$select   .= sprintf( '<option value="%s" data-stock="%d">%s</option>', esc_attr( '' ), 0, esc_html__( 'Select Location', 'simple-multi-inventory-for-woocommerce' ) );
		$selected = apply_filters( 'smifw_default_location', ( isset( $_REQUEST['_stock_location'] ) && ! empty( $_REQUEST['_stock_location'] ) ? wc_clean( wp_unslash( $_REQUEST['_stock_location'] ) ) : null ), $product->get_id() );
		foreach ( $locations as $location ) {
			$c = isset( $location_stocks[ $location->slug ] ) && isset( $location_stocks[ $location->slug ]['stock'] ) ? wc_stock_amount( $location_stocks[ $location->slug ]['stock'] ) : 0;
			if ( $location_price_enabled ) {
				$select .= sprintf( '<option value="%s" data-stock="%d" data-regular_price="%s" data-sale_price="%s" %s>%s</option>', esc_attr( $location->slug ), $c, esc_attr( $location_stocks[ $location->slug ]['regular_price'] ?? 0 ), esc_attr( $location_stocks[ $location->slug ]['sale_price'] ?? 0 ), selected( $selected, esc_attr( $location->slug ), false ), esc_html( $location->name ) );
				$price_container .= '<div class="smifw-regular-price-' . $location->slug . '">' . wc_price( esc_attr( $location_stocks[ $location->slug ]['regular_price'] ?? 0 ) ) . '</div><div class="smifw-sale-price-' . $location->slug . '">' . wc_price( esc_attr( $location_stocks[ $location->slug ]['sale_price'] ?? 0 ) ) . '</div>';
			} else {
				$select .= sprintf( '<option value="%s" data-stock="%d" %s>%s</option>', esc_attr( $location->slug ), $c, selected( $selected, esc_attr( $location->slug ), false ), esc_html( $location->name ) );
			}
		}

		if ($location_price_enabled) {
			$price_container .= '</div>';
		}
		$select .= '</select>';

		return $select . $price_container;
	}

}
