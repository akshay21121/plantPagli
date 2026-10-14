-- PlantPuglu Gardening E-commerce Database Schema & Seed Data
-- Target: MySQL 8.0+ / MariaDB 10.5+
-- Charset: utf8mb4 / Collation: utf8mb4_unicode_ci

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- 1. Users Table
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `phone` VARCHAR(25) NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
  `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
  `remember_token` VARCHAR(100) NULL,
  `reset_token` VARCHAR(100) NULL,
  `reset_expires_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_users_email` (`email`),
  INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Customer Delivery Addresses Table
-- --------------------------------------------------------
DROP TABLE IF EXISTS `customer_addresses`;
CREATE TABLE `customer_addresses` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `label` VARCHAR(50) DEFAULT 'Home',
  `full_name` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(25) NOT NULL,
  `address_line1` VARCHAR(255) NOT NULL,
  `address_line2` VARCHAR(255) NULL,
  `landmark` VARCHAR(150) NULL,
  `city` VARCHAR(100) NOT NULL,
  `state` VARCHAR(100) NOT NULL,
  `pincode` VARCHAR(10) NOT NULL,
  `is_default` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  INDEX `idx_addr_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Categories Table
-- --------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `parent_id` INT UNSIGNED NULL,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(191) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `image` VARCHAR(255) NULL,
  `icon` VARCHAR(100) NULL,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `is_featured` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  INDEX `idx_cat_slug` (`slug`),
  INDEX `idx_cat_parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Products Table
-- --------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(191) NOT NULL UNIQUE,
  `sku` VARCHAR(100) NOT NULL UNIQUE,
  `short_description` VARCHAR(500) NULL,
  `description` LONGTEXT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `compare_price` DECIMAL(10, 2) NULL,
  `cost_price` DECIMAL(10, 2) NULL,
  `stock_quantity` INT NOT NULL DEFAULT 10,
  `is_in_stock` TINYINT(1) DEFAULT 1,
  `plant_height` VARCHAR(100) NULL,
  `pot_size` VARCHAR(100) NULL,
  `sunlight` VARCHAR(150) NULL,
  `watering` VARCHAR(150) NULL,
  `maintenance` VARCHAR(100) DEFAULT 'Low',
  `pet_friendly` TINYINT(1) DEFAULT 0,
  `care_instructions` TEXT NULL,
  `shipping_info` TEXT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_bestseller` TINYINT(1) DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `meta_title` VARCHAR(255) NULL,
  `meta_description` VARCHAR(500) NULL,
  `average_rating` DECIMAL(3, 2) DEFAULT 5.00,
  `reviews_count` INT UNSIGNED DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  INDEX `idx_prod_category` (`category_id`),
  INDEX `idx_prod_slug` (`slug`),
  INDEX `idx_prod_featured` (`is_featured`),
  INDEX `idx_prod_bestseller` (`is_bestseller`),
  INDEX `idx_prod_active` (`is_active`),
  INDEX `idx_prod_price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. Product Images Table
-- --------------------------------------------------------
DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `alt_text` VARCHAR(255) NULL,
  `is_primary` TINYINT(1) DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  INDEX `idx_img_prod` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. Product Variants Table
-- --------------------------------------------------------
DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE `product_variants` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `sku` VARCHAR(100) NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `stock_quantity` INT NOT NULL DEFAULT 10,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  INDEX `idx_var_prod` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 7. Carts & Cart Items Table
-- --------------------------------------------------------
DROP TABLE IF EXISTS `carts`;
CREATE TABLE `carts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NULL,
  `session_id` VARCHAR(191) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  INDEX `idx_cart_session` (`session_id`),
  INDEX `idx_cart_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE `cart_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `cart_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `variant_id` INT UNSIGNED NULL,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  `price` DECIMAL(10, 2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL,
  INDEX `idx_cart_item_cart` (`cart_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 8. Wishlist Table
-- --------------------------------------------------------
DROP TABLE IF EXISTS `wishlists`;
CREATE TABLE `wishlists` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_user_product` (`user_id`, `product_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 9. Orders Table
-- --------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` INT UNSIGNED NULL,
  `customer_name` VARCHAR(150) NOT NULL,
  `customer_email` VARCHAR(191) NOT NULL,
  `customer_phone` VARCHAR(25) NOT NULL,
  `shipping_address` VARCHAR(255) NOT NULL,
  `shipping_landmark` VARCHAR(150) NULL,
  `shipping_city` VARCHAR(100) NOT NULL,
  `shipping_state` VARCHAR(100) NOT NULL,
  `shipping_pincode` VARCHAR(15) NOT NULL,
  `subtotal` DECIMAL(10, 2) NOT NULL,
  `discount_amount` DECIMAL(10, 2) DEFAULT 0.00,
  `shipping_fee` DECIMAL(10, 2) DEFAULT 0.00,
  `tax_amount` DECIMAL(10, 2) DEFAULT 0.00,
  `total_amount` DECIMAL(10, 2) NOT NULL,
  `payment_method` ENUM('cod', 'razorpay') NOT NULL DEFAULT 'cod',
  `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
  `order_status` ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
  `tracking_number` VARCHAR(100) NULL,
  `courier_name` VARCHAR(100) NULL,
  `notes` TEXT NULL,
  `coupon_code` VARCHAR(50) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  INDEX `idx_order_user` (`user_id`),
  INDEX `idx_order_num` (`order_number`),
  INDEX `idx_order_status` (`order_status`),
  INDEX `idx_order_pay_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 10. Order Items Table
-- --------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `product_sku` VARCHAR(100) NULL,
  `variant_name` VARCHAR(150) NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  `subtotal` DECIMAL(10, 2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  INDEX `idx_item_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 11. Payments Table
