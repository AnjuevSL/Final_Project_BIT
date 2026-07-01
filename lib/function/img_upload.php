<?php
include_once('main.php');

class ImageUpload extends Main{

    public function imgUpload($imageName, $imageType, $folderName, $tempName, $id){

        $customName = $id . "_" . $imageName;

        $path = __DIR__ . "/../upload/" . $folderName . "/" . $customName;

        $dbpath = "upload/" . $folderName . "/" . $customName;

        if(move_uploaded_file($tempName, $path)){
            return $dbpath;
        }else{
            die("Image upload failed");
        }
    }
}