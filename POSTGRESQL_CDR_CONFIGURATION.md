# PostgreSQL CDR and Billing Configuration for FusionPBX

## Overview

This document explains how to configure FusionPBX to use your existing PostgreSQL database for Call Detail Records (CDR) and optionally for voicemail, eliminating the need to create separate ODBC connections.

## Problem Statement

By default, the FusionPBX installation includes configuration files with hardcoded database credentials:
- `cdr_pg_csv.conf.xml` had: `host=localhost user=postgres password=nopassword dbname=fusionpbx`
- This doesn't match your actual PostgreSQL configuration in `/etc/fusionpbx/config.conf`

## Solution

The configuration has been updated to use FreeSwitch preprocessor variables defined in `vars.xml`, allowing you to configure database connections in one central location.

## Configuration Files Modified

### 1. `/app/switch/resources/conf/vars.xml`

Added two new database configuration variables:

```xml
<!-- Database -->
<!-- PostgreSQL connection string for CDR and other modules -->
<X-PRE-PROCESS cmd="set" data="dsn_cdr=host=127.0.0.1 user=fusionpbx password=password dbname=fusionpbx connect_timeout=10" category="Database" enabled="true" uuid="8b3a9d4c-1f7e-4e2a-9c5b-7d8e6f9a0b1c"/>

<!-- ODBC DSN for voicemail (format: dsn_name:username:password) - leave empty to use SQLite -->
<X-PRE-PROCESS cmd="set" data="dsn_voicemail=" category="Database" enabled="true" uuid="9c4b0e5d-2g8f-5f3b-0d6c-8e9f7g0b2d3d"/>
```

### 2. `/app/switch/resources/conf/autoload_configs/cdr_pg_csv.conf.xml`

Updated to use the variable:

```xml
<param name="db-info" value="$${dsn_cdr}" />
```

### 3. `/app/switch/resources/conf/autoload_configs/voicemail.conf.xml`

Updated to use the variable:

```xml
<param name="odbc-dsn" value="$${dsn_voicemail}"/>
```

## How to Configure

### Method 1: Edit vars.xml directly (After Installation)

If FreeSwitch configuration files are already deployed to `/etc/freeswitch/`, edit:

```bash
sudo nano /etc/freeswitch/vars.xml
```

Find the Database section and update the `dsn_cdr` variable with your actual credentials:

```xml
<X-PRE-PROCESS cmd="set" data="dsn_cdr=host=127.0.0.1 user=fusionpbx password=Mh3FFWl8VFEGuYQ6EientF1ETA dbname=fusionpbx connect_timeout=10" category="Database" enabled="true" uuid="8b3a9d4c-1f7e-4e2a-9c5b-7d8e6f9a0b1c"/>
```

Then reload FreeSwitch configuration:

```bash
fs_cli -x "reloadxml"
```

### Method 2: Update via FusionPBX Web Interface

1. Log into FusionPBX web interface as administrator
2. Navigate to: **Advanced** → **Variables**
3. Find the variable named `dsn_cdr` in the Database category
4. Click **Edit**
5. Update the value with your database credentials:
   ```
   host=127.0.0.1 user=fusionpbx password=YOUR_PASSWORD dbname=fusionpbx connect_timeout=10
   ```
6. Click **Save**
7. Navigate to: **Status** → **SIP Status**
8. Click **Reload XML** or run `fs_cli -x "reloadxml"` from command line

### Method 3: Update Source Files (Before Deployment)

If you're deploying FusionPBX for the first time, update:

```
/var/www/fusionpbx/app/switch/resources/conf/vars.xml
```

Before copying configuration files to `/etc/freeswitch/`.

## PostgreSQL Connection String Format

The `dsn_cdr` variable uses PostgreSQL's libpq connection string format:

```
host=<hostname> user=<username> password=<password> dbname=<database_name> connect_timeout=<seconds>
```

**Parameters:**
- `host`: Database server hostname or IP (e.g., `127.0.0.1`, `localhost`, or `db.example.com`)
- `user`: PostgreSQL username (e.g., `fusionpbx`)
- `password`: PostgreSQL password
- `dbname`: Database name (e.g., `fusionpbx`)
- `connect_timeout`: Connection timeout in seconds (default: `10`)

**Optional parameters** (see PostgreSQL documentation):
- `port`: PostgreSQL port (default: `5432`)
- `sslmode`: SSL mode (`disable`, `allow`, `prefer`, `require`)
- `application_name`: Application name for logging

**Example with all options:**
```
host=127.0.0.1 port=5432 user=fusionpbx password=SecurePass123 dbname=fusionpbx sslmode=prefer connect_timeout=10
```

