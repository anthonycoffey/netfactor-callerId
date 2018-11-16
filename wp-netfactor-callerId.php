<?php
/*
Plugin Name: Netfactor CallerId for WordPress
Description: This is a custom plugin that utilizes the VisitorTrack IP-Based API to target all dimensions using a prefixed CSS selector, and then inesrt the respective value into the targeted HTML element.
Author: Anthony Coffey
Version: 0.1
Author URI: https://coffeywebdev.com

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

	// get IP address of visitor
	if(function_exists('netfactor_get_user_ip')){
		$ip_address = (netfactor_get_user_ip() <> '127.0.0.1') ? netfactor_get_user_ip() : '192.28.2.52';
	} else {
		$ip_address = '192.28.2.52';
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
			// create javascript wrapper
			echo "<script>(function ($, window, document, undefined) {'use strict';$(function () {";
			$body = json_decode(wp_remote_retrieve_body($get));
			foreach ($body as $key => $value){
				//echo "console.log('.{$key} - {$value}');";  // for debug only
				/*
				 * target each dimension, and autofill the value using jQuery function .html()
				 * more info: http://api.jquery.com/html/
				 *
				 */
				echo "jQuery('input.nf_{$key}').val('{$value}');";
				echo "jQuery('.nf_{$key}:not(input)').html('{$value}');";
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
 */
add_shortcode('netfactor_debug','netfactor_make_spans');
function netfactor_make_spans(){
	// get IP address of visitor
	if(function_exists('netfactor_get_user_ip')){
		$ip_address = (netfactor_get_user_ip() <> '127.0.0.1') ? netfactor_get_user_ip() : '192.28.2.52';
	} else {
		$ip_address = '192.28.2.52';
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

			$body = json_decode(wp_remote_retrieve_body($get));
			foreach ($body as $key => $value){
				echo "<span class='nf_{$key}' style='display: block;'></span>";
				echo "<input class='nf_{$key}' value='' type='text'></input>";
			}

		}


	}
}


/**
* This function returns the visitor's IP address.
*/
function netfactor_get_user_ip() {
	if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
		if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
			$addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
			return trim($addr[0]);
		} else {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
	}
	else {
		return $_SERVER['REMOTE_ADDR'];
	}
}

?>