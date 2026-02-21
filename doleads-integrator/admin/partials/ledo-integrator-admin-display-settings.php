<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.domedia.lk
 * @since      1.0.0
 *
 * @package    Ledo_Integrator
 * @subpackage Ledo_Integrator/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap ledo-admin-wrap">
	<h1><?php esc_html_e( 'DoLeads Integrator Settings', 'ledo' ); ?></h1>
	<hr>

	<?php
	if ( isset( $_POST['ledo_integrator_settings_form_properties'] ) ):
		$company_access_token = sanitize_text_field( $_POST['ledo_integrator_company_access_token'] );
		$utm_data = sanitize_key( $_POST['ledo_integrator_settings_utm_data'] );
		update_option( 'ledo_integrator_company_access_token', $company_access_token );
		update_option( 'ledo_integrator_settings_utm_data', $utm_data ? 1 : 0 );
	?>
		<div id="div_notice" class="notice notice-success"><p id="p_notice"><?php esc_html_e( 'Settings successfully saved', 'ledo' ); ?></p></div>
	<?php else: ?>
		<div id="div_notice" class="notice hidden"><p id="p_notice"></p></div>
	<?php endif; ?>

	<form id="ledo_integrator_settings_form" method="post" action="<?php echo admin_url( 'admin.php?page=ledo-integrator-settings' ); ?>">
		<table class="form-table">
			<tr>
				<th scope="row"><label for="ledo_integrator_company_access_token"><?php esc_html_e( 'Company Access Token', 'ledo' ); ?></label></th>
				<td>
					<input type="text" class="regular-text" name="ledo_integrator_company_access_token" id="ledo_integrator_company_access_token" value="<?php echo get_option( 'ledo_integrator_company_access_token' ); ?>" />
					<input class="button-secondary" type="button" id="ledo_integrator_settings_form_test_auth" value="<?php esc_attr_e( 'Test Authentication', 'ledo' ); ?>"/>
					<p class="description"><?php _e( '<a href="'.$this->app_link.'" target="_blank">Login</a> to the DoLeads system and go to <i>Settings->General Tab</i>. Copy the <i>DoLeads access token</i> and paste it here.', 'ledo' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'UTM Data', 'ledo' ); ?></th>
				<td>
					<label for="ledo_integrator_settings_utm_data">
						<input type="checkbox" name="ledo_integrator_settings_utm_data" id="ledo_integrator_settings_utm_data" value="1" <?php echo get_option( 'ledo_integrator_settings_utm_data' ) ? 'checked' : ''; ?> > <?php esc_html_e( 'Send UTM data to the DoLeads system', 'ledo' ); ?>
					</label>
				</td>
			</tr>
		</table>
		<hr>
		<table>
			<tr>
				<td>
					<input type="hidden" name="ledo_integrator_settings_form_properties" id="ledo_integrator_settings_form_properties" value="ledo_integrator_settings_form_properties" />
					<input class="button-primary" type="submit" id="ledo_integrator_settings_form_submit" value="<?php esc_attr_e( 'Save Changes', 'ledo' ); ?>" />
				</td>
			</tr>
		</table>
	</form>

	<div id="loading_screen" class="loader-screen"><span class="ls-spinner"></span></div>
</div>
