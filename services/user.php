<?php
################################################################################
# User Class                                                                   #
################################################################################

#TODO permission service handles Permissions and maybe checkPermission and execute function
#TODO role service handles  get role editrole etc
#TODO naming off things add create etc login/register
class User {
    private $pdo;

    function __construct() {
        $db = new DB();
        $this->pdo = $db->connect();
    }

    ## LOGIN ###################################################################
    public function login($data) {
        $name=$data->name;
        $password=$data->password;
        if (!empty($name) and !empty($password)) {
            if (is_valid('alphanumeric_s3', $name)== true) {
                if (is_valid('alphanumeric_s1', $password)== true) {
                    if (strlen($password) >= 6) {
                        $stmt = $this->pdo->prepare('SELECT user.id, user.hash, role.name as role_name FROM user JOIN user_role ON user_role.user_id = user.id JOIN role ON role.id = user_role.role_id WHERE user.name = :name');
                        $stmt->execute(['name' => $name]);
                        $hash = '';
                        $user_id = '';
                        $rolenames = array();
                        while ($row = $stmt->fetch()) {
                            $hash = $row['hash'];
                            $user_id=$row['id'];
                            $role_names[]=$row['role_name'];
                        }
                        if (password_verify($password, $hash)) {
                            $token = new JWT();
                            $token = $token->createToken($user_id, $name , $role_names);
                            return array(message => 'loggedin successful');
                        }
                    }
                }
            }
        }
    }

