# FusionPBX Billing Module - Installation and Configuration Guide

## Overview

This guide provides complete installation and configuration instructions for the FusionPBX Billing Module with mod_nibblebill integration.

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Installation Steps](#installation-steps)
3. [Configuration Files](#configuration-files)
4. [Testing the Setup](#testing-the-setup)
5. [Troubleshooting](#troubleshooting)

---

## Prerequisites

- FusionPBX installed and running
- FreeSWITCH with mod_nibblebill compiled and available
- PostgreSQL or MySQL database
- Root or sudo access to server
- Basic knowledge of FreeSWITCH dialplan

---

## Installation Steps

### Step 1: Upgrade Database Schema

After installing the billing module files, upgrade the database:

```bash
cd /var/www/fusionpbx
php core/upgrade/upgrade_schema.php
```

This creates the billing tables:
- `v_billing_rates`
- `v_billing_balances`
- `v_billing_user_rates`
- `v_billing_agent_rates`
- `v_billing_usage`
- `v_billing_balance_history`

### Step 2: Install mod_nibblebill Configuration

#### Option A: Auto-Generate (Recommended)

The billing module will automatically create the nibblebill.conf.xml file during the first upgrade.

Location: `/etc/freeswitch/autoload_configs/nibblebill.conf.xml`

#### Option B: Manual Installation

Copy the template file:

```bash
# Copy nibblebill configuration
sudo cp /var/www/fusionpbx/app/billing/resources/install/nibblebill.conf.xml \
        /etc/freeswitch/autoload_configs/nibblebill.conf.xml

# Set ownership
sudo chown www-data:www-data /etc/freeswitch/autoload_configs/nibblebill.conf.xml
```

**Edit the file to match your database settings:**

```xml
<configuration name="nibblebill.conf" description="FusionPBX Prepaid Billing Configuration">
  <settings>
    <!-- Database Table and Column Names -->
    <param name="db_table" value="v_billing_balances"/>
    <param name="db_column_cash" value="balance"/>
    <param name="db_column_account" value="extension_uuid"/>
    
    <!-- Custom SQL for Balance Lookup -->
    <param name="custom_sql_lookup" value="SELECT balance FROM v_billing_balances WHERE extension_uuid='${nibble_account}' AND domain_uuid='${domain_uuid}'"/>
    
    <!-- Custom SQL for Balance Updates -->
    <param name="custom_sql_save" value="UPDATE v_billing_balances SET balance=${nibble_balance}, last_updated=NOW() WHERE extension_uuid='${nibble_account}' AND domain_uuid='${domain_uuid}'"/>
    
    <!-- Heartbeat: Check balance every 60 seconds -->
    <param name="global_heartbeat" value="60"/>
    
    <!-- Low Balance: Warning at $5 -->
    <param name="lowbal_amt" value="5"/>
    <param name="lowbal_action" value="play ivr/ivr-account_balance_low.wav"/>
    
    <!-- No Balance: Hangup at $0 -->
    <param name="nobal_amt" value="0"/>
    <param name="nobal_action" value="hangup"/>
    
    <!-- Per-Call Maximum: $1000 -->
    <param name="percall_max_amt" value="1000"/>
    <param name="percall_action" value="hangup"/>
  </settings>
</configuration>
```

### Step 3: Load mod_nibblebill in FreeSWITCH

Edit `/etc/freeswitch/autoload_configs/modules.conf.xml` and ensure mod_nibblebill is loaded:

```xml
<load module="mod_nibblebill"/>
```

Reload FreeSWITCH:

```bash
fs_cli -x "reloadxml"
fs_cli -x "reload mod_nibblebill"
```

Verify it's loaded:

```bash
fs_cli -x "module_exists mod_nibblebill"
# Should return: true
```

### Step 4: Install Lua Scripts

Copy the billing Lua scripts to FreeSWITCH scripts directory:

```bash
# Create billing scripts directory
sudo mkdir -p /usr/share/freeswitch/scripts/app/billing

# Copy rate lookup script
sudo cp /var/www/fusionpbx/app/billing/resources/install/scripts/check_rate.lua \
        /usr/share/freeswitch/scripts/app/billing/

# Copy balance check script
sudo cp /var/www/fusionpbx/app/billing/resources/install/scripts/check_balance.lua \
        /usr/share/freeswitch/scripts/app/billing/

# Set ownership
sudo chown -R www-data:www-data /usr/share/freeswitch/scripts/app/billing/
```

### Step 5: Install Dialplan Integration

#### Option A: Via FusionPBX Dialplan Manager (Recommended)

1. Navigate to **Dialplan → Dialplan Manager**
2. Click **Add** to create a new dialplan entry
3. Set:
   - **Name**: `billing_pre_call_check`
   - **Order**: `888` (before outbound routes)
   - **Context**: `default`
   - **Enabled**: `true`
4. Paste the XML from `888_billing_integration.xml`
5. Save and reload dialplan

#### Option B: Direct File Copy

```bash
# Copy dialplan file
sudo cp /var/www/fusionpbx/app/billing/resources/install/dialplan/888_billing_integration.xml \
        /etc/freeswitch/dialplan/default/

# Set ownership
sudo chown www-data:www-data /etc/freeswitch/dialplan/default/888_billing_integration.xml

# Reload dialplan
fs_cli -x "reloadxml"
```

### Step 6: Configure Database Connection

Ensure FreeSWITCH can access the FusionPBX database.

Edit `/etc/odbc.ini` and verify the DSN configuration:

```ini
[fusionpbx]
Driver = PostgreSQL
Description = FusionPBX Database
Servername = localhost
Port = 5432
Database = fusionpbx
Username = fusionpbx
Password = your_password
```

Test ODBC connection:

```bash
echo "SELECT 1;" | isql -v fusionpbx
```

### Step 7: Assign Permissions

In FusionPBX:

1. Navigate to **Groups → Group Permissions**
2. Assign billing permissions to appropriate groups:
   - **superadmin/admin**: All billing permissions
   - **user**: `billing_balance_view`, `billing_usage_view`
   - **agent**: `billing_agent_view`

---

## Configuration Files Reference

### 1. nibblebill.conf.xml

**Location**: `/etc/freeswitch/autoload_configs/nibblebill.conf.xml`

**Purpose**: Configures mod_nibblebill for real-time prepaid billing

**Key Parameters**:
- `custom_sql_lookup`: Query to get current balance
- `custom_sql_save`: Query to update balance
- `global_heartbeat`: Balance check interval (60 seconds)
- `lowbal_amt`: Low balance threshold ($5)
- `nobal_amt`: Minimum balance before hangup ($0)

### 2. Dialplan Files

#### 888_billing_integration.xml

**Location**: `/etc/freeswitch/dialplan/default/888_billing_integration.xml`

**Purpose**: Pre-call balance check and rate lookup

**Features**:
- Checks balance before outbound calls
- Looks up rate based on destination
- Sets nibblebill variables
- Blocks calls with insufficient funds

#### 999_billing_check.xml (Optional)

**Location**: `/etc/freeswitch/dialplan/default/999_billing_check.xml`

**Purpose**: Simple balance check without rate lookup

**Usage**: Lightweight pre-call check

### 3. Lua Scripts

#### check_rate.lua

**Location**: `/usr/share/freeswitch/scripts/app/billing/check_rate.lua`

**Purpose**: Looks up billing rate from database based on destination

**Parameters**:
1. `destination_number`: Number being called
2. `domain_uuid`: Domain UUID
3. `user_uuid`: User UUID

**Returns**: Rate per minute (decimal)

#### check_balance.lua

**Location**: `/usr/share/freeswitch/scripts/app/billing/check_balance.lua`

**Purpose**: Checks if user has sufficient balance

**Actions**:
- Queries database for balance
- Compares against minimum threshold
- Hangs up if insufficient
- Sets nibblebill variables if sufficient

---

## Setting Up Billing for a User

### 1. Create Rate Plans

Navigate to **Billing → Rate Plans** and create rate plans:

**Example 1: US Domestic**
- Rate Name: `US Domestic`
- Destination Prefix: `1`
- Rate per Minute: `0.0100` ($0.01)
- Billing Increment: `60` (per minute)
- Connection Fee: `0.0000`
- Currency: `USD`
- Enabled: `Yes`

**Example 2: International**
- Rate Name: `UK`
- Destination Prefix: `44`
- Rate per Minute: `0.0500` ($0.05)
- Billing Increment: `60`
- Connection Fee: `0.0000`
- Currency: `USD`
- Enabled: `Yes`

### 2. Create Prepaid Balance

Navigate to **Billing → Prepaid Balances** and add balance:

- **Extension**: Select user extension (e.g., 1001)
- **Balance**: `10.00` ($10 initial balance)
- **Currency**: `USD`
- **Low Balance Alert**: `Enabled`
- **Low Balance Threshold**: `5.00`
- **Assigned Rates**: Select rate plans (e.g., US Domestic, UK)

Click **Save**

### 3. Test a Call

1. Make an outbound call from extension 1001
2. Check FreeSWITCH log for billing messages:

```bash
fs_cli -x "console loglevel info"
# Watch for "Billing:" messages
```

3. Verify balance is deducted:
   - Navigate to **Billing → Prepaid Balances**
   - Check the balance has decreased

4. View usage history:
   - Navigate to **Billing → Usage History**
   - See the call record with cost

---

## Testing the Setup

### Test 1: Check Balance via Dialplan

Dial `9999` from any extension to hear your current balance.

### Test 2: Verify mod_nibblebill

```bash
fs_cli -x "module_exists mod_nibblebill"
# Should return: true
```

### Test 3: Test Rate Lookup

```bash
fs_cli -x "lua /usr/share/freeswitch/scripts/app/billing/check_rate.lua 14155551234 DOMAIN_UUID USER_UUID"
# Should return: 0.01 (or your configured rate)
```

### Test 4: Make Test Call

1. Create balance record with $1.00
2. Make a short call
3. Verify:
   - Call connects successfully
   - Balance decreases
   - Usage record created
   - Balance history updated

### Test 5: Test Insufficient Balance

1. Set balance to $0.00
2. Try to make a call
3. Verify:
   - Call is rejected
   - Message played (if configured)
   - No balance deduction

---

## Troubleshooting

### Issue: mod_nibblebill not loaded

**Solution**:
```bash
# Check modules.conf.xml
grep nibblebill /etc/freeswitch/autoload_configs/modules.conf.xml

# If missing, add:
# <load module="mod_nibblebill"/>

# Restart FreeSWITCH
systemctl restart freeswitch
```

### Issue: Database connection failed

**Solution**:
```bash
# Test ODBC connection
echo "SELECT 1;" | isql -v fusionpbx

# Check /etc/odbc.ini configuration
# Verify database credentials
```

### Issue: Rate lookup fails

**Solution**:
```bash
# Check if tables exist
sudo -u postgres psql fusionpbx -c "SELECT * FROM v_billing_rates LIMIT 1;"

# Check Lua script syntax
lua -l /usr/share/freeswitch/scripts/app/billing/check_rate.lua

# Check FreeSWITCH logs
tail -f /var/log/freeswitch/freeswitch.log | grep "Billing Rate"
```

### Issue: Balance not updating

**Solution**:
```bash
# Check SQL query in nibblebill.conf.xml
# Verify domain_uuid variable is set
fs_cli -x "uuid_dump CALL_UUID" | grep domain_uuid

# Check balance table
sudo -u postgres psql fusionpbx -c "SELECT * FROM v_billing_balances WHERE extension_uuid='UUID';"
```

### Issue: Calls not being billed

**Solution**:
1. Check dialplan order - billing check must run before outbound route
2. Verify nibblebill variables are set:
   ```bash
   fs_cli -x "uuid_dump CALL_UUID" | grep nibble
   ```
3. Check CDR processing:
   ```bash
   tail -f /var/log/freeswitch/freeswitch.log | grep CDR
   ```

### Issue: Balance history not recording

**Solution**:
```bash
# Check if table exists
sudo -u postgres psql fusionpbx -c "\d v_billing_balance_history;"

# Check billing class update_balance method
# Verify it's being called with correct parameters
```

---

## Advanced Configuration

### Custom Balance Check Audio

Record custom audio prompts:

```bash
# Record low balance message
/usr/share/freeswitch/sounds/en/us/callie/ivr/ivr-account_balance_low.wav

# Record insufficient funds message
/usr/share/freeswitch/sounds/en/us/callie/ivr/ivr-insufficient_funds.wav
```

### Per-Second Billing

Modify rate plans:
- Set **Billing Increment**: `1` (for per-second)
- Adjust **Rate per Minute** accordingly

### Multiple Currency Support

Create separate rate plans for each currency:
- USD rates with currency `USD`
- EUR rates with currency `EUR`
- Assign appropriate rates to users

### Integration with Payment Gateways

Extend `billing_balance_adjust.php` to integrate with:
- PayPal
- Stripe
- Authorize.net
- Other payment processors

---

## Maintenance

### Regular Tasks

1. **Monitor Balance Usage**:
   ```sql
   SELECT e.extension, b.balance, b.last_updated
   FROM v_billing_balances b
   JOIN v_extensions e ON b.extension_uuid = e.extension_uuid
   WHERE b.balance < b.low_balance_threshold;
   ```

2. **Process Unbilled CDRs**:
   ```bash
   php /var/www/fusionpbx/app/billing/resources/service/cdr_billing_hook.php
   ```

3. **Backup Billing Data**:
   ```bash
   pg_dump -U fusionpbx -t v_billing_* fusionpbx > billing_backup.sql
   ```

### Log Rotation

Add to `/etc/logrotate.d/freeswitch`:

```
/var/log/freeswitch/freeswitch.log {
    daily
    rotate 7
    compress
    missingok
    postrotate
        /usr/bin/fs_cli -x "fsctl send_sighup" > /dev/null 2>&1
    endscript
}
```

---

## Support and Resources

- **FusionPBX Documentation**: https://docs.fusionpbx.com
- **FreeSWITCH mod_nibblebill**: https://freeswitch.org/confluence/display/FREESWITCH/mod_nibblebill
- **GitHub Issues**: Report bugs and request features
- **Community Forum**: https://fusionpbx.com/community

---

## Version Information

- **Billing Module Version**: 1.0
- **FusionPBX Compatibility**: 5.x
- **FreeSWITCH Compatibility**: 1.10+
- **Last Updated**: February 2026

---

## License

Mozilla Public License 1.1 (MPL 1.1)
