# Simple Ubuntu Server Deployment Guide

Deploy your anime CRUD app on Ubuntu with security in 30 minutes.

## What You Need

- Ubuntu Server (20.04+)
- Your server IP address
- A domain name (optional)

---

## Quick Start (Copy & Paste)

### Step 1: Connect to Your Server

```bash
# From your local machine
ssh root@YOUR_SERVER_IP
```

### Step 2: Run Initial Setup Script

```bash
# Update system
apt update && apt upgrade -y

# Install everything we need
apt install -y nginx php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl mysql-server git curl ufw fail2ban certbot python3-certbot-nginx

# Secure MySQL
mysql_secure_installation
# Answer: Y to all questions, set a strong root password

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

---

## Step 3: Create User & Setup SSH

```bash
# Create new user
adduser deploy
# Set password when prompted

# Give sudo access
usermod -aG sudo deploy

# Switch to new user
su - deploy
```

**On your local machine**, generate SSH key:
```bash
ssh-keygen -t ed25519
# Press Enter 3 times (default location, no passphrase)

# Copy key to server
ssh-copy-id deploy@YOUR_SERVER_IP
```

**Back on server**, disable root login:
```bash
sudo nano /etc/ssh/sshd_config
```

Find and change these lines:
```
PermitRootLogin no
PasswordAuthentication no
```

Press `Ctrl+X`, then `Y`, then `Enter` to save.

```bash
# Restart SSH
sudo systemctl restart sshd
```

**Test**: Open new terminal and connect with new user:
```bash
ssh deploy@YOUR_SERVER_IP
# Should work without password!
```

---

## Step 4: Setup Firewall

```bash
# Allow SSH, HTTP, HTTPS
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'

# Enable firewall
sudo ufw --force enable

# Check status
sudo ufw status
```

---

## Step 5: Configure Fail2Ban

```bash
# Create config
sudo nano /etc/fail2ban/jail.local
```

Paste this:
```ini
[DEFAULT]
bantime = 1h
maxretry = 3

[sshd]
enabled = true
```

Press `Ctrl+X`, then `Y`, then `Enter`.

```bash
# Start fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

---

## Step 6: Setup Database

```bash
# Login to MySQL
sudo mysql

# Run these commands (change password!)
CREATE DATABASE anime_laravel;
CREATE USER 'animeuser'@'localhost' IDENTIFIED BY 'YourStrongPassword123!';
GRANT ALL PRIVILEGES ON anime_laravel.* TO 'animeuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## Step 7: Deploy Your App

```bash
# Clone your repo
cd /var/www
sudo git clone https://github.com/YOUR_USERNAME/YOUR_REPO.git anime-app

# Set permissions
sudo chown -R www-data:www-data /var/www/anime-app
sudo chmod -R 755 /var/www/anime-app

# Setup environment
cd /var/www/anime-app
sudo cp .env.example .env
sudo nano .env
```

Update these values in `.env`:
```env
DB_DATABASE=anime_laravel
DB_USERNAME=animeuser
DB_PASSWORD=YourStrongPassword123!
```

Save with `Ctrl+X`, `Y`, `Enter`.

```bash
# Install dependencies
cd /var/www/anime-app
sudo composer install --no-dev

# Import database
mysql -u animeuser -p anime_laravel < database/migrations/create_anime_table.sql
# Enter your database password when prompted
```

---

## Step 8: Configure Nginx

```bash
# Create config file
sudo nano /etc/nginx/sites-available/anime-app
```

Paste this (replace `your-domain.com` with your domain or server IP):
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/anime-app/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }

    location ~ /\.env {
        deny all;
    }
}
```

Save and exit.

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/anime-app /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default

# Test and restart
sudo nginx -t
sudo systemctl restart nginx
```

---

## Step 9: Setup SSL (If you have a domain)

```bash
# Get free SSL certificate
sudo certbot --nginx -d your-domain.com

# Follow the prompts:
# - Enter your email
# - Agree to terms: Y
# - Redirect HTTP to HTTPS: 2 (recommended)
```

**Done!** Your site is now at `https://your-domain.com/anime`

