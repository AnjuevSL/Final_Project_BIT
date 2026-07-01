<?php
//include the db_conn.php
include_once('db_conn.php');

//cerate main class
class Main{
    public Connection $connObj;
    public mysqli $dbResult;
    public function __construct(){
        $this->connObj = new Connection("localhost","root","","bd_projectdb");

        $this->dbResult = $this->connObj->Conn();

        return($this->dbResult);
    }
}
?>