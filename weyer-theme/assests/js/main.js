// Минимальный main.js для быстрого запуска
document.addEventListener('DOMContentLoaded', function() {
    // Счетчики избранного/сравнения
    updateCounters();

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
        document.getElementById('modal-overlay').classList.add('active');
        document.getElementById(id).style.display = 'block';
        document.body.style.overflow = 'hidden';
    };

    window.closeModal = () => {
        document.getElementById('modal-overlay').classList.remove('active');
        document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
        document.body.style.overflow = '';
    };

    // Избранное/сравнение
    window.toggleFavorite = (id) => {
        const favs = JSON.parse(localStorage.getItem('weyer_favorites') || '[]');
        const idx = favs.indexOf(parseInt(id));
        if (idx > -1) favs.splice(idx, 1);
        else favs.push(parseInt(id));
        localStorage.setItem('weyer_favorites', JSON.stringify(favs));
        updateCounters();
    };

    window.toggleCompare = (id) => {
        const comp = JSON.parse(localStorage.getItem('weyer_compare') || '[]');
        const idx = comp.indexOf(parseInt(id));
        if (idx > -1) comp.splice(idx, 1);
        else if (comp.length < 4) comp.push(parseInt(id));
        localStorage.setItem('weyer_compare', JSON.stringify(comp));
        updateCounters();
    };

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
});