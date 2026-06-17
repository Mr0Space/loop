document.addEventListener('DOMContentLoaded', function() {
    const sortSelect = document.querySelector('.sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            window.location.href = url.toString();
        });
    }
    
    highlightActiveFilters();
});

function highlightActiveFilters() {
    const urlParams = new URLSearchParams(window.location.search);
    const category = urlParams.get('category');
    
    if (category) {
        document.querySelectorAll('.filter-btn').forEach(btn => {
            if (btn.dataset.category === category || btn.href?.includes(`category=${category}`)) {
                btn.classList.add('active');
            }
        });
    }
}