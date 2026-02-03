# FusionPBX Billing Module

## Overview

The FusionPBX Billing Module provides comprehensive **prepaid and digit-based billing** for users and call center agents. It features:

- **Dynamic Rate Plans** with support for multiple destination prefixes per user
- **Prepaid Balance Management** with automatic deduction on calls
- **Call Center Agent Billing** with agent-specific rates
- **Real-time Billing** using mod_nibblebill integration
- **Automatic Call Cutoff** when balance runs out
- **Balance History Tracking** for all transactions
- **CDR Integration** for automatic post-call billing
- **Usage Reports** with filtering and statistics

---

## Features

### 1. Rate Plan Management
- Create rate plans with destination prefixes (e.g., 1 for US, 44 for UK, 91 for India)
- Set rate per minute, billing increment (per-second or per-minute)
- Configure connection fees and minimum duration
- Assign multiple prefixes to a single user or agent
- Enable/disable rates dynamically

### 2. Prepaid Balance System
- Manage user balances with add/deduct credit functionality
- Low balance alerts and thresholds
- Balance history tracking with transaction details
- Manual balance adjustments with descriptions
- Multi-currency support

### 3. Call Center Agent Billing
- Separate billing for call center agents
- Agent-specific rate assignments
- Integration with call center queues
- Track agent call costs separately

### 4. Real-time Billing (mod_nibblebill)
- Check balance before call initiation
- Monitor balance during active calls
- Automatic call termination on insufficient funds
- Heartbeat updates every 60 seconds
- Configurable low balance and no balance thresholds

### 5. Usage Tracking & Reporting
- Detailed call usage history
- Filter by extension, date range, destination
- View billing statistics (total calls, duration, cost)
- Export capabilities
- CDR integration for billing information

---

## Installation

### 1. Upgrade Schema
After deploying the billing module files, upgrade the database schema:

```bash
cd /var/www/fusionpbx
php /var/www/fusionpbx/core/upgrade/upgrade_schema.php
```

This will create the following tables:
- `v_billing_rates` - Rate plans and destination prefixes
- `v_billing_balances` - User prepaid balances
- `v_billing_user_rates` - User-to-rate assignments (many-to-many)
- `v_billing_agent_rates` - Agent-specific rate assignments
- `v_billing_usage` - Call usage records
- `v_billing_balance_history` - Balance transaction history

### 2. Configure mod_nibblebill
The module automatically creates `/usr/share/freeswitch/conf/autoload_configs/nibblebill.conf.xml` during installation.

Restart FreeSWITCH to load the configuration:
```bash
fs_cli -x "reloadxml"
fs_cli -x "reload mod_nibblebill"
```

### 3. Assign Permissions
Assign appropriate permissions to user groups:
- **superadmin/admin**: Full access to all billing features
- **user**: View own balance and usage
- **agent**: View own billing as call center agent

---

## Usage

### Setting Up Billing for a User

#### Step 1: Create Rate Plans
1. Navigate to **Billing → Rate Plans**
2. Click **Add** to create a new rate plan
3. Enter:
   - **Rate Name**: e.g., "US Domestic"
   - **Destination Prefix**: e.g., "1" (for US)
   - **Rate per Minute**: e.g., 0.0200 (2 cents per minute)
   - **Billing Increment**: 60 (per-minute) or 1 (per-second)
   - **Connection Fee**: One-time fee per call
   - **Currency**: USD, EUR, etc.
4. Click **Save**

Create multiple rate plans for different destinations.

#### Step 2: Create Prepaid Balance
1. Navigate to **Billing → Prepaid Balances**
2. Click **Add** to create a balance for a user
3. Select:
   - **Extension**: Choose the user/extension
   - **Balance**: Initial balance amount
   - **Currency**: USD, EUR, etc.
   - **Low Balance Alert**: Enable/disable
   - **Low Balance Threshold**: Alert when balance drops below this
   - **Assigned Rates**: Select multiple rate plans for this user
4. Click **Save**

#### Step 3: Test a Call
1. Make an outbound call from the extension
2. After the call ends, check:
   - **Prepaid Balances**: Balance should be reduced
   - **Usage History**: Call record with cost details

### Manual Balance Adjustment

To add or deduct credit manually:

1. Navigate to **Billing → Prepaid Balances**
2. Click on the extension you want to adjust
3. Click **Adjust Balance** (or edit the URL to `billing_balance_adjust.php?id=UUID`)
4. Select:
   - **Transaction Type**: Add Credit, Deduct Credit, or Adjustment
   - **Amount**: Enter amount (always positive)
   - **Description**: Optional note
5. Click **Save**

The balance history will track all adjustments.

### Viewing Usage Reports

1. Navigate to **Billing → Usage History**
2. Filter by:
   - **Extension**: View specific user's usage
   - **Date Range**: From/To dates
   - **Search**: Destination number or prefix
