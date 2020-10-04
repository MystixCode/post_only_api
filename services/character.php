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
    public function get($data) {
        return "api todo get characterdata from db and return data";
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
        return "api todo edit chararcter";
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
