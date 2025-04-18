$(document).ready(function () {
    $('#loginForm').submit(function (e) {
        e.preventDefault();

        const username = $('#username').val();
        const email = $('#email').val();
        const password = $('#password').val();

        $.ajax({
            url: 'http://localhost/project/auth/login',
            method: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({
                username: username,
                email: email,
                password: password
            }),
            success: function (response) {
                console.log('Response:', response);

                if (response.message === "Successful login.") {
                    localStorage.setItem('isLoggedIn', "true");
                    window.location.href = 'index.html';
                } else {
                    localStorage.removeItem('isLoggedIn');
                    alert(response.message || "Login failed");
                }
            },
            error: function (xhr, status, error) {
                console.log('Error:', error);
                alert('Something went wrong. Please try again.');
            }
        });
    });
});