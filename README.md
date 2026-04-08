# Tweety 🐦

Tweety is a Twitter clone built with **Laravel 8**, **Tailwind CSS**, and **Blade Templates**. It features custom user profiles, functional timelines, a robust follow/unfollow system, and dynamic interactions (likes, dislikes, mentions, and image attachments).

## Tech Stack Overview
- **Backend:** Laravel 8.x (PHP 8.0+)
- **Database:** SQLite (Default for local development)
- **Frontend:** Laravel Mix + Tailwind CSS v2
- **Dummy Data:** Pravatar (Avatars), Lorem Picsum (Banners/Tweets)

---

## 🚀 Onboarding & Setup Guide

Welcome to the project! Since this is a legacy Laravel 8 application running a specific Node/Tailwind build pipeline, it is heavily recommended to use **WSL (Windows Subsystem for Linux)** if you are developing on a Windows machine.

### Prerequisites (WSL/Linux)
1. PHP 8.0+ (Tested successfully with PHP 8.3 & `php8.3-sqlite3` driver).
2. Composer (PHP package manager).
3. **Node v16** *(Crucial for ancient PostCSS & Tailwind v2 compatibility - NVM simplifies this)*.

### Step-by-Step Installation

1. **Clone the repository** and navigate into the directory in your WSL terminal.
2. **Setup the Environment File**:
   Copy the example environment variables and generate an application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Make sure `DB_CONNECTION=sqlite` is set in your `.env`.*

3. **Install Backend Dependencies**:
   If you have a newer PHP version (like 8.3), you may need to ignore platform requirements since this framework is locked to older Laravel dependencies:
   ```bash
   composer install --ignore-platform-reqs
   ```

4. **Prepare the Database & Dummy Data**:
   This project relies on SQLite and includes a very thorough `DatabaseSeeder` that populates the application with hundreds of users, tweets, and networking algorithms so you don't start with a blank timeline.
   ```bash
   # Make sure the SQLite database exists
   touch database/database.sqlite
   
   # Link the storage directory for avatars
   php artisan storage:link
   
   # Migrate and seed the data!
   php artisan migrate:fresh --seed
   ```

5. **Install Frontend Dependencies (Node 16)**:
   You will encounter `ERR_PACKAGE_PATH_NOT_EXPORTED` errors if you try to compile assets using Node 18 or 20. Please use Node 16.
   ```bash
   nvm install 16
   nvm use 16
   npm ci
   npm run dev
   ```
   *(We also included a handy `build.sh` script in the root directory that automates the frontend asset pipeline for you!)*

### Local Development

To spin up the web server, simply run:
```bash
php artisan serve
```
You can now access the application at **http://localhost:8000**.

Since you ran the database seeder earlier, you can log in immediately using the predefined test account to view a populated timeline:
- **Email:** `pinky@example.com`
- **Password:** `password`

## Key Features & Architecture
- **Timeline (`User@timeline`)**: Queries not only the active user's tweets, but merges them logically with the tweets of everyone the user follows.
- **Dynamic Images**: Models (`Tweet.php`, `User.php`) are configured to seamlessly serve both relative `/storage` images as well as absolute URLs (`https://`) seamlessly—allowing our factories to populate realistic scraped UI data.
- **Mentions (`Tweet@bodyWithMentions`)**: A custom parser algorithm converts raw `@username` tags into clickable profile anchor tags prior to rendering.

Happy coding!
