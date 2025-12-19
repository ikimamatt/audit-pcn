# 🚀 Deployment & Troubleshooting Guide

## Masalah: Login Modal Tidak Muncul di Hosting

Jika popup login berhasil muncul di lokal tapi tidak muncul di hosting, ikuti langkah-langkah berikut:

---

## ✅ Checklist Deployment

### 1. Upload/Deploy Files
Pastikan file-file berikut sudah ter-upload ke hosting:

```bash
# Files yang dimodifikasi:
✓ app/Http/Controllers/Auth/AuthenticatedSessionController.php
✓ resources/views/layouts/vertical.blade.php
```

### 2. Clear All Cache
```bash
# Jalankan command ini di hosting via SSH atau panel
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear

# Jika menggunakan OPcache
php artisan optimize
```

### 3. Set Proper Permissions
```bash
# Set permission untuk storage dan cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Restart Services
```bash
# Restart PHP-FPM (jika ada akses)
sudo systemctl restart php8.2-fpm

# Atau restart web server
sudo systemctl restart nginx
# atau
sudo systemctl restart apache2
```

---

## 🔍 Debugging Steps

### Step 1: Check Browser Console

1. Login ke aplikasi di hosting
2. Tekan **F12** untuk membuka Developer Tools
3. Klik tab **Console**
4. Check apakah ada error:

**Common Errors:**
```javascript
❌ Uncaught ReferenceError: Swal is not defined
   Solution: SweetAlert2 tidak ter-load

❌ Failed to load resource: net::ERR_BLOCKED_BY_CLIENT
   Solution: AdBlock atau firewall memblock CDN

❌ Failed to load resource: 404 (Not Found)
   Solution: CDN tidak accessible
```

### Step 2: Check Network Tab

1. Klik tab **Network** di Developer Tools
2. Refresh halaman login
3. Login dengan credentials
4. Check apakah request ke CDN SweetAlert2 berhasil:

**Look for:**
```
✓ sweetalert2@11 - Status: 200 OK
✗ sweetalert2@11 - Status: Failed / Blocked
```

### Step 3: Test Session

**Create test route** di `routes/web.php`:
```php
Route::get('/test-session', function() {
    session()->flash('login_success', [
        'name' => 'Test User',
        'role' => 'Test Role',
        'time' => now()->format('H:i:s')
    ]);
    return redirect('/home');
})->middleware('auth');
```

**Test:**
1. Visit: `https://your-domain.com/test-session`
2. Popup should appear
3. If not, session is not working properly

### Step 4: Check Session Driver

**In `.env` file:**
```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

**If using database session:**
```bash
# Make sure session table exists
php artisan session:table
php artisan migrate
```

**If using redis session:**
```env
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

---

## 🛠️ Solutions

### Solution 1: CDN Issues (Most Common)

**Problem:** Hosting blocks external CDN or CDN is slow

**Fix:** Use local copy of SweetAlert2

1. **Download SweetAlert2:**
```bash
npm install sweetalert2
# or
yarn add sweetalert2
```

2. **Copy to public folder:**
```bash
cp node_modules/sweetalert2/dist/sweetalert2.all.min.js public/js/
```

3. **Update layout to use local file:**

In `resources/views/layouts/vertical.blade.php`:
```blade
@if(session('login_success'))
    <!-- Use local file instead of CDN -->
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script>
    // ... rest of code
    </script>
@endif
```

### Solution 2: Cache Issues

**Clear browser cache:**
```
Ctrl + Shift + Delete (Chrome/Firefox)
Cmd + Shift + Delete (Mac)
```

**Clear server cache:**
```bash
# Via SSH
php artisan optimize:clear

# Via cPanel
Use "Clear Cache" option in Laravel tools
```

### Solution 3: Session Not Working

**Check session configuration:**

**In `config/session.php`:**
```php
'driver' => env('SESSION_DRIVER', 'file'),
'lifetime' => env('SESSION_LIFETIME', 120),
'expire_on_close' => false,
'encrypt' => false,
'files' => storage_path('framework/sessions'),
'connection' => null,
'table' => 'sessions',
'store' => null,
'lottery' => [2, 100],
'cookie' => env(
    'SESSION_COOKIE',
    Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
),
'path' => '/',
'domain' => env('SESSION_DOMAIN', null),
'secure' => env('SESSION_SECURE_COOKIE', false),
'http_only' => true,
'same_site' => 'lax',
```

**For HTTPS hosting:**
```env
SESSION_SECURE_COOKIE=true
```

### Solution 4: JavaScript Disabled

**Check if JavaScript is enabled** in browser

**Test with simple alert:**

In `AuthenticatedSessionController.php`:
```php
// Add debug session
$request->session()->flash('test_alert', true);
```

In layout:
```blade
@if(session('test_alert'))
    <script>
        alert('JavaScript is working!');
    </script>
@endif
```

---

## 🧪 Testing Checklist

After deployment, test these scenarios:

- [ ] Login with valid credentials
- [ ] Popup appears after successful login
- [ ] Popup shows correct user name
- [ ] Popup shows correct role
- [ ] Popup shows current time
- [ ] Timer counts down from 5 to 0
- [ ] Popup auto-closes after 5 seconds
- [ ] Can close popup manually with OK button
- [ ] Can close popup by clicking outside
- [ ] Can close popup with ESC key
- [ ] No JavaScript errors in console

---

## 📊 Monitoring & Logs

### Check Laravel Logs
```bash
# View latest logs
tail -f storage/logs/laravel.log

# Check for session errors
grep "session" storage/logs/laravel.log
```

### Check Web Server Logs
```bash
# Nginx
tail -f /var/log/nginx/error.log

# Apache
tail -f /var/log/apache2/error.log
```

### Check PHP Errors
```bash
# Enable error reporting in .env
APP_DEBUG=true
LOG_LEVEL=debug

# Remember to set back to false in production!
```

---

## 🔐 Security Notes

**Before deployment:**

1. ✅ Set `APP_DEBUG=false` in production
2. ✅ Use `SESSION_SECURE_COOKIE=true` for HTTPS
3. ✅ Clear sensitive data from logs
4. ✅ Test thoroughly in staging environment

---

## 📞 Quick Fixes

### Quick Fix #1: Force Clear Everything
```bash
php artisan optimize:clear && \
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache && \
composer dump-autoload
```

### Quick Fix #2: Rebuild Views
```bash
find storage/framework/views -type f -delete
php artisan view:clear
```

### Quick Fix #3: Reset Sessions
```bash
rm -rf storage/framework/sessions/*
php artisan cache:clear
```

---

## 🆘 Still Not Working?

If popup still doesn't appear after all steps:

1. **Check hosting provider documentation** for Laravel requirements
2. **Contact hosting support** about:
   - CDN access restrictions
   - Session storage issues
   - JavaScript execution
3. **Use fallback alert** (already implemented in code)
4. **Consider using local SweetAlert2** instead of CDN

---

## 📚 Additional Resources

- Laravel Sessions: https://laravel.com/docs/session
- SweetAlert2 Docs: https://sweetalert2.github.io/
- Laravel Deployment: https://laravel.com/docs/deployment

---

**Last Updated:** December 2025  
**Tested On:** Laravel 10.x / 11.x
