document.addEventListener('DOMContentLoaded', function() {
    const deliveryOptions = document.querySelectorAll('input[name="delivery"]');
    const deliveryAddress = document.getElementById('deliveryAddress');
    
    function toggleAddress() {
        if (!deliveryAddress) return;
        
        const selected = document.querySelector('input[name="delivery"]:checked');
        if (selected && selected.value === 'courier') {
            deliveryAddress.style.display = 'block';
        } else {
            deliveryAddress.style.display = 'none';
        }
    }
    
    deliveryOptions.forEach(option => {
        option.addEventListener('change', toggleAddress);
    });
    
    toggleAddress();
    
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                if (value.length === 1) {
                    value = '+7 ' + value;
                } else if (value.length <= 4) {
                    value = '+7 (' + value.substring(1);
                } else if (value.length <= 7) {
                    value = '+7 (' + value.substring(1, 4) + ') ' + value.substring(4);
                } else if (value.length <= 9) {
                    value = '+7 (' + value.substring(1, 4) + ') ' + value.substring(4, 7) + '-' + value.substring(7);
                } else {
                    value = '+7 (' + value.substring(1, 4) + ') ' + value.substring(4, 7) + '-' + value.substring(7, 9) + '-' + value.substring(9, 11);
                }
                e.target.value = value;
            }
        });
    }
    
    loadCheckoutItems();
    
    deliveryOptions.forEach(option => {
        option.addEventListener('change', function() {
            updateDeliveryCost(this.value);
        });
    });
    
    autoFillUserData();
});

async function autoFillUserData() {
    try {
        const response = await fetch('/backend/auth.php?action=check');
        const data = await response.json();
        
        if (data.success && data.loggedIn) {
            console.log('Пользователь авторизован');
        }
    } catch (error) {
        console.error('Auth check error:', error);
    }
}

async function loadCheckoutItems() {
    try {
        const response = await fetch('/backend/cart.php?action=get');
        const data = await response.json();
        
        const container = document.querySelector('.order-items');
        const itemsCountSpan = document.querySelector('.items-count');
        const itemsSumSpan = document.querySelector('.items-sum');
        const totalAmountSpan = document.querySelector('.total-amount');
        
        if (!container) return;
        
        if (!data.success || !data.items || data.items.length === 0) {
            window.location.href = '/cart';
            return;
        }
        
        let html = '';
        let totalSum = 0;
        
        data.items.forEach(item => {
            const itemTotal = item.price * item.quantity;
            totalSum += itemTotal;
            
            html += `
                <div class="order-item">
                    <div class="order-item-image">
                        <img src="/${item.image_main || 'frontend/images/no-image.jpg'}" alt="${item.name}">
                    </div>
                    <div class="order-item-info">
                        <h4>${item.name}</h4>
                        <div class="order-item-meta">
                            <span class="order-item-quantity">${item.quantity} шт.</span>
                            <span class="order-item-price">${itemTotal.toLocaleString()} ₽</span>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
        
        if (itemsCountSpan) {
            const totalItems = data.items.reduce((sum, item) => sum + item.quantity, 0);
            itemsCountSpan.textContent = totalItems;
        }
        
        if (itemsSumSpan) itemsSumSpan.textContent = totalSum.toLocaleString() + ' ₽';
        
        updateDeliveryCost('courier');
        
    } catch (error) {
        console.error('Load checkout items error:', error);
    }
}

function updateDeliveryCost(method) {
    const deliveryCostElement = document.querySelector('.delivery-cost');
    const totalAmountElement = document.querySelector('.total-amount');
    const itemsSumElement = document.querySelector('.items-sum');
    
    if (!deliveryCostElement || !totalAmountElement || !itemsSumElement) return;
    
    const itemsSumText = itemsSumElement.textContent.replace(/[^\d]/g, '');
    const itemsTotal = parseInt(itemsSumText) || 0;
    
    let deliveryCost = 0;
    
    switch(method) {
        case 'courier': 
            deliveryCost = 300; 
            break;
        case 'pickup': 
            deliveryCost = 0; 
            break;
        case 'post': 
            deliveryCost = 250; 
            break;
        case 'sdek': 
            deliveryCost = 200; 
            break;
    }
    
    if (itemsTotal >= 5000 && method !== 'pickup') {
        deliveryCost = 0;
        deliveryCostElement.innerHTML = '0 ₽ <span style="color: #6b8a6b; font-size: 0.8rem;">(бесплатно)</span>';
    } else {
        deliveryCostElement.textContent = deliveryCost + ' ₽';
    }
    
    totalAmountElement.textContent = (itemsTotal + deliveryCost).toLocaleString() + ' ₽';
}

window.submitOrder = async function() {
    const required = ['firstName', 'lastName', 'phone', 'email'];
    let isValid = true;
    
    required.forEach(id => {
        const field = document.getElementById(id);
        if (!field || !field.value.trim()) {
            if (field) {
                field.style.borderColor = '#ff4444';
                field.style.backgroundColor = '#fff0f0';
            }
            isValid = false;
        } else if (field) {
            field.style.borderColor = '#e0e0e0';
            field.style.backgroundColor = '';
        }
    });
    
    const deliveryMethod = document.querySelector('input[name="delivery"]:checked')?.value;
    if (deliveryMethod === 'courier') {
        const addressFields = ['city', 'street', 'house'];
        addressFields.forEach(id => {
            const field = document.getElementById(id);
            if (!field || !field.value.trim()) {
                if (field) {
                    field.style.borderColor = '#ff4444';
                    field.style.backgroundColor = '#fff0f0';
                }
                isValid = false;
            }
        });
    }
    
    if (!isValid) {
        showNotification('Заполните все обязательные поля', 'error');
        return;
    }
    
    const formData = {
        firstName: document.getElementById('firstName')?.value,
        lastName: document.getElementById('lastName')?.value,
        phone: document.getElementById('phone')?.value,
        email: document.getElementById('email')?.value,
        delivery: deliveryMethod,
        payment: document.querySelector('input[name="payment"]:checked')?.value,
        address: deliveryMethod === 'courier' ? {
            city: document.getElementById('city')?.value,
            street: document.getElementById('street')?.value,
            house: document.getElementById('house')?.value,
            apartment: document.getElementById('apartment')?.value
        } : null,
        comment: document.querySelector('.order-comment')?.value
    };
    
    const submitBtn = document.querySelector('.submit-order-btn');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Оформляем...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('/backend/order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Заказ оформлен! Спасибо за покупку', 'success');
            
            const clearResponse = await fetch('/backend/cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'clear' })
});
const clearData = await clearResponse.json();
if (!clearData.success) {
    console.warn('Корзина не очистилась, но заказ создан');
}
            
            setTimeout(() => {
                window.location.href = '/order/success?id=' + data.order_id;
            }, 2000);
        } else {
            showNotification(data.message || 'Ошибка при оформлении заказа', 'error');
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    } catch (error) {
        console.error('Order error:', error);
        showNotification('Ошибка соединения', 'error');
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }
};