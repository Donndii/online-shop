$(document).ready(function () {
    let products = [];  // This will hold the product data

    // Function to load all products
    function loadProducts() {
        $.ajax({
            url: 'http://localhost/project/products',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                const list = $('#product-list');
                list.empty();
                $('#product-title').text('All Products');

                if (data.success && Array.isArray(data.data)) {
                    products = data.data;  // Store the products

                    // Display products on the page
                    displayProducts(products);
                } else {
                    list.html('<p>No products found.</p>');
                }
            },
            error: function (xhr, status, error) {
                console.log("AJAX error: ", error);
                console.log("Response Text:", xhr.responseText);
                $('#product-list').html('<p>Could not load products.</p>');
            }
        });
    }

    // Function to display products
    function displayProducts(productsToDisplay) {
        const list = $('#product-list');
        list.empty();

        if (productsToDisplay.length > 0) {
            productsToDisplay.forEach(product => {
                const item = `
                    <div class="product-card">
                        <a href="product.html?id=${product.id}">
                            <img src="${product.product_img}" alt="${product.product_name}" />
                            <h3>${product.product_name}</h3></a>
                            <p><strong>Type:</strong> ${product.product_type}</p>
                            <p>${product.product_info}</p>
                            <p><strong>Price:</strong> $${product.product_price}</p>
                    </div>
                `;
                list.append(item);
                console.log(`Created link for product ${product.product_name} with ID: ${product.product_id}`);
            });
        } else {
            list.html('<p>No matching products found.</p>');
        }
    }

    // Load all products on initial load
    loadProducts();

    // Load categories
    $.ajax({
        url: 'http://localhost/project/products/categories',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.success && Array.isArray(response.data)) {
                const categoryBar = $('#category-bar');
                response.data.forEach(category => {
                    const categoryElement = `<div class="category">${category}</div>`;
                    categoryBar.append(categoryElement);
                });
            }
        },
        error: function () {
            console.error('Could not load categories');
        }
    });

    // Handle category click and product filtering
    $('.category-bar').on('click', '.category', function () {
        const selectedCategory = $(this).text();

        $('.category').removeClass('active');
        $(this).addClass('active');

        if (selectedCategory === "All") {
            $('#product-title').text('All Products');
            loadProducts();  // Load all products when "All" is clicked
        } else {
            $('#product-title').text(`${selectedCategory} Products`);
            $.ajax({
                url: `http://localhost/project/products/${selectedCategory}`,
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    const list = $('#product-list');
                    list.empty();

                    if (response.success && Array.isArray(response.data)) {
                        response.data.forEach(product => {
                            const item = `
                                <div class="product-card">
                                        <a href="product.html?id=${product.id}">
                                        <img src="${product.product_img}" alt="${product.product_name}" />
                                        <h3>${product.product_name}</h3> </a>
                                        <p><strong>Type:</strong> ${product.product_type}</p>
                                        <p>${product.product_info}</p>
                                        <p><strong>Price:</strong> $${product.product_price}</p>
                                </div>
                            `;
                            list.append(item);
                        });
                    } else {
                        list.html('<p>No products found in this category.</p>');
                    }
                },
                error: function () {
                    $('#product-list').html('<p>Error loading category products.</p>');
                }
            });
        }

    });
    $('#search-form').on('submit', function (e) {
        e.preventDefault();  // Prevent form submission

        const searchTerm = $('#search-query').val().toLowerCase();  // Get the search term

        // Filter products based on the search term
        const filteredProducts = products.filter(product => {
            return product.product_name.toLowerCase().includes(searchTerm);
        });

        // Display filtered products
        displayProducts(filteredProducts);
    });

    // Reset the search if the query is empty
    $('#search-query').on('input', function () {
        if ($(this).val() === '') {
            displayProducts(products);  // Show all products again when the search input is cleared
        }
    });

});