-- --------------------------------------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `transaction_id` VARCHAR(191) NULL,
  `razorpay_order_id` VARCHAR(191) NULL,
  `razorpay_payment_id` VARCHAR(191) NULL,
  `razorpay_signature` VARCHAR(255) NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `currency` VARCHAR(10) DEFAULT 'INR',
  `status` ENUM('initiated', 'captured', 'failed', 'refunded') NOT NULL DEFAULT 'initiated',
  `payload` LONGTEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  INDEX `idx_pay_order` (`order_id`),
  INDEX `idx_pay_rzp_order` (`razorpay_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 12. Coupons & Redemptions Table
-- --------------------------------------------------------
DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `description` VARCHAR(255) NULL,
  `discount_type` ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage',
  `discount_value` DECIMAL(10, 2) NOT NULL,
  `min_order_amount` DECIMAL(10, 2) DEFAULT 0.00,
  `max_discount_amount` DECIMAL(10, 2) NULL,
  `usage_limit` INT UNSIGNED DEFAULT 1000,
  `used_count` INT UNSIGNED DEFAULT 0,
  `starts_at` DATETIME NULL,
  `expires_at` DATETIME NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_coupon_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `coupon_redemptions`;
CREATE TABLE `coupon_redemptions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `coupon_id` INT UNSIGNED NOT NULL,
  `order_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NULL,
  `discount_amount` DECIMAL(10, 2) NOT NULL,
  `redeemed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 13. Product Reviews Table
-- --------------------------------------------------------
DROP TABLE IF EXISTS `product_reviews`;
CREATE TABLE `product_reviews` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NULL,
  `customer_name` VARCHAR(150) NOT NULL,
  `customer_email` VARCHAR(191) NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `title` VARCHAR(255) NULL,
  `comment` TEXT NOT NULL,
  `is_verified_purchase` TINYINT(1) DEFAULT 1,
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  INDEX `idx_rev_product` (`product_id`),
  INDEX `idx_rev_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 14. Banners Table
-- --------------------------------------------------------
DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `subtitle` VARCHAR(255) NULL,
  `badge_text` VARCHAR(100) NULL,
  `button_text` VARCHAR(100) DEFAULT 'Shop Now',
  `button_link` VARCHAR(255) DEFAULT '/products',
  `image_path` VARCHAR(255) NULL,
  `type` ENUM('hero', 'promo_top', 'promo_mid', 'announcement') NOT NULL DEFAULT 'hero',
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 15. Store Settings Table
-- --------------------------------------------------------
DROP TABLE IF EXISTS `store_settings`;
CREATE TABLE `store_settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` LONGTEXT NULL,
  `setting_group` VARCHAR(50) DEFAULT 'general',
  `description` VARCHAR(255) NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- SEED DATA
-- --------------------------------------------------------

-- Initial Store Settings
INSERT INTO `store_settings` (`setting_key`, `setting_value`, `setting_group`, `description`) VALUES
('store_name', 'Plantpagli', 'general', 'Store Branding Name'),
('store_tagline', 'For the love of plants — Because plants keep us all alive ♥', 'general', 'Tagline for the store'),
('store_email', 'care@plantpagli.com', 'contact', 'Customer support email'),
('store_phone', '+91 98765 43210', 'contact', 'Customer support phone number'),
('store_whatsapp', '+91 98765 43210', 'contact', 'WhatsApp order support line'),
('store_address', 'Green Heights, 4th Floor, Koramangala 5th Block, Bengaluru, Karnataka, India - 560034', 'contact', 'Physical registered nursery address'),
('currency_symbol', '₹', 'localization', 'Currency display symbol'),
('currency_code', 'INR', 'localization', 'ISO currency code'),
('shipping_fee', '99.00', 'shipping', 'Standard shipping charge across India'),
('free_shipping_threshold', '999.00', 'shipping', 'Minimum order amount for free delivery'),
('razorpay_enabled', '1', 'payment', 'Enable Razorpay payment gateway'),
('razorpay_key_id', 'rzp_test_YourTestKeyId123', 'payment', 'Razorpay Test or Live Key ID'),
('razorpay_key_secret', 'YourTestKeySecret456789', 'payment', 'Razorpay Secret for HMAC verification'),
('cod_enabled', '1', 'payment', 'Enable Cash on Delivery option'),
('site_meta_title', 'PlantPuglu | Buy Live Plants, Seeds, Pots & Gardening Supplies Online India', 'seo', 'Homepage Meta Title'),
('site_meta_description', 'India\'s favourite online garden store. Shop healthy indoor plants, flowering plants, fruit trees, premium pots, organic fertilizers and garden tools with safe doorstep delivery.', 'seo', 'Homepage Meta Description');

-- Initial Admin and Sample Customer (Passwords: 'Admin@123' and 'Customer@123' hashed with bcrypt)
-- Note: Admin can also be created/changed using `php bin/create_admin.php`
INSERT INTO `users` (`name`, `email`, `phone`, `password`, `role`, `status`) VALUES
('PlantPuglu Admin', 'admin@plantpagli.com', '+91 98765 43210', '$2y$12$t41.rZ7eQ3KkG18KqLau2.k5o1Zk1/Q1Qy8z2G7p7X5D2j1s2V3eK', 'admin', 'active'),
('Aarav Sharma', 'aarav.sharma@example.in', '+91 98111 22334', '$2y$12$t41.rZ7eQ3KkG18KqLau2.k5o1Zk1/Q1Qy8z2G7p7X5D2j1s2V3eK', 'customer', 'active');

-- Sample Customer Address
INSERT INTO `customer_addresses` (`user_id`, `label`, `full_name`, `phone`, `address_line1`, `address_line2`, `landmark`, `city`, `state`, `pincode`, `is_default`) VALUES
(2, 'Home', 'Aarav Sharma', '+91 98111 22334', 'Flat 402, Lotus Greens Apartment', 'Sector 14, HSR Layout', 'Near BDA Complex', 'Bengaluru', 'Karnataka', '560102', 1);

-- Product Categories (Full 13 requested categories matching PlantsGuru reference)
INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`, `icon`, `sort_order`, `is_active`, `is_featured`) VALUES
(1, NULL, 'Indoor Plants', 'indoor-plants', 'Lush, shade-tolerant house plants that thrive in home and office spaces.', 'leaf', 1, 1, 1),
(2, NULL, 'Outdoor Plants', 'outdoor-plants', 'Sun-loving shrubs, hedges, and hardy foliage for balconies and gardens.', 'sun', 2, 1, 1),
(3, NULL, 'Air-Purifying Plants', 'air-purifying-plants', 'NASA recommended natural air cleaners that filter toxins like benzene and formaldehyde.', 'wind', 3, 1, 1),
(4, NULL, 'Flowering Plants', 'flowering-plants', 'Vibrant, fragrant flowering plants to bring color and butterflies to your home.', 'flower', 4, 1, 1),
(5, NULL, 'Fruit Plants', 'fruit-plants', 'Grafted fruit saplings suitable for pots, terrace orchards, and backyard gardens.', 'apple', 5, 1, 1),
(6, NULL, 'Vegetable Plants', 'vegetable-plants', 'Fresh organic vegetable saplings and kitchen garden essentials.', 'carrot', 6, 1, 0),
(7, NULL, 'Seeds', 'seeds', 'High-germination vegetable, flower, exotic herb, and microgreen seeds.', 'seedling', 7, 1, 1),
(8, NULL, 'Pots and Planters', 'pots-and-planters', 'Ceramic, terracotta, metal, hanging, and durable UV-treated plastic planters.', 'square', 8, 1, 1),
(9, NULL, 'Soil and Potting Mix', 'soil-and-potting-mix', 'Sterilized potting soils, cocopeat blocks, perlite, and enriched organic mixes.', 'layers', 9, 1, 0),
(10, NULL, 'Fertilizers and Plant Nutrition', 'fertilizers-and-plant-nutrition', 'Organic vermicompost, seaweed extract, neem cake, and balanced plant food.', 'zap', 10, 1, 1),
(11, NULL, 'Gardening Tools', 'gardening-tools', 'Ergonomic trowels, pruning shears, garden hose pipes, sprayers, and gloves.', 'tool', 11, 1, 0),
(12, NULL, 'Garden Decor', 'garden-decor', 'Miniature garden miniatures, pebbles, bird houses, and plant stands.', 'feather', 12, 1, 0),
(13, NULL, 'Plant Combos and Gift Plants', 'plant-combos-and-gift-plants', 'Curated value bundles, lucky plant gifts, and festive green hamper sets.', 'gift', 13, 1, 1);

