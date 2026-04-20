<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Builds focused documentation query packs for a topic, framework, and objectives.')]
class Context7 extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $data = $request->validate([
            'topic' => ['required', 'string', 'max:200'],
            'framework' => ['nullable', 'string', 'max:120'],
            'version' => ['nullable', 'string', 'max:40'],
            'objectives' => ['nullable', 'array', 'max:7'],
            'objectives.*' => ['string', 'max:180'],
        ]);

        $topic = trim((string) $data['topic']);
        $framework = trim((string) ($data['framework'] ?? 'Laravel'));
        $version = trim((string) ($data['version'] ?? ''));
        $objectives = $data['objectives'] ?? [];

        $base = trim($framework.' '.($version !== '' ? $version.' ' : '').$topic);

        $queries = [
            $base.' overview',
            $base.' best practices',
            $base.' api reference',
            $base.' examples',
            $base.' common pitfalls',
            $base.' testing strategy',
            $base.' troubleshooting',
        ];

        foreach ($objectives as $objective) {
            $queries[] = $base.' '.$objective;
        }

        $queries = array_values(array_unique($queries));
        $queries = array_slice($queries, 0, 10);

        $lines = [];
        $lines[] = 'Context7 Query Pack';
        $lines[] = 'Topic: '.$topic;
        $lines[] = 'Framework: '.$framework.($version !== '' ? ' '.$version : '');
        $lines[] = '';
        $lines[] = 'Recommended Queries:';

        foreach ($queries as $index => $query) {
            $lines[] = ($index + 1).'. '.$query;
        }

        if ($objectives !== []) {
            $lines[] = '';
            $lines[] = 'Mapped Objectives:';
            foreach (array_values($objectives) as $index => $objective) {
                $lines[] = ($index + 1).'. '.$objective;
            }
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
            'topic' => $schema->string()
                ->description('Main topic to research in documentation.')
                ->required(),
            'framework' => $schema->string()
                ->description('Framework or platform name, e.g. Laravel, React, Livewire.')
                ->nullable(),
            'version' => $schema->string()
                ->description('Optional version string, e.g. 12.x.')
                ->nullable(),
            'objectives' => $schema->array()
                ->description('Optional focused objectives to include in query pack.')
                ->items($schema->string()),
        ];
    }
}
