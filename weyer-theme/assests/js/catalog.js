// Каталог - минимальная версия
document.addEventListener('DOMContentLoaded', function() {
    // Переключение вида
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.onclick = () => {
            document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('products-grid').className = `catalog-grid ${btn.dataset.view}-view`;
        };
    });

    // Обработка действий товаров
    document.addEventListener('click', function(e) {
        const action = e.target.closest('[data-action]');
        if (!action) return;

        const productId = action.dataset.productId;
        const actionType = action.dataset.action;

        switch(actionType) {
            case 'add-to-favorites':
                toggleFavorite(productId);
                break;
            case 'compare':
                toggleCompare(productId);
                break;
            case 'request-quote':
                openModal('quote-modal');
                document.querySelector('#quote-modal input[name="product_id"]').value = productId;
                break;
        }
    });

    // Простые фильтры
    let filterTimeout;
    function applyFilters() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            const formData = new FormData();
            formData.append('action', 'filter_products');
            formData.append('nonce', weyer_ajax.nonce);
            formData.append('search', document.getElementById('product-search')?.value || '');
            formData.append('category', document.querySelector('input[name="category"]:checked')?.value || '');

            fetch(weyer_ajax.ajax_url, {method: 'POST', body: formData})
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('products-grid').innerHTML = data.data.html;
                        document.getElementById('products-count').textContent = `Найдено: ${data.data.count}`;
                    }
                });
        }, 500);
    }

    // События фильтров
    document.getElementById('product-search')?.addEventListener('input', applyFilters);
    document.querySelectorAll('input[name="category"]').forEach(r => r.addEventListener('change', applyFilters));
    document.getElementById('reset-filters')?.addEventListener('click', () => {
        document.getElementById('product-search').value = '';
        document.querySelector('input[name="category"][value=""]').checked = true;
        applyFilters();
    });
});