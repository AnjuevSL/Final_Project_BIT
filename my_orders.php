<?php
session_start();

if (!(isset($_SESSION['user']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'Customer')) {
    header('Location: index.php');
    exit();
}

include_once('lib/function/customerfunction.php');
include_once('lib/function/reportfunction.php');

// Helper — escapes output for XSS protection
function e($string)
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

$custObj = new Customer();
$repObj  = new Report();

$customerJson = $custObj->loaddatabyid($_SESSION['user']);
$customer = $customerJson ? json_decode($customerJson, true) : null;

$orders = $custObj->getCustomerOrders($customer['customerid']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders — Boutique Store</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>

    <?php include_once 'navbar.php'; ?>

    <div class="container mt-5 mb-5">

        <h2 class="text-center mb-4">My Account</h2>

        <!-- Account nav tabs -->
        <ul class="nav nav-pills justify-content-center mb-4">
            <li class="nav-item">
                <a class="nav-link" href="profile.php">My Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="my_orders.php">My Orders</a>
            </li>
        </ul>

        <div class="checkout-card">

            <?php if (empty($orders)) : ?>
                <div class="p-10">
                    <h5 class="checkout-section-title text-center mb-3">My Orders</h5>
                    <p class="text-center text-muted">
                        You don't have any orders yet.
                        <a href="shop.php">Browse our products</a> and place your first order!
                    </p>
                </div>
            <?php else : ?>
                <h5 class="checkout-section-title mb-3">My Orders</h5>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total (Rs.)</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order) : ?>
                                <tr>
                                    <td>#<?= e($order['orderid']) ?></td>
                                    <td><?= date('Y-m-d', strtotime($order['created_at'])) ?></td>
                                    <td><?= number_format($order['total'], 2) ?></td>
                                    <td><?= e(ucwords(str_replace('_', ' ', $order['payment_method']))) ?></td>
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
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= e(ucwords(str_replace('_', ' ', $order['order_status']))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="order_view.php?orderid=<?= e($order['orderid']) ?>"
                                            class="btn btn-sm btn-outline-dark">
                                            View Order
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- Order Items Modal -->
    <div class="modal fade" id="orderItemsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderItemsBody">
                    Loading...
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'footer.php'; ?>

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Load order items into the modal
            $('.view-items-btn').on('click', function() {
                const orderid = $(this).data('orderid');
                $('#orderItemsBody').html('Loading...');
                $('#orderItemsModal').modal('show');

                $.ajax({
                    type: 'GET',
                    url: 'lib/routes/order/getOrderItems.php',
                    data: {
                        orderid: orderid
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success' && response.items.length > 0) {
                            let html = '<table class="table table-sm"><thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead><tbody>';
                            response.items.forEach(function(item) {
                                html += `<tr><td>${item.product_name}</td><td>${item.qty}</td><td>Rs.${parseFloat(item.price).toFixed(2)}</td><td>Rs.${parseFloat(item.line_total).toFixed(2)}</td></tr>`;
                            });
                            html += '</tbody></table>';
                            $('#orderItemsBody').html(html);
                        } else {
                            $('#orderItemsBody').html('<p class="text-muted text-center mb-0">No items found for this order.</p>');
                        }
                    },
                    error: function() {
                        $('#orderItemsBody').html('<p class="text-danger text-center mb-0">Failed to load items.</p>');
                    }
                });
            });
        });
    </script>
</body>

</html>