---

## Step 10: Port Forwarding (If server is at home)

1. Login to your router (usually http://192.168.1.1)
2. Find "Port Forwarding" section
3. Add these:

| Name  | External Port | Internal Port | Internal IP    | Protocol |
|-------|---------------|---------------|----------------|----------|
| HTTP  | 80            | 80            | SERVER_IP      | TCP      |
| HTTPS | 443           | 443           | SERVER_IP      | TCP      |
| SSH   | 22            | 22            | SERVER_IP      | TCP      |

4. Save and reboot router

---

## Testing Your Deployment

Visit your site:
- With domain: `https://your-domain.com/anime`
- Without domain: `http://YOUR_SERVER_IP/anime`

You should see your anime list!

---

## Updating Your App

```bash
# SSH into server
ssh deploy@YOUR_SERVER_IP

# Pull latest changes
cd /var/www/anime-app
sudo git pull

# Update dependencies
sudo composer install --no-dev

# Restart services
sudo systemctl restart nginx php8.1-fpm
```

---

## Useful Commands

```bash
# Check if services are running
sudo systemctl status nginx
sudo systemctl status php8.1-fpm
sudo systemctl status mysql
sudo systemctl status fail2ban

# View logs
sudo tail -f /var/log/nginx/error.log
sudo fail2ban-client status

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm

# Check blocked IPs
sudo fail2ban-client status sshd

# Unban an IP
sudo fail2ban-client set sshd unbanip 123.123.123.123
```

---

## Troubleshooting

### Can't access website
```bash
# Check Nginx is running
sudo systemctl status nginx

# Check firewall
sudo ufw status

# View error logs
sudo tail -50 /var/log/nginx/error.log
```

### Database connection error
```bash
# Test database connection
mysql -u animeuser -p anime_laravel

# Check .env file
cat /var/www/anime-app/.env

# Make sure credentials match!
```

### Can't SSH after changes
**Don't close your current SSH session!** Open a new terminal and test:
```bash
ssh deploy@YOUR_SERVER_IP
```

If it fails, go back to your old session and fix the issue.

---

## Security Checklist

After deployment, verify:

- [ ] Can SSH with key (not password)
- [ ] Cannot SSH as root
- [ ] Firewall is enabled (`sudo ufw status`)
- [ ] Fail2ban is running (`sudo systemctl status fail2ban`)
- [ ] SSL is working (https://)
- [ ] .env file is not accessible via browser
- [ ] Only necessary ports are open

---

## Quick Security Test

Try these from another computer:

1. **Try to access .env**: `https://your-domain.com/.env` → Should get 403 Forbidden
2. **Try wrong SSH password 3 times** → Should get banned for 1 hour
3. **Check SSL**: `https://your-domain.com` → Should have lock icon

---

## Common Mistakes to Avoid

1. ❌ **Don't skip the SSH key step** - You'll lock yourself out
2. ❌ **Don't close SSH before testing new SSH connection**
3. ❌ **Don't forget to change database password** in both MySQL and .env
4. ❌ **Don't use root user** for daily tasks
5. ❌ **Don't skip SSL** if you have a domain

---

## Need Help?

### Error: "Permission denied"
```bash
sudo chown -R www-data:www-data /var/www/anime-app
```

### Error: "Connection refused"
```bash
# Check if service is running
sudo systemctl status nginx
sudo systemctl start nginx
```

### Error: "502 Bad Gateway"
```bash
# PHP-FPM might be down
sudo systemctl restart php8.1-fpm
```

---

## Summary

Your server now has:

✅ Firewall blocking unauthorized access
✅ Fail2ban blocking brute force attacks
✅ Nginx reverse proxy serving your app
✅ SSL encryption (if domain configured)
✅ Secure SSH (key-based only)
✅ MySQL database
✅ Your app running at `/anime`

**Access your app**: `https://your-domain.com/anime` or `http://YOUR_SERVER_IP/anime`

Total setup time: **~30 minutes**
