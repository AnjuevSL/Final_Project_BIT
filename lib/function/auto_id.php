<?php
//include main.php
include_once('main.php');

//create class
class AutoNumber extends Main{

    //number genarate function
    function NumberGenaration($id, $table, $string){
        $currentID = "SELECT $id FROM $table ORDER BY $id DESC LIMIT 1;";

        if($this->dbResult->error){
            echo($this->dbResult->error);
            exit;
        }

        $sqlResult = $this->dbResult->query($currentID);

        $nor = $sqlResult->num_rows;

        if($nor > 0){
            $rec = $sqlResult->fetch_assoc();
            $prevID = $rec[$id];
            $num = substr($prevID, strlen($string));
            $num = intval($num) + 1;
            $id_padded = str_pad($num, 5, '0', STR_PAD_LEFT);
            $newID = $string . $id_padded;
        }else{
            $newID = $string . "00001";
        }

        return $newID;
    

    }

}
?>