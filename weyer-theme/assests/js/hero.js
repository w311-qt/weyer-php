// Главная страница - табы товаров
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tab').forEach(tab => {
        tab.onclick = function() {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;
            const products = document.querySelectorAll('#gridProducts .p');

            products.forEach(p => {
                p.style.display = (filter === 'all' || p.classList.contains(filter)) ? 'block' : 'none';
            });
        };
    });
});