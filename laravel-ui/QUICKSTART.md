# FusionPBX Laravel UI - Quick Start Guide

Get up and running in minutes with this quick installation guide.

## Prerequisites

- Existing FusionPBX installation
- PHP 8.2+, PostgreSQL, Composer, Node.js 18+

## Quick Installation

### 1. Install System Requirements (if needed)

```bash
# Ubuntu/Debian
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-cli php8.2-pgsql php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-gd nodejs npm -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. Clone and Install

```bash
# Navigate to web directory
cd /var/www

# Clone repository
git clone https://github.com/your-repo/fusionpbx.git fusionpbx-laravel
cd fusionpbx-laravel/laravel-ui

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install

# Setup environment
cp .env.example .env
php artisan key:generate
```

### 3. Configure Database

Edit `.env`:

```bash
nano .env
```

Update database credentials:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=fusionpbx
DB_USERNAME=fusionpbx
DB_PASSWORD=YOUR_FUSIONPBX_PASSWORD
APP_URL=http://YOUR_SERVER_IP:8080
```

### 4. Migrate and Build

```bash
# Run migrations (creates campaign tables only)
php artisan migrate

# Build frontend assets
npm run build

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 5. Configure Nginx

```bash
# Copy configuration
sudo cp nginx-laravel-port.conf /etc/nginx/sites-available/fusionpbx-laravel

# Edit configuration
sudo nano /etc/nginx/sites-available/fusionpbx-laravel
```

Update `server_name` and `root` path:

```nginx
server_name YOUR_SERVER_IP;
root /var/www/fusionpbx-laravel/laravel-ui/public;
```

Enable and reload:

```bash
sudo ln -s /etc/nginx/sites-available/fusionpbx-laravel /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 6. Access Application

Open browser: `http://YOUR_SERVER_IP:8080`

## Quick Test

Verify everything works:

```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
>>> \App\Models\Domain::count();
>>> exit
```

## Common Commands

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Rebuild assets
npm run build

# Run migrations
php artisan migrate

# View logs
tail -f storage/logs/laravel.log
```

## Feature Overview

### 📊 Dashboard
- View system statistics
- Monitor active campaigns
- Track call activity

### 👥 User Management
Navigate to: `/users`
- Create/edit users
- Assign to domains
- Manage permissions

### 📞 Extensions
Navigate to: `/extensions`
- Create extensions
- Configure caller ID
- Link to users

### 🏢 Domains
Navigate to: `/domains`
- Multi-tenant management
- Domain configuration

### 📱 Call Center
Navigate to: `/call-center-queues`
- Create queues
- Manage agents (Available, On Break, Logged Out)
- Assign agent tiers

### 📢 Campaigns
Navigate to: `/campaigns`
- Create voice/SMS campaigns
- Enable AI features
- Import contacts via CSV
- Start/pause/stop campaigns

### 📋 CDR (Call Records)
Navigate to: `/cdr`
- View call history
- Filter by date/extension
- Export to CSV
- Play recordings

## Campaign Quick Start

### Create Your First Campaign

1. **Navigate to Campaigns** → Click "Create Campaign"

2. **Basic Settings:**
   - Name: "Summer Sale Outreach"
   - Type: Progressive
   - Broadcast: Voice Only

3. **Configure Message:**
   - Voice Message: "Hello, this is a special offer..."
   - Caller ID: Select extension

4. **Import Contacts:**
   - Click "Import Contacts"
   - Upload CSV with columns: name, phone, email
   - Submit

5. **Start Campaign:**
   - Review settings
   - Click "Start Campaign"
   - Monitor progress in dashboard

### Sample CSV Format

```csv
name,phone,email
John Doe,1234567890,john@example.com
Jane Smith,0987654321,jane@example.com
```

## AI Features (Optional)

### Enable AI for Campaigns

1. Get API key from:
   - OpenAI: https://platform.openai.com
   - Anthropic: https://www.anthropic.com
   - Google AI: https://ai.google.dev

2. Add to `.env`:
```env
OPENAI_API_KEY=sk-your-key-here
```

3. Create campaign with AI enabled:
   - Enable AI toggle
   - Select provider (OpenAI/Anthropic/Google)
   - Choose model (GPT-4/Claude-3/Gemini)
   - Write prompt: "You are a sales assistant..."

## Troubleshooting Quick Fixes

### Permission Errors
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Database Connection Failed
```bash
# Check PostgreSQL is running
sudo systemctl status postgresql

# Test connection
psql -U fusionpbx -d fusionpbx -h 127.0.0.1
```

### 404 on Routes
```bash
# Clear caches
php artisan config:clear
php artisan route:clear

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

### Assets Not Loading
```bash
npm run build
php artisan config:clear
```

## Security Checklist

- [ ] Set `APP_DEBUG=false` in production
- [ ] Use HTTPS with SSL certificate
- [ ] Configure firewall (UFW/iptables)
- [ ] Regular database backups
- [ ] Keep dependencies updated
- [ ] Use strong passwords
- [ ] Limit SSH access

## Performance Tips

1. **Enable OPcache** (already configured in PHP 8.2)
2. **Use Redis** for caching (optional)
3. **Configure queue workers** for background jobs
4. **Enable Gzip** in Nginx (included in config)
5. **Optimize images** before uploading

## Next Steps

- [ ] Import your users and extensions
- [ ] Set up call center queues
- [ ] Create your first campaign
- [ ] Configure AI providers
- [ ] Review CDR reports
- [ ] Customize Tailwind theme

## Useful URLs

- Dashboard: `http://YOUR_IP:8080/`
- Users: `http://YOUR_IP:8080/users`
- Extensions: `http://YOUR_IP:8080/extensions`
- Campaigns: `http://YOUR_IP:8080/campaigns`
- CDR: `http://YOUR_IP:8080/cdr`

## Get Help

- **Logs**: `storage/logs/laravel.log`
- **Nginx Errors**: `/var/log/nginx/error.log`
- **PHP Errors**: `/var/log/php8.2-fpm.log`
- **Documentation**: [README.md](README.md)
- **Full Guide**: [INSTALLATION.md](INSTALLATION.md)

---

**🚀 You're ready to go!**

Access your new FusionPBX Laravel UI at: `http://YOUR_SERVER_IP:8080`
