<?php

/**
 * OptionsPage
 *
 * Creates page for options, cURL/API test & help
 */

namespace RichJenks\MarketoLeads;

class OptionsPage {

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
			add_submenu_page( 'edit.php?post_type=marketoleads_fields', 'Marketo Leads Options', 'Options', 'manage_options', 'marketo-leads-options', array( $this, 'content' ) );
		} );

		// Check if option should be updated
		if ( isset( $_POST['marketo-leads-options'] ) ) {
			update_option( 'marketo-leads', serialize( array(
				'endpoint' => mysql_real_escape_string( $_POST['endpoint'] ),
				'token'    => mysql_real_escape_string( $_POST['token'] ),
				'url'    => mysql_real_escape_string( $_POST['url'] ),
			) ) );
		}

		// Get current values
		$this->options = unserialize( get_option( 'marketo-leads', serialize( array(
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
							<label for="token">Token</label>
						</th>
						<td>
							<input type="text" class="regular-text" name="token" placeholder="Token" value="<?=$this->options['token'];?>" required>
							<p class="description">Marketo > Admin > Integration > LaunchPoint > View Details</p>
						</td>
					</tr>
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
				</table>
				<input type="hidden" name="marketo-leads-options">
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
			</form>
			<h2>Getting Started</h2>
			<ol>
				<li>Follow Marketo's <a href="http://developers.marketo.com/blog/quick-start-guide-for-marketo-rest-api/" target="_blank">REST API Quick Start Guide</a> until you have determined the Endpoint URL</li>
				<li>Enter values for fields above and Save Changes</li>
				<li><a href="post-new.php?post_type=marketoleads_fields" target="_blank">Add a new field</a>, entering the Marketo field name and form field name(s)</li>
			</ol>
			<h3>Definitions</h3>
			<dl>
				<dt>Marketo Field</dt>
				<dd>The name of the field within Marketo&thinsp;&mdash;&thinsp;see <code>Marketo > Admin > Field Management > Export Field Names</code></dd>
				<dt>Form field name(s)</dt>
				<dd>The <code>name</code> or <code>id</code> attribute of the <code>input</code> element&thinsp;&mdash;&thinsp;found by inspecting the form's HTML</dd>
			</dl>
			<h2>Test Settings</h2>
			<p class="submit"><a class="button button-primary">Run Tests</a></p>
		</div>

	<?php }

}

new OptionsPage;