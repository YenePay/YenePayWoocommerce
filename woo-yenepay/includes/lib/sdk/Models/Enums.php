<?php

namespace YenePay\Models;

/**
 * Abstract Class CheckoutType
 *
 * Abstract class to enumerate available checkout types for YenePay
 *
 * @package YenePay\Models
 *
 */
 
abstract class CheckoutType
{
    const Express = "Express";
    const Cart = "Cart";
	const Deposit = "Deposit";
    const BulkPayment = "BulkPayment";
    const PaymentReceived = "PaymentReceived";
}

?>