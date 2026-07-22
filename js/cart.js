// cart.js — Shopping cart logic (localStorage based)

const CART_KEY = 'boutique_cart';

// Get cart from localStorage
function getCart() {
    const cart = localStorage.getItem(CART_KEY);
    return cart ? JSON.parse(cart) : [];
}

// Save cart to localStorage
function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
    renderCart();
}

// Add a product to the cart (or increase quantity if it already exists).
// `stock` is the total available quantity for this product — if omitted,
// no stock check is performed (kept optional so older calls don't break).
function addToCart(id, name, price, image, stock) {
    const cart = getCart();
    const existing = cart.find(item => item.id === id);
    const currentQtyInCart = existing ? existing.qty : 0;
    const availableStock = (stock !== undefined && stock !== null && stock !== '') ? parseInt(stock, 10) : Infinity;

    if (currentQtyInCart + 1 > availableStock) {
        showToast(`Sorry, only ${availableStock} unit(s) of "${name}" available — you already have ${currentQtyInCart} in your cart.`, true);
        return false;
    }

    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({ id, name, price: parseFloat(price), image, qty: 1 });
    }

    saveCart(cart);
    showToast(`${name} added to cart`);
    openCartSidebar();
    return true;
}

// Add a specific quantity at once (used by the product quick-view modal's
// quantity selector) — does the stock check once instead of looping
// addToCart() one unit at a time.
function addToCartWithQty(id, name, price, image, qty, stock) {
    qty = parseInt(qty, 10) || 1;

    const cart = getCart();
    const existing = cart.find(item => item.id === id);
    const currentQtyInCart = existing ? existing.qty : 0;
    const availableStock = (stock !== undefined && stock !== null && stock !== '') ? parseInt(stock, 10) : Infinity;

    if (currentQtyInCart + qty > availableStock) {
        const canAdd = Math.max(0, availableStock - currentQtyInCart);
        showToast(`Sorry, only ${availableStock} unit(s) of "${name}" available. You can add ${canAdd} more (already ${currentQtyInCart} in cart).`, true);
        return false;
    }

    if (existing) {
        existing.qty += qty;
    } else {
        cart.push({ id, name, price: parseFloat(price), image, qty });
    }

    saveCart(cart);
    showToast(`${name} (x${qty}) added to cart`);
    openCartSidebar();
    return true;
}

// Show the cart sidebar
function openCartSidebar() {
    document.getElementById('cartSidebar').classList.add('show');
    document.getElementById('cartBackdrop').classList.add('show');
}

// Hide the cart sidebar
function closeCartSidebar() {
    document.getElementById('cartSidebar').classList.remove('show');
    document.getElementById('cartBackdrop').classList.remove('show');
}

// Toggle the cart sidebar open/closed (used by the cart icon button)
function toggleCartSidebar() {
    const sidebar = document.getElementById('cartSidebar');
    if (sidebar.classList.contains('show')) {
        closeCartSidebar();
    } else {
        openCartSidebar();
    }
}

// Remove all items from the cart at once
function clearCart() {
    saveCart([]);
}

// Remove a product from the cart completely
function removeFromCart(id) {
    let cart = getCart();
    cart = cart.filter(item => item.id !== id);
    saveCart(cart);
}

// Increase quantity of a cart item.
// `maxStock` (optional) is the live available stock for this product —
// if the item is already at (or would exceed) that amount, refuse and warn.
function increaseQty(id, maxStock) {
    const cart = getCart();
    const item = cart.find(item => item.id === id);
    if (!item) return;

    const limit = (maxStock !== undefined && maxStock !== null) ? parseInt(maxStock, 10) : Infinity;

    if (item.qty + 1 > limit) {
        showToast(`Sorry, only ${limit} unit(s) of "${item.name}" available.`, true);
        return;
    }

    item.qty += 1;
    saveCart(cart);
}

