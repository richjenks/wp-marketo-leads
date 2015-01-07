<?php

/**
 * OptionsView
 *
 * HTML for Options page
 */

?>

<div class="wrap">
	<h2>Options</h2>
	<form method="post">

		<?php // API Options ?>
		<h3>Marketo API</h3>
		<p class="description">Follow Marketo's <a href="http://developers.marketo.com/blog/quick-start-guide-for-marketo-rest-api/" target="_blank">REST API Quick Start Guide</a> to setup a LaunchPoint service and get <code>Client ID</code> &amp; <code>Client Secret</code>.<br>Then go to the Munchkin settings page to get the <code>Munchkin ID</code>.</p>
		<table class="form-table">

			<?php // Client ID ?>
			<tr>
				<th scope="row">
					<label for="client_id">Client ID</label>
				</th>
				<td>
					<input type="text" class="regular-text" name="client_id" placeholder="Client ID" value="<?=$this->options->client_id;?>" required>
				</td>
			</tr>

			<?php // Client Secret ?>
			<tr>
				<th scope="row">
					<label for="client_secret">Client Secret</label>
				</th>
				<td>
					<input type="text" class="regular-text" name="client_secret" placeholder="Client Secret" value="<?=$this->options->client_secret;?>" required>
				</td>
			</tr>

			<?php // Munchkin ID ?>
			<tr>
				<th scope="row">
					<label for="munchkin_id">Munchkin ID</label>
				</th>
				<td>
					<input type="text" class="regular-text" name="munchkin_id" placeholder="Munchkin ID" value="<?=$this->options->munchkin_id;?>" required>
				</td>
			</tr>

		</table>

		<?php // Plugin Options ?>
		<hr>
		<h3>Plugin Options</h3>
		<table class="form-table">

			<?php // Plugin Status ?>
			<tr>
				<th scope="row">
					<label for="status">Plugin Status</label>
				</th>
				<td>
					<select name="status" id="status">
						<option <?php if ( $this->options->status === 'Disabled' ) echo 'selected'; ?>>Disabled</option>
						<option <?php if ( $this->options->status === 'Enabled' ) echo 'selected'; ?>>Enabled</option>
					</select>
					<p class="description">Whether the plugin sends leads to Marketo.</p>
				</td>
			</tr>

			<?php // Debug Mode ?>
			<tr>
				<th scope="row">
					<label for="debug">Debug Mode</label>
				</th>
				<td>
					<select name="debug" id="debug">
						<option <?php if ( $this->options->debug === 'Disabled' ) echo 'selected'; ?>>Disabled</option>
						<option <?php if ( $this->options->debug === 'Enabled' ) echo 'selected'; ?>>Enabled</option>
					</select>
					<p class="description">Whether debug information should be displayed, e.g. to find field <code>name</code>s.</p>
				</td>
			</tr>

			<?php // Action ?>
			<?php $options = array( 'Create/Update', 'Create only', 'Update only', 'Always Create' ); ?>
			<tr>
				<th scope="row">
					<label for="action">Action</label>
				</th>
				<td>
					<select name="action" id="action">
						<?php foreach ( $options as $option ): ?>
							<option <?php if ( $this->options->action === $option ) echo 'selected'; ?>><?= $option; ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description">How incoming leads should be treated. "Always Create" skips Marketo's duplication checks.</p>
				</td>
			</tr>

		</table>

		<?php // Global Fields ?>
		<hr>
		<h3>Global Fields</h3>
		<p class="description">Fields to be sent with every lead. One per line, whitespace is ignored.<br>Marketo field and value separate by a pipe "|" character, e.g. "leadSource | website".</p>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="global_fields">Global Fields</label>
				</th>
				<td>
					<textarea rows="5" cols="50" name="global_fields" id="global_fields"><?=$this->options->global_fields;?></textarea>
				</td>
			</tr>
		</table>
		<input type="hidden" name="rj_ml_options_submitted">
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>

	</form>
</div>
