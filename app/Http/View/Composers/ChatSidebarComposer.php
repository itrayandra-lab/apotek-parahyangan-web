<?php

namespace App\Http\View\Composers;

use App\Models\ChatMessage;
use App\Models\ContactMessage;
use Illuminate\View\View;

class ChatSidebarComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $unreadChatCount = \App\Models\ChatSession::whereHas('unreadMessages')->count();
        $unreadContactCount = ContactMessage::unread()->count();

        $view->with([
            'unreadChatCount' => $unreadChatCount,
            'unreadContactCount' => $unreadContactCount,
        ]);
    }
}
