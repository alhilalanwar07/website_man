<?php

use App\Mcp\Servers\AssistantToolsServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/assistant-tools', AssistantToolsServer::class);
Mcp::local('assistant-tools', AssistantToolsServer::class);
