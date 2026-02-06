#!/bin/bash

##############################################################################
# FusionPBX Laravel Admin Panel Installation Script
# For Debian 11/12 with PHP 8.2
##############################################################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Print functions
print_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [[ $EUID -ne 0 ]]; then
   print_error "This script must be run as root"
   exit 1
fi

print_info "FusionPBX Laravel Admin Panel Installation"
echo "==========================================="
echo ""

# Check Debian version
print_info "Checking Debian version..."
if ! grep -q "Debian" /etc/os-release; then
    print_error "This script is designed for Debian 11 or 12"
    exit 1
fi

# Install PHP 8.2 if not present
print_info "Checking PHP 8.2..."
if ! command -v php8.2 &> /dev/null; then
    print_info "Installing PHP 8.2..."
    apt-get update
    apt-get install -y lsb-release apt-transport-https ca-certificates wget
    wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
    echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list
    apt-get update
    apt-get install -y php8.2 php8.2-cli php8.2-fpm php8.2-pgsql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd
else
    print_info "PHP 8.2 already installed"
fi

# Install Composer if not present
print_info "Checking Composer..."
if ! command -v composer &> /dev/null; then
    print_info "Installing Composer..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
else
    print_info "Composer already installed"
fi

# Install Node.js if not present
print_info "Checking Node.js..."
if ! command -v node &> /dev/null; then
    print_info "Installing Node.js..."
    curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
    apt-get install -y nodejs
else
    print_info "Node.js already installed"
fi

# Set installation directory
INSTALL_DIR="/var/www/admin/user-panel"
CURRENT_DIR=$(pwd)

print_info "Installing to: $INSTALL_DIR"

# Create directory structure
print_info "Creating directory structure..."
mkdir -p /var/www/admin
mkdir -p $INSTALL_DIR

# Copy files
print_info "Copying application files..."
if [ -d "$CURRENT_DIR/laravel-admin-panel" ]; then
    cp -r $CURRENT_DIR/laravel-admin-panel/* $INSTALL_DIR/
elif [ -f "$CURRENT_DIR/composer.json" ]; then
    # We're already in the laravel directory
    cp -r $CURRENT_DIR/* $INSTALL_DIR/
else
    print_error "Cannot find Laravel application files"
    exit 1
fi

cd $INSTALL_DIR

# Install PHP dependencies
print_info "Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

# Install Node dependencies
print_info "Installing Node dependencies..."
npm install --silent

# Build assets
print_info "Building frontend assets..."
npm run build

# Setup environment
print_info "Setting up environment file..."
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

# Generate app key
print_info "Generating application key..."
php artisan key:generate --force

# Read FusionPBX config
FUSIONPBX_CONFIG="/etc/fusionpbx/config.conf"
if [ -f "$FUSIONPBX_CONFIG" ]; then
    print_info "Reading FusionPBX configuration..."
    
    # Extract database credentials
    DB_HOST=$(grep "database.0.host" $FUSIONPBX_CONFIG | cut -d'=' -f2 | tr -d ' ')
    DB_PORT=$(grep "database.0.port" $FUSIONPBX_CONFIG | cut -d'=' -f2 | tr -d ' ')
    DB_NAME=$(grep "database.0.name" $FUSIONPBX_CONFIG | cut -d'=' -f2 | tr -d ' ')
    DB_USER=$(grep "database.0.username" $FUSIONPBX_CONFIG | cut -d'=' -f2 | tr -d ' ')
    DB_PASS=$(grep "database.0.password" $FUSIONPBX_CONFIG | cut -d'=' -f2 | tr -d ' ')
    
    # Update .env file
    sed -i "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
    sed -i "s/DB_PORT=.*/DB_PORT=$DB_PORT/" .env
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env
    
    print_info "Database configuration updated from FusionPBX config"
else
    print_warn "FusionPBX config not found at $FUSIONPBX_CONFIG"
    print_warn "Please manually configure database settings in .env"
fi

# Set permissions
print_info "Setting permissions..."
chown -R www-data:www-data $INSTALL_DIR
find $INSTALL_DIR -type d -exec chmod 755 {} \;
find $INSTALL_DIR -type f -exec chmod 644 {} \;
chmod -R 775 $INSTALL_DIR/storage
chmod -R 775 $INSTALL_DIR/bootstrap/cache

# Optimize Laravel
print_info "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Detect web server
print_info "Configuring web server..."
if systemctl is-active --quiet nginx; then
    print_info "Detected Nginx, creating configuration..."
    
    cat > /etc/nginx/sites-available/fusionpbx-admin-panel << 'EOF'
location /admin/user-panel {
    alias /var/www/admin/user-panel/public;
    try_files $uri $uri/ @laravel_admin;
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $request_filename;
    }
}

location @laravel_admin {
    rewrite /admin/user-panel/(.*)$ /admin/user-panel/index.php?/$1 last;
}
EOF
    
    # Add to default site if exists
    if [ -f "/etc/nginx/sites-enabled/default" ]; then
        print_info "Adding to Nginx default site..."
        # Backup original
        cp /etc/nginx/sites-enabled/default /etc/nginx/sites-enabled/default.backup
        # Insert before the last closing brace
        sed -i '/^}/i \    include /etc/nginx/sites-available/fusionpbx-admin-panel;' /etc/nginx/sites-enabled/default
    fi
    
    nginx -t && systemctl reload nginx
    print_info "Nginx configured successfully"
    
elif systemctl is-active --quiet apache2; then
    print_info "Detected Apache, creating configuration..."
    
    cat > /etc/apache2/sites-available/fusionpbx-admin-panel.conf << 'EOF'
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
EOF
    
    a2enmod rewrite
    a2ensite fusionpbx-admin-panel
    systemctl reload apache2
    print_info "Apache configured successfully"
else
    print_warn "No web server detected. Please configure manually."
fi

# Final messages
echo ""
echo "==========================================="
print_info "Installation completed successfully!"
echo "==========================================="
echo ""
print_info "Next steps:"
echo "  1. Edit $INSTALL_DIR/.env and add your OpenAI API key:"
echo "     OPENAI_API_KEY=your_api_key_here"
echo ""
echo "  2. Access the panel at: http://your-server-ip/admin/user-panel"
echo ""
echo "  3. Review the INSTALLATION.md file for more details"
echo ""
print_warn "Important: Keep your OpenAI API key secure!"
echo ""
