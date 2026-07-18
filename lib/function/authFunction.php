<?php
session_start();

//include main.php
include_once('main.php');

//include auto_id
include_once('auto_id.php');

class Auth extends Main
{

    public function authentication($email, $password)
    {
        if ($email != "" && $password != "") {
            $auth = $this->dbResult->prepare("SELECT loginPassword, loginStatus, loginRole, loginId FROM login_tbl WHERE loginEmail = ? AND d_status = 0");
            $auth->bind_param("s", $email);
            $auth->execute();
            $sqlresult = $auth->get_result();
            $nor = $sqlresult->num_rows;

            if ($nor > 0) {

                $row = $sqlresult->fetch_assoc();
                $dbpassword = $row['loginPassword'];

                if (password_verify($password, $dbpassword)) {

                    $loginstatus = $row['loginStatus'];

                    if ($loginstatus == 1) {

                        $loginrole = $row['loginRole'];
                        switch ($loginrole) {
                            case "admin":
                                $userId = $row['loginId'];
                                $_SESSION['user'] = $userId;
                                $_SESSION['usertype'] = 'Admin';
                                return json_encode([
                                    'loginstatus' => true,
                                    'message' => "logged as Admin",
                                    'path' => "lib/view/dashboard.php"
                                ]);

                            case ("customer"):
                                $userId = $row['loginId'];
                                $_SESSION['user'] = $userId;
                                $_SESSION['usertype'] = 'Customer';

                                return json_encode([
                                    'loginstatus' => true,
                                    'message' => "logged as Customer",
                                    'path' => "shop.php"
                                ]);
                        }
                    } else {
                        return json_encode([
                            'loginstatus' => false,
                            'message' => "Account is Deactivated"
                        ]);
                    }
                } else {

                    return json_encode([
                        'loginstatus' => false,
                        'message' => "Wrong Password!"
                    ]);
                }
            } else {
                return json_encode([
                    'loginstatus' => false,
                    'message' => 'wrong Email!'
                ]);
            }
        } else {
            return json_encode([
                'loginstatus' => false,
                'message' => 'fill all inputs!'
            ]);
        }
    }
}