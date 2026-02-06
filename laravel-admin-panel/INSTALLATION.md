# FusionPBX Laravel Admin Panel

A modern, AI-powered admin panel for FusionPBX built with Laravel 12, featuring:

- 🤖 **AI Agent Integration** with OpenAI GPT-4
- 📞 **Modern CDR Viewer** with advanced filtering and real-time updates
- 🎯 **Hybrid Call Routing** (Human/AI/Hybrid modes)
- 📊 **Real-time Dashboard** with statistics and insights
- 💾 **PostgreSQL Integration** with existing FusionPBX database
- 🎨 **Modern UI** with Tailwind CSS and Alpine.js

## Requirements

- Debian 11 or 12
- PHP 8.2 or higher
- PostgreSQL (existing FusionPBX database)
- Composer
- Node.js and NPM (for frontend assets)
- Nginx or Apache web server
- OpenAI API Key

## Installation

### 1. Copy Files to Server

```bash
# Copy the laravel-admin-panel directory to your server
sudo mkdir -p /var/www/admin
sudo cp -r laravel-admin-panel /var/www/admin/user-panel
cd /var/www/admin/user-panel
```

### 2. Set Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/admin/user-panel

# Set directory permissions
sudo find /var/www/admin/user-panel -type d -exec chmod 755 {} \;
sudo find /var/www/admin/user-panel -type f -exec chmod 644 {} \;

# Set storage and cache permissions
sudo chmod -R 775 /var/www/admin/user-panel/storage
sudo chmod -R 775 /var/www/admin/user-panel/bootstrap/cache
```

### 3. Install Dependencies

```bash
cd /var/www/admin/user-panel

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies and build assets
npm install
npm run build
```

### 4. Configure Environment

```bash
# Copy example environment file
cp .env.example .env

# Edit .env file with your settings
nano .env
```

Update the following in your `.env` file:

```env
APP_NAME="FusionPBX Admin Panel"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-server-ip/admin/user-panel

# Database Configuration (from /etc/fusionpbx/config.conf)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=fusionpbx
DB_USERNAME=fusionpbx
DB_PASSWORD=your_fusionpbx_password

# OpenAI Configuration
OPENAI_API_KEY=your_openai_api_key_here
OPENAI_MODEL=gpt-4
OPENAI_MAX_TOKENS=2000

# AI Agent Settings
AI_AGENT_ENABLED=true
AI_AGENT_MODE=hybrid
AI_AGENT_CONFIDENCE_THRESHOLD=0.8

# Paths
FUSIONPBX_PATH=/var/www/fusionpbx
ADMIN_PANEL_PATH=/var/www/admin/user-panel
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Configure Web Server

#### For Nginx

Create `/etc/nginx/sites-available/admin-panel`:

```nginx
server {
    listen 80;
    server_name your-server-ip;
    root /var/www/admin/user-panel/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location /admin/user-panel {
        alias /var/www/admin/user-panel/public;
        try_files $uri $uri/ @laravel;

        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $request_filename;
        }
    }

    location @laravel {
        rewrite /admin/user-panel/(.*)$ /admin/user-panel/index.php?/$1 last;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/admin-panel /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### For Apache

Create `/etc/apache2/sites-available/admin-panel.conf`:

```apache
Alias /admin/user-panel /var/www/admin/user-panel/public

<Directory /var/www/admin/user-panel/public>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted

    <IfModule mod_rewrite.c>
        RewriteEngine On
        RewriteBase /admin/user-panel
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^ index.php [L]
    </IfModule>
</Directory>
```

Enable the site and required modules:

```bash
sudo a2enmod rewrite
sudo a2ensite admin-panel
sudo systemctl reload apache2
```

### 7. Optimize Laravel

```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear any existing cache
php artisan cache:clear
```

## Usage

### Access the Admin Panel

Open your browser and navigate to:
```
http://your-server-ip/admin/user-panel
```

### Features

#### Dashboard
- View real-time call statistics
- Monitor active extensions
- See recent calls with detailed information
- Auto-refresh every 30 seconds

#### CDR Records
- Advanced filtering by date, direction, caller ID
- Export to CSV
- AI-powered call analysis
- Sortable columns and pagination

#### AI Agent
- Chat interface for natural language queries
- Call routing decisions (Human/AI/Hybrid)
- Automated insights and recommendations
- Quick action buttons for common queries

### AI Agent Modes

1. **Human Only**: All calls route to human agents
2. **AI Only**: All calls handled by AI
3. **Hybrid**: AI decides routing based on context

## Configuration

### OpenAI API Key

Get your API key from [OpenAI Platform](https://platform.openai.com/api-keys)

### Database Connection

The panel uses the existing FusionPBX PostgreSQL database. Configuration is read from `/etc/fusionpbx/config.conf`.

### Customization

Edit the following files to customize:
- `resources/views/layouts/app.blade.php` - Layout and navigation
- `app/Services/AiAgentService.php` - AI behavior and prompts
- `config/services.php` - Service configurations

## Troubleshooting

### Permission Issues

```bash
sudo chown -R www-data:www-data /var/www/admin/user-panel
sudo chmod -R 775 /var/www/admin/user-panel/storage
sudo chmod -R 775 /var/www/admin/user-panel/bootstrap/cache
```

### Database Connection Issues

Check your PostgreSQL connection:
```bash
psql -h 127.0.0.1 -U fusionpbx -d fusionpbx
```

### AI Agent Not Working

1. Verify OpenAI API key in `.env`
2. Check logs: `tail -f storage/logs/laravel.log`
3. Ensure AI_AGENT_ENABLED=true in `.env`

### Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Security

- Change `APP_KEY` in production
- Use HTTPS in production
- Restrict database access
- Keep OpenAI API key secure
- Regular security updates

## Maintenance

### Update Dependencies

```bash
composer update
npm update
npm run build
```

### View Logs

```bash
tail -f storage/logs/laravel.log
```

### Backup

```bash
# Backup .env file
cp .env .env.backup

# Database backup is handled by FusionPBX
```

## Support

For issues and questions:
- Check FusionPBX documentation
- Review Laravel documentation
- OpenAI API documentation

## License

This admin panel integrates with FusionPBX (MPL 1.1 License) and uses Laravel (MIT License).