-- Sample High-Quality Indian Nursery Products (24 Items with full specs, care instructions, and pricing)
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `sku`, `short_description`, `description`, `price`, `compare_price`, `stock_quantity`, `plant_height`, `pot_size`, `sunlight`, `watering`, `maintenance`, `pet_friendly`, `care_instructions`, `shipping_info`, `is_featured`, `is_bestseller`, `average_rating`, `reviews_count`) VALUES
(1, 1, 'Sansevieria Golden Hahnii (Snake Plant)', 'sansevieria-golden-hahnii-snake-plant', 'PP-IN-001', 'Compact rosette dwarf snake plant with radiant yellow margins. Indestructible air purifier.', 'The Sansevieria Golden Hahnii is a charming compact variety of the classic Snake Plant. Featuring tight rosettes of stiff, sword-shaped leaves with lush green centers and vibrant butter-yellow borders, it is arguably one of the most forgiving houseplants ever discovered. Perfect for study desks, nightstands, and living room shelves.', 299.00, 399.00, 45, '6 - 9 inches', '4 inch nursery pot', 'Low to Bright Indirect', 'Once every 10-14 days (Allow soil to dry out)', 'Extremely Low', 0, 'Light: Adapts to low light, thrives in moderate indirect sun. Water: Let soil completely dry between waterings. Overwatering is the only way to kill it! Fertilizer: Feed with diluted organic liquid fertilizer once every 2 months in spring/summer.', 'Ships carefully packed in corrugated breathable transit boxes with soil-lock netting.', 1, 1, 4.90, 38),

(2, 3, 'Peace Lily (Spathiphyllum) - Air Purifier', 'peace-lily-spathiphyllum', 'PP-AP-002', 'Elegant dark foliage with pure white hooded blooms that naturally cleans indoor air.', 'Peace Lily is renowned for its architectural gloss-green foliage and striking white spathes. NASA clean air study validated its power to neutralize benzene, formaldehyde, and carbon monoxide. It even droops slightly when thirsty, gracefully telling you exactly when it wants a drink!', 349.00, 499.00, 30, '10 - 14 inches', '5 inch plastic pot', 'Medium Indirect to Partial Shade', 'When top 1 inch of soil is dry (Approx 2-3 times a week)', 'Low to Medium', 0, 'Light: Keep away from harsh midday direct sun to prevent leaf scorching. Water: Keep soil consistently moist but never waterlogged. Mist leaves weekly in dry summer months. Soil: Well-draining peat-rich potting mix.', 'Potted in fresh nursery soil mix. Ships with transit insurance.', 1, 1, 4.85, 42),

(3, 1, 'Golden Money Plant (Epipremnum Aureum)', 'golden-money-plant-epipremnum', 'PP-IN-003', 'Beloved Indian prosperity plant with heart-shaped golden-streaked cascading leaves.', 'Known throughout India as the Money Plant, Epipremnum Aureum is celebrated for attracting good fortune, positive energy, and clean air into the household. It can climb up a moss stick or trail down bookshelves and hanging macramé baskets with equal splendor.', 199.00, 279.00, 80, '8 - 12 inches', '4 inch pot with hanger hook', 'Bright Indirect to Fluorescent Indoor', 'Once a week or when topsoil feels dry', 'Very Low', 0, 'Light: Any indoor ambient light. Bright indirect light boosts golden variegation. Water: Thoroughly water once a week. You can also propagate easily in clean water bottles.', 'Ready to hang or place on a tabletop.', 1, 1, 4.95, 87),

