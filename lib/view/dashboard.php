<?php
session_start();

if (isset($_SESSION['user'])) {
    if (isset($_SESSION['usertype'])) {
        $usertype = $_SESSION['usertype'];
        if ($usertype != "Admin") {
            header('Location:../../login.php');
        }
    } else {
        header('Location:../../login.php');
    }
} else {
    header('Location:../../login.php');
}

// Load data directly - NO AJAX
include_once('../../lib/function/main.php');
$obj = new Main();

// Get stats
$sqlOrders = "SELECT COUNT(*) as count FROM orders_tbl";
$totalOrders = $obj->dbResult->query($sqlOrders)->fetch_assoc()['count'] ?? 0;

$sqlRevenue = "SELECT COALESCE(SUM(total), 0) as revenue FROM orders_tbl";
$totalRevenue = $obj->dbResult->query($sqlRevenue)->fetch_assoc()['revenue'] ?? 0;

$sqlProducts = "SELECT COUNT(*) as count FROM product_tbl WHERE d_status = 1";
$totalProducts = $obj->dbResult->query($sqlProducts)->fetch_assoc()['count'] ?? 0;

$sqlCustomers = "SELECT COUNT(*) as count FROM customer_tbl";
$totalCustomers = $obj->dbResult->query($sqlCustomers)->fetch_assoc()['count'] ?? 0;

// ---- Inventory stats (active products only) ----
// Out of Stock: quantity is 0
// Low Stock:    quantity > 0 but at/below the reorder level
// Available:    quantity above the reorder level
$sqlOutOfStock = "SELECT COUNT(*) as count FROM product_tbl WHERE d_status = 1 AND quantity <= 0";
$outOfStockCount = $obj->dbResult->query($sqlOutOfStock)->fetch_assoc()['count'] ?? 0;

$sqlLowStock = "SELECT COUNT(*) as count FROM product_tbl WHERE d_status = 1 AND quantity > 0 AND quantity <= reorder_level";
$lowStockCount = $obj->dbResult->query($sqlLowStock)->fetch_assoc()['count'] ?? 0;

$sqlAvailable = "SELECT COUNT(*) as count FROM product_tbl WHERE d_status = 1 AND quantity > reorder_level";
$availableCount = $obj->dbResult->query($sqlAvailable)->fetch_assoc()['count'] ?? 0;

// Get recent orders
$sqlRecentOrders = "SELECT orderid, customer_name, total, order_status, created_at FROM orders_tbl ORDER BY created_at DESC LIMIT 5";
$recentOrders = [];
$result = $obj->dbResult->query($sqlRecentOrders);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recentOrders[] = $row;
    }
}

// Get top products
$sqlTopProducts = "SELECT oi.product_name, COUNT(DISTINCT oi.orderid) as order_count, COALESCE(SUM(oi.line_total), 0) as total_revenue FROM order_items_tbl oi GROUP BY oi.product_name ORDER BY order_count DESC LIMIT 5";
$topProducts = [];
$result = $obj->dbResult->query($sqlTopProducts);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $topProducts[] = $row;
    }
}

// Get order status distribution
$statuses = ['pending', 'billing', 'ready_to_delivery', 'delivery', 'delivered', 'hold', 'cancelled'];
$statusCounts = [];
foreach ($statuses as $status) {
    $sql = "SELECT COUNT(*) as count FROM orders_tbl WHERE order_status = ?";
    $stmt = $obj->dbResult->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $statusCounts[ucwords(str_replace('_', ' ', $status))] = (int)$row['count'];
    }
}

// Get categories
$sqlCategories = "SELECT category, COUNT(*) as count FROM product_tbl WHERE d_status = 1 GROUP BY category ORDER BY count DESC";
$categories = [];
$result = $obj->dbResult->query($sqlCategories);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[$row['category']] = (int)$row['count'];
    }
}

// Inventory status breakdown (for the doughnut chart)
$inventoryStatus = [
    'Available'    => (int) $availableCount,
    'Low Stock'    => (int) $lowStockCount,
    'Out of Stock' => (int) $outOfStockCount,
];
?>
<!doctype html>
<html lang="en">

