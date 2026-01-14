<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_endpoint_returns_active_flag(): void
    {
        $response = $this->get('/api/chatbot/status');

        $response->assertOk()
            ->assertJsonStructure(['active', 'message']);
    }

    public function test_guest_session_created_via_endpoint(): void
    {
        $this->assertDatabaseCount('chat_sessions', 0);

        $response = $this->get('/api/chatbot/session');

        $response->assertOk()
            ->assertJsonStructure(['session_id', 'status', 'is_guest']);

        $this->assertDatabaseHas('chat_sessions', [
            'session_id' => $response->json('session_id'),
            'is_guest' => true,
            'status' => 'active',
        ]);
    }

    public function test_send_message_creates_user_and_bot_messages(): void
    {
        config([
            'services.chatbot.webhook_url' => 'http://fake.test/webhook',
        ]);

        Http::fake([
            '*' => Http::response(['output' => 'Hi there'], 200),
        ]);

        $session = ChatSession::factory()->guest()->create();

        $response = $this->postJson('/api/chatbot/send', [
            'session_id' => $session->session_id,
            'message' => 'Hello bot',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['session', 'messages']);

        $this->assertDatabaseHas('chat_messages', [
            'chat_session_id' => $session->id,
            'type' => 'user',
            'content' => 'Hello bot',
        ]);

        $this->assertDatabaseHas('chat_messages', [
            'chat_session_id' => $session->id,
            'type' => 'bot',
            'content' => 'Hi there',
        ]);
    }

    public function test_history_returns_messages_for_session(): void
    {
        $session = ChatSession::factory()->guest()->create();

        ChatMessage::factory()->for($session)->create([
            'type' => 'user',
            'content' => 'First message',
        ]);

        ChatMessage::factory()->for($session)->create([
            'type' => 'bot',
            'content' => 'Reply message',
        ]);

        $response = $this->getJson('/api/chatbot/history?session_id='.$session->session_id);

        $response->assertOk()
            ->assertJsonCount(2, 'messages');
    }
}
