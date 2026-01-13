<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FriendZone - Find Your Perfect Match</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .font-poppins {
            font-family: 'Poppins', sans-serif;
        }
        .gradient-primary {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 50%, #ffb347 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 50%, #ffb347 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .gradient-hero {
            background: linear-gradient(135deg, rgba(255, 107, 53, 0.1) 0%, rgba(247, 147, 30, 0.1) 50%, rgba(255, 179, 71, 0.1) 100%);
        }
        .gradient-card {
            background: linear-gradient(135deg, rgba(255, 107, 53, 0.1) 0%, rgba(247, 147, 30, 0.1) 100%);
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-30px, -30px) rotate(180deg); }
        }
        .animate-float {
            animation: float 20s infinite ease-in-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease;
        }
        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(255, 107, 53, 0.2);
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(255, 107, 53, 0.15);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white shadow-md backdrop-blur-lg bg-opacity-95">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-heart text-2xl gradient-text"></i>
                    <span class="text-2xl font-poppins font-bold gradient-text">FriendZone</span>
                </div>
                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/home') }}" class="px-6 py-2.5 gradient-primary text-white rounded-full font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 flex items-center space-x-2">
                                <i class="fas fa-home"></i>
                                <span>Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-6 py-2.5 border-2 border-orange-600 text-orange-600 rounded-full font-semibold hover:bg-orange-600 hover:text-white transition-all duration-300 flex items-center space-x-2">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-6 py-2.5 gradient-primary text-white rounded-full font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 flex items-center space-x-2">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Get Started</span>
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="gradient-hero min-h-screen flex items-center relative overflow-hidden">
        <!-- Animated Background Elements -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-orange-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-amber-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float" style="animation-delay: 2s;"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 grid md:grid-cols-2 gap-12 items-center relative z-10">
            <!-- Hero Text -->
            <div class="animate-fade-in-up">
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-poppins font-black mb-6 leading-tight">
                    <span class="gradient-text">Find Your</span><br>
                    <span class="gradient-text">Perfect Match</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-600 mb-8 leading-relaxed">
                    Connect with like-minded people, build meaningful relationships, and discover your next great friendship. Join thousands of users finding their perfect match today.
                </p>
                <div class="flex flex-wrap gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/home') }}" class="px-8 py-4 gradient-primary text-white rounded-full font-bold text-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 flex items-center space-x-2">
                                <span>Go to Dashboard</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="px-8 py-4 gradient-primary text-white rounded-full font-bold text-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 flex items-center space-x-2">
                                <i class="fas fa-rocket"></i>
                                <span>Get Started Free</span>
                            </a>
                            <a href="{{ route('login') }}" class="px-8 py-4 border-2 border-orange-600 text-orange-600 rounded-full font-bold text-lg hover:bg-orange-600 hover:text-white transition-all duration-300 flex items-center space-x-2">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Sign In</span>
                            </a>
                        @endauth
                    @endif
                </div>
                <div class="mt-12 flex items-center space-x-8 text-gray-600">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        <span class="font-semibold">100% Free</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-users text-orange-500 text-xl"></i>
                        <span class="font-semibold">10K+ Members</span>
                    </div>
                </div>
            </div>
            
            <!-- Hero Image -->
            <div class="relative">
                <div class="relative z-10 transform hover:scale-105 transition-transform duration-300">
                    <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=800&h=600&fit=crop" 
                         alt="Friends connecting" 
                         class="rounded-3xl shadow-2xl w-full">
                    <!-- Decorative Gradient Overlay -->
                    <div class="absolute inset-0 gradient-primary opacity-10 rounded-3xl blur-2xl -z-10"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-poppins font-bold mb-4 gradient-text">
                    Why Choose FriendZone?
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Everything you need to find meaningful connections and build lasting relationships
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card p-8 rounded-2xl gradient-card border border-orange-100 transition-all duration-300">
                    <div class="w-20 h-20 gradient-primary rounded-full flex items-center justify-center mb-6 mx-auto shadow-lg">
                        <i class="fas fa-brain text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-poppins font-bold mb-4 text-center">Smart Matching</h3>
                    <p class="text-gray-600 text-center leading-relaxed">
                        Our advanced AI algorithm finds compatible matches based on your interests, preferences, and personality traits.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card p-8 rounded-2xl gradient-card border border-orange-100 transition-all duration-300">
                    <div class="w-20 h-20 gradient-primary rounded-full flex items-center justify-center mb-6 mx-auto shadow-lg">
                        <i class="fas fa-comments text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-poppins font-bold mb-4 text-center">Real-time Chat</h3>
                    <p class="text-gray-600 text-center leading-relaxed">
                        Connect instantly with your matches through our secure, encrypted messaging platform with video call support.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card p-8 rounded-2xl gradient-card border border-orange-100 transition-all duration-300">
                    <div class="w-20 h-20 gradient-primary rounded-full flex items-center justify-center mb-6 mx-auto shadow-lg">
                        <i class="fas fa-shield-alt text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-poppins font-bold mb-4 text-center">Safe & Secure</h3>
                    <p class="text-gray-600 text-center leading-relaxed">
                        Your privacy is our priority. We protect your data with industry-leading security and verification systems.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card p-8 rounded-2xl gradient-card border border-orange-100 transition-all duration-300">
                    <div class="w-20 h-20 gradient-primary rounded-full flex items-center justify-center mb-6 mx-auto shadow-lg">
                        <i class="fas fa-users-cog text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-poppins font-bold mb-4 text-center">Community Features</h3>
                    <p class="text-gray-600 text-center leading-relaxed">
                        Join communities, share posts, participate in events, and connect with people who share your passions.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card p-8 rounded-2xl gradient-card border border-orange-100 transition-all duration-300">
                    <div class="w-20 h-20 gradient-primary rounded-full flex items-center justify-center mb-6 mx-auto shadow-lg">
                        <i class="fas fa-mobile-alt text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-poppins font-bold mb-4 text-center">Mobile Friendly</h3>
                    <p class="text-gray-600 text-center leading-relaxed">
                        Access FriendZone anytime, anywhere with our fully responsive design optimized for all devices.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card p-8 rounded-2xl gradient-card border border-orange-100 transition-all duration-300">
                    <div class="w-20 h-20 gradient-primary rounded-full flex items-center justify-center mb-6 mx-auto shadow-lg">
                        <i class="fas fa-heart text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-poppins font-bold mb-4 text-center">Super Likes</h3>
                    <p class="text-gray-600 text-center leading-relaxed">
                        Stand out from the crowd with Super Likes and let someone special know you're really interested.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 gradient-hero">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-poppins font-bold mb-4 gradient-text">
                    What Our Users Say
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Real stories from people who found meaningful connections on FriendZone
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="testimonial-card bg-white p-8 rounded-2xl shadow-lg transition-all duration-300">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 rounded-full gradient-primary flex items-center justify-center text-white text-2xl font-bold mr-4">
                            S
                        </div>
                        <div>
                            <h4 class="font-poppins font-bold text-lg">Sarah Johnson</h4>
                            <p class="text-gray-500 text-sm">Found her best friend</p>
                        </div>
                    </div>
                    <div class="flex mb-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                    <p class="text-gray-700 leading-relaxed italic">
                        "FriendZone helped me find my perfect match! The smart matching algorithm really understands what I'm looking for. I've met amazing people and made lifelong friendships."
                    </p>
                </div>

                <!-- Testimonial 2 -->
                <div class="testimonial-card bg-white p-8 rounded-2xl shadow-lg transition-all duration-300">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 rounded-full gradient-primary flex items-center justify-center text-white text-2xl font-bold mr-4">
                            M
                        </div>
                        <div>
                            <h4 class="font-poppins font-bold text-lg">Michael Chen</h4>
                            <p class="text-gray-500 text-sm">Loves the community</p>
                        </div>
                    </div>
                    <div class="flex mb-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                    <p class="text-gray-700 leading-relaxed italic">
                        "The community features are fantastic! I've joined several groups that match my interests, and the events feature makes it easy to meet people in person. Highly recommend!"
                    </p>
                </div>

                <!-- Testimonial 3 -->
                <div class="testimonial-card bg-white p-8 rounded-2xl shadow-lg transition-all duration-300">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 rounded-full gradient-primary flex items-center justify-center text-white text-2xl font-bold mr-4">
                            E
                        </div>
                        <div>
                            <h4 class="font-poppins font-bold text-lg">Emma Rodriguez</h4>
                            <p class="text-gray-500 text-sm">Met her soulmate</p>
                        </div>
                    </div>
                    <div class="flex mb-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                    <p class="text-gray-700 leading-relaxed italic">
                        "I was skeptical at first, but FriendZone exceeded all my expectations! The chat interface is smooth, and I love how safe and secure the platform feels. Found my person!"
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="gradient-primary rounded-3xl p-12 md:p-16 shadow-2xl">
                <h2 class="text-4xl md:text-5xl font-poppins font-bold text-white mb-6">
                    Ready to Find Your Perfect Match?
                </h2>
                <p class="text-xl text-white opacity-90 mb-8 max-w-2xl mx-auto">
                    Join thousands of happy users who found meaningful connections. Start your journey today!
                </p>
                @if (Route::has('register'))
                    @guest
                        <a href="{{ route('register') }}" class="inline-block px-10 py-5 bg-white text-orange-600 rounded-full font-bold text-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
                            <i class="fas fa-rocket mr-2"></i>Create Free Account
                        </a>
                    @endguest
                @endif
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <i class="fas fa-heart text-2xl gradient-text"></i>
                        <span class="text-xl font-poppins font-bold gradient-text">FriendZone</span>
                    </div>
                    <p class="text-gray-400">
                        Connect with like-minded people and build meaningful relationships.
                    </p>
                </div>
                <div>
                    <h4 class="font-poppins font-bold mb-4">Product</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Features</a></li>
                        <li><a href="#" class="hover:text-white transition">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition">Safety</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-poppins font-bold mb-4">Company</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">About</a></li>
                        <li><a href="#" class="hover:text-white transition">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition">Careers</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-poppins font-bold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact</a></li>
                        <li><a href="#" class="hover:text-white transition">Privacy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} FriendZone. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
