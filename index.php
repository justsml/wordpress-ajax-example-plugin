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
$actions = array();

/**
 * Example get_customer is an ajax-callable action/function
 *
 * Copy/Paste to add more actions (update_customer):
 */
registerAjax('get_customer', true);
function get_customer($email) {
  global $wpdb;
  $email = $_POST['email'];
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
  wp_send_json_success($results);
}

/* Begin: WP Helpers */
add_action('wp_enqueue_scripts', 'plugin_scripts');
add_action('admin_enqueue_scripts', 'plugin_scripts');
// include plugin script .css/.js
function plugin_scripts() {
  wp_enqueue_style('hooks', plugins_url('/index.css', __FILE__));
  wp_enqueue_script('hooks', plugins_url('/index.js', __FILE__));
  // Emit an object accessible like so: ajaxHooks.url, ajaxHooks.actions
  wp_localize_script('hooks', 'ajaxHooks', array(
    'url' => admin_url('admin-ajax.php'),
    'actions' => array($actions)
  ));
}
function registerAjax($funcName, $requireLogin = true) {
  $actions[] = $funcName;
  if ($requireLogin) {
    add_action('wp_ajax_nopriv_' . $funcName, '' . $funcName);
  }
  add_action('wp_ajax_' . $funcName, '' . $funcName);
}