<head>
    <title>Dashboard - Boutique Store Admin</title>
    <?php include_once('common.php') ?>
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Dashboard</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">

                <!-- Stats Cards Row -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Orders</h6>
                                        <h3><?php echo $totalOrders; ?></h3>
                                    </div>
                                    <i class="bi bi-box-seam" style="font-size: 2rem; opacity: 0.5;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Revenue</h6>
                                        <h3>Rs. <?php echo number_format($totalRevenue, 2); ?></h3>
                                    </div>
                                    <i class="bi bi-cash-coin" style="font-size: 2rem; opacity: 0.5;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Products</h6>
                                        <h3><?php echo $totalProducts; ?></h3>
                                    </div>
                                    <i class="bi bi-bag" style="font-size: 2rem; opacity: 0.5;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Customers</h6>
                                        <h3><?php echo $totalCustomers; ?></h3>
                                    </div>
                                    <i class="bi bi-person" style="font-size: 2rem; opacity: 0.5;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Stats Cards Row -->
                <div class="row mb-4">
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Available Products</h6>
                                        <h3><?php echo $availableCount; ?></h3>
                                    </div>
                                    <i class="bi bi-check-circle-fill" style="font-size: 2rem; opacity: 0.5;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="card text-white bg-danger">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Out of Stock</h6>
                                        <h3><?php echo $outOfStockCount; ?></h3>
                                    </div>
                                    <i class="bi bi-x-circle-fill" style="font-size: 2rem; opacity: 0.5;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Low Stock</h6>
                                        <h3><?php echo $lowStockCount; ?></h3>
                                    </div>
                                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 2rem; opacity: 0.5;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Status Stats -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Order Status Distribution</h5>
                            </div>
                            <div class="card-body">
                                <div style="position: relative; height: 300px;">
                                    <canvas id="orderStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Category Distribution</h5>
                            </div>
                            <div class="card-body">
                                <div style="position: relative; height: 300px;">
                                    <canvas id="categoryChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Inventory Status</h5>
                                <a href="inventory.php" class="btn btn-sm btn-outline-secondary">View Inventory</a>
                            </div>
                            <div class="card-body">
                                <div style="position: relative; height: 300px;">
                                    <canvas id="inventoryStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title">Recent Orders</h5>
                                <a href="order.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (count($recentOrders) > 0) {
                                            foreach ($recentOrders as $order) {
                                                $statusColor = '';
                                                switch ($order['order_status']) {
                                                    case 'pending':
                                                        $statusColor = 'bg-secondary';
                                                        break;
                                                    case 'billing':
                                                        $statusColor = 'bg-warning';
                                                        break;
                                                    case 'ready_to_delivery':
                                                        $statusColor = 'bg-info';
                                                        break;
                                                    case 'delivery':
                                                        $statusColor = 'bg-primary';
                                                        break;
                                                    case 'delivered':
                                                        $statusColor = 'bg-success';
                                                        break;
                                                    case 'hold':
                                                        $statusColor = 'bg-danger';
                                                        break;
                                                    case 'cancelled':
                                                        $statusColor = 'bg-dark';
                                                        break;
                                                    default:
                                                        $statusColor = 'bg-secondary';
                                                }
                                                echo '<tr>';
                                                echo '<td><strong>' . htmlspecialchars($order['orderid']) . '</strong></td>';
                                                echo '<td>' . htmlspecialchars($order['customer_name']) . '</td>';
                                                echo '<td>Rs.' . number_format($order['total'], 2) . '</td>';
                                                echo '<td><span class="badge ' . $statusColor . '">' . ucfirst(str_replace('_', ' ', $order['order_status'])) . '</span></td>';
                                                echo '<td>' . date('Y-m-d', strtotime($order['created_at'])) . '</td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="5" class="text-center">No orders yet</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Top 5 Products</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Orders</th>
                                            <th>Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (count($topProducts) > 0) {
                                            foreach ($topProducts as $product) {
                                                echo '<tr>';
                                                echo '<td>' . htmlspecialchars($product['product_name']) . '</td>';
                                                echo '<td><span class="badge bg-info">' . $product['order_count'] . '</span></td>';
                                                echo '<td>Rs.' . number_format($product['total_revenue'], 2) . '</td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="3" class="text-center">No products ordered yet</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script src="../../js/jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        $(document).ready(function() {
            // Order Status Chart
            const statusData = <?php echo json_encode($statusCounts); ?>;
            const ctx1 = document.getElementById('orderStatusChart');
            if (ctx1) {
                new Chart(ctx1, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(statusData),
                        datasets: [{
                            data: Object.values(statusData),
                            backgroundColor: ['#6c757d', '#ffc107', '#17a2b8', '#007bff', '#28a745', '#dc3545', '#343a40']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Category Chart
            const categoryData = <?php echo json_encode($categories); ?>;
            const ctx2 = document.getElementById('categoryChart');
            if (ctx2) {
                new Chart(ctx2, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(categoryData),
                        datasets: [{
                            data: Object.values(categoryData),
                            backgroundColor: ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#00f2fe', '#fa709a', '#fee140']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Inventory Status Chart (Available / Low Stock / Out of Stock)
            const inventoryData = <?php echo json_encode($inventoryStatus); ?>;
            const ctx3 = document.getElementById('inventoryStatusChart');
            if (ctx3) {
                new Chart(ctx3, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(inventoryData),
                        datasets: [{
                            data: Object.values(inventoryData),
                            backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        });
    </script>

    <?php include_once('footer.php') ?>
</body>

</html>