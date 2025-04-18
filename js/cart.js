$(document).ready(function () {
    function loadCart() {
        const isLoggedIn = JSON.parse(localStorage.getItem('isLoggedIn'));

        if (!isLoggedIn) {
            alert("Please log in to view your cart.");
            window.location.href = "login.html";
            return;
        }

        let cart = JSON.parse(localStorage.getItem("cart")) || [];

        const cartItemsContainer = $("#cart-items");
        cartItemsContainer.empty();

        let total = 0;

        if (cart.length === 0) {
            cartItemsContainer.html('<p style="font-size: 25px;">Your cart is empty.</p>');
            $("#total-price").text("0.00");
            return;
        }

        cart.forEach((item, index) => {
            total += item.price * item.quantity;

            const cartItem = $(`
                <div class="cart-item">
                    <img src="${item.img}" alt="${item.name}" width="80" />
                    <div class="cart-details">
                        <h3>${item.name}</h3>
                        <p>Price: $${item.price.toFixed(2)}</p>
                        <div class="quantity-controls">
                            <button class="decrease" data-index="${index}">-</button>
                            <span>${item.quantity}</span>
                            <button class="increase" data-index="${index}">+</button>
                        </div>
                    </div>
                </div>
            `);

            cartItemsContainer.append(cartItem);
        });

        $("#total-price").text(total.toFixed(2));
    }

    // Increase quantity
    $("#cart-items").on("click", ".increase", function () {
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        const index = $(this).data("index");

        cart[index].quantity += 1;
        localStorage.setItem("cart", JSON.stringify(cart));
        loadCart();
    });

    // Decrease quantity
    $("#cart-items").on("click", ".decrease", function () {
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        const index = $(this).data("index");

        if (cart[index].quantity > 1) {
            cart[index].quantity -= 1;
        } else {
            cart.splice(index, 1);
        }

        localStorage.setItem("cart", JSON.stringify(cart));
        loadCart();
    });

    // Load cart on page load
    loadCart();
});
