<?php
################################################################################
# Character Class                                                              #
################################################################################
class Character {

    ## LIST ####################################################################
    public function list($data, $user_id) {
        $pdo = new DB();
        $pdo = $pdo->connect();
        $stmt = $pdo->prepare('SELECT id, name FROM chars WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $user_id]);
        $payload=array();
        $data = $stmt->fetchAll();
        foreach ($data as $row) {
            //create entry array
            $entry = array();
            $entry['character_id']=$row['id'];
            $entry['character_name']=$row['name'];
            $payload[]=$entry;
        }
        $response = json_encode($payload);
        return $response;
    }

    ## GET #####################################################################
    public function get($data, $user_id) {
        $character_id=$data->character_id;
        if (isset($user_id) && isset($character_id)) {
            if ((is_valid('numeric', $user_id) == true) && (is_valid('numeric', $character_id) == true)) {
                $pdo = new DB();
                $pdo = $pdo->connect();
                $stmt = $pdo->prepare('SELECT id, name FROM chars WHERE id = :character_id AND user_id = :user_id');
                $stmt->execute(array($character_id, $user_id));

                $payload = array();
                $result = $stmt->fetch();
                $payload['character_id']= $result['id'];
                $payload['character_name']= $result['name'];

                return  json_encode($payload);


                //return json_encode(array(character_name => $chars, message => 'DONE TODO..'));
            }
        }

        return json_encode(array(character_name => 'error', message => 'TODO API character>get'));


    }

    ## Create ##################################################################
    public function create($data, $user_id) {
        $name=$data->name;
        if (!empty($name)){
            if (is_valid('alphanumeric_s3', $name) == true) {
                $db = new DB();
                $pdo = $db->connect();
                $stmt = $pdo->prepare('INSERT INTO chars (user_id, name) VALUES (?, ?)');
                $stmt->execute(array($user_id, $name));

                return json_encode("done  todo api errorhandling");
            }
        }
    }

    ## EDIT ####################################################################
    public function edit($data, $user_id) {
        //TODO if user_id correct  for character_id


        $character_id=$data->character_id;
        $character_name=$data->character_name;
        if (isset($user_id) && isset($character_id) && isset($character_name) ) {
            if ((is_valid('numeric', $user_id) == true) && (is_valid('numeric', $character_id) == true) && (is_valid('alphanumeric_s3', $character_name) == true) ) {
                $pdo = new DB();
                $pdo = $pdo->connect();
                $stmt = $pdo->prepare('UPDATE chars SET name = :character_name WHERE id = :character_id AND user_id = :user_id');
                $stmt->execute(array($character_name, $character_id, $user_id));
                return json_encode(array(character_name => 'testcharname', message => 'DONE TODO..'));
            }
        }
        return json_encode("api error character>edit");
    }

    ## DELETE ##################################################################
    public function delete($data, $user_id) {
        $character_id=$data->character_id;
        if (isset($character_id)) {
            if (is_valid('numeric', $character_id) == true) {
                $pdo = new DB();
                $pdo = $pdo->connect();
                $stmt = $pdo->prepare('DELETE FROM chars WHERE id  = :character_id AND user_id = :user_id');
                $stmt->execute(array($character_id, $user_id));
                $response = json_encode(array(message => 'done TODO: errorhandling'));
                return $response;
            }
        }
    }
}
?>
