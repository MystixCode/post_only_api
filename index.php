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
        //TODO if service or action empty or not existing-->http error
        $service = $data->service;
        $action = $data->action;
        $payload = $data->payload;
        if ($service && $action){
            $response = [];
            $response['service'] = $service;
            $response['action'] = $action;
            $response['payload'] = checkPermissionAndExecute($service, $action, $payload);
            echo json_encode($response);
        }
        else{
            http_response_code(406);
        }
    }
}

function checkPermissionAndExecute($service, $action, $payload){
    $permission_name=$service . '_' . $action;
    $instance = new JWT();
    $token = $instance->getToken();
    $decoded_payload = $instance->verifyToken($token);
    require("services/" . $service . ".php");
    $svc = new $service();
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
        }
        catch(PDOException $e)
        {
            return $e->getMessage();
        }
    }
}
?>
