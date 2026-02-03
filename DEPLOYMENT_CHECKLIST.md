# Deployment Checklist for PostgreSQL CDR Configuration

## Pre-Deployment Verification

- [x] Modified files follow FusionPBX configuration patterns
- [x] No hardcoded passwords in committed files
- [x] Default password set to obvious placeholder (CHANGE_ME)
- [x] Documentation includes security warnings
- [x] Backward compatibility maintained
- [x] Code review completed and issues addressed

## Post-Deployment Tasks for Users

### 1. Update Database Credentials

Choose one method:

**Method A: Automated Script (Recommended)**
```bash
cd /var/www/fusionpbx
sudo chmod +x configure_postgresql_cdr.sh
sudo ./configure_postgresql_cdr.sh
```

**Method B: Web Interface**
```
1. Log into FusionPBX as admin
2. Navigate to: Advanced → Variables
3. Find variable: dsn_cdr (Category: Database)
4. Update value with actual credentials
5. Save changes
6. Reload FreeSwitch: Status → SIP Status → Reload XML
```

**Method C: Manual Edit**
```bash
sudo nano /etc/freeswitch/vars.xml
# Find the dsn_cdr line (around line 145)
# Update with actual credentials from /etc/fusionpbx/config.conf
sudo fs_cli -x "reloadxml"
```

### 2. Verify Configuration

```bash
# Check FreeSwitch loaded the configuration
fs_cli -x "eval \${dsn_cdr}"

# Make a test call

# Verify CDR recorded to database
sudo -u postgres psql fusionpbx -c "SELECT uuid, caller_id_name, destination_number, start_stamp FROM v_xml_cdr ORDER BY start_stamp DESC LIMIT 5;"
```

### 3. Monitor Logs

```bash
# Check for any CDR errors
tail -f /var/log/freeswitch/freeswitch.log | grep cdr_pg_csv

# Should see successful INSERT statements after calls
```

## Verification Checklist

### Configuration Files
- [ ] `/etc/freeswitch/vars.xml` has dsn_cdr variable updated with actual credentials
- [ ] FreeSwitch XML reloaded successfully (no errors in log)
- [ ] Can connect to database with provided credentials

### Database Connectivity
- [ ] PostgreSQL is accepting connections from FreeSwitch
- [ ] Table `v_xml_cdr` exists in database
- [ ] Database user has INSERT/UPDATE permissions on v_xml_cdr

### CDR Functionality
- [ ] Test call completed successfully
- [ ] CDR entry appears in database
- [ ] All call fields populated correctly (caller_id, destination, duration, etc.)
- [ ] No errors in FreeSwitch log related to cdr_pg_csv

### Optional: Voicemail Configuration
- [ ] If using PostgreSQL for voicemail, ODBC DSN created
- [ ] dsn_voicemail variable configured in vars.xml
- [ ] Test voicemail message recorded successfully
- [ ] Voicemail messages accessible via web UI

## Troubleshooting Common Issues

### Issue: CDR not recording
**Check:**
1. FreeSwitch logs: `tail -f /var/log/freeswitch/freeswitch.log | grep cdr_pg_csv`
2. Database connectivity: `psql -h 127.0.0.1 -U fusionpbx -d fusionpbx -c "SELECT version();"`
3. Module loaded: `fs_cli -x "module_exists mod_cdr_pg_csv"`
4. Variable expanded: `fs_cli -x "eval \${dsn_cdr}"`

### Issue: Authentication failed
**Check:**
1. Password correct in vars.xml
2. PostgreSQL pg_hba.conf allows connections from 127.0.0.1
3. User fusionpbx exists: `sudo -u postgres psql -c "\du fusionpbx"`

### Issue: Table not found
**Solution:**
```bash
sudo -u postgres psql fusionpbx < /var/www/fusionpbx/resources/install/sql/pgsql.sql
```

### Issue: Permission denied
**Solution:**
```bash
sudo -u postgres psql fusionpbx -c "GRANT ALL PRIVILEGES ON TABLE v_xml_cdr TO fusionpbx;"
```

## Rollback Procedure

If issues occur, rollback to previous configuration:

```bash
# Restore backup
sudo cp /etc/freeswitch/vars.xml.backup.YYYYMMDDHHMMSS /etc/freeswitch/vars.xml

# Or revert to hardcoded credentials
sudo nano /etc/freeswitch/autoload_configs/cdr_pg_csv.conf.xml
# Change: value="$${dsn_cdr}"
# To: value="host=localhost user=postgres password=password dbname=fusionpbx connect_timeout=10"

# Reload
sudo fs_cli -x "reloadxml"
```

## Success Criteria

All of the following should be true:

✅ FreeSwitch starts without errors
✅ Test calls complete successfully  
✅ CDR entries appear in v_xml_cdr table
✅ All call details populated (duration, timestamps, parties)
✅ No database connection errors in logs
✅ Web UI displays call history correctly
✅ Reports generate successfully

## Support

- Documentation: `POSTGRESQL_CDR_CONFIGURATION.md`
- Changes Summary: `CHANGES_SUMMARY.md`
- Configuration Script: `configure_postgresql_cdr.sh`
- FusionPBX Docs: https://docs.fusionpbx.com/
- Community: https://fusionpbx.com/community.php

## Notes

- Default behavior (SQLite) is preserved if variables not configured
- Changes are non-destructive to existing installations
- Backup created automatically by configuration script
- Can revert anytime without data loss
