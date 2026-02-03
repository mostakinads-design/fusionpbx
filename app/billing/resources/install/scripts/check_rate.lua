-- FusionPBX Billing - Rate Lookup Script
-- 
-- This Lua script looks up the billing rate for a destination number
-- from the FusionPBX billing_rates table
--
-- Installation: Copy to /usr/share/freeswitch/scripts/app/billing/check_rate.lua
--
-- Usage in dialplan:
-- <action application="set" data="billing_rate=${lua(app/billing/check_rate.lua ${destination_number} ${domain_uuid} ${user_uuid})}"/>

-- Get parameters
local destination_number = argv[1] or ""
local domain_uuid = argv[2] or ""
local user_uuid = argv[3] or ""

-- Remove any non-digit characters from destination
destination_number = destination_number:gsub("[^0-9]", "")

-- Default rate if nothing found
local default_rate = 0.01

-- Log the lookup attempt
freeswitch.consoleLog("INFO", "Billing Rate Lookup: Checking rate for " .. destination_number .. "\n")

-- Database connection
local dbh = freeswitch.Dbh("core:core")

if not dbh:connected() then
    freeswitch.consoleLog("ERROR", "Billing Rate Lookup: Database connection failed\n")
    return default_rate
end

-- Find the extension_uuid from user_uuid
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
    freeswitch.consoleLog("WARNING", "Billing Rate Lookup: No extension found for user " .. user_uuid .. "\n")
    return default_rate
end

-- Find matching rate (longest prefix match)
local sql = string.format([[
    SELECT r.rate_per_minute, r.billing_increment, r.connection_fee, r.destination_prefix
    FROM v_billing_rates r
    INNER JOIN v_billing_user_rates ur ON r.billing_rate_uuid = ur.billing_rate_uuid
    WHERE r.domain_uuid = '%s'
    AND ur.extension_uuid = '%s'
    AND r.enabled = 'true'
    AND ur.enabled = 'true'
    AND '%s' LIKE r.destination_prefix || '%%'
    ORDER BY LENGTH(r.destination_prefix) DESC
    LIMIT 1
]], domain_uuid, extension_uuid, destination_number)

local rate_found = false
local rate_per_minute = default_rate

dbh:query(sql, function(row)
    rate_per_minute = tonumber(row.rate_per_minute) or default_rate
    rate_found = true
    
    freeswitch.consoleLog("INFO", string.format(
        "Billing Rate Lookup: Found rate %.4f/min for prefix %s\n",
        rate_per_minute, row.destination_prefix
    ))
end)

if not rate_found then
    freeswitch.consoleLog("WARNING", "Billing Rate Lookup: No rate found for " .. destination_number .. ", using default " .. default_rate .. "\n")
end

-- Return rate per minute
return rate_per_minute
