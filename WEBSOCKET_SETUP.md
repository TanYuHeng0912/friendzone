# WebSocket Setup Guide

## Overview
The chat system now supports real-time messaging via WebSockets using Laravel Echo and Pusher. If Pusher is not configured, the system automatically falls back to AJAX polling.

## Setup Instructions

### Option 1: Using Pusher (Recommended for Production)

1. **Create a Pusher account** (free tier available at https://pusher.com)

2. **Get your Pusher credentials** from your Pusher dashboard:
   - App ID
   - Key
   - Secret
   - Cluster (e.g., `mt1`, `us2`, `eu`)

3. **Add to your `.env` file:**
   ```env
   BROADCAST_DRIVER=pusher
   PUSHER_APP_ID=your_app_id
   PUSHER_APP_KEY=your_app_key
   PUSHER_APP_SECRET=your_app_secret
   PUSHER_APP_CLUSTER=mt1
   ```

4. **Install npm dependencies:**
   ```bash
   npm install
   ```

5. **Compile assets:**
   ```bash
   npm run dev
   # or for production
   npm run prod
   ```

6. **The system will automatically use WebSockets** when Pusher is configured!

### Option 2: Using Laravel WebSockets (Self-hosted)

If you prefer a self-hosted solution:

1. **Install Laravel WebSockets:**
   ```bash
   composer require beyondcode/laravel-websockets
   php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
   php artisan migrate
   php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
   ```

2. **Update `.env`:**
   ```env
   BROADCAST_DRIVER=pusher
   PUSHER_APP_ID=local
   PUSHER_APP_KEY=local
   PUSHER_APP_SECRET=local
   PUSHER_APP_CLUSTER=mt1
   WEBSOCKETS_PORT=6001
   ```

3. **Start WebSocket server:**
   ```bash
   php artisan websockets:serve
   ```

4. **Update `config/broadcasting.php`** to use local WebSocket server:
   ```php
   'pusher' => [
       'driver' => 'pusher',
       'key' => env('PUSHER_APP_KEY'),
       'secret' => env('PUSHER_APP_SECRET'),
       'app_id' => env('PUSHER_APP_ID'),
       'options' => [
           'cluster' => env('PUSHER_APP_CLUSTER'),
           'encrypted' => true,
           'host' => '127.0.0.1',
           'port' => 6001,
           'scheme' => 'http'
       ],
   ],
   ```

### Option 3: Fallback to AJAX Polling (Default)

If you don't configure Pusher, the system automatically uses AJAX polling (checks for new messages every 3 seconds). This works without any additional setup but is less efficient than WebSockets.

## How It Works

1. **When Pusher is configured:**
   - Messages are broadcast in real-time via WebSocket
   - Typing indicators are instant
   - No polling needed - more efficient

2. **When Pusher is NOT configured:**
   - System automatically falls back to AJAX polling
   - Messages checked every 3 seconds
   - Typing indicators checked every 1 second
   - Still fully functional, just slightly delayed

## Testing

1. Open two browser windows/tabs
2. Log in as different users
3. Start a chat between them
4. Send messages - they should appear instantly if WebSocket is working
5. Check browser console for "WebSocket connection established" message

## Troubleshooting

- **WebSocket not connecting?** Check browser console for errors
- **Messages not appearing?** Verify Pusher credentials in `.env`
- **Still using polling?** Check that `BROADCAST_DRIVER=pusher` in `.env`
- **CORS errors?** Ensure your Pusher app allows your domain

## Notes

- The system gracefully falls back to polling if WebSocket fails
- All features work with both WebSocket and polling
- No code changes needed - just configure Pusher credentials