3. View totals at the top:
   - Total Calls
   - Total Duration
   - Total Cost

---

## Call Flow Integration

### Automatic Call Cutoff on Zero Balance

To prevent calls when balance is insufficient, add a dialplan condition:

**Example Dialplan** (`/etc/freeswitch/dialplan/default/999_billing_check.xml`):

```xml
<extension name="billing_check" continue="true">
  <condition field="destination_number" expression="^(\d+)$">
    <action application="set" data="nibble_account=${user_uuid}"/>
    <action application="set" data="nibble_rate=0.01"/>
    <action application="nibblebill" data="check"/>
  </condition>
</extension>
```

This checks the balance before connecting the call. If insufficient, the call is rejected.

### Real-time Monitoring with mod_nibblebill

mod_nibblebill monitors the balance during active calls:
- **Heartbeat**: Updates balance every 60 seconds
- **Low Balance Warning**: Plays warning tone when balance < threshold
- **Auto Hangup**: Terminates call when balance reaches zero

Configuration is in `/usr/share/freeswitch/conf/autoload_configs/nibblebill.conf.xml`.

---

## CDR Integration

The billing module automatically processes CDRs after calls complete.

### Automatic Processing
Calls are billed automatically when:
- CDR record is inserted into `v_xml_cdr`
- `billsec > 0` (answered calls only)
- Extension has a balance record
- Matching rate plan is found

### Manual/Batch Processing
To process unbilled CDRs manually:

```php
<?php
require_once '/var/www/fusionpbx/app/billing/resources/service/cdr_billing_hook.php';

// Process all unbilled CDRs
$stats = batch_process_billing();
echo "Processed: " . $stats['processed'] . "\n";
echo "Failed: " . $stats['failed'] . "\n";
?>
```

Add to cron for periodic processing:
```bash
*/5 * * * * php /var/www/fusionpbx/app/billing/resources/service/cdr_billing_hook.php
```

---

## API Reference

### Billing Class

**Location**: `/var/www/fusionpbx/app/billing/resources/classes/billing.php`

#### Methods

##### `find_rate($destination_number)`
Find the best matching rate for a destination.
- **Parameters**: `$destination_number` - Destination number
- **Returns**: Array with rate details or null

##### `calculate_cost($duration, $rate)`
Calculate call cost based on duration and rate.
- **Parameters**: 
  - `$duration` - Call duration in seconds
  - `$rate` - Rate array from find_rate()
- **Returns**: Array with cost details

##### `get_balance()`
Get current balance for the extension.
- **Returns**: Float balance amount or null

##### `update_balance($amount, $transaction_type, $description, $billing_usage_uuid)`
Update balance with history tracking.
- **Parameters**:
  - `$amount` - Amount to add (positive) or deduct (negative)
  - `$transaction_type` - Type: credit, debit, call_cost, adjustment
  - `$description` - Transaction description
  - `$billing_usage_uuid` - Optional usage UUID if call-related
- **Returns**: Boolean success status

##### `check_balance($required_amount)`
Check if sufficient balance exists.
- **Parameters**: `$required_amount` - Required amount
- **Returns**: Boolean true/false

##### `process_call($xml_cdr_uuid, $destination_number, $duration, $call_date)`
Process billing for a completed call.
- **Parameters**:
  - `$xml_cdr_uuid` - CDR UUID
  - `$destination_number` - Destination number
  - `$duration` - Call duration in seconds
  - `$call_date` - Call timestamp
- **Returns**: Boolean success status

### Example Usage

```php
<?php
require_once 'resources/classes/billing.php';

$billing = new billing();
$billing->domain_uuid = $_SESSION['domain_uuid'];
$billing->extension_uuid = $extension_uuid;

// Check balance
$balance = $billing->get_balance();
echo "Current balance: $balance\n";

// Find rate for a destination
$rate = $billing->find_rate('14155551234');
if ($rate) {
    echo "Rate: " . $rate['rate_per_minute'] . " per minute\n";
}

// Calculate cost for a 5-minute call
$cost = $billing->calculate_cost(300, $rate);
echo "Cost: " . $cost['total_cost'] . "\n";

// Add credit
$billing->update_balance(10.00, 'credit', 'Manual credit addition');
?>
```

---

## Database Schema

### v_billing_rates
Stores rate plans with destination prefixes.

| Column | Type | Description |
|--------|------|-------------|
| billing_rate_uuid | UUID | Primary key |
| domain_uuid | UUID | Domain reference |
| rate_name | TEXT | Rate plan name |
| destination_prefix | TEXT | Destination prefix (e.g., 1, 44, 91) |
| rate_per_minute | DECIMAL(10,4) | Rate per minute |
| billing_increment | INTEGER | Billing increment in seconds |
| connection_fee | DECIMAL(10,4) | Connection fee per call |
| enabled | BOOLEAN | Enable/disable rate |

