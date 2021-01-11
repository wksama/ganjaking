<?php
/**
 * Class to handle feature Coupons By Taxonomy
 *
 * @author      StoreApps
 * @category    Admin
 * @package     wocommerce-smart-coupons/includes
 * @since       4.13.0
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Coupons_By_Taxonomy' ) ) {

	/**
	 * Class WC_SC_Coupons_By_Taxonomy
	 */
	class WC_SC_Coupons_By_Taxonomy {

		/**
		 * Variable to hold instance of this class
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		private function __construct() {

			add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'usage_restriction' ), 99, 2 );
			add_action( 'wp_ajax_taxonomy_restriction_row_html', array( $this, 'ajax_taxonomy_restriction_row_html' ) );
			add_action( 'wp_ajax_taxonomy_restriction_select_tag_html', array( $this, 'ajax_taxonomy_restriction_select_tag_html' ) );
			add_action( 'wp_ajax_default_taxonomy_restriction_row_html', array( $this, 'ajax_default_taxonomy_restriction_row_html' ) );
			add_action( 'admin_footer', array( $this, 'styles_and_scripts' ) );
			add_action( 'save_post', array( $this, 'process_meta' ), 10, 2 );
			add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'validate' ), 11, 4 );
			add_filter( 'woocommerce_coupon_is_valid', array( $this, 'handle_non_product_type_coupons' ), 11, 3 );
			add_filter( 'is_protected_meta', array( $this, 'make_action_meta_protected' ), 10, 3 );
		}

		/**
		 * Get single instance of this class
		 *
		 * @return this class Singleton object of this class
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name = '', $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Display field for coupon by payment method
		 *
		 * @param integer   $coupon_id The coupon id.
		 * @param WC_Coupon $coupon    The coupon object.
		 */
		public function usage_restriction( $coupon_id = 0, $coupon = null ) {

			$taxonomy_to_label = $this->get_taxonomy_with_label();
			$terms             = $this->get_terms_grouped_by_taxonomy();
			$operators         = array(
				'incl' => __( 'Include', 'woocommerce-smart-coupons' ),
				'excl' => __( 'Exclude', 'woocommerce-smart-coupons' ),
			);

			$taxonomy_restrictions = get_post_meta( $coupon_id, 'wc_sc_taxonomy_restrictions', true );

			?>
			<div class="options_group smart-coupons-field wc_sc_taxonomy_restrictions">
				<?php
				if ( empty( $taxonomy_restrictions ) ) {
					$this->get_default_taxonomy_restriction_row();
				}
				if ( ! empty( $taxonomy_restrictions ) ) {
					$count = count( $taxonomy_restrictions );
					for ( $i = 0; $i < $count; $i++ ) {
						$args = array(
							'index'                => $i,
							'taxonomy_restriction' => $taxonomy_restrictions[ $i ],
							'taxonomy_to_label'    => $taxonomy_to_label,
							'operators'            => $operators,
							'terms'                => $terms,
						);
						$this->get_taxonomy_restriction_row( $args );
					}
				}
				?>
			</div>
			<?php
		}

		/**
		 * Get default taxonomy restriction row
		 */
		public function get_default_taxonomy_restriction_row() {
			?>
			<p class="form-field">
				<label>
					<?php
						echo esc_html__( 'Taxonomy', 'woocommerce-smart-coupons' );
						$tooltip_text = esc_html__( 'Product taxonomies that the coupon will be applicable for, or its availability in the cart in order for the "Fixed cart discount" to be applied, based on whether the taxonomies are included or excluded. All the taxonomies selected here, should be valid, for this coupon to be valid.', 'woocommerce-smart-coupons' );
						echo wc_help_tip( $tooltip_text ); // phpcs:ignore
					?>
				</label>
				<span id="wc_sc_add_taxonomy_row" class="button wc_sc_add_taxonomy_row" title="<?php echo esc_attr__( 'Add taxonomy restriction', 'woocommerce-smart-coupons' ); ?>"><?php echo esc_html__( 'Add taxonomy restriction', 'woocommerce-smart-coupons' ); ?></span>
			</p>
			<?php
		}

		/**
		 * Get taxonomy restriction row HTML via AJAX
		 */
		public function ajax_default_taxonomy_restriction_row_html() {

			check_ajax_referer( 'wc-sc-default-taxonomy-restriction-row', 'security' );

			$this->get_default_taxonomy_restriction_row();

			die();
		}

		/**
		 * Get taxonomy restriction row
		 *
		 * @param array $args Arguments.
		 */
		public function get_taxonomy_restriction_row( $args = array() ) {
			$index                = ( ! empty( $args['index'] ) ) ? absint( $args['index'] ) : 0;
			$taxonomy_restriction = ( ! empty( $args['taxonomy_restriction'] ) ) ? $args['taxonomy_restriction'] : array();
			$taxonomy_to_label    = ( ! empty( $args['taxonomy_to_label'] ) ) ? $args['taxonomy_to_label'] : array();
			$terms                = ( ! empty( $args['terms'] ) ) ? $args['terms'] : array();
			$operators            = ( ! empty( $args['operators'] ) ) ? $args['operators'] : array();
			$tax                  = $taxonomy_restriction['tax'];
			$op                   = $taxonomy_restriction['op'];
			$value                = $taxonomy_restriction['val'];
			?>
				<p class="form-field wc_sc_taxonomy_restrictions_row wc_sc_taxonomy_restrictions-<?php echo esc_attr( $index ); ?>">
					<label>
						<?php
						if ( 0 === $index ) {
							echo esc_html__( 'Taxonomy', 'woocommerce-smart-coupons' );
							$tooltip_text = esc_html__( 'Product taxonomies that the coupon will be applicable for, or its availability in the cart in order for the "Fixed cart discount" to be applied, based on whether the taxonomies are included or excluded.', 'woocommerce-smart-coupons' );
							echo wc_help_tip( $tooltip_text ); // phpcs:ignore
						} else {
							echo esc_html( ' ' );
						}
						?>
					</label>
					<?php
						$args = array(
							'index'    => $index,
							'column'   => 'tax',
							'all'      => $taxonomy_to_label,
							'selected' => $tax,
							'width'    => '140px',
						);
						$this->get_taxonomy_restriction_select_tag( $args );
						?>
					<?php
						$args = array(
							'index'    => $index,
							'column'   => 'op',
							'all'      => $operators,
							'selected' => $op,
							'width'    => '70px',
						);
						$this->get_taxonomy_restriction_select_tag( $args );
						?>
					<?php
						$args = array(
							'index'    => $index,
							'column'   => 'val',
							'all'      => $terms[ $tax ],
							'selected' => $value,
						);
						$this->get_taxonomy_restriction_select_tag( $args );
						?>
					<span id="wc_sc_add_taxonomy_row" class="button wc_sc_add_taxonomy_row dashicons dashicons-plus-alt2" title="<?php echo esc_attr__( 'Add taxonomy restriction', 'woocommerce-smart-coupons' ); ?>"></span><span id="remove_wc_sc_taxonomy_restrictions-<?php echo esc_attr( $index ); ?>" class="button remove_wc_sc_taxonomy_restrictions-<?php echo esc_attr( $index ); ?> dashicons dashicons-no-alt" title="<?php echo esc_attr__( 'Remove taxonomy restriction', 'woocommerce-smart-coupons' ); ?>"></span>
				</p>
			<?php
		}

		/**
		 * Draw taxonomy restriction select tag
		 *
		 * @param array $args Arguments.
		 */
		public function get_taxonomy_restriction_select_tag( $args = array() ) {
			$index    = ( ! empty( $args['index'] ) ) ? $args['index'] : 0;
			$column   = ( ! empty( $args['column'] ) ) ? $args['column'] : '';
			$all      = ( ! empty( $args['all'] ) ) ? $args['all'] : array();
			$selected = ( ! empty( $args['selected'] ) ) ? $args['selected'] : array();
			$width    = ( ! empty( $args['width'] ) ) ? $args['width'] : '350px';
			?>
			<select name="wc_sc_taxonomy_restrictions[<?php echo esc_attr( $index ); ?>][<?php echo esc_attr( $column ); ?>]<?php echo esc_attr( 'val' === $column ? '[]' : '' ); ?>" id="wc_sc_taxonomy_restrictions-<?php echo esc_attr( $index ); ?>-<?php echo esc_attr( $column ); ?>" style="min-width: <?php echo esc_attr( $width ); ?>;" class="wc-enhanced-select" <?php echo ( 'val' === $column ? 'multiple="multiple"' : '' ); ?> tabindex="-1" aria-hidden="true">
				<?php
				foreach ( $all as $key => $val ) {
					?>
							<option value="<?php echo esc_attr( $key ); ?>"
						<?php
						if ( is_array( $selected ) ) {
							selected( in_array( (string) $key, $selected, true ), true );
						} else {
							selected( $selected, (string) $key );
						}
						?>
							><?php echo esc_html( ucfirst( $val ) ); ?></option>
						<?php
				}
				?>
			</select>
			<?php
		}

		/**
		 * Get taxonomy restriction row HTML via AJAX
		 */
		public function ajax_taxonomy_restriction_select_tag_html() {

			check_ajax_referer( 'wc-sc-taxonomy-restriction-select-tag', 'security' );

			$index = ( ! empty( $_POST['index'] ) ) ? sanitize_text_field( wp_unslash( $_POST['index'] ) ) : 0;
			$tax   = ( ! empty( $_POST['tax'] ) ) ? sanitize_text_field( wp_unslash( $_POST['tax'] ) ) : '';

			$terms = $this->get_terms_grouped_by_taxonomy();

			$args = array(
				'index'  => $index,
				'column' => 'val',
				'all'    => $terms[ $tax ],
			);
			$this->get_taxonomy_restriction_select_tag( $args );

			die();
		}

		/**
		 * Get taxonomy restriction row HTML via AJAX
		 */
		public function ajax_taxonomy_restriction_row_html() {

			check_ajax_referer( 'wc-sc-taxonomy-restriction-row', 'security' );

			$index = ( ! empty( $_POST['index'] ) ) ? sanitize_text_field( wp_unslash( $_POST['index'] ) ) : 0;

			$taxonomy_to_label = $this->get_taxonomy_with_label();
			$terms             = $this->get_terms_grouped_by_taxonomy();
			$operators         = array(
				'incl' => __( 'Include', 'woocommerce-smart-coupons' ),
				'excl' => __( 'Exclude', 'woocommerce-smart-coupons' ),
			);

			$tax = current( array_keys( $taxonomy_to_label ) );
			$op  = current( array_keys( $operators ) );

			$args = array(
				'index'                => $index,
				'taxonomy_restriction' => array(
					'tax' => $tax,
					'op'  => $op,
					'val' => array(),
				),
				'taxonomy_to_label'    => $taxonomy_to_label,
				'operators'            => $operators,
				'terms'                => $terms,
			);
			$this->get_taxonomy_restriction_row( $args );

			die();
		}

		/**
		 * Styles and scripts
		 */
		public function styles_and_scripts() {
			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}
			?>
			<style type="text/css">
				.options_group.wc_sc_taxonomy_restrictions span.select2.select2-container {
					margin: 0 1em 1em 0;
				}
				.options_group.wc_sc_taxonomy_restrictions .button {
					margin: 0 0 0.5em 0.5em;
				}
				.options_group.wc_sc_taxonomy_restrictions .button.dashicons {
					width: auto;
				}
				.options_group.wc_sc_taxonomy_restrictions .dashicons.dashicons-no-alt::before {
					opacity: 0.5;
					font-weight: 100;
				}
				.options_group.wc_sc_taxonomy_restrictions .dashicons {
					vertical-align: middle;
				}
				.options_group.wc_sc_taxonomy_restrictions .woocommerce-help-tip {
					float: right;
					margin: 0 0.4em;
					transform: translateY(25%);
				}
			</style>
			<script type="text/javascript">
				jQuery(function(){
					function wc_sc_taxonomy_restrictions_field_loaded() {
						jQuery('.wc_sc_taxonomy_restrictions').find('.wc_sc_add_taxonomy_row').filter(':not(:last)').remove();
					}
					function wc_sc_add_taxonomy_clicked() {
						jQuery('.wc_sc_taxonomy_restrictions').find('.wc_sc_add_taxonomy_row').remove();
						jQuery('.wc_sc_taxonomy_restrictions').find('p.form-field').filter(':not(p.wc_sc_taxonomy_restrictions_row)').remove();
					}
					wc_sc_taxonomy_restrictions_field_loaded();
					jQuery('body').on('click', '#wc_sc_add_taxonomy_row', function(){
						var count = jQuery('.wc_sc_taxonomy_restrictions').find('.wc_sc_taxonomy_restrictions_row').length;
						jQuery.ajax({
							url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
							type: 'post',
							dataType: 'html',
							data: {
								action: 'taxonomy_restriction_row_html',
								security: '<?php echo esc_html( wp_create_nonce( 'wc-sc-taxonomy-restriction-row' ) ); ?>',
								index: count
							},
							success: function( response ){
								if ( response != undefined && response != '' ) {
									wc_sc_add_taxonomy_clicked();
									jQuery('.wc_sc_taxonomy_restrictions').append(response);
									jQuery(document.body).trigger('wc-enhanced-select-init');
									if ( count <= 0 ) {
										jQuery(document.body).trigger('init_tooltips');
									}
								}
							}
						});
					});
					jQuery('body').on('change', '[id^="wc_sc_taxonomy_restrictions-"][id$="-tax"]', function(){
						var current_element = jQuery(this);
						var element_id      = current_element.attr('id');
						var selected_value  = current_element.val();
						var index           = element_id.replace('wc_sc_taxonomy_restrictions-', '').replace('-tax', '');
						jQuery.ajax({
							url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
							type: 'post',
							dataType: 'html',
							data: {
								action: 'taxonomy_restriction_select_tag_html',
								security: '<?php echo esc_html( wp_create_nonce( 'wc-sc-taxonomy-restriction-select-tag' ) ); ?>',
								index: index,
								tax: selected_value
							},
							success: function( response ){
								if ( response != undefined && response != '' ) {
									jQuery('#wc_sc_taxonomy_restrictions-'+index+'-val').selectWoo('destroy').remove();
									jQuery('#wc_sc_taxonomy_restrictions-'+index+'-op').parent().append(response);
									jQuery(document.body).trigger('wc-enhanced-select-init');
								}
							}
						});
					});
					jQuery('body').on('click', '[id^="remove_wc_sc_taxonomy_restrictions-"]', function(){
						var current_element = jQuery(this);
						if ( current_element.parent().find( '#wc_sc_add_taxonomy_row' ).length > 0 ) {
							current_element.parent().prev().find('[id^=remove_wc_sc_taxonomy_restrictions-]').before(current_element.parent().find('#wc_sc_add_taxonomy_row'));
						}
						if ( current_element.parent().find( '.woocommerce-help-tip' ).length > 0 ) {
							current_element.parent().next().find('label').replaceWith(current_element.parent().find('label'));
						}
						current_element.parent().find('.wc-enhanced-select').selectWoo('destroy');
						current_element.parent().remove();
						var count = jQuery('.wc_sc_taxonomy_restrictions').find('.wc_sc_taxonomy_restrictions_row').length;
						if ( count <= 0 ) {
							jQuery.ajax({
								url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
								type: 'post',
								dataType: 'html',
								data: {
									action: 'default_taxonomy_restriction_row_html',
									security: '<?php echo esc_html( wp_create_nonce( 'wc-sc-default-taxonomy-restriction-row' ) ); ?>'
								},
								success: function( response ){
									if ( response != undefined && response != '' ) {
										jQuery('.wc_sc_taxonomy_restrictions').append(response);
										jQuery(document.body).trigger('wc-enhanced-select-init');
										jQuery(document.body).trigger('init_tooltips');
									}
								}
							});
						}
					});
				});
			</script>
			<?php
		}

		/**
		 * Get taxonomy with label
		 *
		 * @return array
		 */
		public function get_taxonomy_with_label() {
			global $wp_taxonomies;

			$taxonomy_to_label = array();
			$include_taxonomy  = array(
				'product_type',
				'product_visibility',
				'product_tag',
				'product_shipping_class',
			);
			$include_taxonomy  = apply_filters( 'wc_sc_include_taxonomy_for_restrictions', $include_taxonomy, array( 'source' => $this ) );

			if ( ! empty( $wp_taxonomies ) ) {
				foreach ( $wp_taxonomies as $taxonomy => $wp_taxonomy ) {
					if ( in_array( $taxonomy, $include_taxonomy, true ) ) {
						$taxonomy_to_label[ $taxonomy ] = $wp_taxonomy->label;
					}
				}
			}

			return $taxonomy_to_label;
		}

		/**
		 * Get terms grouped by taxonomy
		 *
		 * @return array
		 */
		public function get_terms_grouped_by_taxonomy() {
			$terms_by_taxonomy = array();

			$args = array(
				'hide_empty' => false,
			);

			$terms = get_terms( $args );

			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( empty( $terms_by_taxonomy[ $term->taxonomy ] ) || ! is_array( $terms_by_taxonomy[ $term->taxonomy ] ) ) {
						$terms_by_taxonomy[ $term->taxonomy ] = array();
					}
					$terms_by_taxonomy[ $term->taxonomy ][ $term->slug ] = $term->name;
				}
			}

			return $terms_by_taxonomy;
		}

		/**
		 * Save coupon by payment method data in meta
		 *
		 * @param  Integer $post_id The coupon post ID.
		 * @param  WP_Post $post    The coupon post.
		 */
		public function process_meta( $post_id = 0, $post = null ) {
			if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( is_int( wp_is_post_revision( $post ) ) ) {
				return;
			}
			if ( is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}
			if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( wc_clean( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ), 'woocommerce_save_data' ) ) { // phpcs:ignore
				return;
			}
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			if ( 'shop_coupon' !== $post->post_type ) {
				return;
			}

			$taxonomy_restrictions = ( isset( $_POST['wc_sc_taxonomy_restrictions'] ) ) ? wc_clean( wp_unslash( $_POST['wc_sc_taxonomy_restrictions'] ) ) : array(); // phpcs:ignore
			if ( ! empty( $taxonomy_restrictions ) ) {
				$taxonomy_restrictions = array_values( $taxonomy_restrictions );
			}

			update_post_meta( $post_id, 'wc_sc_taxonomy_restrictions', $taxonomy_restrictions );
		}

		/**
		 * Function to validate coupons against taxonomy
		 *
		 * @param bool            $valid Coupon validity.
		 * @param WC_Product|null $product Product object.
		 * @param WC_Coupon|null  $coupon Coupon object.
		 * @param array|null      $values Values.
		 * @return bool           $valid
		 */
		public function validate( $valid = false, $product = null, $coupon = null, $values = null ) {

			// If coupon is already invalid, no need for further checks.
			if ( true !== $valid ) {
				return $valid;
			}

			if ( empty( $product ) || empty( $coupon ) ) {
				return $valid;
			}

			if ( $this->is_wc_gte_30() ) {
				$coupon_id  = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
				$product_id = ( is_object( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0;
			} else {
				$coupon_id  = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
			}

			if ( ! empty( $coupon_id ) ) {

				$taxonomy_restrictions = get_post_meta( $coupon_id, 'wc_sc_taxonomy_restrictions', true );

				if ( ! empty( $taxonomy_restrictions ) ) {
					$term_ids = $this->get_restricted_term_ids( array( 'taxonomy_restrictions' => $taxonomy_restrictions ) );

					$include_ids = ( ! empty( $term_ids['include'] ) || is_array( $term_ids['include'] ) ) ? $term_ids['include'] : array();
					$exclude_ids = ( ! empty( $term_ids['exclude'] ) || is_array( $term_ids['exclude'] ) ) ? $term_ids['exclude'] : array();

					$taxonomies      = wp_list_pluck( $taxonomy_restrictions, 'tax' );
					$args            = array(
						'fields' => 'ids',
					);
					$object_term_ids = wp_get_object_terms( $product_id, $taxonomies, $args );
					$object_term_ids = array_unique( array_filter( $object_term_ids ) );

					$taxonomy_found = true;
					if ( ! empty( $include_ids ) && is_array( $include_ids ) ) {
						$common_term_ids = array_intersect( $include_ids, $object_term_ids );
						if ( count( $common_term_ids ) > 0 ) {
							$taxonomy_found = true;
						} else {
							$taxonomy_found = false;
						}
					}

					$exclude_taxonomy_found = false;
					if ( ! empty( $exclude_ids ) && is_array( $exclude_ids ) ) {
						$common_exclude_term_ids = array_intersect( $exclude_ids, $object_term_ids );
						if ( count( $common_exclude_term_ids ) > 0 ) {
							$exclude_taxonomy_found = true;
						} else {
							$exclude_taxonomy_found = false;
						}
					}

					$valid = ( $taxonomy_found && ! $exclude_taxonomy_found ) ? true : false;
				}
			}

			return $valid;
		}

		/**
		 * Function to validate non product type coupons against taxonomy restriction
		 * We need to remove coupon if it does not pass taxonomy validation even for single cart item in case of non product type coupons e.g fixed_cart, smart_coupon since these coupon type require all products in the cart to be valid
		 *
		 * @param boolean      $valid Coupon validity.
		 * @param WC_Coupon    $coupon Coupon object.
		 * @param WC_Discounts $discounts Discounts object.
		 * @throws Exception Validation exception.
		 * @return boolean  $valid Coupon validity
		 */
		public function handle_non_product_type_coupons( $valid = true, $coupon = null, $discounts = null ) {

			// If coupon is already invalid, no need for further checks.
			if ( true !== $valid ) {
				return $valid;
			}

			if ( ! is_a( $coupon, 'WC_Coupon' ) ) {
				return $valid;
			}

			if ( $this->is_wc_gte_30() ) {
				$coupon_id     = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
				$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
			} else {
				$coupon_id     = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
			}

			if ( ! empty( $coupon_id ) ) {
				$taxonomy_restrictions = get_post_meta( $coupon_id, 'wc_sc_taxonomy_restrictions', true );
				// If product attributes are not set in coupon, stop further processing and return from here.
				if ( empty( $taxonomy_restrictions ) ) {
					return $valid;
				}
			} else {
				return $valid;
			}

			$product_coupon_types = wc_get_product_coupon_types();

			// Proceed if it is non product type coupon.
			if ( ! in_array( $discount_type, $product_coupon_types, true ) ) {
				if ( class_exists( 'WC_Discounts' ) && isset( WC()->cart ) ) {
					$wc_cart           = WC()->cart;
					$wc_discounts      = new WC_Discounts( $wc_cart );
					$items_to_validate = array();
					if ( is_callable( array( $wc_discounts, 'get_items_to_validate' ) ) ) {
						$items_to_validate = $wc_discounts->get_items_to_validate();
					} elseif ( is_callable( array( $wc_discounts, 'get_items' ) ) ) {
						$items_to_validate = $wc_discounts->get_items();
					} elseif ( isset( $wc_discounts->items ) && is_array( $wc_discounts->items ) ) {
						$items_to_validate = $wc_discounts->items;
					}
					if ( ! empty( $items_to_validate ) && is_array( $items_to_validate ) ) {
						$term_ids = $this->get_restricted_term_ids( array( 'taxonomy_restrictions' => $taxonomy_restrictions ) );

						$include_ids = ( ! empty( $term_ids['include'] ) || is_array( $term_ids['include'] ) ) ? $term_ids['include'] : array();
						$exclude_ids = ( ! empty( $term_ids['exclude'] ) || is_array( $term_ids['exclude'] ) ) ? $term_ids['exclude'] : array();

						$taxonomies = wp_list_pluck( $taxonomy_restrictions, 'tax' );

						$valid_products   = array();
						$invalid_products = array();
						foreach ( $items_to_validate as $item ) {
							$cart_item    = clone $item; // Clone the item so changes to wc_discounts item do not affect the originals.
							$item_product = isset( $cart_item->product ) ? $cart_item->product : null;
							$item_object  = isset( $cart_item->object ) ? $cart_item->object : null;
							if ( ! is_null( $item_product ) && ! is_null( $item_object ) ) {
								if ( $coupon->is_valid_for_product( $item_product, $item_object ) ) {
									$valid_products[] = $item_product;
								} else {
									$invalid_products[] = $item_product;
								}
							}
						}

						// If cart does not have any valid product then throw Exception.
						if ( 0 === count( $valid_products ) ) {
							$error_message = __( 'Sorry, this coupon is not applicable to selected products.', 'woocommerce-smart-coupons' );
							$error_code    = defined( 'E_WC_COUPON_NOT_APPLICABLE' ) ? E_WC_COUPON_NOT_APPLICABLE : 0;
							throw new Exception( $error_message, $error_code );
						} elseif ( count( $invalid_products ) > 0 && ! empty( $exclude_ids ) ) {

							$excluded_products = array();
							foreach ( $invalid_products as $invalid_product ) {
								$product_id   = ( is_object( $invalid_product ) && is_callable( array( $invalid_product, 'get_id' ) ) ) ? $invalid_product->get_id() : 0;
								$product_name = ( is_object( $invalid_product ) && is_callable( array( $invalid_product, 'get_name' ) ) ) ? $invalid_product->get_name() : '';

								$args            = array(
									'fields' => 'ids',
								);
								$object_term_ids = wp_get_object_terms( $product_id, $taxonomies, $args );
								$object_term_ids = array_unique( array_filter( $object_term_ids ) );

								if ( ! empty( $object_term_ids ) && is_array( $object_term_ids ) ) {
									$common_exclude_term_ids = array_intersect( $exclude_ids, $object_term_ids );
									if ( count( $common_exclude_term_ids ) > 0 ) {
										$excluded_products[] = $product_name;
									}
								}
							}

							if ( count( $excluded_products ) > 0 ) {
								// If cart contains any excluded product and it is being excluded from our excluded product attributes then throw Exception.
								/* translators: 1. Singular/plural label for product(s) 2. Excluded product names */
								$error_message = sprintf( __( 'Sorry, this coupon is not applicable to the %1$s: %2$s.', 'woocommerce-smart-coupons' ), _n( 'product', 'products', count( $excluded_products ), 'woocommerce-smart-coupons' ), implode( ', ', $excluded_products ) );
								$error_code    = defined( 'E_WC_COUPON_EXCLUDED_PRODUCTS' ) ? E_WC_COUPON_EXCLUDED_PRODUCTS : 0;
								throw new Exception( $error_message, $error_code );
							}
						}
					}
				}
			}

			return $valid;
		}

		/**
		 * Get restricted term ids
		 *
		 * @param array $args Arguments.
		 * @return array
		 */
		public function get_restricted_term_ids( $args = array() ) {
			global $wp_taxonomies;

			$term_ids = array();

			$taxonomy_restrictions = ( ! empty( $args['taxonomy_restrictions'] ) ) ? $args['taxonomy_restrictions'] : array();

			if ( ! empty( $taxonomy_restrictions ) ) {
				$include_ids = array();
				$exclude_ids = array();
				foreach ( $taxonomy_restrictions as $taxonomy_restriction ) {
					$taxonomy = ( ! empty( $taxonomy_restriction['tax'] ) ) ? $taxonomy_restriction['tax'] : '';
					$operator = ( ! empty( $taxonomy_restriction['op'] ) ) ? $taxonomy_restriction['op'] : '';
					$value    = ( ! empty( $taxonomy_restriction['val'] ) ) ? $taxonomy_restriction['val'] : array();
					if ( ! empty( $taxonomy ) && ! empty( $operator ) && ! empty( $value ) ) {
						$args      = array(
							'taxonomy'   => $taxonomy,
							'hide_empty' => false,
							'fields'     => 'ids',
							'slug'       => $value,
						);
						$found_ids = get_terms( $args );
						$found_ids = array_unique( array_filter( $found_ids ) );
						if ( ! empty( $found_ids ) ) {
							switch ( $operator ) {
								case 'incl':
									$include_ids = array_merge( $include_ids, $found_ids );
									break;
								case 'excl':
									$exclude_ids = array_merge( $exclude_ids, $found_ids );
									break;
							}
						}
					}
				}
				if ( ! empty( $include_ids ) ) {
					$term_ids['include'] = array_unique( array_filter( $include_ids ) );
				}
				if ( ! empty( $exclude_ids ) ) {
					$term_ids['exclude'] = array_unique( array_filter( $exclude_ids ) );
				}
			}

			return $term_ids;
		}

		/**
		 * Make meta data of payment method ids protected
		 *
		 * @param bool   $protected Is protected.
		 * @param string $meta_key The meta key.
		 * @param string $meta_type The meta type.
		 * @return bool $protected
		 */
		public function make_action_meta_protected( $protected = false, $meta_key = '', $meta_type = '' ) {

			if ( 'wc_sc_taxonomy_restrictions' === $meta_key ) {
				return true;
			}
			return $protected;
		}
	}
}

WC_SC_Coupons_By_Taxonomy::get_instance();
