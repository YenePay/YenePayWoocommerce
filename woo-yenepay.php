<?php
/**
 * Plugin Name: YenePay Checkout Payment Gateway
 * Plugin URI: https://github.com/YenePay/YenePayWoocommerce
 * Description: YenePay checkout payment gateway plugin for WooCommerce; start accepting Ethiopian mobile wallet payments on your store. 
 * Version: 1.2.0
 * Requires at least: 4.9
 * Requires PHP: 5.6.20
 * Author: YenePay Financial Technologies
 * Author URI: https://www.yenepay.com 
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
 
defined( 'ABSPATH' ) or die( 'No script allowed.' );
 
define( 'YENEPAY_CHECKOUT_PLUGIN_DIR', plugin_dir_url( __FILE__ )); 

require_once 'vendor/autoload.php';

use YenePay\Models\CheckoutItem;
use YenePay\Models\CheckoutOptions;
use YenePay\Models\IPN;
use YenePay\Models\CheckoutType;
use YenePay\Models\PDT;
use YenePay\CheckoutHelper;


/**
 * Add Gateway class to all payment gateway methods
 */
add_filter( 'woocommerce_payment_gateways', 'yenepay_add_payment_gateway_class' );
function yenepay_add_payment_gateway_class( $methods ) {
	
	$methods[] = 'Woo_YenePay_Gateway'; 
	return $methods;
}

