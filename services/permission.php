<?php
################################################################################
# Permisssion service                                                          #
################################################################################
class Permission {
    private $pdo;

    function __construct() {
        $db = new DB();
        $this->pdo = $db->connect();
    }

    ## LIST ALL PERMISSION #####################################################
    public function list($data) {
        $stmt = $this->pdo->prepare('SELECT role.id as "role_id", role.name as "role_name" FROM role;');
        $stmt->execute();
        $data1 = $stmt->fetchAll();
        $roles = array();
        foreach ($data1 as $key=>$role) {
            $roles[$key]['role_id']=$role['role_id'];
            $roles[$key]['role_name']=$role['role_name'];
        }
        $stmt = $this->pdo->prepare('SELECT permission.id as "permission_id", permission.name as "permission_name" FROM permission;');
        $stmt->execute();
        $data2 = $stmt->fetchAll();
        $permissions=array();
        foreach ($data2 as $key=>$row){
            $permissions[$key]['permission_id']=$row['permission_id'];
            $permissions[$key]['permission_name']=$row['permission_name'];
            $permission_id=$row['permission_id'];
            $stmtx = $this->pdo->prepare('SELECT role.id as "role_id", role.name as "role_name" FROM role JOIN role_permission ON role_permission.role_id=role.id WHERE permission_id = :permission_id;');
            $stmtx->execute(array($permission_id));

            $data3 = $stmtx->fetchAll();
            foreach ($data3 as $keyx=>$rolex) {
                $permissions[$key]['roles'][$keyx] = $rolex['role_id'];
            }
        }
        $payload=array();
        $payload['roles'] = $roles;
        $payload['permissions'] = $permissions;
        return $payload;

    }

    ## addToRole ###############################################################
    public function addToRole($data) {
        if (!empty($data->role_id) && !empty($data->permission_id)){
            if (is_valid('numeric', $data->role_id) == true && is_valid('numeric', $data->permission_id) == true) {
                //TODO if permission_id and role_id not already in role_permission
                $stmt = $this->pdo->prepare('INSERT INTO role_permission (role_id, permission_id) VALUES (:role_id,:permission_id)');
                $stmt->execute(array($data->role_id,$data->permission_id)); //TODO doesnt always work -.-
                return array(message => 'addPermissionToRole done');
            }
        }
        return array(message => 'addPermissionToRole error');
    }

    ## deleteFromRole ##########################################################
    public function deleteFromRole($data) {
        if (!empty($data->role_id) && !empty($data->permission_id)){
            if (is_valid('numeric', $data->role_id) == true && is_valid('numeric', $data->permission_id) == true) {
                $stmt = $this->pdo->prepare('DELETE FROM role_permission WHERE role_id  = ? AND permission_id = ?');
                $stmt->execute(array($data->role_id,$data->permission_id));
                return array(message => 'removePermissionFromRole done');
            }
        }
        return array(message => 'removePermissionFromRole error');
    }


}

?>
