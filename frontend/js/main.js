window.API_BASE = '/backend/';

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: ${type === 'success' ? '#6b8a6b' : '#b9a99a'};
        color: white;
        padding: 12px 24px;
        border-radius: 40px;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        animation: slideUp 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideDown 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 2000);
}

const styleElement = document.createElement('style');
styleElement.textContent = `
    @keyframes slideUp {
        from { opacity: 0; transform: translate(-50%, 20px); }
        to { opacity: 1; transform: translate(-50%, 0); }
    }
    @keyframes slideDown {
        from { opacity: 1; transform: translate(-50%, 0); }
        to { opacity: 0; transform: translate(-50%, 20px); }
    }
`;
document.head.appendChild(styleElement);

window.copyToClipboard = function(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Скопировано: ' + text);
    }).catch(() => {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showNotification('Скопировано: ' + text);
    });
};

function changeWishlistButtons(productId, isInWishlist) {
    const buttons = document.querySelectorAll(`.add-to-favorite[data-id="${productId}"], .add-to-favorite-btn[data-id="${productId}"], .add-to-favorite-detail[data-id="${productId}"]`);
    
    buttons.forEach(btn => {
        const icon = btn.querySelector('i');
        if (isInWishlist) {
            icon.classList.remove('far');
            icon.classList.add('fas');
            btn.classList.add('active');
            btn.title = 'Удалить из избранного';
        } else {
            icon.classList.remove('fas');
            icon.classList.add('far');
            btn.classList.remove('active');
            btn.title = 'Добавить в избранное';
        }
    });
}

function changeAddToCartButtons(productId, isInCart) {
    const buttons = document.querySelectorAll(`.add-to-cart[data-id="${productId}"], .add-to-cart-btn[data-id="${productId}"], .add-to-cart-detail[data-id="${productId}"]`);
    
    buttons.forEach(btn => {
        if (isInCart) {
            btn.innerHTML = '<i class="fas fa-check"></i> В корзине';
            btn.classList.add('in-cart');
        } else {
            btn.innerHTML = 'В корзину';
            btn.classList.remove('in-cart');
        }
    });
}

async function fetchCart() {
    try {
        const response = await fetch(window.API_BASE + 'cart.php?action=get');
        const data = await response.json();
        if (data.success) {
            updateCartCount(data.items ? data.items.length : 0);
            
            if (data.items) {
                document.querySelectorAll('.add-to-cart, .add-to-cart-btn, .add-to-cart-detail').forEach(btn => {
                    const productId = btn.dataset.id;
                    changeAddToCartButtons(productId, false);
                });
                
                data.items.forEach(item => {
                    changeAddToCartButtons(item.product_id, true);
                });
            }
            
            return data.items || [];
        }
    } catch (error) {
        console.error('Cart fetch error:', error);
    }
    return [];
}

function updateCartCount(count) {
    const badges = document.querySelectorAll('.cart-count');
    const cartCountText = document.querySelector('.cart-count-text');
    
    badges.forEach(badge => {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    });
    
    if (cartCountText) {
        cartCountText.textContent = count;
    }
}

let isAdding = false;

async function addToCart(productId, quantity = 1) {
    if (isAdding) return;
    isAdding = true;
    
    console.log('Adding to cart:', productId, 'quantity:', quantity);
    
    try {
        const response = await fetch(window.API_BASE + 'cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                action: 'add', 
                product_id: parseInt(productId), 
                quantity: parseInt(quantity)
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Товар добавлен в корзину');
            await fetchCart();
            await loadMiniCart();
            changeAddToCartButtons(productId, true);
        } else {
            showNotification('Ошибка при добавлении', 'error');
        }
    } catch (error) {
        console.error('Add to cart error:', error);
        showNotification('Ошибка соединения', 'error');
    } finally {
        setTimeout(() => {
            isAdding = false;
        }, 500);
    }
}

async function removeFromCart(productId) {
    try {
        const response = await fetch(window.API_BASE + 'cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                action: 'remove', 
                product_id: parseInt(productId) 
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Товар удален из корзины');
            await fetchCart();
            await loadMiniCart();
            changeAddToCartButtons(productId, false);
            return true;
        }
    } catch (error) {
        console.error('Remove from cart error:', error);
        showNotification('Ошибка при удалении', 'error');
    }
    return false;
}

async function updateCartQuantity(productId, quantity) {
    if (quantity < 1) return false;
    
    try {
        const response = await fetch(window.API_BASE + 'cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                action: 'update', 
                product_id: parseInt(productId), 
                quantity: parseInt(quantity) 
            })
        });
        
        const data = await response.json();
        return data.success;
    } catch (error) {
        console.error('Update quantity error:', error);
        return false;
    }
}

