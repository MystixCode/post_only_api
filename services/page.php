<?php
################################################################################
# Character Class                                                              #
################################################################################
class Page {

    ## LIST ####################################################################
    public function list($data, $user_id) {
        return array(message => 'TODO page list');
    }

    ## GET #####################################################################
    public function get($data, $user_id) {
        return array(message => 'TODO page get');
    }

    ## Create ##################################################################
    public function add($data, $user_id) {
        $page_name=$data->page_name;
        $page_path=$data->page_path; //    /user/edit
        $page_content=$data->page_content;
         if ($page_name && $page_path) {
             $file=$page_path . '.html';
             $file_path = '/var/www/client/pages' . $file;

             $file=$page_path . '.html';
             if(!file_exists($file_path)){
                 mkdir(dirname($file_path), 0777, true); //TODO correct permissionis cant delete folders atm
                        // Some simple example content.
                 if (file_put_contents($file_path, $page_content) !== false)
                 {
                     return array(message => 'Created file: ' . $file);
                 }
             }
            return array(message => 'TODO: page_name=' . $page_name . ' page_path='  . $page_path);
        }
        return array(message => 'TODO page add todo error');
    }

    ## EDIT ####################################################################
    public function edit($data, $user_id) {
        return array(message => 'TODO page edit');
    }

    ## DELETE ##################################################################
    public function delete($data, $user_id) {
        return array(message => 'TODO page delete');
    }
}
?>
