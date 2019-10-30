<?php
class DB
{

    protected $conn = null;


    public function Connect()
    {
        try {

            $dsn = "mysql:dbname=".getenv('DB_DATABASE')."; host=".getenv('DB_HOST')."";
            $user = getenv('DB_USERNAME');
            $password = getenv('DB_PASSWORD');

            $options  = array(PDO::ATTR_ERRMODE =>      PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            );

            $this->conn = new PDO($dsn, $user, $password, $options);
            return $this->conn;

        } catch (PDOException $e) {
            echo 'Connection error : ' . $e->getMessage();
        }
    }

    public function Close()
    {
        $this->conn = null;
    }
}