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


  if ($_SERVER['REQUEST_METHOD'] === 'POST')
  {
    $json = file_get_contents('php://input');
	 	$data = @json_decode($json);
	 	if ($data === null && json_last_error() !== JSON_ERROR_NONE)
	 	{
	 	 	http_response_code(406);
	 	}
	 	else
    {
      #TODO validation
			//TODO if service or action empty or not existing-->http error
      $service = $data->service;
      $action = $data->action;
      $response = [];
      $response['service'] = $service;
      $response['action'] = $action;
      $svc = new $service();
      $response['payload']  = $svc->$action($data->payload);
      echo json_encode($response);
    }
  }





















	// 		$pdo = new PDO('mysql:host=localhost;dbname=MystixGame', 'root', 'test123');
	// 		$action = $data->action;
	// 		if ($action==='login')
	// 		{
	// 			$user=$data->user;	#TODO validate input
	// 			$password=$data->password;
	// 			if (!empty($user) and !empty($password))
	// 			{
	// 				if (is_valid('alphanumeric_s3', $user)== true)
	// 				{
	// 					if (is_valid('alphanumeric_s1', $password)== true)
	// 					{
	// 						if (strlen($password) >= 6)
	// 						{
	// 							$statement = $pdo->prepare("SELECT * FROM user WHERE user = :user");
	// 							if($statement->execute(array(':user' => $user)))
	// 							{
	// 								$count=0;
	// 								while($row = $statement->fetch())
	// 								{
	// 									$count +=1;
	// 									if ($count > 1){
	// 										return;
	// 									}
	// 									$hash=$row['hash'];
	// 								 	if (password_verify($password, $hash)) //if pw hash equals hash from db
	// 									{
	// 										$header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']); // Create token header as a JSON string
	// 										$payloaddata = array(
	// 			      					'iss' => "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
	// 			      					'aud' => "https://192.168.1.151",
	// 			      					'sub' => $row['id'],
	// 			      					'iat' => time(),
	// 			      					'exp' => time() + 3600,
	// 											'username' => $user
	// 			  						);
	// 										$payload = json_encode($payloaddata); // Create token payload as a JSON string
	// 										$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', '~'], base64_encode($header)); // Encode Header to Base64Url String
	// 										$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', '~'], base64_encode($payload)); // Encode Payload to Base64Url String
	// 										$signaturekey='rabC123!';
	// 										$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $signaturekey, true); // Create Signature Hash
	// 										$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', '~'], base64_encode($signature)); // Encode Signature to Base64Url String
	// 										$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature; // Create JWT
	// 										header('Authorization: Bearer '.$jwt); //Put jwt into response header
	// 										#TODO on gameserver
	// 										# request signature key and token from api and store in variable ?? needed?
	// 										# check every udp packet for token and compare, and check signature if ok proceed: https://auth0.com/docs/tokens/guides/jwt/validate-jwt
	// 										# renew token before expired if logged in and still the same ip and profile?? <-- on game server change expiration?
	// 									}
	// 									else
	// 									{
	// 										echo "invalid";
	// 										http_response_code(401); //invalid password
	// 									}
	// 							  }
	// 							}
	// 							else
	// 							{
	// 								echo "invalid";
	// 								http_response_code(401); //invalid user
	// 							}
	// 						}
	// 						else
	// 						{
	// 							echo "invalid";
	// 							http_response_code(401); //password not at >= 6 chars
	// 						}
	// 					}
	// 					else
	// 					{
	// 						echo "invalid";
	// 						http_response_code(401); //password wrong format
	// 					}
	// 				}
	// 				else
	// 				{
	// 					echo "invalid";
	// 					http_response_code(401); //user wrong format
	// 				}
	// 			}
	// 			else
	// 			{
	// 				echo "invalid";
	// 				http_response_code(401); //user or pw empty
	// 			}
	// 		}
	// 		else if ($action==='register')
	// 		{
	// 			$user=$data->user;	#TODO validate input   #TODO only register user if not user with same name exists!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	// 			$email=$data->email;
	// 			$password=$data->password;
	// 			$password2=$data->password2;
	// 			if (!empty($user) and !empty($email) and !empty($password) and !empty($password2))
	// 			{
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
  //
  //
	// 		}
	// 		else if ($action==='edit')
	// 		{
	// 			echo 'edit';
	// 			#TODO check if user logged in and has permission
	// 			#TODO edit data pdo stuff
	// 		}
	// 		else if ($action==='getusername')
	// 		{
	// 			#TODO if servertoken ok
	// 			#get username from db where id = $userid
	// 			$userid=$data->id;
	// 			if (!empty($userid))
	// 			{
	// 				if (is_valid('numeric', $userid)== true)
	// 				{
	// 					$statement = $pdo->prepare("SELECT user FROM user WHERE id = :userid");
	// 					if($statement->execute(array(':userid' => $userid)))
	// 					{
	// 						while($row = $statement->fetch())
	// 						{
	// 							$username=$row['user'];
	// 							echo $userid . "," . $username;
	// 						}
	// 					}
	// 				}
	// 			}
	// 		}
	 // 	}
	 // }
	 // else
	 // {
	 // 	http_response_code(405);
	 // }



?>
