<?php

/**
 * OptionsPage
 *
 * Creates page for options, cURL/API test & help
 */

namespace RichJenks\MarketoLeads;

class OptionsPage {

	/**
	 * @var string Post Type
	 */

	private $post_type = 'rj_ml_cpt_fields';

	/**
	 * @var array Options array
	 */

	private $options;

	/**
	 * __construct
	 *
	 * Start the magic...
	 */

	public function __construct() {

		// Add submenu page
		add_action( 'admin_menu', function() {
			add_submenu_page( 'edit.php?post_type=' . $this->post_type, 'Marketo Leads Options', 'Options', 'manage_options', 'marketo-leads-options', array( $this, 'content' ) );
		} );

		// Check if option should be updated
		if ( isset( $_POST['marketo-leads-options'] ) ) {
			update_option( 'marketo-leads', $this->obfuscate( array(
				'endpoint' => mysql_real_escape_string( $_POST['endpoint'] ),
				'token'    => mysql_real_escape_string( $_POST['token'] ),
				'url'    => mysql_real_escape_string( $_POST['url'] ),
			) ) );
		}

		// Get current values
		$this->options = $this->deobfuscate( get_option( 'marketo-leads', $this->obfuscate( array(
			'endpoint' => '',
			'token'    => '',
			'url'      => '/rest/v1/leads.json',
		) ) ) );

	}

	/**
	 * content
	 *
	 * HTML content for submenu page
	 */

	public function content() { ?>

		<div class="wrap">
			<h1>Marketo Leads Options</h1>
			<form method="post">
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="endpoint">Endpoint</label>
						</th>
						<td>
							<input type="text" class="regular-text" name="endpoint" placeholder="Endpoint" value="<?=$this->options['endpoint'];?>" required>
							<p class="description">Marketo > Admin > Integration > Web Services > REST API</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="url">Leads URL</label>
						</th>
						<td>
							<input type="text" class="regular-text" name="url" placeholder="URL" value="<?=$this->options['url'];?>" required>
							<p class="description"><a href="http://developers.marketo.com/documentation/rest/createupdate-leads/" target="_blank">developers.marketo.com/documentation/rest/createupdate-leads</a></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="token">Token</label>
						</th>
						<td>
							<input type="text" class="regular-text" name="token" placeholder="Token" value="<?=$this->options['token'];?>" required>
							<p class="description">Marketo > Admin > Integration > LaunchPoint > View Details</p>
						</td>
					</tr>
				</table>
				<input type="hidden" name="marketo-leads-options">
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"> <a class="button" onclick="alert('Feature coming soon!');">Test Settings</a></p>
			</form>
		</div>

	<?php }

	/**
	 * obfuscate
	 *
	 * Obfuscates a variable in preparation for storage
	 *
	 * @param mixed $input Variable to be prepared
	 * @return string String ready for storage
	 */

	private function obfuscate( $input ) {
		$input = serialize( $input );
		$input = base64_encode( $input );
		return $input;
	}

	/**
	 * deobfuscate
	 *
	 * Deobfuscates a variable retrieved from storage
	 *
	 * @param mixed $input Variable to be prepared
	 * @return string String ready for storage
	 */

	private function deobfuscate( $input ) {
		$input = base64_decode( $input );
		$input = unserialize( $input );
		return $input;
	}

}

new OptionsPage;