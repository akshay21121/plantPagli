# 🚀 PlantPuglu — Ubuntu Server & AWS EC2 Deployment Guide

A step-by-step beginner-friendly guide to deploying **PlantPuglu** on a fresh Ubuntu 22.04 / 24.04 LTS instance (AWS EC2, DigitalOcean, Linode, or Hetzner).

---

## 📑 Step-by-Step Overview

1. [Server Provisioning & System Updates](#step-1-system-updates--firewall)
2. [Install PHP 8.2+ and Extensions](#step-2-install-php-82-and-required-extensions)
3. [Install & Secure MySQL 8.0](#step-3-install--configure-mysql-80)
4. [Deploy PlantPuglu Codebase & Set Permissions](#step-4-deploy-plantpuglu-codebase)
5. [Configure Web Server (Nginx or Apache)](#step-5-configure-web-server)
6. [Configure Environment Variables (`.env`)](#step-6-configure-environment-variables)
7. [Import Database Schema & Sample Products](#step-7-import-database-schema)
8. [Create the First Administrator](#step-8-create-administrator)
9. [Configure Free HTTPS SSL with Let's Encrypt (Certbot)](#step-9-enable-https-using-certbot)
10. [Cloudflare DNS Setup (Optional)](#step-10-cloudflare-dns-configuration)
11. [Configure Automated Nightly Database Backups](#step-11-automated-database-backups)
12. [Troubleshooting & Common Fixes](#step-12-troubleshooting--common-issues)

---

## Step 1: System Updates & Firewall

Connect to your Ubuntu server via SSH:
```bash
ssh ubuntu@YOUR_SERVER_IP
```

Update system packages:
```bash
sudo apt update && sudo apt upgrade -y
```

Configure UFW firewall:
```bash
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw --force enable
```

---

## Step 2: Install PHP 8.2 and Required Extensions

Add the Ondřej Surý PPA for official PHP releases:
```bash
sudo apt install -y software-properties-common ca-certificates lsb-release apt-transport-https
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
```

Install PHP 8.2, PHP-FPM, and essential e-commerce extensions:
```bash
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-curl php8.2-mbstring \
                    php8.2-xml php8.2-zip php8.2-gd php8.2-opcache
```

Verify the installation:
```bash
php -v
sudo systemctl status php8.2-fpm --no-pager
```

---

## Step 3: Install & Configure MySQL 8.0

Install MySQL server:
```bash
sudo apt install -y mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql
```

Run the security script:
```bash
sudo mysql_secure_installation
```

Log in to MySQL as root:
```bash
sudo mysql
```

Create a dedicated database and a restricted database user:
```sql
CREATE DATABASE plantpagli CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Replace 'YourStrongPassword123!' with your own secure password
CREATE USER 'plantuser'@'localhost' IDENTIFIED BY 'YourStrongPassword123!';

-- Grant privileges only to the plantpagli database
GRANT ALL PRIVILEGES ON plantpagli.* TO 'plantuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## Step 4: Deploy PlantPuglu Codebase

Create the web root directory:
```bash
sudo mkdir -p /var/www/plantpagli
sudo chown -R $USER:$USER /var/www/plantpagli
```

Transfer or upload your PlantPuglu project files to `/var/www/plantpagli/` (using SCP, SFTP, or Git).

### Set Correct Directory Ownership & Permissions:
```bash
# Grant www-data access to upload directories
sudo chown -R www-data:www-data /var/www/plantpagli/public/uploads
sudo chmod -R 775 /var/www/plantpagli/public/uploads

# Ensure PHP scripts are readable
sudo chmod -R 755 /var/www/plantpagli
```

---

## Step 5: Configure Web Server

### Option A: Using Nginx (Recommended for High Performance)

Install Nginx:
```bash
sudo apt install -y nginx
```

Create an Nginx configuration file:
```bash
sudo nano /etc/nginx/sites-available/plantpuglu
```

Paste the following configuration (replace `plantpagli.com` with your actual domain):
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name plantpagli.com www.plantpagli.com;
    
    root /var/www/plantpagli/public;
    index index.php index.html;

    charset utf-8;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Static assets caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|webp|svg)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }

    # Prevent direct access to hidden files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Block PHP execution in upload folders
    location ~* /uploads/.*\.(php|phps|phtml|sh)$ {
        deny all;
    }

    # PHP-FPM Handler
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    client_max_body_size 10M;
}
```

Enable the configuration and reload Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/plantpuglu /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

---

## Step 6: Configure Environment Variables

Navigate to the project root and create `.env`:
```bash
cd /var/www/plantpagli
cp .env.example .env
nano .env
```

Set your production values:
```ini
APP_ENV=production
APP_DEBUG=false
APP_NAME=PlantPuglu
APP_URL=https://plantpagli.com
APP_SECRET=GenerateA64CharacterRandomHexSecretStringHere1234567890

# Database Credentials
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=plantpagli
DB_USER=plantuser
DB_PASS=YourStrongPassword123!
DB_CHARSET=utf8mb4

# Payment Gateway (Razorpay India)
RAZORPAY_KEY_ID=rzp_live_YourLiveKeyIdHere
RAZORPAY_KEY_SECRET=YourLiveKeySecretHere

SESSION_SECURE_COOKIE=true
```

Secure the `.env` file so unauthorized users cannot read database passwords:
```bash
sudo chmod 600 /var/www/plantpagli/.env
```

---

## Step 7: Import Database Schema

Import the schema, initial categories, nursery products, coupons, and settings:
```bash
mysql -u plantuser -p plantpagli < /var/www/plantpagli/database.sql
```

---

## Step 8: Create Administrator

Run the secure CLI script to create your administrative user:
```bash
php /var/www/plantpagli/bin/create_admin.php "Store Manager" "admin@plantpagli.com" "YourMasterAdminPassword123"
```

---

## Step 9: Enable HTTPS using Certbot

Install Certbot:
```bash
sudo apt install -y certbot python3-certbot-nginx
```

Obtain and automatically configure your SSL certificate:
```bash
sudo certbot --nginx -d plantpagli.com -d www.plantpagli.com
```
Certbot will configure SSL certificates and renewal crons automatically.

---

## Step 10: Cloudflare DNS Configuration (Optional)

1. In your Cloudflare Dashboard, add two `A` records:
   - `@` &rarr; Points to your Ubuntu server IP address (Proxied: Orange Cloud).
   - `www` &rarr; Points to your Ubuntu server IP address (Proxied: Orange Cloud).
2. Under **SSL/TLS &rarr; Overview**, select **Full (Strict)** encryption mode (since Certbot is installed on the origin server).
3. Under **Security &rarr; WAF**, default DDoS protection and bot mitigations will automatically safeguard your checkout.

---

## Step 11: Automated Database Backups

Create a backup script:
```bash
sudo mkdir -p /var/backups/plantpuglu
sudo nano /usr/local/bin/backup_plantpuglu.sh
```

Paste:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/plantpuglu"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
mysqldump -u plantuser -p'YourStrongPassword123!' plantpagli | gzip > "$BACKUP_DIR/db_$TIMESTAMP.sql.gz"
# Keep only 14 days of backups
find $BACKUP_DIR -name "*.sql.gz" -mtime +14 -exec rm {} \;
```

Make it executable:
```bash
sudo chmod +x /usr/local/bin/backup_plantpuglu.sh
```

Schedule in cron to run every night at 2:00 AM:
```bash
sudo crontab -e
# Add:
0 2 * * * /usr/local/bin/backup_plantpuglu.sh >/dev/null 2>&1
```

---

## Step 12: Troubleshooting & Common Issues

| Issue | Cause | Solution |
| :--- | :--- | :--- |
| **"SQLSTATE[HY000] Access Denied"** | Incorrect DB credentials in `.env` | Verify DB_USER and DB_PASS in `.env` match MySQL grant. |
| **"404 Not Found" on subpages** | Nginx `try_files` missing or Apache `mod_rewrite` disabled | Check `try_files $uri $uri/ /index.php?$query_string;` in Nginx or run `sudo a2enmod rewrite && sudo systemctl restart apache2`. |
| **"Failed to upload image"** | Upload folder permissions | Run `sudo chown -R www-data:www-data /var/www/plantpagli/public/uploads && sudo chmod -R 775 /var/www/plantpagli/public/uploads`. |
| **"Razorpay verification failed"** | Secret mismatch | Verify that `RAZORPAY_KEY_SECRET` in `.env` exactly matches the secret in Razorpay Dashboard. |

---

You now have a production-grade, secure, and fast deployment of **PlantPuglu** running live!
