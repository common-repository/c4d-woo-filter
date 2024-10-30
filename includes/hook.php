<?php
add_action( 'woocommerce_before_shop_loop', 'c4d_woo_filter_products_wrap_open');
add_action( 'woocommerce_close_shop_loop', 'c4d_woo_filter_products_wrap_close');
add_action( 'woocommerce_shortcode_before_products_loop', 'c4d_woo_filter_products_wrap_open' );
add_action( 'woocommerce_shortcode_after_products_loop', 'c4d_woo_filter_products_wrap_open' );

function c4d_woo_filter_products_wrap_open() {
  echo '<div class="c4d_woo_filter_products_wrap">';
}
function c4d_woo_filter_products_wrap_close() {
  echo '</div>';
}