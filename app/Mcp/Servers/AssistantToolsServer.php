<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\Context7;
use App\Mcp\Tools\Sniper;
use App\Mcp\Tools\TeamCreate;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Assistant Tools Server')]
#[Version('0.1.0')]
#[Instructions('Provides productivity tools: TeamCreate for execution plans, Context7 for documentation query packs, and Sniper for targeted validation checklists.')]
class AssistantToolsServer extends Server
{
    protected array $tools = [
        TeamCreate::class,
        Context7::class,
        Sniper::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}
