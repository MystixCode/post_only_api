<?php
################################################################################
# mystix-api                                                                   #
################################################################################
#TODO maybe integrate API key functionality one day..

require("db.php");
require("jwt.php");
require("validation.php");
require("services/user.php");
require("services/character.php");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Max-Age: 3600");
header("Content-Type: application/json; charset=UTF-8");
header('Access-Control-Allow-Headers: *');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = @json_decode($json);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(406);
    } else {
        #TODO validation
        //TODO if service or action empty or not existing-->http error
        $service = $data->service;
        $action = $data->action;
        $response = [];
        $response['service'] = $service;
        $response['action'] = $action;
        $svc = new $service();
        if (needAuth($service, $action) === true) {
            $instance = new JWT();
            $token = $instance->getToken();
            $decoded_payload = $instance->verifyToken($token);
            if ($decoded_payload != false) {
                $user_id=$decoded_payload['sub'];
                $needed_permission=$service . '_' . $action;
                if (hasPermission($user_id, $needed_permission)) { //TODO baustell!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                    $response['payload']  = $svc->$action($data->payload, $user_id);
                }
                else {
                    http_response_code(401);
                    $response["payload"] = array('message' => 'permission required');
                }
            } else {
                http_response_code(401);
                $response["payload"] = array('message' => 'auth required');
            }
        } else {
            $response['payload']  = $svc->$action($data->payload);
        }
        echo json_encode($response);
    }
}

function needAuth($service, $action) {
    $need = array(
        "user" => ["list", "get", "edit", "delete", "getOther", "editOther"],
        "character" => ["list", "get", "edit", "delete"]
    );
    foreach ($need as $needle => $values) {
        if ($needle == $service) {
            if (in_array($action, $values)) {
                return true;
            }
        }
    }
    return false;
}

function hasPermission($user_id, $needed_permission) {
    //TODO check if user has permission
    $pdo = new DB();
    $pdo = $pdo->connect();
    $permission_name = $needed_permission;
    $stmt = $pdo->prepare('SELECT permission.id FROM permission JOIN (role_permission, role, user_role, user) ON (permission.name=:permission_name AND role_permission.permission_id=permission.id AND role.id=role_permission.role_id AND  user_role.role_id=role.id AND user.id=user_role.user_id AND user.id = :user_id);');
    $stmt->execute(['user_id' => $user_id, 'permission_name' => $permission_name]);
    while ($row = $stmt->fetch())
    {
        return true;
    }
    return false;
}

?>
