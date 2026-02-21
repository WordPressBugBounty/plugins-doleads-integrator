<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.domedia.lk
 * @since      1.0.0
 *
 * @package    Ledo_Integrator
 * @subpackage Ledo_Integrator/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ledo_Integrator
 * @subpackage Ledo_Integrator/public
 * @author     Domedia <admin@domedia.lk>
 */
class Ledo_Integrator_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version    			  The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'wp_footer', array( $this, 'track_user_navigation' ), 100 );
	}

	/**
	 * Track down the pages user visits
	 *
	 * @since    1.2.0
	 */
	public function track_user_navigation() {
		ob_start();
		?>
		<script type="text/javascript">
		if ( typeof(Storage) != "undefined" ) {
			var storage_data = window.localStorage.getItem( 'ledo_user_tracking' );

			if( storage_data != undefined || storage_data != null ){
				storage_data = JSON.parse( storage_data );
				storage_data.push( window.location.href );
			}else{
				storage_data = [ window.location.href ]; 
			}

			var data_string = JSON.stringify( storage_data );
			window.localStorage.setItem( 'ledo_user_tracking', data_string );
			document.cookie = "ledo_user_tracking="+ data_string +"; path=/"; // We use cookies to access this variable from server side
		}
		</script>
		<?php
		echo ob_get_clean();
	}

}
