<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.domedia.lk
 * @since      1.0.0
 *
 * @package    Ledo_Integrator
 * @subpackage Ledo_Integrator/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ledo_Integrator
 * @subpackage Ledo_Integrator/includes
 * @author     Domedia <admin@domedia.lk>
 */

class Ledo_Integrator {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ledo_Integrator    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $ledo_integrator    The string used to uniquely identify this plugin.
	 */
	protected $ledo_integrator;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Ledo application link.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $app_link    Ledo application link.
	 */
	protected $app_link;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->ledo_integrator = 'ledo-integrator';
		$this->version = '1.2.0';
		$this->app_link = 'https://leads.docloud.global';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_form_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ledo_Integrator_Loader. Orchestrates the hooks of the plugin.
	 * - Ledo_Integrator_i18n. Defines internationalization functionality.
	 * - Ledo_Integrator_Admin. Defines all hooks for the admin area.
	 * - Ledo_Integrator_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ledo-integrator-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ledo-integrator-i18n.php';

		/**
		 * The class responsible for calling API endpoints
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ledo-integrator-client.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ledo-integrator-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ledo-integrator-public.php';

		$this->loader = new Ledo_Integrator_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ledo_Integrator_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ledo_Integrator_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ledo_Integrator_Admin( $this->get_plugin_name(), $this->get_version(), $this->app_link );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'wp_ajax_ledo_connection_auth', $plugin_admin, 'ledo_connection_auth' );
		$this->loader->add_action( 'wp_ajax_nopriv_ledo_connection_auth', $plugin_admin, 'ledo_connection_auth' );

		$this->loader->add_action( 'wp_ajax_get_form_types', $plugin_admin, 'get_form_types' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_form_types', $plugin_admin, 'get_form_types' );

		$this->loader->add_action( 'wp_ajax_get_ledo_groups', $plugin_admin, 'get_ledo_groups' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_ledo_groups', $plugin_admin, 'get_ledo_groups' );

		$this->loader->add_action( 'wp_ajax_get_all_forms', $plugin_admin, 'get_all_forms' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_all_forms', $plugin_admin, 'get_all_forms' );

		$this->loader->add_action( 'wp_ajax_get_form_fields', $plugin_admin, 'get_form_fields' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_form_fields', $plugin_admin, 'get_form_fields' );

		$this->loader->add_action( 'wp_ajax_get_ledo_fields', $plugin_admin, 'get_ledo_fields' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_ledo_fields', $plugin_admin, 'get_ledo_fields' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ledo_Integrator_Public( $this->get_plugin_name(), $this->get_version() );

		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_form_hooks() {

		global $ledo_integrator_form_modules;
		if ( isset( $ledo_integrator_form_modules ) ){
			foreach ( $ledo_integrator_form_modules as $module ) {
				$object = new $module();
				$this->loader->add_action( 
					$object->get_action_tag(), 
				    $object, 
				    'handle_callback',
				    10, $object->get_callback_argument_count()
				);
			}
		}

		$client = new Ledo_Integrator_Client( get_option( 'ledo_integrator_company_access_token' ) );
		$this->loader->add_action( 'ledo_integrator_push_to_ledo', $client, 'push_to_ledo', 10, 4 );

	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->ledo_integrator;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ledo_Integrator_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}