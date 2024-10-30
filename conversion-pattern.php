<?php
/**
 * Plugin Name: Conversion Pattern
 * Plugin URI: https://www.conversionpattern.com/
 * Description: This plugin allows you to natively integrate your website with conversion pattern
 * Version: 1.0.0
 * Author: conversionpattern
 * Author URI: https://www.linkedin.com/in/sandeep-pani-3a87117b/
 * Text Domain: conversion-pattern
 * Domain Path: /languages
*/


add_action( 'init', 'conversion_pattern_script_enqueuer' );

add_action( 'woocommerce_payment_complete', 'conversion_pattern_payment_complete_woocommerce' );

function conversion_pattern_payment_complete_woocommerce($order_id) {
	// For now we will just ping the order id back to conversion-pattern to see
	// if this function does what its supposed to
	$order = null;
	if (function_exists('wc_get_order')) {
		$order = wc_get_order($order_id);
	} else {
		$order = new WC_Order($order_id);
	}
	// We are getting these from the $_SERVER, no need to sanitize this right now
	// as they can be in any form and shape and we are okay with that
	$http_host = null;
	if (isset($_SERVER['HTTP_HOST'])) {
		$http_host = sanitize_text_field($_SERVER['HTTP_HOST']);
	}
	$cookie_request_data = '';
	// We need to get all the cookies present on the user session, so we just
	// append all of them with the separator ; which is how browsers send it
	// on request headers too
	foreach($_COOKIE as $key => $value) {
		$cookie_request_data = $cookie_request_data.$key.'='.$value.';';
	}
	$order_total = null;
	// Always check if the function exists to make sure we dont do anything
	// worth crashing
	if (!is_null($order) && method_exists($order, 'get_total')) {
		$order_total = $order->get_total();
	}
	$order_currency = null;
	if (!is_null($order) && method_exists($order, 'get_currency')) {
		$order_currency = $order->get_currency();
	}
	$billing_city = null;
	if (!is_null($order) && method_exists($order, 'get_billing_city')) {
		$billing_city = $order->get_billing_city();
	}
	$billing_postcode = null;
	if (!is_null($order) && method_exists($order, 'get_billing_postcode')) {
		$billing_postcode = $order->get_billing_postcode();
	}
	$billing_country = null;
	if (!is_null($order) && method_exists($order, 'get_billing_country')) {
		$billing_country = $order->get_billing_country();
	}
	$billing_phone = null;
	if (!is_null($order) && method_exists($oder, 'get_billing_phone')) {
		$billing_phone = $order->get_billing_phone();
	}
	$billing_state = null;
	if (!is_null($order) && method_exists($order, 'get_billing_state')) {
		$billing_state = $order->get_billing_state();
	}
	$billing_mobile = null;
	if (!is_null($order) && method_exists($order, 'get_billing_phone')) {
		$billing_mobile = $order->get_billing_phone();
	}
	$billing_email = null;
	if (!is_null($order) && method_exists($order, 'get_billing_email')) {
		$billing_email = $order->get_billing_email();
	}
	$customer_ip = null;
	if (!is_null($order) && method_exists($order, 'get_customer_ip_address')) {
		$customer_ip = $order->get_customer_ip_address();
	}
	$customer_id = null;
	if (!is_null($order) && method_exists($order, 'get_customer_id')) {
		$customer_id = $order->get_customer_id();
	}
	$customer_first_name = null;
	if (!is_null($order) && method_exists($order, 'get_billing_first_name')) {
		$customer_first_name = $order->get_billing_first_name();
	}
	$customer_last_name = null;
	if (!is_null($order) && method_exists($order, 'get_billing_last_name')) {
		$customer_last_name = $order->get_billing_last_name();
	}
	$cart_hash = null;
	if (!is_null($order) && method_exists($order, 'get_cart_hash')) {
		$cart_hash = $order->get_cart_hash();
	}
	$currency = null;
	if (!is_null($order) && method_exists($order, 'get_currency')) {
		$currency = $order->get_currency();
	}
	$apiUrl = 'https://lilo-dot-conversion-pattern.ue.r.appspot.com/ll/woocommerce_plugin_order';
	$apiResponse = wp_remote_post( $apiUrl,
		array(
			'method'    => 'POST',
			'sslverify' => false,
			'body' => array(
				'order_id' => $order_id,
				'order_total' => $order_total,
				'order_currency' => $order_currency,
				'billing_city' => $billing_city,
				'billing_postcode' => $billing_postcode,
				'billing_state' => $billing_state,
				'billing_mobile' => $billing_mobile,
				'billing_email' => $billing_email,
				'billing_country' => $billing_country,
				'billing_phone' => $billing_phone,
				'customer_ip' => $customer_ip,
				'customer_id' => $customer_id,
				'customer_first_name' => $customer_first_name,
				'customer_last_name' => $customer_last_name,
				'cart_hash' => $cart_hash,
				'http_host' => $http_host,
				'cookie_request_data' => $cookie_request_data,
				'currency' => $currency,
			),
			'headers'   => array (
				'Content-type: application/json',
				'Accept: */*',
			),
		)
	);
}

