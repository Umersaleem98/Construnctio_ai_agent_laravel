<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Construction AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .input-group:focus-within label {
            color: #667eea;
        }
        
        .input-group:focus-within i {
            color: #667eea;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .social-btn {
            transition: all 0.3s ease;
        }
        
        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 0.5; }
            100% { transform: scale(1.2); opacity: 0; }
        }
        
        .pulse-ring::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: inherit;
            animation: pulse-ring 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4 relative overflow-hidden">
    
    <!-- Background Decorations -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full floating" style="animation-delay: 0s;"></div>
    <div class="absolute top-40 right-20 w-32 h-32 bg-white/10 rounded-full floating" style="animation-delay: 2s;"></div>
    <div class="absolute bottom-20 left-20 w-24 h-24 bg-white/10 rounded-full floating" style="animation-delay: 4s;"></div>
    <div class="absolute bottom-40 right-10 w-16 h-16 bg-white/10 rounded-full floating" style="animation-delay: 1s;"></div>
    
    <!-- Grid Pattern Overlay -->
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>

    <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 gap-8 items-center relative z-10">
        
        <!-- Left Side - Branding -->
        <div class="text-white space-y-6 hidden lg:block">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm pulse-ring relative">
                    <i class="fas fa-hard-hat text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Construction AI</h1>
                    <p class="text-white/80">Intelligent Project Assistant</p>
                </div>
            </div>
            
            <h2 class="text-5xl font-bold leading-tight">
                Build Smarter with <span class="text-yellow-300">AI Power</span>
            </h2>
            
            <p class="text-xl text-white/90 leading-relaxed">
                Streamline your construction projects with intelligent document analysis, instant cost estimation, and expert AI consultation.
            </p>
            
            <div class="grid grid-cols-2 gap-4 pt-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <i class="fas fa-robot text-2xl mb-2 text-yellow-300"></i>
                    <p class="font-semibold">AI Chatbot</p>
                    <p class="text-sm text-white/70">24/7 Assistance</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <i class="fas fa-file-alt text-2xl mb-2 text-green-300"></i>
                    <p class="font-semibold">OCR Analysis</p>
                    <p class="text-sm text-white/70">Document Processing</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <i class="fas fa-calculator text-2xl mb-2 text-blue-300"></i>
                    <p class="font-semibold">Cost Estimation</p>
                    <p class="text-sm text-white/70">Instant Quotes</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <i class="fas fa-shield-alt text-2xl mb-2 text-red-300"></i>
                    <p class="font-semibold">Safety Guide</p>
                    <p class="text-sm text-white/70">Compliance Check</p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="glass-card rounded-3xl shadow-2xl p-8 md:p-10">
            <div class="text-center mb-8">
                <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg lg:hidden">
                    <i class="fas fa-hard-hat text-white text-3xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back</h2>
                <p class="text-gray-500">Sign in to continue to your dashboard</p>
            </div>

            @if(session('status'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <div class="input-group space-y-2">
                    <label class="text-sm font-semibold text-gray-600 transition-colors">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400 transition-colors"></i>
                        </div>
                        <input type="email" name="email" required value="{{ old('email') }}"
                               class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all outline-none text-gray-800"
                               placeholder="name@company.com">
                    </div>
                </div>
                
                <div class="input-group space-y-2">
                    <label class="text-sm font-semibold text-gray-600 transition-colors">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400 transition-colors"></i>
                        </div>
                        <input type="password" name="password" required id="password"
                               class="w-full pl-12 pr-12 py-4 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all outline-none text-gray-800"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-5 h-5 rounded-lg border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-purple-600 hover:text-purple-700 transition-colors">
                            Forgot password?
                        </a>
                    @endif
                </div>
                
                <button type="submit" class="w-full py-4 btn-primary text-white rounded-xl font-bold text-lg">
                    Sign In to Dashboard
                </button>
            </form>
            
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500 font-medium">Or continue with</span>
                </div>
            </div>
            
            <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center gap-3 py-4 border-2 border-gray-200 rounded-xl font-semibold text-gray-700 bg-white social-btn hover:border-purple-500 hover:text-purple-600">
                <svg class="h-5 w-5" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Continue with Google
            </a>
            
            <p class="text-center mt-8 text-gray-600">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-bold text-purple-600 hover:text-purple-700 transition-colors">
                    Create account now
                </a>
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (password.type === 'password') {
                password.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>