<?php

/**
 * Class CT_Ultimate_GDPR_Model_Logger
 */
class CT_Ultimate_GDPR_Model_Logger {

	/**
	 * @var bool
	 */
	protected static $tables_created = false;

	/**
	 * @var wpdb
	 */
	protected $wpdb;

    protected $current_ver = '2';

    protected $db_ver_key = 'ct_ultimate_gdpr_logger_version';

	/**
	 * CT_Ultimate_GDPR_Model_Logger constructor.
	 */
	public function __construct() {

		global $wpdb;
		$this->wpdb = $wpdb;
		$this->maybe_create_tables();

        add_action("admin_init",array($this,'maybe_update_tables'));
	}

	/**
	 * @param array $data
	 *
	 * @return bool
	 */
	public function consent( $data ) {

		global $wpdb;

		if ( apply_filters( 'ct_ultimate_gdpr_model_logger_disable', false ) ) {
			return false;
		}

		$data = shortcode_atts(
			array(
				'time'       => '',
				'user_id'    => '',
				'type'       => '',
				'user_ip'    => '',
				'user_agent' => '',
				'data'       => array(),
			),
			$data
		);

		if ( ! is_string( $data['data'] ) ) {
			$data['data'] = json_encode( $data['data'] );
		}

		return ! ! $wpdb->insert(
			$this->get_consent_table(),
			$data
		);
	}

	/**
	 * @return mixed
	 */
	private function get_consent_table() {

		global $wpdb;

		return apply_filters( 'ct_ultimate_gdpr_model_logger_consent_table', $wpdb->prefix . 'ct_ugdpr_consent_log' );

	}

	/**
	 *
	 */
	public function maybe_create_tables() {

		global $wpdb;

		if ( self::$tables_created || apply_filters( 'ct_ultimate_gdpr_model_logger_disable', false ) ) {
			return;
		}

		// check if table already exists to avoid delta update queries
		if( $wpdb->get_var( "SHOW TABLES LIKE '{$this->get_consent_table()}'" ) ) {

			self::$tables_created = true;
			return;

		}

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE `{$this->get_consent_table()}` (
  id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  type varchar(255) NOT NULL,
  user_id bigint(20) UNSIGNED NOT NULL,
  user_ip varchar(255) NOT NULL,
  user_agent varchar(255) NOT NULL,
  time bigint(20) UNSIGNED NOT NULL,
  data varchar(2048) NOT NULL,
  PRIMARY KEY (id)
) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

        add_option($this->db_ver_key, $this->current_ver);
	}

    public function maybe_update_tables() {
        global $wpdb;

        $installed_ver = get_option($this->db_ver_key);

        if (version_compare($installed_ver, $this->current_ver, '<')) {
            $table_name = $this->get_consent_table();

            // Prepare SQL query to alter the table
            $sql = "ALTER TABLE `{$table_name}` MODIFY `data` VARCHAR(2048) NOT NULL;";

            $result = $wpdb->query($sql);

            // Check for errors
            if ($result === false) {
                // Handle errors, e.g., log them or notify the admin
                error_log("Failed to alter table for {$table_name}");
            } else {
                // Update the version in the database
                update_option($this->db_ver_key, $this->current_ver);
            }
        }
    }

	/**
	 * @param string $type
	 *
	 * @return array|null|object
	 */
	public function get_logs( $type = '' ) {

		if ( $type ) {

			$query = $this->wpdb->prepare( "
			SELECT *
			FROM {$this->get_consent_table()}
			WHERE type = %s
			ORDER BY id DESC;
",
				$type
			);

		} else {

			$query = "
			SELECT *
			FROM {$this->get_consent_table()}
			ORDER BY id DESC;
";

		}

		$results = $this->wpdb->get_results( $query, ARRAY_A );

		if ( ! $results ) {
			$results = array();
		}

		return $results;
	}

	/**
	 * @param array $logs
	 *
	 * @return string
	 */
	public function render_logs( $logs ) {

		$response = '';

		foreach ( $logs as $log ) {

			foreach ( $log as $key => $val ) {

				$response .= "$key: $val" . PHP_EOL;

				// add user email
				if ( $key == 'user_id' && $val ) {
					$user = get_userdata( $val );
					if ( $user instanceof WP_User ) {
						$response .= "email: {$user->user_email}" . PHP_EOL;
					}
				}

			}

			$response .= PHP_EOL;

		}

		return $response;

	}

}