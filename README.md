# BeadCraft Store — E-Commerce Website

A full-featured e-commerce website for beads, charms, bracelet kits, and craft supplies. Built with **PHP**, **MySQL**, and **CSS** (no frameworks).

## Features

### Storefront
- Beautiful, responsive homepage with hero, featured collections, products, customer reviews, and FAQ
- Product collections listing page
- Category-based product browsing
- Product detail pages with reviews and related products
- Shopping cart with quantity adjustment
- Checkout with Cash on Delivery
- Order confirmation with WhatsApp integration
- Customer registration and login
- Customer account page with order history
- About and Contact pages
- Product review submission

### Admin Panel (`/admin`)
- Secure admin login
- Dashboard with sales stats and recent orders
- Product management (create, edit, delete, search)
- Category management (create, edit, delete)
- Order management (view details, update status, WhatsApp customer)
- Customer list
- Review moderation (approve, hide, delete)
- Store settings (name, WhatsApp, email, shipping fee, social links)
- Change admin password

## Tech Stack
- **PHP** (vanilla, no framework)
- **MySQL** (database)
- **CSS** (custom design system, no framework)
- **PDO** for database access
- **password_hash()** / **password_verify()** for secure authentication
- **CSRF tokens** for form security

## Setup Instructions

### 1. Import the Database
```bash
mysql -u root -p < database/schema.sql
```

### 2. (Optional) Load Sample Data
```bash
mysql -u root -p < database/seed.sql
```

### 3. Configure Database Connection
Edit `config/config.php` and update your MySQL credentials:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'beadcraft_store');
define('DB_USER', 'root');       // your MySQL username
define('DB_PASS', '');            // your MySQL password
```

### 4. Run the Website
Place the project in your web server root (e.g., `htdocs` for XAMPP, `www` for WAMP) and visit:
```
http://localhost/beadcraft-store/
```

### 5. Access the Admin Panel
Navigate to:
```
http://localhost/beadcraft-store/admin/login.php
```

**Default credentials:**
- Username: `admin`
- Password: `admin123`

**Change the password immediately after first login via Settings.**

## Database Schema

See `database/schema.sql` for the complete schema. Tables:

| Table | Purpose |
|-------|---------|
| `admin_users` | Admin accounts |
| `categories` | Product categories (with parent support) |
| `products` | Product catalog |
| `customers` | Registered customers |
| `orders` | Customer orders |
| `order_items` | Line items per order |
| `reviews` | Product reviews (with approval system) |
| `settings` | Key-value store for site configuration |

## Project Structure
```
├── admin/                  # Admin panel
│   ├── includes/           # Admin header/footer
│   ├── login.php           # Admin login
│   ├── dashboard.php       # Admin dashboard
│   ├── products.php        # Product CRUD
│   ├── categories.php      # Category CRUD
│   ├── orders.php          # Order management
│   ├── customers.php       # Customer list
│   ├── reviews.php         # Review moderation
│   └── settings.php         # Store settings
├── assets/
│   └── css/
│       └── style.css       # Complete design system
├── config/
│   ├── config.php          # DB credentials & constants
│   ├── database.php        # PDO connection
│   └── functions.php       # Helper functions
├── database/
│   ├── schema.sql          # Database schema (import this)
│   └── seed.sql            # Sample data (optional)
├── includes/
│   ├── header.php          # Shared site header
│   ├── footer.php          # Shared site footer
│   └── product-card.php    # Reusable product card
├── index.php               # Homepage
├── collections.php         # All collections
├── category.php            # Category product listing
├── product.php             # Product detail page
├── cart.php                # Shopping cart
├── cart-action.php         # Cart add/update/remove handler
├── checkout.php            # Checkout page
├── order-place.php         # Order processing
├── login.php               # Customer login
├── register.php            # Customer registration
├── account.php             # Customer account
├── logout.php              # Customer logout
├── about.php               # About page
├── contact.php             # Contact page
└── review-submit.php       # Review submission handler
```

## Security Features
- Password hashing with PHP's `password_hash()`
- CSRF tokens on all forms
- PDO prepared statements (SQL injection prevention)
- Output escaping with `htmlspecialchars()`
- Admin authentication required for all admin pages

## License
This project is for personal/commercial use. Feel free to customize it for your store.
