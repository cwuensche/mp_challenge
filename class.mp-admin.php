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

	public static function load_resources( $hook_suffix ) {
        if ( 'settings_page_mp-endpoint-api' == $hook_suffix ) {
			wp_register_style( 'mp-admin.css', plugin_dir_url( __FILE__ ) . 'assets/css/mp-admin.css', array(), MP_VERSION );
			wp_enqueue_style( 'mp-admin.css');
        }
	}

	/*
		Add menu item for the admin page in the settings dropdown section 
	*/
	public static function memberpress_data_admin_menu() {
		add_options_page( 'Memberpress API', 'Memberpress API', 'manage_options', 'mp-endpoint-api', array( 'MP_Admin', 'mp_endpoint_data' ) );
	}

	public static function mp_endpoint_data() {
		// If we click on the refresh link, we will call the ajax endpoint to refresh the data.
	?>
	<div class="page-title-box">
	<img src="<?php echo plugin_dir_url(__FILE__); ?>assets/images/mp-logo-horiz-CMYK.jpg" width="452" height="72">
	</div>
	<div class="wrap">
	<h1 class="wp-heading-inline"><?php echo __('API Data', 'mp' ); ?></h1>
	<a href="<?php echo admin_url('options-general.php?page=mp-endpoint-api&action=refresh'); ?>" class="page-title-action">Refresh Data</a>
	<hr class="wp-header-end">
        <?php
        $memberpress_list = new MemberPress_List();
        $memberpress_list->prepare_items();
        $memberpress_list->display();
        ?>
	</div>
	<?php		
	}
}

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class MemberPress_List extends WP_list_Table {
    public function __construct() {
        parent::__construct( [
            'singular' => __( 'MemberPress List', 'mp' ),
            'plural' => __( 'MemberPress List', 'mp' ),
            'ajax' => false
        ] );
    }
    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        
        $data = $this->table_data();

        $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->items = $data;
    }
    public function get_hidden_columns() {
        return array();
    }
    public function get_sortable_columns() {
        return array( );
    }
    public function table_data() {
        $action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );
		if ( !empty( $action ) && "refresh" == $action ) {

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
        $data = array();

		if ( !empty( $endpoint_data ) && false !== $endpoint_data ) {
			foreach ( $endpoint_data as $edata ) {
				if ( null !== $edata && isset ( $edata->rows ) ) {
					foreach ( $edata->rows as $row ) {
                        $data[] = array(
                            'id' => $row->id,
                            'fname' => $row->fname,
                            'lname' => $row->lname,
                            'email' => $row->email,
                            'date' => date( get_option( 'date_format'), $row->date )
                        );
					}
				}
			}
		}

        return $data;
    }
    function get_columns() {
        $columns = [
            'id' => __( 'ID', 'mp' ),
            'fname' => __( 'First Name', 'mp' ),
            'lname' => __( 'Last Name', 'mp' ),
            'email' => __( 'Email', 'mp' ),
            'date' => __( 'Date', 'mp' ),
        ];

        return $columns;
    }
    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'id':
            case 'fname':
            case 'lname':
            case 'email':
            case 'date':
            default:
                return $item[$column_name ];
        }
    }
}