<?php
session_start();
include_once('lib/function/customerfunction.php');

// Helper — escapes output for XSS protection
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// If a customer is logged in, pre-fill their saved details
$customer = null;

if (isset($_SESSION['user']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'Customer') {
    $custObj = new Customer();
    $customerJson = $custObj->loaddatabyid($_SESSION['user']);

    if ($customerJson) {
        $customer = json_decode($customerJson, true);
    }
    //     echo "<pre>";
    // print_r($customer);
    // exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — Boutique Store</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/checkout.css">
</head>

<body>

    <?php include_once 'navbar.php'; ?>

    <div class="container mt-5 mb-5">

        <h2 class="text-center mb-5">Checkout</h2>

        <div class="row g-4">

            <!-- ================= Delivery Details Form ================= -->
            <div class="col-lg-7">
                <div class="checkout-card">
                    <h5 class="checkout-section-title">Delivery Details</h5>

                    <form id="checkoutForm" action="place_order.php" method="POST">

                        <div class="row g-3">
                            <!-- <div class="col-md-6"> -->
                            <!-- <label class="form-label">Customer Id</label> -->
                            <input type="text" name="cusid" class="form-control" value="<?= e($customer['customerid'] ?? '') ?>" required hidden>
                            <!-- </div> -->
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="fullname" class="form-control" value="<?= e($customer['customerName'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" value="<?= e($customer['customerPhone'] ?? '') ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= e($customer['customerEmail'] ?? '') ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Delivery Address</label>
                                <textarea name="address" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Postal Code</label>
                                <input type="text" name="postal_code" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Order Notes (optional)</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="E.g. delivery instructions"></textarea>
                            </div>
                        </div>

                        <h5 class="checkout-section-title mt-4">Payment Method</h5>

                        <div class="payment-option">
                            <input type="radio" name="payment_method" id="cod" value="cod" checked>
                            <label for="cod">Cash on Delivery</label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" name="payment_method" id="bank" value="bank_transfer">
                            <label for="bank">Bank Transfer</label>
                        </div>

                        <!-- Cart data is injected here as JSON before submit (see checkout.js) -->
                        <input type="hidden" name="cart_data" id="cartDataInput">

                        <button type="submit" class="btn btn-dark w-100 mt-4" id="placeOrderBtn" disabled>
                            Place Order
                        </button>
                    </form>
                </div>
            </div>

            <!-- ================= Order Summary ================= -->
            <div class="col-lg-5">
                <div class="checkout-card order-summary">
                    <h5 class="checkout-section-title">Order Summary</h5>

                    <div id="checkoutItems"></div>
                    <p id="checkoutEmpty" class="text-center text-muted" style="display:none;">
                        Your cart is empty. <a href="shop.php">Continue shopping</a>.
                    </p>

                    <div class="order-summary-totals mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span id="checkoutSubtotal">Rs.0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery Fee</span>
                            <span id="checkoutDelivery">Rs.350.00</span>
                        </div>
                        <div class="d-flex justify-content-between order-total-row">
                            <span>Total</span>
                            <span id="checkoutTotal">Rs.0.00</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include_once 'footer.php'; ?>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/cart.js"></script>
    <script src="js/checkout.js"></script>
</body>

</html>