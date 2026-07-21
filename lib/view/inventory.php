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
    <title>Inventory Management</title>
    <?php
    include_once('common.php')
    ?>
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Inventory Management</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Inventory Management</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">

                <!-- Low stock alert banner -->
                <div id="lowStockAlert" class="alert alert-warning d-none" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span id="lowStockText"></span>
                </div>

                <div class="row">
                    <div class="col-12">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Stock Levels</h4>
                            <div>
                                <button type="button" class="btn btn-outline-warning" id="lowStockFilterBtn">
                                    Show Low Stock Only
                                </button>
                            </div>
                        </div>

                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Reorder Level</th>
                                    <th>Stock Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTableBody">
                                <!-- Rows are loaded here via AJAX -->
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- ================= Adjust Stock Modal ================= -->
    <div class="modal fade" id="adjustStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adjust Stock — <span id="adjust_productName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="adjuststockform">
                        <input type="hidden" id="adjust_productId" name="productId">

                        <p class="mb-3">
                            Current quantity: <strong id="adjust_currentQty">0</strong>
                        </p>

                        <div class="mb-3">
                            <label for="adjust_type" class="form-label">Movement Type</label>
                            <select class="form-select" id="adjust_type" name="movementType">
                                <option value="IN">Stock In (new arrival)</option>
                                <option value="OUT">Stock Out (loss / correction)</option>
                                <option value="ADJUSTMENT">Adjustment (manual correction)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="adjust_qty" class="form-label">Quantity</label>
                            <input type="number" min="1" step="1" class="form-control" id="adjust_qty" name="quantity" placeholder="Enter quantity">
                        </div>

                        <div class="mb-3">
                            <label for="adjust_reason" class="form-label">Reason (optional)</label>
                            <textarea class="form-control" id="adjust_reason" name="reason" rows="2" placeholder="E.g. New stock arrival, damaged goods, count correction"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="adjust_reorder" class="form-label">Reorder Level (low-stock alert threshold)</label>
                            <input type="number" min="0" step="1" class="form-control" id="adjust_reorder" name="reorderLevel">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="savereorderbtn" class="btn btn-outline-primary">Save Reorder Level</button>
                    <button id="adjuststockbtn" class="btn btn-primary">Save Stock Change</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= Stock History Modal ================= -->
    <div class="modal fade" id="stockHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Stock History — <span id="history_productName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Change</th>
                                <th>Before</th>
                                <th>After</th>
                                <th>Reason</th>
                                <th>By</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <!-- Rows loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/jquery.js"></script>

    <script>
        $(document).ready(function() {

            var showLowStockOnly = false;

            // Load the inventory list on page load
            loadInventory();
            loadLowStockBanner();

            // ---------------- Live search ----------------
            $('#searchtext').on('input', function() {
                var searchtext = $(this).val().trim().toLowerCase();
                $('#inventoryTableBody tr').each(function() {
                    var rowText = $(this).text().toLowerCase();
                    $(this).toggle(searchtext === '' || rowText.indexOf(searchtext) !== -1);
                });
            });

            // ---------------- Low stock filter toggle ----------------
            $('#lowStockFilterBtn').on('click', function() {
                showLowStockOnly = !showLowStockOnly;
                $(this).toggleClass('btn-outline-warning btn-warning');
                $(this).text(showLowStockOnly ? 'Show All Products' : 'Show Low Stock Only');
                loadInventory();
            });

            // ---------------- Low stock banner ----------------
            function loadLowStockBanner() {
                $.ajax({
                    url: "../routes/inventory/viewinventory.php",
                    type: 'GET',
                    data: {
                        lowstock: 1
                    },
                    dataType: 'json',
                    success: function(items) {
                        if (items && items.length > 0) {
                            $('#lowStockText').text(items.length + ' product(s) are at or below their reorder level.');
                            $('#lowStockAlert').removeClass('d-none');
                        } else {
                            $('#lowStockAlert').addClass('d-none');
                        }
                    }
                });
            }

            // ---------------- View / Load Table ----------------
            function loadInventory() {
                $.ajax({
                    url: "../routes/inventory/viewinventory.php",
                    type: 'GET',
                    data: showLowStockOnly ? {
                        lowstock: 1
                    } : {},
                    dataType: 'json',
                    success: function(items) {
                        var rows = '';

                        if (!items || items.length === 0) {
                            rows = '<tr><td colspan="7" class="text-center">No products found</td></tr>';
                        } else {
                            $.each(items, function(index, item) {
                                var isLow = parseInt(item.quantity) <= parseInt(item.reorder_level);
                                var statusLabel = isLow ?
                                    '<span class="badge bg-danger">Low Stock</span>' :
                                    '<span class="badge bg-success">In Stock</span>';

                                rows += '<tr>' +
                                    '<td><img src="../../' + item.image + '" style="width:50px;height:50px;object-fit:cover;"></td>' +
                                    '<td>' + item.productName + '</td>' +
                                    '<td>' + item.categoryName + '</td>' +
                                    '<td>' + item.quantity + '</td>' +
                                    '<td>' + item.reorder_level + '</td>' +
                                    '<td>' + statusLabel + '</td>' +
                                    '<td>' +
                                    '<button class="btn btn-sm btn-outline-primary btn-adjust" data-id="' + item.productid + '" data-name="' + item.productName + '" data-qty="' + item.quantity + '" data-reorder="' + item.reorder_level + '">Adjust Stock</button> ' +
                                    '<button class="btn btn-sm btn-outline-secondary btn-history" data-id="' + item.productid + '" data-name="' + item.productName + '">History</button>' +
                                    '</td>' +
                                    '</tr>';
                            });
                        }

                        $('#inventoryTableBody').html(rows);
                        $('#searchtext').trigger('input');
                    },
                    error: function() {
                        $('#inventoryTableBody').html('<tr><td colspan="7" class="text-center text-danger">Failed to load inventory</td></tr>');
                    }
                });
            }

            // ---------------- Open Adjust Stock modal ----------------
            $(document).on('click', '.btn-adjust', function() {
                $('#adjust_productId').val($(this).data('id'));
                $('#adjust_productName').text($(this).data('name'));
                $('#adjust_currentQty').text($(this).data('qty'));
                $('#adjust_reorder').val($(this).data('reorder'));
                $('#adjust_qty').val('');
                $('#adjust_reason').val('');
                $('#adjust_type').val('IN');

                $('#adjustStockModal').modal('show');
            });

            // ---------------- Save Stock Change ----------------
            $('#adjuststockbtn').on('click', function(e) {
                e.preventDefault();

                var productId = $('#adjust_productId').val();
                var movementType = $('#adjust_type').val();
                var quantity = $('#adjust_qty').val();
                var reason = $('#adjust_reason').val().trim();

                if (!quantity || quantity <= 0) {
                    alert('Please enter a quantity greater than 0.');
                    return;
                }

                $.ajax({
                    url: "../routes/inventory/adjuststock.php",
                    type: 'POST',
                    data: {
                        productId: productId,
                        movementType: movementType,
                        quantity: quantity,
                        reason: reason
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('Stock updated successfully.');
                            $('#adjustStockModal').modal('hide');
                            loadInventory();
                            loadLowStockBanner();
                        } else if (response.status === 'error_insufficient_stock') {
                            alert('Not enough stock available for this change.');
                        } else {
                            alert(response.message || 'Could not update stock.');
                        }
                    },
                    error: function() {
                        alert('Something went wrong.');
                    }
                });
            });

            // ---------------- Save Reorder Level ----------------
            $('#savereorderbtn').on('click', function(e) {
                e.preventDefault();

                var productId = $('#adjust_productId').val();
                var reorderLevel = $('#adjust_reorder').val();

                if (reorderLevel === '' || reorderLevel < 0) {
                    alert('Please enter a valid reorder level.');
                    return;
                }

                $.ajax({
                    url: "../routes/inventory/updatereorderlevel.php",
                    type: 'POST',
                    data: {
                        productId: productId,
                        reorderLevel: reorderLevel
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('Reorder level updated.');
                            loadInventory();
                            loadLowStockBanner();
                        } else {
                            alert('Could not update reorder level.');
                        }
                    },
                    error: function() {
                        alert('Something went wrong.');
                    }
                });
            });

            // ---------------- View History ----------------
            $(document).on('click', '.btn-history', function() {
                var productId = $(this).data('id');
                var productName = $(this).data('name');

                $('#history_productName').text(productName);

                $.ajax({
                    url: "../routes/inventory/viewhistory.php",
                    type: 'GET',
                    data: {
                        productId: productId
                    },
                    dataType: 'json',
                    success: function(movements) {
                        var rows = '';

                        if (!movements || movements.length === 0) {
                            rows = '<tr><td colspan="7" class="text-center">No stock movements recorded yet</td></tr>';
                        } else {
                            $.each(movements, function(index, m) {
                                var changeLabel = m.quantity_change > 0 ? ('+' + m.quantity_change) : m.quantity_change;
                                rows += '<tr>' +
                                    '<td>' + m.created_at + '</td>' +
                                    '<td>' + m.movement_type + '</td>' +
                                    '<td>' + changeLabel + '</td>' +
                                    '<td>' + m.previous_quantity + '</td>' +
                                    '<td>' + m.new_quantity + '</td>' +
                                    '<td>' + (m.reason || '-') + '</td>' +
                                    '<td>' + (m.created_by || '-') + '</td>' +
                                    '</tr>';
                            });
                        }

                        $('#historyTableBody').html(rows);
                        $('#stockHistoryModal').modal('show');
                    },
                    error: function() {
                        alert('Could not load stock history.');
                    }
                });
            });

        });
    </script>

    <?php
    include_once('footer.php')
    ?>