<?php
/*
Plugin Name: Netfactor CallerId for WordPress
Description: This is a custom plugin that utilizes the VisitorTrack IP-Based API to target all dimensions using a prefixed CSS selector, and then inserts the respective value into the targeted HTML element.
Author: Anthony Coffey
Version: 1.2
Author URI: https://coffeywebdev.com
*/


add_action( 'wp_ajax_my_action', 'my_action' );
add_action( 'wp_ajax_nopriv_my_action', 'my_action' );
function my_action() {
	$ip_address = $_REQUEST['ip'];

	// define REST variables required for GET request
	$api_url = 'http://sleuth.visitor-track.com/Sleuth?ipAddress=' . $ip_address;
	// $api_url = 'http://www.google.com:81/'; // TODO: LEAVE FOR TESTING TIMEOUT
	$token  = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJjamVmZmVyc0Bib21ib3JhLmNvbSIsImp0aSI6ImI0Mjc3M2I0LTE1ZTItNGUzYy05ZWVlLTA1ODkxNzZmNzVkZSIsImVtYWlsIjoiY2plZmZlcnNAYm9tYm9yYS5jb20iLCJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1lIjoiY2plZmZlcnNAYm9tYm9yYS5jb20iLCJleHAiOjE4NjQ0MTU5OTMsImlzcyI6Imh0dHA6Ly9zbGV1dGgudmlzaXRvci10cmFjay5jb20iLCJhdWQiOiJodHRwOi8vc2xldXRoLnZpc2l0b3ItdHJhY2suY29tIn0.HdKhvLXNXb6QQSUrS-ZuZcJknXWbBk9tBlyY5X-sUFc';
	$bearer = 'Bearer';


  $request = wp_remote_get( $api_url, array(
    'headers' => array(
      'Authorization' => "{$bearer} {$token}"
    ),
    'timeout' => 2
  ));

	$response = json_decode( wp_remote_retrieve_body( $request ), true);


  echo json_encode(array('data'=>$response));
  wp_die();
}

add_action('wp_footer', 'netfactor_callerId', 100);
function netfactor_callerId(){ ?>

		<script>
      (function ($, window, document, undefined) {

        'use strict';

          $.getJSON('https://api.ipify.org?format=jsonp&callback=?', function(data) {

            var ip = JSON.parse(JSON.stringify(data, null, 2)).ip;

            $.ajax({
              url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
              data : {
                action : 'my_action',
                ip : ip
              },
              method : 'POST',
              success : function( response ){
                var response = JSON.parse(response)
                var isp = response.data.isp;
                console.log(response.data)
                if(isp){
                  $('.nf_companyName').html('Your Company');
                } else {
                  $('.nf_companyName').html(response.data.companyName);
                }
              },
              error : function(error){
                console.log(error)
              }
            })


          });

      })(jQuery, window, document);
		</script>
		<?php

			$html = ob_get_clean();
			echo $html;
}

?>