<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!--  <link rel="stylesheet" href="css/bootstrap-icons-1.13.1/bootstrap-icons.min.css"> -->
    <link rel="stylesheet" href="css/fontawesome-free-7.1.0-web/css/all.min.css">
</head>

<body>
    <?php include_once('navbar.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-6 px-4">
                <h2 class="text-center">Login</h2>
                <form>
                    <div>
                        <label for="exampleInputEmail1" class="form-label mt-4">Email address</label>
                        <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                            placeholder="Enter email" name="login_Email">

                        <div class="row">
                            <div class="col-11">
                                <label for="exampleInputPassword1" class="form-label mt-3">Password</label>
                                <input type="password" class="form-control" id="password" placeholder="Password" autocomplete="off" name="login_Password">
                            </div>
                            <div class="col-1 mt-5">
                                <button type="button" id="showpwbtn" class="btn btn-dark"><i id="btnicon"
                                        class="fa-solid fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" id="loginbtn" onclick="return false" value="login" class="btn btn-success my-3" name="loginbtn">Login</button>
                    <div>
                        <a href="passwordrest.php">Forget Password</a>
                    </div>
            </div>
            </form>
            <div class="col-6 px-4">
                <h2 class="text-center">Registration</h2>
                <div>
                    <label for="email" class="form-label mt-4">Email address</label>
                    <input type="email" class="form-control" id="email" placeholder="Enter email">
                </div>
                <div class="row">
                    <div class="col-11">
                        <label for="exampleInputPassword2" class="form-label mt-3">Password</label>
                        <input type="password" class="form-control" id="password2" placeholder="Password" autocomplete="off">
                    </div>
                    <div class="col-1 mt-5">
                        <button type="button" id="showpwbtn2" class="btn btn-dark"><i id="btnicon2"
                                class="fa-solid fa-eye"></i></button>
                    </div>
                </div>
                <div>
                    <label for="fullName" class="form-label mt-4">Full name</label>
                    <input type="name" class="form-control" id="fullName" placeholder="Enter name">
                </div>
                <div>
                    <label for="telNo" class="form-label mt-4">Tel No</label>
                    <input type="name" class="form-control" id="telNo" placeholder="Enter Tel no">
                </div>
                <div>
                    <label for="exampleInputNIC" class="form-label mt-4">NIC</label>
                    <input type="name" class="form-control" id="nic" placeholder="Enter NIC">
                </div>
                <div>
                    <div class="row">
                        <div class="col-8">
                            <label for="birthday" class="form-label mt-4">Birthday</label>
                            <input type="date" class="form-control" id="birthday">
                        </div>
                        <div class="col-4">
                            <label for="age" class="form-label mt-4 ">Age</label>
                            <input type="text" class="form-control" id="age" read only>
                        </div>
                    </div>

                </div>
                <div>
                    <label for="exampleSelectgender" class="form-label mt-4">Gender</label>
                    <select class="form-select" id="gender" fdprocessedid="mbstcc">
                        <option disable selected value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="mt">
                    <button type="button" id="regbtn" class="btn btn-success my-3">Register</button>
                </div>
            </div>
        </div>

    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>


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

</html>