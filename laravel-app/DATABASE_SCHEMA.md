# Database Schema Documentation

## Overview

This document provides comprehensive details about the MySQL database schema for the FusionPBX Laravel application.

## Table of Contents
1. [Database Configuration](#database-configuration)
2. [Tables Overview](#tables-overview)
3. [Table Structures](#table-structures)
4. [Relationships](#relationships)
5. [Indexes](#indexes)
6. [Sample Data](#sample-data)
7. [SQL Schema Export](#sql-schema-export)
8. [Common Queries](#common-queries)

---

## Database Configuration

### Connection Details

The application uses MySQL 8.0+ with the following default configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fusionpbx_laravel
DB_USERNAME=root
DB_PASSWORD=
```

**Location:** These settings are configured in the `.env` file (see `.env.example` for template)

### Character Set
- **Charset:** `utf8mb4`
- **Collation:** `utf8mb4_unicode_ci`

---

## Tables Overview

The database consists of **7 main tables**:

| Table Name | Purpose | Records (Seeded) | Soft Delete |
|------------|---------|------------------|-------------|
| `users` | User accounts with roles | 5 | ✅ Yes |
| `extensions` | Phone extensions | 8 | ✅ Yes |
| `call_logs` | Call history records | 50 | ❌ No |
| `settings` | System configuration | 12 | ❌ No |
| `sessions` | User sessions | Dynamic | ❌ No |
| `cache` | Application cache | Dynamic | ❌ No |
| `jobs` | Background jobs queue | Dynamic | ❌ No |
| `personal_access_tokens` | API tokens (Sanctum) | Dynamic | ❌ No |
| `password_reset_tokens` | Password resets | Dynamic | ❌ No |

---

## Table Structures

### 1. Users Table

**Table Name:** `users`

**Purpose:** Stores user accounts with role-based access control

**Columns:**

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `name` | VARCHAR(255) | No | - | User's full name |
| `email` | VARCHAR(255) | No | - | Unique email address |
| `email_verified_at` | TIMESTAMP | Yes | NULL | Email verification timestamp |
| `password` | VARCHAR(255) | No | - | Hashed password (bcrypt) |
| `role` | VARCHAR(255) | No | 'user' | User role (admin/user) |
| `remember_token` | VARCHAR(100) | Yes | NULL | Remember me token |
| `created_at` | TIMESTAMP | Yes | NULL | Record creation time |
| `updated_at` | TIMESTAMP | Yes | NULL | Last update time |
| `deleted_at` | TIMESTAMP | Yes | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `email`
- INDEX: `role`
- INDEX: `deleted_at`

**Sample Data:**
```sql
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@fusionpbx.com', '$2y$12$...', 'admin'),
('John Smith', 'user1@fusionpbx.com', '$2y$12$...', 'user');
```

---

### 2. Extensions Table

**Table Name:** `extensions`

**Purpose:** Manages phone extensions with user assignments

**Columns:**

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `extension_number` | VARCHAR(255) | No | - | Unique extension number |
| `user_id` | BIGINT UNSIGNED | Yes | NULL | Foreign key to users.id |
| `status` | ENUM | No | 'active' | Extension status |
| `description` | VARCHAR(255) | Yes | NULL | Extension description |
| `created_at` | TIMESTAMP | Yes | NULL | Record creation time |
| `updated_at` | TIMESTAMP | Yes | NULL | Last update time |
| `deleted_at` | TIMESTAMP | Yes | NULL | Soft delete timestamp |

**Status Values:**
- `active` - Extension is active and usable
- `inactive` - Extension is disabled
- `suspended` - Extension is temporarily suspended

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `extension_number`
- INDEX: `user_id` (Foreign Key)
- INDEX: `status`
- INDEX: `deleted_at`

**Foreign Keys:**
```sql
ALTER TABLE extensions
ADD CONSTRAINT extensions_user_id_foreign
FOREIGN KEY (user_id) REFERENCES users(id)
ON DELETE CASCADE;
```

**Sample Data:**
```sql
INSERT INTO extensions (extension_number, user_id, status, description) VALUES
('1001', 2, 'active', 'Sales Department Extension'),
('1002', 3, 'active', 'Support Department Extension');
```

---

### 3. Call Logs Table

**Table Name:** `call_logs`

**Purpose:** Tracks all call history and details

**Columns:**

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `caller_id` | VARCHAR(255) | No | - | Caller identification |
| `destination` | VARCHAR(255) | No | - | Call destination |
| `duration` | INT | No | 0 | Call duration in seconds |
| `status` | ENUM | No | 'answered' | Call status |
| `call_type` | VARCHAR(255) | Yes | NULL | Type of call |
| `call_date` | TIMESTAMP | No | CURRENT_TIMESTAMP | Call timestamp |
| `created_at` | TIMESTAMP | Yes | NULL | Record creation time |
| `updated_at` | TIMESTAMP | Yes | NULL | Last update time |

**Status Values:**
- `answered` - Call was answered
- `missed` - Call was not answered
- `busy` - Destination was busy
- `failed` - Call failed to connect

**Call Type Values:**
- `inbound` - Incoming call
- `outbound` - Outgoing call
- `internal` - Internal extension call

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `caller_id`
- INDEX: `destination`
- INDEX: `status`
- INDEX: `call_date`

**Sample Data:**
```sql
INSERT INTO call_logs (caller_id, destination, duration, status, call_type, call_date) VALUES
('+15551234567', '1001', 125, 'answered', 'inbound', '2026-02-09 10:30:00'),
('1002', '1003', 45, 'answered', 'internal', '2026-02-09 11:15:00');
```

---

### 4. Settings Table

**Table Name:** `settings`

**Purpose:** Stores application configuration in key-value format

**Columns:**

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `key` | VARCHAR(255) | No | - | Unique setting key |
| `value` | TEXT | Yes | NULL | Setting value |
| `type` | VARCHAR(255) | No | 'string' | Value data type |
| `group` | VARCHAR(255) | Yes | NULL | Setting group/category |
| `description` | TEXT | Yes | NULL | Setting description |
| `created_at` | TIMESTAMP | Yes | NULL | Record creation time |
| `updated_at` | TIMESTAMP | Yes | NULL | Last update time |

**Type Values:**
- `string` - Text value
- `integer` - Numeric value
- `boolean` - True/False value
- `json` - JSON encoded data

**Group Values:**
- `system` - System-level settings
- `call` - Call-related settings
- `notification` - Notification settings
- `security` - Security settings

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `key`
- INDEX: `group`

**Sample Data:**
```sql
INSERT INTO settings (`key`, value, type, `group`, description) VALUES
('app_name', 'FusionPBX', 'string', 'system', 'Application name'),
('max_call_duration', '3600', 'integer', 'call', 'Maximum call duration in seconds'),
('allow_international', 'true', 'boolean', 'call', 'Allow international calls');
```

---

### 5. Sessions Table

**Table Name:** `sessions`

**Purpose:** Stores user session data

**Columns:**

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | VARCHAR(255) | No | - | Primary key (session ID) |
| `user_id` | BIGINT UNSIGNED | Yes | NULL | Foreign key to users.id |
| `ip_address` | VARCHAR(45) | Yes | NULL | User's IP address |
| `user_agent` | TEXT | Yes | NULL | Browser user agent |
| `payload` | LONGTEXT | No | - | Session data payload |
| `last_activity` | INT | No | - | Last activity timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `user_id`
- INDEX: `last_activity`

---

### 6. Personal Access Tokens Table

**Table Name:** `personal_access_tokens`

**Purpose:** Stores Laravel Sanctum API tokens

**Columns:**

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `tokenable_type` | VARCHAR(255) | No | - | Model type (polymorphic) |
| `tokenable_id` | BIGINT UNSIGNED | No | - | Model ID (polymorphic) |
| `name` | VARCHAR(255) | No | - | Token name |
| `token` | VARCHAR(64) | No | - | Hashed token value |
| `abilities` | TEXT | Yes | NULL | Token abilities/permissions |
| `last_used_at` | TIMESTAMP | Yes | NULL | Last usage timestamp |
| `expires_at` | TIMESTAMP | Yes | NULL | Token expiration time |
| `created_at` | TIMESTAMP | Yes | NULL | Record creation time |
| `updated_at` | TIMESTAMP | Yes | NULL | Last update time |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE KEY: `token`
- INDEX: `tokenable_type`, `tokenable_id`

---

### 7. Cache Table

**Table Name:** `cache`

**Purpose:** Application cache storage

**Columns:**

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `key` | VARCHAR(255) | No | - | Primary key (cache key) |
| `value` | MEDIUMTEXT | No | - | Cached value |
| `expiration` | INT | No | - | Expiration timestamp |

**Indexes:**
- PRIMARY KEY: `key`
- INDEX: `expiration`

---

### 8. Jobs Table

**Table Name:** `jobs`

**Purpose:** Queue for background jobs

**Columns:**

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `queue` | VARCHAR(255) | No | - | Queue name |
| `payload` | LONGTEXT | No | - | Job data |
| `attempts` | TINYINT UNSIGNED | No | - | Number of attempts |
| `reserved_at` | INT UNSIGNED | Yes | NULL | Reserved timestamp |
| `available_at` | INT UNSIGNED | No | - | Available timestamp |
| `created_at` | INT UNSIGNED | No | - | Creation timestamp |

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `queue`

---

### 9. Password Reset Tokens Table

**Table Name:** `password_reset_tokens`

**Purpose:** Temporary tokens for password resets

**Columns:**

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `email` | VARCHAR(255) | No | - | Primary key (user email) |
| `token` | VARCHAR(255) | No | - | Reset token |
| `created_at` | TIMESTAMP | Yes | NULL | Token creation time |

**Indexes:**
- PRIMARY KEY: `email`

---

## Relationships

### Entity Relationship Diagram (ERD)

```
┌─────────────┐         ┌──────────────┐
│   Users     │◄────────│  Extensions  │
│             │ 1     * │              │
│ • id        │         │ • id         │
│ • name      │         │ • user_id    │
│ • email     │         │ • ext_number │
│ • role      │         │ • status     │
└─────────────┘         └──────────────┘

┌─────────────┐
│  Call Logs  │
│             │
│ • id        │
│ • caller_id │
│ • dest      │
│ • duration  │
│ • status    │
└─────────────┘

┌─────────────┐
│  Settings   │
│             │
│ • id        │
│ • key       │
│ • value     │
│ • type      │
│ • group     │
└─────────────┘

┌─────────────┐         ┌──────────────────────────┐
│   Users     │◄────────│ Personal Access Tokens   │
│             │ 1     * │                          │
│ • id        │         │ • tokenable_id           │
└─────────────┘         └──────────────────────────┘

┌─────────────┐         ┌──────────────┐
│   Users     │◄────────│   Sessions   │
│             │ 1     * │              │
│ • id        │         │ • user_id    │
└─────────────┘         └──────────────┘
```

### Relationship Details

#### 1. Users → Extensions (One-to-Many)
- **Type:** One-to-Many
- **Description:** One user can have multiple extensions
- **Foreign Key:** `extensions.user_id` → `users.id`
- **On Delete:** CASCADE (deleting user removes their extensions)

#### 2. Users → Personal Access Tokens (One-to-Many, Polymorphic)
- **Type:** Polymorphic One-to-Many
- **Description:** One user can have multiple API tokens
- **Foreign Key:** `personal_access_tokens.tokenable_id` → `users.id`
- **Polymorphic Type:** `personal_access_tokens.tokenable_type` = 'App\Models\User'

#### 3. Users → Sessions (One-to-Many)
- **Type:** One-to-Many (Optional)
- **Description:** One user can have multiple active sessions
- **Foreign Key:** `sessions.user_id` → `users.id`

---

## Indexes

### Performance Indexes

All tables are optimized with the following indexes:

**Users Table:**
```sql
PRIMARY KEY (id)
UNIQUE KEY users_email_unique (email)
KEY users_role_index (role)
KEY users_deleted_at_index (deleted_at)
```

**Extensions Table:**
```sql
PRIMARY KEY (id)
UNIQUE KEY extensions_extension_number_unique (extension_number)
KEY extensions_user_id_foreign (user_id)
KEY extensions_status_index (status)
KEY extensions_deleted_at_index (deleted_at)
```

**Call Logs Table:**
```sql
PRIMARY KEY (id)
KEY call_logs_caller_id_index (caller_id)
KEY call_logs_destination_index (destination)
KEY call_logs_status_index (status)
KEY call_logs_call_date_index (call_date)
```

**Settings Table:**
```sql
PRIMARY KEY (id)
UNIQUE KEY settings_key_unique (key)
KEY settings_group_index (group)
```

---

## Sample Data

The database seeders create the following sample data:

### Users (5 records)
```
1. Admin User (admin@fusionpbx.com) - Role: admin
2. John Smith (user1@fusionpbx.com) - Role: user
3. Jane Doe (user2@fusionpbx.com) - Role: user
4. Bob Johnson (user3@fusionpbx.com) - Role: user
5. Alice Williams (user4@fusionpbx.com) - Role: user

Default Password: password123
```

### Extensions (8 records)
```
1001 - Sales Department (User: John Smith)
1002 - Support Department (User: Jane Doe)
1003 - Marketing Department (User: Bob Johnson)
1004 - IT Department (User: Alice Williams)
1005 - HR Department (User: John Smith)
1006 - Finance Department (User: Jane Doe)
1007 - Operations (User: Bob Johnson)
1008 - Customer Service (User: Alice Williams)
```

### Call Logs (50 records)
- 50 sample calls spanning the last 30 days
- Mix of inbound, outbound, and internal calls
- Various statuses: answered, missed, busy, failed
- Durations ranging from 0 to 600 seconds

### Settings (12 records)
```
System Group:
- app_name: FusionPBX
- app_version: 1.0.0
- timezone: UTC

Call Group:
- max_call_duration: 3600
- allow_international: true
- call_recording_enabled: false

Notification Group:
- email_notifications: true
- sms_notifications: false
- notification_email: admin@fusionpbx.com

Security Group:
- session_timeout: 1800
- max_login_attempts: 5
- password_min_length: 8
```

---

## SQL Schema Export

### Complete Schema Creation

```sql
-- Create Database
CREATE DATABASE IF NOT EXISTS fusionpbx_laravel 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE fusionpbx_laravel;

-- Users Table
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL DEFAULT 'user',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX idx_role (role),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Extensions Table
CREATE TABLE extensions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    extension_number VARCHAR(255) NOT NULL UNIQUE,
    user_id BIGINT UNSIGNED NULL,
    status ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    description VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_deleted_at (deleted_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Call Logs Table
CREATE TABLE call_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    caller_id VARCHAR(255) NOT NULL,
    destination VARCHAR(255) NOT NULL,
    duration INT NOT NULL DEFAULT 0,
    status ENUM('answered', 'missed', 'busy', 'failed') NOT NULL DEFAULT 'answered',
    call_type VARCHAR(255) NULL,
    call_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_caller_id (caller_id),
    INDEX idx_destination (destination),
    INDEX idx_status (status),
    INDEX idx_call_date (call_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings Table
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(255) NOT NULL UNIQUE,
    `value` TEXT NULL,
    `type` VARCHAR(255) NOT NULL DEFAULT 'string',
    `group` VARCHAR(255) NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_group (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Common Queries

### User Management

```sql
-- Get all active users
SELECT id, name, email, role 
FROM users 
WHERE deleted_at IS NULL;

-- Get admin users
SELECT * FROM users 
WHERE role = 'admin' AND deleted_at IS NULL;

-- Get user with their extensions
SELECT u.name, u.email, e.extension_number, e.status
FROM users u
LEFT JOIN extensions e ON u.id = e.user_id
WHERE u.deleted_at IS NULL;
```

### Extension Management

```sql
-- Get all active extensions
SELECT * FROM extensions 
WHERE status = 'active' AND deleted_at IS NULL;

-- Get extensions without assigned users
SELECT * FROM extensions 
WHERE user_id IS NULL AND deleted_at IS NULL;

-- Count extensions by status
SELECT status, COUNT(*) as count 
FROM extensions 
WHERE deleted_at IS NULL 
GROUP BY status;
```

### Call Logs Analysis

```sql
-- Get recent calls (last 24 hours)
SELECT * FROM call_logs 
WHERE call_date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
ORDER BY call_date DESC;

-- Get call statistics by status
SELECT status, COUNT(*) as count, AVG(duration) as avg_duration
FROM call_logs 
GROUP BY status;

-- Get top callers
SELECT caller_id, COUNT(*) as call_count, SUM(duration) as total_duration
FROM call_logs
GROUP BY caller_id
ORDER BY call_count DESC
LIMIT 10;

-- Get missed calls
SELECT * FROM call_logs 
WHERE status = 'missed'
ORDER BY call_date DESC;
```

### Settings Management

```sql
-- Get all system settings
SELECT * FROM settings 
WHERE `group` = 'system';

-- Get setting by key
SELECT value FROM settings 
WHERE `key` = 'app_name';

-- Get settings by group
SELECT `key`, value, description 
FROM settings 
WHERE `group` = 'call';
```

### Advanced Queries

```sql
-- Get user call history with extension details
SELECT 
    u.name as user_name,
    e.extension_number,
    cl.caller_id,
    cl.destination,
    cl.duration,
    cl.status,
    cl.call_date
FROM users u
JOIN extensions e ON u.id = e.user_id
JOIN call_logs cl ON e.extension_number = cl.destination
WHERE u.deleted_at IS NULL
ORDER BY cl.call_date DESC;

-- Get daily call statistics
SELECT 
    DATE(call_date) as date,
    COUNT(*) as total_calls,
    SUM(CASE WHEN status = 'answered' THEN 1 ELSE 0 END) as answered,
    SUM(CASE WHEN status = 'missed' THEN 1 ELSE 0 END) as missed,
    AVG(duration) as avg_duration
FROM call_logs
WHERE call_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(call_date)
ORDER BY date DESC;
```

---

## Database Backup

### Backup Commands

```bash
# Full database backup
mysqldump -u root -p fusionpbx_laravel > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup structure only
mysqldump -u root -p --no-data fusionpbx_laravel > structure_$(date +%Y%m%d).sql

# Backup specific tables
mysqldump -u root -p fusionpbx_laravel users extensions call_logs > backup_main_tables.sql
```

### Restore Commands

```bash
# Restore from backup
mysql -u root -p fusionpbx_laravel < backup_20260209_120000.sql

# Restore to new database
mysql -u root -p -e "CREATE DATABASE fusionpbx_laravel_new"
mysql -u root -p fusionpbx_laravel_new < backup_20260209_120000.sql
```

---

## Migration Commands

```bash
# Run all migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Reset all migrations
php artisan migrate:reset

# Refresh migrations (reset + migrate)
php artisan migrate:refresh

# Refresh with seeding
php artisan migrate:refresh --seed

# Check migration status
php artisan migrate:status
```

---

## Database Access

### Via Laravel Tinker

```bash
php artisan tinker

# Get all users
>>> User::all();

# Get user by ID
>>> User::find(1);

# Create new user
>>> User::create(['name' => 'Test User', 'email' => 'test@test.com', 'password' => bcrypt('password'), 'role' => 'user']);

# Get extensions with users
>>> Extension::with('user')->get();
```

### Via MySQL CLI

```bash
# Connect to database
mysql -u root -p fusionpbx_laravel

# Show tables
SHOW TABLES;

# Describe table structure
DESCRIBE users;

# Show table indexes
SHOW INDEX FROM users;
```

---

## Performance Optimization

### Recommended Indexes (Already Implemented)

All critical columns are indexed:
- ✅ Foreign keys (user_id)
- ✅ Unique constraints (email, extension_number, key)
- ✅ Frequently searched columns (role, status, caller_id, call_date)
- ✅ Soft delete columns (deleted_at)

### Query Optimization Tips

1. **Use Indexes**: All WHERE clauses use indexed columns
2. **Limit Results**: Use pagination for large datasets
3. **Avoid SELECT ***: Select only needed columns
4. **Use Eager Loading**: Load relationships efficiently
5. **Cache Results**: Cache frequently accessed data

---

## Security Considerations

### Password Storage
- Passwords are hashed using bcrypt (cost factor: 12)
- Never store plain text passwords
- Use `Hash::make()` or `bcrypt()` helper

### SQL Injection Prevention
- Use Laravel's Eloquent ORM
- Use parameter binding for raw queries
- Never concatenate user input into SQL

### Data Protection
- Soft deletes preserve data integrity
- Foreign key constraints maintain referential integrity
- Proper access control through roles

---

## Troubleshooting

### Common Issues

**Connection Refused:**
```bash
# Check MySQL is running
sudo systemctl status mysql

# Start MySQL
sudo systemctl start mysql
```

**Authentication Error:**
```bash
# Reset MySQL password
sudo mysql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'new_password';
FLUSH PRIVILEGES;
```

**Migration Failed:**
```bash
# Clear cache and retry
php artisan config:clear
php artisan cache:clear
php artisan migrate
```

---

## Additional Resources

- **Laravel Documentation:** https://laravel.com/docs/12.x/database
- **MySQL Documentation:** https://dev.mysql.com/doc/
- **Eloquent ORM:** https://laravel.com/docs/12.x/eloquent
- **Migrations:** https://laravel.com/docs/12.x/migrations
- **Seeding:** https://laravel.com/docs/12.x/seeding

---

## Change Log

| Date | Version | Changes |
|------|---------|---------|
| 2026-02-09 | 1.0.0 | Initial database schema documentation |

---

**Last Updated:** February 9, 2026  
**Database Version:** 1.0.0  
**Laravel Version:** 12.50.0
