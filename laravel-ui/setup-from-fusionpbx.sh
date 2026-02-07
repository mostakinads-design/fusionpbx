#!/bin/bash

#########################################################
# FusionPBX Laravel UI - Database Configuration Helper
#########################################################
# This script reads database credentials from FusionPBX
# config file and updates the Laravel .env file
#########################################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}FusionPBX Laravel UI - Configuration Helper${NC}"
echo "=============================================="
echo ""

# Check if FusionPBX config exists
FUSIONPBX_CONFIG="/etc/fusionpbx/config.conf"

if [ ! -f "$FUSIONPBX_CONFIG" ]; then
    echo -e "${RED}ERROR: FusionPBX config file not found at $FUSIONPBX_CONFIG${NC}"
    echo "Please make sure FusionPBX is installed on this system."
    exit 1
fi

echo -e "${GREEN}✓${NC} Found FusionPBX config at $FUSIONPBX_CONFIG"

# Read database credentials from FusionPBX config
DB_HOST=$(grep "^database.0.host" "$FUSIONPBX_CONFIG" | cut -d'=' -f2 | xargs)
DB_PORT=$(grep "^database.0.port" "$FUSIONPBX_CONFIG" | cut -d'=' -f2 | xargs)
DB_NAME=$(grep "^database.0.name" "$FUSIONPBX_CONFIG" | cut -d'=' -f2 | xargs)
DB_USER=$(grep "^database.0.username" "$FUSIONPBX_CONFIG" | cut -d'=' -f2 | xargs)
DB_PASS=$(grep "^database.0.password" "$FUSIONPBX_CONFIG" | cut -d'=' -f2 | xargs)

# Validate that we got all values
if [ -z "$DB_HOST" ] || [ -z "$DB_PORT" ] || [ -z "$DB_NAME" ] || [ -z "$DB_USER" ] || [ -z "$DB_PASS" ]; then
    echo -e "${RED}ERROR: Could not read all database credentials from config file${NC}"
    exit 1
fi

echo -e "${GREEN}✓${NC} Successfully read database credentials"
echo "  Host: $DB_HOST"
echo "  Port: $DB_PORT"
echo "  Database: $DB_NAME"
echo "  Username: $DB_USER"
echo "  Password: [hidden]"
echo ""

# Check if .env file exists
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        echo -e "${YELLOW}→${NC} Creating .env from .env.example..."
        cp .env.example .env
        echo -e "${GREEN}✓${NC} Created .env file"
    else
        echo -e "${RED}ERROR: .env.example file not found${NC}"
        exit 1
    fi
else
    echo -e "${YELLOW}!${NC} .env file already exists"
    read -p "Do you want to update it? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "Aborted."
        exit 0
    fi
fi

# Update .env file with database credentials
echo -e "${YELLOW}→${NC} Updating .env with FusionPBX database credentials..."

# Use sed to update the values
sed -i "s/^DB_HOST=.*/DB_HOST=$DB_HOST/" .env
sed -i "s/^DB_PORT=.*/DB_PORT=$DB_PORT/" .env
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=$DB_PASS|" .env

echo -e "${GREEN}✓${NC} Updated .env file with database credentials"
echo ""

# Generate APP_KEY if not set
if ! grep -q "^APP_KEY=base64:" .env; then
    echo -e "${YELLOW}→${NC} Generating application key..."
    php artisan key:generate
    echo -e "${GREEN}✓${NC} Generated application key"
else
    echo -e "${GREEN}✓${NC} Application key already set"
fi

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Configuration completed successfully!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo "Next steps:"
echo "1. Run: composer install --no-dev --optimize-autoloader"
echo "2. Run: npm install"
echo "3. Run: php artisan migrate"
echo "4. Run: npm run build"
echo "5. Set permissions: sudo chown -R www-data:www-data storage bootstrap/cache"
echo "6. Configure Nginx using the provided config files"
echo ""
