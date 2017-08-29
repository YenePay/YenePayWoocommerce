<?php
/**
 * Plugin Name: WooCommerce YenePay Payment Gateway
 */
 
define( 'WOO_PAYMENT_DIR', plugin_dir_path( __FILE__ )); 
add_action( 'plugins_loaded', 'woo_payment_gateway' );

use YenePay\Models\CheckoutItem;
use YenePay\Models\CheckoutOptions;
use YenePay\Models\IPN;
use YenePay\Models\CheckoutType;
use YenePay\Models\PDT;
use YenePay\CheckoutHelper;

require_once('includes/lib/sdk/Models/CheckoutOptions.php');
require_once('includes/lib/sdk/Models/CheckoutItem.php');
require_once('includes/lib/sdk/Models/IPN.php');
require_once('includes/lib/sdk/Models/Enums.php');
require_once('includes/lib/sdk/Models/PDT.php');
require_once('includes/lib/sdk/CheckoutHelper.php');

function woo_payment_gateway() {
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
			$this->id                 	= 'woo_yenepay'; 
			$this->order_button_text  = __( 'Pay with YenePay', 'woo_yenepay' );
			$this->method_title       	= __( 'YenePay', 'woodev_payment' );  
			$this->method_description 	= __( 'WooCommerce Payment Gateway for YenePay', 'woo_yenepay' );
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
			$this->testmode       = 'yes' === $this->get_option( 'testmode', 'no' );
			$this->debug          = 'yes' === $this->get_option( 'debug', 'no' );
			$this->email          = $this->get_option( 'email' );
			//$this->receiver_email = $this->get_option( 'receiver_email', $this->email );
			//$this->identity_token = $this->get_option( 'identity_token' );

			self::$log_enabled    = $this->debug;
			
			$this->enabled = $this->get_option('enabled');
		
			add_action( 'check_wooyenepay', array( $this, 'check_response') );
			
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
		
		// private function get_yenepay_sdk() {
			// require_once WOO_PAYMENT_DIR . 'includes/lib/vendor/autoload.php';
			
		// }
		
		public function init_form_fields() {
			$this->form_fields = include( 'includes/settings-yenepay.php' );
		}
		
		public function process_payment( $order_id ) {
			echo "<script> alert('Starting payment processing!'); </script>";
			
			global $woocommerce;
			$order = new WC_Order( $order_id );
			
			$all_items = array();
			$subtotal = 0;
			
			// Products
			foreach ( $order->get_items( array( 'line_item', 'fee' ) ) as $item ) {
				echo "<script> alert('start creating checkout item from the user order...'); </script>";
				$itemObject = new CheckoutItem();
				echo "<script> alert('created checkoutitem...'); </script>";
				//$itemObject->setCurrency( get_woocommerce_currency() );
				// if ( 'fee' === $item['type'] ) {
					// $itemObject->setName( __( 'Fee', 'woo_yenepay' ) );
					// $itemObject->setQuantity(1);
					// $itemObject->setPrice( $item['line_total'] ); 
					// $subtotal += $item['line_total'];
				// } else {
					$product          = $order->get_product_from_item( $item );
					$id              = $product ? $product->get_sku() : '';
					$itemObject->setName( $item['name'] );
					$itemObject->setQuantity( $item['qty'] );
					$itemObject->setPrice( $order->get_item_subtotal( $item, false ) );
					$subtotal += $order->get_item_subtotal( $item, false ) * $item['qty'];
					if( $id ) {
						$itemObject->setId( $id );
					}  
				//}
				$all_items[] = $itemObject;
			}
			echo "<script> alert('finished creating order items...'); </script>";
			$sellerCode = $this->get_option('merchantcode');
			$useSandbox = $this->get_option('testmode');
			echo "creating checkoutoptions...";
			$options = new CheckoutOptions($sellerCode, $useSandbox);
			echo "created checkoutoptions.";
			$options->setIPNUrl($this->get_option('ipn_url'))
					->setFailureUrl($this->get_option('failure_url'));
			if($this->get_option('vat_registered'))
				$options->setTotalItemsTax1($order->get_total_tax() );
			else
				$options->setTotalItemsTax2($order->get_total_tax() );
			
			$options->setTotalItemsDeliveryFee($order->get_total_shipping())
					->setMerchantOrderId(order_id)
					->setProcess(CheckoutType::Cart);
			
			echo "<script> alert('finished setting up checkout options...'); </script>";
			//todo: set handling and discounts here (if available)
			
			echo "created checkout helper.";
			$baseUrl = $this->get_return_url( $order );
			if( strpos( $baseUrl, '?') !== false ) {
				$baseUrl .= '&';
			} else {
				$baseUrl .= '?';
			}
			$options->setSuccessUrl( $baseUrl . 'wooyenepay=true&order_id=' . $order_id )
					->setCancelUrl( $baseUrl . 'wooyenepay=cancel&order_id=' . $order_id );
			
			$checkoutHelper = new CheckoutHelper();
			try{
				echo "getting the cart checkout url...";
				$redirectUrl = $checkoutHelper->getCartCheckoutUrl($options, $all_items);
				echo "<script> alert('redirect url: ". $redirectUrl . "'); </script>";
				return array(
					'result' => 'success',
					'redirect' => $redirectUrl
				);
			} catch (Exception $ex) {
				wc_add_notice(  $ex->getMessage(), 'error' );
				echo "<script> alert('exception: ". $ex->getMessage() . "'); </script>";
			}
			return array(
					'result' => 'failure',
					'redirect' => ''
			);
		}
	
		public function check_response() {
			global $woocommerce;
			 
			if( isset( $_GET['wooyenepay'] ) ) {
			 
				$wooyenepay = $_GET['wooyenepay'];
				$order_id = $_GET['order_id'];
				if( $order_id == 0 || $order_id == '' ) {
					return;
				}
				$order = new WC_Order( $order_id );
				if( $order->has_status('completed') || $order->has_status('processing') || $order->has_status('paid') || $order->has_status('delivered')) {
					return;
				}
				if( $wooyenepay == 'true' ) {
					$result = null;
					try {
						if(isset($_GET["TransactionId"])){
							$transactionId = $_GET["TransactionId"];
							// get pdt token from settings
							$pdtToken = $this->get_option('pdt_token');
							if(null != $pdtToken){
								$pdtModel = new PDT("PDT", $pdtToken, $transactionId);
								$useSandbox = $this->get_option('testmode');
								$pdtModel->setUseSandbox($useSandbox);
								$checkoutHelper = new CheckoutHelper();
								$result = $checkoutHelper->RequestPDT($pdtModel);
								//wc_add_notice(  $result['result'], 'error' );
								if($result['result'] == 'SUCCESS'){
									// Payment complete
									$order->payment_complete();
									// Add order note
									$order->add_order_note( sprintf( __( '%s payment approved! Transaction ID: %s ', 'woocommerce' ), $this->title, $transactionId ) );

								}
							}
						}
						
						//$result = $payment->execute($execution, $this->apiContext);
						  
					} catch (Exception $ex) { 
					
						  $data = json_decode( $ex->getData());
						  
						  wc_add_notice(  $ex->getMessage(), 'error' );
					
						  //$order->update_status('failed', sprintf( __( '%s payment failed! Transaction ID: %d', 'woocommerce' ), $this->title, $paymentId ) . ' ' . $ex->getMessage() );
						  return;
					}
								  
					// Remove cart
					$woocommerce->cart->empty_cart();
		  
				}
				if( $wooyenepay == 'cancel' ) { 
					$order = new WC_Order( $order_id );
					//$order->update_status('cancelled', sprintf( __( '%s payment cancelled! Transaction ID: %d', 'woocommerce' ), $this->title, $paymentId ) );
				}
			}
			return;
		}
	}
}

/**
 * Add Gateway class to all payment gateway methods
 */
function woo_add_gateway_class( $methods ) {
	
	$methods[] = 'Woo_YenePay_Gateway'; 
	return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'woo_add_gateway_class' );

add_action( 'init', 'check_for_wooyenepay' );

function check_for_wooyenepay() {
	if( isset($_GET['wooyenepay'])) {
	  // Start the gateways
		WC()->payment_gateways();
		do_action( 'check_wooyenepay' );
	}
	
}


 ?>