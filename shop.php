<?php
require_once 'lib/function/productfunction.php';

// Fetch active products from the database
$productObj = new Product;
$products = $productObj->getActiveProducts();

// ---- Search filter (from navbar search box: shop.php?search=frock) ----
// Matches against product name or product details, case-insensitive.
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($searchQuery !== '') {
    $products = array_values(array_filter($products, function ($p) use ($searchQuery) {
        $haystack = ($p['productName'] ?? '') . ' ' . ($p['productDetails'] ?? '');
        return stripos($haystack, $searchQuery) !== false;
    }));
}

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
            <div id="cartStockWarning" class="alert alert-danger py-2 px-3 small" style="display:none;">
                Some items in your cart exceed available stock. Please reduce the quantity before checking out.
            </div>
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

    <!-- ================= Product Quick View Modal ================= -->
    <div class="modal fade" id="productQuickViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qv_name">Product Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <img id="qv_image" src="" alt="" class="img-fluid rounded">
                        </div>
                        <div class="col-md-6">
                            <p id="qv_price" class="text-success fw-bold fs-4 mb-3">Rs.0.00</p>
                            <p id="qv_details" class="text-muted"></p>
                            <p id="qv_stock" class="small mb-3"></p>

                            <div id="qv_qty_section" class="mb-3">
                                <label class="form-label">Quantity</label>
                                <div class="input-group" style="max-width: 160px;">
                                    <button class="btn btn-outline-secondary" type="button" id="qv_qty_minus">−</button>
                                    <input type="number" class="form-control text-center" id="qv_qty_input" value="1" min="1">
                                    <button class="btn btn-outline-secondary" type="button" id="qv_qty_plus">+</button>
                                </div>
                            </div>

                            <button type="button" class="btn btn-dark w-100" id="qv_addToCartBtn">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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

        <h2 class="text-center mb-4">
            <?= $searchQuery !== '' ? 'Search results for "' . e($searchQuery) . '"' : 'Featured Products' ?>
        </h2>

        <?php if ($searchQuery !== ''): ?>
            <p class="text-center mb-4">
                <a href="shop.php" class="text-decoration-none">&larr; Clear search</a>
            </p>
        <?php endif; ?>

        <div class="row">
            <?php if (empty($products)): ?>
                <?php if ($searchQuery !== ''): ?>
                    <p class="text-center text-muted">No products found matching "<?= e($searchQuery) ?>".</p>
                <?php else: ?>
                    <p class="text-center text-muted">Products naha methanata. Admin panel eken add karanna.</p>
                <?php endif; ?>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card h-100 product-card">
                                <img src="<?= e($product['image']) ?>"
                                    class="card-img-top product-quickview-trigger"
                                    style="cursor:pointer"
                                    alt="<?= e($product['productName']) ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#productQuickViewModal"
                                    data-id="<?= e($product['productid']) ?>"
                                    data-name="<?= e($product['productName']) ?>"
                                    data-price="<?= (float) $product['price'] ?>"
                                    data-image="<?= e($product['image']) ?>"
                                    data-details="<?= e($product['productDetails']) ?>"
                                    data-qty="<?= (int) $product['quantity'] ?>">
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?= e($product['productName']) ?></h5>
                                    <p class="card-text text-success fw-bold">
                                        Rs.<?= number_format($product['price'], 2) ?>
                                    </p>

                                    <?php if ((int) $product['quantity'] > 0): ?>
                                        <button
                                            type="button"
                                            class="btn btn-dark w-100"
                                            onclick="addToCart('<?= e($product['productid']) ?>', '<?= e(addslashes($product['productName'])) ?>', <?= (float) $product['price'] ?>, '<?= e($product['image']) ?>', <?= (int) $product['quantity'] ?>)">
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

    </div>

    <?php include_once 'footer.php'; ?>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/cart.js"></script>
    <script>
        // ---------------- Product Quick View Modal ----------------
        (function() {
            const modalEl = document.getElementById('productQuickViewModal');
            const qtyInput = document.getElementById('qv_qty_input');
            let currentProduct = null;

            modalEl.addEventListener('show.bs.modal', function(event) {
                const trigger = event.relatedTarget;
                if (!trigger) return;

                currentProduct = {
                    id: trigger.getAttribute('data-id'),
                    name: trigger.getAttribute('data-name'),
                    price: parseFloat(trigger.getAttribute('data-price')),
                    image: trigger.getAttribute('data-image'),
                    details: trigger.getAttribute('data-details'),
                    stock: parseInt(trigger.getAttribute('data-qty'), 10)
                };

                document.getElementById('qv_name').textContent = currentProduct.name;
                document.getElementById('qv_image').src = currentProduct.image;
                document.getElementById('qv_image').alt = currentProduct.name;
                document.getElementById('qv_price').textContent = 'Rs.' + currentProduct.price.toFixed(2);
                document.getElementById('qv_details').textContent = currentProduct.details || '';

                const stockEl = document.getElementById('qv_stock');
                const qtySection = document.getElementById('qv_qty_section');
                const addBtn = document.getElementById('qv_addToCartBtn');

                if (currentProduct.stock > 0) {
                    stockEl.innerHTML = '<span class="text-success">In Stock</span> (' + currentProduct.stock + ' available)';
                    qtySection.style.display = 'block';
                    addBtn.disabled = false;
                    addBtn.textContent = 'Add to Cart';
                    qtyInput.value = 1;
                    qtyInput.max = currentProduct.stock;
                } else {
                    stockEl.innerHTML = '<span class="text-danger">Out of Stock</span>';
                    qtySection.style.display = 'none';
                    addBtn.disabled = true;
                    addBtn.textContent = 'Out of Stock';
                }
            });

            document.getElementById('qv_qty_minus').addEventListener('click', function() {
                const val = Math.max(1, (parseInt(qtyInput.value, 10) || 1) - 1);
                qtyInput.value = val;
            });

            document.getElementById('qv_qty_plus').addEventListener('click', function() {
                const max = currentProduct ? currentProduct.stock : 999;
                const val = Math.min(max, (parseInt(qtyInput.value, 10) || 1) + 1);
                qtyInput.value = val;
            });

            qtyInput.addEventListener('input', function() {
                if (!currentProduct) return;
                let val = parseInt(qtyInput.value, 10);
                if (isNaN(val)) return; // let them keep typing
                if (val > currentProduct.stock) qtyInput.value = currentProduct.stock;
            });

            qtyInput.addEventListener('change', function() {
                let val = parseInt(qtyInput.value, 10) || 1;
                if (val < 1) val = 1;
                if (currentProduct && val > currentProduct.stock) val = currentProduct.stock;
                qtyInput.value = val;
            });

            document.getElementById('qv_addToCartBtn').addEventListener('click', function() {
                if (!currentProduct) return;
                const qty = parseInt(qtyInput.value, 10) || 1;

                const added = addToCartWithQty(
                    currentProduct.id,
                    currentProduct.name,
                    currentProduct.price,
                    currentProduct.image,
                    qty,
                    currentProduct.stock
                );

                if (added) {
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    modalInstance.hide();
                }
            });
        })();
    </script>
</body>

</html>