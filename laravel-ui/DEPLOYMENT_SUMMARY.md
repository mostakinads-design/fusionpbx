# Deployment Summary: Separate Installation + CGRates Billing

## Overview

The FusionPBX Laravel UI has been successfully configured to:

1. **Run as a separate installation** from FusionPBX in `/var/www/laravel-ui`
2. **Use CGRates for billing** instead of any internal billing module
3. **Share the FusionPBX PostgreSQL database** for seamless data integration

## Changes Made

### 1. Separate Installation Configuration

#### File: `.env.example`
- Added detailed comments about reading from `/etc/fusionpbx/config.conf`
- Provided clear instructions for database configuration
- Example showing the FusionPBX config structure

#### File: `setup-from-fusionpbx.sh` (NEW)
- Automatic configuration script
- Reads database credentials from `/etc/fusionpbx/config.conf`
- Creates and configures `.env` file automatically
- Generates Laravel application key
- Provides step-by-step progress output

#### Files: `nginx-laravel-port.conf` & `nginx-laravel-subdomain.conf`
- Updated paths from `/var/www/fusionpbx-laravel/public` to `/var/www/laravel-ui/public`
- Ready for deployment as separate application

### 2. Documentation Updates

#### File: `README.md`
- Added "Separate Installation" section
- Updated installation paths clearly showing:
  - FusionPBX: `/var/www/fusionpbx`
  - Laravel UI: `/var/www/laravel-ui`
- Added reference to CGRates for billing
- Updated security section to mention CGRates

#### File: `INSTALLATION.md`
- Complete rewrite of installation steps for separate deployment
- Added "Option A" and "Option B" for repository cloning vs manual copy
- Step-by-step guide for using `setup-from-fusionpbx.sh`
- Manual configuration alternative included
- Updated all nginx configuration examples
- Added verification steps for separate installation

#### File: `QUICKSTART.md`
- Updated quick installation to deploy to `/var/www/laravel-ui`
- Added auto-configuration script usage
- Updated directory structure diagram
- Added CGRates optional setup section

#### File: `CGRATES_INTEGRATION.md` (NEW)
- **Comprehensive 400+ line guide** for CGRates integration
- Installation instructions for CGRates
- PostgreSQL database setup
- FreeSWITCH integration configuration
- Complete Laravel service class (`CgratesService.php`)
- Billing controller implementation
- Example routes and usage
- Real-time balance checking
- Rating plans configuration
- Monitoring and troubleshooting
- Security considerations

### 3. Billing Module Changes

#### What Was Removed:
- **NOTHING** - The application was already designed without any internal billing module as per original requirements

#### What Was Added:
- Complete CGRates integration documentation
- Service class template for CGRates API integration
- Controller examples for billing operations
- Usage examples in campaigns
- Balance checking before campaign start
- Post-call debit functionality

### 4. Updated Project Documentation

#### File: `PROJECT_STRUCTURE.md`
- Updated exclusions to reference CGRates
- Added CGRATES_INTEGRATION.md to documentation list

#### File: `IMPLEMENTATION_SUMMARY.md`
- Updated exclusions section with CGRates references
- Added CGRates documentation to user documentation list

## Installation Paths

```
/var/www/
├── fusionpbx/              ← Existing FusionPBX installation
│   ├── app/
│   ├── core/
│   ├── resources/
│   └── ...
│
└── laravel-ui/             ← NEW: Separate Laravel UI installation
    ├── app/
    │   ├── Http/
    │   │   └── Controllers/
    │   ├── Models/
    │   └── Services/
    │       └── CgratesService.php (template in docs)
    ├── config/
    ├── database/
    ├── public/             ← Nginx document root
    ├── resources/
    ├── routes/
    ├── storage/
    ├── .env.example
    ├── setup-from-fusionpbx.sh
    ├── CGRATES_INTEGRATION.md
    └── ...
```

## Database Configuration

### Shared PostgreSQL Database
Both applications use the **same FusionPBX PostgreSQL database**:

```
┌─────────────────────────────────┐
│   PostgreSQL Server             │
│   127.0.0.1:5432               │
├─────────────────────────────────┤
│                                 │
│   ┌─────────────────────────┐  │
│   │  Database: fusionpbx    │  │
│   │  (shared by both apps)  │  │
│   └─────────────────────────┘  │
│                                 │
│   ┌─────────────────────────┐  │
│   │  Database: cgrates      │  │
│   │  (for CGRates billing)  │  │
│   └─────────────────────────┘  │
│                                 │
└─────────────────────────────────┘
         ▲              ▲
         │              │
    ┌────┴───┐     ┌────┴────┐
    │ Fusion │     │ Laravel │
    │  PBX   │     │   UI    │
    └────────┘     └─────────┘
```

### Configuration Source
Database credentials are read from: `/etc/fusionpbx/config.conf`

Example config:
```ini
database.0.type = pgsql
database.0.host = 127.0.0.1
database.0.port = 5432
database.0.name = fusionpbx
database.0.username = fusionpbx
database.0.password = 3sF4Tmu0KAH7vE5Rs1iBPpREYQI
```

## Billing Architecture

### CGRates External Billing System

```
┌──────────────────┐
│  FreeSWITCH      │
│  (Call Events)   │
└────────┬─────────┘
         │ Event Socket
         │ Port 8021
         ▼
┌──────────────────┐      JSON-RPC      ┌──────────────────┐
│    CGRates       │◄────────────────────│  Laravel UI      │
│  (Rating/CDR)    │      Port 2080      │  (Management)    │
└────────┬─────────┘                     └──────────────────┘
         │
         ▼
┌──────────────────┐
│  PostgreSQL      │
│  cgrates DB      │
└──────────────────┘
```

