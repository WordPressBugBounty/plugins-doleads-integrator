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

	var js_object = ledo_integrator_admin_add_new;
	var form_fields, ledo_fields;
	var selected_form_type, selected_form, selected_ledo_group;

	$( "#pg_title" ).text( js_object.li_as_label_page_title );

	if ( js_object['li_as_form_title'] ){
		$( '#ledo_integrator_add_new_id' ).val( js_object.li_as_form_id );		
		$( '#ledo_integrator_add_new_title' ).val( js_object.li_as_form_title );
	}else{
		$( '#ledo_integrator_add_new_id' ).remove();
	}

	$(document).ready(function (){

		// Get Form Types
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				'action': 'get_form_types'
			},
			success:function( response ){
				var form_types = response.data;
                var $el = $( "#ledo_integrator_add_new_form_type" );

                $el.append( $( "<option></option>" ).attr( "value", '').text( js_object.li_as_str_select_form_type ) );

                $.each( form_types, function( key, value ) {
                    $el.append( $( "<option></option>" ).attr( "value", key ).text( value ) );
                });

				if ( js_object['li_as_form_type'] ){
					$el.val( js_object.li_as_form_type );
					$el.trigger( "change" );
				}
			},
			error: function( response ){
				$( "#p_notice" ).text( js_object.li_as_notices_ajax_failed );
				$( "#div_notice" ).removeClass( 'hidden' ).addClass( 'notice-error' );
			}
		});

		// Get Ledo Groups
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				'action': 'get_ledo_groups'
			},
			success:function( response ){
				if( response.success ){
					var groups = response.data;
	                var $el = $( "#ledo_integrator_add_new_ledo_group" );

	                $el.empty().append( $( "<option></option>" ).attr( "value", '').text( js_object.li_as_str_select_group ) );

	                $.each( groups, function( key, value ) {
	                    $el.append( $( "<option></option>" ).attr({
	                    	'value': value.id,
	                    	'group_token': value.group_token
	                    }).text( value.name ) );
	                });

					if ( js_object['li_as_ledo_group'] ){
						$el.val( js_object.li_as_ledo_group );
						$el.trigger( "change" );
					}
				}else{
					$( "#p_notice" ).text( response.data );
					$( "#div_notice" ).removeClass( 'hidden' ).addClass( 'notice-error' );
				}
			},
			error: function( response ){
				$( "#p_notice" ).text( js_object.li_as_notices_ajax_failed );
				$( "#div_notice" ).removeClass( 'hidden' ).addClass( 'notice-error' );
			}
		});

	});

	// Get forms related to specific form type
	$( '#ledo_integrator_add_new_form_type' ).on( 'change', function(e) {

		$( "#div_notice" ).addClass( 'hidden' );

		if ( selected_form_type && selected_form ){
			var txt;
			var r = confirm( js_object.li_as_str_form_type_change_warning );
			if ( r !== true ) {
				e.preventDefault();
				this.value = selected_form_type;
				return;
			}
            $( "#ledo_integrator_add_new_plugin_form" ).empty();
            selected_form_type = this.value;
		}

		if ( this.value ){
			selected_form_type = this.value;
			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					'action': 'get_all_forms',
					'form_type': this.value
				},
				success:function( response ){
	                var $el = $( "#ledo_integrator_add_new_plugin_form" );

	                if( response.success ){
		                $el.empty().append( $( "<option></option>" ).attr( "value", '' ).text( js_object.li_as_str_select_form ) );

		                $.each( response.data, function( key, value ) {
		                    $el.append( $( "<option></option>" ).attr( "value", key ).text( value ) );
		                });
						if ( js_object['li_as_form'] ){
							$el.val( js_object.li_as_form );
							$el.trigger( "change" );
						}
					}else{
						$( "#p_notice" ).text( response.data );
						$( "#div_notice" ).removeClass( 'hidden' ).addClass( 'notice-error' );
					}
				},
				error: function( response ){
					$( "#p_notice" ).text( js_object.li_as_notices_ajax_failed );
					$( "#div_notice" ).removeClass( 'hidden' ).addClass( 'notice-error' );
				}
			});
		}
	});

	// Get form fields
	$( '#ledo_integrator_add_new_plugin_form' ).on( 'change', function(e) {
		if ( selected_form ){
			var txt;
			var r = confirm( js_object.li_as_str_form_change_warning );
			if ( r !== true ) {
				e.preventDefault();
				this.value = selected_form;
				return;
			}
		}

		if ( this.value ){
			selected_form = this.value;
			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					'action': 'get_form_fields',
					'form_type': $( '#ledo_integrator_add_new_form_type' ).val(),
					'form_id': this.value
				},
				success:function( response ){
	                if( response.success ){
	                	form_fields = response.data;

		    			if ( ledo_fields ){
							$.each( ledo_fields, function( key, obj ) {
								var selector_identifier = 'ff-'+ obj.key;
								var $ff_el = $( '#'+ selector_identifier );
		    					$ff_el.empty().show();

				                $ff_el.append( $( "<option></option>" ).attr( "value", '' ).text( js_object.li_as_str_select_form_fields ) );
				                $.each( form_fields, function( key_field, obj_field ) {
				                	$ff_el.append( $( "<option></option>" ).attr( "value", key_field ).text( obj_field ) );
				                });
							});

							if ( form_fields && js_object['li_as_mapped_fields'] ){
				                $.each( js_object.li_as_mapped_fields, function( key, value ) {
				                	if ( value['form_field'] ){
					                	$( '#ff-'+value['ledo_field'] ).val( value['form_field'] );
				                	}
				                } );
							}
						}
					}else{
						$( "#p_notice" ).text( response.data );
						$( "#div_notice" ).removeClass( 'hidden' ).addClass( 'notice-error' );
					}
				},
				error: function( response ){
					$( "#p_notice" ).text( js_object.li_as_notices_ajax_failed );
					$( "#div_notice" ).removeClass( 'hidden' ).addClass( 'notice-error' );
				}
			});
		}else{
			if ( ledo_fields ){
				$.each( ledo_fields, function( key, obj ) {
					var $ff_el = $( '#ff-'+ obj.key );
		    		$ff_el.empty().hide();
				});
			}
		}
	});

	// Get Ledo fields
	$( '#ledo_integrator_add_new_ledo_group' ).on( 'change', function(e) {
		if ( selected_ledo_group && selected_form ){
			var txt;
			var r = confirm( js_object.li_as_str_ledo_group_change_warning );
			if ( r !== true ) {
				e.preventDefault();
				this.value = selected_ledo_group;
				return;
			}
			$( "#ledo_integrator_add_new_mapping_table > tbody" ).html("");
		}

		if ( this.value ){
			selected_ledo_group = this.value;
			$( '#ledo_integrator_add_new_ledo_group_token' ).val( $( 'option:selected', this ).attr( 'group_token' ) );

			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					'action': 'get_ledo_fields',
					'ledo_group': this.value
				},
				success:function( response ){
	                if( response.success ){

	                	ledo_fields = response.data;
	                	var $row = $( "#ledo_integrator_add_new_mapping_table > tbody" );
	                	$row.html("");

	                	var to_append = '';
		    			$.each( ledo_fields, function( key, obj ) {
		    				var selector_identifier = 'ff-'+ obj.key;
		    				to_append = 
		    				'<tr>' +
								'<td>'+ obj.name +'</td>' +
								'<td><code>'+ obj.key +'</code></td>' +
								'<td>'+( ( obj.required )?'<mark>required<mark>' : 'optional' )+'</td>' +
								'<td><select name="li_form_fields['+ obj.key +']" id="'+ selector_identifier +'" class="regular-text"></select></td>' +
							'</tr>';
							$row.append( to_append );

							setTimeout(function(){
								if ( form_fields ){
					                var $ff_el = $( '#'+ selector_identifier );
					                $ff_el.append( $( "<option></option>" ).attr( "value", '' ).text( js_object.li_as_str_select_form_fields ) );

					                $.each( form_fields, function( key_field, obj_field ) {
					                	$ff_el.append( $( "<option></option>" ).attr( "value", key_field ).text( obj_field ) );
					                });
								}

								if ( $( '#'+ selector_identifier ).find( 'option[value != ""]' ).length == 0 ) {
			    					$( '#'+ selector_identifier ).hide();
								}
							}, 1000);
		    			});

					}else{
						$( "#p_notice" ).text( response.data );
						$( "#div_notice" ).removeClass( 'hidden' ).addClass( 'notice-error' );
					}
				},
				error: function( response ){
					$( "#p_notice" ).text( js_object.li_as_notices_ajax_failed );
					$( "#div_notice" ).removeClass( 'hidden' ).addClass( 'notice-error' );
				}
			});
		}
	});

	// Field Mapping form submit
	$( document ).on( 'submit', '#ledo_integrator_add_new_form', function(e){	
		e.preventDefault();
		if( is_validate() ){
			$( this )[0].submit();
		}
	});

	// Check field validation
	function is_validate(){
		$( "#div_notice" ).addClass( 'hidden' );
		var error_found,
			error_field;

		if( !$( "#ledo_integrator_add_new_title" ).val() ){
			$( "#p_notice" ).text( js_object.li_as_error_mapping_title );
			error_found = true;
			error_field = "ledo_integrator_add_new_title";
		}else if( !$( "#ledo_integrator_add_new_form_type" ).val() ){
			$( "#p_notice" ).text( js_object.li_as_error_mapping_form_type );
			error_found = true;
			error_field = "ledo_integrator_add_new_form_type";
		}else if( !$( "#ledo_integrator_add_new_plugin_form" ).val() ){
			$( "#p_notice" ).text( js_object.li_as_error_mapping_form );
			error_found = true;
			error_field = "ledo_integrator_add_new_plugin_form";
		}else if( !$( "#ledo_integrator_add_new_ledo_group" ).val() ){
			$( "#p_notice" ).text( js_object.li_as_error_mapping_group );
			error_found = true;
			error_field = "ledo_integrator_add_new_ledo_group";
		}

		if ( !error_found ){
			$.each( ledo_fields, function( key, obj ) {
				if( $( '#ff-'+ obj.key ).val() ){
					error_found = false;
					return false;
				}
				$( "#p_notice" ).text( js_object.li_as_error_mapping_no_fields );
				error_found = true;
				error_field = "ledo_integrator_add_new_ledo_group";
			});
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
	    	if ( settings['data'] ){
		    	if ( settings['data'].match( 'action=get_form_types' ) ||
		    		 settings['data'].match( 'action=get_ledo_groups' ) ||
		    		 settings['data'].match( 'action=get_all_forms' ) ||
		    		 settings['data'].match( 'action=get_form_fields' ) ||
		    		 settings['data'].match( 'action=get_ledo_fields' ) ){
			    	$( '#loading_screen' ).addClass( "in" ); 
			    }
			}
	    },
	    ajaxStop: function() {
	    	$( '#loading_screen' ).removeClass( "in" ); 
	    }    
	});

})( jQuery );
