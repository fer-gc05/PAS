function toggleMobileMenu() {
    var mobileMenu = document.getElementById("mobile-menu");
    mobileMenu.classList.toggle("hidden");
}

function toggleDropdown() {
    const dropdown = document.getElementById('user-menu');
    dropdown.classList.toggle('hidden');
}

function closeDropdownAndExecute(callback) {
    const dropdown = document.getElementById('user-menu');
    dropdown.classList.add('hidden');

    if (typeof callback === 'function') {
        callback();
    }
}

document.addEventListener('click', function (event) {
    const dropdown = document.getElementById('user-menu');
    const button = document.getElementById('user-menu-button');

    if (!dropdown.contains(event.target) && event.target !== button) {
        dropdown.classList.add('hidden');
    }
});

function turnOnAlarm() {
    Swal.fire({
        title: 'Introduzca la contraseña',
        input: 'password',
        inputAttributes: {
            autocapitalize: 'off',
            class: 'bg-gray-200 appearance-none border-2 border-gray-200 rounded py-2 px-4 block w-full placeholder-gray-500 text-gray-700 focus:outline-none focus:bg-white focus:border-blue-500'
        },
        showCancelButton: true,
        confirmButtonText: 'Activar',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: (password) => {
            if (!password) {
                Swal.showValidationMessage('La contraseña es requerida');
            } else {
                return axios.post('../index.php', new URLSearchParams({
                    action: 'turnOnAlarm',
                    password: password
                }), {
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                    .then(response => {
                        const data = response.data;
                        if (data.message === 'Alarm activated') {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Alarma activada!',
                                text: 'La alarma ha sido activada correctamente',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else if (data.message === 'Alarm already activated') {
                            Swal.fire({
                                icon: 'info',
                                title: '¡Alarma ya activada!',
                                text: 'La alarma ya se encuentra activada',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else if (data.error === 'Incorrect password') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'La contraseña es incorrecta',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al intentar activar la alarma.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    });
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    });
}

function turnOffAlarm() {
    Swal.fire({
        title: 'Introduzca la contraseña',
        input: 'password',
        inputAttributes: {
            autocapitalize: 'off',
            class: 'bg-gray-200 appearance-none border-2 border-gray-200 rounded py-2 px-4 block w-full placeholder-gray-500 text-gray-700 focus:outline-none focus:bg-white focus:border-blue-500'
        },
        showCancelButton: true,
        confirmButtonText: 'Activar',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: (password) => {
            if (!password) {
                Swal.showValidationMessage('La contraseña es requerida');
            } else {
                return axios.post('../index.php', new URLSearchParams({
                    action: 'turnOffAlarm',
                    password: password
                }), {
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                    .then(response => {
                        const data = response.data;
                        if (data.message === 'Alarm deactivated') {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Alarma desactivada!',
                                text: 'La alarma ha sido desactivada correctamente',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else if (data.message === 'Alarm already deactivated') {
                            Swal.fire({
                                icon: 'info',
                                title: '¡Alarma desactivada!',
                                text: 'La alarma ya se encuentra desactivada',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else if (data.error === 'Incorrect password') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'La contraseña es incorrecta',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al intentar activar la alarma.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    });
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    });
}

document.addEventListener('DOMContentLoaded', () => {
    alarmState();
});

async function alarmState() {
    let alarmStateElement = document.getElementById('alarmState');

    while (true) {
        try {
            const response = await axios.post('../index.php', new URLSearchParams({
                action: 'checkAlarmStatus',
                alarmId: 1
            }), {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            const data = response.data;

            if (data.status === 'Alarm activated') {
                alarmStateElement.textContent = 'Activa';
                alarmStateElement.classList.remove('text-red-800');
                alarmStateElement.classList.add('text-green-800');
            } else if (data.status === 'Alarm deactivated') {
                alarmStateElement.textContent = 'Inactiva';
                alarmStateElement.classList.remove('text-green-800');
                alarmStateElement.classList.add('text-red-800');
            } else {
                console.error('Estado inesperado recibido:', data.status);
            }

        } catch (error) {
            console.error('Error al obtener el estado de la alarma:', error);
        }

        await new Promise(resolve => setTimeout(resolve, 2000));
    }
}

function showMainMenu() {
    Swal.fire({
        title: 'Menú',
        html: `<button onclick="automotion();" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-2 w-full">Configurar automatización</button>
          <button onclick="updatePassword();" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full">Cambiar contraseña</button>`,
        showCloseButton: true,
        showCancelButton: false,
        showConfirmButton: false,
        customClass: {
            container: 'text-left'
        }
    });
}

function updatePassword() {
    Swal.fire({
        title: 'Cambiar contraseña',
        html: `<div class="input-container">
              <input type="password" id="currentPassword" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded py-2 px-4 mb-4 block w-full placeholder-gray-500 text-gray-700 focus:outline-none focus:bg-white" placeholder="Contraseña actual">
              </div>
             <div class="input-container">
              <input type="password" id="newPassword" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded py-2 px-4 mb-4 block w-full placeholder-gray-500 text-gray-700 focus:outline-none focus:bg-white" placeholder="Nueva contraseña">
              </div>`,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Ok',
        cancelButtonText: 'Cancelar',
        showValidationMessage: true,
        preConfirm: () => {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;

            if (!currentPassword || !newPassword) {
                Swal.showValidationMessage('Por favor, complete todos los campos');
            }

            return axios.post('../index.php', new URLSearchParams({
                action: 'updatePassword',
                currentPassword: currentPassword,
                newPassword: newPassword
            }), {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
                .then(response => {
                    const data = response.data;
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Contraseña actualizada',
                            text: 'La contraseña ha sido actualizada correctamente',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } else if (data.message === 'The current password is incorrect') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'La contraseña actual es incorrecta',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Error al actualizar la contraseña',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                });
        }
    })
}

function automotion() {
    axios.post('../index.php', new URLSearchParams({
        action: 'getAutomationStatus'
    }), {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
        .then(response => {
            const data = response.data;

            const status = data.status;
            const turnOnHour = data.turnOnHour;
            const turnOffHour = data.turnOffHour;

            const statusText = status == 1 ? 'Activa' : 'Inactiva';

            Swal.fire({
                title: 'Menú de automatización',
                html: `
  <div class="p-4">
    <div class="mb-4">
      <p>Estado: <span class="${status == 1 ? 'text-green-500' : 'text-red-500'}">${statusText}</span></p>
      <p>Hora de enecendido: ${turnOnHour}</p>
      <p>Hora de apagado: ${turnOffHour}</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <button onclick="activateAutomotion()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full focus:outline-none focus:shadow-outline">
        <i class='bx bx-check-circle'></i>
      </button>
      <button onclick="deactivateAutomotion()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full focus:outline-none focus:shadow-outline">
        <i class='bx bx-x-circle'></i>
      </button>
      <button onclick="updateAutomationSettings()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full focus:outline-none focus:shadow-outline">
        <i class='bx bx-cog'></i>
      </button>
    </div>
  </div>`,
                showCloseButton: true,
                showCancelButton: false,
                showConfirmButton: false,
                customClass: {
                    container: 'text-left'
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Se produjo un error al recuperar datos de automatización', 'error');
        });

}

function activateAutomotion() {
    Swal.fire({
        title: 'Activar automatización?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Activar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post('../index.php', new URLSearchParams({
                action: 'activateAutomation'
            }), {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
                .then(response => {
                    const data = response.data;
                    Swal.fire({
                        text: 'Automatización activada',
                        icon: 'success',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                })
                .catch(error => {
                    console.error('Error al activar la automatización:', error);
                    Swal.fire({
                        text: 'Error al activar la automatización',
                        icon: 'error',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
        }
    });
}

function deactivateAutomotion() {
    Swal.fire({
        title: 'Desactivar automatización?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post('../index.php', new URLSearchParams({
                action: 'deactivateAutomation'
            }), {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
                .then(response => {
                    const data = response.data;
                    Swal.fire({
                        text: 'Automatización desactivada',
                        icon: 'success',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                })
                .catch(error => {
                    console.error('Error al desactivar la automatización:', error);
                    Swal.fire({
                        text: 'Error al desactivar la automatización',
                        icon: 'error',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
        }
    });
}

function updateAutomationSettings() {
    Swal.fire({
        title: 'Actualizar Configuración',
        html: `
    <div class="input-container">
        <label for="turnOnHour">Hora de encendido:</label>
        <input id="turnOnHour" type="time" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded py-2 px-4 mb-4 block w-full placeholder-gray-500 text-gray-700 focus:outline-none focus:bg-white" placeholder="Turn On Hour">
    </div>
    <div class="input-container">
        <label for="turnOffHour">Hora de apagado:</label>
        <input id="turnOffHour" type="time" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded py-2 px-4 mb-4 block w-full placeholder-gray-500 text-gray-700 focus:outline-none focus:bg-white" placeholder="Turn Off Hour">
    </div> `,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const turnOnHour = document.getElementById('turnOnHour').value;
            const turnOffHour = document.getElementById('turnOffHour').value;

            return axios.post('../index.php', new URLSearchParams({
                action: 'updateAutomationConfiguration',
                turnOnHour: turnOnHour,
                turnOffHour: turnOffHour
            }), {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
                .then(response => {
                    const data = response.data;
                    Swal.fire({
                        text: 'Configuración actualizada exitosamente',
                        icon: 'success',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Se produjo un error al actualizar la configuración', 'error');
                });
        }
    });
}

async function checkAutomotion() {
    while (true) {
        try {
            const response = await axios.post('../index.php', new URLSearchParams({
                action: 'checkAutomation'
            }), {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

        } catch (error) {
            console.error('Error al comprobar la automatización:', error);
        }

        await new Promise(resolve => setTimeout(resolve, 1000));
    }
}
checkAutomotion();

function logout() {
    Swal.fire({
        title: 'Cerrar sesión',
        text: '¿Está seguro que desea cerrar sesión?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Cerrar sesión',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post('../index.php', new URLSearchParams({
                action: 'logout'
            }), {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
                .then(response => {
                    window.location.href = '../index.php';
                })
                .catch(error => {
                    console.error('Error al cerrar sesión:', error);
                    Swal.fire({
                        text: 'Error al cerrar sesión',
                        icon: 'error',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
        }
    });
}
