// Полнофункциональный main.js для темы Weyer
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация всех модулей
    updateCounters();
    initScrollToTop();
    initModals();
    initNotifications();
    initFavoritesAndCompare();

    // Кнопка наверх
    const scrollBtn = document.getElementById('scrollToTop');
    if (scrollBtn) {
        window.addEventListener('scroll', () => {
            scrollBtn.style.display = window.pageYOffset > 300 ? 'flex' : 'none';
        });
        scrollBtn.onclick = () => window.scrollTo({top: 0, behavior: 'smooth'});
    }

    // Модальные окна
    window.openModal = (id) => {
        const overlay = document.getElementById('modal-overlay');
        const modal = document.getElementById(id);
        if (overlay && modal) {
            overlay.classList.add('active');
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeModal = () => {
        const overlay = document.getElementById('modal-overlay');
        if (overlay) {
            overlay.classList.remove('active');
            document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
            document.body.style.overflow = '';
        }
    };

    // Закрытие модалок по ESC и клику вне
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });

    const overlay = document.getElementById('modal-overlay');
    if (overlay) {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal();
        });
    }

    // Система уведомлений
    window.showNotification = function(message, type = 'success', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;

        const icon = type === 'success' ? 'check' : type === 'error' ? 'times' : 'info-circle';

        notification.innerHTML = `
            <i class="fas fa-${icon}"></i>
            <span>${message}</span>
            <button class="notification-close" onclick="this.parentElement.remove()">×</button>
        `;

        document.body.appendChild(notification);
        setTimeout(() => notification.classList.add('show'), 100);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, duration);

        return notification;
    };

    // Избранное/сравнение
    window.toggleFavorite = (id) => {
        const favs = JSON.parse(localStorage.getItem('weyer_favorites') || '[]');
        const idx = favs.indexOf(parseInt(id));
        if (idx > -1) {
            favs.splice(idx, 1);
            showNotification('Товар удален из избранного');
        } else {
            favs.push(parseInt(id));
            showNotification('Товар добавлен в избранное');
        }
        localStorage.setItem('weyer_favorites', JSON.stringify(favs));
        updateFavoriteButtons();
        updateCounters();
    };

    window.toggleCompare = (id) => {
        const comp = JSON.parse(localStorage.getItem('weyer_compare') || '[]');
        const idx = comp.indexOf(parseInt(id));
        if (idx > -1) {
            comp.splice(idx, 1);
            showNotification('Товар убран из сравнения');
        } else if (comp.length < 4) {
            comp.push(parseInt(id));
            showNotification('Товар добавлен к сравнению');
        } else {
            showNotification('Можно сравнивать не более 4 товаров', 'warning');
            return;
        }
        localStorage.setItem('weyer_compare', JSON.stringify(comp));
        updateCompareButtons();
        updateCounters();
    };

    function updateFavoriteButtons() {
        const favorites = JSON.parse(localStorage.getItem('weyer_favorites') || '[]');
        document.querySelectorAll('.fav').forEach(btn => {
            const productId = parseInt(btn.getAttribute('data-product-id') || btn.onclick.toString().match(/\d+/)?.[0]);
            if (productId && favorites.includes(productId)) {
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                }
                btn.classList.add('active');
            }
        });
    }

    function updateCompareButtons() {
        const compareList = JSON.parse(localStorage.getItem('weyer_compare') || '[]');
        document.querySelectorAll('[data-action="compare"]').forEach(btn => {
            const productId = parseInt(btn.dataset.productId);
            if (productId && compareList.includes(productId)) {
                btn.classList.add('active');
            }
        });
    }

    function updateCounters() {
        const favs = JSON.parse(localStorage.getItem('weyer_favorites') || '[]');
        const comp = JSON.parse(localStorage.getItem('weyer_compare') || '[]');

        const favBadge = document.getElementById('badgeFav');
        const compBadge = document.getElementById('badgeCompare');

        if (favBadge) {
            favBadge.textContent = favs.length;
            favBadge.style.display = favs.length ? 'block' : 'none';
        }
        if (compBadge) {
            compBadge.textContent = comp.length;
            compBadge.style.display = comp.length ? 'block' : 'none';
        }
    }

    function initScrollToTop() {
        // Уже реализовано выше
    }

    function initModals() {
        // Уже реализовано выше
    }

    function initNotifications() {
        // Уже реализовано выше
    }

    function initFavoritesAndCompare() {
        updateFavoriteButtons();
        updateCompareButtons();

        // Обновление при изменении localStorage
        window.addEventListener('storage', () => {
            updateFavoriteButtons();
            updateCompareButtons();
            updateCounters();
        });
    }

    // Глобальная функция для запроса КП
    window.requestQuote = function(productId) {
        if (typeof openModal === 'function') {
            openModal('quote-modal');
            const productIdInput = document.querySelector('#quote-modal input[name="product_id"]');
            if (productIdInput) {
                productIdInput.value = productId;
            }
        } else {
            showNotification('Функция запроса КП временно недоступна', 'error');
        }
    };
});