add_action( 'plugins_loaded', 'yenepay_init_payment_gateway_class' );
function yenepay_init_payment_gateway_class() {
	class Woo_YenePay_Gateway extends WC_Payment_Gateway {
		
		/** @var bool Whether or not logging is enabled */
		public static $log_enabled = false;
		
		/** @var WC_Logger Logger instance */
		public static $log = false;
		
		/**
		 * Constructor for the gateway class
		 *
		 * @access public
		 * @return void
		 */
		public function __construct() {
			//$this -> plugin_url = WP_PLUGIN_URL . DIRECTORY_SEPARATOR . 'woo-yenepay';
			
			$this->id                 	= 'woo_yenepay'; 
			$this->order_button_text  = __( 'Pay with YenePay', 'woo_yenepay' );
			$this->icon 				= YENEPAY_CHECKOUT_PLUGIN_DIR.'images/yenepay_logo.png';
			$this->method_title       	= __( 'YenePay Checkout', 'woodev_payment' );  
			$this->method_description 	= __( 'YenePay checkout payment gateway plugin for WooCommerce; start accepting Ethiopian mobile wallet payments on your store. ', 'woo_yenepay' );
			$this->title              	= __( 'YenePay', 'woo_yenepay' );
			$this->has_fields = false;
			$this->supports = array( 
				'products'
			);
			//$this->get_yenepay_sdk();
			
		   	// Load the settings.
			$this->init_form_fields();
			$this->init_settings();
			
			// Define user set variables.			
			//$this->title          = $this->get_option( 'title' );
			$this->description    = $this->get_option( 'description' );
			$this->testmode       = $this->get_option( 'testmode' );
			$this->debug          = $this->get_option( 'debug' );
			$this->email          = $this->get_option( 'email' );
			//$this->receiver_email = $this->get_option( 'receiver_email', $this->email );
			//$this->identity_token = $this->get_option( 'identity_token' );

			self::$log_enabled    = $this->debug;
			
			$this->enabled = $this->get_option('enabled');
		
			//yenepay payment success return hook
			//add_action( 'start_accept_yenepay_success', array( $this, 'yenepay_success_callback') );
			add_action( 'woocommerce_api_yenepay-success', array( $this, 'success_webhook' ) );

			//yenepay payment IPN hook
			add_action( 'woocommerce_api_yenepay-ipn', array( $this, 'ipn_webhook' ) );
			
			// Save settings
  			if ( is_admin() ) {
  				// Versions over 2.0
  				// Save our administration options. Since we are not going to be doing anything special
  				// we have not defined 'process_admin_options' in this class so the method in the parent
  				// class will be used instead
  				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
				add_action( 'woocommerce_order_status_on-hold_to_processing', array( $this, 'capture_payment' ) );
				add_action( 'woocommerce_order_status_on-hold_to_completed', array( $this, 'capture_payment' ) );
  			}	
		}
		
		/**
		 * Logging method.
		 *
		 * @param string $message Log message.
		 * @param string $level   Optional. Default 'info'.
		 *     emergency|alert|critical|error|warning|notice|info|debug
		 */
		public static function log( $message, $level = 'info' ) {
			if ( self::$log_enabled ) {
				if ( empty( self::$log ) ) {
					self::$log = wc_get_logger();
				}
				self::$log->log( $level, $message, array( 'source' => 'yenepay' ) );
			}
		}
		
		public function init_form_fields() {
			$this->form_fields = include( 'includes/settings-yenepay.php' );
		}
		
		public function process_payment( $order_id ) {
			global $woocommerce;
			$order = wc_get_order( $order_id );
			
			$all_items = array();
			$subtotal = 0;
			
			// Products
			foreach ( $order->get_items( array( 'line_item', 'fee' ) ) as $item ) {
				$itemObject = new CheckoutItem();
				$product = $order->get_product_from_item( $item );
				$id = $product ? $product->get_sku() : '';
				$itemObject->setName( $item['name'] );
				$itemObject->setQuantity( $item['qty'] );
				$itemObject->setPrice( $order->get_item_subtotal( $item, false ) );
				$subtotal += $order->get_item_subtotal( $item, false ) * $item['qty'];
				if( $id ) {
					$itemObject->setId( $id );
				}  
			
				$all_items[] = $itemObject;
			}
			
			$sellerCode = $this->get_option('merchantcode');
			$useSandbox = $this -> testmode;
			$options = new CheckoutOptions($sellerCode, $useSandbox);
			
			if($this->get_option('vat_registered'))
				$options->setTotalItemsTax1($order->get_total_tax() );
			else
				$options->setTotalItemsTax2($order->get_total_tax() );
			
			$options->setTotalItemsDeliveryFee($order->get_total_shipping())
					->setMerchantOrderId($order_id)
					->setProcess(CheckoutType::Cart);
			
			//todo: set handling and discounts here (if available)			
			$baseUrl = $this->get_return_url( $order );
			$siteUrl = get_site_url();
			
			if( strpos( $baseUrl, '?') !== false ) {
				$baseUrl .= '&';
			} else {
				$baseUrl .= '?';
			}
			$options->setSuccessUrl( $siteUrl . '/wc-api/yenepay-success?wooyenepay=true&order_id=' . $order_id )
					->setCancelUrl( $siteUrl . '/wc-api/yenepay-success?wooyenepay=cancel&order_id=' . $order_id )
					->setIPNUrl($siteUrl . '/wc-api/yenepay-ipn');
					
			
			$checkoutHelper = new CheckoutHelper();
			try{
				$redirectUrl = $checkoutHelper->getCartCheckoutUrl($options, $all_items);						 
				return array(
					'result' => 'success',
					'redirect' => $redirectUrl
				);
			} catch (Exception $ex) {
				wc_add_notice(  $ex->getMessage(), 'error' );
			}
			return array(
					'result' => 'failure',
					'redirect' => ''
			);
		}
	
		public function success_webhook() {
			global $woocommerce;
			
			$wooyenepay = sanitize_text_field($_GET['wooyenepay']);

			if( isset($wooyenepay) ) {
				$order_id = sanitize_key($_GET['order_id']);
				
				if( $order_id == 0 || $order_id == '' ) {
					return;
				}
				$order = wc_get_order( $order_id );
				$returnUrl = $this->get_return_url( $order );
				
				if( $order->has_status('completed') || $order->has_status('processing') || $order->has_status('paid') || $order->has_status('delivered')) {
					wp_safe_redirect( $returnUrl.'order_id='.$order_id );
				}

				//get YenePay transaction Id from query string
				$transactionId = sanitize_key($_GET["TransactionId"]);

				//success return url hit
				if( $wooyenepay == 'true' ) {
					$result = null;

					try {
						if(isset($transactionId)){
							$merchantOrderId = sanitize_key($_GET["MerchantOrderId"]);
							
							// get pdt token from settings
							$pdtToken = $this->get_option('pdt_token');
							if(null != $pdtToken){
								$pdtModel = new PDT("PDT", $pdtToken, $transactionId, $merchantOrderId);
								$useSandbox = $this-> testmode;
								$pdtModel->setUseSandbox($useSandbox);
								$checkoutHelper = new CheckoutHelper();

								//call yenepay's payment gateway API and check the payment staus
								//cURL used here to call yenepay's payment gateway API
								$result = $checkoutHelper->RequestPDT($pdtModel);
								if($result['result'] == 'SUCCESS' && ($result['Status'] == 'Paid' || $result['Status'] == 'Delivered' || $result['Status'] == 'Completed')){
									// Payment complete
									$order->payment_complete();
									// Add order note
									$order->add_order_note( sprintf( __( 'YenePay payment approved! Transaction ID: %s ', 'woocommerce' ), $transactionId ) );

								}
								else{
									self::log("failed PDT request. PDT result is: ".var_dump($result));
								}
							}
						}
						
						//$result = $payment->execute($execution, $this->apiContext);
						  
					} catch (Exception $ex) { 
						  wc_add_notice(  $ex->getMessage(), 'error' );
					
						  //$order->update_status('failed', sprintf( __( '%s payment failed! Transaction ID: %d', 'woocommerce' ), $this->title, $paymentId ) . ' ' . $ex->getMessage() );
						  self::log("Exception on PDT request. Exception is: ".$ex->getMessage());
						  return;
					}
								  
					// Remove cart
					$woocommerce->cart->empty_cart();
					wp_safe_redirect( $returnUrl.'order_id='.$order_id );
		  
				}
				
				//cacel return url hit
				if( $wooyenepay == 'cancel' ) { 
					if(isset($transactionId)){
						$order->update_status('cancelled', sprintf( __( '%s payment cancelled! Transaction ID: %d', 'woocommerce' ), $this->title, $transactionId ) );
						wp_safe_redirect( $returnUrl.'order_id='.$order_id );
					}
				}
			}
			return;
		}
		
		public function ipn_webhook(){
			//self::log("ipn webhook called");	
			global $woocommerce;
					
			@ob_clean();
			
			$wc_order_id = sanitize_key($_POST['MerchantOrderId']);
			$wc_order = wc_get_order( absint( $wc_order_id ) );
			
			
			if( $wc_order->has_status('completed') || $wc_order->has_status('processing') || $wc_order->has_status('paid') || $wc_order->has_status('delivered')) {
				wp_safe_redirect( $returnUrl.'order_id='.$wc_order_id );
			}
			
			$total_amount = number_format(floatval($_POST['TotalAmount']), 2, ".", "");
			$ipn_signature = wp_strip_all_tags($_POST['Signature']);
			$yenepay_order_id = sanitize_key($_POST['TransactionId']);
			$merchant_yenepay_id = sanitize_key($_POST['MerchantId']);
			$customer_yenepay_id = sanitize_key($_POST['BuyerId']);
			$merchant_yenepay_code = sanitize_key($_POST['MerchantCode']);
			$yenepay_order_status = sanitize_text_field($_POST['Status']);
			$yenepay_order_code = sanitize_text_field($_POST['TransactionCode']);
			$transaction_currency = sanitize_text_field($_POST['Currency']);
			$useSandbox = $this->get_option('testmode');
			
			$ipnModel = new IPN();
			$ipnModel -> setTotalAmount($total_amount);
			$ipnModel -> setBuyerId($customer_yenepay_id);
			$ipnModel -> setMerchantOrderId($wc_order_id);
			$ipnModel -> setMerchantId($merchant_yenepay_id);
			$ipnModel -> setMerchantCode($merchant_yenepay_code);
			$ipnModel -> setTransactionId($yenepay_order_id);
			$ipnModel -> setStatus($yenepay_order_status);
			$ipnModel -> setCurrency($transaction_currency);
			$ipnModel -> setTransactionCode($yenepay_order_code);
			$ipnModel -> setUseSandbox($useSandbox);
			$ipnModel -> setSignature($ipn_signature);
			
			$helper = new CheckoutHelper();

			//check if the order total here and the total amount sent via ipn match
			if(floatval($_POST['TotalAmount']) == $wc_order->get_order_total){
				//Call yenepay's payment gateway API and check the IPN validity
				//cURL used here to call yenepay's payment gateway API
				if($helper->isIPNAuthentic($ipnModel))
				{	
					//This means the payment is completed
					//You can now mark the order as "Paid" or "Completed" here and start the delivery process
					
					// Mark order complete
					$wc_order->add_order_note( __( 'IPN completed by YenePay', 'woocommerce' ) );
					$wc_order->payment_complete();
					
					// Empty cart and clear session
					$woocommerce->cart->empty_cart();
					//wp_redirect( $this->get_return_url( $wc_order ) );
					//exit;
				}
			}
			else{
				self::log("ipn verification for order id".$wc_order_id."failed with total amount mismatch.");
			}
		}
		
		/**
         * Check if this gateway is enabled and available in the user's country
         *
         * @access public
         * @return bool
         */
        function is_valid_for_use() {
          $supported_currencies = array('ETB');

            if ( ! in_array( get_woocommerce_currency(), apply_filters( 'woocommerce_yenepay_supported_currencies', $supported_currencies ) ) ) return false;

            return true;
        }
	}
}

?>