(4, 3, 'Areca Palm (Chrysalidocarpus Lutescens)', 'areca-palm-natural-humidifier', 'PP-AP-004', 'Lush tropical feathery fronds that act as a natural indoor humidifier and aesthetic statement.', 'Bring resort vibes to your living room or corporate reception. The Areca Palm features graceful arching fronds with dozens of slender leaflets. It constantly pumps moisture into air-conditioned dry rooms while actively filtering indoor airborne pollutants.', 599.00, 849.00, 25, '2.5 - 3.5 feet', '7 inch self-watering pot', 'Bright Indirect Sunlight', '2 times a week; keep roots moist but well-drained', 'Medium', 1, 'Light: Bright diffused sunlight near a window or balcony. Water: Water thoroughly when top 2 inches dry out. Pruning: Trim yellowing outer stems at the base to encourage fresh center shoots.', 'Large specimen shipping with reinforced wooden crate support for safe transport across India.', 1, 1, 4.80, 29),

(5, 1, 'ZZ Plant (Zamioculcas Zamiifolia)', 'zz-plant-fortune-green', 'PP-IN-005', 'Sculptural glossy mirror leaves that thrive on neglect and minimal light.', 'The ZZ plant is the ultimate modern architectural houseplant. With stems lined by shiny, deep emerald waxy leaflets that look polished by hand, it stores abundant water in potato-like underground rhizomes, making it impervious to occasional forgetfulness.', 449.00, 599.00, 40, '12 - 16 inches', '5 inch planter', 'Low to Medium Indirect Light', 'Once every 2-3 weeks', 'Extremely Low', 0, 'Light: Thrives even in windowless office spaces with fluorescent bulbs. Water: Allow entire soil mass to dry completely before drenching. Never leave pot standing in water.', 'Ships securely fastened in our signature eco-friendly plant box.', 1, 1, 4.92, 54),

(6, 4, 'Mogra / Arabian Jasmine (Jasminum Sambac)', 'mogra-arabian-jasmine-fragrant', 'PP-FL-006', 'Intensely fragrant white traditional Indian blossoms loved for puja and natural aromatherapy.', 'Mogra is an indispensable gem of Indian gardening. Producing clusters of snow-white, intoxicatingly fragrant flowers from early spring through autumn, it brings divine peace and timeless beauty to balcony railings, terraces, and temple gardens.', 249.00, 349.00, 35, '10 - 15 inches', '6 inch nursery pot', 'Full Direct Sun (4-6 hours)', 'Daily in hot summers; 3 times a week in winter', 'Moderate', 1, 'Light: Generous outdoor sunlight is key for heavy bud formation. Water: Regular hydration. Soil: Rich garden loam with compost. Tip: Prune lightly after each flowering cycle and feed with mustard cake liquid or bone meal.', 'Well-rooted live bush with multiple active flowering nodes.', 1, 1, 4.88, 63),

(7, 5, 'Alphonso Mango Grafted Sapling (Hapus)', 'alphonso-mango-grafted-sapling', 'PP-FR-007', 'King of mangoes. Premium authentic grafted variety that fruits in compact containers.', 'Grow the world-famous sweet, saffron-fleshed Alphonso Mango in your terrace garden or home orchard. This grafted specimen starts flowering and producing juicy gourmet mangoes within 2-3 years, far earlier than seed-grown trees.', 699.00, 999.00, 20, '2 - 3 feet', '8 inch polybag with rich soil ball', 'Full Sunny Balcony or Open Ground', 'When topsoil is dry; deep weekly watering', 'Moderate', 1, 'Light: 6+ hours of direct Indian sunlight. Water: Regular watering while plant is young. Reduce water slightly during winter flowering to set fruit. Fertilizer: Apply organic cow dung manure or vermicompost every 3 months.', 'Shipped with root ball intact in special heavy-duty box.', 1, 0, 4.75, 19),

(8, 4, 'Desi Gulab / Indian Fragrant Rose', 'desi-gulab-indian-red-rose', 'PP-FL-008', 'Traditional highly fragrant crimson rose used for pure gulkand, garlands, and home gardens.', 'Unlike scentless hybrid tea cut-flowers, the Indian Desi Rose is celebrated for its ethereal, nostalgic perfume that fills the morning breeze. Free flowering and hardy against local climates.', 229.00, 329.00, 50, '12 - 18 inches', '6 inch nursery pot', 'Full Morning & Afternoon Sun', 'Every morning during summer', 'Moderate', 1, 'Light: Minimum 5 hours direct sunshine. Feed: Monthly handful of vermicompost and Epsom salt spray. Prune spent blooms to trigger fresh branching.', 'Healthy budded bush with sturdy root system.', 0, 1, 4.70, 34),

(9, 1, 'Fiddle Leaf Fig (Ficus Lyrata Bambino)', 'fiddle-leaf-fig-bambino', 'PP-IN-009', 'Interior designer favorite with dramatic violin-shaped sculptural leaves.', 'The Bambino variety of Fiddle Leaf Fig is bred specifically for modern apartments. More compact and bushier than its giant cousin, its thick, leathery leaves with prominent veining add high-fashion luxury to living rooms and cafes.', 699.00, 999.00, 18, '1.5 - 2 feet', '6 inch nursery pot', 'Bright Filtered Sunlight', 'Every 7-10 days', 'Medium', 0, 'Light: Place 2-3 feet away from an east or west facing window. Avoid cold AC drafts. Water: Drench until water drains from the base, then tip excess tray water.', 'Packed with leaf protective sleeves to prevent shipping creases.', 1, 0, 4.65, 23),

