<?php
session_start();
if (!(isset($_SESSION['user']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == "Admin")) {
    header('Location:../../login.php');
    exit();
}

include_once('../function/reportfunction.php');

$repObj     = new Report();
$categories = $repObj->getAllCategoriesList();
?>
<!doctype html>
<html lang="en">

<head>
    <title>Inventory Report</title>
    <?php
    include_once('common.php')
    ?>
    <style>
        @media print {

            .app-header,
            .app-sidebar,
            .app-content-header,
            .no-print {
                display: none !important;
            }

            .app-main,
            .app-content {
                margin: 0 !important;
                padding: 0 !important;
            }
        }

        .print-header {
            display: none;
        }

        @media print {
            .print-header {
                display: block;
                text-align: center;
                margin-bottom: 20px;
            }
        }
    </style>
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Inventory Report</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Inventory Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">

                <!-- Printed-only header (shown only when printing to PDF) -->
                <div class="print-header">
                    <h3>Malee Dress Point — Inventory Report</h3>
                    <p>Generated on <?php echo date('Y-m-d H:i'); ?></p>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-tabs no-print mb-3" id="reportTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stockTabPane" type="button" role="tab">
                            Stock Levels
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#historyTabPane" type="button" role="tab">
                            Movement History
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="reportTabsContent">

                    <!-- ================= Stock Levels Tab ================= -->
                    <div class="tab-pane fade show active" id="stockTabPane" role="tabpanel">

                        <!-- Summary cards -->
                        <div class="row mb-3" id="stockSummaryCards">
                            <div class="col-md-3">
                                <div class="card text-bg-primary">
                                    <div class="card-body">
                                        <h6 class="card-title mb-1">Total Products</h6>
                                        <h4 id="sum_totalProducts">-</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-bg-secondary">
                                    <div class="card-body">
                                        <h6 class="card-title mb-1">Total Quantity</h6>
                                        <h4 id="sum_totalQuantity">-</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-bg-success">
                                    <div class="card-body">
                                        <h6 class="card-title mb-1">In Stock</h6>
                                        <h4 id="sum_okStock">-</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-bg-danger">
                                    <div class="card-body">
                                        <h6 class="card-title mb-1">Low Stock</h6>
                                        <h4 id="sum_lowStock">-</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="card no-print mb-3">
                            <div class="card-body">
                                <form id="stockFilterForm" class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label">Category</label>
                                        <select class="form-select" name="category" id="stock_category">
                                            <option value="">All Categories</option>
                                            <?php foreach ($categories as $cat) : ?>
                                                <option value="<?php echo htmlspecialchars($cat['categoryid']); ?>">
                                                    <?php echo htmlspecialchars($cat['categoryName']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Stock Status</label>
                                        <select class="form-select" name="status" id="stock_status">
                                            <option value="">All</option>
                                            <option value="low">Low Stock Only</option>
                                            <option value="ok">In Stock Only</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" id="stockFilterBtn" class="btn btn-primary">Apply Filter</button>
                                        <button type="button" id="stockResetBtn" class="btn btn-outline-secondary">Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 no-print mb-2">
                            <button type="button" class="btn btn-outline-success btn-sm" id="exportStockExcelBtn">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" id="printStockBtn">
                                <i class="bi bi-file-earmark-pdf"></i> Export PDF (Print)
                            </button>
                        </div>

                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Reorder Level</th>
                                    <th>Stock Status</th>
                                    <th>Product Status</th>
                                </tr>
                            </thead>
                            <tbody id="stockReportBody">
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- ================= Movement History Tab ================= -->
                    <div class="tab-pane fade" id="historyTabPane" role="tabpanel">

                        <!-- Summary cards -->
                        <div class="row mb-3" id="historySummaryCards">
                            <div class="col-md-4">
                                <div class="card text-bg-primary">
                                    <div class="card-body">
                                        <h6 class="card-title mb-1">Total Movements</h6>
                                        <h4 id="sum_totalMovements">-</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-bg-success">
                                    <div class="card-body">
                                        <h6 class="card-title mb-1">Total Stock In</h6>
                                        <h4 id="sum_totalIn">-</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-bg-danger">
                                    <div class="card-body">
                                        <h6 class="card-title mb-1">Total Stock Out</h6>
                                        <h4 id="sum_totalOut">-</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="card no-print mb-3">
                            <div class="card-body">
                                <form id="historyFilterForm" class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label">From Date</label>
                                        <input type="date" class="form-control" name="dateFrom" id="history_dateFrom">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">To Date</label>
                                        <input type="date" class="form-control" name="dateTo" id="history_dateTo">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Movement Type</label>
                                        <select class="form-select" name="movementType" id="history_type">
                                            <option value="">All Types</option>
                                            <option value="IN">Stock In</option>
                                            <option value="OUT">Stock Out</option>
                                            <option value="ADJUSTMENT">Adjustment</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" id="historyFilterBtn" class="btn btn-primary">Apply Filter</button>
                                        <button type="button" id="historyResetBtn" class="btn btn-outline-secondary">Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 no-print mb-2">
                            <button type="button" class="btn btn-outline-success btn-sm" id="exportHistoryExcelBtn">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel
                            </button>
                            <!-- <button type="button" class="btn btn-outline-danger btn-sm" id="printHistoryBtn">
                                <i class="bi bi-file-earmark-pdf"></i> Export PDF (Print)
                            </button> -->
                        </div>

                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Change</th>
                                    <th>Before</th>
                                    <th>After</th>
                                    <th>Reason</th>
                                    <th>By</th>
                                </tr>
                            </thead>
                            <tbody id="historyReportBody">
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <script src="../../js/jquery.js"></script>

    <script>
        $(document).ready(function() {

            loadStockReport();

            // Load history when the History tab is first opened
            $('#history-tab').on('shown.bs.tab', function() {
                if (!$(this).data('loaded')) {
                    loadHistoryReport();
                    $(this).data('loaded', true);
                }
            });

            // ---------------- Stock Levels tab ----------------

            function loadStockReport() {
                $.ajax({
                    url: "../routes/inventory/viewstockreport.php",
                    type: 'GET',
                    data: {
                        category: $('#stock_category').val(),
                        status: $('#stock_status').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        var items = response.items || [];
                        var summary = response.summary || {};

                        $('#sum_totalProducts').text(summary.total_products || 0);
                        $('#sum_totalQuantity').text(summary.total_quantity || 0);
                        $('#sum_okStock').text(summary.ok_stock_count || 0);
                        $('#sum_lowStock').text(summary.low_stock_count || 0);

                        var rows = '';
                        if (!items || items.length === 0) {
                            rows = '<tr><td colspan="7" class="text-center">No products found</td></tr>';
                        } else {
                            $.each(items, function(i, item) {
                                var isLow = parseInt(item.quantity) <= parseInt(item.reorder_level);
                                var stockLabel = isLow ?
                                    '<span class="badge bg-danger">Low Stock</span>' :
                                    '<span class="badge bg-success">In Stock</span>';
                                var statusLabel = item.d_status == 1 ?
                                    '<span class="badge bg-success">Active</span>' :
                                    '<span class="badge bg-secondary">Inactive</span>';

                                rows += '<tr>' +
                                    '<td>' + item.productid + '</td>' +
                                    '<td>' + item.productName + '</td>' +
                                    '<td>' + item.categoryName + '</td>' +
                                    '<td>' + item.quantity + '</td>' +
                                    '<td>' + item.reorder_level + '</td>' +
                                    '<td>' + stockLabel + '</td>' +
                                    '<td>' + statusLabel + '</td>' +
                                    '</tr>';
                            });
                        }
                        $('#stockReportBody').html(rows);
                    }
                });
            }

            $('#stockFilterBtn').on('click', loadStockReport);
            $('#stockResetBtn').on('click', function() {
                $('#stockFilterForm')[0].reset();
                loadStockReport();
            });

            $('#printStockBtn').on('click', function() {
                window.print();
            });

            $('#exportStockExcelBtn').on('click', function() {
                var params = $.param({
                    category: $('#stock_category').val(),
                    status: $('#stock_status').val()
                });
                window.location.href = "../routes/inventory/export_inventory_stock_report.php?" + params;
            });

            // ---------------- Movement History tab ----------------

            function loadHistoryReport() {
                $.ajax({
                    url: "../routes/inventory/viewmovementreport.php",
                    type: 'GET',
                    data: {
                        dateFrom: $('#history_dateFrom').val(),
                        dateTo: $('#history_dateTo').val(),
                        movementType: $('#history_type').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        var movements = response.items || [];
                        var summary = response.summary || {};

                        $('#sum_totalMovements').text(summary.total_movements || 0);
                        $('#sum_totalIn').text(summary.total_in || 0);
                        $('#sum_totalOut').text(summary.total_out || 0);

                        var rows = '';
                        if (!movements || movements.length === 0) {
                            rows = '<tr><td colspan="8" class="text-center">No stock movements found</td></tr>';
                        } else {
                            $.each(movements, function(i, m) {
                                var changeLabel = m.quantity_change > 0 ? ('+' + m.quantity_change) : m.quantity_change;
                                rows += '<tr>' +
                                    '<td>' + m.created_at + '</td>' +
                                    '<td>' + m.productName + '</td>' +
                                    '<td>' + m.movement_type + '</td>' +
                                    '<td>' + changeLabel + '</td>' +
                                    '<td>' + m.previous_quantity + '</td>' +
                                    '<td>' + m.new_quantity + '</td>' +
                                    '<td>' + (m.reason || '-') + '</td>' +
                                    '<td>' + (m.created_by || '-') + '</td>' +
                                    '</tr>';
                            });
                        }
                        $('#historyReportBody').html(rows);
                    }
                });
            }

            $('#historyFilterBtn').on('click', loadHistoryReport);
            $('#historyResetBtn').on('click', function() {
                $('#historyFilterForm')[0].reset();
                loadHistoryReport();
            });

            $('#printHistoryBtn').on('click', function() {
                window.print();
            });

            $('#exportHistoryExcelBtn').on('click', function() {
                var params = $.param({
                    dateFrom: $('#history_dateFrom').val(),
                    dateTo: $('#history_dateTo').val(),
                    movementType: $('#history_type').val()
                });
                window.location.href = "../routes/inventory/export_inventory_movement_report.php?" + params;
            });

        });
    </script>

    <?php
    include_once('footer.php')
    ?>