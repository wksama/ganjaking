<?php
/**
 * WC Order Barcodes function file.
 *
 * @package woocommerce-order-barcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wc_order_barcode' ) ) {
	/**
	 * Fetch a barcode for a given order
	 *
	 * @param int    $order_id Order ID.
	 * @param string $before   Content to display before the barcode.
	 * @param string $after    Content to display after the barcode.
	 *
	 * @return string            Order barcode
	 */
	function wc_order_barcode( $order_id = 0, $before = '', $after = '' ) {
		return $before . WC_Order_Barcodes()->display_barcode( $order_id, false ) . $after;
	}
}

/**
 * Returns the main instance of WooCommerce_Order_Barcodes to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WooCommerce_Order_Barcodes instance
 */
function WC_Order_Barcodes() { // phpcs:ignore
	$instance = WooCommerce_Order_Barcodes::instance();
	if ( is_null( $instance->settings ) ) {
		$instance->settings = WooCommerce_Order_Barcodes_Settings::instance( $instance );
	}
	return $instance;
}
