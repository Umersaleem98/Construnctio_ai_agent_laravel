<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Document;
use App\Models\Message;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ChatController extends Controller
{
    use AuthorizesRequests;

    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        $chats = Auth::user()->chats()->with('messages')->get();
        $documents = Auth::user()->documents()->latest()->get();
        
        return view('chat.index', compact('chats', 'documents'));
    }

    public function create()
    {
        $chat = Chat::create([
            'user_id' => Auth::id(),
            'session_id' => Str::uuid(),
            'title' => 'New Chat',
        ]);

        return redirect()->route('chat.show', $chat);
    }

    public function show(Chat $chat)
    {
        if ($chat->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this chat.');
        }
        
        $chat->load('messages');
        $chats = Auth::user()->chats()->latest()->get();
        $documents = Auth::user()->documents()->latest()->get();

        return view('chat.show', compact('chat', 'chats', 'documents'));
    }

    public function store(Request $request, Chat $chat)
    {
        if ($chat->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this chat.');
        }

        Log::info('Chat Store Request', [
            'chat_id' => $chat->id,
            'user_id' => Auth::id(),
            'message_preview' => substr($request->message, 0, 100),
        ]);

        $request->validate([
            'message' => 'required|string|max:10000',
            'document_ids' => 'nullable|array',
            'document_ids.*' => 'exists:documents,id',
        ]);

        // Save user message
        $userMessage = Message::create([
            'chat_id' => $chat->id,
            'role' => 'user',
            'content' => $request->message,
        ]);

        // Get context (last 10 messages)
        $context = $chat->messages()
            ->where('id', '!=', $userMessage->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->reverse()
            ->map(fn($msg) => [
                'role' => $msg->role,
                'content' => $msg->content,
            ])
            ->toArray();

        // Get selected documents
        $documents = [];
        if ($request->has('document_ids') && !empty($request->document_ids)) {
            $documents = Document::whereIn('id', $request->document_ids)
                ->where('user_id', Auth::id())
                ->get()
                ->map(fn($doc) => [
                    'original_name' => $doc->original_name,
                    'ocr_text' => $doc->ocr_text,
                ])
                ->toArray();
            
            Log::info('Documents loaded', ['count' => count($documents)]);
        }

        // Generate AI response with automatic fallback
        Log::info('Calling AI service');
        $startTime = microtime(true);
        
        $response = $this->aiService->generateContent(
            $request->message,
            $context,
            $documents
        );
        
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        Log::info('AI response received', [
            'duration_ms' => $duration,
            'response_length' => strlen($response),
            'provider_used' => str_contains($response, '[Gemini]') ? 'Gemini' : (str_contains($response, '[ChatGPT]') ? 'ChatGPT' : 'Unknown')
        ]);

        // Save AI response
        $aiMessage = Message::create([
            'chat_id' => $chat->id,
            'role' => 'model',
            'content' => $response,
            'metadata' => [
                'used_documents' => $request->document_ids ?? [],
                'generated_at' => now()->toIso8601String(),
                'response_time_ms' => $duration,
            ],
        ]);

        // Update chat title if first message
        if ($chat->messages()->count() <= 2 && $chat->title === 'New Chat') {
            $title = substr($request->message, 0, 50);
            $chat->update(['title' => $title]);
            Log::info('Chat title updated', ['title' => $title]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $response,
                'message_id' => $aiMessage->id,
                'response_time_ms' => $duration,
            ]);
        }

        return redirect()->route('chat.show', $chat);
    }

    public function destroy(Chat $chat)
    {
        if ($chat->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this chat.');
        }
        
        $chat->delete();

        return redirect()->route('dashboard')->with('success', 'Chat deleted successfully');
    }

    public function getHistory()
    {
        $chats = Auth::user()->chats()
            ->with(['messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->latest()
            ->paginate(20);

        return response()->json($chats);
    }

    /**
     * Test AI providers endpoint
     */
    public function testAI()
    {
        $results = $this->aiService->testProviders();
        $status = $this->aiService->getProviderStatus();
        
        return response()->json([
            'status' => $status,
            'test_results' => $results,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get AI provider status
     */
    public function aiStatus()
    {
        return response()->json($this->aiService->getProviderStatus());
    }
}