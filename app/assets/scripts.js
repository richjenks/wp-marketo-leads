jQuery(document).on('submitResponse.example', function( e, response ){
	if ( response.errors == false ) {
		// Code to be fired upon a submission here
	}
	console.log(response);
	return true;
});