### v_billing_balances
Stores prepaid balances for users.

| Column | Type | Description |
|--------|------|-------------|
| billing_balance_uuid | UUID | Primary key |
| domain_uuid | UUID | Domain reference |
| extension_uuid | UUID | Extension reference |
| balance | DECIMAL(10,4) | Current balance |
| currency | TEXT | Currency code |
| low_balance_threshold | DECIMAL(10,4) | Alert threshold |
| last_updated | TIMESTAMP | Last update time |

### v_billing_user_rates
Many-to-many relationship: users to rate plans (multiple prefixes per user).

| Column | Type | Description |
|--------|------|-------------|
| billing_user_rate_uuid | UUID | Primary key |
| extension_uuid | UUID | Extension reference |
| billing_rate_uuid | UUID | Rate reference |
| enabled | BOOLEAN | Enable/disable assignment |

### v_billing_usage
Tracks call usage and costs.

| Column | Type | Description |
|--------|------|-------------|
| billing_usage_uuid | UUID | Primary key |
| xml_cdr_uuid | UUID | CDR reference |
| extension_uuid | UUID | Extension reference |
| destination_number | TEXT | Destination called |
| matched_prefix | TEXT | Matched rate prefix |
| duration | INTEGER | Call duration (seconds) |
| billable_duration | INTEGER | Billable duration (after increment) |
| cost | DECIMAL(10,4) | Total call cost |
| call_date | TIMESTAMP | Call timestamp |

### v_billing_balance_history
Tracks all balance changes.

| Column | Type | Description |
|--------|------|-------------|
| billing_balance_history_uuid | UUID | Primary key |
| billing_balance_uuid | UUID | Balance record reference |
| transaction_type | TEXT | credit, debit, call_cost, adjustment |
| amount | DECIMAL(10,4) | Transaction amount |
| balance_before | DECIMAL(10,4) | Balance before transaction |
| balance_after | DECIMAL(10,4) | Balance after transaction |
| description | TEXT | Transaction description |
| billing_usage_uuid | UUID | Related usage record (if call) |
| insert_date | TIMESTAMP | Transaction timestamp |

---

## Troubleshooting

### Calls Not Being Billed

1. **Check balance record exists**:
   ```sql
   SELECT * FROM v_billing_balances WHERE extension_uuid = 'UUID';
   ```

2. **Check rate assignment**:
   ```sql
   SELECT * FROM v_billing_user_rates WHERE extension_uuid = 'UUID' AND enabled = true;
   ```

3. **Check CDR processing**:
   - Look for errors in FreeSWITCH logs
   - Verify `billsec > 0` in CDR
   - Check if usage record was created

4. **Manually process CDR**:
   ```php
   process_cdr_billing($xml_cdr_uuid, $domain_uuid);
   ```

### mod_nibblebill Not Working

1. **Verify module loaded**:
   ```bash
   fs_cli -x "module_exists mod_nibblebill"
   ```

2. **Check configuration**:
   ```bash
   cat /usr/share/freeswitch/conf/autoload_configs/nibblebill.conf.xml
   ```

3. **Reload module**:
   ```bash
   fs_cli -x "reload mod_nibblebill"
   ```

4. **Check dialplan**:
   - Ensure `nibble_account` variable is set
   - Verify `nibblebill` application is called

### Balance Not Updating

1. **Check permissions**: Ensure user has `billing_balance_edit` permission
2. **Check database logs**: Look for SQL errors
3. **Verify extension UUID**: Ensure correct extension is selected
4. **Check balance history**: View transaction log for errors

---

## Security Considerations

1. **Permissions**: Restrict `billing_rate_edit` and `billing_balance_edit` to administrators only
2. **Balance Adjustments**: All adjustments are logged in balance history with timestamps
3. **CDR Integration**: Only answered calls (`billsec > 0`) are billed
4. **Rate Matching**: Longest prefix match ensures accurate billing
5. **Transaction Tracking**: Every balance change is recorded with description and user

---

## Future Enhancements

- Invoice generation (PDF/email)
- Payment gateway integration
- Recurring charges (monthly fees)
- Call packages and bundles
- Multi-tier pricing
- Time-of-day/day-of-week rates
- Volume discounts
- SMS billing
- Conference room billing

---

## Support

For issues, questions, or feature requests, please visit:
- FusionPBX Documentation: https://docs.fusionpbx.com
- FusionPBX Community: https://fusionpbx.com/community

---

## License

Mozilla Public License 1.1 (MPL 1.1)

## Contributors

- Mark J Crane <markjcrane@fusionpbx.com>
- GitHub Copilot (AI Assistant)

---

**Version**: 1.0  
**Last Updated**: February 2026
