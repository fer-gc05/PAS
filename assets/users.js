function toggleMobileMenu() {
    var mobileMenu = document.getElementById("mobile-menu");
    mobileMenu.classList.toggle("hidden");
}

function createUser() {
    Swal.fire({
        title: 'Crear un nuevo usuario',
        html: `
        <div class="input-container">
            <label for="username">Nombre de usuario:</label>
            <input id="username" type="text" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded py-2 px-4 mb-4 block w-full placeholder-gray-500 text-gray-700 focus:outline-none focus:bg-white" placeholder="Nombre de usuario">
        </div>
        <div class="input-container">
            <label for="password">Contraseña:</label>
            <input id="password" type="password" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded py-2 px-4 mb-4 block w-full placeholder-gray-500 text-gray-700 focus:outline-none focus:bg-white" placeholder="Contraseña">
        </div>`,
        showCancelButton: true,
        confirmButtonText: 'Crear',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            return axios.post('../index.php', new URLSearchParams({
                action: 'createUser',
                newUsername: username,
                newPassword: password
            }), {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
                .then(response => {
                    const data = response.data;
                    if (data.status === 'success') {
                        Swal.fire({
                            text: 'Usuario creado exitosamente',
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else if (data.message === 'User already exists') {
                        Swal.fire({
                            text: 'El usuario ya existe',
                            icon: 'info',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        Swal.fire({
                            text: 'Error desconocido al crear el usuario',
                            icon: 'error',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Se produjo un error al crear el usuario', 'error');
                });
        }
    });
}

function deleteUser(idUser) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, borrarlo!'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post('../index.php', new URLSearchParams({
                action: 'deleteUser',
                id: idUser
            }), {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
                .then(response => {
                    console.log('Response data:', response.data); // Debugging line
                    const data = response.data;
                    if (data.status === 'success') {
                        Swal.fire({
                            text: 'Usuario eliminado exitosamente',
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        Swal.fire({
                            text: data.message || 'Error desconocido al eliminar el usuario',
                            icon: 'error',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Se produjo un error al eliminar el usuario', 'error');
                });
        }
    });
}


async function getUser() {
    try {
        const response = await axios.post('../index.php', new URLSearchParams({
            action: 'listUserRfid'
        }));

        const users = response.data.data;
        const tableBody = document.getElementById('table-body');
        tableBody.innerHTML = '';

        users.forEach(user => {
            const row = document.createElement('tr');
            row.classList.add('hover:bg-gray-50');
            row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${user.username}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm ${user.rfid_asociado === 'No' ? 'text-red-500' : 'text-green-500'} font-semibold">
                ${user.rfid_asociado === 'No' ? 'Sin asignar' : 'Asignado'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                ${user.rfid_asociado === 'No' ?
                    `<button onclick="assignRfid(${user.user_id})" class="bg-green-500 text-white p-2 rounded-md shadow hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <i class='bx bx-barcode'></i>
                    </button>`
                    : ''}

                ${user.rfid_asociado === 'Sí' ?
                    `<button onclick="desassignRfid(${user.user_id})" class="bg-blue-500 text-white p-2 rounded-md shadow hover:bg-brown-600 focus:outline-none focus:ring-2 focus:ring-brown-500">
                        <i class='bx bx-barcode'></i>
                    </button>`
                    : ''}
                
                <button onclick="deleteUser(${user.user_id})" class="bg-red-500 text-white p-2 rounded-md shadow hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <i class='bx bx-trash'></i>
                </button>
            </td>`;
            tableBody.appendChild(row);
        });
    } catch (error) {
        console.error('Error:', error);
    } finally {
        setTimeout(getUser, 2000);
    }
}

getUser();


async function getRfid() {
    try {
        console.debug("Solicitando RFID...");
        const response = await axios.post('../index.php', new URLSearchParams({
            action: 'getRfid'
        }));

        const rfid = response.data.rfid;
        console.debug("Respuesta del servidor:", response.data);
        return rfid;
    } catch (error) {
        console.error("Error al obtener el RFID:", error);
        return null;
    }
}

async function assignRfid(userId) {
    try {
        console.debug("Cambiando modo de RFID a 'register'...");
        await axios.post('../index.php', new URLSearchParams({
            action: 'setRfidMode',
            newMode: 'register'
        }));

        let rfid = null;
        let canceled = false;

        const swalInstance = await Swal.fire({
            title: 'Asignar RFID',
            text: 'Escanea la tarjeta en el dispositivo',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            allowOutsideClick: false,
            didOpen: async () => {
                Swal.showLoading();
                while (!rfid && !canceled) {
                    console.debug("Esperando RFID...");
                    rfid = await getRfid();
                    if (rfid) {
                        console.debug("RFID recibido:", rfid);
                        Swal.close();
                        break;
                    } else {
                        console.debug("RFID no disponible, reintentando...");
                        await new Promise(resolve => setTimeout(resolve, 1000));
                    }
                }
            },
            preConfirm: () => {
                canceled = true;
            },
            willClose: () => {
                canceled = !rfid;
            }
        });

        if (canceled) {
            console.debug("Proceso cancelado por el usuario.");
            await Swal.fire({
                icon: 'error',
                title: 'Cancelado',
                text: 'El proceso de asignación de RFID ha sido cancelado.',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
        } else if (rfid) {
            console.debug("Registrando RFID:", rfid);
            const response = await axios.post('../index.php', new URLSearchParams({
                action: 'assignRfidToUser',
                rfid_code: rfid,
                user_id: userId,
                device_id: 1
            }));

            if (response.data.status === 'error' && response.data.message === 'RFID already assigned') {
                await Swal.fire({
                    icon: 'error',
                    title: 'RFID ya asignado',
                    text: 'El RFID que has escaneado ya está asignado a un usuario. Escanea una tarjeta diferente.',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
                console.debug("Reiniciando RFID...");
                await axios.post('../index.php', new URLSearchParams({
                    action: 'restartRfid'
                }));
                rfid = null;
            } else if (response.data.status === 'error') {
                throw new Error(response.data.message);
            } else {
                await Swal.fire({
                    icon: 'success',
                    title: 'RFID Asignado',
                    text: `El RFID ${rfid} ha sido asignado correctamente.`,
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });

                console.debug("Reiniciando RFID...");
                await axios.post('../index.php', new URLSearchParams({
                    action: 'restartRfid'
                }));
            }
        } else {
            await Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo obtener el RFID. Inténtalo de nuevo.',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
        }

        console.debug("Cambiando modo de RFID a 'read'...");
        await axios.post('../index.php', new URLSearchParams({
            action: 'setRfidMode',
            newMode: 'read'
        }));

    } catch (error) {
        console.error('Error en asignación de RFID:', error);
        await Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al asignar el RFID. Inténtalo de nuevo.',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
        });

        console.debug("Cambiando modo de RFID a 'read' en caso de error...");
        await axios.post('../index.php', new URLSearchParams({
            action: 'setRfidMode',
            newMode: 'read'
        }));
    }
}


function desassignRfid(userId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, desasignar RFID!'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post('../index.php', new URLSearchParams({
                action: 'desassignRfid',
                id: userId
            }), {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
                .then(response => {
                    console.log('Response data:', response.data);
                    const data = response.data;
                    if (data.status === 'success') {
                        Swal.fire({
                            text: 'RFID desasignado exitosamente',
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        Swal.fire({
                            text: data.message || 'Error desconocido al desasignar el RFID',
                            icon: 'error',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Se produjo un error al desasignar el RFID', 'error');
                });
        }
    });
}
