# FusionPBX Laravel UI

A comprehensive Laravel 11 user interface for FusionPBX that integrates directly with the existing FusionPBX PostgreSQL database. This modern UI provides advanced call center operations, SMS/voice broadcasting with AI capabilities, and intuitive management tools.

## Features

### Core Management
- **User Management** - Full CRUD operations for FusionPBX users
- **Extension Management** - Create, edit, and manage extensions with user assignments
- **Domain Management** - Multi-tenant domain configuration
- **Dashboard** - Real-time statistics and system overview

### Call Center Operations
- **Queue Management** - Configure call center queues with strategies, MOH, tier rules
- **Agent Management** - Manage agents with status updates (Available, On Break, Logged Out)
- **Tier Management** - Assign agents to queues with levels and positions
- **Real-time Statistics** - Monitor active queues and agent performance

### Broadcast Campaigns
- **Campaign Types** - Predictive, Progressive, Preview, Manual
- **Broadcast Options** - Voice Only, SMS Only, Voice + SMS
- **AI Integration** - Support for OpenAI, Anthropic, Google AI
- **Contact Management** - CSV import, bulk contact handling
- **Progress Tracking** - Real-time campaign statistics and call logs

### Call Detail Records (CDR)
- **Advanced Filtering** - Filter by date range, extension, direction, status
- **Call Details** - View comprehensive call information
- **Export** - Export CDR data to CSV
- **Recording Playback** - Built-in audio player for call recordings

### AI Capabilities
- **Multiple Providers** - OpenAI (GPT-4), Anthropic (Claude-3), Google (Gemini)
- **Custom Prompts** - Configure AI conversation scripts
- **Conversation Logs** - Store and review AI interactions
- **Data Extraction** - Extract structured data from conversations

## Technology Stack

- **Framework**: Laravel 11
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Database**: PostgreSQL (existing FusionPBX database)
- **Build Tools**: Vite
- **PHP Version**: 8.2+

## Requirements

- PHP 8.2 or higher
- PostgreSQL 12+
- Composer
- Node.js 18+ and NPM
- Nginx or Apache
- Existing FusionPBX installation

## Installation

For detailed installation instructions, see [INSTALLATION.md](INSTALLATION.md).

For quick setup, see [QUICKSTART.md](QUICKSTART.md).

### Quick Overview

```bash
# Clone the repository
cd /var/www
git clone https://github.com/your-repo/fusionpbx.git fusionpbx-laravel

# Navigate to Laravel UI directory
cd fusionpbx-laravel/laravel-ui

# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Edit .env with your FusionPBX database credentials
nano .env

# Run migrations (only creates new campaign tables)
php artisan migrate

# Build assets
npm run build

# Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Configure Nginx (see nginx-laravel-port.conf)
# Access at http://your-server:8080
```

## Database Integration

This application connects directly to your existing FusionPBX PostgreSQL database. It uses:

- **Existing Tables**: All FusionPBX tables with `v_` prefix
- **New Tables**: Only creates campaign-related tables (v_campaigns, v_campaign_contacts, v_campaign_calls)
- **UUID Primary Keys**: Maintains FusionPBX's UUID-based architecture
- **Naming Conventions**: Follows FusionPBX standards (insert_date, update_date, etc.)

## Directory Structure

```
laravel-ui/
├── app/
│   ├── Http/Controllers/     # All CRUD controllers
│   ├── Models/               # Eloquent models for FusionPBX tables
│   └── Services/             # AI service integration
├── database/
│   └── migrations/           # Migrations for new campaign tables
├── resources/
│   └── views/                # Blade templates with Tailwind CSS
├── routes/
│   └── web.php              # Application routes
├── nginx-laravel-port.conf   # Nginx config for port 8080
├── nginx-laravel-subdomain.conf  # Nginx config for subdomain
└── .env.example             # Environment configuration template
```

## Configuration

### Environment Variables

Key environment variables in `.env`:

```env
# Application
APP_NAME="FusionPBX Laravel UI"
APP_URL=http://your-server:8080

# Database (FusionPBX)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=fusionpbx
DB_USERNAME=fusionpbx
DB_PASSWORD=your_password

# AI Providers (optional)
OPENAI_API_KEY=sk-...
ANTHROPIC_API_KEY=sk-ant-...
GOOGLE_AI_API_KEY=...
```

### Nginx Configuration

Two configuration options are provided:

1. **Port 8080** - Run alongside FusionPBX
2. **Subdomain** - Use a separate subdomain with SSL

See configuration files in the root directory.

## Usage

### Creating a Campaign

1. Navigate to **Campaigns** → **Create Campaign**
2. Fill in campaign details:
   - Name and type (Predictive/Progressive/Preview/Manual)
   - Broadcast type (Voice/SMS/Both)
   - Enable AI if needed and configure provider/model
   - Set voice message or SMS template
   - Configure caller ID and schedule
3. Import contacts via CSV
4. Start the campaign

### Managing Call Center

1. **Create Queues** - Define queue strategy, MOH, timeout settings
2. **Add Agents** - Create agents linked to extensions
3. **Assign Tiers** - Link agents to queues with priority levels
4. **Monitor** - View real-time queue and agent status

### Viewing CDR

1. Navigate to **CDR**
2. Apply filters (date range, extension, direction)
3. Click on a call to view details
4. Export data to CSV if needed

## Security

- **SQL Injection Protection** - Laravel's Eloquent ORM and parameter binding
- **CSRF Protection** - Laravel's built-in CSRF middleware
- **XSS Prevention** - Blade template escaping
- **Secure Headers** - Configured in Nginx
- **Input Validation** - Request validation on all forms
- **No Billing Code** - Intentionally excluded for security

## Performance

- **Database Indexing** - Proper indexes on foreign keys and search fields
- **Eager Loading** - Optimized relationship queries
- **Pagination** - 15 items per page for large datasets
- **Caching** - Laravel's caching for frequently accessed data
- **Asset Optimization** - Vite builds minified CSS/JS

## Support

- **Documentation**: [docs/](docs/)
- **Issues**: GitHub Issues
- **FusionPBX Forum**: https://www.fusionpbx.com

## License

This project follows FusionPBX's licensing terms.

## Contributing

Contributions are welcome! Please follow Laravel and FusionPBX coding standards.

## Credits

- Built on [Laravel 11](https://laravel.com)
- Integrates with [FusionPBX](https://www.fusionpbx.com)
- Styled with [Tailwind CSS](https://tailwindcss.com)

---

**Note**: This UI does NOT include billing, balance management, top-up packages, payment processing, or invoice generation. It focuses purely on call center operations and campaign management.
