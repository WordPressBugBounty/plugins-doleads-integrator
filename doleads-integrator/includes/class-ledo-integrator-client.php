<?php
/**
 * Utility class to call Ledo APIs 
 *
 * This call provides methods to access Ledo APIs. This class have to be used 
 * across the plugin for accessing API endpoints
 *
 * @link       https://www.domedia.lk
 * @since      1.0.0
 *
 * @package    Ledo_Integrator
 * @subpackage Ledo_Integrator/includes
 */

/**
 * Utility class to call Ledo APIs 
 *
 * This call provides methods to access Ledo APIs. This class have to be used 
 * across the plugin for accessing API endpoints
 *
 * @since      1.0.0
 * @package    Ledo_Integrator
 * @subpackage Ledo_Integrator/includes
 * @author     Domedia <admin@domedia.lk>
 */

class Ledo_Integrator_Client {

	/**
	 * The Ledo Company Access Token
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $company_token    The Ledo Company Access Token.
	 */
	protected $company_token;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $company_token ) {
		$this->company_token = $company_token;
	}

	/**
	 * This retrieves Ledo fields.
	 *
	 * @since    1.0.0
	 */
	public function get_ledo_fields() {
		$ledo_fields = array(
			array(
				'key' => 'f_name',
				'name' => 'First Name',
				'required' => true,
			),
			array(
				'key' => 'l_name',
				'name' => 'Last Name',
				'required' => false,
			),
			array(
				'key' => 'phone_number',
				'name' => 'Phone Number',
				'required' => true,
			),
			array(
				'key' => 'email',
				'name' => 'Email Address',
				'required' => false,
			),
			array(
				'key' => 'message',
				'name' => 'Message',
				'required' => false,
			)
		);
		return $ledo_fields;
	}

	/**
	 * Push lead data to the Ledo API
	 *
	 * @since    1.0.0
	 */
	public function push_to_ledo( $type, $id, $data, $attachments=array() ){
		try {
			global $wpdb;
			$table_ledo_integrator_forms = $wpdb->prefix . 'ledo_integrator_forms';	
			$table_ledo_integrator_field_mapping = $wpdb->prefix . 'ledo_integrator_field_mapping';

			$sql = "SELECT A.id, A.ledo_group, A.group_token FROM " . $table_ledo_integrator_forms . " as A " .
				   "WHERE A.form_type = '".$type."' AND A.form = ".$id;
			$form_ids = $wpdb->get_results( $sql );

			if( $form_ids ){
				foreach ( $form_ids as $form_row ) {
					$sql = "SELECT A.ledo_field, A.form_field FROM ".
							$table_ledo_integrator_field_mapping . " A WHERE A.form_id = ".$form_row->id;
					$mappings = $wpdb->get_results( $sql );

					$fields = array();
					foreach ( $mappings as $map_row ) {
						if ( $map_row->form_field !== '' ) {
							$fields[$map_row->ledo_field] = $data[$map_row->form_field];
							unset( $data[$map_row->form_field] );
						}
					}

					// Append additional fields other than ledo pre defined form fields
					if( !empty( $data ) ){
						foreach ( $data as $key => $item ) {
							if( $item !== '' ){
								$sanitize_key = str_replace( '-', '_', $key );
								$fields[$sanitize_key] = ( is_array( $item ) && !empty( $item ) ) ? $item[0] : $item;
				            }
						}
					}

					// Adding group token and utm data / User data
					$fields['group_token'] = $form_row->group_token;
					//$fields = $this->append_utm_data( $fields, $_SERVER['HTTP_REFERER'] );
					$fields = $this->append_website_data( $fields );

					$ret = $this->execute_post_request( 'Weblead/add', $fields, $attachments );
					if( $ret['success'] == false ){
						error_log( 'DoLeads integrator error: '. $ret['message'], 0 );
					}
				}
			}

		} catch ( Exception $e ) {
			error_log( $e->getMessage(), 0 );
		}
	}

	/**
	 * Append UTM data to the existing data
	 *
	 * @since    1.0.0
	 */
	public function append_utm_data( $source_data, $url ){
		$components = parse_url( $url );
		if( isset( $components['query'] ) ){
			parse_str( $components['query'], $params );
			if( !empty( $params ) ){
				$supported_utms = array( 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content' );
				foreach ( $params as $key => $value ) {
					if( in_array( $key, $supported_utms ) ){
						$source_data[$key] = $value;
					}
				}
			}
		}

		return $source_data;	
	}

	/**
	 * Append Website data to the existing data
	 *
	 * @since    1.2.0
	 */
	public function append_website_data( $source_data ){
		$source_data['site_web_referer'] = $_SERVER['HTTP_REFERER']; // URL
		$source_data['site_web_user_agent'] = $_SERVER['HTTP_USER_AGENT']; // User Agent
		$source_data['site_web_ip_address'] = $_SERVER['REMOTE_ADDR']; // User IP address
		$source_data['site_web_request_time'] = $_SERVER['REQUEST_TIME']; // Request time

		$utm_setting = get_option( 'ledo_integrator_settings_utm_data' );

		if( isset( $_COOKIE['ledo_user_tracking'] ) ){
			$sanitized_content = html_entity_decode( stripslashes( $_COOKIE['ledo_user_tracking'] ) );
			$source_data['site_web_user_track'] = $sanitized_content;

			if ( $utm_setting == 1 ){
				foreach ( json_decode( $sanitized_content ) as $key => $url ) {
					$source_data = $this->append_utm_data( $source_data, $url );
				}
			}
		}else{
			if ( $utm_setting == 1 ){
				$source_data = $this->append_utm_data( $source_data, $_SERVER['HTTP_REFERER'] );
			}
		}
		
		return $source_data;
	}

	/**
	 * Actual post request to the API
	 *
	 * @since    1.0.0
	 */
	public function execute_post_request( $endpoint, $data, $attachments=array() ){

		$boundary = wp_generate_password( 24 );
		$headers  = array(
			'content-type' => 'multipart/form-data; boundary=' . $boundary,
		);
		$payload = '';

		// First, add the standard POST fields:
		foreach ( $data as $name => $value ) {
			$payload .= '--' . $boundary . "\r\n";
			$payload .= 'Content-Disposition: form-data; name="' . $name . '"' . "\r\n\r\n";
			$payload .= $value . "\r\n";
		}

		// Upload the files
		if( !empty( $attachments ) ){
			foreach ( $attachments as $name => $value ) {
				$payload .= '--' . $boundary . "\r\n";
				$payload .= 'Content-Disposition: form-data; name="' . $name . '"; filename="' . basename( $value ) . '"' . "\r\n\r\n";
				$payload .= file_get_contents( $value ). "\r\n";
			}
		}
		$payload .= '--' . $boundary . '--';

		$url = 'https://leads.docloud.global/api/'. $endpoint;
		$send_data = array(
			'headers' => $headers,
			'body' 	  => $payload
		);
	    
	    $response = wp_remote_post( $url, $send_data );
	    if( is_wp_error( $response ) ){
	    	return array( 'success' => false, 'message' => __( $response->get_error_message(), 'ledo' ) );
	    }else{
	    	$api_response = json_decode( wp_remote_retrieve_body( $response ), true );
	    	if( $api_response['response']['success'] ){
	    		return array( 'success' => true, 'data' => isset( $api_response['data'] ) ? $api_response['data'] : '' );
	    	}else{
	    		return array( 'success' => false, 'message' => __( $api_response['response']['statusMsg'], 'ledo' ) );
	    	}
	    }

	}

}