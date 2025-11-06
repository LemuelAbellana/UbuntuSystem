# Security Audit Report
**Target**: http://192.168.1.18/anime
**Date**: 2025-11-05
**System**: Laravel-Style Anime CRUD on Ubuntu

---

## ✅ SECURITY STRENGTHS

### 1. SQL Injection Protection
**Status**: ✅ **PROTECTED**
- All database queries use **PDO prepared statements**
- No raw SQL with user input
- Example from `Anime.php`:
```php
$stmt = $this->db->prepare("SELECT * FROM anime WHERE id = ?");
$stmt->execute([$id]);
```

### 2. XSS (Cross-Site Scripting) Protection
**Status**: ✅ **PROTECTED**
- All user output uses `htmlspecialchars()`
- Example from `index.php`:
```php
<td><?php echo htmlspecialchars($item['title']); ?></td>
```

### 3. Environment Configuration
**Status**: ✅ **PROTECTED**
- `.env` file excluded from web access via Nginx
- Database credentials not in code
- `.env` in `.gitignore`

### 4. Server Security
**Status**: ✅ **CONFIGURED**
- Fail2ban active (SSH brute-force protection)
- UFW firewall enabled
- SSH key authentication
- Root login disabled

---

## ⚠️ VULNERABILITIES FOUND

### 1. **CRITICAL: CSRF Protection Missing**
**Risk Level**: 🔴 **HIGH**

**Issue**: No CSRF tokens on forms
- Attackers can forge requests if user is logged in
- Forms can be submitted from external sites

**Affected Files**:
- `resources/views/anime/create.php`
- `resources/views/anime/edit.php`

**Fix Required**:
```php
// Add to each form
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
```

### 2. **HIGH: No Authentication System**
**Risk Level**: 🔴 **HIGH**

**Issue**: Anyone can access and modify data
- No login required
- No user roles
- Public database modification

**Impact**: Anyone on network can:
- Add fake anime
- Delete all records
- Modify existing data

**Recommendation**: Add basic authentication

### 3. **MEDIUM: Direct Object Reference**
**Risk Level**: 🟡 **MEDIUM**

**Issue**: Users can access any anime by ID
- URLs like `/anime/1/edit`, `/anime/2/edit` are predictable
- No ownership validation

**Example Attack**:
```
http://192.168.1.18/anime/999/edit
# Can access any record by guessing IDs
```

### 4. **MEDIUM: No Rate Limiting on Application Level**
**Risk Level**: 🟡 **MEDIUM**

**Issue**: No rate limiting on form submissions
- Attacker can spam create/update/delete
- Database can be flooded

**Current Protection**: Nginx rate limiting configured
**Missing**: Application-level throttling

### 5. **MEDIUM: Unvalidated Input**
**Risk Level**: 🟡 **MEDIUM**

**Issue**: No server-side validation
- Relies only on HTML5 validation (client-side)
- Can be bypassed with curl/Postman

**Example Attack**:
```bash
curl -X POST http://192.168.1.18/anime \
  -d "title=<script>alert('xss')</script>" \
  -d "episodes=-999999"
```

### 6. **LOW: No HTTPS**
**Risk Level**: 🟢 **LOW** (for local network)

**Issue**: Traffic not encrypted
- Credentials sent in plain text
- MITM attacks possible

