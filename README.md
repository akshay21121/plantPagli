# 🌱 PlantPuglu — Complete Gardening & Nursery E-Commerce Platform

Welcome to **PlantPuglu**, an original, high-performance, production-ready gardening and plant nursery e-commerce website built with a modern green & cream aesthetic, clean PHP 8.2+ MVC architecture, PDO MySQL database, and Razorpay payment gateway integration for Indian shoppers.

Inspired by top Indian nursery shopping experiences such as PlantsGuru, PlantPuglu provides a complete, end-to-end shopping workflow—from interactive plant selection, filtering, and customer reviews to coupon discounts, inventory validation, and server-side HMAC payment verification.

---

## 📋 Table of Contents
1. [Architecture & Technology Stack](#-architecture--technology-stack)
2. [Feature Overview](#-feature-overview)
3. [Quick Start (Local Development)](#-quick-start-local-development)
4. [Creating the First Administrator](#-creating-the-first-administrator)
5. [Razorpay Payment Gateway Setup](#-razorpay-payment-gateway-setup)
6. [Database Backup & Restore Commands](#-database-backup--restore-commands)
7. [Testing Instructions](#-testing-instructions)
8. [Production Deployment Guide](#-production-deployment-guide)
9. [Feature Matrix: Implemented vs. Requires Configuration](#-feature-matrix-implemented-vs-requires-configuration)

---

## 🛠 Architecture & Technology Stack

- **Frontend**: HTML5, Modern Vanilla CSS3 (Green & Warm Cream Palette), JavaScript (ES6+), Google Fonts (*Outfit* and *Plus Jakarta Sans*). No bloated frontend framework required.
- **Backend**: Clean, lightweight PSR-compliant PHP 8.2+ MVC structure with Front Controller pattern (`public/index.php`).
- **Database**: MySQL 8.0+ / MariaDB 10.5+ with normalized schema, foreign keys, UTF8mb4 character set, and PDO prepared statements.
- **Payments**: Native Razorpay Orders API & backend HMAC-SHA256 signature verification + Cash on Delivery (COD).
- **Security**:
  - CSRF token validation on all state-changing POST requests.
  - BCrypt password hashing (`PASSWORD_DEFAULT`).
  - Session hijacking prevention with session regeneration, HttpOnly, and SameSite cookie protection.
  - Contextual XSS sanitization (`e()` helper function).
  - Rate limiting on authentication endpoints to deter brute-force attacks.
  - Safe file upload service enforcing MIME verification and random filename generation.

---

## 🌟 Feature Overview

### 1. Customer Shopping Flow
- **Announcement Bar**: Top ticker for discount coupons and free delivery thresholds.
- **Homepage Highlights**: Hero banners, category icons, bestseller highlights, NASA air-purifiers, desk bundles, and customer testimonials.
- **13 Nursery Product Categories**:
  - Indoor Plants, Outdoor Plants, Air-Purifying Plants, Flowering Plants, Fruit Plants, Vegetable Plants, Seeds, Pots & Planters, Soil & Potting Mix, Fertilizers & Nutrition, Gardening Tools, Garden Decor, Plant Combos.
- **Advanced Filtering & Search**: Category sidebar, interactive price range filter, stock filter, sorting (Price, Newest, Popular, Highest Rated), and live keyword search.
- **Botanical Specifications**: Plant height, pot size, sunlight requirements, watering schedules, maintenance rating, and pet safety badges.
- **Customer Reviews**: Star ratings with verified purchase status and instant moderation workflows.
- **Shopping Cart**: Real-time quantity adjustments, free shipping progress bar, promo coupon engine (`PLANTGREEN`, `WELCOME10`).
- **Checkout**: Indian addresses, state selector, 6-digit PIN code validation, Razorpay online gateway, and Cash on Delivery.
- **Customer Account Area**: Saved delivery addresses, password changes, order history, and detailed shipment status timeline (Pending &rarr; Confirmed &rarr; Shipped &rarr; Delivered).
- **Customer Wishlist**: Save favourite varieties with 1-click move to cart.

### 2. Administrator Dashboard (`/admin`)
- **Real-Time KPIs**: Total sales (INR ₹), total orders, pending orders, low stock items (&le;5 units), and customer count.
- **Products Manager**: Full CRUD, multiple image uploads, size & planter variant builder, stock tracking, and CSV export.
- **Categories Manager**: Hierarchy, ordering, slugs, active toggles.
- **Orders Manager**: Filter by status, inspect itemized customer orders, update tracking courier and AWB numbers, manage payment statuses, and CSV export.
- **Coupons Engine**: Create percentage or fixed rupee discounts, minimum order threshold, and usage limits.
- **Reviews Moderation**: Approve, reject, or delete customer reviews.
- **Banners & Promotions**: Manage announcement bar text, hero banners, and promotional cards.
- **Store & Gateway Settings**: Shipping fee configuration, free shipping threshold, Razorpay keys, and contact details.

---

## 🚀 Quick Start (Local Development)

### Prerequisites
- PHP 8.2 or higher (with `pdo_mysql`, `curl`, `openssl`, and `mbstring` extensions).
- MySQL 8.0+ or MariaDB.
- Apache, Nginx, or PHP built-in web server.

### Step-by-Step Installation:

1. **Clone or Extract the Project**:
   ```bash
   cd plantpagli
   ```

2. **Configure Environment Variables**:
   Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```
   Edit `.env` with your MySQL database credentials:
   ```ini
   APP_ENV=development
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=plantpagli
   DB_USER=root
   DB_PASS=your_mysql_password
   ```

3. **Import Database Schema & 24 Seed Plants**:
   Import `database.sql` into your database:
   ```bash
   mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS plantpagli CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -u root -p plantpagli < database.sql
   ```
   *(Alternatively, visit `http://localhost:8000/install.php` in your browser for 1-click database connection test and automated SQL import)*.

4. **Start the Local Development Server**:
   ```bash
   php -S localhost:8000 -t public
   ```
   Open your browser and navigate to: **[http://localhost:8000](http://localhost:8000)**

---

## 🔐 Creating the First Administrator

For security, PlantPuglu does not require an insecure hardcoded admin password. You can create or promote an administrator via the secure CLI tool:

```bash
php bin/create_admin.php "Super Admin" "admin@plantpagli.com" "YourSecurePassword123"
```
Or run `php bin/create_admin.php` without arguments to enter credentials interactively.

*(Default seed accounts for demonstration testing: `admin@plantpagli.com` / `Admin@123` and `aarav.sharma@example.in` / `Customer@123`)*.

---

## 💳 Razorpay Payment Gateway Setup

PlantPuglu integrates seamlessly with Razorpay for Indian customers (UPI, Cards, NetBanking):

1. Log into your [Razorpay Dashboard](https://dashboard.razorpay.com/).
2. Navigate to **Settings &rarr; API Keys** and generate your **Key ID** and **Key Secret**.
3. In PlantPuglu, navigate to `/admin/settings` (or update `.env`):
   - **Razorpay Key ID**: e.g., `rzp_test_1234567890` (or `rzp_live_...` for production).
   - **Razorpay Key Secret**: Enter your private Razorpay key secret.
4. Save settings.
5. In **Test Mode**, test cards and simulated UPI handles provided by Razorpay documentation can be used. If test keys are omitted during development, an automated test payment simulator button is provided on `/checkout/razorpay_pay` to test the full signature verification workflow without live bank hits!

---

## 💾 Database Backup & Restore Commands

### Automated Database Backup
Run in terminal / crontab:
```bash
# Backup command
mysqldump -u root -p plantpagli --single-transaction --quick --lock-tables=false > backup_plantpuglu_$(date +%F_%T).sql
```

### Restore Database
```bash
# Restore command
mysql -u root -p plantpagli < backup_plantpuglu_YYYY-MM-DD.sql
```

---

## 🧪 Testing Instructions

1. **Storefront Browsing**:
   - Visit `/` &rarr; Check hero slider, category cards, bestseller plant grid.
   - Click on any product &rarr; Verify images, variant selector, specs table, and care accordion.
2. **Catalog & Filters**:
   - Visit `/products` &rarr; Filter by "Indoor Plants", adjust price range, and sort by "Price: Low to High".
3. **Cart & Coupons**:
   - Add a plant to cart &rarr; Inspect cart count badge update.
   - Go to `/cart` &rarr; Test quantity `+` / `-` buttons.
   - Enter coupon code `PLANTGREEN` (orders &ge; ₹999) or `WELCOME10` and observe instant total reduction.
4. **Checkout**:
   - Click "Proceed to Checkout" &rarr; Enter delivery address, PIN code, and select Cash on Delivery or Razorpay.
   - Verify order creation in database and receipt redirection to `/checkout/success/{order_number}`.
5. **Customer Dashboard**:
   - Register a customer at `/register` &rarr; Check `/account/orders`, view order tracking timeline, and save a new delivery address at `/account/addresses`.
6. **Admin Dashboard**:
   - Log in at `/login` as admin &rarr; Visit `/admin`.
   - Update order status, add tracking courier name/AWB, add a new product with image, export orders to CSV.

---

## 📊 Feature Matrix: Implemented vs. Requires Configuration

| Feature Component | Status | Details / Actions Needed |
| :--- | :---: | :--- |
| **Catalog & Products (24 Items)** | ✅ Complete | Fully seeded with realistic Indian nursery plants & care specs. |
| **Cart & Stock Validation** | ✅ Complete | Inventory auto-decrements in database transactions. |
| **Coupon Engine** | ✅ Complete | Percentage and flat rupee discounts with cart threshold validation. |
| **Customer Auth & Profiles** | ✅ Complete | Password hashing, multiple addresses, orders history. |
| **Shipment Status Tracking** | ✅ Complete | Visual timeline (Pending &rarr; Confirmed &rarr; Shipped &rarr; Delivered). |
| **Razorpay Backend Verification** | ✅ Complete | Server-side HMAC-SHA256 signature verification implemented. |
| **Razorpay Live API Keys** | ⚙️ Configurable | Replace test keys in `.env` or `/admin/settings` with your live Razorpay credentials. |
| **SMTP Email Dispatch** | ⚙️ Configurable | Configure standard SMTP credentials in `.env` if transactional emails are desired. |
| **SSL / HTTPS Certificate** | ⚙️ Deployment | Run Let's Encrypt / Certbot on your production Ubuntu/EC2 server (instructions in `DEPLOYMENT_UBUNTU.md`). |

---

&copy; 2026 PlantPuglu. Designed and built for plant lovers across India.
#   p l a n t P a g l i  
 