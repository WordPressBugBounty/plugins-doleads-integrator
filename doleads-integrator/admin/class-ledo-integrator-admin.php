<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.domedia.lk
 * @since      1.0.0
 *
 * @package    Ledo_Integrator
 * @subpackage Ledo_Integrator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ledo_Integrator
 * @subpackage Ledo_Integrator/admin
 * @author     Domedia <admin@domedia.lk>
 */
class Ledo_Integrator_Admin {

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
	 * Ledo application link.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $app_link    Ledo application link.
	 */
	private $app_link;

	/**
	 * JavaScript Object tobe used in the javascript files
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $js_object    JavaScript Object tobe used in the javascript files
	 */
	private $js_object;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $app_link ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->app_link = $app_link;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ledo_Integrator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ledo_Integrator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ledo-integrator-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ledo_Integrator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ledo_Integrator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$this->js_object['ajaxurl'] = admin_url( 'admin-ajax.php' );
		$this->js_object['li_as_notices_ajax_failed'] = __( 'Request failed due to internal server error', 'ledo' );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		add_menu_page(
	        __( 'DoLeads Integrator', 'ledo' ),
	        __( 'DoLeads Integrator', 'ledo' ),
	        'manage_options',
	        $this->plugin_name,
	        array( $this, 'display_plugin_settings_page' )
    	);

    	add_submenu_page(
	        $this->plugin_name,
	        __( 'Integrated Forms', 'ledo' ),
	        __( 'Integrated Forms', 'ledo' ),
	        'manage_options',
	        'ledo-integrator-integrated-forms',
	        array( $this, 'display_plugin_integrated_forms_page' )
    	);

    	add_submenu_page(
	        $this->plugin_name,
	        __( 'Add New', 'ledo' ),
	        __( 'Add New', 'ledo' ),
	        'manage_options',
	        'ledo-integrator-add-new',
	        array( $this, 'display_plugin_add_new_page' )
    	);

    	add_submenu_page(
	        null,
	        __( 'Edit Mapping', 'ledo' ),
	        __( 'Edit Mapping', 'ledo' ),
	        'manage_options',
	        'ledo-integrator-edit-mapping',
	        array( $this, 'display_plugin_edit_mapping_page' )
    	);

    	add_submenu_page(
	        $this->plugin_name,
	        __( 'Settings', 'ledo' ),
	        __( 'Settings', 'ledo' ),
	        'manage_options',
	        'ledo-integrator-settings',
	        array( $this, 'display_plugin_settings_page' )
    	);

    	remove_submenu_page( $this->plugin_name, $this->plugin_name );

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_settings_page() {

		$this->js_object['li_as_error_company_token'] = __( 'Please provide a valid token', 'ledo' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ledo-integrator-admin-settings.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( $this->plugin_name, 'ledo_integrator_admin_settings', $this->js_object );

	    include_once ( 'partials/ledo-integrator-admin-display-settings.php' );

	}

	/**
	 * Render integrated forms page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_integrated_forms_page() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ledo-integrator-admin-integrated-forms.js', array( 'jquery' ), $this->version, true );

		if ( isset( $_POST['ledo_integrator_add_new_record'] ) ){
			$this->update_integrator_form_to_db( $_POST );
		}elseif( isset( $_POST['ledo_integrator_form_to_delete'] ) ){
			$form_id = sanitize_key( $_POST['ledo_integrator_form_to_delete'] );
			$this->delete_integrator_form( $form_id );
		}

		$result = $this->get_integrator_forms_from_db();

		wp_localize_script( $this->plugin_name, 'ledo_integrator_admin_integrated_forms', $this->js_object );
		include_once ( 'partials/ledo-integrator-admin-display-integrated-forms.php' );

	}

	/**
	 * Render common form mapping fr add new and edit pages for this plugin.
	 *
	 * @since    1.0.0
	 */
	private function include_common_form_mapping() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ledo-integrator-admin-add-new.js', array( 'jquery' ), $this->version, true );

		$this->js_object['li_as_str_select_form_type'] = __( 'Please select form type', 'ledo' );
		$this->js_object['li_as_str_select_form'] = __( 'Please select a form', 'ledo' );
		$this->js_object['li_as_str_select_form_fields'] = __( 'Please select form fields', 'ledo' );
		$this->js_object['li_as_str_select_group'] = __( 'Please select DoLeads group', 'ledo' );
		$this->js_object['li_as_str_form_type_change_warning'] = __( 'Change of form type will reset form and field mapping. Do you want to continue?', 'ledo' );
		$this->js_object['li_as_str_form_change_warning'] = __( 'Change of form will reset field mapping. Do you want to continue?', 'ledo' );
		$this->js_object['li_as_str_ledo_group_change_warning'] = __( 'Change of DoLeads Group will reset field mapping. Do you want to continue?', 'ledo' );

		$this->js_object['li_as_error_mapping_title'] = __( 'Please provide title', 'ledo' );
		$this->js_object['li_as_error_mapping_form_type'] = __( 'Please select form type', 'ledo' );
		$this->js_object['li_as_error_mapping_form'] = __( 'Please select form', 'ledo' );
		$this->js_object['li_as_error_mapping_group'] = __( 'Please select DoLeads Group', 'ledo' );
		$this->js_object['li_as_error_mapping_no_fields'] = __( 'No fields are mapped', 'ledo' );

		wp_localize_script( $this->plugin_name, 'ledo_integrator_admin_add_new', $this->js_object );

		include_once ( 'partials/ledo-integrator-admin-display-add-new.php' );

	}

