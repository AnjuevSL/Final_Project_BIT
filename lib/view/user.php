<?php
//start sessions
session_start();

if (isset($_SESSION['user'])) {

  if (isset($_SESSION['usertype'])) {

    $usertype = $_SESSION['usertype'];
    if ($usertype == "Admin") {
    } else {
      header('Location:../../index.php');
    }
  } else {
    header('Location:../../index.php');
  }
} else {
  header('Location:../../index.php');
}

?>
<!-- <a href="logout.php"><button type="button" href="" class="btn btn-secondary">logout</button></a> -->

<!doctype html>
<html lang="en">

<head>

  <title>User Management</title>

  <?php
  include_once('common.php')
  ?>
  <!--begin::App Main-->
  <main class="app-main">
    <!--begin::App Content Header-->
    <div class="app-content-header">
      <!--begin::Container-->
      <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
          <div class="col-sm-6">
            <h3 class="mb-0">User Management</h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </div>
        </div>
        <!--end::Row-->
      </div>
      <!--end::Container-->
    </div>
    <!--end::App Content Header-->
    <!--begin::App Content-->
    <div class="app-content">
      <!--begin::Container-->
      <div class="container-fluid">

      </div>
      <!--begin::Row-->
      <div class="row">
        <div class="row">
          <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3 ">
              <h4 class="mb-0">All Users</h4>
              <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                <i class="bi bi-plus-circle"></i> Add User
              </button>
            </div>
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th scope="col">User Name</th>
                  <th scope="col">Email Address</th>
                  <th scope="col">NIC</th>
                  <th scope="col">Phone Number</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody id="userlist">

              </tbody>
            </table>
          </div>
        </div>
        <!--end::Container-->
      </div>
      <!--end::App Content-->
  </main>

  <div class="modal" id="edituserdata" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit User Data</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editid">
          <div>
            <label for="email" class="form-label mt-4">Email address</label>
            <input type="email" class="form-control" id="email" placeholder="Enter email">
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
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" id="editbtn" class="btn btn-primary">Save changes</button>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="../../js/jquery.js"></script>

  <script src="../../js/sweetalert.js"></script>


  <script>
    $(document).ready(function() {

      function loadalldata() {
        $.get("../routes/customer/loademp.php", function(res) {
          $('#userlist').html(res);
        });
      }

      loadalldata();

      function clearform() {
        $('#InputEmail1').val("");
        $('#InputName').val("");
        $('#phone').val("");
        $('#nic').val("");
        $('#gender').val("");
        $('#birthday').val("");
        $('#editid').val("");
      }

      $("#searchtext").on('input', function() {

        $searchtext = $(this).val();

        if ($searchtext != "") {
          $.get("../routes/customer/loadempsearch.php", {
            searchtext: $searchtext
          }, function(res) {
            $('#userlist').html(res);
          });
        } else {
          loadalldata();
        }

      })

      $('#birthday').on('change', function() {

        const birthday = new Date($(this).val());
        const todayday = new Date();
        let age = todayday.getFullYear() - birthday.getFullYear();

        $('#age').val(age);
      });

      $('#editbtn').on('click', function() {
        let email = $('#email').val();
        let name = $('#fullName').val();
        let phone = $('#telNo').val();
        let nic = $('#nic').val();
        let gender = $('#gender').val();
        let birthday = $('#birthday').val();
        let userid = $('#editid').val();

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
            url: '../routes/customer/editCustomer.php',
            data: {
              customerEmail: email,
              customerid: userid,
              customerName: name,
              customerPhone: phone,
              customerNIC: nic,
              customerGender: gender,
              customerBirthday: birthday
            },
            success: function(respons) {

              if ($.trim(respons) == "success") {
                $('#edituserdata').modal('hide');
                loadalldata();
                clearform();
                Swal.fire({

                  title: "Successfully Updated",
                  text: "user Update successfully",
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
                  title: "Error Updating",
                  text: "Something Went Wrong",
                  icon: "error"
                });

              }
            },
            error: function() {
              // error handling
            }
          })
        }
      })

    })

    $(document).on('click', '.deletebtn', function() {

      let customerid = $(this).data('id');

      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then((result) => {
        if (result.isConfirmed) {

          $.get("../routes/customer/deleteempbyid.php", {
            userid: customerid
          }, function(res) {
            if (res == "success") {
              Swal.fire({
                title: "Deleted!",
                text: "Your file has been deleted.",
                icon: "success"
              });
            } else {
              Swal.fire({
                title: "not Deleted!",
                text: "Something Went Wrong.",
                icon: "error"
              });
            }
          })
        }
      });

    })

    $(document).on('click', '.deactivatebtn', function() {

      let customerid = $(this).data('id');
      let status = $(this).data('status');

      if (status === "Active") {
        Swal.fire({
          title: "Are you sure?",
          text: "Do you wnat Deactivate this account!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, deactivate it!"
        }).then((result) => {
          if (result.isConfirmed) {

            $.get("../routes/customer/deactivatebyid.php", {
              userid: customerid
            }, function(res) {
              alert(res);
              if ($.trim(res) == "success") {
                Swal.fire({
                  title: "Deactivated!",
                  text: "Account has been deactivated.",
                  icon: "success"
                }).then(() => {
                  location.reload();
                });
              } else {
                Swal.fire({
                  title: "not DeActivated!",
                  text: "Something Went Wrong.",
                  icon: "error"
                });
              }
            })

          }
        });
      } else {
        Swal.fire({
          title: "Are you sure?",
          text: "Do you wnat Activate this account!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, activate it!"
        }).then((result) => {
          if (result.isConfirmed) {

            $.get("../routes/customer/deactivatebyid.php", {
              userid: customerid
            }, function(res) {
              if (res == "success") {
                Swal.fire({
                  title: "Activated!",
                  text: "Account has been activated.",
                  icon: "success"
                });
              } else {
                Swal.fire({
                  title: "not Activated!",
                  text: "Something Went Wrong.",
                  icon: "error"
                });
              }
            })

          }
        });
      }
    })


    function edituser($userid) {

      $('#editid').val($userid);

      $.get("../routes/customer/loadempbyid.php", {
        userid: $userid
      }, function(res) {

        var jdata = jQuery.parseJSON(res);

        $('#email').val(jdata.customerEmail);
        $('#fullName').val(jdata.customerName);
        $('#telNo').val(jdata.customerPhone);
        $('#nic').val(jdata.customerNIC);
        $('#birthday').val(jdata.customerBirthday);
        $('#gender').val(jdata.customerGender);

        const birthday = new Date(jdata.customerBirthday);
        const todayday = new Date();
        let age = todayday.getFullYear() - birthday.getFullYear();

        $('#age').val(age);


        $('#edituserdata').modal('show');

      })
    }
  </script>
  <?php
  include_once('footer.php')
  ?>



  </body>
  <!--end::Body-->

</html>