<?php

return [

    'amounts' => [
        'subscription' => (int) env('PAYMENT_SUBSCRIPTION_AMOUNT', 50000), // UZS
        'banner'       => (int) env('PAYMENT_BANNER_AMOUNT', 30000),        // UZS
    ],

    'payme' => [
        'merchant_id'  => env('PAYME_MERCHANT_ID', ''),
        'key'          => env('PAYME_KEY', ''),
        'test_key'     => env('PAYME_TEST_KEY', ''),
        'test_mode'    => env('PAYME_TEST_MODE', true),
        'checkout_url' => env('PAYME_TEST_MODE', true)
            ? 'https://test.paycom.uz'
            : 'https://checkout.paycom.uz',
    ],

    'click' => [
        'service_id'        => env('CLICK_SERVICE_ID', ''),
        'merchant_id'       => env('CLICK_MERCHANT_ID', ''),
        'secret_key'        => env('CLICK_SECRET_KEY', ''),
        'merchant_user_id'  => env('CLICK_MERCHANT_USER_ID', ''),
        'checkout_url'      => 'https://my.click.uz/services/pay',
    ],

];