	/**
	 * Render add new forms page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_add_new_page() {

		$this->js_object['li_as_label_page_title'] = __( 'Add New Field Mapping', 'ledo' );
		$this->include_common_form_mapping();

	}

	/**
	 * Render edit mapping page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_edit_mapping_page() {

		if ( isset( $_GET['form_id'] ) && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ){
			$form_id = sanitize_key( $_GET['form_id'] );
			$result = $this->get_integrator_forms_data( $form_id );
			$this->js_object['li_as_form_id'] = $result['form']->id;
			$this->js_object['li_as_form_title'] = $result['form']->title;
			$this->js_object['li_as_form_type'] = $result['form']->form_type;
			$this->js_object['li_as_form'] = $result['form']->form;
			$this->js_object['li_as_ledo_group'] = $result['form']->ledo_group;

			$this->js_object['li_as_mapped_fields'] = $result['form_fields'];
		}
		
		$this->js_object['li_as_label_page_title'] = __( 'Edit Field Mapping', 'ledo' );
		$this->include_common_form_mapping();

	}

	/**
	 * This tests Ledo connection. Called via AJAX service.
	 *
	 * @since    1.0.0
	 */
	public function ledo_connection_auth() {
		$access_token = sanitize_text_field( $_POST['company_access_token'] );

		if( ! empty( $access_token ) ){

			$client_api = new Ledo_Integrator_Client( $access_token );

			$send_data = array( 'ledo_token' => $access_token );
			$ret = $client_api->execute_post_request( 'Company/test_connection', $send_data );
			if( $ret['success'] == false ){
				wp_send_json_error( $ret['message'] );
			}else{
				wp_send_json_success( __( 'Authentication Successfull', 'ledo' ) );
			}

		}else{
			wp_send_json_error( __( 'Please check the Access Token and try again.', 'ledo' ) );
		}

		wp_die();
	}

	/**
	 * This gets all ledo groups from the API
	 *
	 * @since    1.0.0
	 */
	public function get_ledo_groups_from_service( $access_token ) {

		$client_api = new Ledo_Integrator_Client( $access_token );

		$send_data = array( 'ledo_token' => $access_token );
		$ret = $client_api->execute_post_request( 'Group/group_list', $send_data );
		if( $ret['success'] == false ){
			return array( 'success' => false, 'data' => __( 'No Groups found for this company in the DoLeads system. Please add a Group first.', 'ledo' ) );
		}else{
			return array( 'success' => true, 'data' => $ret['data'] );
		}

	}

