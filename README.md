FriendZone - Laravel Dating & Social Platform
A Laravel-based dating and social networking application that helps users find matches, connect through communities, and build meaningful relationships.

🎯 Overview
FriendZone is a dating and social platform built with Laravel that combines traditional dating app features with community-based social networking. Users can discover matches through a smart matching algorithm, engage in real-time chat with voice/video calls, join communities, and build friendships.


🛠 Tech Stack
Backend
Laravel 8+: PHP framework
MySQL: Database
WebSockets: Real-time communication (Laravel WebSockets / Ratchet)
Queue System: Background job processing
File Storage: Local storage for media files

Frontend
Blade Templates: Laravel's templating engine
Tailwind CSS: Utility-first CSS framework (via CDN)
Bootstrap 4: UI components and grid system
Font Awesome: Icon library
JavaScript (Vanilla): Interactive features
jQuery: DOM manipulation and AJAX
Real-Time Features
WebSocket Server: Laravel WebSockets for real-time updates
WebRTC: Peer-to-peer voice and video calling
Event Broadcasting: Laravel events for real-time notifications
Services & Libraries
Faker: Generate fake data for seeding
Carbon: Date and time manipulation
Laravel Legacy Factories: Support for older factory syntax


📦 Installation
Prerequisites
PHP 7.4 or higher
Composer
MySQL 5.7+ or MariaDB
Node.js and NPM (for asset compilation)
WebSocket server support

Step 1: Clone Repository
git clone https://github.com/TanYuHeng0912/friendzone.gitcd friendzone

Step 2: Install Dependencies
composer installcomposer dump-autoloadnpm install

Step 3: Environment Configuration
# Copy environment filecp .env.example .env# Generate application keyphp artisan key:generate

Step 4: Configure Database
Edit .env file and set your database credentials:
DB_CONNECTION=mysqlDB_HOST=127.0.0.1DB_PORT=3306DB_DATABASE=friendzoneDB_USERNAME=your_usernameDB_PASSWORD=your_password

Step 5: Run Migrations
php artisan migrate

Step 6: Create Storage Link
php artisan storage:link

Step 7: Seed Database (Optional)
# Seed with random users, matches, and communitiesphp artisan db:seed# Or seed specific seedersphp artisan db:seed --class=CommunitySeederphp artisan db:seed --class=DatabaseSeeder

Step 8: Create Admin User
php artisan tinker
Then run:
$user = App\User::create([    'email' => 'admin@friendzone.com',    'password' => Hash::make('your_password'),    'is_admin' => 1,    'email_verified_at' => now()]);

Step 9: Start Development Server

# Start Laravel development server
php artisan serve
php artisan websockets:serve
npm run dev
