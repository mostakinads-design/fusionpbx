# Quick Start Guide - FusionPBX Laravel Admin Panel

## Installation (5 minutes)

### Step 1: Run Installation Script

```bash
# On your Debian 11/12 server with FusionPBX already installed
cd /path/to/fusionpbx/repository
sudo bash laravel-admin-panel/install.sh
```

The script automatically:
- ✅ Installs PHP 8.2, Composer, Node.js
- ✅ Copies files to `/var/www/admin/user-panel`
- ✅ Installs all dependencies
- ✅ Reads FusionPBX database config
- ✅ Sets permissions
- ✅ Configures web server

### Step 2: Add OpenAI API Key

```bash
# Edit the environment file
sudo nano /var/www/admin/user-panel/.env

# Add your OpenAI API key (get from https://platform.openai.com/api-keys)
OPENAI_API_KEY=sk-your-api-key-here
```

Save and exit (Ctrl+X, Y, Enter)

### Step 3: Access the Panel

Open browser: `http://your-server-ip/admin/user-panel`

## First Steps

### 1. Dashboard
- View today's call statistics
- Monitor real-time metrics
- See recent calls

### 2. CDR Records
- Click "📞 CDR Records" in navigation
- Use filters to search calls
- Click "🤖 AI Analyze" on any call for insights

### 3. AI Agent
- Click "🤖 AI Agent" in navigation
- Try asking: "Analyze today's call statistics"
- Change mode: Human Only / Hybrid / AI Only

## Common Tasks

### Export CDR Data
1. Go to CDR Records
2. Apply filters (date range, direction, etc.)
3. Click "📥 Export CSV"

### Get AI Insights
1. Open AI Agent
2. Ask questions like:
   - "What are common hangup causes?"
   - "Show call patterns"
   - "Recommend improvements"

### Change AI Mode
1. Go to AI Agent
2. Select mode:
   - **Human Only**: All calls to humans
   - **Hybrid**: AI decides routing
   - **AI Only**: All calls to AI

## Troubleshooting

### Can't access panel?
```bash
# Check if web server is running
sudo systemctl status nginx  # or apache2

# Check permissions
sudo chown -R www-data:www-data /var/www/admin/user-panel
```

### AI not working?
```bash
# Verify API key in .env
sudo grep OPENAI_API_KEY /var/www/admin/user-panel/.env

# Check logs
sudo tail -f /var/www/admin/user-panel/storage/logs/laravel.log
```

### Database connection error?
```bash
# Test PostgreSQL connection
psql -h 127.0.0.1 -U fusionpbx -d fusionpbx

# Re-read FusionPBX config
cat /etc/fusionpbx/config.conf
```

## Features at a Glance

### Dashboard
- ✅ Real-time statistics (refreshes every 30s)
- ✅ Today's call metrics
- ✅ Recent calls list
- ✅ Extension status

### CDR Interface
- ✅ Advanced search & filters
- ✅ Export to CSV
- ✅ AI analysis per call
- ✅ Sortable columns
- ✅ Pagination

### AI Agent
- ✅ Natural language chat
- ✅ Call insights & recommendations
- ✅ Routing decisions
- ✅ Pattern analysis
- ✅ Quick action buttons

## Configuration Files

- **Environment**: `/var/www/admin/user-panel/.env`
- **Web Server**: `/etc/nginx/sites-enabled/` or `/etc/apache2/sites-enabled/`
- **Logs**: `/var/www/admin/user-panel/storage/logs/`

## Support & Documentation

- Detailed install: [INSTALLATION.md](laravel-admin-panel/INSTALLATION.md)
- FusionPBX: https://docs.fusionpbx.com
- OpenAI API: https://platform.openai.com/docs

## Security Checklist

- ✅ Change to HTTPS in production
- ✅ Keep OpenAI API key secure
- ✅ Regular security updates
- ✅ Monitor API usage/costs
- ✅ Backup `.env` file

---

**That's it! You're ready to use your AI-powered admin panel!** 🎉

For more details, see [INSTALLATION.md](laravel-admin-panel/INSTALLATION.md)