async function loadMiniCart() {
    try {
        const response = await fetch(window.API_BASE + 'cart.php?action=get');
        const data = await response.json();
        
        const cartItemsContainer = document.querySelector('.cart-items-dropdown');
        const cartCountText = document.querySelector('.cart-count-text');
        const cartTotalSpan = document.querySelector('.cart-total');
        const cartCount = document.querySelector('.cart-count');
        
        if (!cartItemsContainer) return;
        
        if (!data.success || !data.items || data.items.length === 0) {
            cartItemsContainer.innerHTML = '<div style="padding: 20px 0; text-align: center; color: #7a7a7a;">Корзина пуста</div>';
            if (cartCountText) cartCountText.textContent = '0';
            if (cartTotalSpan) cartTotalSpan.textContent = '0 ₽';
            if (cartCount) {
                cartCount.textContent = '0';
                cartCount.style.display = 'none';
            }
            return;
        }
        
        let html = '';
        let total = 0;
        
        data.items.forEach(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            
            html += `
                <div class="cart-item mini-cart-item" data-product-id="${item.product_id}" style="display: flex; align-items: center; gap: 10px; padding: 10px; border-bottom: 1px solid #e0e0e0; position: relative;">
                    <div class="cart-item-img" style="width: 50px; height: 50px; background: #f5f5f5; border-radius: 8px; overflow: hidden; flex-shrink: 0;">
                        <img src="/${item.image_main || 'frontend/images/no-image.jpg'}" alt="${item.name}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="cart-item-desc" style="flex: 1; min-width: 0; padding-right: 25px;">
                        <strong style="font-size: 0.9rem; display: block; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.name}</strong>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.85rem; color: #6b8a6b;">${item.quantity} x ${item.price.toLocaleString()} ₽</span>
                            <span style="color: #6b8a6b; font-weight: 600;">${itemTotal.toLocaleString()} ₽</span>
                        </div>
                    </div>
                    <button class="mini-cart-remove" onclick="removeFromCartMini(${item.product_id}, this)" 
                            style="position: absolute; top: 5px; right: 5px; width: 24px; height: 24px; background: #f5f5f5; border: 1px solid #e0e0e0; border-radius: 50%; color: #b9a99a; cursor: pointer; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; z-index: 10;"
                            title="Удалить">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        });
        
        cartItemsContainer.innerHTML = html;
        if (cartCountText) cartCountText.textContent = data.items.length;
        if (cartTotalSpan) cartTotalSpan.textContent = total.toLocaleString() + ' ₽';
        if (cartCount) {
            cartCount.textContent = data.items.length;
            cartCount.style.display = 'flex';
        }
        
    } catch (error) {
        console.error('Mini cart error:', error);
        const cartItemsContainer = document.querySelector('.cart-items-dropdown');
        if (cartItemsContainer) {
            cartItemsContainer.innerHTML = '<div style="padding: 20px 0; text-align: center; color: #7a7a7a;">Ошибка загрузки</div>';
        }
    }
}

window.removeFromCartMini = async function(productId, btn) {
    const success = await removeFromCart(productId);
    
    if (success) {
        const cartItem = btn.closest('.mini-cart-item');
        if (cartItem) {
            cartItem.style.transition = 'all 0.3s ease';
            cartItem.style.opacity = '0';
            cartItem.style.transform = 'translateX(20px)';
            
            setTimeout(() => {
                loadMiniCart();
            }, 300);
        }
    }
};

async function updateAllWishlistButtons() {
    try {
        const authResponse = await fetch(window.API_BASE + 'auth.php?action=check');
        const authData = await authResponse.json();
        
        if (!authData.loggedIn) {
            document.querySelectorAll('.add-to-favorite, .add-to-favorite-btn, .add-to-favorite-detail').forEach(btn => {
                const icon = btn.querySelector('i');
                icon.classList.remove('fas');
                icon.classList.add('far');
                btn.classList.remove('active');
            });
            return;
        }
        
        const response = await fetch(window.API_BASE + 'wishlist.php?action=get');
        const data = await response.json();
        
        if (data.success && data.items) {
            document.querySelectorAll('.add-to-favorite, .add-to-favorite-btn, .add-to-favorite-detail').forEach(btn => {
                const icon = btn.querySelector('i');
                icon.classList.remove('fas');
                icon.classList.add('far');
                btn.classList.remove('active');
            });
            
            data.items.forEach(item => {
                changeWishlistButtons(item.product_id, true);
            });
        }
    } catch (error) {
        console.error('Update wishlist buttons error:', error);
    }
}

async function toggleWishlist(productId, button) {
    try {
        const response = await fetch(window.API_BASE + 'wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                action: 'toggle', 
                product_id: parseInt(productId) 
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            if (data.status === 'added') {
                showNotification('Добавлено в избранное');
                changeWishlistButtons(productId, true);
                await loadMiniWishlist();
                await updateWishlistCount();
            } else {
                showNotification('Удалено из избранного');
                changeWishlistButtons(productId, false);
                await loadMiniWishlist();
                await updateWishlistCount();
            }
        } else if (data.message === 'Требуется авторизация') {
            showNotification('Войдите, чтобы добавить в избранное', 'error');
            setTimeout(() => window.location.href = '/login', 1000);
        }
    } catch (error) {
        console.error('Wishlist toggle error:', error);
        showNotification('Ошибка соединения', 'error');
    }
}

async function loadMiniWishlist() {
    const wishlistItems = document.querySelector('.wishlist-items');
    const wishlistCountText = document.querySelector('.wishlist-count-text');
    
    if (!wishlistItems) return;
    
    try {
        const authResponse = await fetch(window.API_BASE + 'auth.php?action=check');
        const authData = await authResponse.json();
        
        if (!authData.loggedIn) {
            wishlistItems.innerHTML = '<div style="padding: 20px 0; text-align: center; color: #7a7a7a;">Войдите, чтобы видеть избранное</div>';
            if (wishlistCountText) wishlistCountText.textContent = '0';
            return;
        }
        
        const response = await fetch(window.API_BASE + 'wishlist.php?action=get');
        const data = await response.json();
        
        if (!data.success || !data.items || data.items.length === 0) {
            wishlistItems.innerHTML = '<div style="padding: 20px 0; text-align: center; color: #7a7a7a;">Избранное пусто</div>';
            if (wishlistCountText) wishlistCountText.textContent = '0';
            return;
        }
        
        let html = '';
        
        data.items.forEach(item => {
            html += `
                <div class="wishlist-item" style="display: flex; align-items: center; gap: 10px; padding: 10px; border-bottom: 1px solid #e0e0e0; position: relative;">
                    <div class="wishlist-item-img" style="width: 50px; height: 50px; background: #f5f5f5; border-radius: 8px; overflow: hidden;">
                        <img src="/${item.image_main || 'frontend/images/no-image.jpg'}" alt="${item.name}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="wishlist-item-desc" style="flex: 1; min-width: 0; padding-right: 25px;">
                        <strong style="font-size: 0.9rem; display: block; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.name}</strong>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #6b8a6b; font-weight: 600;">${item.price.toLocaleString()} ₽</span>
                        </div>
                    </div>
                    <button class="wishlist-item-remove" onclick="removeFromWishlistMini(${item.product_id}, this)" 
                            style="position: absolute; top: 5px; right: 5px; width: 24px; height: 24px; background: #f5f5f5; border: 1px solid #e0e0e0; border-radius: 50%; color: #b9a99a; cursor: pointer; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; z-index: 10;"
                            title="Удалить">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        });
        
        wishlistItems.innerHTML = html;
        if (wishlistCountText) wishlistCountText.textContent = data.items.length;
        
    } catch (error) {
        console.error('Mini wishlist error:', error);
    }
}

