# CGRates Integration Guide

This document explains how to integrate CGRates with the FusionPBX Laravel UI for billing and rating functionality.

## Overview

The FusionPBX Laravel UI **does not include** any internal billing module. Instead, we recommend using **CGRates** - a professional, open-source real-time charging system that integrates seamlessly with FusionPBX and FreeSWITCH.

## Why CGRates?

- **Real-time Rating**: Rate calls as they happen
- **Multi-tenant**: Perfect for FusionPBX's multi-domain architecture
- **Flexible Rating Plans**: Support for various charging schemes
- **CDR Processing**: Integrates with FusionPBX CDR data
- **RESTful API**: Easy integration with Laravel
- **Open Source**: Free and actively maintained

## Architecture

```
┌─────────────────┐     ┌──────────────┐     ┌─────────────────┐
│   FreeSWITCH    │────▶│   CGRates    │◀────│  Laravel UI     │
│   (Call Events) │     │   (Rating)   │     │  (Management)   │
└─────────────────┘     └──────────────┘     └─────────────────┘
         │                      │                      │
         └──────────────────────┴──────────────────────┘
                                │
                         ┌──────▼──────┐
                         │  PostgreSQL  │
                         │  (FusionPBX) │
                         └──────────────┘
```

## Installation

### 1. Install CGRates

```bash
# Add CGRates repository (Debian/Ubuntu)
wget -O - https://apt.cgrates.org/apt.cgrates.org.gpg.key | sudo apt-key add -
echo "deb https://apt.cgrates.org/debian/ nightly main" | sudo tee /etc/apt/sources.list.d/cgrates.list

# Update and install
sudo apt-get update
sudo apt-get install cgrates

# Or build from source
cd /usr/src
git clone https://github.com/cgrates/cgrates.git
cd cgrates
./build.sh
```

### 2. Configure CGRates with PostgreSQL

Edit `/etc/cgrates/cgrates.json`:

```json
{
  "data_db": {
    "db_type": "postgres",
    "db_host": "127.0.0.1",
    "db_port": 5432,
    "db_name": "cgrates",
    "db_user": "cgrates",
    "db_password": "your_cgrates_password"
  },
  "stor_db": {
    "db_type": "postgres",
    "db_host": "127.0.0.1",
    "db_port": 5432,
    "db_name": "cgrates",
    "db_user": "cgrates",
    "db_password": "your_cgrates_password"
  },
  "rals": {
    "enabled": true,
    "thresholds_conns": ["*internal"]
  },
  "cdrs": {
    "enabled": true,
    "chargers_conns": ["*internal"],
    "rals_conns": ["*internal"],
    "attributes_conns": ["*internal"],
    "stats_conns": ["*internal"],
    "thresholds_conns": ["*internal"]
  },
  "sessions": {
    "enabled": true,
    "resources_conns": ["*internal"],
    "routes_conns": ["*internal"],
    "attributes_conns": ["*internal"],
    "rals_conns": ["*internal"],
    "cdrs_conns": ["*internal"],
    "chargers_conns": ["*internal"]
  },
  "http": {
    "json_rpc_url": "/jsonrpc",
    "http_Cdrs": "/cdr_http",
    "use_basic_auth": false,
    "auth_users": {}
  }
}
```

### 3. Create CGRates Database

```bash
# Create PostgreSQL database for CGRates
sudo -u postgres psql
CREATE DATABASE cgrates;
CREATE USER cgrates WITH PASSWORD 'your_cgrates_password';
GRANT ALL PRIVILEGES ON DATABASE cgrates TO cgrates;
\q

# Initialize CGRates database
cgr-migrator -exec=*set_versions -config_path=/etc/cgrates
cgr-migrator -exec=*stordb -config_path=/etc/cgrates
```

### 4. Start CGRates

```bash
sudo systemctl enable cgrates
sudo systemctl start cgrates
sudo systemctl status cgrates
```

## FreeSWITCH Integration

### 1. Configure FreeSWITCH to Send Events to CGRates

Edit `/etc/freeswitch/autoload_configs/event_socket.conf.xml`:

```xml
<configuration name="event_socket.conf" description="Socket Client">
  <settings>
    <param name="nat-map" value="false"/>
    <param name="listen-ip" value="127.0.0.1"/>
    <param name="listen-port" value="8021"/>
    <param name="password" value="ClueCon"/>
  </settings>
</configuration>
```

### 2. Configure CGRates FreeSWITCH Connection

Add to `/etc/cgrates/cgrates.json`:

```json
{
  "freeswitch_agent": {
    "enabled": true,
    "sessions_conns": ["*internal"],
    "subscribe_park": true,
    "create_cdr": true,
    "event_socket_conns": [
      {
        "address": "127.0.0.1:8021",
        "password": "ClueCon",
        "reconnects": 5
      }
    ]
  }
}
```

