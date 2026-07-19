<?php
require_once 'lib/function/orderfunction.php';

function e($string)
{
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
    </div>

    <?php if ($order['payment_method'] === 'bank_transfer'):
                $slip = $orderObj->getSlipByOrderId($order['orderid']);
    ?>
        <div class="checkout-card mb-4">
            <h5 class="checkout-section-title">Bank Transfer Slip</h5>

            <?php if (!$slip): ?>
                <p class="text-danger">No slip uploaded yet. Please contact support.</p>

            <?php elseif ($slip['status'] === 'pending'): ?>
                <p><span class="badge bg-warning text-dark">Pending Review</span></p>
                <p class="text-muted small mb-0">
                    Your slip was uploaded on <?= e(date('d M Y, h:i A', strtotime($slip['uploaded_at']))) ?>
                    and is awaiting admin approval. We'll update your order status once it's verified.
                </p>

            <?php elseif ($slip['status'] === 'approved'): ?>
                <p><span class="badge bg-success">Payment Verified</span></p>
                <p class="text-muted small mb-0">Your bank transfer has been confirmed. Your order is now being processed.</p>

            <?php elseif ($slip['status'] === 'rejected'): ?>
                <p><span class="badge bg-danger">Slip Rejected</span></p>
                <?php if (!empty($slip['rejection_reason'])): ?>
                    <p class="text-muted small mb-2">
                        <strong>Reason:</strong> <?= e($slip['rejection_reason']) ?>
                    </p>
                <?php endif; ?>

                <p class="mb-2">Please upload a new slip to continue with this order.</p>

                <form id="reuploadSlipForm" enctype="multipart/form-data">
                    <input type="hidden" name="orderid" value="<?= e($order['orderid']) ?>">
                    <div class="mb-2">
                        <input type="file" name="payment_slip" id="reuploadSlipInput" class="form-control" accept="image/jpeg,image/png,image/jpg,application/pdf" required>
                        <small class="text-muted">Accepted formats: JPG, PNG, PDF — Max size 5MB</small>
                    </div>
                    <div id="reuploadSlipPreview" class="mb-2"></div>
                    <button type="submit" class="btn btn-dark btn-sm" id="reuploadSlipBtn">Upload New Slip</button>
                </form>
                <div id="reuploadSlipMessage" class="mt-2"></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

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
        <a href="shop.php" class="btn btn-outline-dark">Continue Shopping</a>
    </div>

<?php endif; ?>

</div>

<?php include_once 'footer.php'; ?>

<script src="js/jquery.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/cart.js"></script>
<script>
    // Order placed successfully — clear the cart from localStorage
    <?php if ($order): ?>
        clearCart();
    <?php endif; ?>

    // ===== Slip re-upload handling (only present when slip was rejected) =====
    $(document).ready(function() {
        var reuploadInput = document.getElementById('reuploadSlipInput');

        if (reuploadInput) {
            reuploadInput.addEventListener('change', function() {
                var preview = document.getElementById('reuploadSlipPreview');
                preview.innerHTML = '';
                var file = this.files[0];
                if (!file) return;

                if (file.size > 5 * 1024 * 1024) {
                    alert('File is too large. Max size is 5MB.');
                    this.value = '';
                    return;
                }

                if (file.type === 'application/pdf') {
                    preview.innerHTML = '<div>📄 ' + file.name + '</div>';
                } else if (file.type.startsWith('image/')) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = '<img src="' + e.target.result + '" style="max-width:150px;max-height:150px;border-radius:6px;border:1px solid #ddd;">';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        $('#reuploadSlipForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var $btn = $('#reuploadSlipBtn');
            $btn.prop('disabled', true).text('Uploading...');

            $.ajax({
                url: 'routes/order/reuploadslip.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#reuploadSlipMessage').html('<p class="text-success mb-0">New slip uploaded. Awaiting admin review.</p>');
                        $('#reuploadSlipForm').hide();
                    } else {
                        $('#reuploadSlipMessage').html('<p class="text-danger mb-0">' + (response.message || 'Upload failed. Please try again.') + '</p>');
                        $btn.prop('disabled', false).text('Upload New Slip');
                    }
                },
                error: function() {
                    $('#reuploadSlipMessage').html('<p class="text-danger mb-0">Something went wrong. Please try again.</p>');
                    $btn.prop('disabled', false).text('Upload New Slip');
                }
            });
        });
    });
</script>
</body>

</html>