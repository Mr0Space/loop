document.addEventListener('DOMContentLoaded', function() {
    loadCartItems();
});

async function loadCartItems() {
    try {
        const response = await fetch('/backend/cart.php?action=get');
        const data = await response.json();
        
        const cartContent = document.getElementById('cartContent');
        const cartEmpty = document.getElementById('cartEmpty');
        const cartItemsContainer = document.querySelector('.cart-items');
        const itemsCountSpan = document.querySelector('.items-count');
        const itemsSumSpan = document.querySelector('.items-sum');
        const totalSumSpan = document.querySelector('.total-sum');
        
        if (!cartItemsContainer) return;
        
        if (!data.success || !data.items || data.items.length === 0) {
            if (cartContent) cartContent.style.display = 'none';
            if (cartEmpty) cartEmpty.style.display = 'block';
            return;
        }
        
        if (cartEmpty) cartEmpty.style.display = 'none';
        if (cartContent) cartContent.style.display = 'grid';
        
        let html = '';
        let totalSum = 0;
        let totalItems = 0;
        
        data.items.forEach(item => {
            const itemTotal = item.price * item.quantity;
            totalSum += itemTotal;
            totalItems += item.quantity;
            
            html += `
                <div class="cart-item" data-product-id="${item.product_id}">
                    <div class="cart-item-image">
                        <img src="/${item.image_main || 'frontend/images/no-image.jpg'}" alt="${item.name}">
                    </div>
                    <div class="cart-item-info">
                        <h3 class="cart-item-title">${item.name}</h3>
                        <p class="cart-item-price">${item.price.toLocaleString()} ₽</p>
                        ${item.old_price ? `<span class="old-price">${item.old_price.toLocaleString()} ₽</span>` : ''}
                    </div>
                    <div class="cart-item-quantity">
                        <button class="quantity-btn minus" onclick="updateCartItemQuantity(${item.product_id}, -1)">−</button>
                        <input type="number" class="quantity-input" value="${item.quantity}" min="1" max="99" readonly data-id="${item.product_id}">
                        <button class="quantity-btn plus" onclick="updateCartItemQuantity(${item.product_id}, 1)">+</button>
                    </div>
                    <div class="cart-item-total">
                        <span class="total-price">${itemTotal.toLocaleString()} ₽</span>
                    </div>
                    <button class="cart-item-remove" onclick="removeCartItem(${item.product_id})" title="Удалить">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `;
        });
        
        cartItemsContainer.innerHTML = html;
        
        if (itemsCountSpan) itemsCountSpan.textContent = totalItems;
        if (itemsSumSpan) itemsSumSpan.textContent = totalSum.toLocaleString() + ' ₽';
        if (totalSumSpan) totalSumSpan.textContent = totalSum.toLocaleString() + ' ₽';
        
    } catch (error) {
        console.error('Load cart error:', error);
    }
}

window.updateCartItemQuantity = async function(productId, delta) {
    
    const cartItemsContainer = document.querySelector('.cart-items');
    if (!cartItemsContainer) return;
    
    const cartItem = cartItemsContainer.querySelector(`.cart-item[data-product-id="${productId}"]`);
    const input = cartItem?.querySelector('.quantity-input');
    
    if (!input) return;
    
    const currentValue = parseInt(input.value);
    const newValue = currentValue + delta;
    
    if (newValue < 1 || newValue > 99) return;
    
    const minusBtn = cartItem.querySelector('.quantity-btn.minus');
    const plusBtn = cartItem.querySelector('.quantity-btn.plus');
    if (minusBtn) minusBtn.disabled = true;
    if (plusBtn) plusBtn.disabled = true;
    
    try {
        const response = await fetch('/backend/cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                action: 'update', 
                product_id: productId, 
                quantity: newValue 
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            input.value = newValue;
            await loadCartItems();
            if (typeof window.loadMiniCart === 'function') {
                await window.loadMiniCart();
            }
        } else {
            if (typeof window.showNotification === 'function') {
                window.showNotification('Ошибка при обновлении', 'error');
            }
        }
    } catch (error) {
        console.error('Update quantity error:', error);
        if (typeof window.showNotification === 'function') {
            window.showNotification('Ошибка соединения', 'error');
        }
    } finally {
        if (minusBtn) minusBtn.disabled = false;
        if (plusBtn) plusBtn.disabled = false;
    }
};

window.removeCartItem = async function(productId) {
    const cartItem = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
    
    if (!cartItem) return;
    
    if (typeof window.removeFromCart !== 'function') {
        console.error('removeFromCart function not found');
        return;
    }
    
    const success = await window.removeFromCart(productId);
    
    if (success) {
        cartItem.style.transition = 'all 0.3s ease';
        cartItem.style.opacity = '0';
        cartItem.style.transform = 'translateX(20px)';
        
        setTimeout(async () => {
            await loadCartItems();
            if (typeof window.loadMiniCart === 'function') {
                await window.loadMiniCart();
            }
        }, 300);
    }
};