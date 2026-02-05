# FusionPBX Laravel UI - Installation Guide

This guide provides step-by-step instructions for installing the FusionPBX Laravel UI on your server.

## Prerequisites

Before starting, ensure you have:

- A working FusionPBX installation
- Root or sudo access to your server
- PostgreSQL with the FusionPBX database
- PHP 8.2 or higher
- Composer
- Node.js 18+ and NPM
- Nginx or Apache web server

## Step 1: Check System Requirements

```bash
# Check PHP version (must be 8.2+)
php -v

# Check PostgreSQL version
psql --version

# Check Composer
composer --version

# Check Node.js and NPM
node -v
npm -v
```

If any are missing, install them:

### Install PHP 8.2 (Debian/Ubuntu)

```bash
sudo apt update
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-cli php8.2-pgsql php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-intl
```

### Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### Install Node.js 18+ (Ubuntu/Debian)

```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

## Step 2: Clone or Download the Repository

```bash
# Navigate to web directory
cd /var/www

# Clone the repository (adjust URL as needed)
git clone https://github.com/your-repo/fusionpbx.git fusionpbx-laravel

# Navigate to Laravel UI directory
cd fusionpbx-laravel/laravel-ui
```

## Step 3: Install Dependencies

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
npm install
```

## Step 4: Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit .env file
nano .env
```

### Configure Database Connection

Update these lines in `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=fusionpbx
DB_USERNAME=fusionpbx
DB_PASSWORD=your_actual_password
```

**Important**: Use your actual FusionPBX database credentials.

### Configure Application Settings

```env
APP_NAME="FusionPBX Laravel UI"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-server-ip:8080
```

### (Optional) Configure AI Providers

If you plan to use AI features:

```env
OPENAI_API_KEY=sk-your-openai-key
ANTHROPIC_API_KEY=sk-ant-your-anthropic-key
GOOGLE_AI_API_KEY=your-google-ai-key
```

## Step 5: Run Database Migrations

This will create only the new campaign-related tables:

```bash
php artisan migrate
```

**Note**: This does NOT modify existing FusionPBX tables. It only creates:
- v_campaigns
- v_campaign_contacts
- v_campaign_calls

## Step 6: Build Frontend Assets

```bash
# For production
npm run build

# For development (with hot reload)
npm run dev
```

## Step 7: Set Permissions

```bash
# Set ownership to web server user
sudo chown -R www-data:www-data storage bootstrap/cache

# Set proper permissions
sudo chmod -R 775 storage bootstrap/cache
```

## Step 8: Configure Web Server

### Option A: Using Port 8080 (Recommended)

This runs alongside FusionPBX without conflicts.

```bash
# Copy Nginx configuration
sudo cp nginx-laravel-port.conf /etc/nginx/sites-available/fusionpbx-laravel

# Edit the configuration
sudo nano /etc/nginx/sites-available/fusionpbx-laravel
```

Update these lines:
- `server_name` - Set to your server IP or domain
- `root` - Set to full path of your public directory

```nginx
server_name 192.168.1.100;  # Your server IP
root /var/www/fusionpbx-laravel/laravel-ui/public;
```

Enable the site:

```bash
# Create symbolic link
sudo ln -s /etc/nginx/sites-available/fusionpbx-laravel /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

### Option B: Using Subdomain with SSL

```bash
# Copy subdomain configuration
sudo cp nginx-laravel-subdomain.conf /etc/nginx/sites-available/fusionpbx-laravel-subdomain

# Edit configuration
sudo nano /etc/nginx/sites-available/fusionpbx-laravel-subdomain
```

Update:
- `server_name admin.yourdomain.com;`
- `root /var/www/fusionpbx-laravel/laravel-ui/public;`
- SSL certificate paths

Enable and reload:

```bash
sudo ln -s /etc/nginx/sites-available/fusionpbx-laravel-subdomain /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Step 9: Configure PHP-FPM

Ensure PHP-FPM is running:

```bash
# Check status
sudo systemctl status php8.2-fpm

# Start if not running
sudo systemctl start php8.2-fpm

# Enable on boot
sudo systemctl enable php8.2-fpm
```

## Step 10: Test Installation

### Test Database Connection

```bash
php artisan tinker
```

In tinker:

```php
DB::connection()->getPdo();
\App\Models\Domain::count();
exit
```

### Access the Application

Open your browser and navigate to:
- **Port 8080**: `http://your-server-ip:8080`
- **Subdomain**: `https://admin.yourdomain.com`

You should see the FusionPBX Laravel UI dashboard.

## Step 11: Verify Installation

Check that you can:

1. ✅ Access the dashboard
2. ✅ View users list
3. ✅ View extensions list
4. ✅ View domains list
5. ✅ Access call center queues
6. ✅ Access campaigns
7. ✅ View CDR records

## Troubleshooting

### Issue: "Permission denied" errors

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Issue: "Could not find driver"

Install PostgreSQL PHP extension:

```bash
sudo apt install php8.2-pgsql
sudo systemctl restart php8.2-fpm
```

### Issue: "Connection refused" to database

Check PostgreSQL is running and credentials are correct:

```bash
sudo systemctl status postgresql
psql -U fusionpbx -d fusionpbx -h 127.0.0.1
```

### Issue: 404 on all pages

Check Nginx configuration and ensure `try_files` directive is correct:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### Issue: Assets not loading (CSS/JS)

Rebuild assets:

```bash
npm run build
```

Clear Laravel cache:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Issue: "419 Page Expired" on form submission

Clear cache and check APP_KEY is set:

```bash
php artisan config:clear
grep APP_KEY .env
```

## Security Hardening (Production)

### 1. Disable Debug Mode

In `.env`:
```env
APP_DEBUG=false
```

### 2. Set Secure Permissions

```bash
find storage -type f -exec chmod 664 {} \;
find storage -type d -exec chmod 775 {} \;
```

### 3. Configure Firewall

```bash
# Allow only necessary ports
sudo ufw allow 8080/tcp
sudo ufw enable
```

### 4. Regular Updates

```bash
composer update
npm update
php artisan migrate
```

## Backup

Before any updates, backup:

```bash
# Backup database
pg_dump -U fusionpbx fusionpbx > fusionpbx_backup.sql

# Backup .env file
cp .env .env.backup

# Backup uploaded files (if any)
tar -czf storage_backup.tar.gz storage/app
```

## Next Steps

- Configure campaigns and import contacts
- Set up call center queues and agents
- Configure AI providers (if needed)
- Customize Tailwind theme (optional)
- Set up automated backups

For quick setup commands, see [QUICKSTART.md](QUICKSTART.md).

For usage instructions, see [README.md](README.md).

## Support

If you encounter issues:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check Nginx error log: `/var/log/nginx/error.log`
3. Check PHP-FPM log: `/var/log/php8.2-fpm.log`
4. Visit FusionPBX forums: https://www.fusionpbx.com

---

**Installation Complete!** 🎉

You now have a fully functional Laravel UI for FusionPBX.