async function updateWishlistCount() {
    try {
        const authResponse = await fetch(window.API_BASE + 'auth.php?action=check');
        const authData = await authResponse.json();
        
        if (!authData.loggedIn) {
            document.querySelectorAll('.wishlist-count').forEach(badge => {
                badge.textContent = '0';
                badge.style.display = 'none';
            });
            return;
        }
        
        const wishlistResponse = await fetch(window.API_BASE + 'wishlist.php?action=get');
        const data = await wishlistResponse.json();
        
        const count = data.success && data.items ? data.items.length : 0;
        
        document.querySelectorAll('.wishlist-count').forEach(badge => {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        });
        
    } catch (error) {
        console.error('Update wishlist count error:', error);
    }
}

window.removeFromWishlistMini = async function(productId, btn) {
    try {
        const response = await fetch(window.API_BASE + 'wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                action: 'toggle', 
                product_id: parseInt(productId) 
            })
        });
        
        const data = await response.json();
        
        if (data.success && data.status === 'removed') {
            const wishlistItem = btn.closest('.wishlist-item');
            wishlistItem.style.transition = 'all 0.3s ease';
            wishlistItem.style.opacity = '0';
            wishlistItem.style.transform = 'translateX(20px)';
            
            setTimeout(async () => {
                await loadMiniWishlist();
                await updateWishlistCount();
                
                document.querySelectorAll(`.add-to-favorite[data-id="${productId}"]`).forEach(btn => {
                    const icon = btn.querySelector('i');
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    btn.classList.remove('active');
                });
            }, 300);
            
            showNotification('Товар удален из избранного');
        }
    } catch (error) {
        console.error('Remove from wishlist error:', error);
    }
};

