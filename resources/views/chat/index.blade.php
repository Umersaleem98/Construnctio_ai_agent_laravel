@extends('layouts.app')

@section('title', 'All Chats')
@section('header', 'Conversations')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold mb-1">Your Conversations</h2>
                <p class="text-blue-100">Manage and continue your AI chat sessions</p>
            </div>
            <a href="{{ route('chat.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-blue-700 rounded-xl font-semibold hover:bg-blue-50 transition-all shadow-lg">
                <i class="fas fa-plus"></i>
                New Chat
            </a>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-comments text-blue-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $chats->count() }}</p>
                <p class="text-xs text-gray-500">Total Chats</p>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-comment-dots text-green-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $chats->sum(fn($chat) => $chat->messages->count()) }}</p>
                <p class="text-xs text-gray-500">Total Messages</p>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-purple-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $chats->where('updated_at', '>=', now()->subDay())->count() }}</p>
                <p class="text-xs text-gray-500">Active Today</p>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-file-alt text-orange-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $documents->count() }}</p>
                <p class="text-xs text-gray-500">Documents</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchChats" placeholder="Search conversations..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none">
            </div>
            <div class="flex gap-2">
                <select id="sortChats" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-blue-500 outline-none text-sm">
                    <option value="recent">Most Recent</option>
                    <option value="oldest">Oldest First</option>
                    <option value="messages">Most Messages</option>
                </select>
                <button onclick="toggleView()" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors" title="Toggle View">
                    <i class="fas fa-th-large text-gray-600"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Chats Grid -->
    @if($chats->count() > 0)
        <div id="chatsContainer" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($chats as $chat)
                <div class="chat-card bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all group" data-title="{{ strtolower($chat->title) }}">
                    <div class="p-5">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center group-hover:from-blue-200 group-hover:to-blue-300 transition-all">
                                <i class="fas fa-comment-alt text-blue-600"></i>
                            </div>
                            <div class="flex items-center gap-1">
                                @if($chat->messages->count() > 0)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-lg font-medium">
                                        {{ $chat->messages->count() }} msgs
                                    </span>
                                @endif
                                <div class="relative">
                                    <button onclick="toggleChatMenu('{{ $chat->id }}')" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div id="chatMenu{{ $chat->id }}" class="hidden absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-10">
                                        <a href="{{ route('chat.show', $chat) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-eye mr-2"></i>View Chat
                                        </a>
                                        <button onclick="renameChat('{{ $chat->id }}', '{{ $chat->title }}')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-edit mr-2"></i>Rename
                                        </button>
                                        <hr class="my-2">
                                        <form action="{{ route('chat.destroy', $chat) }}" method="POST" onsubmit="return confirm('Delete this chat permanently?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <i class="fas fa-trash mr-2"></i>Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <a href="{{ route('chat.show', $chat) }}" class="block group-hover:text-blue-600 transition-colors">
                            <h3 class="font-bold text-gray-900 mb-2 line-clamp-1">{{ $chat->title }}</h3>
                        </a>
                        
                        @if($chat->messages->last())
                            <p class="text-sm text-gray-500 line-clamp-2 mb-4 h-10">
                                {{ Str::limit($chat->messages->last()->content, 100) }}
                            </p>
                        @else
                            <p class="text-sm text-gray-400 italic line-clamp-2 mb-4 h-10">No messages yet. Start the conversation...</p>
                        @endif
                        
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <i class="far fa-clock"></i>
                                <span>{{ $chat->updated_at->diffForHumans() }}</span>
                            </div>
                            <a href="{{ route('chat.show', $chat) }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 flex items-center gap-1 group-hover:gap-2 transition-all">
                                Continue <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                    
                    @if($chat->messages->count() > 0 && $chat->messages->last()->metadata && !empty($chat->messages->last()->metadata['used_documents']))
                        <div class="px-5 pb-4">
                            <div class="flex items-center gap-2 text-xs text-gray-500 bg-gray-50 rounded-lg p-2">
                                <i class="fas fa-paperclip text-orange-500"></i>
                                <span>{{ count($chat->messages->last()->metadata['used_documents']) }} document(s) referenced</span>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-comments text-blue-300 text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No conversations yet</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">Start your first chat with our AI assistant to get construction insights, cost estimates, and document analysis.</p>
            <a href="{{ route('chat.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-all shadow-lg hover:shadow-xl">
                <i class="fas fa-plus"></i>
                Start Your First Chat
            </a>
        </div>
    @endif

    <!-- Pagination (if needed) -->
    @if($chats->count() > 12)
        <div class="flex justify-center mt-8">
            <button class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                Load More Conversations
            </button>
        </div>
    @endif
</div>

<!-- Rename Modal -->
<div id="renameModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Rename Chat</h3>
        <form id="renameForm" method="POST">
            @csrf
            @method('PATCH')
            <input type="text" name="title" id="renameTitle" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none mb-4" required>
            <div class="flex gap-3">
                <button type="button" onclick="closeRenameModal()" class="flex-1 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors">Cancel</button>
                <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors">Save</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchChats').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.chat-card');
        
        cards.forEach(card => {
            const title = card.getAttribute('data-title');
            if (title.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
    
    // Toggle chat menu
    function toggleChatMenu(chatId) {
        const menu = document.getElementById('chatMenu' + chatId);
        const allMenus = document.querySelectorAll('[id^="chatMenu"]');
        
        allMenus.forEach(m => {
            if (m !== menu) m.classList.add('hidden');
        });
        
        menu.classList.toggle('hidden');
    }
    
    // Close menus when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.chat-card')) {
            document.querySelectorAll('[id^="chatMenu"]').forEach(m => m.classList.add('hidden'));
        }
    });
    
    // Rename chat
    function renameChat(chatId, currentTitle) {
        document.getElementById('renameTitle').value = currentTitle;
        document.getElementById('renameForm').action = '/chat/' + chatId + '/rename';
        document.getElementById('renameModal').classList.remove('hidden');
    }
    
    function closeRenameModal() {
        document.getElementById('renameModal').classList.add('hidden');
    }
    
    // Toggle view (grid/list)
    let isGridView = true;
    function toggleView() {
        const container = document.getElementById('chatsContainer');
        isGridView = !isGridView;
        
        if (isGridView) {
            container.classList.remove('flex', 'flex-col');
            container.classList.add('grid', 'grid-cols-1', 'md:grid-cols-2', 'xl:grid-cols-3');
        } else {
            container.classList.remove('grid', 'grid-cols-1', 'md:grid-cols-2', 'xl:grid-cols-3');
            container.classList.add('flex', 'flex-col');
        }
    }
    
    // Sort functionality
    document.getElementById('sortChats').addEventListener('change', function(e) {
        const container = document.getElementById('chatsContainer');
        const cards = Array.from(container.children);
        
        cards.sort((a, b) => {
            const sortType = e.target.value;
            
            if (sortType === 'recent') {
                return b.dataset.updated - a.dataset.updated;
            } else if (sortType === 'oldest') {
                return a.dataset.updated - b.dataset.updated;
            } else if (sortType === 'messages') {
                return parseInt(b.dataset.messages) - parseInt(a.dataset.messages);
            }
        });
        
        cards.forEach(card => container.appendChild(card));
    });
</script>
@endpush
@endsection