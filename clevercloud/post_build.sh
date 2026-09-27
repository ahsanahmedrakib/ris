#!/bin/bash
set -euo pipefail

echo "=== Installing npm dependencies ==="
npm ci

echo "=== Building frontend assets (Vite) ==="
npm run build

# Clever Cloud's filesystem is ephemeral outside addons, so anything written
# into the container is lost on the next deploy. Keep the public upload folders
# on the attached volume and symlink them back under public/, which preserves
# the direct /images and /files URLs the app already generates.
UPLOAD_VOLUME="${UPLOAD_VOLUME:-/home/user/data}"

link_uploads() {
  local dir="$1"
  local target="$UPLOAD_VOLUME/$dir"

  mkdir -p "$target"

  # The volume starts empty, so the per-folder execution lock-down is copied in
  # from the checkout. public/.htaccess already denies scripts everywhere, but
  # these make it unconditional and independent of the root rules. When the
  # folder is already a symlink to the volume, source and destination are the
  # same file and cp would fail the build, so that case is skipped.
  if [ -f "public/$dir/.htaccess" ] && [ ! "public/$dir/.htaccess" -ef "$target/.htaccess" ]; then
    cp "public/$dir/.htaccess" "$target/.htaccess" \
      || echo "  WARNING: could not refresh $target/.htaccess"
  fi

  if [ -e "public/$dir" ] && [ ! -L "public/$dir" ]; then
    # Fresh checkouts leave these empty because the folders are gitignored.
    # Refuse to touch a populated one rather than risk discarding uploads.
    if [ -n "$(find "public/$dir" -mindepth 1 -not -name '.htaccess' -print -quit 2>/dev/null)" ]; then
      echo "  WARNING: public/$dir has untracked content; leaving it in place."
      return
    fi
    rm -rf "public/$dir"
  fi

  ln -sfn "$target" "public/$dir"
  echo "  public/$dir -> $target"
}

if [ -d "$UPLOAD_VOLUME" ]; then
  echo "=== Linking uploads to volume: $UPLOAD_VOLUME ==="
  link_uploads images
  link_uploads files
else
  echo "=== WARNING: volume $UPLOAD_VOLUME not mounted; uploads will NOT survive deploys ==="
  echo "===         attach a volume and set UPLOAD_VOLUME, or uploads will be lost ==="
fi

echo "=== Running migrations ==="
php artisan migrate --force

echo "=== Seeding database (if empty) ==="
if ! php artisan db:seed --force; then
  echo "  NOTE: the seeder did not complete."
  echo "        It is idempotent, so an existing database is fine and this is harmless."
  echo "        But on a brand new database it means no admin account was created."
  echo "        Set SEED_ADMIN_PASSWORD (12+ chars) in the Clever Cloud env vars and redeploy."
fi

echo "=== Clearing caches ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Build finished successfully ==="