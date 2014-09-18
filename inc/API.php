<?php

/**
 * API
 *
 * Generic API class
 */

namespace RichJenks\MarketoLeads;

class API {

	/**
	 * call
	 *
	 * Calls an API
	 *
	 * @see http://stackoverflow.com/a/9802854/1562799
	 *
	 * @param string $method HTTP method
	 * @param string $url URL of API endpoint
	 * @param mixed $data Request data
	 * @param string $username Auth username
	 * @param string $password Auth password
	 *
	 * @return mixed Response data
	 *
	 * $data = json_encode( array(
	 * 	'lookupField' => 'email',
	 *  	'input' => array(
	 *  		'firstName' => 'aaa_firstName',
	 *  		'lastName' => 'aaa_lastName',
	 * 	),
	 * ) );
	 * API::call( 'POST', 'https://423-NMU-367.mktorest.com/rest/rest/v1/leads.json?access_token=a1c60140-8cc6-42e4-9b8e-47ab09b4790c:lon', $data )
	 */

	public static function call( $method, $url, $data = false, $username = false, $password = false ) {
echo $data;
		$curl = curl_init();

		switch ( $method ) {
			case "POST":
				curl_setopt( $curl, CURLOPT_POST, 1 );
				if ( $data ) curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
				break;
			case "PUT":
				curl_setopt( $curl, CURLOPT_PUT, 1 );
				break;
			default:
				if ( $data ) $url = sprintf( "%s?%s", $url, http_build_query( $data ) );
		}

		if ( $username && $password ) {
			curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
			curl_setopt( $curl, CURLOPT_USERPWD, "username:password" );
		}

		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );

		$result = curl_exec( $curl );

		curl_close( $curl );

		return $result;

	}

}