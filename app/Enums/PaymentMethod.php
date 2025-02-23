<?php

namespace App\Enums;

enum PaymentMethod
{
    const COD = 'cash';
    const CASH = 'cash';
    const WALLET = 'wallet';
    const PAYPAL = 'paypal';
    const STRIPE = 'stripe';
    const MOLLIE = 'mollie';
    const RAZORPAY = 'razorpay';
    const ALL_PAYMENT_METHODS = [
        'cash', 'paypal', 'stripe', 'mollie', 'razorpay',
    ];
}
