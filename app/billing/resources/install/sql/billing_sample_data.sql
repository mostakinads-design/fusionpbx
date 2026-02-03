-- FusionPBX Billing Module - Sample Data
--
-- This SQL file creates sample rate plans and test data
-- for quickly setting up and testing the billing module
--
-- Installation:
-- PostgreSQL: psql -U fusionpbx fusionpbx < billing_sample_data.sql
-- MySQL: mysql -u fusionpbx -p fusionpbx < billing_sample_data.sql
--
-- NOTE: Replace UUIDs with actual values from your system

-- ============================================================
-- Sample Rate Plans
-- ============================================================

-- US Domestic Rate
INSERT INTO v_billing_rates (
    billing_rate_uuid,
    domain_uuid,
    rate_name,
    rate_description,
    destination_prefix,
    rate_per_minute,
    billing_increment,
    minimum_duration,
    connection_fee,
    currency,
    enabled,
    insert_date,
    insert_user
) VALUES (
    'a1b2c3d4-e5f6-4a5b-8c9d-0e1f2a3b4c5d',
    NULL, -- NULL for system-wide rates, or specify domain_uuid
    'US Domestic',
    'US and Canada calls',
    '1',
    0.0100,
    60,
    0,
    0.0000,
    'USD',
    true,
    NOW(),
    NULL
);

-- UK International Rate
INSERT INTO v_billing_rates (
    billing_rate_uuid,
    domain_uuid,
    rate_name,
    rate_description,
    destination_prefix,
    rate_per_minute,
    billing_increment,
    minimum_duration,
    connection_fee,
    currency,
    enabled,
    insert_date,
    insert_user
) VALUES (
    'b2c3d4e5-f6a7-5b6c-9d0e-1f2a3b4c5d6e',
    NULL,
    'UK International',
    'United Kingdom calls',
    '44',
    0.0500,
    60,
    0,
    0.0000,
    'USD',
    true,
    NOW(),
    NULL
);

-- India International Rate
INSERT INTO v_billing_rates (
    billing_rate_uuid,
    domain_uuid,
    rate_name,
    rate_description,
    destination_prefix,
    rate_per_minute,
    billing_increment,
    minimum_duration,
    connection_fee,
    currency,
    enabled,
    insert_date,
    insert_user
) VALUES (
    'c3d4e5f6-a7b8-6c7d-0e1f-2a3b4c5d6e7f',
    NULL,
    'India International',
    'India calls',
    '91',
    0.0300,
    60,
    0,
    0.0000,
    'USD',
    true,
    NOW(),
    NULL
);

-- Premium Rate (Toll-Free)
INSERT INTO v_billing_rates (
    billing_rate_uuid,
    domain_uuid,
    rate_name,
    rate_description,
    destination_prefix,
    rate_per_minute,
    billing_increment,
    minimum_duration,
    connection_fee,
    currency,
    enabled,
    insert_date,
    insert_user
) VALUES (
    'd4e5f6a7-b8c9-7d8e-1f2a-3b4c5d6e7f8a',
    NULL,
    'Toll-Free Numbers',
    'US toll-free 1-800, 1-888, etc.',
    '1800',
    0.0200,
    60,
    0,
    0.0000,
    'USD',
    true,
    NOW(),
    NULL
);

-- Local Rate (Lower cost)
INSERT INTO v_billing_rates (
    billing_rate_uuid,
    domain_uuid,
    rate_name,
    rate_description,
    destination_prefix,
    rate_per_minute,
    billing_increment,
    minimum_duration,
    connection_fee,
    currency,
    enabled,
    insert_date,
    insert_user
) VALUES (
    'e5f6a7b8-c9d0-8e9f-2a3b-4c5d6e7f8a9b',
    NULL,
    'Local Calls',
    'Local area code calls',
    '415',
    0.0050,
    60,
    0,
    0.0000,
    'USD',
    true,
    NOW(),
    NULL
);

-- ============================================================
-- Sample Balance Records
-- ============================================================
-- 
-- NOTE: You need to replace 'YOUR_DOMAIN_UUID' and 'YOUR_EXTENSION_UUID'
--       with actual UUIDs from your FusionPBX installation
--
-- To get extension UUIDs:
-- SELECT extension_uuid, extension, effective_caller_id_name 
-- FROM v_extensions 
-- WHERE domain_uuid = 'YOUR_DOMAIN_UUID';

-- Example balance for extension 1001
/*
INSERT INTO v_billing_balances (
    billing_balance_uuid,
    domain_uuid,
    extension_uuid,
    balance,
    currency,
    low_balance_alert,
    low_balance_threshold,
    last_updated
) VALUES (
    'f6a7b8c9-d0e1-9f0a-3b4c-5d6e7f8a9b0c',
    'YOUR_DOMAIN_UUID',
    'YOUR_EXTENSION_UUID_FOR_1001',
    10.0000,
    'USD',
    true,
    5.0000,
    NOW()
);
*/

-- ============================================================
-- Sample User Rate Assignments
-- ============================================================
-- 
-- Assign rates to users (multiple rates per user supported)
-- 
-- NOTE: Replace UUIDs with actual values

-- Assign US Domestic rate to extension
/*
INSERT INTO v_billing_user_rates (
    billing_user_rate_uuid,
    domain_uuid,
    extension_uuid,
    billing_rate_uuid,
    enabled,
    insert_date
) VALUES (
    'a7b8c9d0-e1f2-0a1b-4c5d-6e7f8a9b0c1d',
    'YOUR_DOMAIN_UUID',
    'YOUR_EXTENSION_UUID',
    'a1b2c3d4-e5f6-4a5b-8c9d-0e1f2a3b4c5d', -- US Domestic rate
    true,
    NOW()
);
*/

-- Assign UK rate to extension
/*
INSERT INTO v_billing_user_rates (
    billing_user_rate_uuid,
    domain_uuid,
    extension_uuid,
    billing_rate_uuid,
    enabled,
    insert_date
) VALUES (
    'b8c9d0e1-f2a3-1b2c-5d6e-7f8a9b0c1d2e',
    'YOUR_DOMAIN_UUID',
    'YOUR_EXTENSION_UUID',
    'b2c3d4e5-f6a7-5b6c-9d0e-1f2a3b4c5d6e', -- UK rate
    true,
    NOW()
);
*/

-- ============================================================
-- Helper Queries
-- ============================================================

-- Get all rate plans
-- SELECT * FROM v_billing_rates WHERE enabled = true ORDER BY destination_prefix;

-- Get all balances
-- SELECT e.extension, b.balance, b.currency, b.last_updated
-- FROM v_billing_balances b
-- JOIN v_extensions e ON b.extension_uuid = e.extension_uuid
-- ORDER BY e.extension;

-- Get user rate assignments
-- SELECT e.extension, r.rate_name, r.destination_prefix, r.rate_per_minute
-- FROM v_billing_user_rates ur
-- JOIN v_extensions e ON ur.extension_uuid = e.extension_uuid
-- JOIN v_billing_rates r ON ur.billing_rate_uuid = r.billing_rate_uuid
-- WHERE ur.enabled = true
-- ORDER BY e.extension, r.destination_prefix;

-- Get usage for an extension
-- SELECT destination_number, duration, cost, currency, call_date
-- FROM v_billing_usage
-- WHERE extension_uuid = 'YOUR_EXTENSION_UUID'
-- ORDER BY call_date DESC
-- LIMIT 10;
