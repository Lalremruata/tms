import Swal from 'sweetalert2';

import './bootstrap';

// import Alpine from 'alpinejs';

// window.Alpine = Alpine;

// Alpine.start();

document.getElementById('alertButton').addEventListener('click', function() {
    Swal.fire({
        title: 'Success!',
        text: 'Change Password Successfully',
        icon: 'success',
        confirmButtonText: 'ok'
    });
});