**Note**: Low risk if only on local network
**Fix**: Install SSL certificate (Let's Encrypt)

### 7. **LOW: Sensitive Information Disclosure**
**Risk Level**: 🟢 **LOW**

**Issue**: Error messages may reveal paths
- PHP errors show file paths
- Could help attackers map system

**Fix**: Disable error display in production

### 8. **LOW: No Session Security**
**Risk Level**: 🟢 **LOW**

**Issue**: No session management
- No session timeout
- No secure cookies

---

## 🛡️ IMMEDIATE FIXES NEEDED

### Priority 1: Add CSRF Protection

**Create**: `app/Security/CSRF.php`
```php
<?php
namespace App\Security;

class CSRF {
    public static function generateToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateToken($token) {
        return isset($_SESSION['csrf_token']) &&
               hash_equals($_SESSION['csrf_token'], $token);
    }
}
```

**Update forms** to include:
```php
<?php
session_start();
require_once 'app/Security/CSRF.php';
use App\Security\CSRF;
?>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo CSRF::generateToken(); ?>">
    <!-- rest of form -->
</form>
```

**Validate in controller**:
```php
if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
    die('CSRF token validation failed');
}
```

### Priority 2: Add Input Validation

**Create**: `app/Validators/AnimeValidator.php`
```php
<?php
namespace App\Validators;

class AnimeValidator {
    public static function validate($data) {
        $errors = [];

        // Title required, max 255 chars
        if (empty($data['title']) || strlen($data['title']) > 255) {
            $errors[] = 'Title is required (max 255 characters)';
        }

        // Episodes must be positive integer
        if (!empty($data['episodes']) && (!is_numeric($data['episodes']) || $data['episodes'] < 0)) {
            $errors[] = 'Episodes must be a positive number';
        }

        // Rating must be between 0 and 10
        if (!empty($data['rating']) && ($data['rating'] < 0 || $data['rating'] > 10)) {
            $errors[] = 'Rating must be between 0 and 10';
        }

        // Status must be valid enum
        $validStatuses = ['Ongoing', 'Completed', 'Upcoming'];
        if (!empty($data['status']) && !in_array($data['status'], $validStatuses)) {
            $errors[] = 'Invalid status';
        }

        return $errors;
    }
}
```

### Priority 3: Add Basic Authentication

**Create**: `app/Middleware/Auth.php`
```php
<?php
namespace App\Middleware;

class Auth {
    public static function check() {
        session_start();

        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function login($username, $password) {
        // Simple hardcoded check (replace with database)
        if ($username === 'admin' && password_verify($password, PASSWORD_HASH)) {
            $_SESSION['user_id'] = 1;
            return true;
        }
        return false;
    }
}
```

### Priority 4: Disable Error Display

**Update**: `public/index.php`
```php
<?php
// Production settings
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php_errors.log');

require_once __DIR__ . '/../routes/web.php';
```

---

## 🔒 ADDITIONAL SECURITY MEASURES

### 1. HTTP Security Headers

**Add to Nginx config**:
```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net; style-src 'self' 'unsafe-inline';" always;
```

### 2. Database User Permissions

**Limit privileges**:
```sql
REVOKE ALL PRIVILEGES ON anime_laravel.* FROM 'gomz'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON anime_laravel.* TO 'gomz'@'localhost';
FLUSH PRIVILEGES;
```

### 3. File Permissions

**Restrict access**:
```bash
# Make .env readable only by owner
chmod 600 /var/www/anime-app/.env

# Prevent execution in upload directories
chmod 755 /var/www/anime-app/public
```

### 4. Backup Strategy

**Setup automated backups**:
```bash
# Database backup
mysqldump -u gomz -p anime_laravel > backup_$(date +%Y%m%d).sql

# Encrypt backup
gpg --encrypt backup_$(date +%Y%m%d).sql
```

---

## 📊 VULNERABILITY SUMMARY

| Vulnerability | Risk Level | Status | Priority |
|--------------|------------|--------|----------|
| CSRF Missing | 🔴 HIGH | ❌ Vulnerable | P1 |
| No Authentication | 🔴 HIGH | ❌ Vulnerable | P1 |
| Direct Object Reference | 🟡 MEDIUM | ❌ Vulnerable | P2 |
| No Rate Limiting | 🟡 MEDIUM | ⚠️ Partial | P2 |
| Unvalidated Input | 🟡 MEDIUM | ❌ Vulnerable | P2 |
| No HTTPS | 🟢 LOW | ⚠️ Acceptable | P3 |
| Error Disclosure | 🟢 LOW | ❌ Vulnerable | P3 |
| Session Security | 🟢 LOW | ❌ Vulnerable | P3 |
| SQL Injection | ✅ SAFE | ✅ Protected | - |
| XSS | ✅ SAFE | ✅ Protected | - |

---

## 🎯 RECOMMENDED ACTION PLAN

### Immediate (This Week)
1. ✅ Add CSRF protection to all forms
2. ✅ Add input validation
3. ✅ Disable error display
4. ✅ Add HTTP security headers

### Short Term (This Month)
1. Implement basic authentication
2. Add rate limiting
3. Setup SSL certificate
4. Configure automated backups

### Long Term (Next 3 Months)
1. Implement role-based access control
2. Add audit logging
3. Setup intrusion detection
4. Regular security updates

---

## 🔍 PENETRATION TEST COMMANDS

### Test SQL Injection (Should Fail)
```bash
curl "http://192.168.1.18/anime/1' OR '1'='1/edit"
# Expected: 404 or redirect
```

### Test XSS (Should Be Escaped)
```bash
curl -X POST http://192.168.1.18/anime \
  -d "title=<script>alert('xss')</script>" \
  -d "genre=test" \
  -d "status=Ongoing"
# Check if script tags are escaped in HTML
```

### Test CSRF (Currently Vulnerable)
```bash
# Create malicious HTML
cat > csrf_test.html <<EOF
<form action="http://192.168.1.18/anime" method="POST">
  <input name="title" value="CSRF Test">
  <input name="status" value="Ongoing">
</form>
<script>document.forms[0].submit();</script>
EOF
# Open in browser - will succeed without CSRF protection
```

### Test Authentication (Currently Open)
```bash
curl http://192.168.1.18/anime
# Should require login but doesn't
```

---

## 📝 CONCLUSION

**Overall Security Rating**: ⚠️ **MODERATE**

**Strengths**:
- ✅ Protected against SQL injection
- ✅ Protected against XSS
- ✅ Server hardening in place
- ✅ Environment variables secured

**Critical Issues**:
- ❌ No CSRF protection (exploitable)
- ❌ No authentication (public access)
- ❌ No input validation (bypassable)

**Recommendation**: **Implement Priority 1 fixes before production use**

---

**Next Steps**: Apply the fixes in the "IMMEDIATE FIXES NEEDED" section above.
