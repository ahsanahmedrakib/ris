#!/bin/bash
set -euo pipefail

echo "=== Installing npm dependencies ==="
npm ci

echo "=== Building frontend assets (Vite) ==="
npm run build

echo "=== Running migrations ==="
php artisan migrate --force

echo "=== Build finished successfully ==="