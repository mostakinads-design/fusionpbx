# FusionPBX Application Modules

This directory contains the core application modules for FusionPBX. Each subdirectory represents a separate module that provides specific functionality.

## Core Modules

The modules included in this directory are part of the core FusionPBX installation and provide essential PBX features such as:

- **Extensions** - Manage SIP extensions and users
- **Dialplans** - Configure call routing and dialplan logic
- **Conferences** - Conference bridge management
- **Call Centers** - Call center queue and agent management
- **Voicemail** - Voicemail system
- **Call Recordings** - Call recording management
- **IVR Menus** - Interactive Voice Response menus
- And many more...

## Optional Add-on Modules

Some modules are **not included** in the core repository and must be downloaded separately. These include:

### Billing Module

The billing module provides advanced telephony billing capabilities but is maintained as a separate add-on.

**To download and install the billing module:**

See the [MODULES.md](../MODULES.md) file in the root directory for complete instructions on downloading and installing optional modules like billing.

Quick reference:
```bash
# Clone the billing module
git clone https://github.com/comdif/FUSIONPBX-BILLING.git

# Copy to app directory
sudo cp -r FUSIONPBX-BILLING/billing /path/to/fusionpbx/app/

# Set permissions
sudo chown -R www-data:www-data /path/to/fusionpbx/app/billing
```

## Module Structure

Each module typically follows this structure:

```
app/module_name/
├── app_config.php         # Module configuration and database schema
├── app_menu.php          # Menu items
├── app_languages.php     # Translation strings
├── app_defaults.php      # Default settings (optional)
├── *.php                 # Module functionality files
└── resources/           # Assets, classes, images, etc.
    ├── classes/
    └── images/
```

## Installing New Modules

After adding a new module to this directory:

1. Set proper file permissions to match your web server user
2. Log in to FusionPBX as an administrator
3. Go to **Advanced → Upgrade**
4. Click **App Defaults** to register the new module
5. Click **Menu Defaults** to update the menu system
6. Check **Advanced → Group Manager** to configure permissions

## More Information

- Full documentation on optional modules: [MODULES.md](../MODULES.md)
- Official FusionPBX documentation: https://docs.fusionpbx.com
- FusionPBX website: https://www.fusionpbx.com
