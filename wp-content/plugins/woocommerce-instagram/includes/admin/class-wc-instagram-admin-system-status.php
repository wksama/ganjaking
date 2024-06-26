<?php
/**
 * Instagram System Status.
 *
 * Adds extra information related to WooCommerce Instagram to the system status report.
 *
 * @package WC_Instagram/Admin
 * @since   3.6.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Instagram_Admin_System_Status
 */
class WC_Instagram_Admin_System_Status {

	/**
	 * Init.
	 *
	 * @since 3.6.1
	 */
	public static function init() {
		add_action( 'woocommerce_system_status_report', array( __CLASS__, 'output_content' ) );
	}

	/**
	 * Outputs the Instagram content in the system status report.
	 *
	 * @since 3.6.1
	 */
	public static function output_content() {
		$data = array(
			'is_connected'               => wc_instagram_is_connected(),
			'has_page'                   => wc_instagram_has_business_account(),
			'catalog_permalink'          => wc_instagram_get_setting( 'product_catalog_permalink', 'product-catalog/' ),
			'catalogs'                   => wc_instagram_get_product_catalogs( array(), 'objects' ),
			'catalogs_interval'          => wc_instagram_get_setting( 'generate_catalogs_interval', 1 ),
			'catalog_directory'          => WC_INSTAGRAM_CATALOGS_PATH,
			'catalog_directory_writable' => (bool) @fopen( WC_INSTAGRAM_CATALOGS_PATH . 'test.log', 'a' ), // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		);

		include_once __DIR__ . '/views/html-admin-status-report-settings.php';

		if ( $data['is_connected'] ) {
			include_once __DIR__ . '/views/html-admin-status-report-catalogs.php';
		}
	}
}

WC_Instagram_Admin_System_Status::init();
