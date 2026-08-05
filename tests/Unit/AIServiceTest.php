<?php

use App\Ai\Agents\FtthAuditAgent;
use App\Models\Audit;
use App\Models\Project;
use App\Models\User;
use App\Services\AIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Models\Conversation;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\Data\Meta;
use Laravel\Ai\Responses\Data\Usage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->project = Project::factory()->create();
    $this->audit = Audit::factory()->for($this->project)->create();
    $this->user = User::factory()->create();
});

function makeAgentResponse(string $text, string $conversationId): AgentResponse
{
    $response = new AgentResponse(
        'inv-1',
        $text,
        new Usage(0, 0, 0),
        new Meta('test-model'),
    );
    $response->conversationId = $conversationId;

    return $response;
}

it('starts a new conversation via chat', function () {
    $response = makeAgentResponse('Réponse test', 'conv-new-123');

    $agentMock = Mockery::mock(FtthAuditAgent::class, [$this->audit]);
    $agentMock->shouldReceive('forUser')->once()->with($this->user)->andReturnSelf();
    $agentMock->shouldReceive('prompt')->once()->andReturn($response);

    $this->app->bind(FtthAuditAgent::class, fn () => $agentMock);

    $result = app(AIService::class)->chat($this->audit, $this->user, 'Bonjour');

    expect($result['reply'])->toBe('Réponse test');
    expect($result['conversation_id'])->toBe('conv-new-123');
});

it('continues an existing conversation via chat', function () {
    $response = makeAgentResponse('Suite de la conversation', 'conv-existing-456');

    Conversation::create([
        'id' => 'conv-existing-456',
        'project_id' => $this->project->id,
        'audit_id' => $this->audit->id,
        'user_id' => $this->user->id,
        'title' => 'Test',
    ]);

    $agentMock = Mockery::mock(FtthAuditAgent::class, [$this->audit]);
    $agentMock->shouldReceive('continue')->once()->with('conv-existing-456', $this->user)->andReturnSelf();
    $agentMock->shouldReceive('prompt')->once()->andReturn($response);

    $this->app->bind(FtthAuditAgent::class, fn () => $agentMock);

    $result = app(AIService::class)->chat($this->audit, $this->user, 'Dis-moi plus', 'conv-existing-456');

    expect($result['reply'])->toBe('Suite de la conversation');
});

it('rejects a conversation that does not belong to the audit and user', function () {
    $agentMock = Mockery::mock(FtthAuditAgent::class, [$this->audit]);
    $agentMock->shouldNotReceive('continue');
    $agentMock->shouldNotReceive('prompt');

    $this->app->bind(FtthAuditAgent::class, fn () => $agentMock);

    $result = app(AIService::class)->chat($this->audit, $this->user, 'Dis-moi plus', 'conv-foreign-123');

    expect($result['reply'])->toContain('Conversation introuvable');
    expect($result['conversation_id'])->toBeNull();
});

it('returns fallback on chat error', function () {
    $agentMock = Mockery::mock(FtthAuditAgent::class, [$this->audit]);
    $agentMock->shouldReceive('forUser')->once()->andReturnSelf();
    $agentMock->shouldReceive('prompt')->once()->andThrow(new Exception('API failure'));

    $this->app->bind(FtthAuditAgent::class, fn () => $agentMock);

    $result = app(AIService::class)->chat($this->audit, $this->user, 'Bonjour');

    expect($result['reply'])->toContain('erreur');
    expect($result['conversation_id'])->toBeNull();
});

it('preserves conversation id on chat error after continue', function () {
    Conversation::create([
        'id' => 'conv-exist-789',
        'project_id' => $this->project->id,
        'audit_id' => $this->audit->id,
        'user_id' => $this->user->id,
        'title' => 'Test',
    ]);

    $agentMock = Mockery::mock(FtthAuditAgent::class, [$this->audit]);
    $agentMock->shouldReceive('continue')->once()->andReturnSelf();
    $agentMock->shouldReceive('prompt')->once()->andThrow(new Exception('API failure'));

    $this->app->bind(FtthAuditAgent::class, fn () => $agentMock);

    $result = app(AIService::class)->chat($this->audit, $this->user, 'Bonjour', 'conv-exist-789');

    expect($result['reply'])->toContain('erreur');
    expect($result['conversation_id'])->toBe('conv-exist-789');
});

it('returns null when no conversation exists', function () {
    $result = app(AIService::class)->getConversation($this->audit, $this->user);

    expect($result)->toBeNull();
});

it('does not return another users conversation', function () {
    $other = User::factory()->create();

    Conversation::create([
        'id' => 'conv-other-123',
        'project_id' => $this->project->id,
        'audit_id' => $this->audit->id,
        'user_id' => $other->id,
        'title' => 'Test',
    ]);

    $result = app(AIService::class)->getConversation($this->audit, $this->user);

    expect($result)->toBeNull();
});

it('defaults provider to groq via config', function () {
    expect(config('ai.default'))->toBe('groq');
});

it('agent implements HasTools', function () {
    $agent = new FtthAuditAgent($this->audit);

    expect($agent)->toBeInstanceOf(HasTools::class);
    expect($agent->tools())->toBeArray();
    expect($agent->tools())->toHaveCount(6);
});
