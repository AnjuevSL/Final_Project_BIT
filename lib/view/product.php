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
    <title>Product Managemnt</title>
    <?php
    include_once('common.php')
    ?>
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Product Management</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Product Management</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- Header row: title left, Add Product button right -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">All Product</h4>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                + Add Product
                            </button>
                        </div>

                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="productTableBody">
                                <!-- Rows are loaded here via AJAX -->
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- ================= Add Product Modal ================= -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addproductform">
                        <fieldset>
                            <div>
                                <label for="productname" class="form-label mt-2">Product Name</label>
                                <input type="text" class="form-control" id="productname" name="productname" placeholder="Enter Product name">
                            </div>
                            <div>
                                <label for="details" class="form-label mt-3">Product Details</label>
                                <textarea class="form-control" id="details" rows="3" name="details"></textarea>
                            </div>
                            <div>
                                <label for="price" class="form-label mt-3">Price (Rs.)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" placeholder="Enter price">
                            </div>
                            <div>
                                <label for="category" class="form-label mt-3">Product Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option>cat 1</option>
                                    <option>cat 2</option>
                                    <option>cat 3</option>
                                </select>
                            </div>
                            <div>
                                <label for="supplier" class="form-label mt-3">Supplier</label>
                                <select class="form-select" id="supplier" name="supplier">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-7">
                                    <label for="formFile" class="form-label mt-3">Product Image</label>
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
                    <button id="addproductbtn" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= Edit Product Modal ================= -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editproductform">
                        <fieldset>
                            <input type="hidden" id="edit_productId" name="productId">
                            <div>
                                <label for="edit_productname" class="form-label mt-2">Product Name</label>
                                <input type="text" class="form-control" id="edit_productname" name="productname">
                            </div>
                            <div>
                                <label for="edit_details" class="form-label mt-3">Product Details</label>
                                <textarea class="form-control" id="edit_details" rows="3" name="details"></textarea>
                            </div>
                            <div>
                                <label for="edit_price" class="form-label mt-3">Price (Rs.)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="edit_price" name="price">
                            </div>
                            <div>
                                <label for="edit_category" class="form-label mt-3">Product Category</label>
                                <select class="form-select" id="edit_category" name="category">
                                    <option>cat 1</option>
                                    <option>cat 2</option>
                                    <option>cat 3</option>
                                </select>
                            </div>
                            <div>
                                <label for="edit_supplier" class="form-label mt-3">Supplier</label>
                                <select class="form-select" id="edit_supplier" name="supplier">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-7">
                                    <label for="edit_formFile" class="form-label mt-3">Product Image (leave empty to keep current)</label>
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
                    <button id="updateproductbtn" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/jquery.js"></script>

    <script>
        $(document).ready(function() {

            // Load the product list on page load
            loadProducts();

            // ---------------- Add Product ----------------

            $('#formFile').change(function() {
                var fileread = new FileReader();
                fileread.onload = function(e) {
                    $('#imgprev').attr('src', e.target.result);
                    $('#imgprev').attr('style', 'height:200px; width:200px;');
                }
                fileread.readAsDataURL(this.files[0]);
            });

            $('#addproductbtn').on('click', function(e) {
                e.preventDefault();

                // Explicit validation with visible feedback (native HTML5 validation
                // bubbles don't display reliably inside Bootstrap modals)
                var productname = $('#productname').val().trim();
                var price = $('#price').val();
                var imageFile = $('#formFile')[0].files[0];

                if (productname === '') {
                    alert('Please enter a product name.');
                    return;
                }
                if (!price) {
                    alert('Please enter a price.');
                    return;
                }
                if (!imageFile) {
                    alert('Please select a product image.');
                    return;
                }

                var form = $('#addproductform')[0];
                var formData = new FormData(form);
                $.ajax({
                    url: "../routes/product/addproduct.php",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('Product added successfully.');
                            form.reset();
                            $('#imgprev').attr('src', '');
                            $('#addProductModal').modal('hide');
                            loadProducts();
                        } else {
                            alert(response.message || 'Could not save the product.');
                        }
                    },
                    error: function(xhr) {
                        console.error('Add product failed:', xhr.responseText);
                        alert('Something went wrong. Check the browser console for details.');
                    }
                });
            });

            // ---------------- View / Load Table ----------------

            function loadProducts() {
                $.ajax({
                    url: "../routes/product/viewproducts.php",
                    type: 'GET',
                    dataType: 'json',
                    success: function(products) {
                        var rows = '';

                        if (!products || products.length === 0) {
                            rows = '<tr><td colspan="6" class="text-center">No products found</td></tr>';
                        } else {
                            $.each(products, function(index, product) {
                                var statusLabel = product.d_status == 1 ?
                                    '<span class="badge bg-success">Active</span>' :
                                    '<span class="badge bg-secondary">Inactive</span>';

                                // Conditional button: Deactivate if active, Activate if inactive
                                var statusButton = product.d_status == 1 ?
                                    '<button class="btn btn-sm btn-outline-warning btn-deactivate" data-id="' + product.productid + '">Deactivate</button>' :
                                    '<button class="btn btn-sm btn-outline-success btn-activate" data-id="' + product.productid + '">Activate</button>';

                                rows += '<tr>' +
                                    '<td><img src="../../' + product.image + '" style="width:50px;height:50px;object-fit:cover;"></td>' +
                                    '<td>' + product.productName + '</td>' +
                                    '<td>Rs.' + parseFloat(product.price).toFixed(2) + '</td>' +
                                    '<td>' + product.category + '</td>' +
                                    '<td>' + statusLabel + '</td>' +
                                    '<td>' +
                                        '<button class="btn btn-sm btn-outline-primary btn-edit" data-id="' + product.productid + '">Edit</button> ' +
                                        statusButton + ' ' +
                                        '<button class="btn btn-sm btn-outline-danger btn-delete" data-id="' + product.productid + '">Delete</button>' +
                                    '</td>' +
                                    '</tr>';
                            });
                        }

                        $('#productTableBody').html(rows);
                    },
                    error: function() {
                        $('#productTableBody').html('<tr><td colspan="6" class="text-center text-danger">Failed to load products</td></tr>');
                    }
                });
            }

            // ---------------- Edit Product ----------------

            // When Edit is clicked: fetch this product's data and pre-fill the edit modal
            $(document).on('click', '.btn-edit', function() {
                var productId = $(this).data('id');

                $.ajax({
                    url: "../routes/product/viewproducts.php",
                    type: 'GET',
                    data: { productId: productId },
                    dataType: 'json',
                    success: function(product) {
                        $('#edit_productId').val(product.productid);
                        $('#edit_productname').val(product.productName);
                        $('#edit_details').val(product.productDetails);
                        $('#edit_price').val(product.price);
                        $('#edit_category').val(product.category);
                        $('#edit_supplier').val(product.supplier);
                        $('#edit_imgprev').attr('src', '../../' + product.image);

                        $('#editProductModal').modal('show');
                    },
                    error: function() {
                        alert('Could not load product details.');
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

            $('#updateproductbtn').on('click', function(e) {
                e.preventDefault();

                var form = $('#editproductform')[0];
                var formData = new FormData(form);
                $.ajax({
                    url: "../routes/product/updateproduct.php",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('Product updated successfully.');
                            $('#editProductModal').modal('hide');
                            loadProducts();
                        } else {
                            alert(response.message || 'Could not update the product.');
                        }
                    },
                    error: function() {
                        alert('Something went wrong.');
                    }
                });
            });

            // ---------------- Delete Product ----------------

            // Delete button click (event delegation since rows are added dynamically)
            $(document).on('click', '.btn-delete', function() {
                var productId = $(this).data('id');

                if (confirm('This product will be permanently deleted. Are you sure?')) {
                    $.ajax({
                        url: "../routes/product/deleteproduct.php",
                        type: 'POST',
                        data: { productId: productId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                alert('Product deleted successfully.');
                                loadProducts();
                            } else {
                                alert(response.message || 'Could not delete the product.');
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
                var productId = $(this).data('id');

                if (confirm('Deactivate this product?')) {
                    $.ajax({
                        url: "../routes/product/togglestatus.php",
                        type: 'POST',
                        data: { productId: productId, status: 0 },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                alert('Product deactivated.');
                                loadProducts();
                            } else {
                                alert('Could not deactivate the product.');
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
                var productId = $(this).data('id');

                if (confirm('Activate this product?')) {
                    $.ajax({
                        url: "../routes/product/togglestatus.php",
                        type: 'POST',
                        data: { productId: productId, status: 1 },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                alert('Product activated.');
                                loadProducts();
                            } else {
                                alert('Could not activate the product.');
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