function conversion_pattern_script_enqueuer() {
   
   // Register the JS file with a unique handle, file location, and an array of dependencies
   wp_register_script('conversion_pattern_callback_script_identify', plugin_dir_url(__FILE__).'js/conversion_pattern_callback_script_identify.js', array('jquery'));
   wp_register_script('conversion_pattern_callback_script_synchronize', plugin_dir_url(__FILE__).'js/conversion_pattern_callback_script_synchronize.js', array('jquery'));
   
   // localize the script to your domain name, so that you can reference the url to admin-ajax.php file easily
   wp_localize_script( 'conversion_pattern_callback_script_identify', 'conversionPatternAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
   wp_localize_script( 'conversion_pattern_callback_script_synchronize', 'conversionPatternAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
   
   // enqueue jQuery library and the script you registered above
   wp_enqueue_script('jquery');
   wp_enqueue_script('conversion_pattern_callback_script_identify');
   wp_enqueue_script('conversion_pattern_callback_script_synchronize');
}

// this will be used to check ajax callback
function conversion_pattern_lilo_call() {
	// If the user is logged in, then we dont want to do anything, we only want
	// this to work for non-logged in users      
	if (is_user_logged_in()) {
		return;
	}
	// set the content type and return json encode response, then exit
	header('Content-type: application/json');
	$cookie_request_data = '';
	// We need to get all the cookies present on the user session, so we just
	// append all of them with the separator ; which is how browsers send it
	// on request headers too
	foreach($_COOKIE as $key => $value) {
		$cookie_request_data = $cookie_request_data.$key.'='.$value.';';
	}
	$mode = null;
	if (isset($_POST['value'])) {
		$mode = sanitize_text_field($_POST['value']);
	}
	$screen_data_width = null;
	$screen_data_height = null;
	$screen_data_color_depth = null;
	$screen_data_pixel_depth = null;
	if (isset($_POST['screen_height'])) {
		$screen_data_height = intval(sanitize_text_field($_POST['screen_height']));
	}
	if (isset($_POST['screen_width'])) {
		$screen_data_width = intval(sanitize_text_field($_POST['screen_width']));
	}
	if (isset($_POST['screen_pixel_depth'])) {
		$screen_data_pixel_depth = intval(sanitize_text_field($_POST['screen_pixel_depth']));
	}
	if (isset($_POST['screen_color_depth'])) {
		$screen_data_color_depth = intval(sanitize_text_field($_POST['screen_color_depth']));
	}
	$href = null;
	if (isset($_POST['href'])) {
		$href = sanitize_text_field($_POST['href']);
	}
	$user_agent = null;
	if (isset($_SERVER['HTTP_USER_AGENT'])) {
		$user_agent = sanitize_text_field($_SERVER['HTTP_USER_AGENT']);
	}
	// We are getting these from the $_SERVER, no need to sanitize this right now
	// as they can be in any form and shape and we are okay with that
	$http_host = null;
	if (isset($_SERVER['HTTP_HOST'])) {
		$http_host = sanitize_text_field($_SERVER['HTTP_HOST']);
	}
	$origin = null;
	if (isset($_SERVER['HTTP_ORIGIN'])) {
		$origin = sanitize_text_field($_SERVER['HTTP_ORIGIN']);
	}
	$referer = null;
	if (isset($_SERVER['HTTP_REFERER'])) {
		$referer = sanitize_text_field($_SERVER['HTTP_REFERER']);
	}
	$ip = null;
	if (isset($_SERVER['REMOTE_ADDR'])) {
		$ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
	}
	// Send the data over to our private URL where we only allow allowlisted traffic,
	// reach out on https://www.conversionpattern.com/request-an-invite to configure
	// your domain
	$apiUrl = 'https://lilo-dot-conversion-pattern.ue.r.appspot.com/ll/woocommerce_plugin';
	$apiResponse = wp_remote_post( $apiUrl,
		array(
			'method'    => 'POST',
			'sslverify' => false,
			'body' => array(
				'mode' => $mode,
				'cookie_request_data' => $cookie_request_data,
				'user_agent_data' => $user_agent,
				'http_host' => $http_host,
				'origin' => $origin,
				'referer' => $referer,
				'ip' => $ip,
				'href' => $href,
				'screen_data_width' => $screen_data_width,
				'screen_data_height' => $screen_data_height,
				'screen_data_color_depth' => $screen_data_color_depth,
				'screen_data_pixel_depth' => $screen_data_pixel_depth,
			),
			'headers'   => array (
				'Content-type: application/json',
				'Accept: */*',
			),	
		)
	);
	$response = wp_remote_retrieve_body($apiResponse);
	$apiBody = json_decode($response, true);
	// We are interested in the cookie response, which we use to stitch users
	// together for the website which deploys us.
	$cookies = $apiBody['cookieResponse'];
	foreach($cookies as $cookie) {
		$http_only = false;
		if ($cookie['httpOnly'] !== null) {
			$http_only = true;
		}
		$arr_cookie_options = array(
			'expires' => (int)$cookie['expiresAt'],
			'path' => $cookie['pathAttr'],
			'domain' => $cookie['domain'], // leading dot for compatibility or use subdomain
			'httponly' => $http_only,    // or false
			'samesite' => $cookie['sameSite'],
		);
		setcookie(
			$cookie['name'],
			$cookie['value'],
			$arr_cookie_options,
		);
	}
	echo json_encode($apiBody);
	wp_die();
}
add_action("wp_ajax_my_user_identify", "conversion_pattern_lilo_call");
add_action("wp_ajax_nopriv_my_user_identify", "conversion_pattern_lilo_call");