<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * Display a listing of active chat sessions.
     */
    public function index(Request $request): View
    {
        $query = ChatSession::with(['user', 'messages' => function ($q) {
            $q->latest()->limit(1);
        }])
        ->withCount('unreadMessages')
        ->orderByDesc('last_activity_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sessions = $query->paginate(15)->withQueryString();

        return view('admin.chat.index', compact('sessions'));
    }

    /**
     * Display the specified chat session messages.
     */
    public function show(ChatSession $chatSession): View
    {
        $chatSession->load(['user', 'messages' => function ($q) {
            $q->orderBy('sent_at', 'asc');
        }]);

        // Mark all messages as read
        $chatSession->unreadMessages()->update(['is_read_by_admin' => true]);

        return view('admin.chat.show', [
            'session' => $chatSession,
            'messages' => $chatSession->messages,
        ]);
    }

    /**
     * Send a reply from the pharmacist.
     */
    public function reply(Request $request, ChatSession $chatSession): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'nullable|string|max:5000',
            'type' => 'nullable|string|in:pharmacist,product',
            'metadata' => 'nullable|array',
        ]);

        $type = $validated['type'] ?? 'pharmacist';
        $content = $validated['content'] ?? '';

        if ($type === 'pharmacist' && empty($content)) {
            return response()->json(['success' => false, 'message' => 'Message content is required for text replies.'], 422);
        }

        $message = $chatSession->messages()->create([
            'user_id' => auth()->id(),
            'type' => $type,
            'content' => $content,
            'metadata' => $validated['metadata'] ?? [],
            'sent_at' => now(),
        ]);

        $chatSession->updateActivity();

        return response()->json([
            'success' => true,
            'message' => $message->load('user'),
        ]);
    }

    /**
     * Search products and medicines for recommendation.
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        $products = \App\Models\Product::where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'slug', 'price', 'discount_price'])
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'price' => (float)($p->discount_price ?? $p->price),
                    'image_url' => $p->getImageUrl(),
                    'model_type' => 'Product'
                ];
            });

        $medicines = \App\Models\Medicine::with('medicineUnits')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('code', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'code'])
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'name' => $m->name,
                    'slug' => $m->code, // Using code as slug
                    'price' => (float)$m->price,
                    'image_url' => $m->getImageUrl(),
                    'model_type' => 'Medicine'
                ];
            });

        return response()->json($products->concat($medicines));
    }

    /**
     * Sync messages for polling.
     */
    public function sync(ChatSession $chatSession, Request $request): JsonResponse
    {
        $lastId = $request->query('last_id', 0);

        $newMessages = $chatSession->messages()
            ->where('id', '>', $lastId)
            ->orderBy('id', 'asc')
            ->get();

        if ($newMessages->isNotEmpty()) {
            $chatSession->unreadMessages()
                ->whereIn('id', $newMessages->pluck('id'))
                ->update(['is_read_by_admin' => true]);
        }

        return response()->json([
            'new_messages' => $newMessages,
            'last_id' => $newMessages->last()?->id ?? $lastId,
        ]);
    }

    /**
     * Get unread counts for global polling.
     */
    public function unreadCounts(): JsonResponse
    {
        return response()->json([
            'unreadChatCount' => ChatSession::whereHas('unreadMessages')->count(),
            'unreadContactCount' => \App\Models\ContactMessage::unread()->count(),
            'pendingPaymentCount' => \App\Models\Order::where('status', 'pending_payment')->count(),
        ]);
    }
}
