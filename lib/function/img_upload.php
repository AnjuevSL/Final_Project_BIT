<?php
include_once('main.php');

class ImageUpload extends Main
{

    public function imgUpload($imageName, $imageType, $folderName, $tempName, $id)
    {

        $customName = $id . "_" . $imageName;

        $path = __DIR__ . "/../upload/" . $folderName . "/" . $customName;

        // Store the path relative to the project root (lib/upload/...) so it
        // resolves correctly both from root-level pages (login.php) and from
        // admin pages nested under lib/view/ (with the appropriate ../ prefix)
        $dbpath = "lib/upload/" . $folderName . "/" . $customName;

        if (move_uploaded_file($tempName, $path)) {
            return $dbpath;
        } else {
            die("Image upload failed");
        }
    }
}
