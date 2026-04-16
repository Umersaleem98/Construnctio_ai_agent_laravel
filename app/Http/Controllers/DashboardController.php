<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Document;
use App\Models\Message;
use App\Services\AIService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        // Get all statistics
        $stats = [
            // Chat statistics
            'total_chats' => Chat::where('user_id', $userId)->count(),
            'total_messages' => Message::whereHas('chat', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->count(),
            'user_messages' => Message::whereHas('chat', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('role', 'user')->count(),
            'ai_messages' => Message::whereHas('chat', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('role', 'model')->count(),
            
            // Document statistics
            'total_documents' => Document::where('user_id', $userId)->count(),
            'documents_with_text' => Document::where('user_id', $userId)->whereNotNull('ocr_text')->count(),
            'documents_by_type' => Document::where('user_id', $userId)
                ->select('file_type', DB::raw('count(*) as count'))
                ->groupBy('file_type')
                ->pluck('count', 'file_type')
                ->toArray(),
            
            // Activity statistics
            'chats_today' => Chat::where('user_id', $userId)->whereDate('created_at', today())->count(),
            'chats_this_week' => Chat::where('user_id', $userId)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'chats_this_month' => Chat::where('user_id', $userId)->whereMonth('created_at', now()->month)->count(),
            
            'messages_today' => Message::whereHas('chat', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->whereDate('created_at', today())->count(),
            
            // Storage statistics
            'total_storage_used' => Document::where('user_id', $userId)->sum(DB::raw('LENGTH(ocr_text)')),
        ];

        // Get recent records
        $recentChats = Chat::where('user_id', $userId)
            ->with(['messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->latest()
            ->take(5)
            ->get();

        $recentDocuments = Document::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // Get all chats and documents for full listing
        $allChats = Chat::where('user_id', $userId)
            ->withCount('messages')
            ->latest()
            ->get();

        $allDocuments = Document::where('user_id', $userId)
            ->latest()
            ->get();

        // Get AI provider status
        $aiStatus = $this->aiService->getProviderStatus();

        // Get monthly activity data for charts
        $monthlyActivity = Chat::where('user_id', $userId)
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return view('dashboard', compact(
            'stats',
            'recentChats',
            'recentDocuments',
            'allChats',
            'allDocuments',
            'aiStatus',
            'monthlyActivity'
        ));
    }
}