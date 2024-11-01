<?php if ( is_array( $locations ) && count( $locations ) ): ?>
	<?php foreach ( $locations as $location ): ?>
        <div class="options_group">
            <h4><?php echo sprintf( __( 'Stock Of %s', 'simple-multi-inventory-for-woocommerce' ), esc_attr( $location->name ) ); ?></h4>
			<?php
			woocommerce_wp_text_input(
				array(
					'id'                => 'location_' . $location->term_id . '_stock',
					'name'              => sprintf( 'smifw_location_stocks[%s][stock]', $location->slug ),
					'value'             => wc_stock_amount( isset( $location_stocks[ $location->slug ] ) && isset( $location_stocks[ $location->slug ]['stock'] ) ? wc_stock_amount( $location_stocks[ $location->slug ]['stock'] ) : '' ),
					'label'             => __( 'Stock', 'simple-multi-inventory-for-woocommerce' ),
					'desc_tip'          => true,
					'description'       => sprintf( __( 'Stock quantity of %s', 'simple-multi-inventory-for-woocommerce' ), $location->name ),
					'type'              => 'number',
					'custom_attributes' => array(
						'step' => 'any',
					),
					'data_type'         => 'stock',
				)
			);
            if ($location_price_enabled) {
	            woocommerce_wp_text_input(
		            array(
			            'id'        => 'location_' . $location->term_id . '_regular_price',
			            'name'      => sprintf( 'smifw_location_stocks[%s][regular_price]', $location->slug ),
			            'value'     => esc_attr( isset( $location_stocks[ $location->slug ] ) && isset( $location_stocks[ $location->slug ]['regular_price'] ) ? $location_stocks[ $location->slug ]['regular_price'] : '' ),
			            'label'     => __( 'Regular price', 'woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
			            'data_type' => 'price',
		            )
	            );
	            woocommerce_wp_text_input(
		            array(
			            'id'        => 'location_' . $location->term_id . '_sale_price',
			            'name'      => sprintf( 'smifw_location_stocks[%s][sale_price]', $location->slug ),
			            'value'     => esc_attr( isset( $location_stocks[ $location->slug ] ) && isset( $location_stocks[ $location->slug ]['sale_price'] ) ? $location_stocks[ $location->slug ]['sale_price'] : '' ),
			            'data_type' => 'price',
			            'label'     => __( 'Sale price', 'woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
		            )
	            );
            }
			?>
        </div>
	<?php endforeach; ?>
<?php endif; ?>