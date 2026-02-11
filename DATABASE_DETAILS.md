# Database Details - Quick Reference

## 📍 Database Location

**Full Documentation:** [`laravel-app/DATABASE_SCHEMA.md`](laravel-app/DATABASE_SCHEMA.md)

## 🔧 Quick Configuration

### Database Credentials

**File:** `laravel-app/.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fusionpbx_laravel
DB_USERNAME=root
DB_PASSWORD=
```

**Template:** See `laravel-app/.env.example`

## 📊 Database Tables

| # | Table Name | Purpose | Records |
|---|------------|---------|---------|
| 1 | `users` | User accounts with roles | 5 |
| 2 | `extensions` | Phone extensions | 8 |
| 3 | `call_logs` | Call history | 50 |
| 4 | `settings` | Configuration | 12 |
| 5 | `sessions` | User sessions | Dynamic |
| 6 | `cache` | Application cache | Dynamic |
| 7 | `jobs` | Background jobs | Dynamic |
| 8 | `personal_access_tokens` | API tokens | Dynamic |
| 9 | `password_reset_tokens` | Password resets | Dynamic |

## 🔑 Sample Login Credentials

### Admin Account
- **Email:** admin@fusionpbx.com
- **Password:** password123
- **Role:** admin

### Test Users
- **Emails:** user1@fusionpbx.com through user4@fusionpbx.com
- **Password:** password123
- **Role:** user

## 🚀 Quick Setup

```bash
# 1. Navigate to Laravel app
cd laravel-app

# 2. Copy environment file
cp .env.example .env

# 3. Edit .env with your MySQL credentials
nano .env

# 4. Create database
mysql -u root -p -e "CREATE DATABASE fusionpbx_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 5. Run migrations
php artisan migrate

# 6. Seed sample data
php artisan db:seed
```

## 📖 Documentation Files

| Document | Location | Description |
|----------|----------|-------------|
| **Database Schema** | `laravel-app/DATABASE_SCHEMA.md` | Complete schema documentation |
| **Installation Guide** | `laravel-app/INSTALLATION.md` | Setup instructions |
| **API Documentation** | `laravel-app/API_DOCUMENTATION.md` | API endpoints |
| **Laravel README** | `laravel-app/README_LARAVEL.md` | Complete Laravel guide |
| **Integration Guide** | `LARAVEL_INTEGRATION.md` | Integration overview |

## 🔍 Quick Database Queries

### Via MySQL CLI
```bash
mysql -u root -p fusionpbx_laravel
```

```sql
-- View all tables
SHOW TABLES;

-- View users
SELECT * FROM users;

-- View extensions
SELECT * FROM extensions;

-- View recent calls
SELECT * FROM call_logs ORDER BY call_date DESC LIMIT 10;
```

### Via Laravel Tinker
```bash
cd laravel-app
php artisan tinker
```

```php
// Get all users
User::all();

// Get user by ID
User::find(1);

// Get extensions with users
Extension::with('user')->get();

// Get recent call logs
CallLog::orderBy('call_date', 'desc')->take(10)->get();
```

## 📋 Table Relationships

```
Users (1) ──────→ (Many) Extensions
  ↓
  └──────────────→ (Many) API Tokens
  └──────────────→ (Many) Sessions
```

## 🔐 Security Notes

- ✅ Passwords hashed with bcrypt
- ✅ API authentication via Laravel Sanctum
- ✅ Soft deletes on users and extensions
- ✅ Foreign key constraints
- ✅ Indexed columns for performance
- ✅ SQL injection protection via Eloquent ORM

## 📞 Need Help?

1. **Database issues?** → See `laravel-app/DATABASE_SCHEMA.md`
2. **Setup issues?** → See `laravel-app/INSTALLATION.md`
3. **API questions?** → See `laravel-app/API_DOCUMENTATION.md`
4. **General questions?** → See `laravel-app/README_LARAVEL.md`

## 🎯 Next Steps

1. ✅ Review complete schema: `laravel-app/DATABASE_SCHEMA.md`
2. ✅ Set up database: Follow `laravel-app/INSTALLATION.md`
3. ✅ Access admin panel: http://localhost:8000/admin
4. ✅ Test API: http://localhost:8000/api

---

**Quick Answer to "WHERE IS DETAILS DB":**  
👉 **Complete database details are in: [`laravel-app/DATABASE_SCHEMA.md`](laravel-app/DATABASE_SCHEMA.md)**

---

*Last Updated: February 9, 2026*
