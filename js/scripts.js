function toggleCart() {
    const cartModal = document.querySelector('.cart-modal');
    cartModal.classList.toggle('open');
}

function addToCart(productId) {
    fetch('/add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Atualiza a contagem do carrinho silenciosamente
        }
    });
}

function removeFromCart(cartId) {
    fetch('/remove_from_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `cart_id=${cartId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function updateQuantity(cartId, quantity) {
    if (quantity < 1) return;
    fetch('/update_cart_quantity.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `cart_id=${cartId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function toggleDeliveryForm() {
    const deliveryOption = document.getElementById('deliveryOption');
    const deliveryForm = document.getElementById('deliveryForm');
    deliveryForm.classList.toggle('hidden', deliveryOption.value !== 'delivery');
}
