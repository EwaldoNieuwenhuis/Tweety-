#!/bin/bash
set -e

# Install NVM
echo "Installing NVM..."
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"

echo "Installing Node 18..."
nvm install 18
nvm use 18

echo "Installing Composer dependencies..."
composer install --ignore-platform-reqs

echo "Generating Application Key..."
php artisan key:generate

echo "Migrating Database..."
php artisan migrate --force

echo "Installing Node dependencies..."
npm install

echo "Building frontend assets..."
npm run dev

echo "Setup complete!"