(10, 1, 'Crassula Ovata (Jade Plant / Friendship Tree)', 'crassula-ovata-jade-plant-feng-shui', 'PP-IN-010', 'Good luck succulent with fleshy jade-green coins that symbolizes wealth and harmony.', 'A revered plant in Vastu Shastra and Feng Shui, the Jade Plant is a resilient succulent with thick woody stems and plump, glossy oval leaves. It thrives on warm window sills and requires virtually zero everyday fuss.', 249.00, 349.00, 60, '6 - 9 inches', '4 inch pot', 'Bright Sunlight to Gentle Direct Sun', 'Every 10-14 days; allow soil to dry completely', 'Very Low', 0, 'Light: Loves sunny windowsills. Water: Overwatering causes leaf drop; treat like a desert plant. Soil: Sandy, porous succulent soil.', 'Pre-potted in premium gritty succulent mix.', 1, 1, 4.91, 56),

(11, 2, 'Krishna Tulsi / Holy Basil (Sacred Herb)', 'krishna-tulsi-holy-basil-live-plant', 'PP-HB-011', 'Sacred medicinal dark-purple leaf Tulsi rich in healing adaptogens and essential oils.', 'Every Indian courtyard and balcony is blessed by the sacred Tulsi. Krishna Tulsi boasts rich purple-tinged foliage with a warm, spicy clove aroma. Known to relieve coughs, boost immunity, and repel mosquitoes naturally.', 149.00, 199.00, 100, '8 - 12 inches', '5 inch earthen style pot', 'Direct Morning Sunlight', 'Daily light watering to maintain moist soil', 'Low', 1, 'Light: Minimum 4 hours direct sun. Water: Water the base gently each morning. Pinch off top seed-spikes (manjari) regularly to keep the plant bushy and perennial.', 'Ships with holy brass-finish decorative pot wrap.', 1, 1, 4.96, 112),

(12, 13, 'Workplace Zen - 3 Air Purifier Combo', 'workplace-zen-3-air-purifier-combo', 'PP-CB-012', 'Curated combo of Snake Plant, Money Plant, and Jade Plant in matching pastel planters.', 'Our #1 bestselling gift combo! Includes three of India\'s most resilient, low-maintenance houseplants that purify the air and attract abundance. Comes beautifully presented in matching pastel planters with care cards.', 699.00, 999.00, 35, 'Set of 3 (6-10 inches each)', '4 inch designer pots x 3', 'Bright Indirect to Medium Light', 'Once a week for all 3 plants', 'Very Low', 0, 'Complete care guide booklet included in the box. Perfect for birthdays, housewarmings, or corporate desk setups.', 'Special triple-plant shock-absorbent gift packaging with greeting card.', 1, 1, 4.94, 76),

(13, 7, 'Exotic Herb & Salad Garden Seed Kit (12 Varieties)', 'exotic-herb-salad-seed-kit-12-varieties', 'PP-SD-013', 'Non-GMO seeds for Basil, Cherry Tomato, Coriander, Mint, Lettuce, Spinach & more.', 'Transform your balcony into a gourmet edible garden. Includes 12 individual seed packets with 85%+ guaranteed germination rate, plant tags, and an illustrated sowing calendar for all Indian seasons.', 399.00, 599.00, 50, 'N/A (Seeds)', 'N/A', 'Sunlight varies by crop', 'Keep seedbed moist with fine mist', 'Low', 1, 'Detailed step-by-step germination guide included. Sowing depth: 2-3 times seed size.', 'Sealed moisture-barrier aluminium foil packets.', 0, 1, 4.82, 45),

(14, 8, 'Handcrafted Ribbed Ceramic Planter (Set of 2 - Forest Green)', 'handcrafted-ribbed-ceramic-planter-forest-green', 'PP-PT-014', 'Heavy glazed artisanal planters with built-in drainage holes and matching saucers.', 'Elevate your plant presentation with these luxurious hand-glazed ceramic pots. Finished in deep forest green with tactile vertical fluting that catches the light beautifully in modern interiors.', 649.00, 899.00, 25, 'Height: 5.5 inches, Dia: 6 inches', '6 inch internal diameter', 'N/A', 'N/A', 'Zero', 1, 'Includes drainage hole rubber stopper and under-saucer to safeguard wooden floors.', 'Double bubble-wrapped with high-density EPS corner protectors.', 1, 0, 4.87, 28),

(15, 10, 'Organic Enriched Vermicompost (5 Kg Bag)', 'organic-enriched-vermicompost-5kg', 'PP-FT-015', 'Pure odorless worm castings enriched with neem cake, bone meal, and beneficial bio-enzymes.', 'Nature\'s black gold for every houseplant and garden vegetable. Provides slow-release nitrogen, phosphorus, and potassium while enhancing soil moisture retention and microbial biodiversity.', 299.00, 399.00, 120, 'N/A', '5 Kilogram sealed bag', 'N/A', 'N/A', 'Zero', 1, 'Application: Top-dress 2-3 tablespoons per pot once every 3-4 weeks. Scratch lightly into topsoil and water thoroughly.', 'Heavy-duty 50-micron polybag delivered directly to your doorstep.', 0, 1, 4.93, 89),

(16, 11, 'Ergonomic Japanese Bypass Pruning Shears (SK5 Steel)', 'japanese-bypass-pruning-shears-sk5-steel', 'PP-TL-016', 'Razor-sharp rust-proof carbon steel blades for effortless branch trimming and harvesting.', 'Engineered for clean, precise cuts that heal quickly without crushing plant stems. Features comfort-grip non-slip handles and a safety thumb-lock switch for safe storage.', 499.00, 749.00, 40, 'Length: 8.2 inches', 'N/A', 'N/A', 'N/A', 'Zero', 1, 'Wipe clean with a light oil cloth after cutting sticky sap branches.', 'Supplied in blister pack with spare replacement torsion spring.', 0, 1, 4.89, 36),

