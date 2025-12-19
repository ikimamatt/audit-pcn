#!/bin/bash

# ============================================
# Deployment Fix Script
# Run this after deploying to hosting
# ============================================

echo "🚀 Starting deployment fix..."
echo ""

# Clear all cache
echo "🧹 Clearing all cache..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
echo "✅ Cache cleared!"
echo ""

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Optimized!"
echo ""

# Set proper permissions
echo "🔒 Setting proper permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache
echo "✅ Permissions set!"
echo ""

# Composer autoload
echo "📦 Dumping autoload..."
composer dump-autoload --optimize
echo "✅ Autoload dumped!"
echo ""

# Check session directory
echo "📂 Checking session directory..."
if [ -d "storage/framework/sessions" ]; then
    echo "✅ Session directory exists"
else
    echo "⚠️  Creating session directory..."
    mkdir -p storage/framework/sessions
    chmod -R 775 storage/framework/sessions
    echo "✅ Session directory created"
fi
echo ""

# Test session
echo "🧪 Testing session..."
php artisan tinker --execute="session()->put('test', 'working'); echo 'Session test: ' . session('test');"
echo ""

echo "✨ Deployment fix completed!"
echo ""
echo "📋 Next steps:"
echo "1. Clear browser cache (Ctrl+Shift+Delete)"
echo "2. Try login again"
echo "3. Check browser console (F12) for errors"
echo "4. If still not working, check DEPLOYMENT_TROUBLESHOOTING.md"
echo ""
