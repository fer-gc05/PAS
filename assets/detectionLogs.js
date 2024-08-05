function toggleMobileMenu() {
    var mobileMenu = document.getElementById("mobile-menu");
    mobileMenu.classList.toggle("hidden");
}

function saveDetectionFilters() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    localStorage.setItem('startDate', startDate);
    localStorage.setItem('endDate', endDate);
}

function getSavedFilters() {
    const today = new Date().toISOString().slice(0, 10);
    document.getElementById('startDate').value = today;
    document.getElementById('endDate').value = today;
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

function filterDetectionLogs() {
    if (!validateDates()) return;

    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    axios.post('../index.php', new URLSearchParams({
        action: 'getDetectionLogs',
        startTime: startDate + 'T00:00:00Z',
        endTime: endDate + 'T23:59:59Z'
    }).toString(), {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
        .then(response => {
            const logs = response.data;
            const tableBody = document.getElementById('table-body_d');
            tableBody.innerHTML = '';
            logs.forEach(log => {
                const row = document.createElement('tr');
                row.classList.add('hover:bg-gray-100');
                row.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${log.action}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${log.timestamp}</td>
    `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error fetching logs:', error);
        });
}

document.addEventListener('DOMContentLoaded', function () {
    getSavedFilters();
    filterDetectionLogs();
    setInterval(filterDetectionLogs, 5000);
});