<?php
include_once('main.php');
include_once('auto_id.php');
include_once('img_upload.php');

class Category extends Main
{

    // CREATE — add a new category
    public function addcategory($categoryName, $categoryimageName, $categoryimageSize, $categoryimageType, $categoryimageLocation, $description)
    {
        $autonumber = new AutoNumber;
        $id = $autonumber->NumberGenaration("categoryid", "categories_tbl", "CAT");

        $imageupload = new ImageUpload;
        $imageurl = $imageupload->imgUpload($categoryimageName, $categoryimageType, 'category', $categoryimageLocation, $id);


        $sql = $this->dbResult->prepare("
            INSERT INTO categories_tbl
            (categoryid, categoryName, description, image, d_status)
            VALUES (?, ?, ?, ?, 1)
        ");

        $sql->bind_param(
            "ssss",
            $id,
            $categoryName,
            $description,
            $imageurl
        );

        if ($sql->execute()) {
            return "success";
        } else {
            return "error";
        }
    }

    // UPDATE — edit an existing category
    public function updatecategory($categoryId, $categoryName, $description, $categoryimageName = null, $categoryimageType = null, $categoryimageLocation = null)
    {
        if (!empty($categoryimageName)) {
            $imageupload = new ImageUpload;
            $imageurl = $imageupload->imgUpload($categoryimageName, $categoryimageType, 'category', $categoryimageLocation, $categoryId);

            $sql = $this->dbResult->prepare(
                "UPDATE categories_tbl
                 SET categoryName = ?, description = ?, image = ?
                 WHERE categoryid = ?"
            );
            $sql->bind_param("ssss", $categoryName, $description, $imageurl, $categoryId);
        } else {
            $sql = $this->dbResult->prepare(
                "UPDATE categories_tbl
                 SET categoryName = ?, description = ?
                 WHERE categoryid = ?"
            );
            $sql->bind_param("sss", $categoryName, $description, $categoryId);
        }

        if ($sql->execute()) {
            return ("success");
        } else {
            return ("error2");
        }
        // $sql = $this->dbResult->prepare(
        //     "UPDATE categories_tbl
        //      SET categoryName = ?, description = ?
        //      WHERE categoryid = ?"
        // );
        // $sql->bind_param("sss", $categoryName, $description, $categoryId);

        // if ($sql->execute()) {
        //     return "success";
        // } else {
        //     return "error";
        // }
    }

    // TOGGLE STATUS — activate or deactivate
    public function toggleStatus($categoryId, $status)
    {
        $sql = $this->dbResult->prepare("UPDATE categories_tbl SET d_status = ? WHERE categoryid = ?");
        $sql->bind_param("is", $status, $categoryId);

        if ($sql->execute()) {
            return "success";
        } else {
            return "error";
        }
    }

    // DELETE — hard delete
    public function deletecategory($categoryId)
    {
        $sql = $this->dbResult->prepare("DELETE FROM categories_tbl WHERE categoryid = ?");
        $sql->bind_param("s", $categoryId);

        if ($sql->execute()) {
            return "success";
        } else {
            return "error";
        }
    }
    // Deactivate option
    public function deactivatecategory($categoryId)
    {
        $sql = $this->dbResult->prepare("UPDATE category_tbl SET d_status = 0 WHERE categoryid = ?");
        $sql->bind_param("s", $categoryId);

        if ($sql->execute()) {
            return ("success");
        } else {
            return ("error2");
        }
    }


    // READ — all categories
    public function getAllCategories()
    {
        $sql = $this->dbResult->prepare(
            "SELECT *
             FROM categories_tbl
             ORDER BY categoryid DESC"
        );
        $sql->execute();
        $result = $sql->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // READ — active categories only
    public function getActiveCategories()
    {
        $sql = $this->dbResult->prepare(
            "SELECT categoryid, categoryName
             FROM categories_tbl
             WHERE d_status = 1
             ORDER BY categoryName ASC"
        );
        $sql->execute();
        $result = $sql->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // READ — single category
    public function getCategoryById($categoryId)
    {
        $sql = $this->dbResult->prepare(
            "SELECT categoryid, categoryName, description, d_status
             FROM categories_tbl
             WHERE categoryid = ?"
        );
        $sql->bind_param("s", $categoryId);
        $sql->execute();
        $result = $sql->get_result();

        return $result->fetch_assoc();
    }
}
