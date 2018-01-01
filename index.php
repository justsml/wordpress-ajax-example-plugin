<?php
/*
Plugin Name: Ajax Hooks
Plugin URI: http://www.danlevy.net
Description: AJAX magic
Version: 1.0.0
Author: Dan Levy
Author URI: https://github.com/justsml
License: MIT
*/

function registerAjax($funcName, $requireLogin = true) {
  if ($requireLogin) {
    add_action('wp_ajax_nopriv_' . $funcName, '' . $funcName);
  }
  add_action('wp_ajax_' . $funcName, '' . $funcName);
}

/**
 * Setup get_customer as an ajax-callable function
 */
registerAjax('get_customer', true);
function get_customer() {
	global $wpdb;
	$email = $_POST['email'];
	$results = search_customer($email);
	wp_send_json_success($results);
}

function search_customer($email) {
	global $wpdb;
	$query = 'SELECT wpcx_customers.salesrep,
		wpcx_customers.display_name as cust_display_name,
		wpcx_customers.email as cust_email,
		wpcx_customers.primaryphone as cust_primaryphone,
		wpcx_customers.address as cust_address,
		wpcx_customers.city as cust_city,
		wpcx_customers.state as cust_state,
		wpcx_customers.zip as cust_zip,
		wpcx_users.display_name as rep_display_name,
		wpcx_users.email as rep_email
	FROM wpcx_customers
		INNER JOIN wpcx_users
			ON wpcx_customers.location = wpcx_users.location
		INNER JOIN wpcx_customers
			ON wpcx_users.location = wpcx_customers.location
	WHERE wpcx_customers.email=%s';
  $results = $wpdb->get_results($wpdb->prepare($query, $email));
	return $results;
	// wp_send_json_success($results);
}

add_action('wp_enqueue_scripts', 'plugin_scripts');
add_action('admin_enqueue_scripts', 'plugin_scripts');


function plugin_scripts() {
	// if(is_single()) {
	// 	wp_enqueue_style('hooks', plugins_url('/index.css', __FILE__));
	// }

	wp_enqueue_script('hooks', plugins_url('/index.js', __FILE__));
	// wp_enqueue_script('hooks', plugins_url('/index.js', __FILE__), array('jquery'), '1.0', true);

	// will emit an object accessible via: ajaxHooks.url
	wp_localize_script('hooks', 'ajaxHooks', array(
		'url' => admin_url('admin-ajax.php'),
		'actions' => array('get_customer')
	));
}
