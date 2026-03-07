## Module 1 — Infrastructure & Server Setup
> 🟢 Sonnet

### Goal
Production server fully configured and ready to receive the application.

### Tasks
- [ ] Install Redis on VM
- [ ] Configure Redis in Laravel `.env`
- [ ] Install Let's Encrypt SSL (Cloudflare Full mode)
- [ ] Set up GitHub Actions for automated deploy on push to main
- [ ] Install Supervisor for queue workers
- [ ] Record Oracle VM public IP in this file

### Technical Detail

**Redis install:**
```bash
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

**Laravel `.env` (production):**
```
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

**Let's Encrypt:**
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d sinfat.com -d www.sinfat.com
```

**GitHub Actions deploy (`.github/workflows/deploy.yml`):**
```yaml
name: Deploy
on:
  push:
    branches: [main]
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to Oracle VM
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ubuntu
          key: ${{ secrets.SERVER_SSH_KEY }}
          script: |
            cd /var/www/sinfat
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            php artisan sitemap:generate
```

**Supervisor config (`/etc/supervisor/conf.d/sinfat-worker.conf`):**
```ini
[program:sinfat-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/sinfat/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=1
user=ubuntu
```

**Oracle VM public IP:** `<FILL IN>`

### Acceptance Criteria
- [ ] `redis-cli ping` returns `PONG`
- [ ] `https://sinfat.com` loads with valid SSL (padlock in browser)
- [ ] Push to main triggers GitHub Action and deploys automatically
- [ ] Supervisor running: `sudo supervisorctl status`

### Dependencies
None — do this first.

---

