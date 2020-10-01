<?php
################################################################################
# DB Class                                                              #
################################################################################
class DB {
    public function connect() {
        $dsn = 'mysql:host=localhost;dbname=api';
        $dbuser = 'master';
        $dbpassword = 'Yep_Das_Geht!_Bitch6';
        try {
            $pdo = new PDO($dsn, $dbuser, $dbpassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // prevent emulation of prepared statements
            $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            return $pdo;
        } catch (PDOException $e) {
            return 'DB connection failed: ' . $e->getMessage();
        }
    }
}
