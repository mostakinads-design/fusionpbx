# FusionPBX Optional Modules

FusionPBX has a modular architecture that allows you to extend its functionality with optional modules. Some modules are included in the core repository, while others are maintained separately as add-ons.

## Optional Add-on Modules

### Billing Module

The billing module is an optional add-on for FusionPBX that provides telephony post-payment billing capabilities. This module is **not included** in the core FusionPBX repository and must be downloaded and installed separately.

#### How to Download the Billing Module

The billing module is maintained in a separate repository. Here are the available options:

**Option 1: FUSIONPBX-BILLING by comdif (Recommended)**
- Repository: https://github.com/comdif/FUSIONPBX-BILLING
- Description: Telephony post payment billing system for the multi-tenant FusionPBX IPBX
- License: Check the repository for license details

To download and install:

```bash
# Clone the billing repository
cd /tmp
git clone https://github.com/comdif/FUSIONPBX-BILLING.git

# Copy the billing module to your FusionPBX installation
# Assuming FusionPBX is installed in /var/www/fusionpbx
sudo cp -r FUSIONPBX-BILLING/billing /var/www/fusionpbx/app/

# Set proper permissions (adjust user/group as needed)
sudo chown -R www-data:www-data /var/www/fusionpbx/app/billing
sudo chmod -R 755 /var/www/fusionpbx/app/billing
```

**Option 2: Download as ZIP**
1. Go to https://github.com/comdif/FUSIONPBX-BILLING
2. Click the green "Code" button
3. Select "Download ZIP"
4. Extract the ZIP file
5. Copy the `billing` folder to your FusionPBX `app` directory

#### Testing the Billing Module

After installation:

1. Log in to your FusionPBX web interface as an administrator
2. Navigate to **Advanced → Upgrade**
3. Click on **App Defaults** to register the billing module
4. The billing module should now appear in your menu

#### Module Detection in Code

FusionPBX code checks for the billing module presence using:

```php
if (file_exists(dirname(__DIR__, 2)."/app/billing/app_config.php")) {
    // Billing module is installed
}
```

You can find this check in files like:
- `app/call_broadcast/call_broadcast_edit.php`
- `app/extensions/extension_edit.php` (references billing in language files)

## Other Third-Party Modules

### Domain Statistics
- Repository: https://github.com/AccelerateNetworks/Domain-Statistics
- Description: Statistics about FusionPBX Customer Domains, helpful for billing and accounting

## Installing Optional Modules

General steps for installing optional FusionPBX modules:

1. **Download the module** from its repository
2. **Place the module** in the appropriate directory:
   - Most modules go in the `app/` directory
   - Some may go in `resources/` or other locations (check module documentation)
3. **Set permissions** to match your web server user (typically `www-data`)
4. **Register the module** by going to Advanced → Upgrade → App Defaults in the FusionPBX web interface
5. **Clear cache** if necessary (Advanced → Upgrade → Menu Defaults)
6. **Check permissions** to ensure users have access to the new module features

## Developing Your Own Modules

If you're developing your own FusionPBX module, refer to the existing modules in the `app/` directory as examples. Each module typically includes:

- `app_config.php` - Module configuration and database schema
- `app_menu.php` - Menu items for the module
- `app_languages.php` - Multi-language support strings
- `app_defaults.php` - Default settings (optional)
- PHP files for the module's functionality
- `resources/` directory for classes, images, etc.

## Support and Documentation

- Official Documentation: https://docs.fusionpbx.com
- Community Support: https://www.fusionpbx.com
- YouTube Channel: https://www.youtube.com/FusionPBX
- GitHub Issues: Report issues in the respective module's repository

## Important Notes

- Always backup your FusionPBX installation before installing optional modules
- Verify module compatibility with your FusionPBX version
- Some modules may require additional database changes or dependencies
- Review the security and licensing implications of third-party modules
- Test modules in a development environment before deploying to production
