# FusionPBX - Laravel 12 + Filament 4 Integration

This repository now includes a complete **Laravel 12 application with Filament 4 admin panel** and full **MySQL database integration** for modern web-based PBX management.

## 🚀 What's New

We've added a complete Laravel application alongside the existing FusionPBX PHP application, providing:

- **Modern Admin Panel** - Beautiful Filament 4 interface for managing users, extensions, call logs, and settings
- **RESTful API** - 27 endpoints for programmatic access with Laravel Sanctum authentication
- **MySQL Integration** - Full MySQL 8.0+ support with optimized schema
- **Role-Based Access** - Admin and user roles with proper permissions
- **Sample Data** - Pre-configured sample data for quick testing

## 📁 Repository Structure

```
fusionpbx/
├── app/                          # Original FusionPBX application
├── core/                         # Original FusionPBX core
├── resources/                    # Original FusionPBX resources
├── themes/                       # Original FusionPBX themes
├── laravel-app/                  # ⭐ NEW: Laravel 12 + Filament 4 Application
│   ├── app/
│   │   ├── Filament/             # Admin panel resources
│   │   ├── Http/Controllers/Api/ # REST API controllers
│   │   └── Models/               # Eloquent models
│   ├── database/
│   │   ├── migrations/           # Database schema
│   │   └── seeders/              # Sample data
│   ├── routes/
│   │   ├── api.php              # API routes
│   │   └── web.php              # Web routes
│   ├── README_LARAVEL.md        # Laravel documentation
│   ├── API_DOCUMENTATION.md      # Complete API docs
│   └── INSTALLATION.md          # Quick setup guide
├── index.php                     # Original FusionPBX entry point
├── login.php                     # Original FusionPBX login
└── readme.md                     # Original FusionPBX README
```

## 🎯 Quick Start

### Prerequisites

- PHP 8.3+
- Composer 2.0+
- MySQL 8.0+
- Node.js 18+ (optional)

### Installation (5 minutes)

```bash
# Navigate to Laravel application
cd laravel-app

# Install dependencies
composer install

# Configure environment
cp .env.example .env
# Edit .env with your MySQL credentials

# Setup database
php artisan migrate
php artisan db:seed

# Start server
php artisan serve
```

**Access the admin panel:**
- URL: http://localhost:8000/admin
- Email: admin@fusionpbx.com
- Password: password123

📖 **Full instructions:** See `laravel-app/INSTALLATION.md`

## ✨ Features

### Filament 4 Admin Panel

Modern, responsive admin interface with:

- **User Management** - Create, edit, delete users with role assignment
- **Extension Management** - Manage phone extensions with user assignment
- **Call Log Viewer** - View and filter call history with advanced search
- **Settings Manager** - Configure system settings by group
- **Beautiful UI** - Color-coded badges, filters, search, bulk actions

**Access:** `/admin`

### REST API

Complete RESTful API with Laravel Sanctum authentication:

- **27 Endpoints** - Full CRUD for all resources
- **Token Authentication** - Secure API access
- **Pagination** - Efficient data handling
- **Filtering** - Advanced query capabilities
- **Rate Limiting** - 60 requests/minute

**Base URL:** `/api`  
**Documentation:** See `laravel-app/API_DOCUMENTATION.md`

### Database

Optimized MySQL schema with:

- **Users** - Role-based with soft deletes
- **Extensions** - Phone extensions with user relationships
- **Call Logs** - Comprehensive call tracking
- **Settings** - Flexible key-value configuration

All tables include proper indexes for performance.

## 📖 Documentation

| Document | Description | Location |
|----------|-------------|----------|
| **Laravel README** | Complete setup and configuration guide | `laravel-app/README_LARAVEL.md` |
| **API Documentation** | All 27 API endpoints with examples | `laravel-app/API_DOCUMENTATION.md` |
| **Installation Guide** | Quick 10-minute setup | `laravel-app/INSTALLATION.md` |
| **Original FusionPBX README** | Original application docs | `readme.md` |

## 🔌 API Quick Example

```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@fusionpbx.com","password":"password123"}'

# Response includes token
{
  "success": true,
  "data": {
    "token": "1|abc123...",
    "user": {...}
  }
}

# Use token for authenticated requests
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Accept: application/json"
```

## 🎨 Screenshots

### Admin Dashboard (Filament)
- Clean, modern interface
- Easy navigation
- Responsive design

### Users Management
- List view with search and filters
- Create/Edit forms with validation
- Role-based badges
- Bulk actions

### Extensions Management
- Extension assignment to users
- Status management (active/inactive/suspended)
- Soft delete support

### Call Logs Viewer
- Real-time call log display
- Filter by status, type, date range
- Duration formatting
- Search by caller or destination

### Settings Manager
- Grouped settings (System, Call, Notification, Security)
- Type-specific badges (String, Integer, Boolean, JSON)
- Easy edit interface

## 🔐 Security Features

