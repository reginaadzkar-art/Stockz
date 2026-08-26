#!/bin/sh
set -e

# 1. Setup PORT for Render (Render sets $PORT dynamically, default to 10000 if not set)
export PORT="${PORT:-10000}"
echo "==> Configuring Nginx on PORT: ${PORT}..."

# Replace ${PORT} placeholder in Nginx template
# In Alpine Linux, HTTP server blocks belong in /etc/nginx/http.d/
if [ -f /etc/nginx/nginx.conf.template ]; then
    mkdir -p /etc/nginx/http.d
    rm -f /etc/nginx/conf.d/*.conf /etc/nginx/http.d/*.conf
    envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/http.d/default.conf
    nginx -t
fi

# 2. Ensure .env exists
if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.example ]; then
        echo "==> Creating .env from .env.example..."
        cp /var/www/html/.env.example /var/www/html/.env
    else
        touch /var/www/html/.env
    fi
fi

# 3. Setup APP_KEY (auto-generate if missing)
if [ -z "$APP_KEY" ]; then
    EXISTING_KEY=$(grep -E "^APP_KEY=" /var/www/html/.env 2>/dev/null | cut -d '=' -f2- || true)
    if [ -z "$EXISTING_KEY" ]; then
        echo "==> APP_KEY is not set. Generating application key..."
        php artisan key:generate --force
        export APP_KEY=$(grep -E "^APP_KEY=" /var/www/html/.env | cut -d '=' -f2-)
    else
        export APP_KEY="$EXISTING_KEY"
    fi
    echo "==> Application key ready."
fi

# 4. Setup SQLite database if needed
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_PATH="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    DB_DIR=$(dirname "$DB_PATH")
    mkdir -p "$DB_DIR"
    if [ ! -f "$DB_PATH" ]; then
        echo "==> Creating SQLite database file at: $DB_PATH"
        touch "$DB_PATH"
    fi
    chmod -R 777 "$DB_DIR"
    chown -R www-data:www-data "$DB_DIR"
    chmod 666 "$DB_PATH"
fi

# 5. Ensure required storage and cache directories exist with proper permissions
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 6. Create storage symlink
echo "==> Linking storage..."
php artisan storage:link --force 2>/dev/null || true

# 7. Run database migrations (enabled by default so tables are always ready)
if [ "${AUTO_MIGRATE:-true}" != "false" ]; then
    echo "==> Running database migrations..."
    php artisan migrate --force || echo "==> Migration warning/skipped."
fi

# 8. Run database seeder (seeds default admin user if AUTO_SEED is not explicitly false)
if [ "${AUTO_SEED:-true}" != "false" ]; then
    echo "==> Running database seeder..."
    php artisan db:seed --force || echo "==> Seeding warning/skipped."
fi

# 9. Clear and Optimize Laravel caches
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

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
