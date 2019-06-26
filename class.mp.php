<?php

if ( !class_exists('mp') )
{
	class MP {
		private static $initiated = false;

		public static function init() {
			if ( ! self::$initiated ) {
				self::init_hooks();
			}
		}
		
		public static function init_hooks() {
			self::$initiated = true;
			require_once( MP__PLUGIN_DIR . 'class.mp-shortcodes.php' );
			add_action( 'wp_ajax_memberpress_endpoint', array( 'MP', 'memberpress_endpoint' ) );
			add_action( 'wp_ajax_nopriv_memberpress_endpoint', array( 'MP', 'memberpress_endpoint' ) );
		}

		// ajax endpoint that can be called from either the admin or the front-end
		public static function memberpress_endpoint() {
			$saved_endpoint_data = get_transient( 'memberpress_endpoint' );
            $post_method = filter_input( INPUT_POST, 'method', FILTER_SANITIZE_STRING );

			$endpoint_data = ( false === $saved_endpoint_data || !empty( $post_method ) && "refresh" == $post_method  ) ?  wp_remote_get( "https://cspf-dev-challenge.herokuapp.com/" ) : $saved_endpoint_data;

			if ( false === $saved_endpoint_data || !empty( $post_method ) && "refresh" == $post_method ) {

				if ( is_wp_error( $endpoint_data ) ) {
					return false;
				}

				$body = wp_remote_retrieve_body( $endpoint_data );

                if ( null !== json_decode( $body ) ) {
                    $data = json_decode( $body );

                    if ( !empty( $data ) ) {
                        set_transient( 'memberpress_endpoint', $data, 3600 );
                        echo json_encode( $data );
                    }
                }
				exit;
			} else {
				echo json_encode( $endpoint_data );
				exit;
			}
		}
	}
}

