<?php

namespace Setting\Bundle\ToolBundle\Service;

class DatabaseArchive {

    private $archiveservername = "127.0.0.1";
    private $archiveusername = "root";
    private $archivepassword = "dhaka123";
    private $archivedbname = "alexabd";


    public function backupDatabaseConnection() {

        $servername = "127.0.0.1";
        $username = "root";
        $password = "dhaka123";

        try {
            $conn = new \PDO("mysql:host=$this->archiveservername;dbname=$this->archivedbname",  $this->archiveusername, $this->archivepassword);
            // set the PDO error mode to exception
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $conn;
        }
        catch(\PDOException $e)
        {
            return "failed";
        }
    }

    public function insertSalesData()
    {
        $conn = $this->backupDatabaseConnection();
        $stmt = $conn->prepare("SELECT id, amount FROM account_cash WHERE globalOption_id=96 LIMIT 0,10");
        $result = $stmt->execute();
        foreach($result->fetchAll() as $row) {
            echo $row['id'];
        }
        $conn->close();

    }

}