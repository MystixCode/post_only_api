<?php
################################################################################
# Character Class                                                              #
################################################################################
class Character {
  public function list($data, $user_id)
  {
    $db = new DB();
    $pdo = $db->connect();

    //$user_id=;
    //$user_id =3;
    //echo $user_id;

    $stmt = $pdo->prepare('SELECT id, name FROM chars WHERE user_id = :user_id');
    $stmt->execute(['user_id' => $user_id]);
    $payload = array();
    while ($row = $stmt->fetch())
    {
        $payload[] = array( $row['id'], $row['name']); //TODO RICHTIG in payload inna formatiara json shit
    }
    return $payload;
  }

  public function get($data)
  {
    //TODO verify token
    return "api todo get characterdata from db and return data";
  }

  public function add($data)
  {
    //TODO verify token
    return "api todo add character";
  }

  public function edit($data)
  {
    //TODO verify token
    return "api todo edit chararcter";
  }

  public function delete($data)
  {
    //TODO verify token
    return "api todo delete";
  }

}
?>
