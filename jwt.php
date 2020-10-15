<?php
################################################################################
# JWT Class                                                                   #
################################################################################
class JWT {
    private $pdo;

    function __construct() {
        $db = new DB();
        $this->pdo = $db->connect();
    }

    ## create refresh token ####################################################
    public function createRefreshToken($user_id) {
        $expires= time() + 50400; //TODO: into conf.ini $refreshTokenTTL
        $refresh_token=uniqid();
        //DELETE old token
        $stmt = $this->pdo->prepare('DELETE FROM refresh_token WHERE user_id = ?');
        $stmt->execute(array($user_id));
        //CREATE new token
        $stmt = $this->pdo->prepare('INSERT INTO refresh_token (user_id, expires, refresh_token) VALUES (?, ?, ?)');
        $stmt->execute(array($user_id, $expires, $refresh_token));

        return $refresh_token;
    }

    ## verify refresh token ####################################################
    public function verifyRefreshToken($refresh_token) {
        $jwt = new JWT();
        $token =$jwt->getToken();
        $jwtarray=explode('.', $token);
        $user_id=$payload = json_decode(base64_decode(strtr($jwtarray[1], '-_', '+/')), true)['sub'];
        $rolenames = array();
        $stmt = $this->pdo->prepare('SELECT refresh_token.expires, user.id, user.name, role.name as "role_name" FROM user JOIN refresh_token ON refresh_token.user_id=user.id AND refresh_token.refresh_token=:refresh_token JOIN user_role ON user_role.user_id = user.id JOIN role ON role.id = user_role.role_id WHERE user.id=:user_id;');
        $stmt->execute(array('user_id' => $user_id, 'refresh_token' => $refresh_token));
        $data = $stmt->fetchAll();
        if (!empty($data)){
            $name= $data['name'];
            $name='';
            $expires='';
            foreach ($data as $entry){
                $expires=$entry['expires'];
                $name= $entry['name'];
                $rolenames[]=$entry['role_name'];
            }
            if ($expires > time()){
                $tmp=array($user_id, $name, $rolenames);
                return $tmp;
            }
        }
        return false;
    }
    ## createToken #############################################################
    public function createToken($user_id, $name, $rolenames) {
        //TODO omit '=' in urldecode/encode
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']); // Create token header as a JSON string
        $payloaddata = array(
            'iss' => "https://" . $_SERVER['SERVER_NAME'],
            'aud' => "https://mystixgame.tk",
            'sub' => $user_id,
            'iat' => time(),
            'exp' => time() + 300, //TODO into conf.ini $tokenTTL
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
        return $jwt;
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
