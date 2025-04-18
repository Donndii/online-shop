$(document).ready(function () {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    function updateTotal() {
        let total = 0;
        cart.forEach(item => {
            total += item.price * item.quantity;
        });
        $('#totalPrice').text(total.toFixed(2));
    }

    $('#checkoutForm').on('submit', function (e) {
        e.preventDefault();

        if (cart.length === 0) {
            alert("Your cart is empty.");
            return;
        }

        alert("Payment successful! Thank you for your purchase.");

        setTimeout(() => {
            localStorage.removeItem("cart");
            cart = [];
            updateTotal();
            $('#checkoutForm')[0].reset();

            window.location.href('index.html');
        }, 100);
    });

    updateTotal();
});
