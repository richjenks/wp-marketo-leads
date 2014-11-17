<?php

/**
 * DebugView
 *
 * HTML for debug information
 */

?>

<h1>Plugin Options</h1>
<?php $this->options->client_secret = '[REDACTED]'; ?>
<?php var_dump( $this->options ); ?>

<h1>Post Data</h1>
<?php var_dump( $_POST ); ?>

<h1>Configured Fields</h1>
<?php var_dump( $this->fields ); ?>

<h1>Extra Fields</h1>
<?php var_dump( $this->get_extra_fields( $this->options->fields ) ); ?>

<h1>Global Fields</h1>
<?php var_dump( $this->get_global_fields( $this->options->global_fields ) ); ?>

<h1>Lead Data</h1>
<?php var_dump( $this->lead ); ?>