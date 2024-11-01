(function ($) {
    'use strict';

    /**
     * All of the code for your public-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    function adjust_stock(container) {
        const select = container.find('select')
        const locationSlug = select.val()
        const productId = container.data('product')
        const productType = container.data('product_type')
        if (locationSlug) {
            const selectedOption = select.find('option[value="' + locationSlug + '"]')
            if (selectedOption) {
                const stock = Number(selectedOption.data('stock'))
                const regular_price = Number(selectedOption.data('regular_price'))
                const sale_price = Number(selectedOption.data('sale_price'))

                const regular_price_html = $('.smifw-prices-' + productId + ' .smifw-regular-price-' + locationSlug).html()
                const sale_price_html = $('.smifw-prices-' + productId + ' .smifw-sale-price-' + locationSlug).html()

                if (stock && stock > 0) {
                    $(document).trigger('location_stock_selected', {
                        stock,
                        regular_price,
                        sale_price,
                        regular_price_html,
                        sale_price_html,
                        productType,
                        type: 'in_stock'
                    })
                } else {
                    $(document).trigger('location_stock_selected', {
                        stock,
                        regular_price,
                        sale_price,
                        regular_price_html,
                        sale_price_html,
                        productType,
                        type: 'out_of_stock'
                    })
                }
            }
        }
    }

    function mustSelectLocation(container) {
        const locationSlug = container.find('select').val()
        if (container.length && !locationSlug) {
            $('p.stock').hide();
            if (smifw.price_enabled === 'yes') {
                $('p.price').hide();
            }
            $('.single_add_to_cart_button').attr('type', 'button').addClass('disabled').addClass('must-select-location');
        } else if (container.length && locationSlug) {
            adjust_stock(container)
        } else {
            $('p.stock').show();
            if (smifw.price_enabled === 'yes') {
                $('p.price').show();
            }
            $('.single_add_to_cart_button:not(.wc-variation-selection-needed)').attr('type', 'submit').removeClass('disabled').removeClass('must-select-location');
        }
    }

    $(document).ready(function () {
        if (document.querySelectorAll('.stock-location-selector').length) {
            document.querySelectorAll('.stock-location-selector').forEach(function (el) {
                mustSelectLocation($(el))
            })
        }
    })

    $(document).on('input', '.stock_location', function (e) {
        mustSelectLocation($(this).parent())
    })

    // Variable Product
    $(document).on('show_variation', function (event, variation) {
        const markup = variation.location_stocks_html
        $('.woocommerce-variation-add-to-cart .smifw-location-and-market-selector').remove()
        if (markup) {
            $('.woocommerce-variation-add-to-cart').prepend(markup)
            $(document).trigger('market_or_location_box_appeared', variation.variation_id)
        }
        $(document).trigger('location_stock_select_changed', variation.variation_id)
    })

    $(document).on('hide_variation', function () {
        $('.woocommerce-variation-add-to-cart .smifw-location-and-market-selector').remove()
        $(document).trigger('location_stock_select_changed', null)
    })

    $(document).on('location_stock_select_changed', function (e, data) {
        mustSelectLocation($('.location-selector-' + data))
    })

    $(document).on('location_stock_selected', function (e, data) {
        if (data.type === 'in_stock') {
            $('p.stock').removeClass('out-of-stock').addClass('in-stock').text(sprintf(wp.i18n.__('%s in stock', 'woocommerce'), data.stock)).show();
            $('.single_add_to_cart_button:not(.wc-variation-selection-needed)').attr('type', 'submit').removeClass('disabled').removeClass('must-select-location');
        } else if (data.type === 'out_of_stock') {
            $('p.stock').removeClass('in-stock').addClass('out-of-stock').text(wp.i18n.__('Out of stock', 'woocommerce')).show();
            $('.single_add_to_cart_button').attr('type', 'button').addClass('disabled').addClass('must-select-location');
        }

        if (smifw.price_enabled === 'yes') {
            let priceHtml = ''
            if (data.sale_price && data.regular_price) {
                priceHtml += '<del>' + data.regular_price_html + '</del><ins>' + data.sale_price_html + '</ins>'
            } else if (data.regular_price) {
                priceHtml += '<del>' + data.regular_price_html + '</del>'
            }
            $('.price').html(priceHtml).show()
        }
    })

    $(document).on('click', '.must-select-location', function (e) {
        alert(wp.i18n.__('You must select stock location', 'simple-multi-inventory-for-woocommerce'))
    })
})(jQuery);
