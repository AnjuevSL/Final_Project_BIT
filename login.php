<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/fontawesome-free-7.1.0-web/css/all.min.css">
    <link rel="stylesheet" href="css/responsive.css">

    <style>
        :root {
            --brand: #BF9264;
            --brand-dark: #9c7345;
            --ink: #2b2622;
            --muted: #8c8378;
            --line: #e8e2d9;
        }

        body {
            background: radial-gradient(circle at 50% 0%, #fdfbf8 0%, #f3efe8 55%, #ede6d9 100%);
            color: var(--ink);
        }

        .auth-wrap {
            max-width: 460px;
            margin: 48px auto 70px;
            padding: 0 16px;
        }

        .auth-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 1px 2px rgba(43, 38, 34, 0.04), 0 18px 40px -12px rgba(43, 38, 34, 0.16);
            overflow: hidden;
            border: 1px solid rgba(191, 146, 100, 0.12);
        }

        /* ---- Header ---- */
        .auth-card-header {
            text-align: center;
            padding: 34px 24px 22px;
        }

        .auth-brand-mark {
            width: 46px;
            height: 46px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.1rem;
            box-shadow: 0 6px 14px -4px rgba(191, 146, 100, 0.55);
        }

        .auth-card-header h2 {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            margin-bottom: 6px;
        }

        .auth-card-header p {
            color: var(--muted);
            font-size: 0.87rem;
            margin-bottom: 0;
        }

        /* ---- Tabs (segmented control) ---- */
        .auth-tabs {
            display: flex;
            margin: 4px 24px 0;
            background: #f4f0e9;
            border-radius: 11px;
            padding: 4px;
            gap: 4px;
        }

        .auth-tabs .nav-link {
            flex: 1;
            text-align: center;
            border: none;
            border-radius: 8px;
            color: var(--muted);
            font-weight: 600;
            font-size: 0.88rem;
            letter-spacing: 0.02em;
            padding: 9px 0;
            transition: background .2s ease, color .2s ease, box-shadow .2s ease;
        }

        .auth-tabs .nav-link.active {
            background: #fff;
            color: var(--ink);
            box-shadow: 0 2px 6px rgba(43, 38, 34, 0.1);
        }

        .auth-tabs .nav-link:not(.active):hover {
            color: var(--ink);
        }

        /* ---- Body / form ---- */
        .auth-body {
            padding: 26px 24px 30px;
        }

        @media (min-width: 576px) {
            .auth-body {
                padding: 30px 32px 34px;
            }
        }

        .auth-body .form-group-spaced {
            margin-bottom: 18px;
        }

        .auth-body .form-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--muted);
            margin-bottom: 7px;
        }

        .auth-body .form-control,
        .auth-body .form-select {
            font-size: 16px; /* avoids iOS zoom-on-focus */
            padding: 11px 14px;
            border: 1.5px solid var(--line);
            border-radius: 9px;
            background: #fdfcfa;
            transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .auth-body .form-control:focus,
        .auth-body .form-select:focus {
            border-color: var(--brand);
            background: #fff;
            box-shadow: 0 0 0 3.5px rgba(191, 146, 100, 0.16);
        }

        .auth-body .form-control.is-valid,
        .auth-body .form-select.is-valid {
            border-color: #4caf7d;
            background: #fdfcfa url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%234caf7d' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.5-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 14px;
        }

        .auth-body .form-control.is-invalid,
        .auth-body .form-select.is-invalid {
            border-color: #e0665a;
            background: #fdfcfa;
        }

        .pw-group {
            position: relative;
        }

        .pw-group .form-control {
            padding-right: 46px;
        }

        .pw-toggle-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: var(--muted);
            width: 34px;
            height: 34px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s ease, color .15s ease;
        }

        .pw-toggle-btn:hover {
            color: var(--brand-dark);
            background: rgba(191, 146, 100, 0.1);
        }

        .auth-submit-btn {
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            border: none;
            width: 100%;
            padding: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.01em;
            border-radius: 9px;
            color: #fff;
            box-shadow: 0 8px 18px -8px rgba(191, 146, 100, 0.7);
            transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
        }

        .auth-submit-btn:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 10px 20px -8px rgba(191, 146, 100, 0.8);
            color: #fff;
        }

        .auth-submit-btn:active {
            transform: translateY(0);
        }

        .forgot-link {
            display: block;
            text-align: center;
            margin-top: 16px;
            font-size: 0.85rem;
            color: var(--muted);
            text-decoration: none;
        }

        .forgot-link:hover {
            color: var(--brand-dark);
            text-decoration: underline;
        }

        .auth-body hr.auth-divider {
            border: none;
            border-top: 1px solid var(--line);
            margin: 22px 0 18px;
        }

        .g-birthday {
            --bs-gutter-x: 12px;
        }
    </style>
