<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Construction AI') }} - @yield('title', 'Dashboard')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        .chat-message {
            animation: messageSlide 0.3s ease-out;
        }
        
        @keyframes messageSlide {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        
        .typing-indicator span {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #94a3b8;
            border-radius: 50%;
            animation: typingBounce 1.4s infinite ease-in-out both;
        }
        
        .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
        .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }
        
        @keyframes typingBounce {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; }
            40% { transform: scale(1); opacity: 1; }
        }
        
        .btn-press:active {
            transform: scale(0.98);
        }
        
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        @auth
        <aside id="sidebar" class="sidebar w-72 bg-slate-900 text-white flex flex-col h-screen fixed left-0 top-0 z-50 transition-transform duration-300 lg:translate-x-0 -translate-x-full">
            <!-- Logo -->
            <div class="p-6 border-b border-slate-800">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-blue-500/25 transition-shadow">
                        <i class="fas fa-hard-hat text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg tracking-tight">Construction AI</h1>
                        <p class="text-xs text-slate-400 font-medium">Intelligent Assistant</p>
                    </div>
                </a>
            </div>
            
            <!-- New Chat Button -->
            <div class="p-4">
                <a href="{{ route('chat.create') }}" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-blue-900/20 btn-press">
                    <i class="fas fa-plus"></i>
                    <span class="font-semibold">New Conversation</span>
                </a>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto px-3 space-y-1 custom-scrollbar">
                <p class="px-4 text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 mt-2">Menu</p>
                
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('chat.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-comments w-5 text-center"></i>
                    <span>All Chats</span>
                    @if(auth()->user()->chats()->count() > 0)
                        <span class="ml-auto bg-slate-700 text-xs px-2 py-1 rounded-full">{{ auth()->user()->chats()->count() }}</span>
                    @endif
                </a>
                
                <a href="{{ route('documents.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('documents.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-folder-open w-5 text-center"></i>
                    <span>Documents</span>
                    @if(auth()->user()->documents()->count() > 0)
                        <span class="ml-auto bg-slate-700 text-xs px-2 py-1 rounded-full">{{ auth()->user()->documents()->count() }}</span>
                    @endif
                </a>
                
                <p class="px-4 text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 mt-6">Recent Chats</p>
                
                @php
                    $recentChats = auth()->user()->chats()->latest()->take(8)->get();
                @endphp
                
                @forelse($recentChats as $chatItem)
                    <a href="{{ route('chat.show', $chatItem) }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->route('chat')?->id === $chatItem->id ? 'bg-slate-800 text-white border-l-2 border-blue-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200' }} transition-all group">
                        <i class="fas fa-comment-alt text-slate-500 group-hover:text-blue-400 text-xs"></i>
                        <span class="truncate flex-1">{{ Str::limit($chatItem->title, 25) }}</span>
                    </a>
                @empty
                    <p class="px-4 text-sm text-slate-600 italic">No recent chats</p>
                @endforelse
            </nav>
            
            <!-- User Profile -->
            <div class="p-4 border-t border-slate-800">
                <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-800 transition-colors cursor-pointer group">
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=3b82f6&color=fff' }}" 
                         alt="{{ auth()->user()->name }}" 
                         class="w-10 h-10 rounded-full border-2 border-slate-600 group-hover:border-blue-500 transition-colors">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="relative">
                        <button onclick="toggleUserMenu()" class="text-slate-400 hover:text-white transition-colors p-1">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div id="userMenu" class="hidden absolute bottom-full right-0 mb-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-50">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <a href="{{ route('documents.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-cog mr-2"></i>Settings
                            </a>
                            <hr class="my-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Mobile Overlay -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>
        @endauth
        
        <!-- Main Content -->
        <main class="flex-1 {{ auth()->check() ? 'lg:ml-72' : '' }} min-h-screen flex flex-col">
            @auth
            <!-- Top Header -->
            <header class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-30 px-6 py-4">
                <div class="flex items-center justify-between max-w-7xl mx-auto">
                    <div class="flex items-center gap-4">
                        <button onclick="toggleSidebar()" class="lg:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-bars text-gray-600 text-xl"></i>
                        </button>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">@yield('header', 'Dashboard')</h2>
                            <p class="text-sm text-gray-500 hidden sm:block">{{ now()->format('F j, Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <button class="p-2.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-all relative" onclick="showToast('Notifications coming soon!', 'warning')">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                        </button>
                        
                        <button class="p-2.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-all" onclick="showToast('Help center coming soon!', 'info')">
                            <i class="fas fa-question-circle text-lg"></i>
                        </button>
                        
                        <div class="h-8 w-px bg-gray-200 mx-1"></div>
                        
                        <a href="{{ route('chat.create') }}" class="hidden sm:flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-medium btn-press">
                            <i class="fas fa-plus text-sm"></i>
                            <span>New Chat</span>
                        </a>
                    </div>
                </div>
            </header>
            @endauth
            
            <!-- Page Content -->
            <div class="flex-1 p-6">
                <div class="max-w-7xl mx-auto">
                    @if(session('success'))
                        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3 animate-fade-in" role="alert">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            <span class="font-medium">{{ session('success') }}</span>
                            <button onclick="this.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-3 animate-fade-in" role="alert">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                            <span class="font-medium">{{ session('error') }}</span>
                            <button onclick="this.parentElement.remove()" class="ml-auto text-red-600 hover:text-red-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            </div>
            
            <!-- Footer -->
            @auth
            <footer class="bg-white border-t border-gray-200 py-6 px-6 mt-auto">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} Construction AI. All rights reserved.</p>
                    <div class="flex items-center gap-6">
                        <a href="#" class="hover:text-gray-700 transition-colors">Privacy</a>
                        <a href="#" class="hover:text-gray-700 transition-colors">Terms</a>
                        <a href="#" class="hover:text-gray-700 transition-colors">Support</a>
                    </div>
                </div>
            </footer>
            @endauth
        </main>
    </div>
    
    <!-- Toast Container -->
    <div id="toastContainer"></div>

    <script>
        // Sidebar toggle for mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar && overlay) {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('hidden');
            }
        }
        
        // User menu toggle
        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }
        
        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('userMenu');
            const button = event.target.closest('button[onclick="toggleUserMenu()"]');
            
            if (menu && !menu.contains(event.target) && !button) {
                menu.classList.add('hidden');
            }
        });
        
        // Toast notification system
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            
            const colors = {
                success: 'border-l-4 border-green-500 text-green-800',
                error: 'border-l-4 border-red-500 text-red-800',
                warning: 'border-l-4 border-yellow-500 text-yellow-800',
                info: 'border-l-4 border-blue-500 text-blue-800'
            };
            
            const icons = {
                success: 'fa-check-circle text-green-500',
                error: 'fa-exclamation-circle text-red-500',
                warning: 'fa-exclamation-triangle text-yellow-500',
                info: 'fa-info-circle text-blue-500'
            };
            
            toast.className = `toast ${colors[type]} bg-white px-6 py-4 rounded-lg shadow-2xl flex items-center gap-3 min-w-[300px]`;
            toast.innerHTML = `
                <i class="fas ${icons[type]} text-xl"></i>
                <span class="font-medium flex-1">${message}</span>
                <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => toast.classList.add('show'), 10);
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }
        
        // Auto-resize textareas
        document.querySelectorAll('textarea[data-auto-resize]').forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>