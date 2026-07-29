<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Project;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Ai\Models\ConversationMessage;

class AuditChatController extends Controller
{
    public function __construct(
        protected AIService $ai,
    ) {}

    public function __invoke(Request $request, Project $project, Audit $audit): JsonResponse
    {
        if ($request->isMethod('get')) {
            return $this->fetchConversation($audit);
        }

        return $this->sendMessage($request, $audit);
    }

    protected function fetchConversation(Audit $audit): JsonResponse
    {
        $conversation = $this->ai->getConversation($audit);

        if (! $conversation) {
            return response()->json([
                'conversation_id' => null,
                'messages' => [],
            ]);
        }

        $messages = $conversation->messages()
            ->oldest()
            ->get()
            ->map(fn (ConversationMessage $m) => [
                'role' => $m->role,
                'content' => $m->content,
            ]);

        return response()->json([
            'conversation_id' => $conversation->id,
            'messages' => $messages,
        ]);
    }

    protected function sendMessage(Request $request, Audit $audit): JsonResponse
    {
        $data = $request->validate([
            'message' => 'required|string|max:2000',
            'conversation_id' => 'nullable|string|max:36',
        ]);

        $result = $this->ai->chat(
            $audit,
            $request->user(),
            $data['message'],
            $data['conversation_id'] ?? null,
        );

        return response()->json($result);
    }
}
