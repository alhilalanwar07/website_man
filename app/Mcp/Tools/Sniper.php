<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Generates a targeted validation checklist to review changes and catch high-risk issues quickly.')]
class Sniper extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $data = $request->validate([
            'objective' => ['required', 'string', 'max:400'],
            'scope' => ['nullable', 'string', 'max:400'],
            'risks' => ['nullable', 'array', 'max:12'],
            'risks.*' => ['string', 'max:200'],
            'test_command' => ['nullable', 'string', 'max:300'],
        ]);

        $objective = trim((string) $data['objective']);
        $scope = trim((string) ($data['scope'] ?? ''));
        $risks = $data['risks'] ?? [];
        $testCommand = trim((string) ($data['test_command'] ?? ''));

        $lines = [];
        $lines[] = 'Sniper Validation Checklist';
        $lines[] = 'Objective: '.$objective;

        if ($scope !== '') {
            $lines[] = 'Scope: '.$scope;
        }

        $lines[] = '';
        $lines[] = 'Critical Checks:';
        $lines[] = '1. Confirm expected behavior changed only in target scope.';
        $lines[] = '2. Verify null, empty, and boundary-value handling.';
        $lines[] = '3. Check authorization and data exposure risks.';
        $lines[] = '4. Validate validation rules and user-facing messages.';
        $lines[] = '5. Detect regressions in dependent paths and UI states.';

        if ($risks !== []) {
            $lines[] = '';
            $lines[] = 'Highlighted Risks:';
            foreach (array_values($risks) as $index => $risk) {
                $lines[] = ($index + 1).'. '.$risk;
            }
        }

        if ($testCommand !== '') {
            $lines[] = '';
            $lines[] = 'Suggested Test Command:';
            $lines[] = $testCommand;
        }

        $lines[] = '';
        $lines[] = 'Done Criteria:';
        $lines[] = '- No critical regressions found.';
        $lines[] = '- High-risk checks are explicitly verified.';
        $lines[] = '- Target tests pass with clear evidence.';

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
            'objective' => $schema->string()
                ->description('What needs to be validated or reviewed.')
                ->required(),
            'scope' => $schema->string()
                ->description('Optional scope boundary, e.g. file, module, or feature.')
                ->nullable(),
            'risks' => $schema->array()
                ->description('Optional list of known risks to prioritize.')
                ->items($schema->string()),
            'test_command' => $schema->string()
                ->description('Optional test command to run for evidence.')
                ->nullable(),
        ];
    }
}
