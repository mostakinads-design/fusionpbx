# FusionPBX Billing - Quick Start Guide

## 🚀 Fast Installation (5 Minutes)

### Step 1: Upgrade Database (1 min)
```bash
cd /var/www/fusionpbx
php core/upgrade/upgrade_schema.php
```
✅ Creates 6 billing tables
✅ Auto-generates nibblebill.conf.xml

### Step 2: Enable mod_nibblebill (1 min)
```bash
# Edit modules.conf.xml
sudo nano /etc/freeswitch/autoload_configs/modules.conf.xml

# Add this line:
<load module="mod_nibblebill"/>

# Save and reload
fs_cli -x "reload mod_nibblebill"
fs_cli -x "module_exists mod_nibblebill"  # Should return: true
```

### Step 3: Install Lua Scripts (1 min)
```bash
sudo mkdir -p /usr/share/freeswitch/scripts/app/billing
sudo cp /var/www/fusionpbx/app/billing/resources/install/scripts/*.lua \
        /usr/share/freeswitch/scripts/app/billing/
sudo chown -R www-data:www-data /usr/share/freeswitch/scripts/app/billing/
```

### Step 4: Install Dialplan (1 min)
```bash
sudo cp /var/www/fusionpbx/app/billing/resources/install/dialplan/888_billing_integration.xml \
        /etc/freeswitch/dialplan/default/
sudo chown www-data:www-data /etc/freeswitch/dialplan/default/888_billing_integration.xml
fs_cli -x "reloadxml"
```

### Step 5: Create Test Data (1 min)
```bash
# Load sample rate plans
sudo -u postgres psql fusionpbx < /var/www/fusionpbx/app/billing/resources/install/sql/billing_sample_data.sql
```

## 📋 Create Your First Billable User

### Via Web UI:

1. **Create Rate Plan**
   - Navigate to: **Billing → Rate Plans → Add**
   - Rate Name: `US Calls`
   - Prefix: `1`
   - Rate: `0.01` (1 cent per minute)
   - Click **Save**

2. **Create Balance**
   - Navigate to: **Billing → Prepaid Balances → Add**
   - Extension: `1001`
   - Balance: `10.00`
   - Assigned Rates: Check `US Calls`
   - Click **Save**

3. **Test Call**
   - Dial any US number from extension 1001
   - Call should connect
   - Balance decreases after call ends

4. **Check Results**
   - Go to: **Billing → Usage History**
   - See your call with cost details

## ✅ Verify Installation

```bash
# 1. Check mod_nibblebill
fs_cli -x "module_exists mod_nibblebill"
# Expected: true

# 2. Check config file
ls -l /etc/freeswitch/autoload_configs/nibblebill.conf.xml
# Should exist

# 3. Check Lua scripts
ls -l /usr/share/freeswitch/scripts/app/billing/
# Should show: check_balance.lua, check_rate.lua

# 4. Check dialplan
ls -l /etc/freeswitch/dialplan/default/888_billing_integration.xml
# Should exist

# 5. Check database tables
sudo -u postgres psql fusionpbx -c "\dt v_billing*"
# Should show 6 tables
```

## 🎯 Test Features

### Test Balance Info
Dial `9999` from any extension to hear current balance.

### Test Insufficient Balance
1. Set extension balance to `$0.00`
2. Try making a call
3. Call should be rejected

### Test Rate Lookup
```bash
fs_cli -x "lua app/billing/check_rate.lua 14155551234 YOUR_DOMAIN_UUID YOUR_USER_UUID"
# Should return rate (e.g., 0.01)
```

### Test Real-time Billing
1. Create balance with `$1.00`
2. Make a 2-minute call
3. Watch FreeSWITCH logs:
```bash
fs_cli
/log info
# Look for "Billing:" messages
```

## 📚 Next Steps

- **Full Documentation**: See `INSTALL.md`
- **Configuration Reference**: See `CONFIGURATION.md`
- **Module Features**: See `README.md`

## ⚡ Common Commands

```bash
# Reload FreeSWITCH config
fs_cli -x "reloadxml"

# Reload mod_nibblebill
fs_cli -x "reload mod_nibblebill"

# View FreeSWITCH logs
tail -f /var/log/freeswitch/freeswitch.log | grep Billing

# Check balance via SQL
sudo -u postgres psql fusionpbx -c "SELECT e.extension, b.balance FROM v_billing_balances b JOIN v_extensions e ON b.extension_uuid=e.extension_uuid;"

# Process unbilled CDRs
php /var/www/fusionpbx/app/billing/resources/service/cdr_billing_hook.php
```

## 🆘 Quick Troubleshooting

**Problem**: mod_nibblebill not loading
```bash
# Check if compiled
ls /usr/lib/freeswitch/mod/mod_nibblebill.so
# If missing, install: apt-get install freeswitch-mod-nibblebill
```

**Problem**: Database connection failed
```bash
# Test ODBC
echo "SELECT 1;" | isql -v fusionpbx
# Fix credentials in /etc/odbc.ini
```

**Problem**: Calls not being billed
```bash
# Check dialplan executed
fs_cli -x "show dialplan"
# Look for billing_pre_call_check

# Check variables set
# During call: fs_cli -x "uuid_dump CALL_UUID" | grep nibble
```

## 🎉 Success!

Your FusionPBX Billing Module is now installed and ready to use!

For detailed configuration and advanced features, see the full documentation.
