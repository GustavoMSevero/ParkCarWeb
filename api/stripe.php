<?php
include_once("stripe-php-7.100.0/init.php");
include_once("config/config.php");

$stripe = new \Stripe\StripeClient($stripClient);
$apiKey = \Stripe\Stripe::setApiKey($setApiKey);
$price = $_GET['price'] ?? 20;

$customer = \Stripe\Customer::create();

$ephemeralKey = \Stripe\EphemeralKey::create(
  ['customer' => $customer->id],
  ['stripe_version' => '2020-08-27']
);
$paymentIntent = \Stripe\PaymentIntent::create([
  'amount' => $price * 100,
  'currency' => 'brl',
  'customer' => $customer->id
]);

$response = array(
    'paymentIntent' => $paymentIntent->client_secret,
    'ephemeralKey' => $ephemeralKey->secret,
    'customer' => $customer->id
);


echo json_encode($response);
