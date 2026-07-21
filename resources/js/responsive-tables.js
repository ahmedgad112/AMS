const ACTION_LABELS = ['إجراءات', 'إجراءات أخرى'];

function isActionsCell(label, index, headers) {
    if (ACTION_LABELS.includes(label)) {
        return true;
    }

    return !label && index === headers.length - 1;
}

function initResponsiveTables() {
    document.querySelectorAll('main table:not(.table-keep)').forEach((table) => {
        table.classList.add('table-cards');

        const headers = Array.from(table.querySelectorAll('thead th')).map((th) =>
            th.textContent.trim()
        );

        if (headers.length === 0) {
            return;
        }

        table.querySelectorAll('tbody tr').forEach((row) => {
            row.classList.add('table-card');

            const cells = Array.from(row.querySelectorAll('td'));

            cells.forEach((cell, index) => {
                if (cell.hasAttribute('colspan')) {
                    cell.classList.add('table-card-empty');
                    return;
                }

                const label = headers[index] ?? '';

                if (label) {
                    cell.setAttribute('data-label', label);
                }

                if (index === 0) {
                    cell.classList.add('table-card-title');
                } else if (isActionsCell(label, index, headers)) {
                    cell.classList.add('table-card-actions');
                } else {
                    cell.classList.add('table-card-field');
                }
            });
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initResponsiveTables);
} else {
    initResponsiveTables();
}
