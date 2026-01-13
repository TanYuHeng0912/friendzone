# FriendZone - Laravel Dating & Social Platform

![FriendZone Demo]

A comprehensive Laravel-based dating and social networking application that combines traditional dating app features with community-based social networking. FriendZone helps users discover matches through intelligent algorithms, engage in real-time communication, and build meaningful connections.

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)

## 🎯 Overview

FriendZone is a full-featured dating and social platform built with Laravel that enables users to:
- Discover potential matches through smart matching algorithms
- Engage in real-time chat with voice and video calling capabilities
- Join interest-based communities and participate in social activities
- Build friendships and connections beyond dating
- Manage comprehensive profiles with photos, interests, and preferences

## ✨ Features

### Core Dating Features
- **Smart Matching System**: AI-powered compatibility algorithm based on age, interests, location, languages, and relationship preferences
- **Swipe Interface**: Tinder-like swipe functionality to like or pass on potential matches
- **Compatibility Scoring**: Real-time compatibility percentage calculation (0-100%) between users
- **Match Discovery**: Browse and discover potential matches with customizable search filters
- **Profile Management**: Comprehensive profile editing with multiple photo uploads, bio, interests, and search preferences

### Real-Time Communication
- **Instant Messaging**: Real-time chat with WebSocket support for instant message delivery
- **Voice Messages**: Record and send voice messages in conversations
- **Media Sharing**: Share photos and videos in chat conversations with media gallery
- **Message Features**: 
  - Message reactions (❤️, 😂, 😮, 😢, 👍, 👎)
  - Reply to specific messages
  - Typing indicators
  - Read receipts
  - Message search functionality
- **Voice & Video Calls**: WebRTC-based voice and video calling with call history tracking

### Community Features
- **Communities**: Join and create interest-based communities
- **Posts & Comments**: Create posts and engage with comments within communities
- **Post Reactions**: Like posts and interact with community content
- **Polls**: Create and vote on community polls
- **Events**: Create events and manage RSVPs
- **Activity Feed**: Track community and friend activities in real-time

### Social Features
- **Friendship System**: Send, accept, and manage friend requests
- **Friend Lists**: View and manage your friends list
- **Online Status**: Real-time online/offline status indicators
- **Activity Tracking**: Comprehensive activity feed showing user interactions

### Admin Features
- **Admin Dashboard**: Comprehensive admin panel with statistics and analytics
- **User Management**: Ban, suspend, and manage users
- **Community Management**: Create, edit, and delete communities
- **Feedback Management**: Review and respond to user feedback
- **Analytics**: View user growth, match rates, and activity statistics

## 🛠 Tech Stack

### Backend
- **Laravel 10**: PHP framework
- **PHP 8.1+**: Programming language
- **MySQL**: Relational database
- **Laravel WebSockets**: Real-time communication (beyondcode/laravel-websockets)
- **Laravel Queue**: Background job processing
- **File Storage**: Local storage for media files

### Frontend
- **Blade Templates**: Laravel's templating engine
- **Tailwind CSS**: Utility-first CSS framework (via CDN)
- **Bootstrap 4**: UI components and grid system
- **Font Awesome**: Icon library
- **JavaScript (Vanilla)**: Interactive features and AJAX
- **jQuery**: DOM manipulation

### Real-Time Features
- **WebSocket Server**: Laravel WebSockets for real-time updates
- **WebRTC**: Peer-to-peer voice and video calling
- **Event Broadcasting**: Laravel events for real-time notifications
- **Pusher Integration**: Real-time message broadcasting

### Services & Libraries
- **Faker**: Generate fake data for database seeding
- **Carbon**: Date and time manipulation
- **Laravel UI**: Authentication scaffolding
- **Guzzle HTTP**: HTTP client for API requests

## 📦 Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL 5.7+ or MariaDB
- Node.js and NPM (for asset compilation)
- WebSocket server support (for real-time features)

### Step 1: Clone Repository
```bash
git clone https://github.com/TanYuHeng0912/friendzone.git
cd friendzone
```

### Step 2: Install Dependencies
```bash
# Install PHP dependencies
composer install
composer dump-autoload

# Install Node.js dependencies (optional, for asset compilation)
npm install
```

### Step 3: Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Database
Edit `.env` file and set your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=friendzone
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 5: Run Migrations
```bash
php artisan migrate
```

### Step 6: Create Storage Link
```bash
php artisan storage:link
```

### Step 7: Seed Database (Optional)
```bash
# Seed with random users, matches, communities, and test data
php artisan db:seed

# Or seed specific seeders
php artisan db:seed --class=CommunitySeeder
php artisan db:seed --class=DatabaseSeeder
```

### Step 8: Create Admin User
Use the artisan command to create an admin user:
```bash
php artisan make:user:admin
```
Or manually create via tinker:
```bash
php artisan tinker
```
```php
$user = App\User::create([
    'email' => 'admin@friendzone.com',
    'password' => Hash::make('your_password'),
    'is_admin' => 1,
    'email_verified_at' => now()
]);
```

### Step 9: Start Development Servers
```bash
# Terminal 1: Start Laravel development server
php artisan serve

# Terminal 2: Start queue worker (for background jobs)
php artisan queue:work

# Terminal 3: Start WebSocket server (for real-time features)
php artisan websockets:serve
```


## 👥 Authors

- **Tan Yu Heng** - [@TanYuHeng0912](https://github.com/TanYuHeng0912)

---

**Built with ❤️ using Laravel 10**

---
