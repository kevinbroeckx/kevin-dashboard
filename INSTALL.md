# Installation Guide

Complete step-by-step instructions for setting up the Kevin Dashboard.

## System Requirements

- **PHP**: 8.4+ (portable installation included)
- **Node.js**: 20+ (for asset compilation)
- **Composer**: 2.5+
- **OpenClaw Gateway**: Running on port 18789
- **Disk Space**: ~500MB (vendor + node_modules)
- **OS**: Windows, macOS, or Linux

## Step 1: Clone or Download

```bash
cd /path/to/workspace
git clone https://github.com/yourusername/kevin-dashboard.git
cd kevin-dashboard
```

Or if you already have the folder:

```bash
cd /path/to/workspace/kevin-dashboard
```

## Step 2: Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` with your OpenClaw Gateway details:

```bash
# Find your gateway password
cat ~/.openclaw/openclaw.json | grep -A2 '"password"'

# Update .env
OPENCLAW_HOST=127.0.0.1
OPENCLAW_PORT=18789
OPENCLAW_TOKEN=YOUR_GATEWAY_PASSWORD_HERE
```

**Note**: On Windows, use PowerShell:
```powershell
(Get-Content C:\Users\[YourUser]\.openclaw\openclaw.json | ConvertFrom-Json).gateway.auth.password
```

## Step 3: Install PHP Dependencies

```bash
composer install
```

**If composer is not in PATH**, use:
```bash
php composer.phar install
```

## Step 4: Install JavaScript Dependencies

```bash
npm install
```

## Step 5: Generate Application Key

```bash
php artisan key:generate
```

This creates a unique `APP_KEY` in `.env` for encryption.

## Step 6: Setup Database

Initialize SQLite database:

```bash
php artisan migrate
```

This creates `database/database.sqlite` automatically.

## Step 7: Build Frontend Assets

```bash
npm run build
```

For development with hot reload:

```bash
npm run dev
```

(Run in a separate terminal)

## Step 8: Start the Development Server

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

You should see:
```
   INFO  Server running on [http://0.0.0.0:8000].

  Press Ctrl+C to stop the server
```

## Step 9: Open in Browser

Navigate to:

```
http://localhost:8000
```

You should see the Kevin Dashboard with all 5 panels populating data.

## Verification Checklist

- [ ] Dashboard loads at `http://localhost:8000`
- [ ] Status panel shows green "Online" indicator
- [ ] Schedule panel lists your cron jobs
- [ ] Activity feed shows recent events
- [ ] Quick Messenger accepts input
- [ ] Kanban board has default task

## Troubleshooting

### "SQLSTATE[HY000]: General error: 1 cannot open shared object file"

**Problem**: SQLite extension missing

**Solution**:
```bash
php -m | grep pdo  # Check if pdo is loaded
php artisan tinker
>>> DB::statement('SELECT 1');
```

If it fails, check your `php.ini`:
```bash
php --ini
```

Ensure `extension=pdo_sqlite` is uncommented.

### "OpenClaw Gateway refused connection"

**Problem**: Can't reach gateway

**Solution**:
1. Verify gateway is running:
   ```bash
   openclaw status
   ```

2. Check token in `.env` matches gateway password:
   ```bash
   cat ~/.openclaw/openclaw.json | grep password
   ```

3. Test connectivity:
   ```bash
   curl -H 'Authorization: Bearer YOUR_TOKEN' \
     http://127.0.0.1:18789/health
   ```

### "npm: command not found"

**Problem**: Node.js not installed

**Solution**:
- Download from [nodejs.org](https://nodejs.org) (LTS)
- Or on macOS: `brew install node`
- Verify: `node --version && npm --version`

### Composer timeout during install

**Problem**: Network too slow for dependency download

**Solution**:
```bash
composer install --prefer-dist --no-interaction --no-scripts
php artisan optimize
```

Or increase timeout:
```bash
composer config process-timeout 600
```

### "The APP_KEY environment variable is not set"

**Problem**: `php artisan key:generate` didn't run

**Solution**:
```bash
php artisan key:generate --force
php artisan cache:clear
php artisan config:clear
```

## Production Deployment

⚠️ **Note**: This dashboard is designed for **local/private network use only**.

For production:

1. Use a proper web server (Nginx, Apache)
2. Enable HTTPS
3. Configure proper authentication (beyond gateway token)
4. Use environment secrets management
5. Enable rate limiting on API routes

Example Nginx config:

```nginx
server {
    listen 80;
    server_name kevin-dashboard.local;
    root /var/www/kevin-dashboard/public;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

## Next Steps

- Read the [README.md](./README.md) for feature overview
- Check [docs/ARCHITECTURE.md](./docs/ARCHITECTURE.md) for deeper dive
- Try sending a message in the Quick Messenger panel
- Explore the Kanban board for task management

## Need Help?

- Check [Troubleshooting](#troubleshooting) above
- Review OpenClaw docs: https://docs.openclaw.ai
- Open an issue on GitHub

---

**Happy monitoring! 🎯**
