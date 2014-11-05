<?php

/**
 * OptionsView
 *
 * HTML for Options page
 */

?>

<div class="wrap">
	<h2>Marketo Leads Options</h2>
	<p>Follow Marketo's <a href="http://developers.marketo.com/blog/quick-start-guide-for-marketo-rest-api/" target="_blank">REST API Quick Start Guide</a> to setup a LaunchPoint service and get <code>Client ID</code> &amp; <code>Client Secret</code>, then go to the Munchkin settings page to get the <code>Munchkin ID</code>.</p>
	<form method="post">
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
		<input type="hidden" name="rj_ml_options_submitted">
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
	</form>
</div>