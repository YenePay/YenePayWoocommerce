<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings for YenePay Gateway.
 */
return array(
	'enabled' => array(
		'title'   => __( 'Enable/Disable', 'woo_yenepay' ),
		'type'    => 'checkbox',
		'label'   => __( 'Enable YenePay Payment', 'woo_yenepay' ),
		'default' => 'yes',
	),
	'title' => array(
		'title'       => __( 'Title', 'woo_yenepay' ),
		'type'        => 'text',
		'description' => __( 'This controls the title which the user sees during checkout.', 'woo_yenepay' ),
		'default'     => __( 'YenePay Payment', 'woo_yenepay' ),
		'desc_tip'    => true,
	),
	'description' => array(
		'title'       => __( 'Description', 'woo_yenepay' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'This controls the description which the user sees during checkout.', 'woo_yenepay' ),
		'default'     => __( "Pay with YenePay; you can pay directly from your bank account during checkout.", 'woo_yenepay' ),
	),
	'merchantcode' => array(
		'title'       => __( 'YenePay Merchant Code', 'woo_yenepay' ),
		'type'        => 'text',
		'description' => sprintf(__( 'Please enter your YenePay Merchant Code; this is required and can be found by logging into your <a href="%s">YenePay Account Manager</a>.', 'woo_yenepay' ),'https://account.yenepay.com/'),
		'desc_tip'    => true,
		'placeholder' => 'Your YenePay Merchant Code',
	),
	'testmode' => array(
		'title'       => __( 'Sandbox mode', 'woo_yenepay' ),
		'type'        => 'checkbox',
		'label'       => __( 'Use YenePay Sandbox', 'woo_yenepay' ),
		'default'     => 'yes',
		'description' => sprintf( __( 'YenePay sandbox can be used to test payments before going live. Start using our <a href="%s">sandbox application</a>.', 'woo_yenepay' ), 'https://sandbox.yenepay.com/' ),
	),
	'vat_registered' => array(
		'title'       => __( 'VAT registered?', 'woo_yenepay' ),
		'type'        => 'checkbox',
		'label'       => __( 'Indicate if you are VAT registered', 'woo_yenepay' ),
		'default'     => 'no',
		'description' => __( 'Indicate if you are VAT registered', 'woo_yenepay' ),
	),
	'debug' => array(
		'title'       => __( 'Debug log', 'woo_yenepay' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable logging', 'woo_yenepay' ),
		'default'     => 'no',
		'description' => sprintf( __( 'Log YenePay events, such as IPN requests, inside %s', 'woo_yenepay' ), '<code>' . WC_Log_Handler_File::get_log_file_path( 'paypal' ) . '</code>' ),
	),
	'advanced' => array(
		'title'       => __( 'Advanced options', 'woo_yenepay' ),
		'type'        => 'title',
		'description' => '',
	),
	'pdt_token' => array(
		'title'       => __( 'YenePay PDT token', 'woo_yenepay' ),
		'type'        => 'text',
		'description' => __( 'Get your "Payment Data Transfer" key (Settings > Notification Settings) on https://account.yenepay.com and then copy your pdt token here.', 'woo_yenepay' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => '',
	),
	// 'ipn_url' => array(
	// 	'title'       => __( 'IPN url', 'woo_yenepay' ),
	// 	'type'        => 'text',
	// 	'description' => __( 'Optionally enter the URL on your Notification Settings on YenePay Account Manager. ', 'woo_yenepay' ),
	// 	'default'     => '',
	// 	'desc_tip'    => true,
	// 	'placeholder' => __( 'Optional', 'woo_yenepay' ),
	// ),
	// 'success_url' => array(
		// 'title'       => __( 'Success url', 'woo_yenepay' ),
		// 'type'        => 'text',
		// 'description' => __( 'Enter a success url to redirect customer after successful payment on YenePay ', 'woo_yenepay' ),
		// 'default'     => '',
		// 'desc_tip'    => true,
		// 'placeholder' => __( 'Optional', 'woo_yenepay' ),
	// ),
	// 'cancel_url' => array(
		// 'title'       => __( 'Cancel url', 'woo_yenepay' ),
		// 'type'        => 'text',
		// 'description' => __( 'Enter a cancel url to redirect customer when payment is cancelled on YenePay. ', 'woo_yenepay' ),
		// 'default'     => '',
		// 'desc_tip'    => true,
		// 'placeholder' => __( 'Optional', 'woo_yenepay' ),
	// ),
	// 'failure_url' => array(
	// 	'title'       => __( 'Failure url', 'woo_yenepay' ),
	// 	'type'        => 'text',
	// 	'description' => __( 'Enter a failure url to redirect customer when payment fails on YenePay. ', 'woo_yenepay' ),
	// 	'default'     => '',
	// 	'desc_tip'    => true,
	// 	'placeholder' => __( 'Optional', 'woo_yenepay' ),
	// ),
);
