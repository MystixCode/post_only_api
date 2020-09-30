<?php
################################################################################
# User Class                                                                   #
################################################################################
class User {

  public function login($data)
  {
    $name=$data->name;
    $password=$data->password;

    if (!empty($name) and !empty($password))
    {
        if (is_valid('alphanumeric_s3', $name)== true)
        {
          if (is_valid('alphanumeric_s1', $password)== true)
          {
            if (strlen($password) >= 6)
            {
              $pdo = new DB();
              $pdo = $pdo->connect();
              $stmt = $pdo->prepare('SELECT id, hash FROM user WHERE name = :name');
              $stmt->execute(['name' => $name]);

              while ($row = $stmt->fetch())
              {
                if (password_verify($password, $row['hash'])) //if pw hash equals hash from db
                {
                   //success -> Create token
                   $token = new JWT();
                   $token = $token->createToken($row['id'], $name);

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
  }

  public function register($data)
  {
    $name=$data->name;	#TODO validate input   #TODO only register user if not user with same name exists!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    $email=$data->email;
    $password=$data->password;
    $password2=$data->password2;
    if (!empty($name) and !empty($email) and !empty($password) and !empty($password2))
    {
      if (is_valid('alphanumeric_s3', $name) == true)
      {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) != false)
        {
          if (is_valid('alphanumeric_s1', $password) == true and is_valid('alphanumeric_s1', $password2) == true)
          {
            if ($password === $password2)
            {
              if (strlen($password) >= 6)
              {
                  $pdo = new DB();
                  $pdo = $pdo->connect();

                #check if $user already exists in db
                $stmt2 = $pdo->prepare('SELECT * FROM user WHERE name=?');
                $stmt2->execute(array($name));
                $row = $stmt2->fetch(PDO::FETCH_ASSOC);
                if( ! $row) #if username doesnt already exist
                {
                  $hash = password_hash($password, PASSWORD_DEFAULT);

                  $stmt = $pdo->prepare('INSERT INTO user (hash, name, email) VALUES (?, ?, ?)');
                  $stmt->execute(array($hash, $name, $email));
//TODO ADD default Permissions
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

  public function edit($data)
  {
    //TODO verify token
    return "api todo edit user";
  }

  public function delete($data)
  {
    //TODO verify token
    return "api todo delete user";
  }

}









// 				if (is_valid('alphanumeric_s3', $user)== true)
// 				{
// 					if (filter_var($email, FILTER_VALIDATE_EMAIL) != false)
// 					{
// 						if (is_valid('alphanumeric_s1', $password) == true and is_valid('alphanumeric_s1', $password2) == true)
// 						{
// 							if ($password === $password2)
// 							{
// 								#TODO check if $user already exists in db
// 								if (strlen($password) >= 6)
// 								{
// 									$statement = $pdo->prepare('SELECT * FROM user WHERE user=?');
// 									$statement->execute(array($user));
// 									#$pdo->execute();
// 									$row = $statement->fetch(PDO::FETCH_ASSOC);
// 									if( ! $row) #if username doesnt already exist
// 									{
// 										$hash = password_hash($password, PASSWORD_DEFAULT); //https://www.php.net/manual/en/function.password-hash.php
// 										$statement = $pdo->prepare("INSERT INTO user (user, hash, email) VALUES (?, ?, ?)");
// 										$statement->execute(array($user, $hash, $email)); //write username and passwordhash to user db
// 										$neue_id = $pdo->lastInsertId(); //TODO pdo error handling
// 										echo "New User registered";
// 									}
// 									else
// 									{
// 										echo "username already taken";
// 										http_response_code(401); //user already exists
// 									}
// 								}
// 								else
// 								{
// 									echo "password need to be atleast 6 chars long";
// 									http_response_code(401); //user already exists
// 								}
// 							}
// 							else
// 							{
// 								echo "passwords dont match";
// 								http_response_code(401); //user already exists
// 							}
// 						}
// 						else
// 						{
// 							echo "passwords wrong format. allowed: a-z 0-9 .,!?:_@$";
// 							http_response_code(401); //user already exists
// 						}
// 					}
// 					else
// 					{
// 						echo "email not valid";
// 						http_response_code(401); //user already exists
// 					}
// 				}
// 				else
// 				{
// 					echo "username wrong format. allowed: a-z 0-9 _-";
// 					http_response_code(401); //user already exists
// 				}
// 			}
// 			else
// 			{
// 				echo "value empty";
// 				http_response_code(401); //invalid input
// 			}

?>
