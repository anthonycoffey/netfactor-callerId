<?php
/*
Plugin Name: Netfactor CallerId for WordPress
Description: This is a custom plugin that utilizes the VisitorTrack IP-Based API to target all dimensions using a prefixed CSS selector, and then inesrt the respective value into the targeted HTML element.
Author: Anthony Coffey
Version: 0.2
Author URI: https://coffeywebdev.com
*/

/**
*
* This function is triggered by the 'wp_head' action hook
* more info: https://codex.wordpress.org/Function_Reference/wp_head
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
*  On page load, this function will insert the respective value for companyId using jQuery .html() function,
*  this will empty the node so be sure to use something like span above to avoid any content being erased accidentally.
*
*/

add_action('wp_head', 'netfactor_callerId');
function netfactor_callerId(){
	// ENABLED DIMENSIONS
	// add more strings to the array to enable support for more values
	$ENABLED_DIMENSIONS = array('companyName');


	// get IP address of visitor
	if(function_exists('netfactor_get_user_ip')){
		$ip_address = netfactor_get_user_ip();
	}

	// define REST variables required for GET request
	$api_url = 'http://sleuth.visitor-track.com/Sleuth?ipAddress='.$ip_address;
	$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJjamVmZmVyc0BuZXRmYWN0b3IuY29tIiwianRpIjoiYmRlN2M2NWYtM2U1ZS00N2MzLWJjYzktYzljM2IyNjU5YTk4IiwiZW1haWwiOiJjamVmZmVyc0BuZXRmYWN0b3IuY29tIiwiaHR0cDovL3NjaGVtYXMueG1sc29hcC5vcmcvd3MvMjAwNS8wNS9pZGVudGl0eS9jbGFpbXMvbmFtZSI6ImNqZWZmZXJzQG5ldGZhY3Rvci5jb20iLCJleHAiOjE4NTc5Njk4NTksImlzcyI6Imh0dHA6Ly9zbGV1dGgudmlzaXRvci10cmFjay5jb20iLCJhdWQiOiJodHRwOi8vc2xldXRoLnZpc2l0b3ItdHJhY2suY29tIn0.GhmHjeAHrmctO-dDjRj3AK-eEmw2haiwukfW8SRBBUQ';
	$bearer = 'Bearer';

	// check to ensure this version of WordPress has wp_remote_get() function
	if(function_exists('wp_remote_get')){
		$get = wp_remote_get($api_url,array(
			'headers' => array(
				'Authorization' => "{$bearer} {$token}"
			),
		));

		// check to ensure this version of WordPress has wp_remote_retrieve_body() function
		if(function_exists('wp_remote_retrieve_body')){
			ob_start();
			$body = json_decode(wp_remote_retrieve_body($get));

			// create javascript wrapper
			echo "<script>(function ($, window, document, undefined) {'use strict';$(function () {";

			if($body->isp == false){
				// do nothing special
			} else {
				// If IsIsp TRUE, then display "Your Company" instead of the value returned by the API
				$body->companyName = "Your Company";
			}

			foreach ($body as $key => $value){
				/*
				 * target each dimension, and autofill the value using jQuery function .html()
				 * more info: http://api.jquery.com/html/
				 *
				 */
					if(in_array($key, $ENABLED_DIMENSIONS)):
						echo "jQuery('input.nf_{$key}').val('{$value}');";
						echo "jQuery('.nf_{$key}:not(input)').html('{$value}');";
					endif;
			}

			echo "});})(jQuery, window, document);</script>";

			$autofill_html = ob_get_clean();
			echo $autofill_html;
		}
	}

}


/**
* This function does the same thing as netfactor_callerId(), except it just prints out <span class="nf_DIMENSIONHERE">
* for every Dimension listed on page 5 of the VisitorTrack IP-Based API Documentation
*
* This function is triggered by using the following shortcode: [netfactor_debug]
*
* This function is used to test the javascript that "auto-fills" all matched values
*
*/
add_shortcode('netfactor_debug','netfactor_make_spans');
function netfactor_make_spans(){
	// ENABLED DIMENSIONS
	// add more strings to the array to enable support for more values
	$ENABLED_DIMENSIONS = array('companyName');

	// get IP address of visitor
	if(function_exists('netfactor_get_user_ip')){
		$ip_address = netfactor_get_user_ip();
	}

	// define variables
	$api_url = 'http://sleuth.visitor-track.com/Sleuth?ipAddress='.$ip_address;
	$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJjamVmZmVyc0BuZXRmYWN0b3IuY29tIiwianRpIjoiYmRlN2M2NWYtM2U1ZS00N2MzLWJjYzktYzljM2IyNjU5YTk4IiwiZW1haWwiOiJjamVmZmVyc0BuZXRmYWN0b3IuY29tIiwiaHR0cDovL3NjaGVtYXMueG1sc29hcC5vcmcvd3MvMjAwNS8wNS9pZGVudGl0eS9jbGFpbXMvbmFtZSI6ImNqZWZmZXJzQG5ldGZhY3Rvci5jb20iLCJleHAiOjE4NTc5Njk4NTksImlzcyI6Imh0dHA6Ly9zbGV1dGgudmlzaXRvci10cmFjay5jb20iLCJhdWQiOiJodHRwOi8vc2xldXRoLnZpc2l0b3ItdHJhY2suY29tIn0.GhmHjeAHrmctO-dDjRj3AK-eEmw2haiwukfW8SRBBUQ';
	$bearer = 'Bearer';

	// check to ensure this version of WordPress has wp_remote_get() function
	if(function_exists('wp_remote_get')){
		$get = wp_remote_get($api_url,array(
			'headers' => array(
				'Authorization' => "{$bearer} {$token}"
			),
		));

		// check to ensure this version of WordPress has wp_remote_retrieve_body() function
		if(function_exists('wp_remote_retrieve_body')){
			// get body of reponse, convert to PHP
			$body = json_decode(wp_remote_retrieve_body($get));

			if($body->isp == false){
				var_dump($body);
			} else {
				// If IsIsp TRUE, then display "Your Company" instead of the value returned by the API
				$body->companyName = "Your Company";
			}

			foreach ($body as $key => $value){
				if(in_array($key, $ENABLED_DIMENSIONS)) {
					echo "<span class='nf_{$key}' style='display: block;'></span>";
					echo "<input class='nf_{$key}' value='' type='text'>";
				}
			}
		}
	}

}

/**
*  Get visitor's IP address using ipify.org API
*  read more: https://www.ipify.org/
*/
function netfactor_get_user_ip() {
	$ip = file_get_contents('https://api.ipify.org');
	return $ip;
}

?>