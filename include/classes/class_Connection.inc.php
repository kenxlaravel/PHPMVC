<?php

class Connection {

    private $dbh;

    public static function getHandle() {

        global $dbh;

        if ( !($dbh instanceof PDO) ) {
            $connection = new self();
            $dbh = $connection->PDO_Connection();
        }

        return $dbh;
    }

    function DB_Connection() {
        $Var_mysql_connection = mysql_connect(db_host, db_user, db_pass);
        mysql_set_charset('utf8', $Var_mysql_connection);
        mysql_select_db(db);
        echo mysql_error();
    }

    function PDO_Connection() {

        //Check if we already have a PDO instance before creating a new one
        if ( !$this->dbh instanceof PDO && defined('PDO::ATTR_DRIVER_NAME') ) {
            try {
                $dsn = 'mysql:dbname=' . db . ';host=' . db_host;
                $options = array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8, SESSION group_concat_max_len=15000'
                );
                $this->dbh = new PDO($dsn, db_user, db_pass, $options);

                $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            } catch (PDOException $e) {
                $error = $e->getMessage();
                echo $error;
                die();
            }
        }
        return $this->dbh;
    }

}