async function checkAuth() {
    try {
        const response = await fetch(window.API_BASE + 'auth.php?action=check');
        const data = await response.json();
        
        if (data.success && data.loggedIn) {
            if (data.is_admin) {
                const userMenu = document.querySelector('.user-menu .auth-buttons');
                if (userMenu) {
                    userMenu.innerHTML = `
                        <a href="/admin/index.php" class="auth-btn"><i class="fas fa-cog"></i> Админ-панель</a>
                        <button class="auth-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Выйти</button>
                    `;
                }
                
                const userHeader = document.querySelector('.user-header');
                if (userHeader) {
                    userHeader.innerHTML = `<i class="fas fa-crown" style="color: #ffc107;"></i> Админ: ${data.user.name}`;
                }
            } else {
                const userMenu = document.querySelector('.user-menu .auth-buttons');
                if (userMenu) {
                    userMenu.innerHTML = `
                        <a href="/profile" class="auth-btn"><i class="fas fa-user"></i> Личный кабинет</a>
                        <button class="auth-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Выйти</button>
                    `;
                }
                
                const userHeader = document.querySelector('.user-header');
                if (userHeader) {
                    userHeader.innerHTML = `<i class="far fa-smile"></i> Здравствуйте, ${data.user.name}`;
                }
            }
            
            await loadMiniWishlist();
            await updateWishlistCount();
        } else {
            const wishlistItems = document.querySelector('.wishlist-items');
            if (wishlistItems) {
                wishlistItems.innerHTML = '<div style="padding: 20px 0; text-align: center; color: #7a7a7a;">Войдите, чтобы видеть избранное</div>';
            }
        }
    } catch (error) {
        console.error('Auth check error:', error);
    }
}

window.logout = async function() {
    try {
        const response = await fetch(window.API_BASE + 'auth.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'logout' })
        });
        
        const data = await response.json();
        if (data.success) {
            window.location.href = '/';
        }
    } catch (error) {
        console.error('Logout error:', error);
    }
};

function setupDropdowns() {
    const containers = document.querySelectorAll('.dropdown-container');
    
    containers.forEach(container => {
        const btn = container.querySelector('.icon-btn');
        const menu = container.querySelector('.dropdown-menu');
        
        if (!btn || !menu) return;
        
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            
            containers.forEach(c => {
                if (c !== container) {
                    c.querySelector('.dropdown-menu')?.classList.remove('show');
                }
            });
            
            menu.classList.toggle('show');
        });
    });
    
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            menu.classList.remove('show');
        });
    });
}

window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.toggleWishlist = toggleWishlist;
window.loadMiniCart = loadMiniCart;
window.fetchCart = fetchCart;
window.updateCartCount = updateCartCount;
window.checkAuth = checkAuth;
window.showNotification = showNotification;
window.loadMiniWishlist = loadMiniWishlist;
window.updateWishlistCount = updateWishlistCount;
window.changeAddToCartButtons = changeAddToCartButtons;
window.changeWishlistButtons = changeWishlistButtons;
window.updateAllWishlistButtons = updateAllWishlistButtons;

document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
    fetchCart();
    loadMiniCart();
    loadMiniWishlist();
    updateWishlistCount();
    updateAllWishlistButtons();
    
    document.querySelectorAll('.dropdown-container').forEach(container => {
        container.addEventListener('mouseenter', function() {
            if (this.querySelector('.cart-menu')) {
                loadMiniCart();
            }
            if (this.querySelector('.wishlist-menu')) {
                loadMiniWishlist();
            }
        });
    });
    
    document.body.addEventListener('click', function(e) {
        const cartBtn = e.target.closest('.add-to-cart, .add-to-cart-btn, .add-to-cart-detail');
        
        if (cartBtn) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = cartBtn.dataset.id;
            if (!productId) return;
            
            let quantity = 1;
            const qtyInput = document.getElementById('productQuantity');
            if (qtyInput) {
                quantity = parseInt(qtyInput.value) || 1;
            }
            
            console.log('ВЫЗОВ: productId=' + productId + ' quantity=' + quantity);
            addToCart(productId, quantity);
            return;
        }
        
        const favBtn = e.target.closest('.add-to-favorite, .add-to-favorite-btn, .add-to-favorite-detail');
        if (favBtn) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = favBtn.dataset.id;
            if (productId) {
                toggleWishlist(productId, favBtn);
            }
        }
    });
    
    setupDropdowns();
});