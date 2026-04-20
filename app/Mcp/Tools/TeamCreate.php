<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Builds a clear multi-agent execution plan from a goal, constraints, and deliverables.')]
class TeamCreate extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $data = $request->validate([
            'goal' => ['required', 'string', 'max:500'],
            'context' => ['nullable', 'string', 'max:1000'],
            'agents' => ['nullable', 'array', 'max:10'],
            'agents.*' => ['string', 'max:120'],
            'constraints' => ['nullable', 'array', 'max:10'],
            'constraints.*' => ['string', 'max:200'],
            'deliverables' => ['nullable', 'array', 'max:10'],
            'deliverables.*' => ['string', 'max:200'],
        ]);

        $goal = trim((string) $data['goal']);
        $context = trim((string) ($data['context'] ?? ''));
        $agents = $data['agents'] ?? [
            'explore-codebase',
            'research-expert',
            'implementation',
            'validator',
        ];
        $constraints = $data['constraints'] ?? [];
        $deliverables = $data['deliverables'] ?? [
            'Working implementation',
            'Validation notes',
        ];

        $lines = [];
        $lines[] = 'TeamCreate Plan';
        $lines[] = 'Goal: '.$goal;

        if ($context !== '') {
            $lines[] = 'Context: '.$context;
        }

        $lines[] = '';
        $lines[] = 'Agent Assignment:';
        foreach (array_values($agents) as $index => $agent) {
            $lines[] = ($index + 1).'. '.$agent;
        }

        if ($constraints !== []) {
            $lines[] = '';
            $lines[] = 'Constraints:';
            foreach (array_values($constraints) as $index => $constraint) {
                $lines[] = '- '.($index + 1).'. '.$constraint;
            }
        }

        $lines[] = '';
        $lines[] = 'Execution Steps:';
        $lines[] = '1. Explore and map the current state.';
        $lines[] = '2. Gather references and implementation patterns.';
        $lines[] = '3. Build the smallest viable change set.';
        $lines[] = '4. Validate with focused checks and tests.';
        $lines[] = '5. Summarize outputs and follow-up actions.';

        $lines[] = '';
        $lines[] = 'Expected Deliverables:';
        foreach (array_values($deliverables) as $index => $deliverable) {
            $lines[] = ($index + 1).'. '.$deliverable;
        }

        return Response::text(implode("\n", $lines));
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'goal' => $schema->string()
                ->description('Primary objective to solve.')
                ->required(),
            'context' => $schema->string()
                ->description('Optional background context.')
                ->nullable(),
            'agents' => $schema->array()
                ->description('Optional preferred agent roles.')
                ->items($schema->string()),
            'constraints' => $schema->array()
                ->description('Optional constraints and boundaries.')
                ->items($schema->string()),
            'deliverables' => $schema->array()
                ->description('Optional expected outputs.')
                ->items($schema->string()),
        ];
    }
}
