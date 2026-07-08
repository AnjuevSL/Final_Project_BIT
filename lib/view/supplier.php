<?php
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
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Supplier Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print" onload="this.media = 'all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../../css/adminlte.css" />
    <script src="../../js/jquery.js"></script>
    <?php include 'common.php'; ?>

</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Supplier Management</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center mb-3 ">
                            <h4 class="mb-0">All Supplier</h4>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                                    <i class="bi bi-plus-circle"></i> Add Supplier
                                </button>
                            </div>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Supplier ID</th>
                                        <th>Supplier Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="supplierTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <div class="modal fade" id="addSupplierModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Supplier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addsupplierform">
                            <div class="mb-3">
                                <label for="supplierName" class="form-label">Supplier Name</label>
                                <input type="text" class="form-control" id="supplierName" name="supplierName" placeholder="Enter supplier name">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" placeholder="Enter address" rows="3"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="addsupplierbtn">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editSupplierModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Supplier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editsupplierform">
                            <input type="hidden" id="editSupplierId" name="supplierId">
                            <div class="mb-3">
                                <label for="editSupplierName" class="form-label">Supplier Name</label>
                                <input type="text" class="form-control" id="editSupplierName" name="supplierName">
                            </div>
                            <div class="mb-3">
                                <label for="editEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editEmail" name="email">
                            </div>
                            <div class="mb-3">
                                <label for="editPhone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="editPhone" name="phone">
                            </div>
                            <div class="mb-3">
                                <label for="editAddress" class="form-label">Address</label>
                                <textarea class="form-control" id="editAddress" name="address" rows="3"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="updatesupplierbtn">Update</button>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'footer.php'; ?>
    </div>

    <script>
        $(document).ready(function() {
            loadSuppliers();

            $('#addsupplierbtn').on('click', function(e) {
                e.preventDefault();
                var supplierName = $('#supplierName').val().trim();
                if (supplierName === '') {
                    alert('Please enter a supplier name.');
                    return;
                }
                var formData = {
                    supplierName: supplierName,
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                    address: $('#address').val()
                };
                $.ajax({
                    url: "../routes/supplier/addsupplier.php",
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('Supplier added successfully.');
                            $('#addsupplierform')[0].reset();
                            $('#addSupplierModal').modal('hide');
                            loadSuppliers();
                        } else {
                            alert(response.message || 'Could not save the supplier.');
                        }
                    },
                    error: function(xhr) {
                        console.error('Add supplier failed:', xhr.responseText);
                        alert('Something went wrong.');
                    }
                });
            });

            function loadSuppliers() {
                $.ajax({
                    url: "../routes/supplier/viewsuppliers.php",
                    type: 'GET',
                    dataType: 'json',
                    success: function(suppliers) {
                        var rows = '';
                        if (!suppliers || suppliers.length === 0) {
                            rows = '<tr><td colspan="6" class="text-center">No suppliers found</td></tr>';
                        } else {
                            $.each(suppliers, function(index, supplier) {
                                var statusLabel = supplier.d_status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
                                var statusButton = supplier.d_status == 1 ? '<button class="btn btn-sm btn-outline-warning btn-deactivate" data-id="' + supplier.supplierid + '">Deactivate</button>' : '<button class="btn btn-sm btn-outline-success btn-activate" data-id="' + supplier.supplierid + '">Activate</button>';
                                rows += '<tr><td>' + supplier.supplierid + '</td><td>' + supplier.supplierName + '</td><td>' + (supplier.email || '-') + '</td><td>' + (supplier.phone || '-') + '</td><td>' + statusLabel + '</td><td><button class="btn btn-sm btn-outline-primary btn-edit" data-id="' + supplier.supplierid + '">Edit</button> ' + statusButton + ' <button class="btn btn-sm btn-danger btn-delete" data-id="' + supplier.supplierid + '">Delete</button></td></tr>';
                            });
                        }
                        $('#supplierTableBody').html(rows);
                    },
                    error: function() {
                        $('#supplierTableBody').html('<tr><td colspan="6" class="text-center text-danger">Failed to load suppliers</td></tr>');
                    }
                });
            }

            $(document).on('click', '.btn-edit', function() {
                var supplierId = $(this).data('id');
                $.ajax({
                    url: "../routes/supplier/viewsuppliers.php?supplierId=" + supplierId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(supplier) {
                        $('#editSupplierId').val(supplier.supplierid);
                        $('#editSupplierName').val(supplier.supplierName);
                        $('#editEmail').val(supplier.email);
                        $('#editPhone').val(supplier.phone);
                        $('#editAddress').val(supplier.address);
                        $('#editSupplierModal').modal('show');
                    },
                    error: function() {
                        alert('Could not load supplier details.');
                    }
                });
            });

            $('#updatesupplierbtn').on('click', function(e) {
                e.preventDefault();
                var supplierId = $('#editSupplierId').val();
                var supplierName = $('#editSupplierName').val().trim();
                if (supplierName === '') {
                    alert('Please enter a supplier name.');
                    return;
                }
                var formData = {
                    supplierId: supplierId,
                    supplierName: supplierName,
                    email: $('#editEmail').val(),
                    phone: $('#editPhone').val(),
                    address: $('#editAddress').val()
                };
                $.ajax({
                    url: "../routes/supplier/updatesupplier.php",
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('Supplier updated successfully.');
                            $('#editSupplierModal').modal('hide');
                            loadSuppliers();
                        } else {
                            alert(response.message || 'Could not update the supplier.');
                        }
                    },
                    error: function() {
                        alert('Something went wrong.');
                    }
                });
            });

            $(document).on('click', '.btn-deactivate', function() {
                var supplierId = $(this).data('id');
                if (confirm('Deactivate this supplier?')) {
                    $.ajax({
                        url: "../routes/supplier/togglesupplier.php",
                        type: 'POST',
                        data: {
                            supplierId: supplierId,
                            status: 0
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                alert('Supplier deactivated.');
                                loadSuppliers();
                            } else {
                                alert('Could not deactivate the supplier.');
                            }
                        },
                        error: function() {
                            alert('Something went wrong.');
                        }
                    });
                }
            });

            $(document).on('click', '.btn-activate', function() {
                var supplierId = $(this).data('id');
                if (confirm('Activate this supplier?')) {
                    $.ajax({
                        url: "../routes/supplier/togglesupplier.php",
                        type: 'POST',
                        data: {
                            supplierId: supplierId,
                            status: 1
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                alert('Supplier activated.');
                                loadSuppliers();
                            } else {
                                alert('Could not activate the supplier.');
                            }
                        },
                        error: function() {
                            alert('Something went wrong.');
                        }
                    });
                }
            });

            $(document).on('click', '.btn-delete', function() {
                var supplierId = $(this).data('id');
                if (confirm('Permanently delete this supplier?')) {
                    $.ajax({
                        url: "../routes/supplier/deletesupplier.php",
                        type: 'POST',
                        data: {
                            supplierId: supplierId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                alert('Supplier deleted successfully.');
                                loadSuppliers();
                            } else {
                                alert(response.message || 'Could not delete the supplier.');
                            }
                        },
                        error: function() {
                            alert('Something went wrong.');
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>