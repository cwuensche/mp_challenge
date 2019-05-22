<?php

class MP_CLI {
	/*
	 * WP CLI function for clearing the transient.
	*/
	public static function memberpress_clear_endpoint_table_data( ) {
		$saved_endpoint_data = get_transient( 'memberpress_endpoint' );
		if ( false !== $saved_endpoint_data ) {
			delete_transient( 'memberpress_endpoint' );
			WP_CLI::success( 'You may now fetch new data from the endpoint!' );
		} else {
			WP_CLI::error( 'No data saved to clear from the memberpress endpoint.' );
		}
	}
}

	WP_CLI::add_command( 'memberpress_clear_endpoint_table_data', array( 'MP_CLI', 'memberpress_clear_endpoint_table_data' ) );