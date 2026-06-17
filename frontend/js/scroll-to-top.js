document.addEventListener('DOMContentLoaded', function() {
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');
    
    if (!scrollToTopBtn) return;
    
    let lastScrollTop = 0;
    let hideTimeout;
    
    function checkScrollPosition() {
        const currentScroll = window.scrollY;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        
        if (currentScroll > 300) {
            scrollToTopBtn.classList.add('show');
            
            if (currentScroll > lastScrollTop && currentScroll > 500) {
                scrollToTopBtn.classList.remove('show');
                scrollToTopBtn.classList.add('hide');
            } else {
                scrollToTopBtn.classList.add('show');
                scrollToTopBtn.classList.remove('hide');
            }
            
            if (currentScroll + windowHeight > documentHeight - 100) {
                scrollToTopBtn.classList.add('show');
                scrollToTopBtn.classList.remove('hide');
            }
            
            clearTimeout(hideTimeout);
            hideTimeout = setTimeout(function() {
                if (window.scrollY > 300) {
                    scrollToTopBtn.classList.remove('show');
                    scrollToTopBtn.classList.add('hide');
                }
            }, 5000);
            
        } else {
            scrollToTopBtn.classList.remove('show', 'hide');
        }
        
        lastScrollTop = currentScroll;
    }
    
    window.addEventListener('scroll', function() {
        requestAnimationFrame(checkScrollPosition);
    });
    
    checkScrollPosition();
    
scrollToTopBtn.addEventListener('click', function(e) {
    e.preventDefault();
    
    scrollToTopBtn.classList.add('click');
    setTimeout(() => {
        scrollToTopBtn.classList.remove('click');
    }, 200);
    
    const startPosition = window.scrollY;
    const startTime = performance.now();
    const duration = 800;
    
    function animation(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const easeProgress = 1 - (1 - progress) * (1 - progress);
        
        window.scrollTo(0, startPosition * (1 - easeProgress));
        
        if (progress < 1) {
            requestAnimationFrame(animation);
        }
    }
    
    requestAnimationFrame(animation);
});
});