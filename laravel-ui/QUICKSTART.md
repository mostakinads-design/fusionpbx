# FusionPBX Laravel UI - Quick Start Guide

Get up and running in minutes with this quick installation guide for **separate installation** alongside FusionPBX.

## Important: Installation Paths

- **FusionPBX**: `/var/www/fusionpbx` (existing)
- **Laravel UI**: `/var/www/laravel-ui` (new, separate)
- **Database**: Shared FusionPBX PostgreSQL

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

### 2. Deploy to Separate Directory

```bash
# Navigate to web root
cd /var/www

# Clone repository and extract Laravel UI
git clone https://github.com/your-repo/fusionpbx.git fusionpbx-repo
sudo cp -r fusionpbx-repo/laravel-ui /var/www/laravel-ui
sudo rm -rf fusionpbx-repo

# Set ownership
sudo chown -R $USER:$USER /var/www/laravel-ui

# Navigate to Laravel UI directory
cd /var/www/laravel-ui
```

### 3. Auto-Configure Database (Recommended)

```bash
# This reads credentials from /etc/fusionpbx/config.conf
sudo bash setup-from-fusionpbx.sh
```

### 4. Install Dependencies and Build

```bash
# Install dependencies
composer install --no-dev --optimize-autoloader
npm install

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
sudo cp nginx-laravel-port.conf /etc/nginx/sites-available/laravel-ui

# Verify the root path is correct (already set to /var/www/laravel-ui/public)
sudo nano /etc/nginx/sites-available/laravel-ui
# Update server_name to your IP/domain

# Enable site
sudo ln -s /etc/nginx/sites-available/laravel-ui /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 6. Access Application

Open browser: `http://YOUR_SERVER_IP:8080`

## Manual Database Configuration (Alternative)

If you didn't use the auto-config script:

```bash
# Create .env from example
cp .env.example .env
php artisan key:generate

# Get credentials from FusionPBX config
sudo cat /etc/fusionpbx/config.conf | grep "^database.0"

# Edit .env and paste the credentials
nano .env
```

## Verification

```bash
# Verify both directories exist
ls -la /var/www/ | grep -E "(fusionpbx|laravel-ui)"

# Test database connection
cd /var/www/laravel-ui
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

## Directory Structure

After installation you should have:

```
/var/www/
├── fusionpbx/          # FusionPBX (existing)
│   ├── app/
│   ├── core/
│   └── ...
└── laravel-ui/         # Laravel UI (new)
    ├── app/
    ├── public/
    ├── resources/
    └── ...
```

## Get Help

- **Logs**: `storage/logs/laravel.log`
- **Nginx Errors**: `/var/log/nginx/error.log`
- **PHP Errors**: `/var/log/php8.2-fpm.log`
- **Documentation**: [README.md](README.md)
- **Full Guide**: [INSTALLATION.md](INSTALLATION.md)

---

**🚀 You're ready to go!**

Access your new FusionPBX Laravel UI at: `http://YOUR_SERVER_IP:8080`
