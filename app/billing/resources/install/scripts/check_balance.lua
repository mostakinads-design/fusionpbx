-- FusionPBX Billing - Check Balance Script
-- 
-- This Lua script checks if a user has sufficient balance before allowing a call
--
-- Installation: Copy to /usr/share/freeswitch/scripts/app/billing/check_balance.lua
--
-- Usage in dialplan:
-- <action application="lua" data="app/billing/check_balance.lua"/>

-- Get session variables
local session = assert(session, "No session available")
local domain_uuid = session:getVariable("domain_uuid") or ""
local user_uuid = session:getVariable("user_uuid") or ""
local destination_number = session:getVariable("destination_number") or ""

-- Minimum balance required to make a call
local minimum_balance = 0.10

freeswitch.consoleLog("INFO", string.format(
    "Billing Balance Check: User %s calling %s\n",
    user_uuid, destination_number
))

-- Database connection
local dbh = freeswitch.Dbh("core:core")

if not dbh:connected() then
    freeswitch.consoleLog("ERROR", "Billing Balance Check: Database connection failed\n")
    session:hangup("FACILITY_REJECTED")
    return
end

-- Find extension_uuid
local extension_uuid = nil
local sql = string.format([[
    SELECT extension_uuid 
    FROM v_extensions 
    WHERE domain_uuid = '%s' 
    AND extension_uuid IN (
        SELECT extension_uuid 
        FROM v_extension_users 
        WHERE user_uuid = '%s'
    ) 
    LIMIT 1
]], domain_uuid, user_uuid)

dbh:query(sql, function(row)
    extension_uuid = row.extension_uuid
end)

if not extension_uuid then
    freeswitch.consoleLog("WARNING", "Billing Balance Check: No extension found for user\n")
    session:hangup("FACILITY_REJECTED")
    return
end

-- Get current balance
local balance = nil
local sql = string.format([[
    SELECT balance 
    FROM v_billing_balances 
    WHERE domain_uuid = '%s' 
    AND extension_uuid = '%s'
]], domain_uuid, extension_uuid)

dbh:query(sql, function(row)
    balance = tonumber(row.balance)
end)

if not balance then
    freeswitch.consoleLog("WARNING", "Billing Balance Check: No balance record found\n")
    session:answer()
    session:sleep(500)
    session:streamFile("ivr/ivr-no_account_found.wav")
    session:hangup("FACILITY_REJECTED")
    return
end

-- Check if balance is sufficient
if balance < minimum_balance then
    freeswitch.consoleLog("WARNING", string.format(
        "Billing Balance Check: Insufficient balance %.2f (minimum %.2f)\n",
        balance, minimum_balance
    ))
    
    session:answer()
    session:sleep(500)
    session:streamFile("ivr/ivr-insufficient_funds.wav")
    session:hangup("CALL_REJECTED")
    return
end

-- Balance is sufficient, set variables for nibblebill
session:setVariable("nibble_account", extension_uuid)
session:setVariable("nibble_current_balance", tostring(balance))

freeswitch.consoleLog("INFO", string.format(
    "Billing Balance Check: Sufficient balance %.2f, call allowed\n",
    balance
))
