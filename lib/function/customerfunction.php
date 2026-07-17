<?php
include_once('main.php');

// include auto_id
include_once('auto_id.php');

class Customer extends Main
{

    public function addCustomer($email, $password, $name, $phone, $nic, $gender, $birthday)
    {

        $checkPhone = $this->dbResult->prepare("SELECT customerPhone FROM customer_tbl WHERE customerPhone = ?");
        $checkPhone->bind_param("s", $phone);
        $checkPhone->execute();
        $checkPhone->store_result();


        $autonumber = new AutoNumber;

        $id = $autonumber->NumberGenaration("customerid", "customer_tbl", "CUS");

        if ($this->dbResult->error) {
            echo ($this->dbResult->error);
            exit;
        }

        $sqlInsert = $this->dbResult->prepare("INSERT INTO customer_tbl VALUES (?, ?, ?, ?, ?, ?, ?, 0, Now())");
        $sqlInsert->bind_param("sssssss", $id, $email, $name, $phone, $nic, $gender, $birthday);

        if ($sqlInsert->execute()) {
            $newpassword = md5($password);
            $sqlinsertlogin = $this->dbResult->prepare("INSERT INTO login_tbl VALUES (?, ?, ?, 'customer', 1, 0, NOW())");
            $sqlinsertlogin->bind_param("sss", $id, $email, $password);

            if ($sqlinsertlogin->execute()) {
                return "success";
            } else {
                return "error2";
            }
        } else {
            return "error";
        }

        $sqlInsert->close();
    }



