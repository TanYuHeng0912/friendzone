<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FriendZone</title>
  <style>
    /* CSS styles */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #374151;
      color: #fff;
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    nav ul {
      list-style-type: none;
      margin: 0;
      padding: 0;
      display: flex;
    }

    nav li {
      margin-right: 20px;
    }

    nav a {
      color: #fff;
      text-decoration: none;
    }

    .hero {
      display: flex;
      align-items: center;
      padding: 50px;
    }

    .hero-content {
      flex: 1;
    }

    .hero-content h1 {
      font-size: 40px;
      margin-bottom: 20px;
    }

    .hero-content p {
      font-family: 'Roboto', Arial, sans-serif;
      font-size: 18px;
      margin-bottom: 30px;
    }

    .btn {
      background-color: #6366f1; /* Purple color */
      color: #ffffff; /* White text color */
      border: none; /* Remove button border */
      border-radius: 9999px; /* Create a rounded button shape */
      padding: 12px 24px; /* Add some padding around the button */
      font-size: 16px; /* Set the font size */
      cursor: pointer; /* Change the cursor to a pointer on hover */
      text-decoration: none;
      transition: background-color 0.3s ease; /* Add a smooth transition effect */
    }

    .btn:hover {
      background-color: #4f46e5; /* Slightly darker purple on hover */
    }
    .hero-image {
      flex: 1;
      text-align: right;
    }

    .hero-image img {
      max-width: 100%;
      height: auto;
      border-radius: 50%;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    }
    
    .hero-image::before {
      content: "";
      position: absolute;
      top: 60%;
      left: 30%;
      transform: translate(-50%, -50%);
      width: 90%;
      height: 90%;
      border-radius: 50%;
      background-color: rgba(76, 110, 245, 0.1);
      z-index: -1;
    }

       .footer {
      background-color: #374151;
      padding: 20px;
      text-align: center;
      color:#fff;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <header>  

    <div class="logo">Friend Zone</div>
    <nav>

    </nav>
          <div class="links">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/home') }}"class="btn">Home</a>
                @else
                @endauth
            @endif
        </div>
  </header>



  <div class="hero">
    <div class="hero-content">
      <h1>Recognize your friends instantly</h1>
      <p>Our advanced facial recognition technology helps you identify and connect with friends from your photos with incredible accuracy.</p>
                <div class="links">
            @if (Route::has('login'))
                @auth
                   
                @else
                &nbsp;
        
                    <a href="{{ route('login') }}"class="btn">   &nbsp;  Login &nbsp;</a>
                &nbsp;&nbsp;&nbsp;
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"class="btn">Register</a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
    <div class="hero-image">
      <img src="https://storage.googleapis.com/uxpilot-auth.appspot.com/9f0bccdf8d-47cfb50c084636305116.png" alt="Group photo">
    </div>
  </div>
   <div class="footer">
    © 2025 FriendZone. All rights reserved.
  </div>
</body>
</html>
 

