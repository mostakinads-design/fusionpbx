# FusionPBX Laravel UI - INSTALLATION QUICK START

## 📦 What's Included

A complete modern Laravel 11 interface for FusionPBX with:

✅ **Core Features**: Domains, Users, Extensions, Call Records  
✅ **Auto Dialer**: Predictive/Progressive/Preview with AI support  
✅ **Call Center**: Queues, Agents, IVR Builder  
✅ **Billing**: Balance management, Top-up packages  
✅ **DID Management**: Voice/SMS/Both routing options  
✅ **Live CDR**: Real-time call monitoring  
✅ **Production Ready**: Nginx configs, security, optimization  

## 🚀 Quick Install

```bash
cd /home/runner/work/fusionpbx/fusionpbx/laravel-ui

# 1. Install dependencies
composer install --no-dev --optimize-autoloader

# 2. Configure
cp .env.example .env
php artisan key:generate
nano .env  # Edit database credentials

# 3. Database
php artisan migrate

# 4. Permissions
sudo chown -R www-data:www-data .
sudo chmod -R 775 storage bootstrap/cache

# 5. Nginx (choose one)
# Option A: Different port (8080/8443)
sudo cp nginx-laravel-ui.conf /etc/nginx/sites-available/laravel-ui
sudo ln -s /etc/nginx/sites-available/laravel-ui /etc/nginx/sites-enabled/

# Option B: Subdomain (admin.fusionpbx.com)
sudo cp nginx-subdomain.conf /etc/nginx/sites-available/laravel-ui
# Edit file with your domain
sudo ln -s /etc/nginx/sites-available/laravel-ui /etc/nginx/sites-enabled/

# 6. Test & reload
sudo nginx -t
sudo systemctl reload nginx

# 7. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔧 Database Configuration (.env)

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=fusionpbx
DB_USERNAME=fusionpbx
DB_PASSWORD=your_password
```

## 🌐 Access

- **Port-based**: http://your-server:8080
- **HTTPS**: https://your-server:8443
- **Subdomain**: https://admin.fusionpbx.com

## 📊 Features Overview

### 1. DID Management (NEW!)
- Create DIDs with phone numbers
- **Voice Only**: Handle voice calls
- **SMS Only**: Handle SMS messages
- **Voice + SMS**: Handle both
- Route to: Extension, IVR, Queue, External number
- Call recording option

### 2. Auto Dialer
- **Campaign Types**: Predictive, Progressive, Preview, Manual
- **AI Agents**: Enable AI-powered call handling
- **Contact Import**: CSV bulk import
- **Real-time Stats**: Live campaign monitoring

### 3. Billing System
- **User Balances**: Track credit per user
- **Top-up Packages**: Predefined packages with bonuses
- **Custom Top-ups**: Flexible amounts
- **Auto-recharge**: Automatic replenishment
- **Transaction History**: Full audit trail

### 4. Call Queues
- Multiple routing strategies
- Agent tiers (priority levels)
- Queue statistics
- Music on hold
- Abandoned call handling

### 5. IVR Builder
- Multi-level menus
- Custom prompts
- Timeout handling
- Direct dial option

## 🔒 Security Checklist

- [x] HTTPS configured
- [x] Security headers enabled
- [x] APP_DEBUG=false in production
- [x] Strong database passwords
- [x] File permissions set correctly
- [ ] Firewall configured (your task)
- [ ] SSL certificates installed (your task)
- [ ] Fail2ban enabled (recommended)

## 🎯 Post-Installation

### Create Sample Data

```bash
php artisan tinker
```

```php
// Create top-up package
\App\Models\TopUpPackage::create([
    'package_uuid' => (string) \Illuminate\Support\Str::uuid(),
    'domain_uuid' => 'your-domain-uuid-here',
    'package_name' => 'Starter Pack',
    'amount' => 10.00,
    'bonus_amount' => 2.00,
    'price' => 10.00,
    'currency' => 'USD',
    'is_active' => true,
]);
```

### Test Features

1. **Dashboard**: Check statistics display
2. **DIDs**: Create a test DID with voice routing
3. **Extensions**: Verify existing extensions appear
4. **Billing**: Create user balance and test top-up
5. **Campaigns**: Create test campaign with contacts

## 📝 Common Commands

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check logs
tail -f storage/logs/laravel.log
tail -f /var/log/nginx/laravel-ui-error.log

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
```

## 🐛 Troubleshooting

### 502 Bad Gateway
```bash
sudo systemctl status php8.2-fpm
sudo systemctl restart php8.2-fpm
```

### Permission Errors
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Database Connection Error
```bash
# Test connection
psql -h 127.0.0.1 -U fusionpbx -d fusionpbx

# Check PostgreSQL
sudo systemctl status postgresql
```

## 📚 File Structure

```
laravel-ui/
├── app/
│   ├── Http/Controllers/     # All controllers
│   └── Models/               # Database models
├── database/
│   └── migrations/           # Database schema
├── resources/
│   └── views/                # Blade templates
├── routes/
│   └── web.php               # Route definitions
├── public/                   # Web root
├── storage/                  # Logs, cache, sessions
├── nginx-laravel-ui.conf     # Nginx config (port-based)
├── nginx-subdomain.conf      # Nginx config (subdomain)
└── README.md                 # Full documentation
```

## 🎨 Customization

### Change Colors
Edit `resources/views/layout.blade.php`:
```html
<!-- Change from blue-600 to your color -->
<nav class="bg-purple-600 text-white">
```

### Add Logo
```html
<div class="flex-shrink-0">
    <img src="/logo.png" class="h-8 w-auto">
</div>
```

### Modify Navigation
Edit navigation items in `resources/views/layout.blade.php`

## 💡 Tips

1. **Always use HTTPS in production**
2. **Set APP_DEBUG=false for security**
3. **Backup database regularly**
4. **Monitor disk space for logs**
5. **Use queue workers for background tasks**
6. **Enable OPcache for performance**

## 📞 Support

- **Documentation**: See README.md for full guide
- **Logs**: Check `storage/logs/laravel.log`
- **Nginx Logs**: `/var/log/nginx/laravel-ui-*.log`

## 🎉 You're Ready!

Access your new interface at:
- http://your-server:8080 (or your configured domain)

Start by:
1. Creating DIDs for inbound routing
2. Setting up campaigns for outbound dialing
3. Configuring billing packages
4. Creating agents for call center

---

**Version**: 1.0.0  
**Laravel**: 11.x  
**PHP**: 8.2+  
**PostgreSQL**: 12+
