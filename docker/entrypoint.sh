#!/bin/sh
set -e

# 1. Setup PORT for Render (Render sets $PORT dynamically, default to 10000 if not set)
export PORT="${PORT:-10000}"
echo "==> Configuring Nginx on PORT: ${PORT}..."

# Replace ${PORT} placeholder in Nginx template
if [ -f /etc/nginx/nginx.conf.template ]; then
    mkdir -p /etc/nginx/http.d /etc/nginx/conf.d
    envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/http.d/default.conf
    cp /etc/nginx/http.d/default.conf /etc/nginx/conf.d/default.conf 2>/dev/null || true
fi

# 2. Setup SQLite database if needed
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_PATH="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    DB_DIR=$(dirname "$DB_PATH")
    mkdir -p "$DB_DIR"
    if [ ! -f "$DB_PATH" ]; then
        echo "==> Creating SQLite database file at: $DB_PATH"
        touch "$DB_PATH"
    fi
    chmod -R 775 "$DB_DIR"
    chown -R www-data:www-data "$DB_DIR"
fi

# 3. Ensure required directories and permissions exist
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 4. Create storage symlink
echo "==> Linking storage..."
php artisan storage:link --force 2>/dev/null || true

# 5. Check APP_KEY
if [ -z "$APP_KEY" ]; then
    echo "==> WARNING: APP_KEY is not set! Please configure APP_KEY in Render environment variables."
fi

# 6. Run database migrations if AUTO_MIGRATE=true or RUN_MIGRATIONS=true
if [ "${AUTO_MIGRATE:-false}" = "true" ] || [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "==> Running database migrations..."
    php artisan migrate --force || echo "==> Migration failed or already up to date."
fi

# 7. Run database seeder if AUTO_SEED=true
if [ "${AUTO_SEED:-false}" = "true" ]; then
    echo "==> Running database seeder..."
    php artisan db:seed --force || echo "==> Seeding completed or skipped."
fi

# 8. Optimize Laravel caches for production
if [ "${APP_ENV:-production}" = "production" ] && [ -n "$APP_KEY" ]; then
    echo "==> Optimizing Laravel cache for production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "==> Starting application services..."
# If custom arguments are provided, run them; otherwise run supervisord
if [ "$#" -gt 0 ]; then
    exec "$@"
else
    exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
fi
