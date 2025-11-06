# Quick Deployment Guide

## Step 1: Set Your Server IP

**Replace `YOUR_SERVER_IP` with your actual IP address in all commands below.**

Example: If your server IP is `192.168.0.100`, replace every `YOUR_SERVER_IP` with `192.168.0.100`

---

## Step 2: Deploy via Git (Recommended)

```bash
# Push changes from local machine
git add .
git commit -m "Implement security fixes: CSRF, Auth, Validation, Error handling"
git push origin main

# Deploy on server
ssh deploy@YOUR_SERVER_IP
cd /var/www/anime-app
git pull origin main
sudo chown -R www-data:www-data /var/www/anime-app
sudo chmod 600 /var/www/anime-app/.env
sudo touch /var/log/php_errors.log
sudo chown www-data:www-data /var/log/php_errors.log
sudo systemctl restart php8.4-fpm
exit
```

---

## Step 3: Test the System

### Test 1: Open in Browser
```
http://YOUR_SERVER_IP/anime
```
- Should redirect to login page
- Login with: `admin` / `admin123`
- Should see anime list with logout button

### Test 2: Test CSRF Protection (from your local machine)
```bash
curl -X POST "http://YOUR_SERVER_IP/anime" \
  -H "Origin: http://malicious-site.com" \
  -d "title=CSRF_TEST&genre=Attack&status=Ongoing"
```
**Expected**: "CSRF token validation failed"

### Test 3: Test Logout
- Click "Logout" button
- Should redirect to login
- Trying to access anime list should redirect back to login

---

## Step 4: Change Default Password

```bash
# Generate new password hash
php -r "echo password_hash('YOUR_NEW_PASSWORD', PASSWORD_DEFAULT);"

# SSH to server and edit Auth.php
ssh deploy@YOUR_SERVER_IP
sudo nano /var/www/anime-app/app/Middleware/Auth.php

# Replace the PASSWORD_HASH line with your new hash
# Save and exit (Ctrl+O, Enter, Ctrl+X)

# Restart PHP
sudo systemctl restart php8.4-fpm
```

---

## Troubleshooting

### Issue: "Permission denied" when accessing site
```bash
ssh deploy@YOUR_SERVER_IP
sudo chown -R www-data:www-data /var/www/anime-app
sudo systemctl restart php8.4-fpm
```

### Issue: Can't login
```bash
ssh deploy@YOUR_SERVER_IP
sudo tail -20 /var/log/php_errors.log
# Check for errors
```

### Issue: Session issues
```bash
ssh deploy@YOUR_SERVER_IP
sudo chmod 1733 /var/lib/php/sessions/
sudo systemctl restart php8.4-fpm
```

---

## Quick Commands Reference

**SSH into server**:
```bash
ssh deploy@YOUR_SERVER_IP
```

**Restart PHP**:
```bash
sudo systemctl restart php8.4-fpm
```

**View error logs**:
```bash
sudo tail -f /var/log/php_errors.log
```

**Check Nginx logs**:
```bash
sudo tail -f /var/log/nginx/error.log
```

---

## Security Status After Deployment

✅ CSRF Protection - Active
✅ Authentication Required - Active
✅ Input Validation - Active
✅ Error Display - Hidden
✅ SQL Injection - Protected
✅ XSS Protection - Protected
✅ .env File - Hidden

**System Status**: 🟢 Production Ready (for local network)

---

**Login**: `http://YOUR_SERVER_IP/login`
**Username**: `admin`
**Password**: `admin123` (change immediately after first login!)
