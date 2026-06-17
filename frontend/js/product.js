document.addEventListener('DOMContentLoaded', function() {
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            const mainImage = document.getElementById('mainProductImage');
            if (mainImage) {
                mainImage.src = this.src;
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });
});

window.incrementQuantity = function() {
    const input = document.getElementById('productQuantity');
    if (input) input.value = Math.min(99, parseInt(input.value) + 1);
};

window.decrementQuantity = function() {
    const input = document.getElementById('productQuantity');
    if (input) input.value = Math.max(1, parseInt(input.value) - 1);
};