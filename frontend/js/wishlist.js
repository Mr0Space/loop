document.addEventListener('DOMContentLoaded', function() {
});

window.removeFromWishlist = async function(btn) {
    const productId = btn.dataset.id;
    const card = btn.closest('.wishlist-card');
    
    try {
        const response = await fetch('/backend/wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                action: 'toggle', 
                product_id: parseInt(productId) 
            })
        });
        
        const data = await response.json();
        
        if (data.success && data.status === 'removed') {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                card.remove();
                if (document.querySelectorAll('.wishlist-card').length === 0) {
                    window.location.reload();
                }
            }, 300);
            
            showNotification('Товар удален из избранного');
            
            fetchCart();
        }
    } catch (error) {
        console.error('Remove from wishlist error:', error);
    }
};

window.addToCartFromWishlist = function(btn) {
    const productId = btn.dataset.id;
    if (productId) {
        addToCart(productId);
        
        btn.innerHTML = '<i class="fas fa-check"></i> Добавлено';
        btn.style.background = '#4a6b4a';
        
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-shopping-cart"></i> В корзину';
            btn.style.background = '';
        }, 2000);
    }
};