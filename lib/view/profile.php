<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../index.php");
    exit();
}

require_once(__DIR__ . '/../function/main.php');

$obj = new Main();
$conn = $obj->dbResult;

$user_id = $_SESSION['user'];


// Get current user data
$sql = $conn->prepare("
    SELECT loginid, loginEmail, loginRole, created_at
    FROM login_tbl
    WHERE loginid = ?
");

$sql->bind_param("s", $user_id);
$sql->execute();

$user = $sql->get_result()->fetch_assoc();

$sql->close();


// Default profile image
$profile_img = '../../assets/woman.png';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile | Malee Dress Point</title>

    <?php
    include_once('common.php')
    ?>
    <!--begin::App Main-->
    <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">My Profile</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">My Profile</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!--end::App Content Header-->

        <!--begin::App Content-->
        <div class="app-content">
            <div class="container-fluid">

                <?php if (!empty($success_msg)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $success_msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo $error_msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!--begin::Profile Image Card-->
                    <div class="col-md-4">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile text-center">
                                <img class="profile-user-img img-fluid img-circle shadow mb-3"
                                    src="<?php echo htmlspecialchars($profile_img); ?>"
                                    alt="Profile Image"
                                    style="width:120px; height:120px; object-fit:cover;">

                                <h3 class="profile-username text-center">
                                    <?php echo htmlspecialchars($user['loginEmail'] ?? ''); ?>
                                </h3>

                                <p class="text-secondary text-center">
                                    <?php echo htmlspecialchars($user['loginRole'] ?? 'Admin'); ?>
                                </p>

                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Email</b> <span class="float-end"><?php echo htmlspecialchars($user['loginEmail'] ?? '-'); ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Role</b> <span class="float-end"><?php echo htmlspecialchars($user['loginRole'] ?? '-'); ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Member Since</b>
                                        <span class="float-end"><?php echo isset($user['created_at']) ? date('M Y', strtotime($user['created_at'])) : '-'; ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--end::Profile Image Card-->

                    <!--begin::Edit Forms-->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#profile-info" data-bs-toggle="tab">Profile Info</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#change-password" data-bs-toggle="tab">Change Password</a></li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">

                                    <!--begin::Profile Info Tab-->
                                    <div class="active tab-pane" id="profile-info">

                                        <div class="mb-3">

                                            <label class="form-label">Email</label>

                                            <input type="email"
                                                id="adminEmail"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($user['loginEmail']); ?>">

                                        </div>


                                        <button type="button"
                                            id="updateProfile"
                                            class="btn btn-primary">

                                            <i class="bi bi-check-circle me-1"></i>
                                            Save Changes

                                        </button>


                                    </div>
                                    <!--end::Profile Info Tab-->

                                    <!--begin::Change Password Tab-->
                                    <div class="tab-pane" id="change-password">


                                        <div class="mb-3">

                                            <label class="form-label">
                                                New Password
                                            </label>

                                            <input type="password"
                                                id="adminPassword"
                                                class="form-control"
                                                minlength="6">

                                        </div>


                                        <div class="mb-3">

                                            <label class="form-label">
                                                Confirm New Password
                                            </label>

                                            <input type="password"
                                                id="confirmPassword"
                                                class="form-control"
                                                minlength="6">

                                        </div>



                                        <button type="button"
                                            id="updatePassword"
                                            class="btn btn-warning">

                                            <i class="bi bi-key-fill me-1"></i>
                                            Change Password

                                        </button>


                                    </div>
                                    <!--end::Change Password Tab-->

                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Edit Forms-->
                </div>

            </div>
        </div>
        <!--end::App Content-->
    </main>
    <!--end::App Main-->

    <?php
    include_once('footer.php')
    ?>

    <script src="../../js/jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // =====================
        // Update Email
        // =====================

        $('#updateProfile').click(function() {


            $.ajax({

                url: '../routes/customer/editCurrentAdmin.php',

                type: 'POST',

                data: {


                    adminEmail: $('#adminEmail').val(),

                    adminPassword: ''

                },


                success: function(res) {


                    res = $.trim(res);


                    if (res == "success") {


                        Swal.fire({

                            icon: 'success',

                            title: 'Profile Updated',

                            didClose: function() {

                                document.activeElement.blur();

                                location.reload();

                            }

                        });


                    } else if (res == "Email Exists") {


                        Swal.fire({

                            icon: 'warning',

                            title: 'Email already exists'

                        });


                    } else {


                        Swal.fire({

                            icon: 'error',

                            title: 'Update Failed',

                            text: res

                        });


                    }


                },


                error: function() {

                    Swal.fire({

                        icon: 'error',

                        title: 'Server Error'

                    });

                }


            });


        });





        // =====================
        // Update Password
        // =====================


        $('#updatePassword').click(function() {


            let password = $('#adminPassword').val();

            let confirmPassword = $('#confirmPassword').val();



            if (password == "") {


                Swal.fire({

                    icon: 'warning',

                    title: 'Enter password'

                });

                return;

            }



            if (password.length < 6) {


                Swal.fire({

                    icon: 'warning',

                    title: 'Password minimum 6 characters'

                });

                return;

            }



            if (password !== confirmPassword) {


                Swal.fire({

                    icon: 'warning',

                    title: 'Passwords do not match'

                });

                return;

            }





            $.ajax({


                url: '../routes/customer/editCurrentAdmin.php',

                type: 'POST',


                data: {


                    adminEmail: $('#adminEmail').val(),

                    adminPassword: password


                },


                success: function(res) {


                    res = $.trim(res);



                    if (res == "success") {


                        Swal.fire({

                            icon: 'success',

                            title: 'Password Updated',

                            didClose: function() {

                                document.activeElement.blur();

                            }

                        });



                        $('#adminPassword').val('');

                        $('#confirmPassword').val('');


                    } else {


                        Swal.fire({

                            icon: 'error',

                            title: 'Update Failed',

                            text: res

                        });


                    }


                },


                error: function() {


                    Swal.fire({

                        icon: 'error',

                        title: 'Server Error'

                    });


                }


            });


        });
    </script>

    </body>
    <!--end::Body-->

</html>