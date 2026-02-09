# FusionPBX Laravel 12 + Filament 4 Application

A complete Laravel 12 application with Filament 4 admin panel and full MySQL integration for FusionPBX.

## 🚀 Features

### Core Features
- **Laravel 12** - Latest stable version with modern PHP 8.3+ support
- **Filament 4** - Beautiful admin panel with CRUD operations
- **MySQL Database** - Full MySQL 8.0+ integration with optimized schema
- **Laravel Sanctum** - API authentication for secure token-based access
- **Role-Based Access Control** - Admin and User roles
- **Soft Deletes** - Safe data deletion with recovery options
- **Database Indexing** - Optimized queries for performance

### Modules
1. **Users Management** - Complete user CRUD with role assignment
2. **Extensions Management** - Phone extension management with user assignment
3. **Call Logs** - Call history tracking with filtering and search
4. **System Settings** - Configurable application settings

### Admin Panel (Filament)
- Modern, responsive dashboard
- Complete CRUD interfaces for all modules
- Advanced filtering and search
- Bulk actions support
- Export functionality (CSV/Excel)
- Beautiful UI components

### API Features
- RESTful API endpoints
- Token-based authentication (Sanctum)
- Proper HTTP status codes
- JSON response formatting
- API rate limiting
- Comprehensive API documentation

## 📋 Requirements

### System Requirements
- **PHP** >= 8.3
- **Composer** >= 2.0
- **MySQL** >= 8.0
- **Node.js** >= 18.x (for asset compilation)
- **Ubuntu/Debian** OS (tested on Ubuntu 24.04 LTS)

### PHP Extensions Required
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- MySQL PDO driver

## 🔧 Installation

### Step 1: Environment Setup

```bash
# Navigate to the Laravel application directory
cd /path/to/fusionpbx/laravel-app

# Copy environment file
cp .env.example .env
```

### Step 2: Configure Environment Variables

Edit `.env` file:

```env
APP_NAME="FusionPBX Laravel"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

# MySQL Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fusionpbx_laravel
DB_USERNAME=your_mysql_user
DB_PASSWORD=your_mysql_password
```

### Step 3: Create MySQL Database

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE fusionpbx_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create user (optional, recommended for security)
CREATE USER 'fusionpbx_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON fusionpbx_laravel.* TO 'fusionpbx_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 4: Install Dependencies

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Generate application key
php artisan key:generate

# Install NPM dependencies (for asset compilation)
npm install
npm run build
```

### Step 5: Database Migration

```bash
# Run migrations
php artisan migrate

# Seed database with sample data (optional)
php artisan db:seed
```

### Step 6: Storage Setup

```bash
# Create storage link
php artisan storage:link

# Set proper permissions
chmod -R 775 storage bootstrap/cache
```

### Step 7: Create Admin User

```bash
# Create a Filament admin user
php artisan make:filament-user
```

Follow the prompts to create your admin account.

## 🗄️ Database Schema

### Users Table
- `id` - Primary key
- `name` - User's full name
- `email` - Unique email address
- `password` - Hashed password
- `role` - User role (admin/user)
- `email_verified_at` - Email verification timestamp
- `remember_token` - Session token
- `created_at`, `updated_at` - Timestamps
- `deleted_at` - Soft delete timestamp

### Extensions Table
- `id` - Primary key
- `extension_number` - Unique extension number
- `user_id` - Foreign key to users table
- `status` - Status (active/inactive/suspended)
- `description` - Extension description
- `created_at`, `updated_at` - Timestamps
- `deleted_at` - Soft delete timestamp

### Call Logs Table
- `id` - Primary key
- `caller_id` - Caller identification
- `destination` - Call destination
- `duration` - Call duration in seconds
- `status` - Call status (answered/missed/busy/failed)
- `call_type` - Call type (inbound/outbound)
- `call_date` - Call timestamp
- `created_at`, `updated_at` - Timestamps

### Settings Table
- `id` - Primary key
- `key` - Unique setting key
- `value` - Setting value
- `type` - Value type (string/boolean/integer/json)
- `group` - Setting group for organization
- `description` - Setting description
- `created_at`, `updated_at` - Timestamps

## 🎨 Accessing the Application

### Admin Panel (Filament)
```
URL: http://your-domain.com/admin
```

Login with the admin credentials you created.

### API Endpoints
```
Base URL: http://your-domain.com/api
```

#### Authentication
- `POST /api/login` - Login and get token
- `POST /api/logout` - Logout
- `POST /api/register` - Register new user

#### Users
- `GET /api/users` - List all users
- `GET /api/users/{id}` - Get specific user
- `POST /api/users` - Create user
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user

#### Extensions
- `GET /api/extensions` - List all extensions
- `GET /api/extensions/{id}` - Get specific extension
- `POST /api/extensions` - Create extension
- `PUT /api/extensions/{id}` - Update extension
- `DELETE /api/extensions/{id}` - Delete extension

#### Call Logs
- `GET /api/call-logs` - List call logs
- `GET /api/call-logs/{id}` - Get specific call log
- `POST /api/call-logs` - Create call log

#### Settings
- `GET /api/settings` - List all settings
- `GET /api/settings/{key}` - Get specific setting
- `POST /api/settings` - Create/update setting

## 🔐 API Authentication

### Getting an Access Token

```bash
curl -X POST http://your-domain.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

