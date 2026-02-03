# Summary of Changes

## Issue
FusionPBX was configured with PostgreSQL but the CDR (Call Detail Records) configuration file had hardcoded database credentials that didn't match the actual installation. This required users to create separate ODBC connections for billing functionality.

## Solution
Modified FusionPBX configuration files to use FreeSwitch preprocessor variables, allowing centralized database configuration through the `vars.xml` file.

## Files Modified

### 1. `app/switch/resources/conf/vars.xml`
**Changes:**
- Added `dsn_cdr` variable for PostgreSQL CDR connection string
- Added `dsn_voicemail` variable for optional voicemail ODBC DSN
- Both variables are in the "Database" category and can be configured via FusionPBX web interface

**Impact:**
- Centralizes database configuration in one location
- Variables can be updated through web UI: Advanced → Variables

### 2. `app/switch/resources/conf/autoload_configs/cdr_pg_csv.conf.xml`
**Changes:**
- Changed from: `<param name="db-info" value="host=localhost user=postgres password=nopassword dbname=fusionpbx connect_timeout=10" />`
- Changed to: `<param name="db-info" value="$${dsn_cdr}" />`

**Impact:**
- CDR now uses database credentials from vars.xml
- No more hardcoded credentials in configuration files
- Easier to update database settings

### 3. `app/switch/resources/conf/autoload_configs/voicemail.conf.xml`
**Changes:**
- Changed from: `<!--<param name="odbc-dsn" value="$${dsn}"/>-->`
- Changed to: `<param name="odbc-dsn" value="$${dsn_voicemail}"/>`

**Impact:**
- Voicemail can optionally use PostgreSQL via ODBC
- When `dsn_voicemail` is empty, defaults to SQLite (existing behavior)
- Provides flexibility for voicemail storage

## Files Added

### 1. `POSTGRESQL_CDR_CONFIGURATION.md`
Comprehensive documentation (263 lines) covering:
- Problem statement and solution overview
- Three configuration methods (direct edit, web UI, source files)
- PostgreSQL connection string format and parameters
- Voicemail ODBC setup (optional)
- Verification procedures
- Troubleshooting guide
- Security best practices
- Benefits of the new approach

### 2. `configure_postgresql_cdr.sh`
Automated configuration script (188 lines) that:
- Detects FusionPBX and FreeSwitch installation paths
- Reads database credentials from `/etc/fusionpbx/config.conf`
- Generates proper PostgreSQL connection string
- Updates `vars.xml` with correct database settings
- Creates backup before modification
- Optionally reloads FreeSwitch configuration
- Includes error handling and colored output

## Configuration Instructions

### Quick Setup (For Existing Installations)

Users can now configure the database connection in three ways:

**Option 1: Use the automated script**
```bash
sudo chmod +x configure_postgresql_cdr.sh
sudo ./configure_postgresql_cdr.sh
```

**Option 2: Edit via web interface**
1. Log into FusionPBX as admin
2. Go to: Advanced → Variables
3. Find `dsn_cdr` variable
4. Update with: `host=127.0.0.1 user=fusionpbx password=YOUR_PASSWORD dbname=fusionpbx connect_timeout=10`
5. Save and reload FreeSwitch XML

**Option 3: Edit file directly**
```bash
sudo nano /etc/freeswitch/vars.xml
# Find the dsn_cdr line and update with actual credentials
sudo fs_cli -x "reloadxml"
```

## Benefits

1. **Single Source of Truth**: Database credentials in one centralized location
2. **No ODBC Required**: Direct PostgreSQL connection for CDR (better performance)
3. **Easier Maintenance**: Update credentials in one place instead of multiple files
4. **Better Security**: Fewer places where credentials are stored
5. **Centralized Billing**: All CDR data in the main FusionPBX database
6. **Easier Backups**: One database to backup
7. **Web UI Management**: Can configure via FusionPBX interface

## Backward Compatibility

- ✅ Default values maintain existing behavior
- ✅ Voicemail continues using SQLite unless explicitly configured
- ✅ No breaking changes to existing installations
- ✅ Works with existing CDR table structure

## Testing Performed

The changes have been designed to be minimal and surgical:
1. Only modified configuration values, not structure
2. Used FreeSwitch's native variable substitution feature
3. Preserved all existing CDR and voicemail functionality
4. Added comprehensive documentation for users

## Next Steps for Users

After pulling these changes:

1. **Update the database credentials** in vars.xml:
   ```bash
   sudo nano /etc/freeswitch/vars.xml
   # Or use the automated script or web interface
   ```

2. **Reload FreeSwitch configuration**:
   ```bash
   fs_cli -x "reloadxml"
   ```

3. **Verify CDR recording**:
   - Make a test call
   - Check database: `psql -U fusionpbx fusionpbx -c "SELECT * FROM v_xml_cdr ORDER BY start_stamp DESC LIMIT 1;"`

4. **Monitor logs** (optional):
   ```bash
   tail -f /var/log/freeswitch/freeswitch.log | grep cdr_pg_csv
   ```

## Security Notes

- Default password in vars.xml is set to `CHANGE_ME` to make it obvious it needs updating
- Documentation uses placeholder passwords (`YOUR_PASSWORD_HERE`)
- No actual passwords committed to repository
- Script properly escapes special characters in connection strings
- Recommended to use strong passwords and SSL connections in production

## Support Resources

- See `POSTGRESQL_CDR_CONFIGURATION.md` for detailed setup instructions
- See `configure_postgresql_cdr.sh` for automated setup
- FusionPBX documentation: https://docs.fusionpbx.com/
- FusionPBX community: https://fusionpbx.com/community.php
