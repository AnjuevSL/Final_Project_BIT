<?php
require_once 'lib/function/orderfunction.php';

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

$orderid = $_GET['orderid'] ?? '';
$orderObj = new Order;
$order = $orderid !== '' ? $orderObj->getOrderById($orderid) : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed — Boutique Store</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/checkout.css">
</head>

<body>

    <?php include_once 'navbar.php'; ?>

    <div class="container mt-5 mb-5">

        <?php if (!$order): ?>
            <div class="text-center">
                <h3>Order not found</h3>
                <a href="index.php" class="btn btn-dark mt-3">Back to Shop</a>
            </div>
        <?php else: ?>

            <div class="checkout-card text-center mb-4">
                <h2 class="text-success">Thank you, <?= e($order['customer_name']) ?>!</h2>
                <p class="text-muted">Your order has been placed successfully.</p>
                <p><strong>Order ID:</strong> <?= e($order['orderid']) ?></p>
                <p><strong>Payment Method:</strong>
                    <?= $order['payment_method'] === 'cod' ? 'Cash on Delivery' : 'Bank Transfer' ?>
                </p>
                <p><strong>Status:</strong> <span class="text-capitalize"><?= e($order['order_status']) ?></span></p>
            </div>

            <div class="checkout-card">
                <h5 class="checkout-section-title">Order Summary</h5>

                <?php foreach ($order['items'] as $item): ?>
                    <div class="checkout-item d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <p class="mb-1 checkout-item-name"><?= e($item['product_name']) ?></p>
                            <p class="mb-0 text-muted small">
                                Qty: <?= (int) $item['qty'] ?> × Rs.<?= number_format($item['price'], 2) ?>
                            </p>
                        </div>
                        <span class="fw-bold">Rs.<?= number_format($item['line_total'], 2) ?></span>
                    </div>
                <?php endforeach; ?>

                <div class="order-summary-totals mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>Rs.<?= number_format($order['subtotal'], 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery Fee</span>
                        <span>Rs.<?= number_format($order['delivery_fee'], 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between order-total-row">
                        <span>Total</span>
                        <span>Rs.<?= number_format($order['total'], 2) ?></span>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-outline-dark">Continue Shopping</a>
            </div>

        <?php endif; ?>

    </div>

    <?php include_once 'footer.php'; ?>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/cart.js"></script>
    <script>
        // Order placed successfully — clear the cart from localStorage
        <?php if ($order): ?>
            clearCart();
        <?php endif; ?>
    </script>
</body>

</html>