<?php
//start sessions
session_start();

if (isset($_SESSION['user'])) {

  if (isset($_SESSION['usertype'])) {

    $usertype = $_SESSION['usertype'];
    if ($usertype == "Admin") {
    } else {
      header('Location:../../login.php');
    }
  } else {
    header('Location:../../login.php');
  }
} else {
  header('Location:../../login.php');
}

?>
<!-- <a href="logout.php"><button type="button" href="" class="btn btn-secondary">logout</button></a> -->

<!doctype html>
<html lang="en">

<head>

  <title>Admin User Management</title>

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
            <h3 class="mb-0">Admin User Management</h3>
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
              <h4 class="mb-0">All Admin Users</h4>
              <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                <i class="bi bi-plus-circle"></i> Add User
              </button>
            </div>
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Email Address</th>
                  <th scope="col">Play Role</th>
                  <th scope="col">Status</th>
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

  <div class="modal" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Admin User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div>
            <label for="addAdminEmail" class="form-label mt-4">Email address</label>
            <input type="email" class="form-control" id="addAdminEmail" placeholder="Enter email">
          </div>
          <div>
            <label for="addAdminPassword" class="form-label mt-4">Password</label>
            <input type="password" class="form-control" id="addAdminPassword" placeholder="Enter password">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="addAdminBtn" class="btn btn-primary">Add Admin</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal" id="edituserdata" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Admin Data</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editid">
          <div>
            <label for="email" class="form-label mt-4">Email address</label>
            <input type="email" class="form-control" id="email" placeholder="Enter email">
          </div>
          <div>
            <label for="editPassword" class="form-label mt-4">Password <small class="text-muted">(leave blank to keep unchanged)</small></label>
            <input type="password" class="form-control" id="editPassword" placeholder="Enter new password">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="editbtn" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>

  <script src="../../js/jquery.js"></script>

  <script src="../../js/sweetalert.js"></script>


  <script>
    function loadalldata() {
      $.get("../routes/customer/loaddata.php", {
        role: 'admin'
      }, function(res) {
        $('#userlist').html(res);
      });
    }

    $(document).ready(function() {

      loadalldata();

      $("#searchtext").on('input', function() {

        $searchtext = $(this).val();

        if ($searchtext != "") {
          $.get("../routes/customer/loaddatasearch.php", {
            searchtext: $searchtext,
            role: 'admin'
          }, function(res) {
            $('#userlist').html(res);
          });
        } else {
          loadalldata();
        }

      })

    })

    $('#addAdminBtn').on('click', function() {
      let email = $('#addAdminEmail').val();
      let password = $('#addAdminPassword').val();
      let error = 0;

      if (email == "") {
        $('#addAdminEmail').attr('class', 'form-control is-invalid');
        error++;
      } else {
        $('#addAdminEmail').attr('class', 'form-control is-valid');
      }

      if (password == "" || password.length < 6) {
        $('#addAdminPassword').attr('class', 'form-control is-invalid');
        error++;
      } else {
        $('#addAdminPassword').attr('class', 'form-control is-valid');
      }

      if (error == 0) {
        $.ajax({
          type: 'POST',
          url: '../routes/customer/addAdmin.php',
          data: {
            adminEmail: email,
            adminPassword: password
          },
          success: function(respons) {
            respons = $.trim(respons);

            if (respons == "success") {
              $('#addSupplierModal').modal('hide');
              $('#addAdminEmail').val("");
              $('#addAdminPassword').val("");
              loadalldata();
              Swal.fire({
                title: "Successfully Added",
                text: "Admin user added successfully",
                icon: "success"
              });
            } else if (respons == "Email Exists") {
              Swal.fire({
                title: "Email",
                text: "Email already exists",
                icon: "info"
              });
            } else {
              Swal.fire({
                title: "Error Adding",
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
    });

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
            if ($.trim(res) == "success") {
              loadalldata();
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
              if ($.trim(res) == "success") {
                loadalldata();
                Swal.fire({
                  title: "Deactivated!",
                  text: "Account has been deactivated.",
                  icon: "success"
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
              if ($.trim(res) == "success") {
                loadalldata();
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


    $('#editbtn').on('click', function() {
      let email = $('#email').val();
      let password = $('#editPassword').val();
      let userid = $('#editid').val();

      let error = 0;

      if (email == "") {
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

      if (error == 0) {

        $.ajax({
          type: 'POST',
          url: '../routes/customer/editAdmin.php',
          data: {
            loginid: userid,
            adminEmail: email,
            adminPassword: password
          },
          success: function(respons) {
            respons = $.trim(respons);

            if (respons == "success") {
              $('#edituserdata').modal('hide');
              $('#editPassword').val("");
              loadalldata();
              Swal.fire({
                title: "Successfully Updated",
                text: "Admin updated successfully",
                icon: "success"
              });

            } else if (respons == "Email Exists") {
              Swal.fire({
                title: "Email",
                text: "Email already exists",
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

    function edituser($userid) {

      $('#editid').val($userid);

      $.get("../routes/customer/loadadminbyid.php", {
        userid: $userid
      }, function(res) {

        var jdata = jQuery.parseJSON(res);

        $('#email').val(jdata.loginEmail);
        $('#editPassword').val("");

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