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

                $newpassword = md5($password);

                $row = $sqlresult->fetch_assoc();
                $dbpassword = $row['loginPassword'];

                if ($newpassword == $dbpassword) {

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
                                    'path' => "lib/view/index.php"
                                ]);

                            case ("customer"):
                                $userId = $row['loginId'];
                                $_SESSION['user'] = $userId;
                                $_SESSION['usertype'] = 'Customer';

                                return json_encode([
                                    'loginstatus' => true,
                                    'message' => "logged as Customer",
                                    'path' => "lib/view/index2.php"
                                ]);
                        }
                    } else {
                        return json_encode([
                            'status' => true,
                            'message' => "Account id Deactivated"
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
                'status' => false,
                'message' => 'fill all inputs!'
            ]);
        }
    }
     public function authentication2($email, $password)
    {
        if ($email != "" && $password != "") {
            $auth = $this->dbResult->prepare("SELECT loginPassword, loginStatus, loginRole, loginId FROM login_tbl WHERE loginEmail = ?");
            $auth->bind_param("s", $email);
            $auth->execute();
            $sqlresult = $auth->get_result();
            $nor = $sqlresult->num_rows;

            if ($nor > 0) {

                $newpassword = md5($password);

                $row = $sqlresult->fetch_assoc();
                $dbpassword = $row['loginPassword'];

                if ($newpassword == $dbpassword) {

                    $loginstatus = $row['loginStatus'];

                    if ($loginstatus == 1) {

                        $loginrole = $row['loginRole'];
                        switch ($loginrole) {
                            case "admin":
                                $userId = $row['loginId'];
                                $_SESSION['user'] = $userId;
                                $_SESSION['usertype'] = 'Admin';

                                header('Location:../../index2.php');
                                return json_encode([
                                    'loginstatus' => true,
                                    'message' => "logged as Admin",
                                    'path' => "lib/view/index.php"
                                ]);

                            case ("customer"):
                                $userId = $row['loginId'];
                                $_SESSION['user'] = $userId;
                                $_SESSION['usertype'] = 'Customer';

                                return json_encode([
                                    'loginstatus' => true,
                                    'message' => "logged as Customer",
                                    'path' => "lib/view/index2.php"
                                ]);
                        }
                    } else {
                        return json_encode([
                            'status' => true,
                            'message' => "Account id Deactivated"
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
                'status' => false,
                'message' => 'fill all inputs!'
            ]);
        }
    }
}
