<?php // phpcs:ignore WordPress.NamingConventions

/**
 * YWAR_Review_Report
 *
 * @author  YITH <plugins@yithemes.com>
 * @package       YITH\yit-woocommerce-advanced-reviews\Templates\Reports
 * @version 1.0.0
 */
class YWAR_Review_Report extends WC_Admin_Report {


	/**
	 * Data_to_show
	 *
	 * @var string
	 */
	public $data_to_show = '';

	/**
	 * __construct
	 *
	 * @param name mixed $name name.
	 * @return void
	 */
	public function __construct( $name ) {
		$data_to_show = $name;
	}

	/**
	 * Get the legend for the main chart sidebar
	 *
	 * @return array
	 */
	public function get_chart_legend() {
		return array();
	}

	/**
	 * Output an export link
	 */
	public function get_export_button() {
		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : 'last_month';//phpcs:ignore WordPress.Security.NonceVerification
		?>
		<a
			href="#"
			download="report-<?php echo esc_attr( $current_range ); ?>-<?php echo esc_html( date_i18n( 'Y-m-d', current_time( 'timestamp' ) ) );//phpcs:ignore --Because Timestamps discouraged ?>.csv"
			class="export_csv"
			data-export="table"
			>
			<?php esc_html_e( 'Export CSV', 'yith-woocommerce-advanced-reviews' ); ?>
		</a>
		<?php
	}

	/**
	 * Output the report
	 */
	public function output_report() {
		global $report_name;

		$ranges = array(
			'year'       => esc_html__( 'Year', 'yith-woocommerce-advanced-reviews' ),
			'last_month' => esc_html__( 'Last Month', 'yith-woocommerce-advanced-reviews' ),
			'month'      => esc_html__( 'This Month', 'yith-woocommerce-advanced-reviews' ),
		);

		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : 'last_month';//phpcs:ignore WordPress.Security.NonceVerification

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ), true ) ) {
			$current_range = 'last_month';
		}

		$this->calculate_current_range( $current_range );

		$hide_sidebar = true;

		include YITH_YWAR_TEMPLATES_DIR . '/reports/ywar-html-review-report.php';
	}

	/**
	 * Get_main_chart
	 *
	 * @return void
	 */
	public function get_main_chart() {
		global $wpdb;

		$start_date = gmdate( 'Y-m-d', $this->start_date );
		$end_date   = gmdate( 'Y-m-d', $this->end_date );

		$query = "SELECT p.id, p.post_title, count(*) as total, min(rev_meta.meta_value) as min_rating, max(rev_meta.meta_value) as max_rating, avg(rev_meta.meta_value) as avg_rating
			FROM {$wpdb->posts} p
			left JOIN {$wpdb->postmeta} pm ON ( p.ID = pm.meta_value )
			left join {$wpdb->posts} rev ON (rev.id = pm.post_id)
			left join {$wpdb->postmeta} rev_meta ON (rev.ID = rev_meta.post_id)
			left join {$wpdb->postmeta} rev_meta2 ON (rev.ID = rev_meta2.post_id)
			where ( (p.post_type = 'product')
				AND (rev.post_type = '" . YITH_YWAR_POST_TYPE . "')
				AND (rev.post_parent = 0)
				AND (p.post_status = 'publish')
				AND (rev_meta.meta_key = '_ywar_rating')
				AND (rev_meta2.meta_key = '_ywar_product_id')
				AND (rev_meta2.meta_value = p.id)
				AND (rev.post_date >= '$start_date 00:00:00')
				AND (rev.post_date <= '$end_date 23:59:59') )
			group by p.id";

		global $report_name;

		switch ( $report_name ) {
			case 'most-commented':
				$query .= ' order by total DESC';
				break;
			case 'min-rating':
				$query .= ' order by min_rating ASC';
				break;
			case 'max-rating':
				$query .= ' order by max_rating DESC';
				break;
			case 'average-rating':
				$query .= ' order by avg_rating DESC';
				break;
			default:
				$query .= ' order by wp_posts.post_title ASC';
				break;
		}

		$results = $wpdb->get_results( $query ); //phpcs:ignore --Call directly database is discouraged

		$most_commented_class = 'most-commented' === $report_name ? 'selected' : '';
		$min_rating_class     = 'min-rating' === $report_name ? 'selected' : '';
		$max_rating_class     = 'max-rating' === $report_name ? 'selected' : '';
		$avg_rating_class     = 'avg-rating' === $report_name ? 'selected' : '';

		?>
		<table class="widefat product-review-stats">
			<thead>
			<tr>
				<th class="product"><?php esc_html_e( 'Product', 'yith-woocommerce-advanced-reviews' ); ?></th>
				<th class="stats <?php echo esc_attr( $most_commented_class ); ?>"><?php esc_html_e( 'Reviews', 'yith-woocommerce-advanced-reviews' ); ?></th>
				<th class="stats <?php echo esc_attr( $max_rating_class ); ?>"><?php esc_html_e( 'Best rate', 'yith-woocommerce-advanced-reviews' ); ?></th>
				<th class="stats <?php echo esc_attr( $min_rating_class ); ?>"><?php esc_html_e( 'Worst rate', 'yith-woocommerce-advanced-reviews' ); ?></th>
				<th class="stats <?php echo esc_attr( $avg_rating_class ); ?>"><?php esc_html_e( 'Average rate', 'yith-woocommerce-advanced-reviews' ); ?></th>
			</tr>
			</thead>
			<?php if ( $results ) : ?>
				<tbody>
				<?php
				foreach ( $results as $product_stats ) {
					?>
					<tr>
						<th scope="row"><?php echo esc_attr( $product_stats->post_title ); ?></th>
						<td class="stats <?php echo esc_attr( $most_commented_class ); ?>"><?php echo esc_attr( $product_stats->total ); ?></td>
						<td class="stats <?php echo esc_attr( $max_rating_class ); ?>"><?php echo esc_attr( $product_stats->max_rating ); ?></td>
						<td class="stats <?php echo esc_attr( $min_rating_class ); ?>"><?php echo esc_attr( $product_stats->min_rating ); ?></td>
						<td class="stats <?php echo esc_attr( $avg_rating_class ); ?>"><?php echo esc_attr( round( $product_stats->avg_rating, 2 ) ); ?></td>
					</tr>
					<?php
				}
				?>
				</tbody>
			<?php else : ?>
				<tbody>
				<tr>
					<td><?php esc_html_e( 'No reviews found in this period', 'yith-woocommerce-advanced-reviews' ); ?></td>
				</tr>
				</tbody>
			<?php endif; ?>
		</table>
		<?php
	}
}
