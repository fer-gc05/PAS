function login(event) {
    event.preventDefault();

    let username = document.getElementById('user').value;
    let password = document.getElementById('password').value;

    if (username === '' || password === '') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Por favor, complete todos los campos',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        return;
    }

    axios.post('../index.php', new URLSearchParams({
        action: 'login',
        username: username,
        password: password
    }), {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
        .then(response => {
            let data = response.data;
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Bienvenido!',
                    text: 'Iniciando sesión...',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    window.location.href = '../index.php?view=alarm';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Usuario o contraseña incorrectos',
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
                text: 'Ha ocurrido un error',
                toast: true,
                showConfirmButton: false,
                timer: 3000
            });
            console.error(error);
        });
}