(17, 10, 'Pure Cold-Pressed Neem Oil for Plants (250 ml Concentrate)', 'pure-cold-pressed-neem-oil-concentrate', 'PP-FT-017', '100% natural organic pesticide and fungicide. Effective against aphids, mealybugs, and mites.', 'Protect your greens without toxic chemical sprays. Cold-pressed from organic Indian neem seeds, rich in Azadirachtin. Emulsifies easily with water and a drop of liquid soap.', 249.00, 349.00, 90, '250 ml amber bottle', 'N/A', 'N/A', 'N/A', 'Zero', 1, 'Usage: Mix 5ml neem oil + 1ml mild dish liquid in 1 liter warm water. Shake well and spray both sides of leaves in late evening.', 'Leak-proof induction sealed bottle with measuring cap.', 0, 1, 4.90, 58),

(18, 9, 'Premium Ready Potting Mix for Indoor Plants (5 Kg)', 'premium-ready-potting-mix-indoor-plants-5kg', 'PP-SL-018', 'Soilless sterilized blend of cocopeat, perlite, vermiculite, biochar, and aged compost.', 'Eliminate root rot and soil gnats forever! This lightweight, fast-draining blend provides optimal aeration and moisture balance so plant roots breathe easily in pots.', 349.00, 499.00, 70, 'N/A', '5 Kg bag', 'N/A', 'N/A', 'Zero', 1, 'Ready to use straight out of the bag. No mixing or sterilization required.', 'Sealed moisture-regulated packaging.', 0, 1, 4.86, 41),

(19, 13, 'Variegated Syngonium / Arrowhead Plant', 'variegated-syngonium-arrowhead-plant', 'PP-IN-019', 'Delightful marbled white and pastel green arrow foliage that brightens study tables.', 'A dynamic fast-grower with heart-to-arrow shaped foliage. The variegation patterns are unique on every single leaf, creating an ever-changing living sculpture.', 229.00, 319.00, 45, '7 - 10 inches', '4 inch grower pot', 'Medium to Bright Indirect Light', 'Twice a week; loves light misting', 'Low', 0, 'Light: Bright diffused light retains sharp white marbling. Water: Keep soil lightly moist. Easily propagated from stem cuttings in water.', 'Delivered in plastic nursery pot with drainage holes.', 0, 0, 4.78, 19),

(20, 2, 'Bougainvillea Dwarf Hybrid (Pink Flame)', 'bougainvillea-dwarf-hybrid-pink-flame', 'PP-OD-020', 'Profuse tropical blooms of magenta-pink bracts. Drought-hardy balcony champion.', 'Bougainvilleas adore hot Indian summers. This compact dwarf hybrid doesn\'t grow into an unruly jungle but stays neatly contained in patio containers, smothering itself in shocking pink blossoms.', 299.00, 429.00, 35, '12 - 16 inches', '6 inch container', 'Full Blazing Sun (6+ hours)', 'Sparingly; water only when dry to trigger maximum blooms', 'Low', 1, 'Light: The more sun, the more flowers! Water: Do not overwater; stress promotes blooming. Feed: High potassium organic fertilizer every 4 weeks.', 'Healthy blooming stock packed with sturdy stem braces.', 1, 0, 4.76, 22),

(21, 5, 'Kagzi Lime / Indian Nimbu Grafted Plant', 'kagzi-lime-indian-nimbu-grafted-plant', 'PP-FR-021', 'Prolific producer of thin-skinned, extra juicy sour limes. Year-round fruiting in terrace pots.', 'The indispensable Kagzi Lemon brings fresh vitamin C straight from your terrace. Grafted to start bearing fragrant blossoms and juicy yellow-green limes early. Fragrant leaves repel pests.', 349.00, 499.00, 30, '1.5 - 2 feet', '7 inch bag', 'Direct Sun', 'Water when topsoil feels dry', 'Moderate', 1, 'Light: Full sun. Feed: Feed with mustard cake liquid and compost every 40 days. Prune water suckers.', 'Grafted sapling with established root network.', 0, 1, 4.81, 31),

(22, 1, 'Monstera Deliciosa (Swiss Cheese Plant)', 'monstera-deliciosa-swiss-cheese-plant', 'PP-IN-022', 'Iconic jungle giant with split fenestrated leaves that brings high-end botanic luxury home.', 'The crown jewel of modern urban indoor jungles! As Monstera Deliciosa matures, its grand deep-green leaves develop dramatic natural splits and perforations known as fenestrations.', 599.00, 899.00, 25, '14 - 18 inches', '6 inch pot with starter moss pole', 'Bright Indirect Sunlight', 'Once a week; allow top 2 inches to dry', 'Moderate', 0, 'Light: Medium to bright indirect sunlight. Wipe leaves with damp cloth monthly to clear dust and enhance photosynthesis. Guide aerial roots into the soil or moss pole.', 'Ships with secure leaf strapping and reinforced root anchor.', 1, 1, 4.97, 67),

(23, 7, 'Organic Desi Vegetable Seed Box (8 Kitchen Essentials)', 'organic-desi-vegetable-seed-box', 'PP-SD-023', 'Bhindi (Okra), Palak, Desi Tomato, Baingan, Hari Mirch, Lauki, Karela & Dhaniya.', 'Cultivate clean, chemical-free greens for your family kitchen. 8 traditional heirloom Indian vegetable varieties selected for high yield, heat tolerance, and authentic flavor.', 249.00, 349.00, 85, 'N/A', 'Seed Box with 8 packs', 'Full sun for all vegetables', 'Regular gentle watering', 'Low', 1, 'Includes planting depth chart and companion planting tips.', 'Individual airtight sachets.', 0, 1, 4.84, 52),

(24, 12, 'Hand-Painted Terracotta Owl Garden Miniature Set', 'hand-painted-terracotta-owl-garden-miniatures', 'PP-DC-024', 'Whimsical trio of waterproof ceramic owls to decorate flower pots, trays, and desks.', 'Bring playful charm to your succulent bowls, bonsai trays, or fairy gardens with these colorful, hand-painted ceramic figurines. Weatherproof and UV-resistant.', 199.00, 299.00, 60, '2.5 inches each (Set of 3)', 'N/A', 'N/A', 'N/A', 'Zero', 1, 'Clean with dry or damp cloth.', 'Individually foam packed in recyclable craft box.', 0, 0, 4.88, 17);

