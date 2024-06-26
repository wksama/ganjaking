<?php
/**
 * Security class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 3.12.0
 */

defined( 'YITH_WCMAP' ) || exit;  // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCMAP_Security', false ) ) {
	/**
	 * Security class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 2.5.0
	 */
	class YITH_WCMAP_Security {

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  2.5.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'handle_actions' ), 1 );
			if ( 'yes' === get_option( 'yith-wcmap-enable-verifying-email', 'no' ) ) {
				add_action( 'woocommerce_created_customer', array( $this, 'created_customer_action' ), 0, 3 );
				add_filter( 'woocommerce_registration_error_email_exists', array( $this, 'filter_registration_email_exists' ), 99, 2 );
				add_filter( 'woocommerce_process_login_errors', array( $this, 'filter_login_errors' ), 99, 3 );
				add_filter( 'allow_password_reset', array( $this, 'allow_password_reset' ), 99, 2 );

				add_action( 'template_redirect', array( $this, 'block_checkout_process' ), 1 );

				$effect = get_option( 'yith-wcmap-verifying-email-effect', 'no-login' );

				if ( 'no-login' === $effect ) {
					add_filter( 'woocommerce_email_actions', array( $this, 'filter_created_customer_action' ) );
				}
			}
		}

		/**
		 * Get customer id from validation code
		 *
		 * @since  2.5.0
		 * @param string $code The validation code.
		 * @return integer
		 */
		protected function get_customer_id_from_validation_code( $code ) {
			global $wpdb;

			$q = $wpdb->prepare( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s", '_ywcmap_validation_code', $code );
			$r = $wpdb->get_var( $q ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

			return absint( $r );
		}

		/**
		 * Get validation code for customer
		 *
		 * @since  2.5.0
		 * @param integer $customer_id The customer ID.
		 * @return string
		 */
		protected function get_validation_code_from_customer_id( $customer_id ) {
			return get_user_meta( $customer_id, '_ywcmap_validation_code', true );
		}

		/**
		 * Check if a customer needs email verify
		 *
		 * @since  2.5.0
		 * @param mixed $customer The customer to verify. Can be WP_User object, email address or customer id.
		 * @return boolean
		 */
		public function customer_needs_verify( $customer ) {

			$customer_id = 0;
			if ( $customer instanceof WP_User ) {
				$customer_id = $customer->ID;
			} elseif ( is_email( $customer ) ) {
				$customer = get_user_by( 'email', $customer );
				if ( $customer ) {
					$customer_id = $customer->ID;
				}
			} else {
				$customer_id = absint( $customer );
			}

			/**
			 * APPLY_FILTERS: ywcmap_skip_verification
			 *
			 * Filters whether to skip the account verification.
			 *
			 * @param string $skip_verification Whether to skip account verification or not. Possible values: 'yes' or 'no' (default).
			 * @param int    $customer_id       Customer ID.
			 *
			 * @return string
			 */
			if ( 'yes' === apply_filters( 'ywcmap_skip_verification', 'no', $customer_id ) ) {
				return false;
			}

			return ( $customer_id && $this->get_validation_code_from_customer_id( $customer_id ) );
		}

		/**
		 * Created customer actions
		 *
		 * @since  2.5.0
		 * @param integer|string $customer_id        The customer ID created.
		 * @param array          $new_customer_data  The new customer data array.
		 * @param boolean        $password_generated True if password is autogenerated, false otherwise.
		 * @return void
		 */
		public function created_customer_action( $customer_id, $new_customer_data, $password_generated ) {

			if ( defined( 'WOOCOMMERCE_CHECKOUT' ) || 'yes' === apply_filters( 'ywcmap_skip_verification', 'no', $customer_id ) ) {
				return;
			}

			( ! empty( WC()->session ) && WC()->session->has_session() ) || do_action( 'woocommerce_set_cart_cookies', true );

			$effect = get_option( 'yith-wcmap-verifying-email-effect', 'no-login' );

			if ( 'no-login' === $effect ) {
				add_filter( 'woocommerce_registration_auth_new_customer', '__return_false' );
				// Postpone standard email.
				// remove_action( 'woocommerce_created_customer_notification', array( WC_Emails::instance(), 'customer_new_account' ), 10 );
				// Create user temp meta.
				update_user_meta(
					$customer_id,
					'_ywcmap_temp_data',
					array(
						'customer_data'  => $new_customer_data,
						'pass_generated' => $password_generated,
					)
				);
			}

			update_user_meta( $customer_id, '_ywcmap_validation_code', md5( time() . $customer_id ) );

			if ( function_exists( 'wc_add_notice' ) ) {
				wc_add_notice( $this->get_message( 'created', $customer_id ) );
			}

			$email = WC_Emails::instance()->emails['YITH_WCMAP_Verify_Account'];
			$email->trigger( $customer_id );

			/**
			 * DO_ACTION: yith_wcmap_after_verify_account_email
			 *
			 * Allows to trigger some action after sending email to verify the account.
			 */
			do_action( 'yith_wcmap_after_verify_account_email' );
		}

		/**
		 * Filter email actions when creating an account
		 *
		 * @param array $actions Email actions.
		 *
		 * @return array
		 */
		public function filter_created_customer_action( $actions ) {
			$key = array_search( 'woocommerce_created_customer', $actions, true );

			if ( false !== $key ) {
				unset( $actions[ $key ] );
			}

			return $actions;
		}

		/**
		 * Filter registration email exists error message
		 *
		 * @since  2.5.0
		 * @param string $default The default error message.
		 * @param string $email   The customer email.
		 * @return string
		 */
		public function filter_registration_email_exists( $default, $email ) {

			$customer    = get_user_by( 'email', $email );
			$customer_id = $customer ? $customer->ID : 0;

			if ( $customer_id && $this->customer_needs_verify( $customer ) ) {
				$default = $this->get_message( 'exists-needs-verify', $customer_id );
			}

			return $default;
		}

		/**
		 * Filter login errors. Check if customer account needs to be verified
		 *
		 * @since  2.5.0
		 * @param WP_Error $errors   Registration errors.
		 * @param string   $username The customer username.
		 * @param string   $password The customer password.
		 * @return WP_Error
		 */
		public function filter_login_errors( $errors, $username, $password ) {
			$customer    = get_user_by( is_email( $username ) ? 'email' : 'login', $username );
			$customer_id = $customer ? $customer->ID : 0;
			$login_lock  = 'no-login' === get_option( 'yith-wcmap-verifying-email-effect', 'no-login' );

			if ( ! $customer_id || ! $login_lock || ! $this->customer_needs_verify( $customer_id ) ) {
				return $errors;
			}

			$errors->add( 'login-error-needs-varified', $this->get_message( 'login-needs-verify', $customer_id ) );

			return $errors;
		}

		/**
		 * Lock password reset for customer that needs account to be verified
		 *
		 * @since  2.5.0
		 * @param boolean $lock        True to lock password reset, false otherwise.
		 * @param integer $customer_id The customer ID.
		 * @return boolean
		 */
		public function allow_password_reset( $lock, $customer_id ) {
			return ( 'no-login' === get_option( 'yith-wcmap-verifying-email-effect', 'no-login' ) && $this->customer_needs_verify( $customer_id ) ) ? false : $lock;
		}

		/**
		 * Block checkout process
		 *
		 * @since  2.5.0
		 * @return void
		 */
		public function block_checkout_process() {
			global $wp;

			if ( is_page( wc_get_page_id( 'checkout' ) ) && wc_get_page_id( 'checkout' ) !== wc_get_page_id( 'cart' ) && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] )
				&& 'no-purchase' === get_option( 'yith-wcmap-verifying-email-effect', 'no-purchase' ) && $this->customer_needs_verify( get_current_user_id() ) ) {

				wc_add_notice( $this->get_message( 'block-checkout' ), 'error' );
				wp_safe_redirect( wc_get_page_permalink( 'cart' ) );
				exit;
			}
		}

		/**
		 * Get the resend email link html
		 *
		 * @since  2.5.0
		 * @param integer $customer_id The customer ID.
		 * @return string
		 */
		public function get_resend_email_html( $customer_id ) {
			global $post;

			$c = $customer_id ? $this->get_validation_code_from_customer_id( $customer_id ) : '';
			if ( empty( $c ) ) {
				return '';
			}

			if ( is_page( wc_get_page_id( 'checkout' ) ) ) {
				$url = wc_get_cart_url();
				$to  = wc_get_page_id( 'cart' );
			} else {
				$url = $_SERVER['REQUEST_URI']; // phpcs:ignore
				$to  = ( ! is_null( $post ) && 'page' === $post->post_type ) ? $post->ID : wc_get_page_id( 'myaccount' );
			}

			$url = add_query_arg(
				array(
					'c'      => $c,
					'action' => 'ywcmap_resend_email_action',
					'to'     => intval( $to ),
				),
				$url
			);

			return '<a href="' . $url . '">' . esc_html__( 'Click here to resend email.', 'yith-woocommerce-customize-myaccount-page' ) . '</a>';
		}

		/**
		 * Get notice messages based to context
		 *
		 * @since  2.5.0
		 * @param string  $context     The message context.
		 * @param integer $customer_id The customer ID.
		 * @return string
		 */
		public function get_message( $context = 'created', $customer_id = 0 ) {

			$msg_part = '';
			if ( empty( $customer_id ) ) {
				$customer_id = get_current_user_id();
			}

			if ( $customer_id ) {
				$resend_link = $this->get_resend_email_html( $customer_id );
				// translators: %s stand for the link to resend confirmation email.
				$msg_part = sprintf( __( 'You have to confirm your account. Please, click on the link in the verification email. %s', 'yith-woocommerce-customize-myaccount-page' ), $resend_link );
			}

			switch ( $context ) {
				case 'exists-needs-verify':
					// translators: %s is a message that ask customer to confirm the account clicking on the link in the verification email.
					$msg = sprintf( __( 'An account is already registered with this email. %s', 'yith-woocommerce-customize-myaccount-page' ), $msg_part );
					break;
				case 'login-needs-verify':
					// translators: %s is a message that ask customer to confirm the account clicking on the link in the verification email.
					$msg = sprintf( __( 'Login is not permitted! %s', 'yith-woocommerce-customize-myaccount-page' ), $msg_part );
					break;
				case 'block-checkout':
					// translators: %s is a message that ask customer to confirm the account clicking on the link in the verification email.
					$msg = sprintf( __( 'Checkout is not permitted! %s', 'yith-woocommerce-customize-myaccount-page' ), $msg_part );
					break;
				case 'confirmed':
					$msg = __( 'Thanks your account is now confirmed!', 'yith-woocommerce-customize-myaccount-page' );
					break;
				default:
					$msg = __( 'A confirmation email has been sent to your email address. Please click on the confirmation link in the email to complete your account activation.', 'yith-woocommerce-customize-myaccount-page' );
					break;
			}

			/**
			 * APPLY_FILTERS: yith_wcmap_get_notice_message_$context
			 *
			 * Filters the notice message based on the context.
			 * <code>$context</code> will be replaced with the message context.
			 *
			 * @param string $msg Message.
			 *
			 * @return string
			 */
			return apply_filters( "yith_wcmap_get_notice_message_{$context}", $msg );
		}

		/**
		 * Confirm email action
		 *
		 * @since  2.5.0
		 * @return void
		 */
		public function handle_actions() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( empty( $_GET['action'] ) || empty( $_GET['c'] ) ) {
				return;
			}

			$action = sanitize_text_field( wp_unslash( $_GET['action'] ) );
			$action = str_replace( 'ywcmap_', '', $action );

			if ( ! method_exists( $this, $action ) ) {
				return;
			}

			$c  = $this->get_customer_id_from_validation_code( sanitize_text_field( wp_unslash( $_GET['c'] ) ) );
			$to = ! empty( $_GET['to'] ) ? absint( $_GET['to'] ) : wc_get_page_id( 'myaccount' );

			if ( ! $c ) {
				return;
			}

			$this->$action( $c, $to );

			wp_safe_redirect( get_permalink( $to ) );
			exit;
			// phpcs:enable WordPress.Security.NonceVerification.Recommended
		}

		/**
		 * Verify account action
		 *
		 * @since  2.5.0
		 * @param string|integer $customer_id The customer id.
		 * @param string|integer $to          Destination link.
		 * @return void
		 */
		public function confirm_email_action( $customer_id, $to = 0 ) {
			delete_user_meta( $customer_id, '_ywcmap_validation_code' );

			is_user_logged_in() || wc_set_customer_auth_cookie( $customer_id );

			wc_clear_notices();
			wc_add_notice( $this->get_message( 'confirmed' ), 'success' );

			$data = get_user_meta( $customer_id, '_ywcmap_temp_data', true );
			if ( empty( $data ) ) {
				return;
			}

			$user_pass = ! empty( $data['customer_data']['user_pass'] ) ? $data['customer_data']['user_pass'] : '';
			$email     = WC_Emails::instance()->emails['WC_Email_Customer_New_Account'];
			$email->trigger( $customer_id, $user_pass, $data['pass_generated'] );

			delete_user_meta( $customer_id, '_ywcmap_temp_data' );
		}

		/**
		 * Resend verify email action
		 *
		 * @since  2.5.0
		 * @param string|integer $customer_id The customer id.
		 * @param string|integer $to          Destination link.
		 * @return void
		 */
		public function resend_email_action( $customer_id, $to = 0 ) {

			$email = WC_Emails::instance()->emails['YITH_WCMAP_Verify_Account'];
			$email->trigger( $customer_id, $to );

			wc_clear_notices();
			wc_add_notice( $this->get_message() );
		}
	}
}
