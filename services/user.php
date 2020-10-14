<?php
################################################################################
# User Class                                                                   #
################################################################################

#TODO permission service handles Permissions and maybe checkPermissionandexecute function
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
    public function add($data) {
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
                                    $user_id = $this->pdo->lastInsertId();
                                    $role_id = 2; //DEFAULT ROLE !!!!!!!!!!!!!!!
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

    ## GETOTHER ################################################################
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

    ## EDIT Other ##############################################################
    public function editOther($data, $user_id) {
        $user_id=$data->id;
        if (isset($user_id)) {
            if (is_valid('numeric', $user_id) == true) {
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
        }
    }

    ## DELETE Other#############################################################
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

}

?>
