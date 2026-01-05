# Mobile Testing Setup Guide

## Quick Start

1. **Run the servers:**
   - Double-click `start-server.bat` (Windows)
   - Or run manually:
     ```bash
     php artisan serve --host=0.0.0.0 --port=8000
     php artisan websockets:serve --host=0.0.0.0 --port=6001
     ```

2. **Connect your phone:**
   - Make sure your phone is on the **same WiFi network** as your computer
   - Open browser on phone and go to: `http://192.168.0.127:8000`

## Your Network Details

- **Local IP Address:** 192.168.0.127
- **Laravel App URL:** http://192.168.0.127:8000
- **WebSocket URL:** ws://192.168.0.127:6001

## Important Notes

1. **Firewall:** Windows Firewall may block connections. If you can't access from phone:
   - Go to Windows Defender Firewall
   - Allow PHP through firewall for Private networks
   - Or temporarily disable firewall for testing

2. **Same Network:** Your phone and computer must be on the same WiFi network

3. **HTTPS:** For production, you'll need HTTPS. For local testing, HTTP is fine.

4. **WebSocket:** Make sure both servers are running:
   - Laravel server (port 8000)
   - WebSocket server (port 6001)

## Troubleshooting

### Can't access from phone:
- Check firewall settings
- Verify both devices are on same WiFi
- Try accessing `http://192.168.0.127:8000` from your computer's browser first
- Check if IP address changed: run `ipconfig` and look for IPv4 Address

### WebSocket not working:
- Make sure WebSocket server is running on port 6001
- Check `.env` file has correct WebSocket settings:
  ```
  PUSHER_HOST=192.168.0.127
  PUSHER_PORT=6001
  PUSHER_SCHEME=http
  ```

### Video/Camera not working:
- Mobile browsers require HTTPS for camera/microphone access
- For local testing, you may need to use a tool like ngrok to create HTTPS tunnel
- Or test on desktop browser first

## Using ngrok for HTTPS (REQUIRED for Camera/Microphone on Mobile)

**Mobile browsers require HTTPS for camera/microphone access!**

### Quick Setup with ngrok:

1. **Install ngrok:**
   - Download from: https://ngrok.com/download
   - Extract to a folder (or add to PATH)

2. **Start your Laravel server:**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

3. **Start ngrok:**
   ```bash
   ngrok http 8000
   ```

4. **Get your HTTPS URL:**
   - ngrok will show something like: `https://abc123.ngrok.io`
   - Copy this HTTPS URL

5. **Update WebSocket configuration:**
   - Open `.env` file
   - Update these lines:
     ```
     PUSHER_HOST=abc123.ngrok.io
     PUSHER_PORT=443
     PUSHER_SCHEME=https
     ```
   - Replace `abc123.ngrok.io` with your actual ngrok domain

6. **Clear config cache:**
   ```bash
   php artisan config:clear
   ```

7. **Access from phone:**
   - Use the HTTPS URL from ngrok: `https://abc123.ngrok.io`
   - Camera/microphone should now work!

### Alternative: Use ngrok with custom domain (Free)

If you sign up for free ngrok account, you can get a static domain:
- Sign up at: https://dashboard.ngrok.com/signup
- Get your authtoken
- Run: `ngrok config add-authtoken YOUR_TOKEN`
- Use: `ngrok http 8000 --domain=your-domain.ngrok-free.app`

### Note:
- ngrok free tier shows a warning page (click "Visit Site" to continue)
- For production, use a real SSL certificate

