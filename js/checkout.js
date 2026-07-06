// checkout.js — Order summary rendering + form submit handling

const DELIVERY_FEE = 350.00;

// Render the order summary panel using the cart from localStorage
function renderCheckoutSummary() {
    const cart = getCart();
    const itemsEl = document.getElementById('checkoutItems');
    const emptyEl = document.getElementById('checkoutEmpty');
    const subtotalEl = document.getElementById('checkoutSubtotal');
    const deliveryEl = document.getElementById('checkoutDelivery');
    const totalEl = document.getElementById('checkoutTotal');
    const placeOrderBtn = document.getElementById('placeOrderBtn');

    if (!itemsEl) return; // not on the checkout page

    if (cart.length === 0) {
        itemsEl.innerHTML = '';
        emptyEl.style.display = 'block';
        subtotalEl.textContent = 'Rs.0.00';
        totalEl.textContent = 'Rs.0.00';
        placeOrderBtn.disabled = true;
        return;
    }

    emptyEl.style.display = 'none';
    placeOrderBtn.disabled = false;

    itemsEl.innerHTML = cart.map(item => `
        <div class="checkout-item d-flex align-items-center mb-3">
            <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}" class="checkout-item-img">
            <div class="flex-grow-1 ms-3">
                <p class="mb-1 checkout-item-name">${escapeHtml(item.name)}</p>
                <p class="mb-0 text-muted small">Qty: ${item.qty} × Rs.${item.price.toFixed(2)}</p>
            </div>
            <span class="fw-bold">Rs.${(item.price * item.qty).toFixed(2)}</span>
        </div>
    `).join('');

    const subtotal = getCartTotal();
    const total = subtotal + DELIVERY_FEE;

    subtotalEl.textContent = 'Rs.' + subtotal.toFixed(2);
    deliveryEl.textContent = 'Rs.' + DELIVERY_FEE.toFixed(2);
    totalEl.textContent = 'Rs.' + total.toFixed(2);
}

// Before the form submits, attach the current cart as JSON so the server can process the order
function attachCartDataToForm() {
    const form = document.getElementById('checkoutForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const cart = getCart();

        if (cart.length === 0) {
            e.preventDefault();
            alert('Your cart is empty.');
            return;
        }

        document.getElementById('cartDataInput').value = JSON.stringify(cart);

        // Cart is cleared after the form actually submits to place_order.php
        // (place_order.php should redirect to an order-confirmation page)
    });
}

document.addEventListener('DOMContentLoaded', function () {
    renderCheckoutSummary();
    attachCartDataToForm();
});