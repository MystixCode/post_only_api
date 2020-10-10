<?php
################################################################################
# Character Class                                                              #
################################################################################
class Character {
    private $pdo;

    function __construct() {
        $db = new DB();
        $this->pdo = $db->connect();
    }

    ## LIST ####################################################################
    public function list($data, $user_id) {
        $stmt = $this->pdo->prepare('SELECT id, name FROM chars WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $user_id]);
        $payload=array();
        $data = $stmt->fetchAll();
        foreach ($data as $row) {
            //create entry array
            if ($row['id'] !== null AND $row['name'] !== null ){
                $entry = array();
                $entry['character_id']=$row['id'];
                $entry['character_name']=$row['name'];
                $payload[]=$entry;
            }
        }
        return $payload;
    }

    ## GET #####################################################################
    public function get($data, $user_id) {
        $character_id=$data->character_id;
        if (isset($user_id) && isset($character_id)) {
            if ((is_valid('numeric', $user_id) == true) && (is_valid('numeric', $character_id) == true)) {
                $stmt = $this->pdo->prepare('SELECT id, name FROM chars WHERE id = :character_id AND user_id = :user_id');
                $stmt->execute(array($character_id, $user_id));
                $payload = array();
                $result = $stmt->fetch();
                $payload['character_id']= $result['id'];
                $payload['character_name']= $result['name'];
                return  $payload;
            }
        }
        return array(character_name => 'error', message => 'TODO API character>get');
    }

    ## Create ##################################################################
    public function create($data, $user_id) {
        $name=$data->name;
        if (!empty($name)){
            if (is_valid('alphanumeric_s3', $name) == true) {
                $stmt = $this->pdo->prepare('INSERT INTO chars (user_id, name) VALUES (?, ?)');
                $stmt->execute(array($user_id, $name));
                return "done  todo api errorhandling";
            }
        }
    }

    ## EDIT ####################################################################
    public function edit($data, $user_id) {
        $character_id=$data->character_id;
        $character_name=$data->character_name;
        if (isset($user_id) && isset($character_id) && isset($character_name) ) {
            if ((is_valid('numeric', $user_id) == true) && (is_valid('numeric', $character_id) == true) && (is_valid('alphanumeric_s3', $character_name) == true) ) {
                $stmt = $this->pdo->prepare('UPDATE chars SET name = :character_name WHERE id = :character_id AND user_id = :user_id');
                $stmt->execute(array($character_name, $character_id, $user_id));
                return array(message => 'API DONE TODO..');
            }
        }
        return "api error character>edit";
    }

    ## DELETE ##################################################################
    public function delete($data, $user_id) {
        $character_id=$data->character_id;
        if (isset($character_id)) {
            if (is_valid('numeric', $character_id) == true) {
                $stmt = $this->pdo->prepare('DELETE FROM chars WHERE id  = :character_id AND user_id = :user_id');
                $stmt->execute(array($character_id, $user_id));
                return array(message => 'done TODO: errorhandling');
            }
        }
    }
}
?>
