<dialog id="order-transactions-dialog" class="rounded-xl shadow-xl border border-gray-200 p-0 w-full max-w-lg backdrop:bg-black/40">
    <div class="p-5">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800" id="order-transactions-title">Tranzakciók</h2>
                <p class="text-sm text-gray-500" id="order-transactions-subtitle"></p>
            </div>
            <button type="button" id="order-transactions-close" class="text-gray-400 hover:text-gray-600 text-xl leading-none" aria-label="Bezárás">&times;</button>
        </div>
        <div id="order-transactions-loading" class="text-sm text-gray-500 py-4 hidden">Betöltés…</div>
        <div id="order-transactions-error" class="text-sm text-red-600 py-4 hidden"></div>
        <div id="order-transactions-empty" class="text-sm text-gray-500 py-4 hidden">Ehhez a megrendeléshez még nincs rögzített tranzakció.</div>
        <div class="overflow-x-auto hidden" id="order-transactions-table-wrap">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tranzakció azonosító</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Típus</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Összeg</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Időpont</th>
                    </tr>
                </thead>
                <tbody id="order-transactions-body" class="divide-y divide-gray-200"></tbody>
            </table>
        </div>
    </div>
</dialog>

<script>
(function () {
    var dialog = document.getElementById('order-transactions-dialog');
    if (!dialog) return;

    var titleEl = document.getElementById('order-transactions-title');
    var subtitleEl = document.getElementById('order-transactions-subtitle');
    var loadingEl = document.getElementById('order-transactions-loading');
    var errorEl = document.getElementById('order-transactions-error');
    var emptyEl = document.getElementById('order-transactions-empty');
    var tableWrap = document.getElementById('order-transactions-table-wrap');
    var tbody = document.getElementById('order-transactions-body');
    var closeBtn = document.getElementById('order-transactions-close');

    function formatAmount(value) {
        return new Intl.NumberFormat('hu-HU').format(value) + ' Ft';
    }

    function setVisible(el, show) {
        if (!el) return;
        el.classList.toggle('hidden', !show);
    }

    function openTransactions(orderId) {
        titleEl.textContent = 'Tranzakciók – #' + orderId;
        subtitleEl.textContent = '';
        setVisible(loadingEl, true);
        setVisible(errorEl, false);
        setVisible(emptyEl, false);
        setVisible(tableWrap, false);
        tbody.innerHTML = '';
        dialog.showModal();

        fetch('{{ url('/admin/orders') }}/' + orderId + '/transactions', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (res) {
                if (!res.ok) throw new Error('Nem sikerült betölteni a tranzakciókat.');
                return res.json();
            })
            .then(function (data) {
                setVisible(loadingEl, false);
                subtitleEl.textContent = 'Fizetve állapot: ' + (data.fizetve_label || '—');

                if (!data.transactions || data.transactions.length === 0) {
                    setVisible(emptyEl, true);
                    return;
                }

                var rows = '';
                data.transactions.forEach(function (tx) {
                    rows += '<tr>' +
                        '<td class="px-3 py-2 text-gray-800 font-mono text-xs break-all">' + tx.transaction_id + '</td>' +
                        '<td class="px-3 py-2 text-gray-700">' + tx.type_label + '</td>' +
                        '<td class="px-3 py-2 text-right font-medium text-gray-900">' + formatAmount(tx.amount) + '</td>' +
                        '<td class="px-3 py-2 text-gray-600 whitespace-nowrap">' + tx.created_at + '</td>' +
                        '</tr>';
                });
                tbody.innerHTML = rows;
                setVisible(tableWrap, true);
            })
            .catch(function (err) {
                setVisible(loadingEl, false);
                errorEl.textContent = err.message || 'Hiba történt.';
                setVisible(errorEl, true);
            });
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-order-transactions');
        if (!btn) return;
        e.preventDefault();
        var orderId = btn.getAttribute('data-order-id');
        if (orderId) openTransactions(orderId);
    });

    closeBtn.addEventListener('click', function () { dialog.close(); });
    dialog.addEventListener('click', function (e) {
        if (e.target === dialog) dialog.close();
    });
})();
</script>
