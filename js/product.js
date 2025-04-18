$(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');

    if (!productId) {
        window.location.href = "index.html";
    } else {
        $.ajax({
            url: `http://localhost/project/products/${productId}`,
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.success && data.data) {
                    const product = data.data;

                    // Populate product details
                    $('#product-name').text(product.product_name);
                    $('#product-img').attr('src', product.product_img);
                    $('#product-description').text(product.product_info);
                    $('#product-price').text(product.product_price);
                    $('#product-info').text(product.product_description);

                    // Add to cart logic
                    $('#cart').click(function () {
                        const isLogin = JSON.parse(localStorage.getItem('isLoggedIn'));
                        if (!isLogin) {
                            alert("Please log in to add items to the cart.");
                            window.location.href = "login.html";
                            return;
                        }

                        const currentProduct = {
                            id: productId,
                            name: product.product_name,
                            price: parseFloat(product.product_price),
                            img: product.product_img,
                            quantity: 1
                        };

                        // Get current cart or initialize
                        let cart = JSON.parse(localStorage.getItem('cart')) || [];

                        // Check if product already in cart
                        const existingIndex = cart.findIndex(item => item.id === productId);

                        if (existingIndex !== -1) {
                            cart[existingIndex].quantity += 1;
                        } else {
                            cart.push(currentProduct);
                        }

                        // Save updated cart
                        localStorage.setItem('cart', JSON.stringify(cart));

                        alert("Product added to your cart.");
                    });

                } else {
                    $('#product-details').html('<p>Product not found.</p>');
                }
            },
            error: function () {
                $('#product-details').html('<p>There was an error fetching the product details.</p>');
            }
        });
    }
});