-- Product Variants (Sizes and Pot Options)
INSERT INTO `product_variants` (`product_id`, `name`, `sku`, `price`, `stock_quantity`) VALUES
(1, '4 Inch Nursery Pot (Standard)', 'PP-IN-001-4N', 299.00, 30),
(1, '5 Inch Ceramic Planter (Forest Green)', 'PP-IN-001-5C', 499.00, 15),
(2, '5 Inch Nursery Pot (Standard)', 'PP-AP-002-5N', 349.00, 20),
(2, '6 Inch Self-Watering Planter (White)', 'PP-AP-002-6SW', 549.00, 10),
(4, '7 Inch Nursery Pot (3 Feet Tall)', 'PP-AP-004-7N', 599.00, 15),
(4, '9 Inch Premium Textured Planter (4 Feet Tall)', 'PP-AP-004-9P', 999.00, 10),
(5, '5 Inch Nursery Pot', 'PP-IN-005-5N', 449.00, 25),
(5, '6 Inch Glazed Earthen Pot', 'PP-IN-005-6E', 699.00, 15),
(22, '6 Inch Pot with Moss Stick (Standard)', 'PP-IN-022-6M', 599.00, 15),
(22, '8 Inch Pot with Extended Moss Stick (Specimen)', 'PP-IN-022-8M', 999.00, 10);

-- Product Images (Primary and Alternate Views)
INSERT INTO `product_images` (`product_id`, `image_path`, `alt_text`, `is_primary`, `sort_order`) VALUES
(1, 'snake-plant.jpg', 'Sansevieria Golden Hahnii Dwarf Snake Plant in Ceramic Pot', 1, 1),
(1, 'snake-plant-2.jpg', 'Snake Plant Top View Rosette Pattern', 0, 2),
(2, 'peace-lily.jpg', 'Peace Lily Spathiphyllum with White Bloom in Living Room', 1, 1),
(2, 'peace-lily-2.jpg', 'Peace Lily Dark Foliage Close-up', 0, 2),
(3, 'money-plant.jpg', 'Golden Money Plant Trailing Vine in Hanging Pot', 1, 1),
(3, 'money-plant-2.jpg', 'Money Plant Heart Shaped Leaves Detail', 0, 2),
(4, 'areca-palm.jpg', 'Areca Palm Luxury Houseplant in Self Watering Pot', 1, 1),
(5, 'zz-plant.jpg', 'ZZ Plant Glossy Emerald Green Leaves', 1, 1),
(6, 'mogra.jpg', 'Fragrant Mogra Arabian Jasmine Flowering Bush', 1, 1),
(7, 'mango-sapling.jpg', 'Grafted Alphonso Mango Fruit Plant in Container', 1, 1),
(8, 'desi-gulab.jpg', 'Desi Red Rose Fragrant Bush with Flowers', 1, 1),
(9, 'fiddle-leaf-fig.jpg', 'Fiddle Leaf Fig Bambino Houseplant', 1, 1),
(10, 'jade-plant.jpg', 'Crassula Ovata Jade Plant Good Luck Succulent', 1, 1),
(11, 'tulsi.jpg', 'Sacred Krishna Tulsi Holy Basil Plant with Brass Pot Wrap', 1, 1),
(12, 'plant-combo.jpg', 'Workplace Zen 3 Air Purifier Plant Combo Set', 1, 1),
(13, 'seeds-kit.jpg', 'Exotic Herb & Salad Garden 12 Seed Kit', 1, 1),
(14, 'ceramic-planter.jpg', 'Handcrafted Ribbed Ceramic Planters Set in Forest Green', 1, 1),
(15, 'vermicompost.jpg', 'Organic Enriched Vermicompost 5Kg Bag', 1, 1),
(16, 'pruning-shears.jpg', 'Japanese Carbon Steel Bypass Pruning Shears', 1, 1),
(17, 'neem-oil.jpg', 'Pure Cold Pressed Organic Neem Oil for Plants 250ml', 1, 1),
(18, 'potting-mix.jpg', 'Premium Ready Potting Mix 5Kg Soilless Blend', 1, 1),
(19, 'syngonium.jpg', 'Variegated Syngonium Arrowhead Houseplant', 1, 1),
(20, 'bougainvillea.jpg', 'Bougainvillea Dwarf Hybrid Pink Flame in Bloom', 1, 1),
(21, 'kagzi-lime.jpg', 'Grafted Kagzi Lime Lemon Plant with Fruits', 1, 1),
(22, 'monstera.jpg', 'Monstera Deliciosa Swiss Cheese Plant with Moss Stick', 1, 1),
(23, 'veggie-seeds.jpg', 'Organic Desi Vegetable Seed Box 8 Kitchen Varieties', 1, 1),
(24, 'owl-decor.jpg', 'Hand Painted Terracotta Owl Garden Figurines Set of 3', 1, 1);