</head>

<body>
    <?php include_once('navbar.php'); ?>

    <div class="auth-wrap">
        <div class="auth-card">

            <div class="auth-card-header">
                <div class="auth-brand-mark">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <h2>Welcome</h2>
                <p>Login to your account or create a new one</p>
            </div>

            <!-- Tab switcher -->
            <ul class="nav auth-tabs" id="authTab" role="tablist">
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-pane"
                        type="button" role="tab" aria-controls="login-pane" aria-selected="true">
                        Login
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-pane"
                        type="button" role="tab" aria-controls="register-pane" aria-selected="false">
                        Register
                    </button>
                </li>
            </ul>

            <div class="tab-content">

                <!-- ============ LOGIN TAB ============ -->
                <div class="tab-pane fade show active auth-body" id="login-pane" role="tabpanel" aria-labelledby="login-tab">
                    <form>
                        <div class="form-group-spaced">
                            <label for="exampleInputEmail1" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                                placeholder="Enter email" name="login_Email">
                        </div>

                        <div class="form-group-spaced">
                            <label for="password" class="form-label">Password</label>
                            <div class="pw-group">
                                <input type="password" class="form-control" id="password" placeholder="Password" autocomplete="off" name="login_Password">
                                <button type="button" id="showpwbtn" class="pw-toggle-btn">
                                    <i id="btnicon" class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" id="loginbtn" onclick="return false" value="login" class="btn auth-submit-btn mt-2" name="loginbtn">Login</button>

                        <a href="passwordrest.php" class="forgot-link">Forgot Password?</a>
                    </form>
                </div>

                <!-- ============ REGISTER TAB ============ -->
                <div class="tab-pane fade auth-body" id="register-pane" role="tabpanel" aria-labelledby="register-tab">

                    <div class="form-group-spaced">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" placeholder="Enter email">
                    </div>

                    <div class="form-group-spaced">
                        <label for="password2" class="form-label">Password</label>
                        <div class="pw-group">
                            <input type="password" class="form-control" id="password2" placeholder="Password" autocomplete="off">
                            <button type="button" id="showpwbtn2" class="pw-toggle-btn">
                                <i id="btnicon2" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group-spaced">
                        <label for="fullName" class="form-label">Full name</label>
                        <input type="text" class="form-control" id="fullName" placeholder="Enter name">
                    </div>

                    <div class="form-group-spaced">
                        <label for="telNo" class="form-label">Tel No</label>
                        <input type="text" class="form-control" id="telNo" placeholder="Enter Tel no">
                    </div>

                    <div class="form-group-spaced">
                        <label for="nic" class="form-label">NIC</label>
                        <input type="text" class="form-control" id="nic" placeholder="Enter NIC">
                    </div>

                    <div class="form-group-spaced">
                        <div class="row g-birthday">
                            <div class="col-8">
                                <label for="birthday" class="form-label">Birthday</label>
                                <input type="date" class="form-control" id="birthday">
                            </div>
                            <div class="col-4">
                                <label for="age" class="form-label">Age</label>
                                <input type="text" class="form-control" id="age" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-spaced">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender">
                            <option disabled selected value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                    <button type="button" id="regbtn" class="btn auth-submit-btn mt-1">Register</button>
                </div>

            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.js"></script>
    <script src="js/sweetalert.js"></script>
    <script>
        $(document).ready(function() {
            const today = new Date().toISOString().split('T')[0];

            $('#birthday').attr('max', today);

            $('#birthday').on('change', function() {
                const birthday = new Date($(this).val());
                const todayday = new Date();
                let age = todayday.getFullYear() - birthday.getFullYear();

                $('#age').val(age);
            });
            $('#telNo').on('input change', function() {
                let value = $(this).val();

                console.log(value);

                value = value.replace(/\D/g, '');

                if (value.length > 0 && !value.startsWith('0')) {
                    value = "0" + value;
                }

                if (value.length > 10) {
                    value = value.slice(0, 10);
                }
                console.log(value);

                $(this).val(value);
            });

            $('#nic').on('input change', function() {
                let value = $(this).val().trim().toUpperCase();

                const oldNIC = /^\d{9}[VX]$/;

                const newNIC = /^\d{12}$/;

                if (oldNIC.test(value) || newNIC.test(value)) {
                    $('#nic').attr('class', 'form-control is-valid');
                } else {
                    $('#nic').attr('class', 'form-control is-invalid');


                    if (value.length > 12) {
                        value = value.slice(0, 12);
                        $(this).val(value);
                    }
                }
            });
            $('#loginbtn').on('click', function() {
                let email = $('#exampleInputEmail1').val();
                let password = $('#password').val();

                let error = 0;

                if (email == "") {
                    // alert("Please Add Email");
                    $('#exampleInputEmail1').attr('class', 'form-control is-invalid');

                    Swal.fire({
                        title: "Insert Email",
                        text: "Email is empty!",
                        icon: "error"
                    });
                    error++;
                } else {
                    $('#exampleInputEmail1').attr('class', 'form-control is-valid');
                }
                if (password == "" || password.length < 5) {

                    $('#password').attr('class', 'form-control is-invalid');

                    error++;
                } else {
                    $('#password').attr('class', 'form-control is-valid');
                }

                if (error == 0) {

                    $.ajax({
                        type: 'POST',
                        url: 'lib/routes/auth/authentication.php',
                        data: {
                            loginEmail: email,
                            loginPassword: password
                        },
                        dataType: 'json',
                        success: function(respons) {
                            if (respons.loginstatus === true) {
                                window.location.href = respons.path;
                            } else {
                                Swal.fire({
                                    title: "Login Failed",
                                    text: respons.message,
                                    icon: "error"
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: "Error",
                                text: "Something went wrong. Please try again.",
                                icon: "error"
                            });
                        }
                    })
                }

            });
            $('#regbtn').on('click', function() {
                let email = $('#email').val();
                let password = $('#password2').val();
                let name = $('#fullName').val();
                let phone = $('#telNo').val();
                let nic = $('#nic').val();
                let gender = $('#gender').val();
                let birthday = $('#birthday').val();

                console.log(phone);
                let error = 0;

                if (email == "") {
                    // alert("Please Add Email");
                    $('#email').attr('class', 'form-control is-invalid');

                    Swal.fire({
                        title: "Insert Email",
                        text: "Email is empty!",
                        icon: "error"
                    });
                    error++;
                } else {
                    $('#email').attr('class', 'form-control is-valid');
                }
                if (password == "" || password.length < 5) {

                    $('#password2').attr('class', 'form-control is-invalid');

                    error++;
                } else {
                    $('#password2').attr('class', 'form-control is-valid');
                }
                if (name == "") {
                    $('#fullName').attr('class', 'form-control is-invalid');
                    error++;
                } else {
                    $('#fullName').attr('class', 'form-control is-valid');
                }

                if (phone == "" || phone.length < 10) {
                    $('#telNo').attr('class', 'form-control is-invalid');
                    error++;
                } else {
                    $('#telNo').attr('class', 'form-control is-valid');

                }

                if (nic == "" || nic.length < 10) {
                    $('#nic').attr('class', 'form-control is-invalid');
                    error++;
                } else {
                    $('#nic').attr('class', 'form-control is-valid');
                }

                console.log(phone);

                if (gender == null || gender == "") {
                    $('#gender').attr('class', 'form-control is-invalid');
                    error++;
                } else {
                    $('#gender').attr('class', 'form-control is-valid');
                }

                if (error == 0) {
                    // alert('success!');

                    $.ajax({
                        type: 'POST',
                        url: 'lib/routes/customer/addCustomer.php',
                        data: {
                            customerEmail: email,
                            customerPassword: password,
                            customerName: name,
                            customerPhone: phone,
                            customerNIC: nic,
                            customerGender: gender,
                            customerBirthday: birthday
                        },
                        success: function(respons) {
                            if (respons == "success") {
                                Swal.fire({
                                    title: "Successfully Registered",
                                    text: "user created successfully",
                                    icon: "success"
                                }).then(function() {
                                    location.reload();
                                });
                            } else if (respons == "Phone number Exists") {
                                Swal.fire({
                                    title: "Phone number",
                                    text: "Phone number Exists",
                                    icon: "info"
                                });
                            } else {
                                Swal.fire({
                                    title: "Err",
                                    text: respons,
                                    icon: "info"
                                });
                            }
                        },
                        error: function() {
                            // error handling
                        }
                    })
                }

            });



            $("#showpwbtn").on('click', function() {
                var passwordField = $("#password");

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    $("#btnicon").attr('class', 'fa-solid fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    $("#btnicon").attr('class', 'fa-solid fa-eye');
                }
            })

            $("#showpwbtn2").on('click', function() {
                var passwordField = $("#password2");

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    $("#btnicon2").attr('class', 'fa-solid fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    $("#btnicon2").attr('class', 'fa-solid fa-eye');
                }
            })
        });
    </script>
</body>

</html>