Response:
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "role": "admin"
  }
}
```

### Using the Token

```bash
curl -X GET http://your-domain.com/api/users \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Accept: application/json"
```

## 🚀 Running the Application

### Development Server

```bash
# Start Laravel development server
php artisan serve --host=0.0.0.0 --port=8000
```

Access at: `http://localhost:8000`

### Production Deployment

#### Using Apache

1. Configure virtual host:

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/fusionpbx/laravel-app/public

    <Directory /path/to/fusionpbx/laravel-app/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/fusionpbx_error.log
    CustomLog ${APACHE_LOG_DIR}/fusionpbx_access.log combined
</VirtualHost>
```

2. Enable modules and restart:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Using Nginx

1. Configure server block:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/fusionpbx/laravel-app/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

2. Restart Nginx:

```bash
sudo systemctl restart nginx
```

## 🔧 Maintenance

### Cache Management

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database Backup

```bash
# Create backup
mysqldump -u root -p fusionpbx_laravel > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore from backup
mysql -u root -p fusionpbx_laravel < backup_20260209_120000.sql
```

### Update Application

```bash
# Pull latest changes
git pull origin main

# Update dependencies
composer install --no-dev
npm install && npm run build

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan optimize
```

## 🐛 Troubleshooting

### Permission Issues

```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Database Connection Issues

1. Check MySQL service is running:
```bash
sudo systemctl status mysql
```

2. Verify credentials in `.env` file
3. Test connection:
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

### 500 Internal Server Error

1. Check Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

2. Enable debug mode temporarily:
```env
APP_DEBUG=true
```

3. Check web server error logs:
```bash
# Apache
sudo tail -f /var/log/apache2/error.log

# Nginx
sudo tail -f /var/log/nginx/error.log
```

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs/12.x)
- [Filament Documentation](https://filamentphp.com/docs)
- [Laravel Sanctum Documentation](https://laravel.com/docs/12.x/sanctum)
- [MySQL Documentation](https://dev.mysql.com/doc/)

## 🔒 Security Best Practices

1. **Always use HTTPS in production**
2. **Keep APP_DEBUG=false in production**
3. **Use strong database passwords**
4. **Regularly update dependencies**:
   ```bash
   composer update
   npm update
   ```
5. **Enable rate limiting for API endpoints**
6. **Regular security audits**:
   ```bash
   composer audit
   ```
7. **Regular database backups**
8. **Use environment-specific configurations**

## 📝 License

This Laravel application is part of the FusionPBX project and follows the same licensing terms.

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📧 Support

For issues and questions:
- Check the documentation
- Review Laravel logs
- Contact the development team

---

**Version:** 1.0.0  
**Last Updated:** February 2026  
**Maintained by:** FusionPBX Development Team
