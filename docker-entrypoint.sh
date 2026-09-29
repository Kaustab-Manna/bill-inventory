#!/bin/bash
set -e

# Dynamically bind Apache to the PORT assigned by Render (defaults to 80 if not set)
PORT="${PORT:-80}"
echo "Configuring Apache to listen on port $PORT..."
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf

# Ensure writable directory permissions
chown -R www-data:www-data /var/www/html/writable
chmod -R 775 /var/www/html/writable

# Optional: Auto-run migrations and seeders if RUN_MIGRATIONS is set to true
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running database migrations..."
    php spark migrate --all || true

    echo "Running default seeders..."
    php spark db:seed CoreSeeder || true
    php spark db:seed Phase2Seeder || true
    php spark db:seed Phase3Seeder || true
fi

exec "$@"