    ## REGISTER ################################################################
    public function register($data) {
        $name=$data->name;
        $email=$data->email;
        $password=$data->password;
        $password2=$data->password2;
        if (!empty($name) and !empty($email) and !empty($password) and !empty($password2)) {
            if (is_valid('alphanumeric_s3', $name) == true) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL) != false) {
                    if (is_valid('alphanumeric_s1', $password) == true and is_valid('alphanumeric_s1', $password2) == true) {
                        if ($password === $password2) {
                            if (strlen($password) >= 6) {
                                #check if $user already exists in db
                                $stmt2 = $this->pdo->prepare('SELECT * FROM user WHERE name=?');
                                $stmt2->execute(array($name));
                                $row = $stmt2->fetch(PDO::FETCH_ASSOC);
                                if( ! $row) { #if username doesnt already exist
                                    $hash = password_hash($password, PASSWORD_DEFAULT);
                                    $stmt = $this->pdo->prepare('INSERT INTO user (hash, name, email) VALUES (?, ?, ?)');
                                    $stmt->execute(array($hash, $name, $email));
                                    //TODO ADD default Permissions!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                                    $user_id = $this->pdo->lastInsertId();
                                    $role_id = 2; //DEFAULT ROLE
                                    $stmtz = $this->pdo->prepare('INSERT INTO user_role (user_id, role_id) VALUES (?, ?)');
                                    $stmtz->execute(array($user_id, $role_id));
                                    return array(message => 'user doesnt already exist - register done');
                                }
                                return array(message => 'user already exists');
                            }
                        }
                    }
                }
            }
        }
        return array(message => 'register failed - empty values');
    }

    ## LIST ####################################################################
    public function list($data) {
        $stmt = $this->pdo->prepare('SELECT user.id, user.name, user.email, role.name as role_name FROM user JOIN user_role ON user_role.user_id = user.id JOIN role ON role.id = user_role.role_id');
        $stmt->execute();
        $payload=array();
        $data = $stmt->fetchAll();
        foreach ($data as $row) {
            $entry = array();
            $entry['user_id']=$row['id'];
            $entry['user_name']=$row['name'];
            $entry['user_email']=$row['email'];
            $entry['user_roles'][]=$row['role_name'];
            $alreadyexists = false;
            foreach ($payload as $key=>$item){
                   if (isset($item['user_id']) && $item['user_id'] == $row['id']) {
                       $payload[$key]['user_roles'][] = $row['role_name'];
                       $alreadyexists = true;
                   }
               }
             if ($alreadyexists == false){
                 $payload[]=$entry;
             }
        }
        return $payload;
    }

    ## GETOTHER #####################################################################
    public function getOther($data, $user_id) {
        $user_id=$data->id;
        if (isset($user_id)) {
            if (is_valid('numeric', $user_id) == true) {
                $stmt = $this->pdo->prepare('SELECT user.name, user.email, role.name as role_name FROM user JOIN user_role ON user_role.user_id = user.id JOIN role ON role.id = user_role.role_id WHERE user.id = :user_id');
                $stmt->execute(array($user_id));
                $user_name='';
                $user_email='';
                $rolen_names = array();
                while ($row = $stmt->fetch())
                {
                    $user_name=$row['name'];
                    $user_email=$row['email'];
                    $role_names[]=$row['role_name'];
                }
                return array(user_id => $user_id, user_name => $user_name, user_email => $user_email, roles => $role_names);
            }
        }
    }

    ## GET #####################################################################
    public function get($data, $user_id) {
        $stmt = $this->pdo->prepare('SELECT user.name, user.email, role.name as role_name FROM user JOIN user_role ON user_role.user_id = user.id JOIN role ON role.id = user_role.role_id WHERE user.id = :user_id');
        $stmt->execute(array($user_id));
        $user_name='';
        $user_email='';
        $rolen_names = array();
        while ($row = $stmt->fetch())
        {
            $user_name=$row['name'];
            $user_email=$row['email'];
            $role_names[]=$row['role_name'];
        }
        return array(user_id => $user_id, user_name => $user_name, user_email => $user_email, roles => $role_names);
    }

    ## EDIT Other ####################################################################
    public function editOther($data, $user_id) {
        $user_id=$data->id;
        if (isset($user_id)) {
            if (is_valid('numeric', $user_id) == true) {
                $name=$data->name;	#TODO validate input   #TODO only register user if not user with same name exists!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                $email=$data->email;
                $password=$data->password;
                $password2=$data->password2;
                if (!empty($name)) {
                    if (is_valid('alphanumeric_s3', $name) == true) {
                        $stmt = $this->pdo->prepare('UPDATE user SET name = :name WHERE id = :user_id');
                        $stmt->execute(array($name, $user_id));
                    }
                }
                if (!empty($email)) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL) != false) {
                        $stmt = $this->pdo->prepare('UPDATE user SET email = :email WHERE id = :user_id');
                        $stmt->execute(array($email, $user_id));
                    }
                }
                if (!empty($password) and !empty($password2)) {
                    if (is_valid('alphanumeric_s1', $password) == true and is_valid('alphanumeric_s1', $password2) == true){
                        if ($password === $password2) {
                            if (strlen($password) >= 6) {
                                $hash = password_hash($password, PASSWORD_DEFAULT);
                                $stmt = $this->pdo->prepare('UPDATE user SET hash = :hash WHERE id = :user_id');
                                $stmt->execute(array($hash, $user_id));
                            }
                        }
                    }
                }
                return array(message => 'Done TODO errorhandling');
            }
        }
    }

    ## EDIT ####################################################################
    public function edit($data, $user_id) {
        $name=$data->name;
        $email=$data->email;
        $password=$data->password;
        $password2=$data->password2;
        if (!empty($name)) {
            if (is_valid('alphanumeric_s3', $name) == true) {
                $stmt = $this->pdo->prepare('UPDATE user SET name = :name WHERE id = :user_id');
                $stmt->execute(array($name, $user_id));
            }
        }
        if (!empty($email)) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL) != false) {
                $stmt = $this->pdo->prepare('UPDATE user SET email = :email WHERE id = :user_id');
                $stmt->execute(array($email, $user_id));
            }
        }
        if (!empty($password) and !empty($password2)) {
            if (is_valid('alphanumeric_s1', $password) == true and is_valid('alphanumeric_s1', $password2) == true){
                if ($password === $password2) {
                    if (strlen($password) >= 6) {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $this->pdo->prepare('UPDATE user SET hash = :hash WHERE id = :user_id');
                        $stmt->execute(array($hash, $user_id));
                    }
                }
            }
        }
        return array(message => 'Done TODO errorhandling');
    }

    ## DELETE ##################################################################
    public function delete($data, $user_id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM chars WHERE user_id = :user_id');
        $stmt->execute(array($user_id));
        $stmt = $this->pdo->prepare('DELETE FROM user_role WHERE user_id = :user_id');
        $stmt->execute(array($user_id));
        $stmt = $this->pdo->prepare('DELETE FROM user WHERE id = :user_id');
        $stmt->execute(array($user_id));
        return array(message => 'done TODO: errorhandling');
    }

    ## DELETE Other##################################################################
    public function deleteOther($data, $user_id)
    {
        $id=$data->id;
        if (isset($id)) {
            if (is_valid('numeric', $id) == true) {
                $stmt = $this->pdo->prepare('DELETE FROM user_role WHERE user_id = :user_id');
                $stmt->execute(array($id));
                $stmt = $this->pdo->prepare('DELETE FROM user WHERE id = :user_id');
                $stmt->execute(array($id));
                return array(message => 'done TODO: errorhandling');
            }
        }
    }

    ## LIST Roles ####################################################################
    public function listRoles($data) {
        $stmt = $this->pdo->prepare('SELECT role.id as "role_id", role.name as "role_name", role.name as "role_name" FROM role;');
        $stmt->execute();
        $payload=array();
        $data = $stmt->fetchAll();
        foreach ($data as $key =>$row) {
            $payload[$key]['role_id']=$row['role_id'];
            $payload[$key]['role_name']=$row['role_name'];
        }
        return $payload;
    }

    ## GET Roles ####################################################################
    public function getRole($data) {
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

    ## Edit Roles ####################################################################
    public function editRole($data) {

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

    ## Add Role ####################################################################
    public function addRole($data) {
        $name=$data->name;
        if (!empty($name)){
            if (is_valid('alphanumeric', $name) == true) {
                $stmt = $this->pdo->prepare('INSERT INTO role (name) VALUES (?)');
                $stmt->execute(array($name));
                return "done  todo api errorhandling";
            }
        }
    }
    ## Delete Role ####################################################################
    public function deleteRole($data) {
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

    ## LIST ALL PERMISSION ####################################################################
    public function listAllPermission($data) {
        $stmt = $this->pdo->prepare('SELECT permission.name as "permission_name", role.name as "role_name" FROM permission JOIN role_permission ON role_permission.permission_id = permission.id JOIN role ON role.id = role_permission.role_id ORDER BY permission.name;');
        $stmt->execute();
        $payload=array();
        $data = $stmt->fetchAll();
        foreach ($data as $row) {
            $alreadyexists = false;
            foreach ($payload as $key=>$item){
                   if (isset($item['permission_name']) && $item['permission_name'] == $row['permission_name']) {
                       $payload[$key]['roles'][] = $row['role_name'];
                       $alreadyexists = true;
                   }
               }
             if ($alreadyexists == false){
                 $entry = array();
                 $entry['permission_name']=$row['permission_name'];
                 $entry['roles'][]=$row['role_name'];
                 $payload[]=$entry;
             }
        }
        return $payload;
    }

    ## EDIT ALL PERMISSION ####################################################################
    public function editAllPermission($data) {

    }
}

?>
