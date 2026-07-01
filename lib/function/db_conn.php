<?php
//database connection class
class Connection{

    //create the privet variable
    private $server;
    private $user;
    private $password;
    private $database;

    //call the constructor
    public function __construct($server,$user,$password,$database){
        $this->server = $server;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
    }

    //create connection method and execute sql query
    public function Conn(){
        $conn = new mysqli($this->server, $this->user, $this->password, $this->database);
        $result = (!$conn)?null:$conn;
        return($result);
    }
}
?>