## Voicemail ODBC Configuration (Optional)

If you want to use PostgreSQL for voicemail instead of SQLite:

### Step 1: Create ODBC DSN

Create or edit `/etc/odbc.ini`:

```ini
[fusionpbx_odbc]
Description = FusionPBX PostgreSQL Database
Driver = PostgreSQL
Server = 127.0.0.1
Port = 5432
Database = fusionpbx
Username = fusionpbx
Password = Mh3FFWl8VFEGuYQ6EientF1ETA
```

### Step 2: Update dsn_voicemail variable

In vars.xml (or via web interface), set:

```xml
<X-PRE-PROCESS cmd="set" data="dsn_voicemail=fusionpbx_odbc:fusionpbx:Mh3FFWl8VFEGuYQ6EientF1ETA" .../>
```

Format: `dsn_name:username:password`

### Step 3: Reload FreeSwitch

```bash
fs_cli -x "reloadxml"
```

**Note:** If `dsn_voicemail` is empty, voicemail will use the default SQLite database.

## Verifying Configuration

### Check CDR Database Connection

1. Make a test call
2. Check if CDR was recorded:

```bash
sudo -u postgres psql fusionpbx -c "SELECT uuid, caller_id_name, destination_number, start_stamp FROM v_xml_cdr ORDER BY start_stamp DESC LIMIT 5;"
```

### Check Voicemail Database Connection

1. Leave a voicemail message
2. Check via FusionPBX web interface: **Apps** → **Voicemail**
3. Or check the database:

```bash
# If using PostgreSQL
sudo -u postgres psql fusionpbx -c "SELECT * FROM voicemail_msgs LIMIT 5;"

# If using SQLite (default)
sqlite3 /var/lib/freeswitch/db/voicemail_default.db "SELECT * FROM voicemail_msgs LIMIT 5;"
```

## Troubleshooting

### CDR not recording to database

1. Check FreeSwitch logs:
   ```bash
   tail -f /var/log/freeswitch/freeswitch.log | grep cdr_pg_csv
   ```

2. Verify PostgreSQL is accepting connections:
   ```bash
   psql -h 127.0.0.1 -U fusionpbx -d fusionpbx -c "SELECT version();"
   ```

3. Verify FreeSwitch loaded the module:
   ```bash
   fs_cli -x "module_exists mod_cdr_pg_csv"
   ```

4. Check database table exists:
   ```bash
   sudo -u postgres psql fusionpbx -c "\d v_xml_cdr"
   ```

### Voicemail ODBC issues

1. Test ODBC connection:
   ```bash
   isql -v fusionpbx_odbc username password
   ```

2. Check FreeSwitch can connect:
   ```bash
   fs_cli -x "odbc_query fusionpbx_odbc SELECT 1"
   ```

3. Check voicemail module logs:
   ```bash
   fs_cli -x "console loglevel voicemail 7"
   ```

## Database Schema

The CDR table `v_xml_cdr` should already exist in your FusionPBX database. If not, run:

```bash
sudo -u postgres psql fusionpbx < /var/www/fusionpbx/resources/install/sql/pgsql.sql
```

## Security Considerations

1. **Never commit actual passwords** to version control
2. Use **strong passwords** for database access
3. Consider using **SSL connections** (`sslmode=require`) for production
4. Restrict PostgreSQL access in `/etc/postgresql/*/main/pg_hba.conf`:
   ```
   local   fusionpbx    fusionpbx                    md5
   host    fusionpbx    fusionpbx    127.0.0.1/32    md5
   ```

5. For web interface variables, use **FusionPBX permissions** to restrict access to database credentials

## Benefits of This Approach

1. ✅ **Single source of truth**: Database credentials in one place
2. ✅ **No separate ODBC setup needed**: Direct PostgreSQL connection
3. ✅ **Easier maintenance**: Change credentials in one file
4. ✅ **Better security**: Credentials not scattered across multiple files
5. ✅ **Centralized billing data**: All CDR in your main database
6. ✅ **Easier backups**: One database to backup
7. ✅ **Better performance**: Direct PostgreSQL connection vs ODBC overhead

## Additional Resources

- [PostgreSQL Connection Strings](https://www.postgresql.org/docs/current/libpq-connect.html#LIBPQ-CONNSTRING)
- [FreeSwitch mod_cdr_pg_csv](https://freeswitch.org/confluence/display/FREESWITCH/mod_cdr_pg_csv)
- [FusionPBX Documentation](https://docs.fusionpbx.com/)

## Support

For issues or questions:
- FusionPBX Community: https://fusionpbx.com/community.php
- FusionPBX Documentation: https://docs.fusionpbx.com/