Restart services:

```bash
sudo systemctl restart cgrates
sudo systemctl restart freeswitch
```

## Laravel UI Integration

### 1. Add CGRates Configuration to .env

```env
# CGRates Configuration
CGRATES_ENABLED=true
CGRATES_HOST=127.0.0.1
CGRATES_PORT=2080
CGRATES_API_URL=http://127.0.0.1:2080/jsonrpc
```

### 2. Create CGRates Service Class

Create `app/Services/CgratesService.php`:

```php
<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CgratesService
{
    protected $apiUrl;
    protected $enabled;

    public function __construct()
    {
        $this->apiUrl = env('CGRATES_API_URL', 'http://127.0.0.1:2080/jsonrpc');
        $this->enabled = env('CGRATES_ENABLED', false);
    }

    /**
     * Make a JSON-RPC call to CGRates
     */
    protected function call($method, $params = [])
    {
        if (!$this->enabled) {
            return ['error' => 'CGRates is not enabled'];
        }

        try {
            $response = Http::post($this->apiUrl, [
                'method' => $method,
                'params' => [$params],
                'id' => time(),
            ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('CGRates API Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get account balance
     */
    public function getBalance($account)
    {
        return $this->call('ApierV1.GetAccount', [
            'Tenant' => 'cgrates.org',
            'Account' => $account,
        ]);
    }

    /**
     * Set account balance
     */
    public function setBalance($account, $balance)
    {
        return $this->call('ApierV2.SetBalance', [
            'Tenant' => 'cgrates.org',
            'Account' => $account,
            'BalanceType' => '*monetary',
            'Value' => $balance,
            'Balance' => [
                'ID' => $account . '_balance',
                'Value' => $balance,
            ],
        ]);
    }

    /**
     * Get CDRs from CGRates
     */
    public function getCDRs($filters = [])
    {
        return $this->call('ApierV2.GetCDRs', $filters);
    }

    /**
     * Process CDR
     */
    public function processCDR($cdr)
    {
        return $this->call('CdrsV1.ProcessCDR', $cdr);
    }

    /**
     * Get rating plans
     */
    public function getRatingPlans()
    {
        return $this->call('ApierV1.GetRatingPlanIDs', [
            'Tenant' => 'cgrates.org',
        ]);
    }

    /**
     * Set rating plan for account
     */
    public function setRatingPlan($account, $ratingPlan)
    {
        return $this->call('ApierV1.SetAccount', [
            'Tenant' => 'cgrates.org',
            'Account' => $account,
            'ActionPlanIDs' => [$ratingPlan],
        ]);
    }

    /**
     * Debit account for campaign
     */
    public function debitAccount($account, $amount, $description = '')
    {
        return $this->call('ApierV1.DebitBalance', [
            'Tenant' => 'cgrates.org',
            'Account' => $account,
            'BalanceType' => '*monetary',
            'Value' => $amount,
            'ExtraData' => $description,
        ]);
    }

    /**
     * Check if account can afford a call
     */
    public function canCall($account, $destination, $duration = 60)
    {
        $result = $this->call('Responder.GetCost', [
            'Tenant' => 'cgrates.org',
            'Account' => $account,
            'Destination' => $destination,
            'AnswerTime' => date('Y-m-d H:i:s'),
            'Usage' => $duration . 's',
        ]);

        return isset($result['result']) && $result['result']['Cost'] > 0;
    }
}
```

### 3. Add CGRates Routes

Add to `routes/web.php`:

```php
// CGRates Integration Routes
Route::prefix('billing')->group(function () {
    Route::get('/', [BillingController::class, 'index'])->name('billing.index');
    Route::get('/balance/{account}', [BillingController::class, 'getBalance'])->name('billing.balance');
    Route::post('/balance/{account}', [BillingController::class, 'setBalance'])->name('billing.set-balance');
    Route::get('/cdrs', [BillingController::class, 'cdrs'])->name('billing.cdrs');
    Route::get('/rating-plans', [BillingController::class, 'ratingPlans'])->name('billing.rating-plans');
});
```

### 4. Create Billing Controller

Create `app/Http/Controllers/BillingController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Services\CgratesService;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    protected $cgrates;

    public function __construct(CgratesService $cgrates)
    {
        $this->cgrates = $cgrates;
    }

    public function index()
    {
        return view('billing.index');
    }

    public function getBalance($account)
    {
        $balance = $this->cgrates->getBalance($account);
        return response()->json($balance);
    }

    public function setBalance(Request $request, $account)
    {
        $validated = $request->validate([
            'balance' => 'required|numeric|min:0',
        ]);

        $result = $this->cgrates->setBalance($account, $validated['balance']);
        
        if (isset($result['error'])) {
            return back()->with('error', 'Failed to set balance: ' . $result['error']);
        }

        return back()->with('success', 'Balance updated successfully');
    }

    public function cdrs(Request $request)
    {
        $filters = $request->only(['account', 'destination', 'start_date', 'end_date']);
        $cdrs = $this->cgrates->getCDRs($filters);
        
        return view('billing.cdrs', compact('cdrs'));
    }

    public function ratingPlans()
    {
        $plans = $this->cgrates->getRatingPlans();
        return view('billing.rating-plans', compact('plans'));
    }
}
```

