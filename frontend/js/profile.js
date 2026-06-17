document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('profile_phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', phoneMask);
    }
});

function phoneMask(e) {
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
}

async function cancelOrder(orderId) {
    if (!confirm('Вы уверены, что хотите отменить заказ?')) {
        return;
    }
    
    try {
        const response = await fetch('/backend/order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                action: 'cancel', 
                order_id: orderId 
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Заказ отменен');
            
            const orderCard = document.getElementById(`order-${orderId}`);
            const statusSpan = orderCard.querySelector('.order-status');
            const cancelBtn = orderCard.querySelector('.order-cancel-btn');
            
            statusSpan.textContent = 'cancelled';
            statusSpan.className = 'order-status status-cancelled';
            
            if (cancelBtn) {
                cancelBtn.remove();
            }
        } else {
            showNotification(data.message || 'Ошибка при отмене заказа', 'error');
        }
    } catch (error) {
        console.error('Cancel order error:', error);
        showNotification('Ошибка соединения', 'error');
    }
}

window.cancelOrder = cancelOrder;