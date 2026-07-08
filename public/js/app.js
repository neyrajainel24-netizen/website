window.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) {
        window.lucide.createIcons();
    }

    const rows = Array.from(document.querySelectorAll('[data-user-row]'));
    const searchInput = document.querySelector('[data-user-search]');
    const filterButtons = Array.from(document.querySelectorAll('[data-role-filter]'));
    const visibleCount = document.querySelector('[data-user-visible-count]');
    const noResults = document.querySelector('[data-user-no-results]');
    const exportButton = document.querySelector('[data-export-users]');
    let activeRole = 'all';

    const updateUsersTable = () => {
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        let count = 0;

        rows.forEach((row) => {
            const matchesRole = activeRole === 'all' || row.dataset.role === activeRole;
            const matchesQuery = !query
                || (row.dataset.name || '').includes(query)
                || (row.dataset.email || '').includes(query);
            const isVisible = matchesRole && matchesQuery;

            row.hidden = !isVisible;

            if (isVisible) {
                count += 1;
            }
        });

        if (visibleCount) {
            visibleCount.textContent = String(count);
        }

        if (noResults) {
            noResults.hidden = count !== 0;
        }
    };

    if (searchInput && rows.length > 0) {
        searchInput.addEventListener('input', updateUsersTable);
    }

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            activeRole = button.dataset.roleFilter || 'all';
            filterButtons.forEach((item) => item.classList.toggle('is-active', item === button));
            updateUsersTable();
        });
    });

    if (exportButton && rows.length > 0) {
        exportButton.addEventListener('click', () => {
            const lines = [['Nombre', 'Email', 'Rol', 'Estado']];

            rows.filter((row) => !row.hidden).forEach((row) => {
                const cells = Array.from(row.querySelectorAll('td'));
                const person = cells[0] ? cells[0].innerText.trim().split('\n') : [];
                const role = cells[1] ? cells[1].innerText.trim() : '';
                const status = cells[2] ? cells[2].innerText.trim() : '';

                lines.push([person[0] || '', person[1] || '', role, status]);
            });

            const csv = lines
                .map((line) => line.map((value) => `"${String(value).replaceAll('"', '""')}"`).join(','))
                .join('\n');
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');

            link.href = url;
            link.download = 'usuarios-cafego.csv';
            link.click();
            URL.revokeObjectURL(url);
        });
    }

});
