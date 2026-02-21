<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://www.domedia.lk
 * @since      1.0.0
 *
 * @package    Ledo_Integrator
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;
$db_tables = array(
	$wpdb->prefix . 'ledo_integrator_forms',
	$wpdb->prefix . 'ledo_integrator_field_mapping'
);

foreach ( $db_tables as $key => $table_name ) {
	$wpdb->query( "DROP TABLE IF EXISTS $table_name" ); 
}

delete_option( 'ledo_integrator_company_access_token' );
delete_option( 'ledo_integrator_settings_utm_data' );