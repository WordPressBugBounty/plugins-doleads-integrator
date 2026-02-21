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
	<h1 id="pg_title"></h1>
	<hr>

	<div id="div_notice" class="notice hidden"><p id="p_notice"></p></div>

	<form id="ledo_integrator_add_new_form" method="post" action="<?php echo admin_url( 'admin.php?page=ledo-integrator-integrated-forms' ); ?>">
		<input type="hidden" name="ledo_integrator_add_new_id" id="ledo_integrator_add_new_id" value="" />
		<input type="hidden" name="ledo_integrator_add_new_ledo_group_token" id="ledo_integrator_add_new_ledo_group_token" value="" />

		<table class="form-table">
			<tr>
				<th scope="row"><label for="ledo_integrator_add_new_title"><?php esc_html_e( 'Title', 'ledo' ); ?></label></th>
				<td><input type="text" class="regular-text" name="ledo_integrator_add_new_title" id="ledo_integrator_add_new_title" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="ledo_integrator_add_new_form_type"><?php esc_html_e( 'Form Type', 'ledo' ); ?></label></th>
				<td><select class="regular-text" name="ledo_integrator_add_new_form_type" id="ledo_integrator_add_new_form_type"></select></td>
			</tr>
			<tr>
				<th scope="row"><label for="ledo_integrator_add_new_plugin_form"><?php esc_html_e( 'Form', 'ledo' ); ?></label></th>
				<td><select class="regular-text" name="ledo_integrator_add_new_plugin_form" id="ledo_integrator_add_new_plugin_form"></select></td>
			</tr>
			<tr>
				<th scope="row"><label for="ledo_integrator_add_new_ledo_group"><?php esc_html_e( 'DoLeads Group', 'ledo' ); ?></label></th>
				<td><select class="regular-text" name="ledo_integrator_add_new_ledo_group" id="ledo_integrator_add_new_ledo_group"></select></td>
			</tr>

			<tr>
				<th scope="row"><label for="ledo_integrator_add_new_mapping_table"><?php esc_html_e( 'Field Mapping', 'ledo' ); ?></label></th>
				<td>
					<table class="widefat striped" id="ledo_integrator_add_new_mapping_table">
						<thead>
							<tr>
								<th class="row-title"><?php esc_attr_e( 'Doleads Field', 'ledo' ); ?></th>
								<th><?php esc_attr_e( 'Field Key', 'ledo' ); ?></th>
								<th><?php esc_attr_e( 'Validation', 'ledo' ); ?></th>
								<th width="25%"><?php esc_attr_e( 'Form Field', 'ledo' ); ?></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</td>
			</tr>
		</table>
		<hr>
		<table>
			<tr>
				<td>
					<input type="hidden" name="ledo_integrator_add_new_record" id="ledo_integrator_add_new_record" value="ledo_integrator_add_new_record" />
					<input class="button-primary" type="submit" id="ledo_integrator_add_new_submit" value="<?php esc_attr_e( 'Save Mapping', 'ledo' ); ?>" />
				</td>
			</tr>
		</table>
	</form>

	<div id="loading_screen" class="loader-screen"><span class="ls-spinner"></span></div>

</div>
