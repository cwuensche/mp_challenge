<?php

class MP_Admin {

	private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}

	}
	
	public static function init_hooks() {
		self::$initiated = true;
		add_action( 'admin_enqueue_scripts', array( 'MP_Admin', 'load_resources' ) );
		add_action( 'admin_menu', array( 'MP_Admin', 'memberpress_data_admin_menu' ) );
	}

	public static function load_resources() {
			wp_register_style( 'mp-admin.css', plugin_dir_url( __FILE__ ) . 'assets/css/mp-admin.css', array(), MP_VERSION );
			wp_enqueue_style( 'mp-admin.css');
	}

	/*
		Add menu item for the admin page in the settings dropdown section 
	*/
	public static function memberpress_data_admin_menu() {
		add_options_page( 'Memberpress API', 'Memberpress API', 'manage_options', 'mp-endpoint-api', array( 'MP_Admin', 'mp_endpoint_data' ) );
	}

	public static function mp_endpoint_data() {
		// If we click on the refresh link, we will call the ajax endpoint to refresh the data.
		if ( !empty( $_GET['action'] ) && "refresh" == $_GET['action'] ) {

			 wp_remote_post(
				admin_url( 'admin-ajax.php' ),
				array(
					'body' => array(
						'action' => 'memberpress_endpoint',
						'method' => 'refresh',
					)
				)
			);
		}
			$endpoint_data = get_transient( 'memberpress_endpoint' );

	?>
	<div class="page-title-box">
	<img src="<?php echo plugin_dir_url(__FILE__); ?>assets/images/mp-logo-horiz-CMYK.jpg" width="452" height="72">
	</div>
	<div class="wrap">
	<h1 class="wp-heading-inline">API Data</h1>
	<a href="<?php echo admin_url('options-general.php?page=mp-endpoint-api&action=refresh'); ?>" class="page-title-action">Refresh Data</a>
	<hr class="wp-header-end">

	<table class="wp-list-table widefat fixed striped pages">
		<thead>
		<tr>
			<th scope="col" id="title" class="manage-column column-id column-primary">ID</th>
			<th scope="col" id="title" class="manage-column column-fname">Fname</th>
			<th scope="col" id="title" class="manage-column column-lname">Lname</th>
			<th scope="col" id="title" class="manage-column column-email">Email</th>
			<th scope="col" id="title" class="manage-column column-date">Date</th>
		</tr>
		</thead>
		<tbody id="the-list">
			<?php
		if ( empty( $endpoint_data ) || false === $endpoint_data ) {
		?>
			<tr>
				<td colspan="5">There is no available data from the API at this time.</td>
			</tr>
		<?php
		} else {
			foreach ( $endpoint_data as $data ) {

				if ( null !== $data && isset ( $data->rows ) ) {
					foreach ( $data->rows as $row ) {
					?>
						<tr id="post-" class="iedit author-self level-0 post-3 type-page status-draft hentry entry">
							<td class="id column-id" data-colname="id"><?php echo $row->id; ?></td>
							<td class="fname column-fname" data-colname="fname"><?php echo $row->fname; ?></td>
							<td class="id column-lname" data-colname="lname"><?php echo $row->lname; ?></td>
							<td class="email column-email" data-colname="email"><?php echo $row->email; ?></td>
							<td class="date column-date" data-colname="date"><?php echo date( get_option( 'date_format'), $row->date ); ?></td>
						</tr>
			<?php
					}
				}
			}
		?>

		<?php
		}
		?>
				</tbody>

		<tfoot>
		<tr>
		<tr>
			<th scope="col" id="title" class="manage-column column-id column-primary">ID</th>
			<th scope="col" id="title" class="manage-column column-fname">Fname</th>
			<th scope="col" id="title" class="manage-column column-lname">Lname</th>
			<th scope="col" id="title" class="manage-column column-email">Email</th>
			<th scope="col" id="title" class="manage-column column-date">Date</th>
		</tr>
		</tr>
		</tfoot>

	</table>
	</div>
	<?php		
	}
}