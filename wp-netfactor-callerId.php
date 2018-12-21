<?php
/*
Plugin Name: Netfactor CallerId for WordPress
Description: This is a custom plugin that utilizes the VisitorTrack IP-Based API to target all dimensions using a prefixed CSS selector, and then inserts the respective value into the targeted HTML element.
Author: Anthony Coffey
Version: 1.1
Author URI: https://coffeywebdev.com
*/

/**
*
* This function is triggered by the 'wp_footer' action hook
* more info: https://codex.wordpress.org/Function_Reference/wp_footer
*
* Below I've used the 'Dimension' with a prefix of 'nf_' to create a CSS selector that looks like this:
* .nf_companyId
* .nf_companyName
* .nf_websiteUrl
*  and so on...
*
*  Anywhere these classes are used, the netfactor_callerId() function defined below will target
*  all of the CSS selectors in the Dimension column listed on page 5 of the VisitorTrack IP-Based API Documentation
*  and using jQuery will insert the respective value into the targeted HTML element.
*
*  For example:
*
*  Anywhere on the site, if there was an HTML element that looked like this:
*  <span class="nf_companyId"></span>
*
*  On page load, this function will insert the respective value for companyId using Javascript
*  this will empty the node so be sure to use something like span above to avoid any content being erased accidentally.
*
*/

add_action('wp_footer', 'netfactor_callerId');
function netfactor_callerId(){
	// ENABLED DIMENSIONS
	// add more strings to the array to enable support for more values
	$ENABLED_DIMENSIONS = array('companyName');

	// get IP address of visitor
	if(function_exists('netfactor_get_user_ip')){
		$ip_address = "";
		$ip_address = netfactor_get_user_ip();
	}

	// define REST variables required for GET request
	$api_url = 'http://sleuth.visitor-track.com/Sleuth?ipAddress='.$ip_address;
//		$api_url = 'http://www.google.com:81/'; // TODO: LEAVE FOR TESTING TIMEOUT
	$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJjamVmZmVyc0BuZXRmYWN0b3IuY29tIiwianRpIjoiODZkYTE5ZDAtMTZiMi00MDQ5LWFiMTAtMjJiZjliYWQ4MThiIiwiZW1haWwiOiJjamVmZmVyc0BuZXRmYWN0b3IuY29tIiwiaHR0cDovL3NjaGVtYXMueG1sc29hcC5vcmcvd3MvMjAwNS8wNS9pZGVudGl0eS9jbGFpbXMvbmFtZSI6ImNqZWZmZXJzQG5ldGZhY3Rvci5jb20iLCJleHAiOjE4NjA4NzI3ODgsImlzcyI6Imh0dHA6Ly9zbGV1dGgudmlzaXRvci10cmFjay5jb20iLCJhdWQiOiJodHRwOi8vc2xldXRoLnZpc2l0b3ItdHJhY2suY29tIn0.H9CSoS2U75GV63aLPpgV0uNOLhQfdccXXfvs8PNPdXY';
	$bearer = 'Bearer';

	// check to ensure this version of WordPress has wp_remote_get() function
	if(function_exists('wp_remote_get')){
		$request = wp_remote_get($api_url,array(
			'headers' => array(
				'Authorization' => "{$bearer} {$token}"
			),
			'timeout' => 2
		));

		// check to ensure this version of WordPress has wp_remote_retrieve_body() function
		if(function_exists('wp_remote_retrieve_body')){

			ob_start();

			$response = json_decode(wp_remote_retrieve_body($request), true);

			// create javascript wrapper
			echo "<script>";

			echo "console.log('%c IP: {$ip_address}', 'color: red; font-size: 20px');";
			echo "console.log('%c IsIsp: {$response['isp']}', 'color: red; font-size: 20px');";

			if(isset($request->errors)){
				// print error to console.log
				echo "console.log('%c ERROR: {$request->errors['http_request_failed'][0]}', 'color: red; font-size: 20px');";
				// HARDCODE Your Company as companyName
				$response['companyName'] = "Your Company";
			} else {
				// IF isp TRUE, then display "Your Company" instead of the value returned by the API
				if($response['isp'] === true){
					$response['companyName'] = "Your Company";
				}
			}

			foreach ($response as $key => $value){
				/*
				* target each dimension, and autofill the value
				*/
				if(in_array($key, $ENABLED_DIMENSIONS)):
					echo "document.querySelectorAll('span.nf_{$key}').innerHTML = '{$value}';";
//					echo "document.querySelector('input.nf_{$key}').value = '{$value}';";   // input field support disabled for now
				endif;
			}

			echo "</script>";

			$autofill_html = ob_get_clean();
			echo $autofill_html;
		}
	}

}


/**
*  Get visitor's IP address
*/
function netfactor_get_user_ip() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return apply_filters( 'wpb_get_ip', $ip );
}

?>