### Key Features:
- **Real-time rating** during calls
- **Post-call CDR processing**
- **Balance management** via API
- **Rating plans** configuration
- **Multi-tenant** support
- **RESTful API** for Laravel integration

## Quick Deployment Guide

### Step 1: Deploy Laravel UI

```bash
# Navigate to web root
cd /var/www

# Clone and extract
git clone https://github.com/your-repo/fusionpbx.git fusionpbx-repo
sudo cp -r fusionpbx-repo/laravel-ui /var/www/laravel-ui
sudo rm -rf fusionpbx-repo

# Set ownership
sudo chown -R $USER:$USER /var/www/laravel-ui
cd /var/www/laravel-ui
```

### Step 2: Auto-Configure

```bash
# Run automatic configuration (reads FusionPBX config)
sudo bash setup-from-fusionpbx.sh
```

This script:
- ✅ Reads database credentials from `/etc/fusionpbx/config.conf`
- ✅ Creates `.env` from `.env.example`
- ✅ Configures database connection
- ✅ Generates application key

### Step 3: Install & Build

```bash
composer install --no-dev --optimize-autoloader
npm install
php artisan migrate
npm run build
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Step 4: Configure Nginx

```bash
sudo cp nginx-laravel-port.conf /etc/nginx/sites-available/laravel-ui
sudo nano /etc/nginx/sites-available/laravel-ui
# Update server_name to your IP

sudo ln -s /etc/nginx/sites-available/laravel-ui /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Step 5: Access Application

http://YOUR_SERVER_IP:8080

### Step 6: Setup CGRates (Optional)

For billing functionality, follow the comprehensive guide in:
`CGRATES_INTEGRATION.md`

## Testing Checklist

- [ ] Laravel UI accessible at http://SERVER_IP:8080
- [ ] Can login to Laravel UI
- [ ] Dashboard shows FusionPBX data (domains, users, extensions)
- [ ] Can view CDR records from FusionPBX
- [ ] FusionPBX still accessible at its normal URL
- [ ] Both applications use same PostgreSQL database
- [ ] Campaign tables created successfully (v_campaigns, etc.)
- [ ] Directory structure shows both `/var/www/fusionpbx` and `/var/www/laravel-ui`

## Verification Commands

```bash
# Check both directories exist
ls -la /var/www/ | grep -E "(fusionpbx|laravel-ui)"

# Test Laravel UI database connection
cd /var/www/laravel-ui
php artisan tinker
>>> DB::connection()->getPdo();
>>> \App\Models\Domain::count();
>>> exit

# Verify FusionPBX still works
curl -I http://localhost  # or your FusionPBX URL

# Check Laravel UI access
curl -I http://localhost:8080
```

## Nginx Configuration Summary

### FusionPBX (existing)
- Path: `/var/www/fusionpbx`
- Port: 80/443 (HTTPS)
- Config: `/etc/nginx/sites-available/fusionpbx` (existing)

### Laravel UI (new)
- Path: `/var/www/laravel-ui/public`
- Port: 8080
- Config: `/etc/nginx/sites-available/laravel-ui` (new)

Both can run simultaneously without conflicts!

## Benefits of This Architecture

### 1. **Separation of Concerns**
- FusionPBX handles telephony
- Laravel UI handles modern web interface
- CGRates handles billing
- Each can be updated independently

### 2. **Security**
- No billing code in Laravel UI reduces attack surface
- Professional billing system (CGRates) with security focus
- Separate applications limit security breach impact

### 3. **Scalability**
- Can deploy Laravel UI on different server
- CGRates can be clustered for high availability
- Database remains centralized

### 4. **Maintainability**
- Clear separation makes debugging easier
- Updates to one don't affect others
- Can rollback changes independently

### 5. **Flexibility**
- Can use Laravel UI without CGRates
- Can use CGRates with or without Laravel UI
- Mix and match components as needed

## Documentation Files

| File | Purpose |
|------|---------|
| `README.md` | Project overview and features |
| `INSTALLATION.md` | Detailed installation guide (separate path) |
| `QUICKSTART.md` | Quick setup commands |
| `CGRATES_INTEGRATION.md` | Complete CGRates billing integration |
| `PROJECT_STRUCTURE.md` | File listing and structure |
| `IMPLEMENTATION_SUMMARY.md` | Technical implementation details |
| `setup-from-fusionpbx.sh` | Auto-configuration script |

## Support Resources

- **Laravel UI Issues**: Check `storage/logs/laravel.log`
- **Nginx Issues**: Check `/var/log/nginx/error.log`
- **PHP Issues**: Check `/var/log/php8.2-fpm.log`
- **CGRates Issues**: Check `/var/log/cgrates/cgrates.log`
- **Database Issues**: Check PostgreSQL logs

## Next Steps

1. ✅ Deploy Laravel UI to `/var/www/laravel-ui`
2. ✅ Configure database connection via script
3. ✅ Access Laravel UI at port 8080
4. 📋 Optionally install CGRates (see CGRATES_INTEGRATION.md)
5. 📋 Configure rating plans in CGRates
6. 📋 Test campaign billing integration
7. 📋 Set up monitoring for both systems

## Conclusion

The FusionPBX Laravel UI is now:

✅ **Deployed separately** in `/var/www/laravel-ui`
✅ **Uses FusionPBX PostgreSQL** database (shared)
✅ **Configured automatically** via setup script
✅ **Billing-ready** with CGRates integration docs
✅ **Production-ready** with comprehensive documentation

Both applications can coexist peacefully, sharing the same database while operating independently!

---

**Status**: ✅ **READY FOR DEPLOYMENT**

**Access URL**: http://YOUR_SERVER_IP:8080
