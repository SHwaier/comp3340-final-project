# project-comp3077

Authentication using JWT 
basic middleware implemented to protect some endpoints
Authentication and authorization are both implemented, users either have the "admin" role, or the "customer"
Input sanitization is also implemented to protect against SQL injection and XSS attacks
Database credentials and JWT secret implemented as environment variable inside the Apache server, this way they are not exposed to the public and are kept private.
Theme can be by an admin from the admin portal at `/admin/settings`

## Tech Stack:
### Backend:
- PHP
### Frontend
- HTML
- CSS
- JavaScript
- PHP
### Database
- MariaDB

## Installation
### Requirements

- A PHP server is required to run the application.
- Apache server is needed to handle `.htaccess` and environment variables.
- All application files must reside in the root directory of the website (excluding `.htaccess.example`).
- A MySQL-compatible database is required, with full access to it. MariaDB is supported.
- The `.htaccess` file must be updated with the correct environment variables to establish a connection to your database.


# 🛍️ Luxe Frontend Documentation

This document outlines the structure and purpose of all frontend components, assets, and scripts in the `luxe/` directory.

---

## 📁 Directory Structure Overview

```
Luxe/
│
├── assets/           → Static assets like images, icons, and logos
├── cart/             → Cart page and related logic
├── components/       → Reusable PHP components (e.g. header, footer, product cards)
├── product/          → Product detail page and product images
├── scripts/          → All JavaScript logic (modular and page-specific)
├── shop/             → Main shop/product listing page
└── styles/           → Global and scoped CSS files
```

---

## 📟 Folder Breakdown

### 📁 `assets/`

**Static files** used globally in the application.

- **`img/`** – Fallbacks or generic image assets (e.g. `placeholder.png`)
- **`logo/`** – Light/dark versions of the site logo + favicons
- **`svg/`** – Icons used in nav, UI (cart, menu, user)

---

### 📁 `cart/`

- **`index.php`** – Displays all cart items, allows quantity updates and deletions. Communicates with `api/cart.php`.

---

### 📁 `components/`

**Modular PHP components**, reusable across pages.

- **`header.php`** – Navigation bar with responsive menu and login/cart/user logic
- **`footer.php`** – Footer with logo, contact info, and links
- **`metas.php`** – Meta tags used in `<head>` (author, SEO, favicon, responsive)
- **`scripts.php`** – Common `<script>` includes (e.g. theme, app.js)
- **`product-card.php`** – Displays a single product in card layout
- **`product-grid.php`** – Displays a grid of multiple product cards

---

### 📁 `product/`

- **`index.php`** – Product detail page (2-column layout: image + info)
- **`images/`** – Product images (used by product listings and detail page)

---

### 📁 `scripts/`

JavaScript used across the frontend:

| File              | Description |
|-------------------|-------------|
| `app.js`          | dynamic UI toggling (header/user/menu) and any general frontend js needs |
| `login.js`        | Handles login form submission and token storage |
| `register.js`     | Validates and submits registration form, auto-logins |
| `addToCart.js`    | Handles the logic for adding and sending post requests to add items to the user's cart | 
| `cart.js`         | Handles the cart logic to delete and change item quantity | 

---

### 📁 `shop/`

- **`index.php`** – Main shopping page. Loads product cards dynamically via API (`api/products.php`)

---

### 📁 `styles/`

- **`style.css`** – Global design system (themes, layout, typography, buttons, cards, containers)
- **`product.css`** – Custom styles for the product detail page (optional split for modularity)

---

## 🔁 API Integration

All data is fetched dynamically via RESTful APIs under `/api/`, with `Bearer` JWT auth. Example integrations:

- `GET /api/products.php` – Load all or single product
- `POST /api/cart.php` – Add to cart
- `PUT /api/cart.php` – Update quantity
- `DELETE /api/cart.php` – Remove item
- `GET /api/auth/getSession.php` – Validate logged-in user on frontend
- `GET /api/themes.php` – Returns all available themes and their ID
- `GET /api/theme.php` – Returns the theme ID of the active theme
- `PUT /api/theme.php` – Updates the active theme to a different one
- More would have been implemented if I had another day
---

## 🌙 Theming

- Theme controlled via dynamically added style sheets depending on the actively selected theme by the admin
- Switch options: `light`, `dark`, `black-friday`
- User's preference is saved in `cookie`
- Active theme is fetched from the DB on load 
- Theme can be changed by going to `/admin/settings` and changing it through the dropdown list: NOTE ONLY ADMINS CAN CHANGE IT

---

## 💡 Notes

- Responsive design: header/nav/footer adjusts to mobile/tablet/desktop
- Componentized (e.g., product cards reused everywhere)
- Secure authentication through JWT, salting and hashing: SHA256
- Authorization roles implemented users can be either customers by default or admins
- Only admins can visit `/admin` and any of it's subpages
