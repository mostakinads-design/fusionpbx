# FusionPBX Alternatives and Modern Implementations

This document provides information about alternative implementations and modern versions of FusionPBX that may better suit specific use cases.

## FS PBX - Modern Laravel/Vue.js Implementation

### Overview

**FS PBX** is a complete reimplementation of FusionPBX that started as a fork but has been extensively redesigned with modern web technologies. It provides a contemporary user interface and improved development experience while maintaining integration with FreeSWITCH and core FusionPBX features.

- **Repository**: https://github.com/nemerald-voip/fspbx
- **License**: Check the repository for license details
- **Technology Stack**:
  - **Backend**: Laravel (PHP framework)
  - **Frontend**: Vue.js (JavaScript framework)
  - **Styling**: Tailwind CSS
  - **Architecture**: Modern, modular design

### Key Features

- **Modern User Interface**: Clean, responsive design built with Vue.js and Tailwind CSS
- **Enhanced Performance**: Optimized backend infrastructure using Laravel
- **Improved Developer Experience**: Modern development tools and practices
- **Integration with FreeSWITCH**: Maintains core telephony functionality
- **Modular Architecture**: Easier to extend and maintain

### Installation

FS PBX provides a simplified installation process for Debian 12/13:

```bash
# Quick installation via script
wget -O- https://raw.githubusercontent.com/nemerald-voip/fspbx/main/install/install-fspbx.sh | bash
```

**System Requirements**:
- Debian 12 or 13
- 4GB RAM minimum (more recommended for production)
- 30GB hard drive (NVME recommended for production)

### Video Tutorial

A comprehensive 10-minute installation tutorial is available:
- YouTube: https://youtu.be/go6dUce0Nis

### Updating FS PBX

To update an existing FS PBX installation:

```bash
cd /var/www/fspbx
git pull
php artisan app:update

# Check for database migrations
php artisan migrate:status

# Apply migrations if needed
php artisan migrate
```

### Premium Modules

FS PBX offers premium modules for enhanced functionality:

1. **Contact Center Module**: Advanced call management with live dashboard and management portal
2. **STIR/SHAKEN Module**: Call authentication with Attestation A signing capability

### Screenshots and Demo

For visual examples of the FS PBX interface, visit the repository:
https://github.com/nemerald-voip/fspbx#screenshots

## Comparison: FusionPBX vs FS PBX

### FusionPBX (This Repository)

**Advantages**:
- Mature, stable codebase with years of development
- Large community and extensive documentation
- Wide range of built-in modules
- Compatible with existing FusionPBX installations
- Direct PHP implementation without framework overhead
- Extensive third-party module ecosystem

**Best For**:
- Organizations already using FusionPBX
- Users who need maximum stability and community support
- Environments requiring specific legacy features
- Teams comfortable with traditional PHP development

### FS PBX (Modern Alternative)

**Advantages**:
- Modern, responsive user interface
- Contemporary development stack (Laravel + Vue.js)
- Cleaner codebase for developers
- Enhanced user experience
- Modern tooling and development practices
- Easier for developers familiar with Laravel/Vue.js

**Best For**:
- New installations preferring modern UI
- Development teams experienced with Laravel/Vue.js
- Organizations prioritizing user experience
- Projects requiring custom UI development

## Migration Considerations

### Moving from FusionPBX to FS PBX

If you're considering migrating from traditional FusionPBX to FS PBX:

1. **Evaluate Requirements**: Ensure FS PBX supports all features you currently use
2. **Test Environment**: Set up a test installation to verify functionality
3. **Data Migration**: Plan for migrating users, extensions, and configurations
4. **Training**: Prepare users for the new interface
5. **Support**: Ensure you have support resources available

**Important Notes**:
- FS PBX is a separate project with its own development cycle
- Not all FusionPBX features may be implemented in FS PBX
- Check the FS PBX repository for current feature list and roadmap
- Consider maintaining a parallel installation during transition

### Migration Path

There is no automated migration tool between FusionPBX and FS PBX. Migration typically involves:

1. Fresh FS PBX installation
2. Manual configuration of domains and settings
3. Recreation of users and extensions
4. Testing and validation
5. Cutover planning

## Additional Resources

### FusionPBX Resources
- Official Site: https://www.fusionpbx.com
- Documentation: https://docs.fusionpbx.com
- Community Forums: https://www.fusionpbx.com
- YouTube Channel: https://www.youtube.com/FusionPBX

### FS PBX Resources
- GitHub Repository: https://github.com/nemerald-voip/fspbx
- Wiki Documentation: https://github.com/nemerald-voip/fspbx/wiki
- Installation Guide: https://github.com/nemerald-voip/fspbx/wiki/How-to-Secure-FS-PBX-with-a-Let's-Encrypt-SSL-Certificate

## Getting Help

### For FusionPBX
- Report issues at: https://www.fusionpbx.com (account required)
- Community support through official channels
- Commercial support available: https://www.fusionpbx.com/support

### For FS PBX
- GitHub Issues: https://github.com/nemerald-voip/fspbx/issues
- Contact the maintainers through the repository
- Check the repository for support options

## Contributing

Both projects welcome contributions:

- **FusionPBX**: Requires signing a Contributor License Agreement (CLA)
- **FS PBX**: Follow contribution guidelines in the repository

## Conclusion

Both FusionPBX and FS PBX are viable options for telephony systems:

- Choose **FusionPBX** for stability, mature features, and community support
- Choose **FS PBX** for modern UI, contemporary development stack, and enhanced UX

The best choice depends on your specific requirements, team expertise, and priorities. Both systems integrate with FreeSWITCH and provide robust PBX functionality.

For new projects, we recommend evaluating both options in a test environment before making a decision.
