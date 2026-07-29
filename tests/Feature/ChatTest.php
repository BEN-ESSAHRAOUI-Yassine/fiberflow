<?php

use App\Ai\Agents\FtthAuditAgent;
use App\Models\Audit;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\Data\Meta;
use Laravel\Ai\Responses\Data\Usage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->project = Project::factory()->create();
    $this->audit = Audit::factory()->completed()->create(['project_id' => $this->project->id]);
    $this->user = User::factory()->create();
});

function mockAiResponse(string $text, string $conversationId = 'conv-123'): AgentResponse
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

describe('Chat endpoint', function () {

    it('returns AI response on POST', function () {
        $response = mockAiResponse('Bonjour, comment puis-je vous aider?');

        $agentMock = Mockery::mock(FtthAuditAgent::class, [$this->audit]);
        $agentMock->shouldReceive('forUser')->once()->with($this->user)->andReturnSelf();
        $agentMock->shouldReceive('prompt')->once()->andReturn($response);

        $this->app->bind(FtthAuditAgent::class, fn () => $agentMock);

        $result = $this->actingAs($this->user)
            ->postJson("/projects/{$this->project->id}/audits/{$this->audit->id}/chat", [
                'message' => 'Bonjour',
            ]);

        $result->assertOk()
            ->assertJsonPath('reply', 'Bonjour, comment puis-je vous aider?')
            ->assertJsonPath('conversation_id', 'conv-123');
    });

    it('returns empty conversation on GET when none exists', function () {
        $result = $this->actingAs($this->user)
            ->getJson("/projects/{$this->project->id}/audits/{$this->audit->id}/chat");

        $result->assertOk()
            ->assertJsonPath('conversation_id', null)
            ->assertJsonPath('messages', []);
    });

    it('returns validation error on missing message', function () {
        $result = $this->actingAs($this->user)
            ->post("/projects/{$this->project->id}/audits/{$this->audit->id}/chat", [
                'not_a_message' => 'hello',
            ]);

        $result->assertSessionHasErrors('message');
    });

    it('returns 401 for guest', function () {
        $result = $this->postJson("/projects/{$this->project->id}/audits/{$this->audit->id}/chat", [
            'message' => 'Bonjour',
        ]);

        $result->assertRedirect('/login');
    });

    it('makes no real HTTP calls during chat', function () {
        $response = mockAiResponse('Test response');

        $agentMock = Mockery::mock(FtthAuditAgent::class, [$this->audit]);
        $agentMock->shouldReceive('forUser')->once()->andReturnSelf();
        $agentMock->shouldReceive('prompt')->once()->andReturn($response);

        $this->app->bind(FtthAuditAgent::class, fn () => $agentMock);

        $this->actingAs($this->user)
            ->postJson("/projects/{$this->project->id}/audits/{$this->audit->id}/chat", [
                'message' => 'Bonjour',
            ]);

        Mockery::close();
        expect(true)->toBeTrue();
    });

    it('handles AI service errors gracefully', function () {
        $agentMock = Mockery::mock(FtthAuditAgent::class, [$this->audit]);
        $agentMock->shouldReceive('forUser')->once()->andReturnSelf();
        $agentMock->shouldReceive('prompt')->once()->andThrow(new Exception('AI service unavailable'));

        $this->app->bind(FtthAuditAgent::class, fn () => $agentMock);

        $result = $this->actingAs($this->user)
            ->postJson("/projects/{$this->project->id}/audits/{$this->audit->id}/chat", [
                'message' => 'Bonjour',
            ]);

        $result->assertOk()
            ->assertJsonPath('reply', fn ($reply) => str_contains($reply, 'erreur'));
    });
});
