<?php
session_start();
if (isset($_SESSION['user']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == "Admin") {
    $currentpage = 'order_report.php';
} else {
    header('Location:../../index.php');
    exit();
}

// Adjust this include path to wherever your Report/Main class autoloading happens
include_once('../function/reportfunction.php');

$repObj = new Report();

// Read filters from GET
$status = $_GET['status'] ?? null;
$from   = $_GET['from'] ?? null;
$to     = $_GET['to'] ?? null;

$orders  = $repObj->getOrderReport($status, $from, $to);
$summary = $repObj->getOrderReportSummary($status, $from, $to);
?>
<!doctype html>
<html lang="en">

<head>
    <title>Order Report</title>
    <?php include_once('common.php'); ?>
</head>

<body>
    <main class="app-main">
        <div class="app-content-header mt-3">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Order Report</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">Order Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">

                <!-- Summary cards
        <div class="row mb-3">
          <div class="col-md-3 col-sm-6">
            <div class="card text-bg-primary">
              <div class="card-body">
                <h6 class="mb-1">Total Orders</h6>
                <h3 class="mb-0"><?php echo (int) $summary['total_orders']; ?></h3>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="card text-bg-success">
              <div class="card-body">
                <h6 class="mb-1">Total Revenue</h6>
                <h3 class="mb-0">Rs. <?php echo number_format($summary['total_revenue'], 2); ?></h3>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="card text-bg-warning">
              <div class="card-body">
                <h6 class="mb-1">Pending</h6>
                <h3 class="mb-0"><?php echo (int) $summary['pending_count']; ?></h3>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="card text-bg-danger">
              <div class="card-body">
                <h6 class="mb-1">Cancelled</h6>
                <h3 class="mb-0"><?php echo (int) $summary['cancelled_count']; ?></h3>
              </div>
            </div>
          </div>
        </div> -->

                <!-- Filters -->
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="GET" class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label mb-1">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">All Status</option>
                                    <?php
                                    $statusList = ['pending', 'billing', 'ready_to_delivery', 'delivery', 'delivered', 'hold', 'cancelled'];
                                    foreach ($statusList as $s) {
                                        $selected = ($status === $s) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($s) . '" ' . $selected . '>' . htmlspecialchars(ucwords(str_replace('_', ' ', $s))) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label mb-1">From Date</label>
                                <input type="date" name="from" class="form-control form-control-sm" value="<?php echo htmlspecialchars($from ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label mb-1">To Date</label>
                                <input type="date" name="to" class="form-control form-control-sm" value="<?php echo htmlspecialchars($to ?? ''); ?>">
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
                                <a href="order_report.php" class="btn btn-secondary btn-sm">Reset</a>

                                <!-- <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()"><i class="bi bi-printer"></i> Print</button> -->
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">All Orders</h4>
                            <input type="text" id="searchOrder" class="form-control form-control-sm" style="max-width:250px" placeholder="Search on this page...">
                            <a href="export_order_report.php?status=<?php echo urlencode($status ?? ''); ?>&from=<?php echo urlencode($from ?? ''); ?>&to=<?php echo urlencode($to ?? ''); ?>"
                                class="btn btn-success btn-sm ms-auto">
                                <i class="bi bi-file-earmark-excel"></i> Export to Excel
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer Name</th>
                                        <th>Phone</th>
                                        <th>City</th>
                                        <th>Payment</th>
                                        <th>Total (Rs.)</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody id="orderlist">
                                    <?php if (!empty($orders)) : ?>
                                        <?php foreach ($orders as $order) : ?>
                                            <tr>
                                                <td>#<?php echo htmlspecialchars($order['orderid']); ?></td>
                                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                                <td><?php echo htmlspecialchars($order['phone']); ?></td>
                                                <td><?php echo htmlspecialchars($order['city']); ?></td>
                                                <td><?php echo htmlspecialchars(ucwords($order['payment_method'])); ?></td>
                                                <td><?php echo number_format($order['total'], 2); ?></td>
                                                <td>
                                                    <?php
                                                    $badgeClass = match ($order['order_status']) {
                                                        'delivered' => 'bg-success',
                                                        'cancelled' => 'bg-danger',
                                                        'pending'   => 'bg-warning text-dark',
                                                        'hold'      => 'bg-secondary',
                                                        default     => 'bg-info text-dark',
                                                    };
                                                    ?>
                                                    <span class="badge <?php echo $badgeClass; ?>">
                                                        <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $order['order_status']))); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No orders found for the selected filters.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
    <script src="../../js/jquery.js"></script>
    <script>
        // Simple client-side search across the already-rendered table
        document.getElementById('searchOrder').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('#orderlist tr').forEach(function(row) {
                row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    </script>
    <?php include_once('footer.php'); ?>
</body>

</html>