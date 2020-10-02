<?php
################################################################################
# DB Class                                                              #
################################################################################
class DB {

    public function connect() {

        try {
            $conf = parse_ini_file('conf.ini');
            $pdo = new PDO($conf['db_dsn'], $conf['db_user'], $conf['db_password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // prevent emulation of prepared statements
            $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            return $pdo;
        } catch (PDOException $e) {
            return 'DB connection failed: ' . $e->getMessage();
        }
    }
}