- **Laravel Sanctum** - Token-based API authentication
- **Password Hashing** - Bcrypt encryption
- **Role-Based Access** - Admin and user roles
- **Input Validation** - All endpoints validated
- **SQL Injection Protection** - Eloquent ORM
- **CSRF Protection** - Laravel middleware
- **Soft Deletes** - Safe data handling
- **Rate Limiting** - API abuse prevention

## 🛠️ Technology Stack

| Component | Technology | Version |
|-----------|-----------|---------|
| Framework | Laravel | 12.50.0 |
| Admin Panel | Filament | 4.7.0 |
| API Auth | Laravel Sanctum | 4.3.0 |
| Database | MySQL | 8.0+ |
| PHP | PHP | 8.3.6 |
| Package Manager | Composer | 2.9.5 |

## 📊 Sample Data

When you run `php artisan db:seed`, you get:

- **5 Users** - 1 admin + 4 test users
- **8 Extensions** - Extensions 1001-1008
- **50 Call Logs** - Sample calls spanning 30 days
- **12 Settings** - Default system configuration

**All test accounts use password:** `password123`

## 🚦 Status

| Component | Status | Version |
|-----------|--------|---------|
| Laravel Application | ✅ Complete | 12.50.0 |
| Filament Admin Panel | ✅ Complete | 4.7.0 |
| REST API | ✅ Complete | v1.0.0 |
| Database Schema | ✅ Complete | MySQL 8.0+ |
| Documentation | ✅ Complete | 28,000+ chars |
| Sample Data | ✅ Complete | 75+ records |

## 🔗 Useful Links

- **Laravel Documentation:** https://laravel.com/docs/12.x
- **Filament Documentation:** https://filamentphp.com/docs
- **Laravel Sanctum:** https://laravel.com/docs/12.x/sanctum
- **Original FusionPBX:** https://www.fusionpbx.com

## 🤝 Integration with Original FusionPBX

The Laravel application runs **alongside** the original FusionPBX application:

- **Original FusionPBX** - Continues to work at root URL
- **Laravel App** - Runs at `/laravel-app` or on separate port
- **Shared Data** - Can access same MySQL database if configured
- **Independent** - Can be deployed separately or together

## 🧪 Testing

```bash
cd laravel-app

# Run all tests
php artisan test

# Test API endpoint
curl http://localhost:8000/api/user

# Check routes
php artisan route:list
```

## 📞 Support

For Laravel application issues:
- Check `laravel-app/INSTALLATION.md` for troubleshooting
- Review logs in `laravel-app/storage/logs/laravel.log`
- See API docs in `laravel-app/API_DOCUMENTATION.md`

For original FusionPBX issues:
- See the main `readme.md`
- Visit https://www.fusionpbx.com

## 🎓 Learning Resources

New to Laravel or Filament? Check out:

1. **Laravel Documentation** - Comprehensive guide to Laravel features
2. **Filament Documentation** - Learn about admin panel capabilities
3. **API Documentation** - Understand available endpoints
4. **Installation Guide** - Step-by-step setup instructions

## ⚠️ Production Deployment

Before deploying to production:

1. ✅ Change all default passwords
2. ✅ Set `APP_ENV=production` in `.env`
3. ✅ Set `APP_DEBUG=false`
4. ✅ Enable HTTPS
5. ✅ Run `composer install --no-dev`
6. ✅ Run `php artisan config:cache`
7. ✅ Run `php artisan route:cache`
8. ✅ Set up regular backups
9. ✅ Configure proper file permissions
10. ✅ Set up monitoring and logging

## 📈 Performance

The application is optimized with:

- **Database Indexing** - All foreign keys and searchable fields indexed
- **Query Optimization** - Eloquent relationships properly loaded
- **Caching** - Configuration and route caching available
- **Pagination** - All list endpoints paginated
- **API Rate Limiting** - Prevents abuse

## 🎉 What You Get

With this integration, you get a complete, modern web application with:

- ✅ Beautiful admin interface (Filament 4)
- ✅ Complete REST API (27 endpoints)
- ✅ Full MySQL integration
- ✅ User and role management
- ✅ Extension management
- ✅ Call log tracking
- ✅ Flexible settings system
- ✅ Token-based authentication
- ✅ Comprehensive documentation
- ✅ Sample data for testing
- ✅ Production-ready code

## 🚀 Getting Started

1. **Read** `laravel-app/INSTALLATION.md` (10 minutes)
2. **Install** following the quick start guide
3. **Explore** the admin panel at `/admin`
4. **Test** the API endpoints
5. **Customize** for your needs
6. **Deploy** to production

## 📝 License

This Laravel integration is part of the FusionPBX project and follows the same licensing terms as the main FusionPBX application.

---

**Version:** 1.0.0  
**Last Updated:** February 2026  
**Laravel Version:** 12.50.0  
**Filament Version:** 4.7.0  

**Enjoy your new Laravel-powered FusionPBX! 🎉**
