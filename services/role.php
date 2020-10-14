<?php
################################################################################
# User Class                                                                   #
################################################################################

#TODO permission service handles Permissions and maybe checkPermissionandexecute function
#TODO role service handles  get role editrole etc
#TODO naming off things add create etc login/register
class Role {
    private $pdo;

    function __construct() {
        $db = new DB();
        $this->pdo = $db->connect();
    }


    ## GET Role ###############################################################
    public function get($data) {
        $id=$data->id;
        if (isset($id)) {
            if (is_valid('numeric', $id) == true) {
                $stmt = $this->pdo->prepare('SELECT role.id as "role_id", role.name as "role_name", role.name as "role_name" FROM role WHERE role.id=:role_id;');
                $stmt->execute(array($id));
                $data = $stmt->fetchAll();
                foreach ($data as $key =>$row) {
                    $payload['role_id']=$row['role_id'];
                    $payload['role_name']=$row['role_name'];
                }
                return $payload;
            }
        }
    }

    ## Edit Roles ##############################################################
    public function edit($data) {

        $role_id=$data->role_id;
        $role_name=$data->role_name;
        if (isset($role_id) && isset($role_name) ) {
            if ((is_valid('numeric', $role_id) == true) && (is_valid('alphanumeric', $role_name) == true) ) {
                $stmt = $this->pdo->prepare('UPDATE role SET name = :role_name WHERE id = :role_id');
                $stmt->execute(array($role_name, $role_id));
                return array(message => 'API DONE TODO..');
            }
        }
        return "api error editRole";
    }

    ## Add Role ################################################################
    public function add($data) {
        $name=$data->name;
        if (!empty($name)){
            if (is_valid('alphanumeric', $name) == true) {
                $stmt = $this->pdo->prepare('INSERT INTO role (name) VALUES (?)');
                $stmt->execute(array($name));
                return "done  todo api errorhandling";
            }
        }
    }
    ## Delete Role #############################################################
    public function delete($data) {
        $role_id=$data->role_id;
        if (isset($role_id)) {
            if (is_valid('numeric', $role_id) == true) {
                $stmt = $this->pdo->prepare('DELETE FROM role_permission WHERE role_id  = :role_id');
                $stmt->execute(array($role_id));
                $stmt = $this->pdo->prepare('DELETE FROM user_role WHERE role_id  = :role_id');
                $stmt->execute(array($role_id));
                $stmt = $this->pdo->prepare('DELETE FROM role WHERE id  = :role_id');
                $stmt->execute(array($role_id));
                return array(message => 'done TODO: errorhandling');
            }
        }
        return 'error';
    }

    ## LIST Roles ##############################################################
    public function list($data) {
        $stmt = $this->pdo->prepare('SELECT id as "role_id", name as "role_name" FROM role;');
        $stmt->execute();
        $payload=array();
        $data = $stmt->fetchAll();
        foreach ($data as $key =>$row) {
            $payload[$key]['role_id']=$row['role_id'];
            $payload[$key]['role_name']=$row['role_name'];
        }
        return $payload;
    }

}

?>
