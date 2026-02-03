#!/bin/bash
# 
# PostgreSQL CDR Configuration Script for FusionPBX
# This script helps configure the PostgreSQL database connection for CDR and voicemail
#

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Print colored output
print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if running as root
check_root() {
    if [ "$EUID" -ne 0 ]; then 
        print_error "Please run this script as root or with sudo"
        exit 1
    fi
}

# Function to detect config file location
find_fusionpbx_config() {
    if [ -f "/etc/fusionpbx/config.conf" ]; then
        echo "/etc/fusionpbx/config.conf"
    elif [ -f "/usr/local/etc/fusionpbx/config.conf" ]; then
        echo "/usr/local/etc/fusionpbx/config.conf"
    else
        echo ""
    fi
}

# Function to parse config.conf
parse_config() {
    local config_file="$1"
    local key="$2"
    grep "^${key}" "$config_file" | cut -d'=' -f2- | xargs
}

# Function to find FreeSwitch vars.xml location
find_freeswitch_vars() {
    if [ -f "/etc/freeswitch/vars.xml" ]; then
        echo "/etc/freeswitch/vars.xml"
    elif [ -f "/usr/local/freeswitch/conf/vars.xml" ]; then
        echo "/usr/local/freeswitch/conf/vars.xml"
    else
        echo ""
    fi
}

# Main configuration function
configure_database() {
    print_info "FusionPBX PostgreSQL CDR Configuration"
    echo ""

    # Find FusionPBX config
    local fusionpbx_config=$(find_fusionpbx_config)
    if [ -z "$fusionpbx_config" ]; then
        print_error "FusionPBX config file not found!"
        print_info "Please ensure FusionPBX is installed at /etc/fusionpbx/config.conf"
        exit 1
    fi
    print_success "Found FusionPBX config: $fusionpbx_config"

    # Parse database settings from FusionPBX config
    local db_type=$(parse_config "$fusionpbx_config" "database.0.type")
    local db_host=$(parse_config "$fusionpbx_config" "database.0.host")
    local db_port=$(parse_config "$fusionpbx_config" "database.0.port")
    local db_name=$(parse_config "$fusionpbx_config" "database.0.name")
    local db_user=$(parse_config "$fusionpbx_config" "database.0.username")
    local db_pass=$(parse_config "$fusionpbx_config" "database.0.password")
    local db_sslmode=$(parse_config "$fusionpbx_config" "database.0.sslmode")

    # Set defaults
    [ -z "$db_port" ] && db_port="5432"
    [ -z "$db_sslmode" ] && db_sslmode="prefer"

    print_info "Database Configuration from FusionPBX:"
    echo "  Type: $db_type"
    echo "  Host: $db_host"
    echo "  Port: $db_port"
    echo "  Name: $db_name"
    echo "  User: $db_user"
    echo "  Password: ********"
    echo ""

    if [ "$db_type" != "pgsql" ]; then
        print_warning "Database type is not PostgreSQL (pgsql)!"
        print_info "This script is designed for PostgreSQL. Current type: $db_type"
        read -p "Do you want to continue anyway? (y/n): " continue
        if [ "$continue" != "y" ]; then
            exit 0
        fi
    fi

    # Build PostgreSQL connection string
    local dsn_cdr="host=${db_host} port=${db_port} user=${db_user} password=${db_pass} dbname=${db_name} sslmode=${db_sslmode} connect_timeout=10"
    
    print_info "Generated CDR connection string:"
    echo "  $dsn_cdr"
    echo ""

    # Find FreeSwitch vars.xml
    local vars_xml=$(find_freeswitch_vars)
    if [ -z "$vars_xml" ]; then
        print_error "FreeSwitch vars.xml not found!"
        print_info "Expected locations:"
        echo "  - /etc/freeswitch/vars.xml"
        echo "  - /usr/local/freeswitch/conf/vars.xml"
        exit 1
    fi
    print_success "Found FreeSwitch vars.xml: $vars_xml"

    # Backup vars.xml
    local backup_file="${vars_xml}.backup.$(date +%Y%m%d%H%M%S)"
    print_info "Creating backup: $backup_file"
    cp "$vars_xml" "$backup_file"
    print_success "Backup created"

    # Update vars.xml
    print_info "Updating vars.xml with database credentials..."
    
    # Check if dsn_cdr already exists
    if grep -q 'data="dsn_cdr=' "$vars_xml"; then
        # Update existing
        sed -i "s|data=\"dsn_cdr=[^\"]*\"|data=\"dsn_cdr=${dsn_cdr}\"|g" "$vars_xml"
        print_success "Updated existing dsn_cdr variable"
    else
        print_warning "dsn_cdr variable not found in vars.xml"
        print_info "Please add it manually or use the web interface"
    fi

    echo ""
    print_success "Configuration complete!"
    echo ""
    print_info "Next steps:"
    echo "  1. Reload FreeSwitch configuration:"
    echo "     fs_cli -x 'reloadxml'"
    echo ""
    echo "  2. Verify CDR is recording to PostgreSQL:"
    echo "     Make a test call, then check:"
    echo "     psql -U ${db_user} -d ${db_name} -c \"SELECT uuid, caller_id_name, destination_number FROM v_xml_cdr ORDER BY start_stamp DESC LIMIT 5;\""
    echo ""
    echo "  3. Check FreeSwitch logs for any errors:"
    echo "     tail -f /var/log/freeswitch/freeswitch.log | grep cdr_pg_csv"
    echo ""
    print_info "Backup file created: $backup_file"
    echo ""
    
    # Ask to reload FreeSwitch XML
    read -p "Do you want to reload FreeSwitch XML now? (y/n): " reload
    if [ "$reload" == "y" ]; then
        if command -v fs_cli &> /dev/null; then
            print_info "Reloading FreeSwitch XML..."
            fs_cli -x "reloadxml"
            print_success "FreeSwitch XML reloaded"
        else
            print_warning "fs_cli command not found. Please reload manually:"
            echo "  fs_cli -x 'reloadxml'"
        fi
    fi
}

# Main execution
check_root
configure_database
