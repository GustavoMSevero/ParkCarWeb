<?php

function jwt($idClient, $type) {
   $jwtPassword = "75f27952739ff55ccdfcca853ea14a2c";

   try {
      
      $header = [
         'alg' => 'HS256',
         'typ' => 'JWT'
      ];
      $header = json_encode($header);
      $header = base64_encode($header);
      
      $payload = [
         'id' => $idClient,
         'type' => $type,
         'iat' => time(),
         'exp' => time() + (30 * 24 * 60 * 60)
      ];

      $payload = json_encode($payload);
      $payload = base64_encode($payload);
      
      $signature = hash_hmac('sha256', "$header.$payload", $jwtPassword, true);
      $signature = base64_encode($signature);
      
      $token = "$header.$payload.$signature";

      $refreshToken = json_encode(md5(uniqid(rand(), true)));
      $refreshToken = base64_encode($refreshToken);


      $refreshTokenP = explode("==", $refreshToken);
      $refreshToken = $refreshTokenP[0];
      
      $return = array(
         'token' => $token,
         'refreshToken' => $refreshToken
      );
      
      return $return;
      
   } catch (Exception $e) {
      echo $e->getMessage();
   }
}


function verifyJWT() {
   $jwtPassword = "75f27952739ff55ccdfcca853ea14a2c";

   $token = isset($_GET['token']) ? $_GET['token'] : null;

   if (!$token) {
      throw new Exception("Token not provided.");
   }

   $token = explode(".", $token);

   $header = $token[0] ?? null;
   $payload = $token[1] ?? null;
   $signature = $token[2] ?? null;

   $decodedPayload = base64_decode($payload);
   $decodedPayload = json_decode($decodedPayload, true);

   $isTokenExpired = (($decodedPayload['exp'] - time()) < 0 ? true : false);

   if ($isTokenExpired) throw new Exception("Token expired", 1);
   

   $valid = hash_hmac('sha256',"$header.$payload", $jwtPassword, true);
   $valid = base64_encode($valid);

   if($signature !== $valid){
      throw new Exception("Token invalid", 1); 
   }

   return $decodedPayload['id'];
}

