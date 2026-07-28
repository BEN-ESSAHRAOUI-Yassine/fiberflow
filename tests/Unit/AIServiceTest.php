<?php

use App\Ai\Agents\FtthAuditAgent;
use App\Models\Audit;
use App\Models\Project;
use App\Models\User;
use App\Services\AIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Contracts\HasTools;
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

    $agentMock = Mockery::mock(FtthAuditAgent::class, [$this->audit]);
    $agentMock->shouldReceive('continue')->once()->with('conv-existing-456', $this->user)->andReturnSelf();
    $agentMock->shouldReceive('prompt')->once()->andReturn($response);

    $this->app->bind(FtthAuditAgent::class, fn () => $agentMock);

    $result = app(AIService::class)->chat($this->audit, $this->user, 'Dis-moi plus', 'conv-existing-456');

    expect($result['reply'])->toBe('Suite de la conversation');
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
    $agentMock = Mockery::mock(FtthAuditAgent::class, [$this->audit]);
    $agentMock->shouldReceive('continue')->once()->andReturnSelf();
    $agentMock->shouldReceive('prompt')->once()->andThrow(new Exception('API failure'));

    $this->app->bind(FtthAuditAgent::class, fn () => $agentMock);

    $result = app(AIService::class)->chat($this->audit, $this->user, 'Bonjour', 'conv-exist-789');

    expect($result['reply'])->toContain('erreur');
    expect($result['conversation_id'])->toBe('conv-exist-789');
});

it('returns null when no conversation exists', function () {
    $result = app(AIService::class)->getConversation($this->audit);

    expect($result)->toBeNull();
});

it('defaults provider to groq', function () {
    $service = new AIService;
    $reflection = new ReflectionClass($service);
    $provider = $reflection->getProperty('provider');
    $provider->setAccessible(true);

    expect($provider->getValue($service))->toBe('groq');
});

it('agent implements HasTools', function () {
    $agent = new FtthAuditAgent($this->audit);

    expect($agent)->toBeInstanceOf(HasTools::class);
    expect($agent->tools())->toBeArray();
    expect($agent->tools())->toHaveCount(6);
});
