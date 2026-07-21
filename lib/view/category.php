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
<!doctype html>
<html lang="en">

<head>
    <title>Category Managemnt</title>
    <?php
    include_once('common.php')
    ?>
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Category Management</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Category Management</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- Header row: title left, Add category button right -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">All Category</h4>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addcategoryModal">
                                + Add Category
                            </button>
                        </div>

                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <!-- <th>Price</th> -->
                                    <!-- <th>Category</th> -->
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="categoryTableBody">
                                <!-- Rows are loaded here via AJAX -->
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- ================= Add category Modal ================= -->
    <div class="modal fade" id="addcategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addcategoryform">
                        <fieldset>
                            <div>
                                <label for="categoryname" class="form-label mt-2">Category Name</label>
                                <input type="text" class="form-control" id="categoryname" name="categoryname" placeholder="Enter category name">
                            </div>
                            <div>
                                <label for="description" class="form-label mt-3">Category description</label>
                                <textarea class="form-control" id="description" rows="3" name="description"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-7">
                                    <label for="formFile" class="form-label mt-3">Category Image</label>
                                    <input class="form-control" type="file" id="formFile" name="formFile">
                                </div>
                                <div class="col-5">
                                    <img src="" alt="" id="imgprev">
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="addcategorybtn" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= Edit category Modal ================= -->
    <div class="modal fade" id="editcategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editcategoryform">
                        <fieldset>
                            <input type="hidden" id="edit_categoryId" name="categoryId">
                            <div>
                                <label for="edit_categoryname" class="form-label mt-2">Category Name</label>
                                <input type="text" class="form-control" id="edit_categoryname" name="categoryname">
                            </div>
                            <div>
                                <label for="edit_description" class="form-label mt-3">Category description</label>
                                <textarea class="form-control" id="edit_description" rows="3" name="description"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-7">
                                    <label for="edit_formFile" class="form-label mt-3">Category Image (leave empty to keep current)</label>
                                    <input class="form-control" type="file" id="edit_formFile" name="formFile">
                                </div>
                                <div class="col-5">
                                    <img src="" alt="" id="edit_imgprev" style="height:80px;width:80px;object-fit:cover;">
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="updatecategorybtn" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/jquery.js"></script>

    <script>
        $(document).ready(function() {

            // Load the category list on page load
            loadcategorys();

            // ---------------- Live search ----------------
            // Filters the already-loaded category rows as you type in the
            // shared navbar search box (#searchtext).
            $('#searchtext').on('input', function() {
                var searchtext = $(this).val().trim().toLowerCase();
                $('#categoryTableBody tr').each(function() {
                    var rowText = $(this).text().toLowerCase();
                    $(this).toggle(searchtext === '' || rowText.indexOf(searchtext) !== -1);
                });
            });

            // ---------------- Add category ----------------

            $('#formFile').change(function() {
                var fileread = new FileReader();
                fileread.onload = function(e) {
                    $('#imgprev').attr('src', e.target.result);
                    $('#imgprev').attr('style', 'height:200px; width:200px;');
                }
                fileread.readAsDataURL(this.files[0]);
            });

            $('#addcategorybtn').on('click', function(e) {
                e.preventDefault();

                var categoryname = $('#categoryname').val().trim();
                var imageFile = $('#formFile')[0].files[0];

                if (categoryname === '') {
                    alert('Please enter a category name.');
                    return;
                }
                if (!imageFile) {
                    alert('Please select a category image.');
                    return;
                }

                var form = $('#addcategoryform')[0];
                var formData = new FormData(form);
                $.ajax({
                    url: "../routes/category/addcategory.php",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('category added successfully.');
                            form.reset();
                            $('#imgprev').attr('src', '');
                            $('#addcategoryModal').modal('hide');
                            loadcategorys();
                        } else {
                            alert(response.message || 'Could not save the category.');
                        }
                    },
                    error: function(xhr) {
                        console.error('Add category failed:', xhr.responseText);
                        alert('Something went wrong. Check the browser console for description.');
                    }
                });
            });

            // ---------------- View / Load Table ----------------

            function loadcategorys() {
                $.ajax({
                    url: "../routes/category/viewcategory.php",
                    type: 'GET',
                    dataType: 'json',
                    success: function(categorys) {
                        var rows = '';

                        if (!categorys || categorys.length === 0) {
                            rows = '<tr><td colspan="6" class="text-center">No categorys found</td></tr>';
                        } else {
                            $.each(categorys, function(index, category) {
                                var statusLabel = category.d_status == 1 ?
                                    '<span class="badge bg-success">Active</span>' :
                                    '<span class="badge bg-secondary">Inactive</span>';

                                // Conditional button: Deactivate if active, Activate if inactive
                                var statusButton = category.d_status == 1 ?
                                    '<button class="btn btn-sm btn-outline-warning btn-deactivate" data-id="' + category.categoryid + '">Deactivate</button>' :
                                    '<button class="btn btn-sm btn-outline-success btn-activate" data-id="' + category.categoryid + '">Activate</button>';

                                rows += '<tr>' +
                                    '<td><img src="../../' + category.image + '" style="width:80px;height:80px;object-fit:cover;"></td>' +
                                    '<td>' + category.categoryName + '</td>' +
                                    '<td>' + statusLabel + '</td>' +
                                    '<td class="">' +
                                    '<button class="btn btn-sm btn-outline-primary btn-edit" data-id="' + category.categoryid + '">Edit</button> ' +
                                    statusButton + ' ' +
                                    '<button class="btn btn-sm btn-outline-danger btn-delete" data-id="' + category.categoryid + '">Delete</button>' +
                                    '</td>' +
                                    '</tr>';
                            });
                        }

                        $('#categoryTableBody').html(rows);

                        // keep an active search term applied to freshly loaded rows
                        $('#searchtext').trigger('input');
                    },
                    error: function() {
                        $('#categoryTableBody').html('<tr><td colspan="6" class="text-center text-danger">Failed to load categorys</td></tr>');
                    }
                });
            }

            // ---------------- Edit category ----------------

            // When Edit is clicked: fetch this category's data and pre-fill the edit modal
            $(document).on('click', '.btn-edit', function() {
                var categoryId = $(this).data('id');

                $.ajax({
                    url: "../routes/category/viewcategory.php",
                    type: 'GET',
                    data: {
                        categoryId: categoryId
                    },
                    dataType: 'json',
                    success: function(category) {
                        $('#edit_categoryId').val(category.categoryid);
                        $('#edit_categoryname').val(category.categoryName);
                        $('#edit_description').val(category.description);
                        $('#edit_imgprev').attr('src', '../../' + category.image);

                        $('#editcategoryModal').modal('show');
                    },
                    error: function() {
                        alert('Could not load category description.');
                    }
                });
            });

            $('#edit_formFile').change(function() {
                var fileread = new FileReader();
                fileread.onload = function(e) {
                    $('#edit_imgprev').attr('src', e.target.result);
                }
                fileread.readAsDataURL(this.files[0]);
            });

            $('#updatecategorybtn').on('click', function(e) {
                e.preventDefault();

                var form = $('#editcategoryform')[0];
                var formData = new FormData(form);
                $.ajax({
                    url: "../routes/category/updatecategory.php",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('category updated successfully.');
                            $('#editcategoryModal').modal('hide');
                            loadcategorys();
                        } else {
                            alert(response.message || 'Could not update the category.');
                        }
                    },
                    error: function() {
                        alert('Something went wrong.');
                    }
                });
            });

            // ---------------- Delete category ----------------

            // Delete button click (event delegation since rows are added dynamically)
            $(document).on('click', '.btn-delete', function() {
                var categoryId = $(this).data('id');

                if (confirm('This category will be permanently deleted. Are you sure?')) {
                    $.ajax({
                        url: "../routes/category/deletecategory.php",
                        type: 'POST',
                        data: {
                            categoryId: categoryId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                alert('category deleted successfully.');
                                loadcategorys();
                            } else {
                                alert(response.message || 'Could not delete the category.');
                            }
                        },
                        error: function() {
                            alert('Something went wrong.');
                        }
                    });
                }
            });

            // Deactivate button click (set d_status = 0)
            $(document).on('click', '.btn-deactivate', function() {
                var categoryId = $(this).data('id');

                if (confirm('Deactivate this category?')) {
                    $.ajax({
                        url: "../routes/category/togglestatus.php",
                        type: 'POST',
                        data: {
                            categoryId: categoryId,
                            status: 0
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                alert('category deactivated.');
                                loadcategorys();
                            } else {
                                alert('Could not deactivate the category.');
                            }
                        },
                        error: function() {
                            alert('Something went wrong.');
                        }
                    });
                }
            });

            // Activate button click (set d_status = 1)
            $(document).on('click', '.btn-activate', function() {
                var categoryId = $(this).data('id');

                if (confirm('Activate this category?')) {
                    $.ajax({
                        url: "../routes/category/togglestatus.php",
                        type: 'POST',
                        data: {
                            categoryId: categoryId,
                            status: 1
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                alert('category activated.');
                                loadcategorys();
                            } else {
                                alert('Could not activate the category.');
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

    <?php
    include_once('footer.php')
    ?>