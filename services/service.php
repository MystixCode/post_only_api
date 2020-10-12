<?php
################################################################################
# Service Class                                                              #
################################################################################
class Service {

    ## LIST ####################################################################
    public function list($data, $user_id) {
        return array(message => 'TODO service list');
    }

    ## GET #####################################################################
    public function get($data, $user_id) {
        return array(message => 'TODO service get');
    }

    ## Add ##################################################################
    public function add($data, $user_id) {
        $service_name=$data->service_name;
        $service_actions=$data->service_actions;
        if ($service_name) {
            if (is_valid('alphanumeric', $service_name) == true) {
                $page_content = "
<?php
################################################################################
# $service_name Class
################################################################################
class $service_name {

}
?>";
                //Create servicename.php File
                $file=strtolower($service_name) . '.php';
                $file_path = '/var/www/api/services/' . $file;
                if(!file_exists($file_path)){
                    mkdir(dirname($file_path), 0777, true); //TODO correct permissionis cant delete folders atm
                    if ($service_actions){
                        $pdo = new DB();
                        $pdo = $pdo->connect();
                        foreach ($service_actions as $key => $action) {
                            if (is_valid('alphanumeric', $action) == true){
                                try
                                {
                                    //add each action to permission table
                                    $permission_name = $service_name . '_' . $action;
                                     $stmt = $pdo->prepare('INSERT INTO permission (name) VALUES (:permission_name);');
                                     $stmt->execute(['permission_name' => $permission_name]);

                                     //add admin role permission to action
                                     $role_id=1;
                                     $permission_id=$pdo->lastInsertId();
                                     $stmt = $pdo->prepare('INSERT INTO role_permission (role_id, permission_id) VALUES (:role_id, :permission_id);');
                                     $stmt->execute(['role_id' => $role_id, 'permission_id' => $permission_id]);
                                }
                                catch(PDOException $e)
                                {
                                    return $e->getMessage();
                                }
                                $insert = "
    ## $action ####################################################################
    public function $action(\$data, \$user_id) {
        return array(message => 'TODO api $action');
    }";
                                $page_content = substr_replace( $page_content, $insert, (strlen($page_content) - 5), 0 );
                            }
                        }
                    }
                    if (file_put_contents($file_path, $page_content) !== false)
                    {
                            return array(message => 'Created file: ' . $file_path);
                    }
                }
            }
        }
        return array(message => 'TODO service add todo error');
    }

    ## Add Action to service ###################################################
    public function addAction($data, $user_id) {
        $action_name=$data->action_name;
        $service_id=$data->service_id;
        if ($action_name && $service_id) {

            //do stuff
            //create action in service

            return array(message => 'TODO API service>add');
        }
        return array(message => 'TODO service add todo error');
    }

    ## EDIT ####################################################################
    public function edit($data, $user_id) {
        return array(message => 'TODO service edit');
    }

    ## DELETE ##################################################################
    public function delete($data, $user_id) {
        return array(message => 'TODO service delete');
    }
}
?>
