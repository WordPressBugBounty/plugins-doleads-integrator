<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.domedia.lk
 * @since      1.0.0
 *
 * @package    Ledo_Integrator
 * @subpackage Ledo_Integrator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ledo_Integrator
 * @subpackage Ledo_Integrator/includes
 * @author     Domedia <admin@domedia.lk>
 */
class Ledo_Integrator_Activator {

	/**
	 * Activates the plugin.
	 *
	 * Creates options and tables in the database when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		add_option( 'ledo_integrator_company_access_token' );
		add_option( 'ledo_integrator_settings_utm_data', 1 );

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'ledo_integrator_forms';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'") != $table_name ) {
			$sql = "CREATE TABLE $table_name (
			    id int(11) NOT NULL AUTO_INCREMENT,
			    title varchar(255) DEFAULT '' NOT NULL,
			    form_type varchar(255) DEFAULT '' NOT NULL,
			    form varchar(255) DEFAULT '' NOT NULL,
			    ledo_group varchar(255) DEFAULT '' NOT NULL,
			    group_token varchar(255) DEFAULT '' NOT NULL,
			    UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		$table_name = $wpdb->prefix . 'ledo_integrator_field_mapping';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'") != $table_name ) {
			$sql = "CREATE TABLE $table_name (
			    id int(11) NOT NULL AUTO_INCREMENT,
			    form_id int(11) NOT NULL,
			    ledo_field varchar(255) DEFAULT '' NOT NULL,
			    form_field varchar(255) DEFAULT '' NOT NULL,
			    UNIQUE KEY id (id)
			) $charset_collate;";
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

	}

}
