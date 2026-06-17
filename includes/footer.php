<?php
?>
</main> 

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="footer-logo">
                    <img src="/frontend/images/IK.png" alt="LOOP zero waste" width="36" height="36">
                    <span>LOOP zero waste</span>
                </div>
                <p class="footer-description">
                    Многоразовые альтернативы для жизни без отходов.
                </p>
                <div class="footer-social">
                    <a href="https://vk.com/umpk_professionalitet" class="social-link" target="_blank"><i class="fab fa-vk"></i></a>
                    <a href="https://web.telegram.org/a/#-1001515133002" class="social-link" target="_blank"><i class="fab fa-telegram"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Каталог</h4>
                <ul class="footer-links">
                    <li><a href="/catalog?new=1">Новинки</a></li>
                    <li><a href="/catalog?category=1">Эко-гигиена</a></li>
                    <li><a href="/catalog?category=2">Ноль отходов</a></li>
                    <li><a href="/catalog?category=3">В дорогу</a></li>
                    <li><a href="/catalog?category=4">Экоподарки</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Покупателям</h4>
                <ul class="footer-links">
                    <li><a href="/about">О нас</a></li>
                    <li><a href="/delivery">Доставка и оплата</a></li>
                    <li><a href="/contacts">Контакты</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Свяжитесь с нами</h4>
                <ul class="footer-links">
                    <li>
                        <i class="far fa-envelope"></i> 
                        <a href="javascript:void(0)" onclick="copyToClipboard('pedkolledj-1@yandex.ru')">
                            pedkolledj-1@yandex.ru
                        </a>
                    </li>
                    <li>
                        <i class="far fa-envelope"></i> 
                        <a href="javascript:void(0)" onclick="copyToClipboard('umpk-priem@bk.ru')">
                            umpk-priem@bk.ru (приемная)
                        </a>
                    </li>
                    <li>
                        <i class="fas fa-phone-alt"></i> 
                        <a href="javascript:void(0)" onclick="copyToClipboard('8 (347) 262-91-90')">
                            8 (347) 262-91-90
                        </a>
                    </li>
                    <li>
                        <i class="fas fa-phone-alt"></i> 
                        <a href="javascript:void(0)" onclick="copyToClipboard('8 (347) 235-94-72')">
                            8 (347) 235-94-72
                        </a>
                    </li>
                    <li>
                        <i class="fas fa-phone-alt"></i> 
                        <a href="javascript:void(0)" onclick="copyToClipboard('8 (347) 235-94-79')">
                            8 (347) 235-94-79
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-copyright">
                © 2026 LOOP zero waste
            </div>
            <div class="footer-legal">
                <a href="/pp">Политика конфиденциальности</a>
                <a href="/ua">Пользовательское соглашение</a>
            </div>
        </div>
    </div>
</footer>

<script src="/frontend/js/script.js"></script>
<script src="/frontend/js/main.js"></script>

<?php if (isset($page_script) && $page_script): ?>
    <script src="/frontend/js/<?= $page_script ?>"></script>
<?php endif; ?>

<script src="/frontend/js//pagination.js"></script>

<script src="/frontend/js/scroll-to-top.js"></script>

<script src="/frontend/js/script.js"></script>

<button id="scrollToTopBtn" class="scroll-to-top" title="Наверх">
    <i class="fas fa-arrow-up"></i>
</button>
</body>
</html>