(function() {
  const containers = document.querySelectorAll('.dropdown-container');
  
  containers.forEach(container => {
    const btn = container.querySelector('.icon-btn');
    const menu = container.querySelector('.dropdown-menu');
    
    if (!btn || !menu) return;

    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      
      containers.forEach(c => {
        if (c !== container) {
          c.querySelector('.dropdown-menu')?.classList.remove('show-on-click');
        }
      });
      
      menu.classList.toggle('show-on-click');
    });

    document.addEventListener('click', (event) => {
      if (!container.contains(event.target)) {
        menu.classList.remove('show-on-click');
      }
    });
  });

  const style = document.createElement('style');
  style.innerHTML = `
    @media (max-width: 768px) {
      .dropdown-menu.show-on-click {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
      }
    }
  `;
  document.head.appendChild(style);
})();

(function() {
  const slides = document.querySelectorAll('.slide');
  const prevBtn = document.querySelector('.slider-prev');
  const nextBtn = document.querySelector('.slider-next');
  const dots = document.querySelectorAll('.dot');
  
  if (!slides.length) return;
  
  let currentIndex = 0;
  const totalSlides = slides.length;
  
  function showSlide(index) {
    if (index < 0) index = totalSlides - 1;
    if (index >= totalSlides) index = 0;
    
    slides.forEach(slide => slide.classList.remove('active'));

    slides[index].classList.add('active');

    dots.forEach((dot, i) => {
      dot.classList.toggle('active', i === index);
    });
    
    currentIndex = index;
  }
  
  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      showSlide(currentIndex - 1);
    });
  }
  
  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      showSlide(currentIndex + 1);
    });
  }
  
  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
      showSlide(index);
    });
  });
  
})();

function copyToClipboard(text, element) {
  navigator.clipboard.writeText(text).then(function() {
    showNotification('Скопировано: ' + text, element);
  }).catch(function(err) {
    fallbackCopy(text, element);
  });
}

function fallbackCopy(text, element) {
  const textarea = document.createElement('textarea');
  textarea.value = text;
  textarea.style.position = 'fixed';
  textarea.style.opacity = '0';
  document.body.appendChild(textarea);
  textarea.select();
  
  try {
    document.execCommand('copy');
    showNotification('Скопировано: ' + text, element);
  } catch (err) {
    alert('Не удалось скопировать. Попробуйте выделить текст вручную.');
  }
  
  document.body.removeChild(textarea);
}

function showNotification(message, element) {
  const notification = document.createElement('div');
  notification.textContent = message;
  notification.style.cssText = `
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #6b8a6b;
    color: white;
    padding: 10px 20px;
    border-radius: 40px;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 9999;
    animation: fadeInOut 2s ease forwards;
  `;
  
  const style = document.createElement('style');
  style.textContent = `
    @keyframes fadeInOut {
      0% { opacity: 0; transform: translate(-50%, 20px); }
      10% { opacity: 1; transform: translate(-50%, 0); }
      90% { opacity: 1; transform: translate(-50%, 0); }
      100% { opacity: 0; transform: translate(-50%, -20px); }
    }
  `;
  document.head.appendChild(style);
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.remove();
  }, 2000);
}