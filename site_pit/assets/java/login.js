// JavaScript code to show the Bootstrap modal
document.getElementById('loginForm').addEventListener('submit', function (e) {
    e.preventDefault();

    // Your login logic here
    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;
    const tipoLogin = document.getElementById('tipo_login').value;

    // Use AJAX or fetch to send a POST request to your PHP file for login verification
    fetch('your_login_verification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            email,
            senha,
            tipo_login: tipoLogin,
        }),
    })
    .then(response => response.text())
    .then(result => {
        // Set the login result in the modal body
        const modalBody = document.querySelector('#loginResultModal .modal-body');
        modalBody.innerHTML = result;
        // Show the modal
        $('#loginResultModal').modal('show');

        // Check if the result indicates a successful login
        if (result === 'Login successful') {
            // Redirect to the "pagina_inicial.php" after a short delay (you can adjust the delay)
            setTimeout(function() {
                window.location.href = 'pagina_inicial.php';
            }, 2000); // 2000 milliseconds (2 seconds)
        }
    });
});