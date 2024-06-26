<?php


class WC_GFPA_Order {

	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WC_GFPA_Order();
		}
	}

	public function __construct() {
		add_filter( 'woocommerce_order_item_get_formatted_meta_data', array(
			$this,
			'on_get_woocommerce_order_item_get_formatted_meta_data'
		), 10, 2 );

		add_action( 'woocommerce_after_order_itemmeta', array( $this, 'custom_order_item_meta' ), 10, 3 );
	}

	/**
	 *
	 * This is in place because WC sanitizes meta values via the sanatize_text_field, which strips HTML.
	 *
	 * @param $meta
	 * @param $order_item
	 */
	public function on_get_woocommerce_order_item_get_formatted_meta_data( $meta, $order_item ) {

		foreach ( $meta as $item_id => &$value ) {
			if ( empty( $value->display_value ) ) {

				if ( ! empty( $value->value ) && ( strpos( $value->value, '<img' ) !== false || strpos( $value->value, '<a' ) !== false ) ) {
					$value->display_value = $value->value;
				}

			}

			if ( strpos( $value->key, '_gf_email_hidden_' ) === 0 ) {
				$value->display_key = str_replace( '_gf_email_hidden_', '', $value->display_key );
			}
		}

		return $meta;

	}

	public function custom_order_item_meta( $item_id, $item, $product ) {

		if ( is_object( $item ) ) {
			$meta_data_items = $item->get_meta_data();
			foreach ( $meta_data_items as $meta ) {
				if ( $meta->key == '_gravity_forms_history' ) {
					$entry_id = isset( $meta->value['_gravity_form_linked_entry_id'] ) ? $meta->value['_gravity_form_linked_entry_id'] : false;

					if ( $entry_id ) {

						$entry = GFAPI::get_entry( $entry_id );
						if ( $entry && ! is_wp_error( $entry ) ) {
							echo '<div class="view">';
							echo '<a href="' . admin_url( 'admin.php?page=gf_entries&view=entry&id=' . $entry['form_id'] . '&lid=' . $entry_id ) . '">' . __( 'View Gravity Form Entry', 'wc_gf_addons' ) . '</a>';
							echo '</div>';
						}
					}

				}
			}
		}

	}
}
