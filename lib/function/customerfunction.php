<?php
include_once('main.php');

// include auto_id
include_once('auto_id.php');

class Customer extends Main
{

    public function addCustomer($email, $password, $name, $phone, $nic, $gender, $birthday)
    {

        // Check phone duplicate
        $checkPhone = $this->dbResult->prepare("SELECT customerPhone FROM customer_tbl WHERE customerPhone = ?");
        $checkPhone->bind_param("s", $phone);
        $checkPhone->execute();
        $checkPhone->store_result();

        if ($checkPhone->num_rows > 0) {
            $checkPhone->close();
            return "Phone number Exists";
        }
        $checkPhone->close();

        // Check email duplicate
        $checkEmail = $this->dbResult->prepare("SELECT customerEmail FROM customer_tbl WHERE customerEmail = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            $checkEmail->close();
            return "Email Exists";
        }
        $checkEmail->close();

        $autonumber = new AutoNumber;

        $id = $autonumber->NumberGenaration("customerid", "customer_tbl", "CUS");

        if ($this->dbResult->error) {
            echo ($this->dbResult->error);
            exit;
        }

        $sqlInsert = $this->dbResult->prepare("INSERT INTO customer_tbl VALUES (?, ?, ?, ?, ?, ?, ?, 0, Now())");
        $sqlInsert->bind_param("sssssss", $id, $email, $name, $phone, $nic, $gender, $birthday);

        if ($sqlInsert->execute()) {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sqlinsertlogin = $this->dbResult->prepare("
                INSERT INTO login_tbl (loginid, loginEmail, loginPassword, loginRole, loginStatus, d_status, created_at)
                VALUES (?, ?, ?, 'customer', 1, 0, NOW())
            ");
            $sqlinsertlogin->bind_param("sss", $id, $email, $hashedPassword);

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

    public function addAdmin($email, $password)
    {
        $checkEmail = $this->dbResult->prepare("SELECT loginEmail FROM login_tbl WHERE loginEmail = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            $checkEmail->close();
            return "Email Exists";
        }
        $checkEmail->close();

        $autonumber = new AutoNumber;
        $id = $autonumber->NumberGenaration("loginid", "login_tbl", "ADM");

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sqlinsertlogin = $this->dbResult->prepare("
        INSERT INTO login_tbl
        (loginid, loginEmail, loginPassword, loginRole, loginStatus, d_status, created_at)
        VALUES (?, ?, ?, 'admin', 1, 0, NOW())
        ");

        $sqlinsertlogin->bind_param("sss", $id, $email, $hashedPassword);

        if ($sqlinsertlogin->execute()) {
            $sqlinsertlogin->close();
            return "success";
        } else {
            $sqlinsertlogin->close();
            return "error";
        }
    }

    public function editAdmin($loginid, $email, $password = null)
    {
        $checkEmail = $this->dbResult->prepare("
                SELECT loginid
                FROM login_tbl
                WHERE loginEmail = ?
                AND loginid != ?
            ");

        $checkEmail->bind_param("ss", $email, $loginid);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            $checkEmail->close();
            return "Email Exists";
        }

        $checkEmail->close();

        if (!empty($password)) {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $updateAdmin = $this->dbResult->prepare("
            UPDATE login_tbl
            SET loginEmail = ?, loginPassword = ?
            WHERE loginid = ? AND loginRole = 'admin'
        ");

            $updateAdmin->bind_param("sss", $email, $hashedPassword, $loginid);
        } else {

            $updateAdmin = $this->dbResult->prepare("
            UPDATE login_tbl
            SET loginEmail = ?
            WHERE loginid = ? AND loginRole = 'admin'
        ");

            $updateAdmin->bind_param("ss", $email, $loginid);
        }

        if ($updateAdmin->execute()) {
            $updateAdmin->close();
            return "success";
        }

        $updateAdmin->close();
        return "error";
    }

    public function editCurrentAdmin($email, $password = null)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $loginid = $_SESSION['user'];

        // Check duplicate email
        $checkEmail = $this->dbResult->prepare("
                SELECT loginid
                FROM login_tbl
                WHERE loginEmail = ?
                AND loginid != ?
            ");

        $checkEmail->bind_param("ss", $email, $loginid);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            $checkEmail->close();
            return "Email Exists";
        }

        $checkEmail->close();

        if (!empty($password)) {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $updateAdmin = $this->dbResult->prepare("
            UPDATE login_tbl
            SET loginEmail = ?, loginPassword = ?
            WHERE loginid = ? AND loginRole = 'admin'
        ");

            $updateAdmin->bind_param("sss", $email, $hashedPassword, $loginid);
        } else {

            $updateAdmin = $this->dbResult->prepare("
            UPDATE login_tbl
            SET loginEmail = ?
            WHERE loginid = ? AND loginRole = 'admin'
        ");

            $updateAdmin->bind_param("ss", $email, $loginid);
        }

        if ($updateAdmin->execute()) {
            $updateAdmin->close();
            return "success";
        } else {
            $updateAdmin->close();
            return "error";
        }
    }

    public function loaddataadminbyid($id)
    {
        $sql = $this->dbResult->prepare("SELECT * FROM login_tbl WHERE loginid = ? AND loginRole = 'admin' AND d_status = 0");
        $sql->bind_param("s", $id);
        $sql->execute();

        $result = $sql->get_result();
        $nor = $result->num_rows;

        if ($nor > 0) {
            $rec = $result->fetch_assoc();
            return json_encode($rec);
        }
    }

    public function loaddataadmin()
    {
        $getquery = "
                SELECT *
                FROM login_tbl
                WHERE loginRole = 'admin'
                AND d_status = 0
                ";

        $sqlresult = $this->dbResult->query($getquery);

        $nor = $sqlresult->num_rows;

        if ($nor > 0) {
            while ($rec = $sqlresult->fetch_assoc()) {

                $status = ($rec['loginStatus'] == 1) ? "Active" : "Inactive";
                $btnClass = ($rec['loginStatus'] == 1) ? "btn-warning" : "btn-success";
                $btnText = ($rec['loginStatus'] == 1) ? "Deactivate" : "Activate";

                echo '
        <tr>
            <th>' . $rec['loginid'] . '</th>
            <td>' . $rec['loginEmail'] . '</td>
            <td>' . $rec['loginRole'] . '</td>
            <td>' . $status . '</td>
            <td>
                <button class="btn btn-sm btn-info"
                onclick="edituser(\'' . $rec['loginid'] . '\')">
                Edit
                </button>

                <button class="btn btn-sm ' . $btnClass . ' deactivatebtn"
                data-id="' . $rec['loginid'] . '"
                data-status="' . $status . '">
                ' . $btnText . '
                </button>

                <button class="btn btn-sm btn-danger deletebtn"
                data-id="' . $rec['loginid'] . '">
                Delete
                </button>
            </td>
        </tr>';
            }
        }

        return $nor;
    }

    public function loaddatacus()
    {
        $getquery = "
        SELECT * 
        FROM customer_tbl 
        JOIN login_tbl 
        ON customer_tbl.customerid = login_tbl.loginid 
        WHERE customer_tbl.d_status = 0
        AND login_tbl.loginRole = 'customer'
        ";

        $sqlresult = $this->dbResult->query($getquery);

        $nor = $sqlresult->num_rows;

        if ($nor > 0) {
            while ($rec = $sqlresult->fetch_assoc()) {

                $status = ($rec['loginStatus'] == 1) ? "Active" : "Inactive";
                $btnClass = ($rec['loginStatus'] == 1) ? "btn-warning" : "btn-success";
                $btnText = ($rec['loginStatus'] == 1) ? "Deactivate" : "Activate";

                echo '
        <tr>
            <th>' . $rec['customerid'] . '</th>
            <td>' . $rec['customerName'] . '</td>
            <td>' . $rec['customerEmail'] . '</td>
            <td>' . $rec['customerPhone'] . '</td>
            <td>' . $rec['customerNIC'] . '</td>
            <td>' . $status . '</td>
            <td>
                <button class="btn btn-sm btn-info"
                onclick="edituser(\'' . $rec['customerid'] . '\')">
                Edit
                </button>

                <button class="btn btn-sm ' . $btnClass . ' deactivatebtn"
                data-id="' . $rec['customerid'] . '"
                data-status="' . $status . '">
                ' . $btnText . '
                </button>

                <button class="btn btn-sm btn-danger deletebtn"
                data-id="' . $rec['customerid'] . '">
                Delete
                </button>
            </td>
        </tr>';
            }
        }

        return $nor;
    }

    public function loaddatasearch($text, $role)
    {
        $search = "%{$text}%";

        if ($role == "Admin") {

            $sql = $this->dbResult->prepare("
                SELECT *
                FROM login_tbl
                WHERE loginRole = ?
                AND (
                    loginid LIKE ?
                    OR loginEmail LIKE ?
                )
            ");

            $sql->bind_param("sss", $role, $search, $search);
        } else {

            $sql = $this->dbResult->prepare("
                SELECT *
                FROM customer_tbl
                INNER JOIN login_tbl
                    ON customer_tbl.customerid = login_tbl.loginid
                WHERE customer_tbl.d_status = 0
                AND login_tbl.loginRole = ?
                AND (
                    customer_tbl.customerName LIKE ?
                    OR customer_tbl.customerPhone LIKE ?
                    OR customer_tbl.customerEmail LIKE ?
                )
            ");

            $sql->bind_param("ssss", $role, $search, $search, $search);
        }

        $sql->execute();

        $result = $sql->get_result();

        while ($rec = $result->fetch_assoc()) {

            $status = ($rec['loginStatus'] == 1) ? "Active" : "Inactive";
            $btnClass = ($rec['loginStatus'] == 1) ? "btn-warning" : "btn-success";
            $btnText = ($rec['loginStatus'] == 1) ? "Deactivate" : "Activate";


            if ($role == "Admin") {

                echo '
                <tr>
                    <th>' . $rec['loginid'] . '</th>
                    <td>' . $rec['loginEmail'] . '</td>
                    <td>' . $rec['loginRole'] . '</td>
                    <td>' . $status . '</td>
                    <td>
                        <button type="button"
                            onclick="edituser(\'' . $rec['loginid'] . '\')"
                            class="btn btn-info">
                            Edit
                        </button>
    
                        <button type="button"
                            class="btn ' . $btnClass . ' deactivatebtn"
                            data-id="' . $rec['loginid'] . '">
                            ' . $btnText . '
                        </button>
    
                        <button type="button"
                            class="btn btn-danger deletebtn"
                            data-id="' . $rec['loginid'] . '">
                            Delete
                        </button>
                    </td>
                </tr>';
            } else {

                echo '
                <tr>
                    <th>' . $rec['customerid'] . '</th>
                    <th>' . $rec['customerName'] . '</th>
                    <td>' . $rec['customerEmail'] . '</td>
                    <td>' . $rec['customerPhone'] . '</td>
                    <td>' . $rec['customerNIC'] . '</td>
                    <td>' . $status . '</td>
                    <td>
                        <button type="button"
                            onclick="edituser(\'' . $rec['customerid'] . '\')"
                            class="btn btn-info">
                            Edit
                        </button>
    
                        <button type="button"
                            class="btn ' . $btnClass . ' deactivatebtn"
                            data-id="' . $rec['customerid'] . '">
                            ' . $btnText . '
                        </button>
    
                        <button type="button"
                            class="btn btn-danger deletebtn"
                            data-id="' . $rec['customerid'] . '">
                            Delete
                        </button>
                    </td>
                </tr>';
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
