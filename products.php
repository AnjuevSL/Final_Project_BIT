<?php
require_once 'lib/function/productfunction.php';
require_once 'lib/function/categoryfunction.php';

// Helper — escapes output for XSS protection
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Fetch active products
$productObj  = new Product;
$allProducts = $productObj->getActiveProducts();

// ---- Category filter (from navbar dropdown link: products.php?category=CAT004) ----
$selectedCategoryId   = isset($_GET['category']) ? trim($_GET['category']) : '';
$selectedCategoryName = '';

if ($selectedCategoryId !== '') {
    $allProducts = array_values(array_filter($allProducts, function ($p) use ($selectedCategoryId) {
        return isset($p['category']) && $p['category'] === $selectedCategoryId;
    }));

    // Look up the category name for the page heading
    $categoryObj = new Category();
    $categoryRow = $categoryObj->getCategoryById($selectedCategoryId);
    if ($categoryRow) {
        $selectedCategoryName = $categoryRow['categoryName'];
    }
}

// ---- Simple pagination (12 products per page) ----
$perPage    = 12;
$totalItems = count($allProducts);
$totalPages = max(1, (int) ceil($totalItems / $perPage));

$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;
if ($currentPage > $totalPages) $currentPage = $totalPages;

$offset   = ($currentPage - 1) * $perPage;
$products = array_slice($allProducts, $offset, $perPage);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products — Boutique Store</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="css/fontawesome-free-7.1.0-web/css/all.min.css">
</head>

<body>

    <?php include_once 'navbar.php'; ?>

    <!-- Cart trigger button -->
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

    <!-- Page Header -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">
            <?= $selectedCategoryName !== '' ? e($selectedCategoryName) : 'All Products' ?>
        </h2>

        <?php if ($selectedCategoryId !== ''): ?>
            <p class="text-center mb-4">
                <a href="products.php" class="text-decoration-none">&larr; Back to all products</a>
            </p>
        <?php endif; ?>

        <?php if ($totalItems > 0): ?>
            <p class="text-center text-muted mb-4">
                Showing <?= e((string)($offset + 1)) ?>–<?= e((string) min($offset + $perPage, $totalItems)) ?>
                of <?= e((string) $totalItems) ?> products
            </p>
        <?php endif; ?>

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
                                <?php if ((int) $product['quantity'] > 0): ?>
                                    <button
                                        type="button"
                                        class="btn btn-dark w-100"
                                        onclick="addToCart('<?= e($product['productid']) ?>', '<?= e(addslashes($product['productName'])) ?>', <?= (float) $product['price'] ?>, '<?= e($product['image']) ?>')">
                                        Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-secondary w-100" disabled>
                                        Out of Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <?php $catQuery = $selectedCategoryId !== '' ? '&category=' . urlencode($selectedCategoryId) : ''; ?>
            <nav aria-label="Product pagination" class="mt-4 mb-5">
                <ul class="pagination justify-content-center">

                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= e((string) max(1, $currentPage - 1)) ?><?= $catQuery ?>">Previous</a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= e((string) $i) ?><?= $catQuery ?>"><?= e((string) $i) ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= e((string) min($totalPages, $currentPage + 1)) ?><?= $catQuery ?>">Next</a>
                    </li>

                </ul>
            </nav>
        <?php endif; ?>

    </div>

    <?php include_once 'footer.php'; ?>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/cart.js"></script>
</body>

</html>