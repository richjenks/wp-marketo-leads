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
		<h3>Marketo API</h3>
		<p>Follow Marketo's <a href="http://developers.marketo.com/blog/quick-start-guide-for-marketo-rest-api/" target="_blank">REST API Quick Start Guide</a> to setup a LaunchPoint service and get <code>Client ID</code> &amp; <code>Client Secret</code>.<br>Then go to the Munchkin settings page to get the <code>Munchkin ID</code>.</p>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="client_id">Client ID</label>
				</th>
				<td>
					<input type="text" class="regular-text" name="client_id" placeholder="Client ID" value="<?=$this->options['client_id'];?>" required>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="client_secret">Client Secret</label>
				</th>
				<td>
					<input type="text" class="regular-text" name="client_secret" placeholder="Client Secret" value="<?=$this->options['client_secret'];?>" required>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="munchkin_id">Munchkin ID</label>
				</th>
				<td>
					<input type="text" class="regular-text" name="munchkin_id" placeholder="Munchkin ID" value="<?=$this->options['munchkin_id'];?>" required>
				</td>
			</tr>
		</table>
		<hr>
		<h3>Plugin Options</h3>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="status">Status</label>
				</th>
				<td>
					<select name="status" id="status">
						<option <?php if ( $this->options['status'] === 'Disabled' ) echo 'selected'; ?>>Disabled</option>
						<option <?php if ( $this->options['status'] === 'Enabled' ) echo 'selected'; ?>>Enabled</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="debug">Debug</label>
				</th>
				<td>
					<select name="debug" id="debug">
						<option <?php if ( $this->options['debug'] === 'Disabled' ) echo 'selected'; ?>>Disabled</option>
						<option <?php if ( $this->options['debug'] === 'Enabled' ) echo 'selected'; ?>>Enabled</option>
					</select>
				</td>
			</tr>
		</table>
		<input type="hidden" name="rj_ml_options_submitted">
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
	</form>
</div>