// Decrease quantity of a cart item (removes it if it hits 0)
function decreaseQty(id) {
    let cart = getCart();
    const item = cart.find(item => item.id === id);
    if (item) {
        item.qty -= 1;
        if (item.qty <= 0) {
            cart = cart.filter(i => i.id !== id);
        }
    }
    saveCart(cart);
}

// Calculate cart total
function getCartTotal() {
    return getCart().reduce((sum, item) => sum + (item.price * item.qty), 0);
}

// Calculate total item count (for the cart badge)
function getCartCount() {
    return getCart().reduce((sum, item) => sum + item.qty, 0);
}

// Escape HTML to prevent XSS when injecting product names into the DOM
function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// Fetch current stock for every item in the cart from the server.
// Returns a Promise resolving to a map: { productid: { quantity, productName } }
function fetchStockForCart(cart) {
    const ids = cart.map(item => item.id);
    if (ids.length === 0) return Promise.resolve({});

    return fetch('lib/routes/product/checkstock.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ids })
    })
        .then(res => res.json())
        .catch(() => ({})); // fail silently — don't block cart rendering if the check fails
}

// Render the cart sidebar contents (async — checks live stock first)
async function renderCart() {
    const cart = getCart();
    const cartItemsEl = document.getElementById('cartItems');
    const cartTotalEl = document.getElementById('cartTotal');
    const cartBadge = document.getElementById('cartBadge');
    const cartEmptyEl = document.getElementById('cartEmpty');
    const cartWarningEl = document.getElementById('cartStockWarning');

    if (!cartItemsEl) return; // cart markup not on this page

    cartBadge.textContent = getCartCount();
    cartBadge.style.display = getCartCount() > 0 ? 'inline-block' : 'none';

    if (cart.length === 0) {
        cartItemsEl.innerHTML = '';
        cartEmptyEl.style.display = 'block';
        cartTotalEl.textContent = 'Rs.0.00';
        if (cartWarningEl) cartWarningEl.style.display = 'none';
        return;
    }

    cartEmptyEl.style.display = 'none';

    const stockMap = await fetchStockForCart(cart);
    let hasStockIssue = false;

    cartItemsEl.innerHTML = cart.map(item => {
        const stockInfo = stockMap[item.id];
        const available = stockInfo ? stockInfo.quantity : null;
        const overStock = available !== null && item.qty > available;
        if (overStock) hasStockIssue = true;

        const warningHtml = overStock
            ? `<p class="mb-1 small text-danger">Only ${available} in stock — reduce quantity</p>`
            : '';

        return `
        <div class="cart-item d-flex align-items-center mb-3">
            <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}" class="cart-item-img">
            <div class="cart-item-info flex-grow-1 ms-3">
                <p class="cart-item-name mb-1">${escapeHtml(item.name)}</p>
                <p class="cart-item-price mb-1">Rs.${item.price.toFixed(2)}</p>
                ${warningHtml}
                <div class="cart-qty-control d-flex align-items-center">
                    <button class="btn btn-sm btn-outline-secondary" onclick="decreaseQty('${item.id}')">−</button>
                    <span class="mx-2">${item.qty}</span>
                    <button class="btn btn-sm btn-outline-secondary" onclick="increaseQty('${item.id}', ${available !== null ? available : 'null'})">+</button>
                </div>
            </div>
            <button class="btn-remove-item" onclick="removeFromCart('${item.id}')" title="Remove">&times;</button>
        </div>
    `;
    }).join('');

    cartTotalEl.textContent = 'Rs.' + getCartTotal().toFixed(2);

    if (cartWarningEl) {
        cartWarningEl.style.display = hasStockIssue ? 'block' : 'none';
    }
}

// Small toast notification when an item is added (or a stock warning).
// Pass isError = true for a "can't do that" style message (styled inline
// so it works without needing a new class in cart.css).
function showToast(message, isError) {
    let toast = document.getElementById('cartToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'cartToast';
        toast.className = 'cart-toast';
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.style.backgroundColor = isError ? '#dc3545' : '';
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), isError ? 3500 : 2000);
}

// Initialize cart display on page load
document.addEventListener('DOMContentLoaded', renderCart);