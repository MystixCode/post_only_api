<?php
################################################################################
# User Class                                                                   #
################################################################################
class User {
    
    ## LOGIN ###################################################################
    public function login($data) {
        $name=$data->name;
        $password=$data->password;
        if (!empty($name) and !empty($password)) {
            if (is_valid('alphanumeric_s3', $name)== true) {
                if (is_valid('alphanumeric_s1', $password)== true) {
                    if (strlen($password) >= 6) {
                        $pdo = new DB();
                        $pdo = $pdo->connect();
                        $stmt = $pdo->prepare('SELECT user.id, user.hash, role.name as role_name FROM user JOIN user_role ON user_role.user_id = user.id JOIN role ON role.id = user_role.role_id WHERE user.name = :name');
                        $stmt->execute(['name' => $name]);
                        $hash = '';
                        $user_id = '';
                        $rolenames = array();
                        while ($row = $stmt->fetch()) {
                            $hash = $row['hash'];
                            $user_id=$row['id'];
                            $role_names[]=$row['role_name'];
                        }
                        if (password_verify($password, $hash)) //if pw hash equals hash from db
                        {
                            //success -> Create token
                            $token = new JWT();
                            $token = $token->createToken($user_id, $name , $role_names);
                            #TODO on gameserver
                            # request signature key and token from api and store in variable ?? needed?
                            # check every udp packet for token and compare, and check signature if ok proceed: https://auth0.com/docs/tokens/guides/jwt/validate-jwt
                            # renew token before expired if logged in and still the same ip and profile?? <-- on game server change expiration?
                            return "login successfull";
                        }
                    }
                }
            }
        }
    }

    ## REGISTER ################################################################
    public function register($data) {
        $name=$data->name;	#TODO validate input   #TODO only register user if not user with same name exists!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $email=$data->email;
        $password=$data->password;
        $password2=$data->password2;
        if (!empty($name) and !empty($email) and !empty($password) and !empty($password2)) {
            if (is_valid('alphanumeric_s3', $name) == true) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL) != false) {
                    if (is_valid('alphanumeric_s1', $password) == true and is_valid('alphanumeric_s1', $password2) == true) {
                        if ($password === $password2) {
                            if (strlen($password) >= 6) {
                                $pdo = new DB();
                                $pdo = $pdo->connect();
                                #check if $user already exists in db
                                $stmt2 = $pdo->prepare('SELECT * FROM user WHERE name=?');
                                $stmt2->execute(array($name));
                                $row = $stmt2->fetch(PDO::FETCH_ASSOC);
                                if( ! $row) { #if username doesnt already exist
                                    $hash = password_hash($password, PASSWORD_DEFAULT);
                                    $stmt = $pdo->prepare('INSERT INTO user (hash, name, email) VALUES (?, ?, ?)');
                                    $stmt->execute(array($hash, $name, $email));
                                    //TODO ADD default Permissions!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                                    $user_id = $pdo->lastInsertId();
                                    $role_id = 2; //DEFAULT ROLE
                                    $stmtz = $pdo->prepare('INSERT INTO user_role (user_id, role_id) VALUES (?, ?)');
                                    $stmtz->execute(array($user_id, $role_id));
                                    return "user doesnt already exist - register done";
                                }
                                return "user already exists";
                            }
                        }
                    }
                }
            }
        }
        return "register failed - empty values";
    }

    ## LIST ####################################################################
    public function list($data) {
        return "api todo list user";
    }

    ## GET #####################################################################
    public function get($data, $user_id) {
        $pdo = new DB();
        $pdo = $pdo->connect();
        $stmt = $pdo->prepare('SELECT user.name, user.email, role.name as role_name FROM user JOIN user_role ON user_role.user_id = user.id JOIN role ON role.id = user_role.role_id WHERE user.id = :user_id');
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
        $response = json_encode(array(service => 'user', action => 'get', user_id => $user_id, user_name => $user_name, user_email => $user_email, roles => $role_names));
        return $response;
    }

    ## EDIT ####################################################################
    public function edit($data, $user_id) {
        $name=$data->name;	#TODO validate input   #TODO only register user if not user with same name exists!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $email=$data->email;
        $password=$data->password;
        $password2=$data->password2;
        $pdo = new DB();
        $pdo = $pdo->connect();
        if (!empty($name)) {
            if (is_valid('alphanumeric_s3', $name) == true) {
                $stmt = $pdo->prepare('UPDATE user SET name = :name WHERE id = :user_id');
                $stmt->execute(array($name, $user_id));
            }
        }
        if (!empty($email)) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL) != false) {
                $stmt = $pdo->prepare('UPDATE user SET email = :email WHERE id = :user_id');
                $stmt->execute(array($email, $user_id));
            }
        }
        if (!empty($password) and !empty($password2)) {
            if (is_valid('alphanumeric_s1', $password) == true and is_valid('alphanumeric_s1', $password2) == true){
                if ($password === $password2) {
                    if (strlen($password) >= 6) {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare('UPDATE user SET hash = :hash WHERE id = :user_id');
                        $stmt->execute(array($hash, $user_id));
                    }
                }
            }
        }
        $response = json_encode(array(service => 'user', action => 'edit', message => 'Done TODO errorhandling'));
        return $response;
    }

    ## DELETE ##################################################################
    public function delete($data)
    {
        return "api todo delete user";
    }

}

?>
