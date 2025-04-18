$(document).ready(function () {
    $('#registerForm').submit(function (e) {
        e.preventDefault();

        const username = $('#username').val();
        const email = $('#email').val();
        const password = $('#password').val();
        const confirmPassword = $('#confirm_password').val();

        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            return;
        }

        $.ajax({
            url: 'http://localhost/project/auth/register',
            method: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({
                username: username,
                email: email,
                password: password
            }),
            success: function (response) {
                if (response.success || response.message === "User was created.") {
                    alert('Registration successful! Please log in.');

                    setTimeout(() => {
                        window.location.href = 'login.html';
                    }, 1000);
                } else {
                    alert('x' + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
            }
        });
    });
});