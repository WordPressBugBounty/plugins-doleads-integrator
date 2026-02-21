<?php

/**
 * The plugin bootstrap file 
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://leads.docloud.global
 * @since             1.0.0
 * @package           Ledo_Integrator
 *
 * @wordpress-plugin
 * Plugin Name:       DoLeads Integrator
 * Description:       DoLeads Integrator plugin connects your wordpress website contact form with 'DoLeads' Leads Management System
 * Version:           1.2.2
 * Author:            Domedia
 * Author URI:        https://www.domedia.lk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ledo
 * Domain Path:       /languages
 */

// Make sure we don't expose any info if called directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LEDO_INTEGRATOR_VERSION', '1.2.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_ledo_integrator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ledo-integrator-activator.php';
	Ledo_Integrator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_ledo_integrator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ledo-integrator-deactivator.php';
	Ledo_Integrator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ledo_integrator' );
register_deactivation_hook( __FILE__, 'deactivate_ledo_integrator' );

/*
 * Register class names of modules here.
 */
$ledo_integrator_form_modules = array( 
	'Ledo_Integrator_Contact_Form_7'
);

/**
 * Include all of the form integration files
 */
foreach ( glob( dirname( __FILE__ ) . "/modules/*.*" ) as $filename ) {
	require_once $filename;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ledo-integrator.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ledo_integrator() {

	$plugin = new Ledo_Integrator();
	$plugin->run();

}
run_ledo_integrator();