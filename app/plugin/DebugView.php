<?php

/**
 * DebugView
 *
 * HTML for debug information
 */

// Sanitize options for viewing
unset( $this->options['submit'] );
unset( $this->options['client_secret'] );
unset( $this->options['rj_ml_options_submitted'] );
?>

<style>
* { font-family: sans-serif; }
table { border-collapse: collapse; }
td, th { vertical-align: top; text-align: left; padding: .5em; }
tr:nth-child(odd) { background: #d6eef6; }
</style>

<h1>Marketo Leads Debug Info</h1>

<p><em>You are seeing this because you are logged in and Debug Mode is enabled.</em></p>
<p><em>Don't worry, visitors who aren't logged in won't see this!</em></p>

<h2>Plugin Options</h2>
<table>
	<?php foreach ( $this->options as $option => $value ): ?>
		<tr>
			<th scope="row"><?= $option; ?></th>
			<td><pre><?php
				if ( !is_string( $value ) ) {
					echo json_encode( (array) $value, JSON_PRETTY_PRINT );
				} elseif ( strpos( $value , '|' ) !== false ) {
					echo '<table>';
					$rows = explode( "\n", $value );
					foreach ( $rows as $row ) {
						echo '<tr>';
						$cells = explode( '|', $row );
						foreach ( $cells as $cell ) {
							echo '<td>' . trim( $cell ) . '</td>';
						}
						echo '</tr>';
					}
					echo '</table>';
				} else {
					echo $value;
				}
			?></pre></td>
		</tr>
	<?php endforeach; ?>
</table>

<h2>Post Data</h2>
<table>
	<?php foreach ( $this->post as $key => $value ): ?>
		<tr>
			<th scope="row"><?= $key; ?></th>
			<td><pre><?= $value; ?></pre></td>
		</tr>
	<?php endforeach; ?>
</table>

<h2>Configured Fields</h2>
<table>
	<?php foreach ( $this->fields as $key => $values ): ?>
		<tr>
			<th scope="row"><?= $key; ?></th>
			<td><?php foreach ( $values as $value ) {
				echo $value . '<br>';
			} ?></td>
		</tr>
	<?php endforeach; ?>
</table>

<h2>Default Fields</h2>
<table>
	<?php $fields = $this->get_default_fields( $this->options['default_fields'] ); ?>
	<?php foreach ( $fields as $key => $value ): ?>
		<tr>
			<th scope="row"><?= $key; ?></th>
			<td><pre><?= $value; ?></pre></td>
		</tr>
	<?php endforeach; ?>
</table>

<h2>Lead Data</h2>
<table>
	<?php $lead = $this->lead; ?>
	<?php foreach ( $lead as $key => $value ): ?>
		<tr>
			<th scope="row"><?= $key; ?></th>
			<td><pre><?= $value; ?></pre></td>
		</tr>
	<?php endforeach; ?>
</table>