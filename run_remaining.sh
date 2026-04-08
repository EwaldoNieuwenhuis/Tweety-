#!/bin/bash
set -e
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"

echo "Setting Node version..."
nvm use 18

echo "Generating Application Key..."
php artisan key:generate

echo "Migrating Database..."
php artisan migrate --force

echo "Installing Node dependencies..."
npm install

echo "Building frontend assets..."
npm run dev

echo "Setup complete!"
