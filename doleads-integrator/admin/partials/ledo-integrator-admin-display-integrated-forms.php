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

	<h1><?php esc_html_e( 'DoLeads Integrator', 'ledo' ); ?></h1>
	<div id="div_notice" class="notice hidden"><p id="p_notice"></p></div>

	<table>
		<tr>
			<td>
				<form method="post" action="<?php echo admin_url( 'admin.php?page=ledo-integrator-add-new'); ?>">
					<input class="button-primary" type="submit" value="<?php esc_html_e( 'Add New Form Mapping', 'ledo' ); ?>" />
				</form>	
			</td>
		</tr>
	</table>

	<hr />
	
	<?php if( $result ): ?>
		<form id="ledo_integrator_form_delete" method="post" action="<?php echo admin_url( 'admin.php?page=ledo-integrator-integrated-forms' ); ?>">
			<input type="hidden" id="ledo_integrator_form_to_delete" name="ledo_integrator_form_to_delete" value="" >
		</form>

		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_attr_e( 'Title', 'ledo' ); ?></th>
					<th><?php esc_attr_e( 'Form Type', 'ledo' ); ?></th>
					<th><?php esc_attr_e( 'Form ID', 'ledo' ); ?></th>
					<th><?php esc_attr_e( 'DoLeads Group', 'ledo' ); ?></th>
					<th><?php esc_attr_e( 'Fields Mapped', 'ledo' ); ?></th>
					<th><?php esc_attr_e( 'Actions', 'ledo' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $result as $row ): ?>
					<tr>
						<td><?php echo esc_html( $row->title ); ?></td>
						<?php 
							$formtype = $row->form_type; 
							$object = new $formtype();
							$plugin_name = $object->get_plugin_name();
						?>
						<td><?php echo esc_html( $plugin_name ); ?></td>
						<td><?php echo esc_html( $row->form ); ?></td>
						<td><?php echo esc_html( isset( $row->ledo_group_name ) ? $row->ledo_group_name : $row->ledo_group ); ?></td>
						<td><?php echo esc_html( $row->mapped_fields_count ); ?></td>
						<td>
							<div class="row-actions">
								<?php printf( '<span class="edit"><a href="%1$s">%2$s</a></span>', admin_url( 'admin.php?page=ledo-integrator-edit-mapping&action=edit&form_id='. $row->id ), esc_attr__( 'Edit', 'ledo' ) ); ?>
								|
								<span class="trash" onclick="if ( confirm( 'Please confirm to delete.' ) ) {document.getElementById( 'ledo_integrator_form_to_delete' ).value = '<?php echo $row->id ?>'; document.getElementById( 'ledo_integrator_form_delete' ).submit(); return true;} return false;"><?php echo esc_attr__( 'Delete', 'ledo' ); ?></span>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

</div>
