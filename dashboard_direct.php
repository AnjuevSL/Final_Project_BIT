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
?>
<!doctype html>
<html lang="en">

<head>
    <title>Dashboard - Boutique Store Admin</title>
    <?php include_once('common.php') ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
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
                                        <h3 id="totalOrders">0</h3>
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
                                        <h3 id="totalRevenue">Rs. 0</h3>
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
                                        <h3 id="totalProducts">0</h3>
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
                                        <h3 id="totalCustomers">0</h3>
                                    </div>
                                    <i class="bi bi-person" style="font-size: 2rem; opacity: 0.5;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Status Stats -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Order Status Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="orderStatusChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Monthly Revenue</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="revenueChart"></canvas>
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
                                    <tbody id="recentOrdersTableBody">
                                        <tr>
                                            <td colspan="5" class="text-center">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Products & Categories -->
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
                                    <tbody id="topProductsTableBody">
                                        <tr>
                                            <td colspan="3" class="text-center">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Category Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../../js/jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
    <script>
        $(document).ready(function() {
            loadDashboardStats();
            loadRecentOrders();
            loadTopProducts();
            loadCharts();
        });

        function loadDashboardStats() {
            $.get("../routes/dashboard/stats.php", function(data) {
                let stats = JSON.parse(data);
                $('#totalOrders').text(stats.totalOrders);
                $('#totalRevenue').text('Rs. ' + parseFloat(stats.totalRevenue).toFixed(2));
                $('#totalProducts').text(stats.totalProducts);
                $('#totalCustomers').text(stats.totalCustomers);
            });
        }

        function loadRecentOrders() {
            $.get("../routes/dashboard/recentorders.php", function(data) {
                let orders = JSON.parse(data);
                let rows = '';

                if (orders.length === 0) {
                    rows = '<tr><td colspan="5" class="text-center">No orders yet</td></tr>';
                } else {
                    $.each(orders, function(index, order) {
                        let statusColor = getStatusColor(order.order_status);
                        rows += '<tr>';
                        rows += '<td><strong>' + order.orderid + '</strong></td>';
                        rows += '<td>' + order.customer_name + '</td>';
                        rows += '<td>Rs.' + parseFloat(order.total).toFixed(2) + '</td>';
                        rows += '<td><span class="badge ' + statusColor + '">' + formatStatus(order.order_status) + '</span></td>';
                        rows += '<td>' + new Date(order.created_at).toLocaleDateString() + '</td>';
                        rows += '</tr>';
                    });
                }

                $('#recentOrdersTableBody').html(rows);
            });
        }

        function loadTopProducts() {
            $.get("../routes/dashboard/topproducts.php", function(data) {
                let products = JSON.parse(data);
                let rows = '';

                if (products.length === 0) {
                    rows = '<tr><td colspan="3" class="text-center">No data yet</td></tr>';
                } else {
                    $.each(products, function(index, product) {
                        rows += '<tr>';
                        rows += '<td>' + product.product_name + '</td>';
                        rows += '<td><span class="badge bg-info">' + product.order_count + '</span></td>';
                        rows += '<td>Rs.' + parseFloat(product.total_revenue).toFixed(2) + '</td>';
                        rows += '</tr>';
                    });
                }

                $('#topProductsTableBody').html(rows);
            });
        }

        function loadCharts() {
            // Order Status Chart
            $.get("../routes/dashboard/orderstatus.php", function(data) {
                let statusData = JSON.parse(data);

                const ctx = document.getElementById('orderStatusChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(statusData),
                        datasets: [{
                            data: Object.values(statusData),
                            backgroundColor: [
                                '#6c757d',
                                '#ffc107',
                                '#17a2b8',
                                '#007bff',
                                '#28a745',
                                '#dc3545',
                                '#343a40'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            });

            // Category Chart
            $.get("../routes/dashboard/categories.php", function(data) {
                let categoryData = JSON.parse(data);

                const ctx = document.getElementById('categoryChart').getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(categoryData),
                        datasets: [{
                            data: Object.values(categoryData),
                            backgroundColor: [
                                '#667eea',
                                '#764ba2',
                                '#f093fb',
                                '#4facfe',
                                '#00f2fe',
                                '#fa709a',
                                '#fee140'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            });

            // Revenue Chart
            $.get("../routes/dashboard/revenue.php", function(data) {
                let revenueData = JSON.parse(data);

                const ctx = document.getElementById('revenueChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: Object.keys(revenueData),
                        datasets: [{
                            label: 'Revenue',
                            data: Object.values(revenueData),
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        }

        function getStatusColor(status) {
            const colorMap = {
                'pending': 'bg-secondary',
                'billing': 'bg-warning',
                'ready_to_delivery': 'bg-info',
                'delivery': 'bg-primary',
                'delivered': 'bg-success',
                'hold': 'bg-danger',
                'cancelled': 'bg-dark'
            };
            return colorMap[status] || 'bg-secondary';
        }

        function formatStatus(status) {
            const statusMap = {
                'pending': 'Pending',
                'billing': 'Billing',
                'ready_to_delivery': 'Ready to Delivery',
                'delivery': 'Delivery',
                'delivered': 'Delivered',
                'hold': 'Hold',
                'cancelled': 'Cancelled'
            };
            return statusMap[status] || status;
        }
    </script>

    <?php include_once('footer.php') ?>
    </body>

</html>