let pageSelectActive = false;

window.showPageSelect = function(dotsElement, minPage, maxPage) {
    if (pageSelectActive) return;
    
    const select = document.createElement('select');
    select.className = 'pagination-select';
    select.style.cssText = `
        width: 70px;
        height: 40px;
        border: 1px solid #6b8a6b;
        border-radius: 20px;
        text-align: center;
        font-size: 1rem;
        outline: none;
        margin: 0 5px;
        cursor: pointer;
        background: white;
        padding: 0 5px;
    `;
    
    for (let i = minPage; i <= maxPage; i++) {
        const option = document.createElement('option');
        option.value = i;
        option.textContent = i;
        select.appendChild(option);
    }
    
    dotsElement.style.display = 'none';
    dotsElement.parentNode.insertBefore(select, dotsElement.nextSibling);
    
    pageSelectActive = true;
    
    select.focus();
    
    select.addEventListener('change', function() {
        const pageNum = parseInt(select.value);
        
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('page', pageNum);
        
        window.location.href = '/catalog?' + urlParams.toString();
    });
    
    select.addEventListener('blur', function() {
        setTimeout(function() {
            window.removePageSelect();
        }, 200);
    });
};

window.removePageSelect = function() {
    const select = document.querySelector('.pagination-select');
    if (select && select.parentNode) {
        const dots = select.previousSibling;
        if (dots && dots.classList && dots.classList.contains('pagination-dots')) {
            dots.style.display = 'inline-block';
        }
        select.remove();
    }
    pageSelectActive = false;
};

document.addEventListener('click', function(e) {
    if (!e.target.classList.contains('pagination-select') && 
        !e.target.classList.contains('pagination-dots')) {
        window.removePageSelect();
    }
});