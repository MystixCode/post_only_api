<?php
################################################################################
# JWT Class                                                                   #
################################################################################
class JWT {

    ## createToken #############################################################
    public function createToken($id, $name, $rolenames) {
        //TODO omit '=' in urldecode/encode
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']); // Create token header as a JSON string
        $payloaddata = array(
            'iss' => "https://" . $_SERVER['SERVER_NAME'],
            'aud' => "https://mystixgame.tk",
            'sub' => $id,
            'iat' => time(),
            'exp' => time() + 1800,
            'username' => $name,
            'roles' => $rolenames
        );
        $payload = json_encode($payloaddata); // Create token payload as a JSON string
        $base64UrlHeader = str_replace(['+', '/'], ['-', '_'], base64_encode($header)); // Encode Header to Base64Url String
        $base64UrlPayload = str_replace(['+', '/'], ['-', '_'], base64_encode($payload)); // Encode Payload to Base64Url String
        $conf = parse_ini_file('conf.ini');
        $signaturekey=$conf['jwt_secret'];
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $signaturekey, true); // Create Signature Hash
        $base64UrlSignature = str_replace(['+', '/'], ['-', '_'], base64_encode($signature)); // Encode Signature to Base64Url String
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature; // Create JWT
        header('Authorization: Bearer '.$jwt); //Put jwt into response header
        header('Access-Control-Expose-Headers: Authorization');
        return true;
    }

    ## verifyToken #############################################################
    public function verifyToken($token) {
        $conf = parse_ini_file('conf.ini');
        $signaturekey=$conf['jwt_secret'];
        $jwtarray=explode('.', $token);
        if (count($jwtarray) == 3) { #if jwt consists of 3 items separated by .
            $header = json_decode(base64_decode(strtr($jwtarray[0], '-_', '+/')), true); #decode
            $payload = json_decode(base64_decode(strtr($jwtarray[1], '-_', '+/')), true); #decode
            $signature1 = $jwtarray[2];
            if ($header["alg"] == "HS256") {
                $signature2 = strtr(base64_encode(hash_hmac('sha256', $jwtarray[0] . "." . $jwtarray[1], $signaturekey, true)), '+/', '-_');
                if($signature1 === $signature2){ #if signature is valid
                    if ($payload["iss"] AND $payload["aud"] AND $payload["sub"] AND $payload["iat"] AND $payload["exp"]) { #if payloadparams
                        if ($payload["iss"]=="https://" . $_SERVER['SERVER_NAME']) {
                            if ($payload["aud"] == "https://mystixgame.tk"){
                                if ($payload["exp"] > time()) {
                                    return $payload;
                                }
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    ## getToken ################################################################
    public function getToken(){
        $headers = apache_request_headers();
        if(isset($headers['Authorization'])){
            if (substr( $headers['Authorization'], 0, 7 ) === "Bearer "){
                $token = str_replace("Bearer ", "", $headers['Authorization']);
                return $token;
            }
        }
        return false;
    }
}
