<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Construction AI - Intelligent Project Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(102, 126, 234, 0.5); }
            50% { box-shadow: 0 0 40px rgba(102, 126, 234, 0.8); }
        }
        
        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        
        .feature-card {
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .scroll-indicator {
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
    </style>
</head>
<body class="antialiased">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/90 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center">
                        <i class="fas fa-hard-hat text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Construction AI</span>
                </div>
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">Sign in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2.5 btn-primary text-white rounded-lg font-medium">Get Started</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center gradient-bg overflow-hidden pt-16">
        <!-- Background Decorations -->
        <div class="absolute top-20 left-10 w-20 h-20 bg-white/10 rounded-full floating"></div>
        <div class="absolute top-40 right-20 w-32 h-32 bg-white/10 rounded-full floating" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-20 left-20 w-24 h-24 bg-white/10 rounded-full floating" style="animation-delay: 4s;"></div>
        <div class="absolute bottom-40 right-10 w-16 h-16 bg-white/10 rounded-full floating" style="animation-delay: 1s;"></div>
        
        <!-- Grid Pattern -->
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 rounded-full text-white/90 text-sm font-medium mb-8 backdrop-blur-sm">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                AI-Powered Construction Assistant
            </div>
            
            <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight">
                Build Smarter with <br>
                <span class="text-yellow-300">Intelligent AI</span>
            </h1>
            
            <p class="text-xl text-white/90 mb-10 max-w-3xl mx-auto leading-relaxed">
                Streamline your construction projects with AI-powered document analysis, 
                instant cost estimation, and expert consultation available 24/7.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-purple-700 rounded-xl font-bold text-lg btn-secondary flex items-center gap-2">
                    <i class="fas fa-rocket"></i>
                    Start Free Trial
                </a>
                <a href="{{ route('login') }}" class="px-8 py-4 bg-white/20 text-white border-2 border-white/30 rounded-xl font-bold text-lg hover:bg-white/30 transition-all flex items-center gap-2">
                    <i class="fas fa-play"></i>
                    Watch Demo
                </a>
            </div>
            
            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-16 max-w-4xl mx-auto">
                <div class="text-center">
                    <p class="text-4xl font-bold text-white">10K+</p>
                    <p class="text-white/80 text-sm">Active Users</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-bold text-white">50K+</p>
                    <p class="text-white/80 text-sm">Projects Analyzed</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-bold text-white">99%</p>
                    <p class="text-white/80 text-sm">Accuracy Rate</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-bold text-white">24/7</p>
                    <p class="text-white/80 text-sm">AI Support</p>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white/60 scroll-indicator">
            <i class="fas fa-chevron-down text-2xl"></i>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Powerful Features for <span class="gradient-text">Construction Pros</span></h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Everything you need to manage construction projects efficiently with AI assistance.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-robot text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">AI Chat Assistant</h3>
                    <p class="text-gray-600 leading-relaxed">Get instant answers about building codes, regulations, safety protocols, and construction best practices.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-file-alt text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Document OCR</h3>
                    <p class="text-gray-600 leading-relaxed">Upload blueprints, permits, and contracts. Our AI extracts text and analyzes content instantly.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-calculator text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Cost Estimation</h3>
                    <p class="text-gray-600 leading-relaxed">Get accurate material and labor cost estimates powered by AI analysis of current market rates.</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-orange-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Safety Compliance</h3>
                    <p class="text-gray-600 leading-relaxed">Stay compliant with AI-powered safety checks and regulation updates for your projects.</p>
                </div>
                
                <!-- Feature 5 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-14 h-14 bg-pink-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-pink-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Project Analytics</h3>
                    <p class="text-gray-600 leading-relaxed">Track project progress, budgets, and timelines with intelligent AI-powered insights.</p>
                </div>
                
                <!-- Feature 6 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-mobile-alt text-indigo-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Mobile Ready</h3>
                    <p class="text-gray-600 leading-relaxed">Access your projects and AI assistant from any device, anywhere, anytime.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">How It <span class="gradient-text">Works</span></h2>
                <p class="text-xl text-gray-600">Get started in three simple steps</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 gradient-bg rounded-full flex items-center justify-center mx-auto mb-6 pulse-glow">
                        <span class="text-white text-3xl font-bold">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Create Account</h3>
                    <p class="text-gray-600">Sign up for free and set up your construction profile in minutes.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-20 h-20 gradient-bg rounded-full flex items-center justify-center mx-auto mb-6 pulse-glow" style="animation-delay: 0.5s;">
                        <span class="text-white text-3xl font-bold">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Upload Documents</h3>
                    <p class="text-gray-600">Add your project files, blueprints, and contracts for AI analysis.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-20 h-20 gradient-bg rounded-full flex items-center justify-center mx-auto mb-6 pulse-glow" style="animation-delay: 1s;">
                        <span class="text-white text-3xl font-bold">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Chat with AI</h3>
                    <p class="text-gray-600">Ask questions, get estimates, and manage projects with AI assistance.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 gradient-bg">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">Ready to Transform Your Construction Projects?</h2>
            <p class="text-xl text-white/90 mb-10">Join thousands of construction professionals using AI to build smarter and faster.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-purple-700 rounded-xl font-bold text-lg hover:bg-gray-100 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-rocket"></i>
                    Start Free Trial
                </a>
                <a href="{{ route('login') }}" class="px-8 py-4 bg-white/20 text-white border-2 border-white/30 rounded-xl font-bold text-lg hover:bg-white/30 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </a>
            </div>
            <p class="text-white/70 mt-6 text-sm">No credit card required • Free forever plan available</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center">
                            <i class="fas fa-hard-hat text-white text-lg"></i>
                        </div>
                        <span class="text-xl font-bold text-white">Construction AI</span>
                    </div>
                    <p class="text-sm">Intelligent assistant for modern construction projects.</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">API</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Company</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">About</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Privacy</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Terms</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Security</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm">&copy; {{ date('Y') }} Construction AI. All rights reserved.</p>
                <div class="flex gap-4 mt-4 md:mt-0">
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-gray-700 transition-colors">
                        <i class="fab fa-twitter text-white"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-gray-700 transition-colors">
                        <i class="fab fa-linkedin text-white"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-gray-700 transition-colors">
                        <i class="fab fa-github text-white"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>