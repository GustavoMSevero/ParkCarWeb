<?php
include_once("stripe-php-7.100.0/init.php");
include_once("config/config.php");
include_once("functions/jwt.php");
include_once("con.php");

$pdo = conectar();

$stripe = new \Stripe\StripeClient($stripClient);
$apiKey = \Stripe\Stripe::setApiKey($setApiKey);
$price = $_GET['price'] ?? 20;
$idClient = verifyJWT();

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

// $idClient = 29;
// echo json_encode($response);

$checkIfHasCredits = $pdo->prepare("SELECT idCredits FROM credits WHERE idClient=:idClient");
$checkIfHasCredits->bindValue(":idClient", $idClient);
$checkIfHasCredits->execute();

$qty = $checkIfHasCredits->rowCount();

if ($qty > 0) {
  // echo 'já tem créditos';
  $updateCreditValue=$pdo->prepare("UPDATE credits SET creditValue=:creditValue WHERE idClient=:idClient");
  $updateCreditValue->bindValue(":creditValue", $price);
  $updateCreditValue->bindValue(":idClient", $idClient);
  $updateCreditValue->execute();

} else {
  // echo 'não tem créditos';
  $buyCredits=$pdo->prepare("INSERT INTO credits (idCredits, idClient, customerStripe, creditValue) VALUES(?,?,?,?)");
  $buyCredits->bindValue(1, NULL);
  $buyCredits->bindValue(2, $idClient);
  $buyCredits->bindValue(3, $response['customer']);
  $buyCredits->bindValue(4, $price);
  $buyCredits->execute();
  
}

