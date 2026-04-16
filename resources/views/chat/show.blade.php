@extends('layouts.app')

@section('title', $chat->title)
@section('header', $chat->title)

@section('content')
<div class="flex flex-col h-[calc(100vh-140px)] -m-6">
    <!-- Chat Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                <i class="fas fa-robot text-white"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">Construction AI Assistant</h3>
                <p class="text-xs text-green-600 flex items-center gap-1">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Online
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors flex items-center gap-2">
                <i class="fas fa-upload"></i>
                <span class="hidden sm:inline">Upload Doc</span>
            </button>
            <form action="{{ route('chat.destroy', $chat) }}" method="POST" onsubmit="return confirm('Delete this chat?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    
    <!-- Messages Container -->
    <div id="messagesContainer" class="flex-1 overflow-y-auto chat-container p-6 space-y-6 bg-gray-50">
        @forelse($chat->messages as $message)
            <div class="chat-message flex gap-4 {{ $message->role === 'user' ? 'flex-row-reverse' : '' }}">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    @if($message->role === 'user')
                        <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}" 
                             class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                    @else
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-sm">
                            <i class="fas fa-robot text-white text-sm"></i>
                        </div>
                    @endif
                </div>
                
                <!-- Message Content -->
                <div class="max-w-3xl {{ $message->role === 'user' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200' }} rounded-2xl px-6 py-4 shadow-sm">
                    @if($message->role === 'model')
                        <div class="markdown-body text-gray-800 prose prose-slate max-w-none">
                            {!! nl2br(e($message->content)) !!}
                        </div>
                        
                        @if($message->metadata && !empty($message->metadata['used_documents']))
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-xs text-gray-500 mb-2">Referenced documents:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($message->metadata['used_documents'] as $docId)
                                        @php $doc = \App\Models\Document::find($docId); @endphp
                                        @if($doc)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-orange-100 text-orange-700 text-xs">
                                                <i class="fas fa-file-alt mr-1"></i>
                                                {{ Str::limit($doc->original_name, 20) }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <p class="whitespace-pre-wrap">{{ $message->content }}</p>
                    @endif
                </div>
            </div>
        @empty
            <!-- Welcome Message -->
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <i class="fas fa-hard-hat text-white text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome to Construction AI</h2>
                <p class="text-gray-600 max-w-md mx-auto mb-8">I can help you with construction queries, analyze uploaded documents, and provide professional insights.</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl mx-auto">
                    <button onclick="setQuickMessage('What are the latest building codes for residential construction?')" class="p-4 bg-white border border-gray-200 rounded-xl hover:border-blue-500 hover:shadow-md transition-all text-left">
                        <i class="fas fa-building text-blue-500 mb-2"></i>
                        <p class="font-medium text-gray-900">Building Codes</p>
                        <p class="text-sm text-gray-500">Ask about regulations</p>
                    </button>
                    <button onclick="setQuickMessage('Analyze this construction plan and suggest improvements')" class="p-4 bg-white border border-gray-200 rounded-xl hover:border-blue-500 hover:shadow-md transition-all text-left">
                        <i class="fas fa-drafting-compass text-purple-500 mb-2"></i>
                        <p class="font-medium text-gray-900">Plan Analysis</p>
                        <p class="text-sm text-gray-500">Upload documents</p>
                    </button>
                </div>
            </div>
        @endforelse
        
        <!-- Typing Indicator -->
        <div id="typingIndicator" class="hidden chat-message flex gap-4">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-sm">
                <i class="fas fa-robot text-white text-sm"></i>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl px-6 py-4 shadow-sm">
                <div class="typing-indicator flex gap-1">
                    <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                    <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                    <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Input Area -->
    <div class="bg-white border-t border-gray-200 p-4 flex-shrink-0">
        <form id="messageForm" class="max-w-4xl mx-auto">
            @csrf
            
            <!-- Selected Documents -->
            <div id="selectedDocs" class="flex flex-wrap gap-2 mb-3 hidden"></div>
            
            <div class="relative flex items-end gap-2 bg-gray-100 rounded-2xl p-2">
                <button type="button" onclick="toggleDocSelector()" class="p-3 text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-xl transition-colors" title="Attach documents">
                    <i class="fas fa-paperclip"></i>
                </button>
                
                <textarea id="messageInput" name="message" rows="1" placeholder="Message Construction AI..." 
                          class="flex-1 bg-transparent border-0 resize-none max-h-32 py-3 px-2 focus:ring-0 focus:outline-none text-gray-800" required></textarea>
                
                <button type="submit" id="sendButton" class="p-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors disabled:opacity-50">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Document Selector -->
<div id="docSelector" class="hidden fixed bottom-24 left-1/2 transform -translate-x-1/2 bg-white rounded-xl shadow-2xl border border-gray-200 p-4 w-80 z-50">
    <div class="flex items-center justify-between mb-3">
        <h4 class="font-semibold text-gray-900">Select Documents</h4>
        <button onclick="toggleDocSelector()" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="max-h-48 overflow-y-auto space-y-2">
        @forelse($documents as $doc)
            <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                <input type="checkbox" value="{{ $doc->id }}" class="doc-checkbox w-4 h-4 text-blue-600 rounded border-gray-300">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $doc->original_name }}</p>
                    <p class="text-xs text-gray-500">{{ Str::limit($doc->ocr_text, 30) ?: 'No text' }}</p>
                </div>
            </label>
        @empty
            <p class="text-sm text-gray-500 text-center py-4">No documents available</p>
        @endforelse
    </div>
    <button onclick="confirmDocSelection()" class="w-full mt-3 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">Attach Selected</button>