	/**
	 * This gets all ledo groups. Called via AJAX service.
	 *
	 * @since    1.0.0
	 */
	public function get_ledo_groups() {

		$access_token = get_option( 'ledo_integrator_company_access_token' );
		if( $access_token ){
			$api_response = $this->get_ledo_groups_from_service( $access_token );
			if( $api_response['success'] ){
				wp_send_json_success( $api_response['data'] );
			}else{
				wp_send_json_error( $api_response['data'] );
			}
		}else{
			wp_send_json_error( __( 'Please authenticate first, via access token in the Settings panel', 'ledo' ) );
		}

		wp_die();
	}

	/**
	 * This gets all supported form types. Called via AJAX service.
	 *
	 * @since    1.0.0
	 */
	public function get_form_types() {

		global $ledo_integrator_form_modules;

		$result = array();
		foreach ( $ledo_integrator_form_modules as $module ) {
			$object = new $module();
			$result[$module] = $object->get_plugin_name();
		}

		wp_send_json_success( $result );
		wp_die();
	}

	/**
	 * This gets all forms related to a specific form type. Called via AJAX service.
	 *
	 * @since    1.0.0
	 */
	public function get_all_forms() {
		$form_type = sanitize_text_field( $_POST['form_type'] );

		if( !empty( $form_type ) ){
			$object = new $form_type();
			$all_forms = $object->get_all_forms();

			if( !empty( $all_forms ) ){
				$result = array();
				foreach ( $all_forms as $form ) {
					$result[$form['id']] = $form['label'];
				}

				wp_send_json_success( $result );
			}else{
				wp_send_json_error( __( 'No contact forms found. Please create a form in Contact form 7 before operating this settings.', 'ledo' ) );
			}
		}else{
			wp_send_json_error( __( 'Please select a form', 'ledo' ) );
		}
		wp_die();
	}

	/**
	 * This gets form fields. Called via AJAX service.
	 *
	 * @since    1.0.0
	 */
	public function get_form_fields() {
		$form_id = sanitize_key( $_POST['form_id'] );
		$form_type = sanitize_text_field( $_POST['form_type'] );

		if( !empty( $form_type ) && !empty( $form_id ) ){
			$object = new $form_type();
			$form_fields = $object->get_form_fields( $form_id );

			if( !empty( $form_fields ) ){
				$result = array();
				foreach ( $form_fields as $field ) {
					if( !empty( $field['id'] ) ){
						$result[$field['id']] = $field['label'].' '.$field['id'];
					}
				}

				wp_send_json_success( $result );
			}else{
				wp_send_json_error( __( 'No fields found. Please insert fields in the selected contact form before operating this settings.', 'ledo' ) );
			}
		}else{
			wp_send_json_error( __( 'Please select a form type and a form', 'ledo' ) );
		}
		wp_die();
	}

	/**
	 * This gets Ledo fields. Called via AJAX service.
	 *
	 * @since    1.0.0
	 */
	public function get_ledo_fields() {

		$access_token = get_option( 'ledo_integrator_company_access_token' );
		$client_api = new Ledo_Integrator_Client( $access_token );
		$fields = $client_api->get_ledo_fields();

		wp_send_json_success( $fields );
		
		wp_die();
	}

	/**
	 * This inserts / updates an integrator form to the database
	 *
	 * @since    1.0.0
	 */
	public function update_integrator_form_to_db( $data ) {

		global $wpdb;
		$table_ledo_integrator_forms = $wpdb->prefix . 'ledo_integrator_forms';	
		$table_ledo_integrator_field_mapping = $wpdb->prefix . 'ledo_integrator_field_mapping';

		$prepared_data = array(
			'title' => sanitize_text_field( $data['ledo_integrator_add_new_title'] ),
			'form_type' => sanitize_text_field( $data['ledo_integrator_add_new_form_type'] ),
			'form' => sanitize_text_field( $data['ledo_integrator_add_new_plugin_form'] ),
			'ledo_group' => sanitize_text_field( $data['ledo_integrator_add_new_ledo_group'] ),
			'group_token' => sanitize_text_field( $data['ledo_integrator_add_new_ledo_group_token'] )
		);

		if ( isset( $data['ledo_integrator_add_new_id'] ) ){
			$wpdb->update(
				$table_ledo_integrator_forms, 
				$prepared_data, 
				array( 'id' => sanitize_key( $data['ledo_integrator_add_new_id'] ) ), 
				array( '%s', '%s', '%s', '%s', '%s' ), 
				array( '%d' )
			);

			$parent_id = sanitize_key( $data['ledo_integrator_add_new_id'] );
			$wpdb->delete( $table_ledo_integrator_field_mapping, array( 'form_id' => $parent_id ), array( '%d' ) );
		}else{
			$wpdb->insert( $table_ledo_integrator_forms, $prepared_data );			
			$parent_id = $wpdb->insert_id;
		}

		foreach ( $data['li_form_fields'] as $key => $value ) {
			if( !empty( $value ) ){
				$prepared_data = array(
					'form_id' => $parent_id,
					'ledo_field' => $key,
					'form_field' => $value
				);
				$wpdb->insert( $table_ledo_integrator_field_mapping, $prepared_data );
			}
		}

		$this->js_object['li_as_notices_success_message'] = __( 'DoLeads Form Mapping is successfully saved', 'ledo' );
	}

