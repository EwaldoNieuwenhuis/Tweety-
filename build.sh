#!/bin/bash
set -e
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"

echo "Using Node 16 for backwards compatibility..."
nvm install 16
nvm use 16

echo "Rebuilding node modules for Node 16..."
npm ci || npm install
npm run dev

echo "Build successful!"
