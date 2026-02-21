(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	var js_object = ledo_integrator_admin_settings;

	// Settings form submit
	$( document ).on( 'submit', '#ledo_integrator_settings_form', function(e){	
		e.preventDefault();
		if( is_validate() ){
			$( this )[0].submit();
		}
	});

	// Test authentication
	$( document ).on( 'click', '#ledo_integrator_settings_form_test_auth', function(){
		if( is_validate() ){
			$.ajax({
				type:"POST",
				url: ajaxurl,
				data: {
					'action': 'ledo_connection_auth',
					'company_access_token': $( "#ledo_integrator_company_access_token" ).val()
				},
				success:function( response ){
					if( response.success ){
						$( "#p_notice" ).text( response.data );
						$( "#div_notice" ).removeClass( 'hidden notice-error' ).addClass( 'notice-success' );
					}else{
						$( "#p_notice" ).text( response.data );
						$( "#div_notice" ).removeClass( 'hidden notice-success' ).addClass( 'notice-error' );
					}
				},
				error: function( response ){
					$( "#p_notice" ).text( js_object.li_as_notices_ajax_failed );
					$( "#div_notice" ).removeClass( 'hidden' ).addClass( 'notice-error' );
				}
			});
		}
	});

	// Check field validation
	function is_validate(){
		$( "#div_notice" ).addClass( 'hidden' );
		var error_found,
			error_field;

		if( !$( "#ledo_integrator_company_access_token" ).val() ){
			$( "#p_notice" ).text( js_object.li_as_error_company_token );
			error_found = true;
			error_field = "ledo_integrator_company_access_token";
		}

		if( error_found ){
			$( "#div_notice" ).removeClass( 'hidden' ).addClass( 'notice-error' );
			$( "#"+error_field ).focus();
			return false;
		}

		return true;
	}

	$(document).on({
	    ajaxSend: function( event, request, settings ) {
	    	if (settings['data'].match( 'action=ledo_connection_auth' )){
		    	$( '#loading_screen' ).addClass( "in" ); 
		    }
	    },
	    ajaxStop: function() {
	    	$( '#loading_screen' ).removeClass( "in" ); 
	    }    
	});


})( jQuery );
