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

// Add a product to the cart (or increase quantity if it already exists)
function addToCart(id, name, price, image) {
    const cart = getCart();
    const existing = cart.find(item => item.id === id);

    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({ id, name, price: parseFloat(price), image, qty: 1 });
    }

    saveCart(cart);
    showToast(`${name} added to cart`);
    openCartSidebar();
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

// Increase quantity of a cart item
function increaseQty(id) {
    const cart = getCart();
    const item = cart.find(item => item.id === id);
    if (item) item.qty += 1;
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

// Render the cart sidebar contents
function renderCart() {
    const cart = getCart();
    const cartItemsEl = document.getElementById('cartItems');
    const cartTotalEl = document.getElementById('cartTotal');
    const cartBadge = document.getElementById('cartBadge');
    const cartEmptyEl = document.getElementById('cartEmpty');

    if (!cartItemsEl) return; // cart markup not on this page

    cartBadge.textContent = getCartCount();
    cartBadge.style.display = getCartCount() > 0 ? 'inline-block' : 'none';

    if (cart.length === 0) {
        cartItemsEl.innerHTML = '';
        cartEmptyEl.style.display = 'block';
        cartTotalEl.textContent = 'Rs.0.00';
        return;
    }

    cartEmptyEl.style.display = 'none';

    cartItemsEl.innerHTML = cart.map(item => `
        <div class="cart-item d-flex align-items-center mb-3">
            <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}" class="cart-item-img">
            <div class="cart-item-info flex-grow-1 ms-3">
                <p class="cart-item-name mb-1">${escapeHtml(item.name)}</p>
                <p class="cart-item-price mb-1">Rs.${item.price.toFixed(2)}</p>
                <div class="cart-qty-control d-flex align-items-center">
                    <button class="btn btn-sm btn-outline-secondary" onclick="decreaseQty('${item.id}')">−</button>
                    <span class="mx-2">${item.qty}</span>
                    <button class="btn btn-sm btn-outline-secondary" onclick="increaseQty('${item.id}')">+</button>
                </div>
            </div>
            <button class="btn-remove-item" onclick="removeFromCart('${item.id}')" title="Remove">&times;</button>
        </div>
    `).join('');

    cartTotalEl.textContent = 'Rs.' + getCartTotal().toFixed(2);
}

// Small toast notification when an item is added
function showToast(message) {
    let toast = document.getElementById('cartToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'cartToast';
        toast.className = 'cart-toast';
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2000);
}

// Initialize cart display on page load
document.addEventListener('DOMContentLoaded', renderCart);