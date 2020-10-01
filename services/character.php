<?php
################################################################################
# Character Class                                                              #
################################################################################
class Character {
    
    ## LIST ####################################################################
    public function list($data, $user_id) {
        $db = new DB();
        $pdo = $db->connect();
        //$user_id=;
        //$user_id =3;
        //echo $user_id;
        $stmt = $pdo->prepare('SELECT id, name FROM chars WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $user_id]);
        $payload = array();
        while ($row = $stmt->fetch()) {
            $payload[] = array( $row['id'], $row['name']); //TODO RICHTIG in payload inna formatiara json shit
        }
        return $payload;
    }

    ## GET #####################################################################
    public function get($data) {
        return "api todo get characterdata from db and return data";
    }

    ## ADD #####################################################################
    public function add($data) {
        return "api todo add character";
    }

    ## EDIT ####################################################################
    public function edit($data) {
        return "api todo edit chararcter";
    }

    ## DELETE ##################################################################
    public function delete($data) {
        return "api todo delete";
    }
}
?>
