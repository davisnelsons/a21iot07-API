<?php 
/*
Adapted from https://roytuts.com/php-rest-api-authentication-using-jwt/
*/
Class jwt_util {

public function generate_jwt($headers, $payload) {
	$secret = '----';


	$headers_encoded = $this->base64url_encode(json_encode($headers));
	
	$payload_encoded = $this->base64url_encode(json_encode($payload));
	
	$signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
	$signature_encoded = $this->base64url_encode($signature);
	
	$jwt = "$headers_encoded.$payload_encoded.$signature_encoded";
	
	return $jwt;
}


private function get_authorization_header(){
	$headers = null;
	
	if (isset($_SERVER['Authorization'])) {
		$headers = trim($_SERVER["Authorization"]);
	} else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
		$headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
	} else if (function_exists('apache_request_headers')) {
		$requestHeaders = apache_request_headers();
		// Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
		$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
		//print_r($requestHeaders);
		if (isset($requestHeaders['Authorization'])) {
			$headers = trim($requestHeaders['Authorization']);
		}
	}
	
	return $headers;
}

public function is_jwt_valid($jwt) {
	if(is_null($jwt)) {
		return FALSE;
	}

	$secret = 'iot0770tioIOT0770TIOIIIOOOTtT000777OOOTTTIII777';
	// split the jwt
	$tokenParts = explode('.', $jwt);
	$header = base64_decode($tokenParts[0]);
	$payload = base64_decode($tokenParts[1]);
	$signature_provided = $tokenParts[2];
	
	//echo "received header is " . $header . " and received payload is " . $payload;

	// check the expiration time - note this will cause an error if there is no 'exp' claim in the jwt
	$expiration = json_decode($payload)->exp;
	$is_token_expired = ($expiration - time()) < 0;

	// build a signature based on the header and payload using the secret
	$base64_url_header = $this->base64url_encode($header);
	$base64_url_payload = $this->base64url_encode($payload);
	$signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
	$base64_url_signature = $this->base64url_encode($signature);

	// verify it matches the signature provided in the jwt
	$is_signature_valid = ($base64_url_signature === $signature_provided);
	
	//echo "computed is ".$base64_url_signature. " and received is " . $signature_provided;
	if ($is_token_expired || !$is_signature_valid) {
		return FALSE;
	} else {
		return TRUE;
	} 
}

public function get_bearer_token() {
    $headers = $this->get_authorization_header();
	
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

public function get_user_id($token) {
    $tokenParts = explode('.', $token);
    $payload = json_decode(base64_decode($tokenParts[1]));
    return $payload->userId;
}
private function token_invalid() {
    http_response_code(401);
    echo json_encode(array("error"=>"invalid token"));
    exit();
}

private function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
  
private function base64url_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

}