-- Sample Promotional Coupons
INSERT INTO `coupons` (`code`, `description`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount_amount`, `usage_limit`, `is_active`, `expires_at`) VALUES
('WELCOME10', 'Flat 10% OFF on your first green order', 'percentage', 10.00, 299.00, 200.00, 5000, 1, '2027-12-31 23:59:59'),
('PLANTGREEN', 'Special ₹150 OFF on orders above ₹999', 'fixed', 150.00, 999.00, 150.00, 2000, 1, '2027-12-31 23:59:59'),
('MONSOON20', 'Monsoon Gardening Festival 20% OFF', 'percentage', 20.00, 1499.00, 400.00, 1000, 1, '2027-12-31 23:59:59');

-- Promotional Banners and Announcement Items
INSERT INTO `banners` (`title`, `subtitle`, `badge_text`, `button_text`, `button_link`, `image_path`, `type`, `sort_order`, `is_active`) VALUES
('Bring Nature Inside Your Home', 'Hand-picked, nursery-fresh indoor plants that purify the air and uplift your spirits with safe doorstep delivery across India.', 'Nursery Fresh Guarantee', 'Explore Bestsellers', '/products?category=indoor-plants', 'hero-indoor-plants.jpg', 'hero', 1, 1),
('Monsoon Blossom & Fruit Harvest', 'Grafted Mango, Fragrant Mogra, Desi Roses, and Balcony Citrus trees ready to fruit and bloom this season.', 'Special Seasonal Picks', 'Shop Fruit & Flowers', '/products?category=fruit-plants', 'hero-flowering.jpg', 'hero', 2, 1),
('🌱 Monsoon Green Fest: Use Code PLANTGREEN for ₹150 OFF on ₹999+ | Free Shipping Across India on ₹999+', NULL, NULL, NULL, NULL, NULL, 'announcement', 1, 1),
('Workplace Greenery Bundles', 'Transform your desk and study with zero-fuss succulent and snake plant trios.', 'Value Combos', 'Shop Bundles', '/products?category=plant-combos-and-gift-plants', 'promo-combo.jpg', 'promo_mid', 1, 1);

-- Verified Customer Reviews
INSERT INTO `product_reviews` (`product_id`, `user_id`, `customer_name`, `customer_email`, `rating`, `title`, `comment`, `is_verified_purchase`, `status`) VALUES
(1, 2, 'Priya S.', 'priya.s@example.com', 5, 'Arrived in pristine condition!', 'I was hesitant about ordering live plants online to Bengaluru, but the packaging of this Snake plant blew me away. Not a single grain of soil spilled, and the golden variegation is so bright. Living happily on my desk for 3 weeks now.', 1, 'approved'),
(1, NULL, 'Rohan Verma', 'rohan.v@example.com', 5, 'Super low maintenance', 'Forgot to water it for 12 days while on a business trip, came back and it still looked fresh as day one. Truly indestructible.', 1, 'approved'),
(2, 2, 'Meenakshi Iyer', 'meenakshi@example.com', 5, 'Blooming beautifully', 'The Peace Lily started producing a second white bloom within 10 days of arrival. Leaves are deep waxy green. Excellent nursery quality!', 1, 'approved'),
(3, NULL, 'Amitabh Roy', 'amitabh@example.com', 5, 'Thick healthy vines', 'Grown over 1 foot longer already! Great price compared to local roadside nurseries where plants often die within a week.', 1, 'approved'),
(22, NULL, 'Sunita Deshmukh', 'sunita.d@example.com', 5, 'Stunning Monstera specimen', 'Big healthy leaves with beautiful splits. The packaging had a wooden support frame to prevent bending during transit to Pune. Highly recommend PlantPuglu!', 1, 'approved');

-- Sample Initial Orders
INSERT INTO `orders` (`order_number`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `shipping_landmark`, `shipping_city`, `shipping_state`, `shipping_pincode`, `subtotal`, `discount_amount`, `shipping_fee`, `total_amount`, `payment_method`, `payment_status`, `order_status`, `tracking_number`, `courier_name`, `coupon_code`) VALUES
('PP-2026-1001', 2, 'Aarav Sharma', 'aarav.sharma@example.in', '+91 98111 22334', 'Flat 402, Lotus Greens Apartment, Sector 14, HSR Layout', 'Near BDA Complex', 'Bengaluru', 'Karnataka', '560102', 1247.00, 150.00, 0.00, 1097.00, 'razorpay', 'paid', 'delivered', 'DTDC-BLR-883921', 'DTDC Express', 'PLANTGREEN'),
('PP-2026-1002', NULL, 'Divya Patel', 'divya.patel@example.com', '+91 99222 33445', '703 Green Meadows, Baner Road', 'Opposite Orchid School', 'Pune', 'Maharashtra', '411045', 548.00, 0.00, 99.00, 647.00, 'cod', 'pending', 'processing', 'BLUEDART-PN-9921', 'BlueDart', NULL);

-- Sample Order Items
INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `product_sku`, `variant_name`, `price`, `quantity`, `subtotal`) VALUES
(1, 1, 'Sansevieria Golden Hahnii (Snake Plant)', 'PP-IN-001', '4 Inch Nursery Pot (Standard)', 299.00, 1, 299.00),
(1, 2, 'Peace Lily (Spathiphyllum) - Air Purifier', 'PP-AP-002', '5 Inch Nursery Pot (Standard)', 349.00, 1, 349.00),
(1, 22, 'Monstera Deliciosa (Swiss Cheese Plant)', 'PP-IN-022', '6 Inch Pot with Moss Stick', 599.00, 1, 599.00),
(2, 3, 'Golden Money Plant (Epipremnum Aureum)', 'PP-IN-003', 'Standard Pot with Hanger', 199.00, 1, 199.00),
(2, 6, 'Mogra / Arabian Jasmine (Jasminum Sambac)', 'PP-FL-006', 'Standard Flowering Bush', 249.00, 1, 249.00),
(2, 11, 'Krishna Tulsi / Holy Basil (Sacred Herb)', 'PP-HB-011', 'Sacred Live Plant', 149.00, 1, 149.00);

-- Sample Payment Log
INSERT INTO `payments` (`order_id`, `payment_method`, `transaction_id`, `razorpay_order_id`, `razorpay_payment_id`, `razorpay_signature`, `amount`, `currency`, `status`) VALUES
(1, 'razorpay', 'pay_live_sample_1001', 'order_rzp_mock_1001', 'pay_sample_proof_9921', 'mock_verified_signature_token', 1097.00, 'INR', 'captured'),
(2, 'cod', 'cod_pending_1002', NULL, NULL, NULL, 647.00, 'INR', 'initiated');

SET FOREIGN_KEY_CHECKS = 1;
