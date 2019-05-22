<?php
/*
Plugin Name: Memberpress Challenge
Plugin URI: https://github.com/cwuensche
Description: This is a plugin created for a memberpress interview challenge.
Version: 0.1
Author: Carl Wuensche
Author URI: https://github.com/cwuensche
Text Domain: mp
*/
if ( !function_exists( 'add_action' ) ) {
	echo 'Direct access violation.';
	exit;
}

define( 'MP__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MP_VERSION', '0.1' );

require_once( MP__PLUGIN_DIR . 'class.mp.php' );
add_action( 'init', array( 'MP', 'init' ) );

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once( MP__PLUGIN_DIR . 'class.mp-admin.php' );
	add_action( 'init', array( 'MP_Admin', 'init' ) );
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once( MP__PLUGIN_DIR . 'class.mp-cli.php' );
}