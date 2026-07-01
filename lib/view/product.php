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
                <div class="row justify-content-end">
                    <div class="col-3">

                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <h3>Add Product</h3>
                        <form id="addproductform">
                            <fieldset>
                                <div>
                                    <label for="exampleInputEmail1" class="form-label mt-4">Product Name</label>
                                    <input type="text" class="form-control" id="productname" name="productname" placeholder="Enter Product name">
                                </div>
                                <div>
                                    <label for="exampleTextarea" class="form-label mt-4">Product Details</label>
                                    <textarea class="form-control" id="details" rows="3" name="details"></textarea>
                                </div>
                                <div>
                                    <label for="exampleSelect1" class="form-label mt-4">Product Category</label>
                                    <select class="form-select" id="category" name="category">
                                        <option>cat 1</option>
                                        <option>cat 2</option>
                                        <option>cat 3</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="exampleSelect1" class="form-label mt-4">Supplier</label>
                                    <select class="form-select" id="supplier" name="supplier">
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-7">
                                        <label for="formFile" class="form-label mt-4">Product Image</label>
                                        <input class="form-control" type="file" id="formFile" name="formFile">
                                    </div>
                                    <div class="col-5">
                                        <img src="" alt="" id="imgprev">
                                    </div>
                                </div>

                                <div class="my-2">
                                    <button id="addproductbtn" onclick="return false" class="btn btn-primary">Save</button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <div class="col-6">
                        <h3>All Product</h3>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../../js/jquery.js"></script>
    <script src="../../js/sweetalert2.js"></script>

    <script>
        $(document).ready(function() {

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

                var form = $('#addproductform')[0];
                var formData = new FormData(form);
                $.ajax({
                    url: "../routes/product/addproduct.php",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {

                    },
                    error: function(data) {

                    }
                });

            });
        });
    </script>

    <?php
    include_once('footer.php')
    ?>