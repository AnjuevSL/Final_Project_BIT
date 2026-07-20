<?php
require_once 'lib/function/productfunction.php';

// Fetch active products from the database
$productObj = new Product;
$products = $productObj->getActiveProducts();

// Helper — escapes output for XSS protection
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutique Store</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="css/fontawesome-free-7.1.0-web/css/all.min.css">
</head>

<body>

    <?php include_once 'navbar.php'; ?>

    <!-- Cart trigger button (move this into navbar.php next to your logo/menu) -->
    <button type="button" class="cart-icon-btn" onclick="toggleCartSidebar()">
        🛒
        <span id="cartBadge" class="cart-badge">0</span>
    </button>

    <!-- Backdrop (click to close) -->
    <div id="cartBackdrop" class="cart-backdrop" onclick="closeCartSidebar()"></div>

    <!-- Cart Sidebar (custom, no Bootstrap JS dependency) -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-sidebar-header">
            <h5>Your Cart</h5>
            <button type="button" class="btn-close" onclick="closeCartSidebar()"></button>
        </div>
        <div class="cart-sidebar-body">
            <div id="cartItems"></div>
            <p id="cartEmpty" class="text-center text-muted" style="display:none;">Your cart is empty.</p>
        </div>
        <div class="cart-footer">
            <div class="d-flex justify-content-between mb-3">
                <span class="cart-total-label">Total</span>
                <span id="cartTotal" class="cart-total-label">Rs.0.00</span>
            </div>
            <a href="checkout.php" class="btn btn-dark w-100 mb-2">Checkout</a>
            <button type="button" class="btn btn-outline-danger w-100 btn-sm" onclick="clearCart()">Clear Cart</button>
        </div>
    </div>

    <!-- Banner Carousel -->
    <div id="carouselIndicators" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselIndicators" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#carouselIndicators" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#carouselIndicators" data-bs-slide-to="2"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/Banner Image 1.webp" class="d-block w-100 banner-img" alt="Banner 1">
                <div class="carousel-caption">
                    <h1>Explore Our Fashion Collection</h1>
                </div>
            </div>
            <div class="carousel-item">
                <img src="assets/Banner Image 2.webp" class="d-block w-100 banner-img" alt="Banner 2">
                <div class="carousel-caption">
                    <h1>Discover New Trends</h1>
                </div>
            </div>
            <div class="carousel-item">
                <img src="assets/Banner Image 3.webp" class="d-block w-100 banner-img" alt="Banner 3">
                <div class="carousel-caption">
                    <h1>Shop Your Style</h1>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Featured Products -->
    <div class="container mt-5">

        <h2 class="text-center mb-4">Featured Products</h2>

        <div class="row">
            <?php if (empty($products)): ?>
                <p class="text-center text-muted">Products naha methanata. Admin panel eken add karanna.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card h-100 product-card">
                            <img src="<?= e($product['image']) ?>" class="card-img-top" alt="<?= e($product['productName']) ?>">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= e($product['productName']) ?></h5>
                                <p class="card-text text-success fw-bold">
                                    Rs.<?= number_format($product['price'], 2) ?>
                                </p>
                                <button
                                    type="button"
                                    class="btn btn-dark w-100"
                                    onclick="addToCart('<?= e($product['productid']) ?>', '<?= e(addslashes($product['productName'])) ?>', <?= (float) $product['price'] ?>, '<?= e($product['image']) ?>')">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

    <?php include_once 'footer.php'; ?>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/cart.js"></script>
</body>

</html>