## Usage Examples

### Check Balance Before Campaign

```php
use App\Services\CgratesService;

$cgrates = new CgratesService();

// Before starting campaign
$campaign = Campaign::find($id);
$accountId = $campaign->domain->domain_name; // or extension

$balance = $cgrates->getBalance($accountId);

if ($balance['result']['BalanceMap']['*monetary'][0]['Value'] < 100) {
    return back()->with('error', 'Insufficient balance to start campaign');
}
```

### Debit for Campaign Calls

```php
// After campaign call
$cost = 0.05; // Calculate based on duration and rate

$cgrates->debitAccount(
    $accountId,
    $cost,
    "Campaign call to {$contact->contact_phone}"
);
```

### Real-time Balance Display

In your blade views:

```blade
<div class="balance-widget">
    <h3>Account Balance</h3>
    <div id="balance" data-account="{{ $account }}">
        Loading...
    </div>
</div>

<script>
async function updateBalance() {
    const account = document.getElementById('balance').dataset.account;
    const response = await fetch(`/billing/balance/${account}`);
    const data = await response.json();
    
    if (data.result) {
        const balance = data.result.BalanceMap['*monetary'][0].Value;
        document.getElementById('balance').innerHTML = `$${balance.toFixed(2)}`;
    }
}

updateBalance();
setInterval(updateBalance, 30000); // Update every 30 seconds
</script>
```

## Rating Plans Configuration

### Basic Rating Plan Example

```bash
# Create a basic rating plan
cgr-console 'ApierV1.SetTPRatingPlan({"TPid":"RATING_PLAN_1","ID":"RP_RETAIL","RatingPlanBindings":[{"DestinationRatesId":"DR_RETAIL","TimingId":"*any","Weight":10}]})'

# Set destination rates
cgr-console 'ApierV1.SetTPDestinationRate({"TPid":"RATING_PLAN_1","ID":"DR_RETAIL","DestinationRatesId":"DR_RETAIL_1","RatesId":"RT1","RoundingMethod":"*up","RoundingDecimals":4,"MaxCost":0,"MaxCostStrategy":""})'

# Set rates
cgr-console 'ApierV1.SetTPRate({"TPid":"RATING_PLAN_1","ID":"RT1","RateSlots":[{"ConnectFee":0,"Rate":0.05,"RateUnit":"60s","RateIncrement":"60s","GroupIntervalStart":"0s"}]})'
```

## Monitoring and Maintenance

### Check CGRates Status

```bash
sudo systemctl status cgrates
cgr-console 'Status()'
```

### View CGRates Logs

```bash
sudo tail -f /var/log/cgrates/cgrates.log
```

### Test CGRates API

```bash
# Test connection
curl -X POST http://127.0.0.1:2080/jsonrpc \
  -H "Content-Type: application/json" \
  -d '{
    "method": "ApierV1.Ping",
    "params": [],
    "id": 1
  }'
```

## Troubleshooting

### CGRates Not Starting

```bash
# Check configuration
cgr-loader -config_path=/etc/cgrates -validate

# Check PostgreSQL connection
psql -U cgrates -d cgrates -h 127.0.0.1
```

### FreeSWITCH Not Connecting

```bash
# Check event socket
fs_cli -x "event_socket status"

# Check CGRates connection
cgr-console 'SessionSv1.GetActiveSessions({})'
```

## Additional Resources

- **CGRates Documentation**: https://cgrates.readthedocs.io/
- **CGRates GitHub**: https://github.com/cgrates/cgrates
- **CGRates Forum**: https://groups.google.com/g/cgrates
- **FusionPBX + CGRates**: https://docs.fusionpbx.com/en/latest/applications/call_center.html

## Security Considerations

1. **Firewall**: Restrict CGRates API port (2080) to localhost or trusted IPs
2. **Authentication**: Enable basic auth in CGRates configuration
3. **Database**: Use strong passwords for CGRates database
4. **SSL/TLS**: Use HTTPS for API calls in production
5. **Rate Limiting**: Implement rate limiting on billing endpoints

## Conclusion

By using CGRates for billing instead of a built-in module, you get:
- Professional-grade billing system
- Separation of concerns (billing vs UI)
- Better scalability
- Active community support
- Regular updates and security patches

The Laravel UI focuses on call center management and campaigns, while CGRates handles all billing operations efficiently and reliably.
