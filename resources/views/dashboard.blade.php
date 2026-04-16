@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 rounded-2xl shadow-xl p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white opacity-5 rounded-full -ml-24 -mb-24"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-medium backdrop-blur-sm">
                        {{ now()->format('l, F j, Y') }}
                    </span>
                    <span class="flex items-center gap-1 px-3 py-1 bg-green-500/20 rounded-full text-xs font-medium text-green-100 backdrop-blur-sm">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        System Online
                    </span>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold mb-2">Welcome back, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h1>
                <p class="text-blue-100 text-lg max-w-xl">Ready to streamline your construction projects? Start a new conversation or upload documents for AI-powered analysis.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('chat.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-blue-700 rounded-xl font-semibold hover:bg-blue-50 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-plus"></i>
                    New Chat
                </a>
                <button onclick="document.getElementById('quickUploadModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-500/30 text-white border border-white/30 rounded-xl font-semibold hover:bg-blue-500/50 transition-all backdrop-blur-sm">
                    <i class="fas fa-upload"></i>
                    Upload Doc
                </button>
            </div>
        </div>
    </div>

    <!-- AI Provider Status -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-robot text-purple-500"></i>
            AI Provider Status
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Gemini Status -->
            <div class="flex items-center justify-between p-4 rounded-xl {{ $aiStatus['gemini']['configured'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $aiStatus['gemini']['configured'] ? 'bg-green-100' : 'bg-red-100' }}">
                        <i class="fab fa-google {{ $aiStatus['gemini']['configured'] ? 'text-green-600' : 'text-red-600' }}"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Google Gemini</p>
                        <p class="text-xs {{ $aiStatus['gemini']['configured'] ? 'text-green-600' : 'text-red-600' }}">
                            {{ $aiStatus['gemini']['configured'] ? 'Configured (' . $aiStatus['gemini']['key_preview'] . ')' : 'Not configured' }}
                        </p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $aiStatus['gemini']['configured'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $aiStatus['gemini']['configured'] ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <!-- OpenAI Status -->
            <div class="flex items-center justify-between p-4 rounded-xl {{ $aiStatus['openai']['configured'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $aiStatus['openai']['configured'] ? 'bg-green-100' : 'bg-red-100' }}">
                        <i class="fas fa-brain {{ $aiStatus['openai']['configured'] ? 'text-green-600' : 'text-red-600' }}"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">OpenAI ChatGPT</p>
                        <p class="text-xs {{ $aiStatus['openai']['configured'] ? 'text-green-600' : 'text-red-600' }}">
                            {{ $aiStatus['openai']['configured'] ? 'Configured (' . $aiStatus['openai']['key_preview'] . ')' : 'Not configured' }}
                        </p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $aiStatus['openai']['configured'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $aiStatus['openai']['configured'] ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
        <p class="text-sm text-gray-500 mt-3">
            <i class="fas fa-info-circle mr-1"></i>
            Fallback order: <strong>{{ $aiStatus['fallback_order'] }}</strong>. If one fails, the other will automatically respond.
        </p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Chats -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                    <i class="fas fa-comments text-blue-600 text-2xl"></i>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-lg">
                    {{ $stats['chats_today'] }} today
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['total_chats'] }}</h3>
            <p class="text-sm text-gray-500 font-medium">Total Conversations</p>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-400">{{ $stats['chats_this_week'] }} this week • {{ $stats['chats_this_month'] }} this month</p>
            </div>
        </div>

        <!-- Total Messages -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center group-hover:bg-purple-100 transition-colors">
                    <i class="fas fa-message text-purple-600 text-2xl"></i>
                </div>
                <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-1 rounded-lg">
                    {{ $stats['messages_today'] }} today
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['total_messages'] }}</h3>
            <p class="text-sm text-gray-500 font-medium">Total Messages</p>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-400">{{ $stats['user_messages'] }} user • {{ $stats['ai_messages'] }} AI</p>
            </div>
        </div>

        <!-- Documents -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center group-hover:bg-orange-100 transition-colors">
                    <i class="fas fa-file-alt text-orange-600 text-2xl"></i>
                </div>
                <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-lg">
                    {{ $stats['documents_with_text'] }} processed
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['total_documents'] }}</h3>
            <p class="text-sm text-gray-500 font-medium">Documents Uploaded</p>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-400">
                    @foreach($stats['documents_by_type'] as $type => $count)
                        {{ $count }} {{ strtoupper($type) }}@if(!$loop->last), @endif
                    @endforeach
                </p>
            </div>
        </div>

        <!-- AI Status -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center group-hover:bg-green-100 transition-colors">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <span class="flex items-center gap-1 text-xs font-medium text-green-600">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Online
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">Dual AI</h3>
            <p class="text-sm text-gray-500 font-medium">Gemini + ChatGPT</p>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-400">Auto-fallback enabled</p>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: All Records (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- All Chats Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-comments text-blue-500"></i>
                        All Conversations ({{ $allChats->count() }})
                    </h3>
                    <div class="flex gap-2">
                        <input type="text" id="searchChats" placeholder="Search chats..." 
                               class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        <a href="{{ route('chat.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            View All
                        </a>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 font-semibold text-gray-700">Chat Title</th>
                                <th class="px-6 py-3 font-semibold text-gray-700">Messages</th>
                                <th class="px-6 py-3 font-semibold text-gray-700">Last Activity</th>
                                <th class="px-6 py-3 font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="chatsTable">
                            @forelse($allChats as $chat)
                                <tr class="hover:bg-gray-50 transition-colors chat-row" data-title="{{ strtolower($chat->title) }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-comment text-blue-600 text-xs"></i>
                                            </div>
                                            <span class="font-medium text-gray-900">{{ Str::limit($chat->title, 40) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs">
                                            {{ $chat->messages_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $chat->updated_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('chat.show', $chat) }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                            Continue <i class="fas fa-arrow-right text-xs ml-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        No chats yet. <a href="{{ route('chat.create') }}" class="text-blue-600 hover:underline">Start one now</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- All Documents Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-folder-open text-orange-500"></i>
                        All Documents ({{ $allDocuments->count() }})
                    </h3>
                    <div class="flex gap-2">
                        <input type="text" id="searchDocs" placeholder="Search documents..." 
                               class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        <a href="{{ route('documents.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            View All
                        </a>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 font-semibold text-gray-700">Document Name</th>
                                <th class="px-6 py-3 font-semibold text-gray-700">Type</th>
                                <th class="px-6 py-3 font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 font-semibold text-gray-700">Uploaded</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="docsTable">
                            @forelse($allDocuments as $doc)
                                <tr class="hover:bg-gray-50 transition-colors doc-row" data-name="{{ strtolower($doc->original_name) }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-file-{{ in_array($doc->file_type, ['png','jpg','jpeg']) ? 'image' : 'pdf' }} text-orange-600 text-xs"></i>
                                            </div>
                                            <span class="font-medium text-gray-900">{{ Str::limit($doc->original_name, 40) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-lg text-xs uppercase">
                                            {{ $doc->file_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($doc->ocr_text)
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-lg text-xs">
                                                <i class="fas fa-check mr-1"></i>Processed
                                            </span>
                                        @else
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs">
                                                <i class="fas fa-clock mr-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $doc->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        No documents uploaded. <button onclick="document.getElementById('quickUploadModal').classList.remove('hidden')" class="text-blue-600 hover:underline">Upload one now</button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar Widgets (1/3 width) -->
        <div class="space-y-6">
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-bolt text-yellow-500"></i>
                    Quick Actions
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('chat.create') }}" class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 hover:bg-blue-100 transition-colors group">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">New Chat</p>
                            <p class="text-xs text-gray-500">Start AI conversation</p>
                        </div>
                    </a>
                    
                    <button onclick="document.getElementById('quickUploadModal').classList.remove('hidden')" class="w-full flex items-center gap-3 p-3 rounded-xl bg-orange-50 hover:bg-orange-100 transition-colors group text-left">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-upload"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Upload Document</p>
                            <p class="text-xs text-gray-500">PDF, PNG, JPG</p>
                        </div>
                    </button>
                    
                    <a href="{{ route('chat.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-purple-50 hover:bg-purple-100 transition-colors group">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Chat History</p>
                            <p class="text-xs text-gray-500">View all conversations</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-clock text-blue-500"></i>
                        Recent Activity
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($recentChats->take(3) as $chat)
                        <a href="{{ route('chat.show', $chat) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-comment text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 text-sm truncate">{{ $chat->title }}</p>
                                <p class="text-xs text-gray-500">{{ $chat->updated_at->diffForHumans() }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No recent activity</p>
                    @endforelse
                </div>
            </div>

            <!-- Monthly Activity Chart -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-green-500"></i>
                    Monthly Activity
                </h3>
                <div class="space-y-2">
                    @forelse($monthlyActivity as $month => $count)
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-500 w-16">{{ $month }}</span>
                            <div class="flex-1 bg-gray-100 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(100, ($count / max($monthlyActivity)) * 100) }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-700 w-8">{{ $count }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No activity data</p>
                    @endforelse
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl border border-blue-100 p-5">
                <h4 class="font-bold text-blue-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-lightbulb text-yellow-500"></i>
                    Pro Tips
                </h4>
                <ul class="space-y-2 text-sm text-blue-800">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
                        <span>Upload blueprints for instant material estimation</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
                        <span>Reference multiple docs in a single chat</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
                        <span>Both Gemini and ChatGPT work with auto-fallback</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Quick Upload Modal -->
<div id="quickUploadModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900">Quick Upload</h3>
            <button onclick="document.getElementById('quickUploadModal').classList.add('hidden')" class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors">
                <i class="fas fa-times text-gray-500"></i>
            </button>
        </div>
        
        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" id="quickUploadForm">
            @csrf
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer" id="dropZone">
                <input type="file" name="document" id="quickFileInput" accept=".pdf,.png,.jpg,.jpeg" class="hidden" required>
                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                <p class="text-gray-600 font-medium mb-1">Drop your file here</p>
                <p class="text-sm text-gray-400">or click to browse</p>
                <p class="text-xs text-gray-400 mt-3">PDF, PNG, JPG up to 10MB</p>
            </div>
            
            <div id="quickFilePreview" class="hidden mt-4 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-3">
                    <i class="fas fa-file text-2xl text-orange-500"></i>
                    <div class="flex-1 text-left">
                        <p id="quickFileName" class="font-medium text-gray-900 text-sm truncate"></p>
                        <p id="quickFileSize" class="text-xs text-gray-500"></p>
                    </div>
                    <button type="button" onclick="clearQuickFile()" class="text-gray-400 hover:text-red-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" id="quickUploadBtn" class="w-full mt-4 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <i class="fas fa-upload mr-2"></i>
                Upload & Process
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Search functionality for chats
    document.getElementById('searchChats').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.chat-row');
        
        rows.forEach(row => {
            const title = row.getAttribute('data-title');
            row.style.display = title.includes(searchTerm) ? '' : 'none';
        });
    });

    // Search functionality for documents
    document.getElementById('searchDocs').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.doc-row');
        
        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            row.style.display = name.includes(searchTerm) ? '' : 'none';
        });
    });

    // Quick upload handling
    const quickFileInput = document.getElementById('quickFileInput');
    const dropZone = document.getElementById('dropZone');
    const quickFilePreview = document.getElementById('quickFilePreview');
    const quickUploadBtn = document.getElementById('quickUploadBtn');

    dropZone.addEventListener('click', () => quickFileInput.click());
    
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-blue-500', 'bg-blue-50');
    });
    
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
    });
    
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        if (e.dataTransfer.files.length) {
            quickFileInput.files = e.dataTransfer.files;
            handleQuickFile();
        }
    });

    quickFileInput.addEventListener('change', handleQuickFile);

    function handleQuickFile() {
        const file = quickFileInput.files[0];
        if (file) {
            document.getElementById('quickFileName').textContent = file.name;
            document.getElementById('quickFileSize').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            quickFilePreview.classList.remove('hidden');
            dropZone.classList.add('hidden');
            quickUploadBtn.disabled = false;
        }
    }

    function clearQuickFile() {
        quickFileInput.value = '';
        quickFilePreview.classList.add('hidden');
        dropZone.classList.remove('hidden');
        quickUploadBtn.disabled = true;
    }
</script>
@endpush
@endsection