	/**
	 * This retrieves integrator forms from the database
	 *
	 * @since    1.0.0
	 */
	public function get_integrator_forms_from_db() {

		global $wpdb;
		$table_ledo_integrator_forms = $wpdb->prefix . 'ledo_integrator_forms';	
		$table_ledo_integrator_field_mapping = $wpdb->prefix . 'ledo_integrator_field_mapping';

		$sql = "SELECT A.id, A.title, A.form_type, A.form, A.ledo_group, COUNT(B.id) as mapped_fields_count ".
			   "FROM ". $table_ledo_integrator_forms ." as A LEFT JOIN ".
			   $table_ledo_integrator_field_mapping." as B ON A.id = B.form_id GROUP BY A.id";
		$result = $wpdb->get_results( $sql );

		if( $result ){
			$access_token = get_option( 'ledo_integrator_company_access_token' );
			if( $access_token ){
				$api_response = $this->get_ledo_groups_from_service( $access_token );
				if( $api_response['success'] ){
					$group_list = $api_response['data'];

		    		foreach ( $result as $key => $value ) {
		    			$group_id = $value->ledo_group;
		    			$group_key = array_search( $group_id, array_column( $group_list, 'id' ) );
		    			$value->ledo_group_name = $group_list[$group_key]['name'];
		    		}
				}
			}
		}

		return $result;

	}

	/**
	 * This retrieves integrator form data from the database
	 *
	 * @since    1.0.0
	 */
	public function get_integrator_forms_data( $form_id ) {

		global $wpdb;
		$table_ledo_integrator_forms = $wpdb->prefix . 'ledo_integrator_forms';	
		$table_ledo_integrator_field_mapping = $wpdb->prefix . 'ledo_integrator_field_mapping';

		$sql_1 = "SELECT A.id, A.title, A.form_type, A.form, A.ledo_group FROM ". $table_ledo_integrator_forms .
			   " A WHERE A.id = ".$form_id;
		$result_1 = $wpdb->get_row( $sql_1 );

		$sql_2 = "SELECT A.ledo_field, A.form_field FROM ". $table_ledo_integrator_field_mapping . 
		         " A WHERE A.form_id = ".$result_1->id;
		$result_2 = $wpdb->get_results( $sql_2 );

		if( $result_1 && $result_2 ){
			$output = array(
				'form' => $result_1,
				'form_fields' => $result_2
			);
			return $output;
		}else{
			return false;
		}
	}

	/**
	 * This deletes an integrator form from the database
	 *
	 * @since    1.0.0
	 */
	public function delete_integrator_form( $form_id ) {

		global $wpdb;
		$table_ledo_integrator_forms = $wpdb->prefix . 'ledo_integrator_forms';	
		$table_ledo_integrator_field_mapping = $wpdb->prefix . 'ledo_integrator_field_mapping';

		$ret_1 = $wpdb->delete( $table_ledo_integrator_forms, array( 'id' => $form_id ), array( '%d' ) );
		$ret_2 = $wpdb->delete( $table_ledo_integrator_field_mapping, array( 'form_id' => $form_id ), array( '%d' ));

		if( $ret_1 && $ret_2 ){
			$this->js_object['li_as_notices_success_message'] = __( 'DoLeads Form Mapping is successfully deleted', 'ledo' );
		}
	}

}