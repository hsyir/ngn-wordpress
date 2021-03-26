<?php
/**
 * Plugin Name: Elementor number element
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
include('ele-options.php');
function add_elementor_num_block() {
	include_once(ABSPATH.'wp-admin/includes/plugin.php');
	if (is_plugin_active( 'elementor/elementor.php' )) {
		include_once 'elementor-block.php';
		 \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new num_elementor() );
	}
}
add_action( 'init', 'add_elementor_num_block' );

add_action( 'wp_enqueue_scripts' , function() {
	wp_enqueue_script( 'jquery' );
	$params = array(
		'url'       => admin_url('admin-ajax.php'),
		'nonce'     => wp_create_nonce('ele-number'),
    );
    wp_localize_script( 'jquery', 'eleAjax', $params );
} );

add_action( 'wp_enqueue_scripts', function() {
	wp_register_style( 'bs4-isolated', plugin_dir_url( __FILE__ ) . 'assets/isolatedbs4.min.css' );
	wp_enqueue_script( 'num-elementor-script', plugin_dir_url( __FILE__ ) . 'assets/num-elementor.js', [ 'jquery' ], null, true );
	wp_register_script( 'bs4js', plugin_dir_url( __FILE__ ) . 'assets/bs4js.js', [ 'jquery' ], null, true );
	wp_register_script( 'bs4popper', plugin_dir_url( __FILE__ ) . 'assets/popper.js', [ 'jquery' ], null, true );
} );

add_action( 'wp_ajax_nopriv_check_ele_number_stat' , 'ele_number_check_status' );
add_action( 'wp_ajax_check_ele_number_stat' , 'ele_number_check_status' );
function ele_number_check_status() {
	//check_ajax_referer( 'ele-number', 'security' );
	$city = $_POST['city'];
	$mid  = $_POST['mid'];
	$last = $_POST['last'];
	$info = get_option( 'ele-number-server' );
	if ( ! is_array( $info ) ) {
		wp_send_json_error( 'Server details is empty' );
		exit();
	}
	$srv  = $info['ele-number-server-address'];
	$user = $info['ele-number-server-username'];
	$pass = $info['ele-number-server-password'];
	$final_url = $srv . '?pre_number=' . $city . '&mid_number=' . $mid . '&number=' . $last;
	$args = array(
		'headers' => array(
			'Authorization' => 'Basic ' . base64_encode( $user . ':' . $pass ),
		)
	);
	$response = wp_remote_get( $final_url, $args );
	$body = wp_remote_retrieve_body( $response );
	$result = json_decode( $body );
	wp_send_json( $result );
	exit();
}

add_action( 'wp_ajax_nopriv_save_ele_number_stat' , 'ele_number_save_status' );
add_action( 'wp_ajax_save_ele_number_stat' , 'ele_number_save_status' );
function ele_number_save_status() {
	$city = $_POST['city'];
	$mid  = $_POST['mid'];
	$last = $_POST['last'];
	$phone = $_POST['phone'];
	$details = $_POST['details'];
	$requests = get_option('ele-number-submitted-requests');
	if ( ! is_array( $requests ) ) {
		$requests = array();
	}
	$item = array(
		'pre-code'     => $city,
		'mid-code'     => $mid,
		'last-code'    => $last,
		'user-phone'   => $phone,
		'user-details' => $details,
	);
	$key = count( $requests );
	$requests[$key] = $item;
	update_option( 'ele-number-submitted-requests', $requests );

	// send email ato admin
	$to = get_option( 'admin_email' );
	$subject = 'درخواست شماره ی جدید ';
	$body  = '<!doctype html>';
	$body .= '<html>';
	$body .= '<body>';
	$body .='<p> یک در خواست در سایت شما مبنی بر خرید شماره ثبت شده است .</p>';
	$body .='<p> شماره ی ثبت شده : ' . $city . $mid . $last . '</p>';
	$body .='<p> توسط : ' . $details . ' </p>';
	$body .='<p> شماره همراه : ' . $phone . '</p>';
	$body .='</body>';
	$body .= '</html>';
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	wp_mail( $to, $subject, $body, $headers, );
	exit();
}