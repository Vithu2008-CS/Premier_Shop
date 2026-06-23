#!/bin/bash
#
# Custom Railpack start script
# ============================
# Railpack builds this app (it IGNORES nixpacks.toml) and, by default, runs its
# own built-in start-container.sh which boots Laravel and then hands off to
# FrankenPHP. That default has no place for the Laravel scheduler, so
# `cart:remind-abandoned` (and any future scheduled task) never fired in prod.
#
# Railpack's documented override is to drop a start-container.sh in the project
# root — it REPLACES the built-in one entirely. So this file mirrors the upstream
# default verbatim and adds ONE thing: the scheduler, started in the background
# before the (foreground, blocking) web server.
#
# Keep this in sync with upstream:
#   https://github.com/railwayapp/railpack -> core/providers/php/start-container.sh

set -e

if [ "$IS_LARAVEL" = "true" ]; then
  if [ "$RAILPACK_SKIP_MIGRATIONS" != "true" ]; then
    # Idempotent; railway.json's preDeployCommand already migrated in a one-off
    # container before this deploy, but the default boots with this too — harmless.
    echo "Running migrations and seeding database ..."
    php artisan migrate --force
  fi

  php artisan storage:link
  php artisan optimize:clear
  php artisan optimize

  # Start the Laravel scheduler in the background. `schedule:work` invokes
  # schedule:run every minute (see routes/console.php: cart:remind-abandoned
  # hourly). withoutOverlapping uses the shared DB cache lock, so the task stays
  # deduped even if the service scales to multiple instances. It dies with the
  # container when the foreground server below exits.
  echo "Starting Laravel scheduler ..."
  php artisan schedule:work &

  echo "Starting Laravel server ..."
fi

# Start the FrankenPHP server (foreground / blocking — keeps the container alive).
docker-php-entrypoint --config /Caddyfile --adapter caddyfile 2>&1
