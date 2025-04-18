# Online Shop 

## Description
A simple client-side demonstration built with PHP, HTML, CSS, and JavaScript. It includes:

- Product listing and details (`product.html`)
- Shopping cart management (`cart.html`)
- Checkout process with form validation (`checkout.html`)
- User login/logout functionality via `localStorage` (`login.html`)


## Prerequisites

- [XAMPP](https://www.apachefriends.org/index.html) installed (Apache and MySQL services)
- A modern web browser (Chrome, Firefox, Edge, Safari)
- Git (optional, for cloning the repository)

## Installation & Setup

1. **Clone this repository** (or download ZIP):
   ```bash
   git clone https://github.com/Donndii/online-shop.git
   ```

2. **Move the project folder into XAMPP’s `htdocs`** directory:
   - **Windows:**
     ```text
     C:\xampp\htdocs\online-shop
     ```
   - **macOS (using default XAMPP install):**
     ```bash
     sudo cp -R online-shop /Applications/XAMPP/htdocs/
     ```
   - **Linux:**
     ```bash
     sudo cp -R online-shop /opt/lampp/htdocs/
     ```

3. **Start Apache (and MySQL, if needed)** via the XAMPP control panel.

4. **Open the application** in your browser:
   ```text
   http://localhost/online-shop/index.html
   ```


## Usage

- **Home (`index.html`)**: Browse featured products and navigate to product listings.
- **Products (`product.html`)**: View all products; click "Add to Cart" to save items in `localStorage`.
- **Cart (`cart.html`)**: Review items, adjust quantities, or buy products.
- **Checkout (`checkout.html`)**: Enter name and email to simulate payment; on success, cart is cleared.
- **Login (`login.html`)**: Enter any username/password to "log in"—sets `localStorage.isLoggedIn` to `true`. Logout clears user data.

