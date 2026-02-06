# Laravel Admin Panel for FusionPBX

## 📦 What's Included

This repository now includes a complete **Laravel-based Admin Panel** with AI integration that works alongside your existing FusionPBX installation.

### Location
- **Source Code**: `/laravel-admin-panel/` directory in this repository
- **Installation Target**: `/var/www/admin/user-panel` on your server
- **Access URL**: `http://your-server-ip/admin/user-panel`

## 🚀 Quick Install

```bash
# Clone this repository (if not already)
cd /path/to/fusionpbx

# Run the automated installer
sudo bash laravel-admin-panel/install.sh

# Add your OpenAI API key
sudo nano /var/www/admin/user-panel/.env
# Set: OPENAI_API_KEY=your-key-here

# Access the panel
# http://your-server-ip/admin/user-panel
```

## ✨ Key Features

### 🤖 AI Integration
- **OpenAI GPT-4** powered call analysis
- **Hybrid Routing**: Human/AI/Hybrid modes
- **Chat Interface**: Natural language queries
- **Smart Insights**: Automated call analysis and recommendations

### 📞 Modern CDR Viewer
- Advanced filtering by date, direction, caller
- Real-time search and pagination
- Export to CSV
- AI analysis for each call

### 📊 Live Dashboard
- Real-time statistics (auto-refresh every 30s)
- Today's call metrics
- Recent calls monitoring
- Extension status

### 🎨 Modern Interface
- Responsive design (works on mobile, tablet, desktop)
- Tailwind CSS styling
- Alpine.js interactivity
- Professional UI/UX

## 📋 Requirements

- **OS**: Debian 11 or 12
- **PHP**: 8.2+
- **Database**: PostgreSQL (uses existing FusionPBX DB)
- **Web Server**: Nginx or Apache
- **Node.js**: 16+
- **OpenAI API Key**: Required for AI features

## 📚 Documentation

- **[QUICKSTART.md](laravel-admin-panel/QUICKSTART.md)** - 5-minute setup guide
- **[INSTALLATION.md](laravel-admin-panel/INSTALLATION.md)** - Detailed installation instructions
- **[README.md](laravel-admin-panel/README.md)** - Overview and features

## 🔧 Configuration

The panel is pre-configured to:
- ✅ Read database credentials from `/etc/fusionpbx/config.conf`
- ✅ Connect to existing FusionPBX PostgreSQL database
- ✅ Work alongside FusionPBX without modifications
- ✅ Use tables: `v_xml_cdr`, `v_extensions`, etc.

You only need to add your **OpenAI API Key**.

## 🎯 AI Agent Modes

### Human Only
All calls route to human agents. AI available for analysis only.

### Hybrid (Recommended)
AI analyzes context and routes calls intelligently between human and AI agents.

### AI Only
All calls handled by AI system. Good for simple inquiries or after-hours.

## 📸 What You Get

### Dashboard
- Live call statistics
- Recent calls with status
- Performance metrics
- Quick access to all features

### CDR Records Page
- Searchable call history
- Advanced filters
- AI analysis button for each call
- CSV export

### AI Agent Page  
- Chat interface
- Mode selector (Human/AI/Hybrid)
- Quick action buttons
- Natural language queries

## 🛡️ Security

- Environment variables for sensitive data
- CSRF protection enabled
- Proper file permissions
- API key security guidelines
- Production-ready configuration

## 📦 Technical Stack

- **Backend**: Laravel 12 (PHP 8.2)
- **Frontend**: Tailwind CSS, Alpine.js
- **Database**: PostgreSQL (FusionPBX)
- **AI**: OpenAI GPT-4 API
- **Charts**: Chart.js

## 🔄 Integration with FusionPBX

### ✅ Seamless Integration
- Uses existing FusionPBX database
- No modifications to FusionPBX required
- Separate URL path (`/admin/user-panel`)
- Shares same database credentials

### ✅ Data Access
- Reads CDR records from `v_xml_cdr` table
- Accesses extensions from `v_extensions` table
- Real-time data synchronization
- No data duplication

## 💡 Use Cases

### Call Center Managers
- Monitor call performance in real-time
- Get AI insights on call quality
- Export data for reports
- Track agent performance

### System Administrators
- Modern interface for system monitoring
- Quick access to call records
- AI-powered troubleshooting
- Efficient data export

### Business Owners
- Understand call patterns
- Get AI recommendations for improvement
- Monitor customer interactions
- Make data-driven decisions

## 🚨 Common Issues & Solutions

### Installation Failed?
```bash
# Check logs
sudo tail -f /var/log/syslog

# Re-run installation
sudo bash laravel-admin-panel/install.sh
```

### AI Not Working?
1. Verify OpenAI API key in `.env`
2. Check `AI_AGENT_ENABLED=true`
3. Review logs: `/var/www/admin/user-panel/storage/logs/`

### Database Connection Error?
1. Verify FusionPBX is running
2. Check PostgreSQL is accessible
3. Confirm credentials in `.env`

## 📞 Support

- **FusionPBX**: https://www.fusionpbx.com
- **Laravel**: https://laravel.com/docs
- **OpenAI**: https://platform.openai.com/docs

## 📄 License

- **Laravel Framework**: MIT License
- **FusionPBX Integration**: MPL 1.1 License
- **OpenAI API**: Commercial (usage-based pricing)

## 🎉 Get Started Now!

```bash
cd /path/to/fusionpbx
sudo bash laravel-admin-panel/install.sh
```

Then access: `http://your-server-ip/admin/user-panel`

---

**Built for the FusionPBX Community with ❤️**

Combines the power of Laravel, FusionPBX, and AI to create a modern call center management experience.
