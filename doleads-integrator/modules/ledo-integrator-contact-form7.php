<?php
/**
 * The Contact form 7 integration of the plugin.
 *
 * @link       https://www.domedia.lk
 * @since      1.0.0
 *
 * @package    Ledo_Integrator
 * @subpackage Ledo_Integrator/modules
 */

/**
 * The Contact form 7 integration of the plugin.
 *
 * Defines the plugin name, plugin slug, and callback functions
 *
 * @package    Ledo_Integrator
 * @subpackage Ledo_Integrator/modules
 * @author     Domedia <admin@domedia.lk>
 */

class Ledo_Integrator_Contact_Form_7 {

    /**
     * Retrieves plugin slug
     *
     * @since    1.0.0
     */
    public function get_plugin_slug(){
        return "wp-contact-form-7";
    }

    /**
     * Retrieves the action tag of contact form 7
     *
     * @since    1.0.0
     */
    public function get_action_tag(){
        return "wpcf7_mail_sent";
    }

    /**
     * Retrieves contact form 7 plugin name
     *
     * @since    1.0.0
     */
    public function get_plugin_name(){
        return "Contact Form 7";
    }

    /**
     * Retrieves all forms registered to contact form 7
     *
     * @since    1.0.0
     */
    public function get_all_forms(){
        $result = array();
        $args = array( 'post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1 );
        if ( $cf7Forms = get_posts( $args ) ) {
            foreach ( $cf7Forms as $cf7Form ) {
                $result[] = array( 'id' => $cf7Form->ID, 'label' => $cf7Form->post_title );
            }
        }
        return $result;
    }

    /**
     * Retrieves the for fields related to a form
     *
     * @since    1.0.0
     */
    public function get_form_fields( $form_id ){
        $result = array();
        $contact_Form = WPCF7_ContactForm::get_instance( $form_id );
        $form_fields = $contact_Form->scan_form_tags();
        if( $form_fields ){
            foreach ( $form_fields as $field ) {
                $result[]   = array( 'id' => $field->name, 'label' => $field->type );
            }
        }

        return $result;
    }

    /**
     * Callback argument count
     *
     * @since    1.0.0
     */
    public function get_callback_argument_count(){
        return 1;
    }

    /**
     * Register contact form 7 callback function
     *
     * @since    1.0.0
     */
    public function handle_callback( $contact_form ){
        $submission = WPCF7_Submission::get_instance();
        if ( $submission ) {
            $form_id        = $contact_form->id();
            $posted_data    = $submission->get_posted_data();
            $uploaded_files = $submission->uploaded_files();

            if( !empty( $uploaded_files ) ){
                foreach ( $uploaded_files as $key => $value ) {
                    unset( $posted_data[$key] );
                }
            }

            do_action( 'ledo_integrator_push_to_ledo', __CLASS__, $form_id, $posted_data, $uploaded_files );
        }
    }
}