<?php

namespace App\Http\Middleware;

use App\Models\ChatbotConfiguration;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChatbotAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = $request->userAgent();
        $blockedBots = ['bot', 'crawl', 'spider', 'slurp', 'facebookexternalhit', 'monitoring'];

        if ($userAgent && str($userAgent)->contains($blockedBots, true)) {
            return response()->json([
                'message' => 'Chatbot unavailable for bots.',
            ], Response::HTTP_FORBIDDEN);
        }

        $isActiveConfig = ChatbotConfiguration::getValue('chatbot_active', config('services.chatbot.active', true));
        $isActive = filter_var($isActiveConfig, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($isActive === false) {
            return response()->json([
                'message' => 'Chatbot is currently disabled.',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return $next($request);
    }
}