    public function editCustomer($userid, $email, $name, $phone, $nic, $gender, $birthday)
    {

        $checkPhone = $this->dbResult->prepare("SELECT customerPhone FROM customer_tbl WHERE customerPhone = ? AND customerid != ?");
        $checkPhone->bind_param("ss", $phone, $userid);
        $checkPhone->execute();
        $checkPhone->store_result();


        if ($checkPhone->num_rows > 0) {
            $checkPhone->close();
            return ("Phone number Exists");
        } else {
            if ($this->dbResult->error) {
                echo ($this->dbResult->error);
                exit;
            }
        }
        $updateCustomer = $this->dbResult->prepare("UPDATE customer_tbl JOIN login_tbl ON customer_tbl.customerid = login_tbl.loginid 
        SET customerEmail = ?, loginEmail = ?, 
            customerName = ?, customerPhone = ?, customerNIC = ?, customerGender = ?, customerBirthday = ? WHERE customerid = ?");

        $updateCustomer->bind_param("ssssssss", $email, $email, $name, $phone, $nic, $gender, $birthday, $userid);

        if (!$updateCustomer->execute()) {
            return ("error");
        } else {
            return ("success");
        }

        $updateCustomer->close();

        $sqlInsert->close();
    }

    /* public function loaddata(){
    $getempdata = $this->dbResult->prepare("SELECT customerEmail , customerName, customerPhone FROM customer_tbl WHERE d_status = 0;");

    $getempdata->execute();
    $getempdata->store_result();

    $getempdata->bind_result($email, $name, $phone);


    while($getempdata->fetch()){
        echo ('<tr class="table-success">
            <th scope="row">'.$name.'</th>
            <td>'.$email.'</td>
            <td>'.$phone.'</td>
            <td></td>
        </tr>');
    }

}
 */
    public function loaddata()
    {

        $getqueru = "SELECT * FROM customer_tbl JOIN login_tbl ON customer_tbl.customerid = login_tbl.loginid WHERE customer_tbl.d_status = 0;";

        if ($this->dbResult->error) {
            echo ($this->dbResult->error);
            exit;
        }

        $sqlresult = $this->dbResult->query($getqueru);

        $nor = $sqlresult->num_rows;

        if ($nor > 0) {
            while ($rec = $sqlresult->fetch_assoc()) {
                $btn = "";

                if ($rec['loginStatus'] == '1') {
                    $btn = '<button type="button" class="btn btn-sm btn-warning deactivatebtn" data-id="' . $rec['customerid'] . '" data-status="Active">Deactivate</button>';
                } else if ($rec['loginStatus'] == '0') {
                    $btn = '<button type="button" class="btn btn-sm btn-success deactivatebtn" data-id="' . $rec['customerid'] . '" data-status="Deactive">Activate</button>';
                } else {
                    $btn = "";
                }
                echo ('<tr class="">
                <th scope="row">' . $rec['customerName'] . '</th>
                <td>' . $rec['customerEmail'] . '</td>
                <td>' . $rec['customerPhone'] . '</td>
                <td>' . $rec['customerNIC'] . '</td>
                <td class="my-0 py-0"><button type="button" data-bs-toggle="modal" data-bs-target="#staticBackdrop"  onclick="edituser(\'' . $rec['customerid'] . '\' )" class="btn btn-sm btn-info">Edit</button> 
                <button type="button" class="btn btn-sm btn-danger deletebtn" data-id="' . $rec['customerid'] . '">Delete</button>
                ' . $btn . '</td>
            </tr>');
            }
        }

        return ($nor);
    }

    public function loaddatasearch($text)
    {

        $getsearchdataquery = "SELECT * FROM customer_tbl WHERE d_status = 0 AND (customerName LIKE '%$text%' OR customerPhone LIKE '%$text%')";

        if ($this->dbResult->error) {
            echo ($this->dbResult->error);
            exit;
        }

        $sqlresult = $this->dbResult->query($getsearchdataquery);

        $nor = $sqlresult->num_rows;

        if ($nor > 0) {
            while ($rec = $sqlresult->fetch_assoc()) {
                echo ('<tr class="table-success">
                <th scope="row">' . $rec['customerName'] . '</th>
                <td>' . $rec['customerEmail'] . '</td>
                <td>' . $rec['customerPhone'] . '</td>
                <td>' . $rec['customerNIC'] . '</td>
                <td class="my-0 py-0"><button type="button" onclick="edituser(\'' . $rec['customerid'] . '\' )" class="btn btn-warning">Edit</button> 
                <button type="button" class="btn btn-danger">Delete</button></td>
            </tr>');
            }
        }
    }

    public function loaddatabyid($id)
    {

        $usedetails = "SELECT * FROM customer_tbl WHERE d_status = 0 AND customerId = '$id';";

        if ($this->dbResult->error) {
            echo ($this->dbResult->error);
            exit;
        }

        $sqlresult = $this->dbResult->query($usedetails);

        $nor = $sqlresult->num_rows;

        if ($nor > 0) {
            $rec = $sqlresult->fetch_assoc();

            return json_encode($rec);
        }
    }

    public function deletedatabyid($id)
    {
        if ($this->dbResult->error) {
            echo ($this->dbResult->error);
            exit;
        }

        $deletecustomer = $this->dbResult->prepare("UPDATE customer_tbl JOIN login_tbl ON customer_tbl.customerid = login_tbl.loginid SET customer_tbl.d_status = 1, login_tbl.d_status = 1 WHERE customerid = ?");

        $deletecustomer->bind_param("s", $id);

        if (!$deletecustomer->execute()) {
            return ("error");
        } else {
            return ("success");
        }

        $deletecustomer->close();

        $sqlinsert->close();
    }
    public function deactivatebyid($id)
    {
        if ($this->dbResult->error) {
            echo ($this->dbResult->error);
            exit;
        }

        $deletecustomer = $this->dbResult->prepare("UPDATE login_tbl SET loginStatus = IF(loginStatus = 1, 0, 1) WHERE loginid = ?");

        $deletecustomer->bind_param("s", $id);

        if (!$deletecustomer->execute()) {
            return ("error");
        } else {
            return ("success");
        }

        $deletecustomer->close();

        $sqlinsert->close();
    }

    public function getCustomerOrders($customerId)
    {
        $sql = $this->dbResult->prepare("
            SELECT *
            FROM orders_tbl
            WHERE customer_id = ?
            ORDER BY created_at DESC
        ");

        $sql->bind_param("s", $customerId);
        $sql->execute();

        return $sql->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
