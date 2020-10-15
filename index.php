<?php
################################################################################
# mystix-api                                                                   #
################################################################################
#TODO maybe integrate API key functionality one day..
require("db.php");
require("jwt.php");
require("validation.php");

header('Access-Control-Allow-Origin: *');
header("Access-Control-Max-Age: 3600");
header('Access-Control-Allow-Headers: *');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo '<h1>Mystix API</h1>';
    echo '<a href="https://github.com/MystixGame/api">Mystix API on Github</a>';
}

## if POST -> check token and permission and then run service->action ###########
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Content-Type: application/json; charset=UTF-8");
    $json = file_get_contents('php://input');
    $data = @json_decode($json);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(406);
    } else {
        #TODO validation
        $service = $data->service;
        $action = $data->action;
        $payload = $data->payload;
        if ($service && $action){
            $response = [];
            $response['service'] = $service;
            $response['action'] = $action;

            if ($service=='token' && $action == 'refresh'){
                $jwt = new JWT();
                $tmp = $jwt->verifyRefreshToken($payload);
                if ($tmp !== false){
                    $user_id=$tmp[0];
                    $name=$tmp[1];
                    $role_names=$tmp[2];
                    $access_token = $jwt->createToken($user_id, $name , $role_names);
                    $refresh_token = $jwt->createRefreshToken($user_id);
                    $entry = array();
                    $entry["token_type"] = "bearer";
                    $entry["access_token"] = $access_token;
                    $entry["refresh_token"] = $refresh_token;
                    $response['payload'] = $entry;
                    echo json_encode($response);
                }
            }
            else{
                $test = checkPermissionAndExecute($service, $action, $payload);
                if (array_key_exists('error', $test)) {
                    http_response_code($test['error']);
                }
                else{
                    $response['payload'] = $test;
                }
                echo json_encode($response);
            }
        }
        else{
            http_response_code(406);
        }
    }
}

function checkPermissionAndExecute($service, $action, $payload){

    if (!file_exists("services/" . $service . ".php")) {
        return array(error => '404');
    }
    require("services/" . $service . ".php");
    $permission_name=$service . '_' . $action;
    $instance = new JWT();
    $token = $instance->getToken();
    $decoded_payload = $instance->verifyToken($token);
    $svc = new $service();
    if (!method_exists($svc, $action)){
        return array(error => '404');
    }
    $pdo = new DB();
    $pdo = $pdo->connect();

    if ($decoded_payload != false) { //IF LOGGEDIN USER
        //check in db if user_id has permission
        $user_id=$decoded_payload['sub'];
        try
        {
            $stmt = $pdo->prepare('SELECT permission.id FROM permission JOIN (role_permission, role, user_role, user) ON (permission.name=:permission_name AND role_permission.permission_id=permission.id AND role.id=role_permission.role_id AND  user_role.role_id=role.id AND user.id=user_role.user_id AND user.id = :user_id);');
            $stmt->execute(['user_id' => $user_id, 'permission_name' => $permission_name]);
            while ($row = $stmt->fetch()){
                return $svc->$action($payload, $user_id);
            }
            return array(error => '401');
        }
        catch(PDOException $e)
        {
            return $e->getMessage();
        }
    } else { //IF ANONYMOUS USER
        //check in db if anon role has permission
        try
        {
            $stmt = $pdo->prepare('SELECT role_permission.id from role_permission JOIN (permission, role) ON (permission.id=role_permission.permission_id AND permission.name=:permission_name AND role.id=role_permission.role_id AND role.name = "anon");');
            $stmt->execute(['permission_name' => $permission_name]);
            while ($row = $stmt->fetch())
            {
                return $svc->$action($payload);
            }
            return array(error => '401');
        }
        catch(PDOException $e)
        {
            return $e->getMessage();
        }
    }
}
?>
