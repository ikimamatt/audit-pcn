@echo off
REM ============================================
REM Deployment Fix Script (Windows)
REM Run this after deploying to hosting
REM ============================================

echo 🚀 Starting deployment fix...
echo.

REM Clear all cache
echo 🧹 Clearing all cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
echo ✅ Cache cleared!
echo.

REM Optimize for production
echo ⚡ Optimizing for production...
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo ✅ Optimized!
echo.

REM Composer autoload
echo 📦 Dumping autoload...
composer dump-autoload --optimize
echo ✅ Autoload dumped!
echo.

echo ✨ Deployment fix completed!
echo.
echo 📋 Next steps:
echo 1. Clear browser cache (Ctrl+Shift+Delete)
echo 2. Try login again
echo 3. Check browser console (F12) for errors
echo 4. If still not working, check DEPLOYMENT_TROUBLESHOOTING.md
echo.
pause
