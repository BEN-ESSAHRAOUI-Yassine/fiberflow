<?php

namespace App\Services;

use App\Ai\Agents\FtthAuditAgent;
use App\Models\Audit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Models\Conversation;
use Throwable;

class AIService
{
    public function __construct(
        protected string $provider = 'groq',
    ) {}

    public function chat(Audit $audit, User $user, string $message, ?string $conversationId = null): array
    {
        $agent = $this->createAgent($audit);

        try {
            if ($conversationId) {
                $agent->continue($conversationId, $user);
            } else {
                $agent->forUser($user);
            }

            $response = $agent->prompt($message, provider: $this->provider);

            $newConversationId = $response->conversationId;

            if (! $conversationId && $newConversationId) {
                DB::table('agent_conversations')
                    ->where('id', $newConversationId)
                    ->update([
                        'project_id' => $audit->project_id,
                        'audit_id' => $audit->id,
                    ]);
            }

            return [
                'reply' => $response->text,
                'conversation_id' => $newConversationId,
            ];
        } catch (Throwable $e) {
            Log::warning("AI chat failed for audit {$audit->id}: {$e->getMessage()}");

            $message = match (true) {
                str_contains($e->getMessage(), '413') || str_contains($e->getMessage(), 'too large') => 'La conversation est trop longue. Commencez une nouvelle discussion.',
                str_contains($e->getMessage(), '429') || str_contains($e->getMessage(), 'rate limit') => 'Trop de requêtes. Réessayez dans quelques instants.',
                default => 'Désolé, une erreur est survenue. Veuillez réessayer.',
            };

            return [
                'reply' => $message,
                'conversation_id' => $conversationId,
            ];
        }
    }

    public function getConversation(Audit $audit): ?Conversation
    {
        return Conversation::where('audit_id', $audit->id)
            ->latest('updated_at')
            ->first();
    }

    protected function createAgent(Audit $audit): FtthAuditAgent
    {
        return app(FtthAuditAgent::class, ['audit' => $audit]);
    }
}
