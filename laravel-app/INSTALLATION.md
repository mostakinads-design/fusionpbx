# Quick Installation Guide - FusionPBX Laravel App

This guide provides step-by-step instructions for quickly setting up the Laravel 12 + Filament 4 application.

## Prerequisites Checklist

- [ ] PHP 8.3 or higher installed
- [ ] Composer 2.0+ installed
- [ ] MySQL 8.0+ installed and running
- [ ] Node.js 18+ installed (optional, for asset compilation)
- [ ] Git installed
- [ ] Terminal/command line access

## Installation Steps

### Step 1: Navigate to Application Directory

```bash
cd /path/to/fusionpbx/laravel-app
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

This will install all required Laravel and Filament packages.

### Step 3: Configure Environment

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Database

Edit the `.env` file and update database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fusionpbx_laravel
DB_USERNAME=your_mysql_username
DB_PASSWORD=your_mysql_password
```

### Step 5: Create Database

```bash
# Login to MySQL
mysql -u root -p

# In MySQL prompt, run:
CREATE DATABASE fusionpbx_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Step 6: Run Migrations

```bash
php artisan migrate
```

This creates all necessary database tables.

### Step 7: Seed Database (Optional but Recommended)

```bash
php artisan db:seed
```

This creates:
- **Admin User**: email: `admin@fusionpbx.com`, password: `password123`
- **4 Test Users**: email: `user{1-4}@fusionpbx.com`, password: `password123`
- **8 Sample Extensions**: 1001-1008
- **50 Sample Call Logs**
- **12 Default Settings**

### Step 8: Create Storage Link

```bash
php artisan storage:link
```

### Step 9: Set Permissions (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
```

### Step 10: Create Filament Admin User (If Not Using Seeder)

If you didn't run the seeder, create an admin user manually:

```bash
php artisan make:filament-user
```

Follow the prompts to create your admin account.

### Step 11: Start Development Server

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### Step 12: Access the Application

#### Admin Panel (Filament)
- URL: `http://localhost:8000/admin`
- Email: `admin@fusionpbx.com`
- Password: `password123` (if using seeder)

#### API Endpoints
- Base URL: `http://localhost:8000/api`
- See `API_DOCUMENTATION.md` for all endpoints

## Quick Test

Test that everything is working:

```bash
# Test API login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@fusionpbx.com","password":"password123"}'

# Expected response: JSON with success: true and a token
```

## Production Deployment (Quick)

For production deployment:

1. **Set environment to production:**
```env
APP_ENV=production
APP_DEBUG=false
```

2. **Optimize application:**
```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. **Set up web server** (Apache or Nginx) pointing to `/public` directory

4. **Enable HTTPS** using Let's Encrypt or similar

5. **Set proper permissions:**
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## Troubleshooting Common Issues

### Issue: "Connection refused" when accessing MySQL

**Solution:**
```bash
# Check if MySQL is running
sudo systemctl status mysql

# Start MySQL if not running
sudo systemctl start mysql
```

### Issue: "Permission denied" errors

**Solution:**
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
```

### Issue: "Class not found" errors

**Solution:**
```bash
composer dump-autoload
php artisan clear-compiled
php artisan cache:clear
```

### Issue: "Route not found" for API endpoints

**Solution:**
```bash
php artisan route:clear
php artisan route:cache
php artisan route:list
```

### Issue: Can't access Filament admin panel

**Solution:**
```bash
# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan filament:upgrade

# Make sure you have an admin user
php artisan make:filament-user
```

## Default Credentials

### Admin User (Created by Seeder)
- **Email:** admin@fusionpbx.com
- **Password:** password123
- **Role:** admin

### Test Users
- **Emails:** user1@fusionpbx.com through user4@fusionpbx.com
- **Password:** password123
- **Role:** user

**⚠️ IMPORTANT: Change these passwords in production!**

## Verify Installation

Run these commands to verify everything is set up correctly:

```bash
# Check PHP version
php -v

# Check Laravel version
php artisan --version

# List all routes
php artisan route:list

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit

# Run tests
php artisan test
```

## Next Steps

1. ✅ Read `README_LARAVEL.md` for detailed documentation
2. ✅ Read `API_DOCUMENTATION.md` for API usage
3. ✅ Explore the Filament admin panel at `/admin`
4. ✅ Test API endpoints using Postman or cURL
5. ✅ Customize settings through the admin panel
6. ✅ Change default passwords
7. ✅ Configure email settings in `.env`
8. ✅ Set up backups
9. ✅ Configure monitoring and logging

## Useful Commands

```bash
# Clear all caches
php artisan optimize:clear

# View routes
php artisan route:list

# View available commands
php artisan list

# Interactive shell
php artisan tinker

# Create backup
php artisan db:seed --class=BackupSeeder

# View logs
tail -f storage/logs/laravel.log
```

## Getting Help

- Check Laravel docs: https://laravel.com/docs/12.x
- Check Filament docs: https://filamentphp.com/docs
- View logs in `storage/logs/laravel.log`
- Enable debug mode temporarily: `APP_DEBUG=true` in `.env`

---

**Installation Time:** ~10 minutes  
**Difficulty:** Easy  
**Last Updated:** February 2026
