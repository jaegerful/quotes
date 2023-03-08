<?php
    class DatabaseClient {

        public $connection = null;

        /* establish connection in constructor. */

        public function __construct() {

            try {
                $dsn = "pgsql:host=" . getenv('host') . ";port=" . getenv('port') . ";dbname=" . getenv('database');
                $this->connection = new PDO($dsn, getenv("user"), getenv("password"));

                /* make errors from queries visible.  */
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                /* get results from database as associative arrays. */
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            }
            catch(PDOException $error) {
                echo nl2br("error establishing connection with database: {$error->getMessage()}");
            }
        }

    }
?>