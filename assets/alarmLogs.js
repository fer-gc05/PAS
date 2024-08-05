function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
}

function saveFilters() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    localStorage.setItem('searchInput', searchInput);

}

function getSavedFilters() {
    const searchInput = localStorage.getItem('searchInput') || '';
    document.getElementById('searchInput').value = searchInput;

}

function validateDates() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const today = new Date().toISOString().slice(0, 10);

    if (startDate && endDate && startDate > endDate) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            text: 'La fecha limite superior no puede ser menor a la fecha limite inferior.',
        });
        document.getElementById('startDate').value = today;
        document.getElementById('endDate').value = today;
        return false;
    }

    if (startDate === '') {
        document.getElementById('startDate').value = today;
    }

    if (endDate === '') {
        document.getElementById('endDate').value = today;
    }

    return true;
}

async function filterLogs() {
    if (!validateDates()) return;

    const searchInput = document.getElementById('searchInput').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    try {
        const response = await axios.post('../index.php', new URLSearchParams({
            action: 'getAlarmLogs',
            search: searchInput,
            startTime: startDate,
            endTime: endDate
        }));

        const data = response.data;

        if (Array.isArray(data)) {
            const tableBody = document.getElementById('table-body');
            tableBody.innerHTML = '';
            data.forEach((log) => {
                const row = document.createElement('tr');
                row.classList.add('bg-white');
                row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">${log.action}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">${log.timestamp}</div>
            </td>
        `;
                tableBody.appendChild(row);
            });
        } else {
            console.error('Error: La respuesta del servidor no es un array.', data);
        }
    } catch (error) {
        console.error('Error:', error.response ? error.response.data : error.message);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const today = new Date().toISOString().slice(0, 10);
    document.getElementById('startDate').value = today;
    document.getElementById('endDate').value = today;
    getSavedFilters();
    filterLogs();
    setInterval(filterLogs, 5000);
});