</div>

@push('scripts')
<script>
let selectedDocuments = [];

function setQuickMessage(text) {
    document.getElementById('messageInput').value = text;
    document.getElementById('messageInput').focus();
}

function toggleDocSelector() {
    document.getElementById('docSelector').classList.toggle('hidden');
}

function confirmDocSelection() {
    selectedDocuments = [];
    const checkboxes = document.querySelectorAll('.doc-checkbox:checked');
    const container = document.getElementById('selectedDocs');
    container.innerHTML = '';
    
    checkboxes.forEach(function(cb) {
        selectedDocuments.push(cb.value);
        const docName = cb.closest('label').querySelector('.font-medium').textContent;
        const tag = document.createElement('span');
        tag.className = 'inline-flex items-center gap-1 px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm';
        tag.innerHTML = '<i class="fas fa-file-alt text-xs"></i>' + 
            docName.substring(0, 20) + (docName.length > 20 ? '...' : '') + 
            '<button onclick="removeDoc(\'' + cb.value + '\')" class="ml-1 hover:text-orange-900"><i class="fas fa-times text-xs"></i></button>';
        container.appendChild(tag);
    });
    
    container.classList.toggle('hidden', selectedDocuments.length === 0);
    toggleDocSelector();
}

function removeDoc(docId) {
    selectedDocuments = selectedDocuments.filter(function(id) { 
        return id !== docId; 
    });
    document.querySelector('.doc-checkbox[value="' + docId + '"]').checked = false;
    confirmDocSelection();
}

// Message form handling
document.getElementById('messageForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message) {
        alert('Please enter a message');
        return;
    }
    
    const sendBtn = document.getElementById('sendButton');
    const typingIndicator = document.getElementById('typingIndicator');
    const container = document.getElementById('messagesContainer');
    
    // Disable input
    input.disabled = true;
    sendBtn.disabled = true;
    
    // Add user message to UI immediately
    const userMsgDiv = document.createElement('div');
    userMsgDiv.className = 'chat-message flex gap-4 flex-row-reverse';
    
    const userAvatar = '{{ auth()->user()->avatar ?? "https://ui-avatars.com/api/?name=" . urlencode(auth()->user()->name) }}';
    userMsgDiv.innerHTML = '<div class="flex-shrink-0"><img src="' + userAvatar + '" class="w-10 h-10 rounded-full border-2 border-white shadow-sm"></div>' +
        '<div class="max-w-3xl bg-blue-600 text-white rounded-2xl px-6 py-4 shadow-sm"><p class="whitespace-pre-wrap">' + 
        message.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</p></div>';
    
    container.appendChild(userMsgDiv);
    
    // Clear input
    input.value = '';
    input.style.height = 'auto';
    
    // Show typing indicator
    typingIndicator.classList.remove('hidden');
    container.scrollTop = container.scrollHeight;
    
    try {
        const response = await fetch('{{ route("chat.message", $chat) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                message: message,
                document_ids: selectedDocuments
            })
        });
        
        const result = await response.json();
        
        // Hide typing indicator
        typingIndicator.classList.add('hidden');
        
        if (result.success) {
            // Add AI response
            const aiMsgDiv = document.createElement('div');
            aiMsgDiv.className = 'chat-message flex gap-4';
            aiMsgDiv.innerHTML = '<div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-sm">' +
                '<i class="fas fa-robot text-white text-sm"></i></div>' +
                '<div class="max-w-3xl bg-white border border-gray-200 rounded-2xl px-6 py-4 shadow-sm">' +
                '<div class="markdown-body text-gray-800 prose prose-slate max-w-none">' + 
                result.message.replace(/\\n/g, '<br>') + '</div></div>';
            container.appendChild(aiMsgDiv);
            
            // Clear selected documents
            selectedDocuments = [];
            document.getElementById('selectedDocs').innerHTML = '';
            document.getElementById('selectedDocs').classList.add('hidden');
            document.querySelectorAll('.doc-checkbox').forEach(function(cb) { cb.checked = false; });
        } else {
            throw new Error(result.message || 'Failed to get response');
        }
        
    } catch (error) {
        console.error('Chat error:', error);
        typingIndicator.classList.add('hidden');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'chat-message flex gap-4';
        errorDiv.innerHTML = '<div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center shadow-sm">' +
            '<i class="fas fa-exclamation text-white text-sm"></i></div>' +
            '<div class="max-w-3xl bg-red-50 border border-red-200 rounded-2xl px-6 py-4 shadow-sm">' +
            '<p class="text-red-700 font-medium">Error: ' + error.message + '</p>' +
            '<p class="text-red-500 text-sm mt-1">Please try again or check your connection.</p></div>';
        container.appendChild(errorDiv);
    }
    
    // Re-enable input
    input.disabled = false;
    sendBtn.disabled = false;
    input.focus();
    container.scrollTop = container.scrollHeight;
});

// Auto-resize textarea
const textarea = document.getElementById('messageInput');
textarea.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 200) + 'px';
});
</script>
@endpush
@endsection