<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class WorkspaceSession
{
    public function resolve(Request $request): string
    {
        $sessionKey = $request->session()->get('rag_workspace_key');

        if (is_string($sessionKey) && $sessionKey !== '') {
            return $sessionKey;
        }

        $cookieKey = $request->cookie('rag_workspace_key');

        if (is_string($cookieKey) && $cookieKey !== '') {
            $request->session()->put('rag_workspace_key', $cookieKey);

            return $cookieKey;
        }

        $workspaceKey = Str::uuid()->toString();
        $request->session()->put('rag_workspace_key', $workspaceKey);

        Cookie::queue(Cookie::forever('rag_workspace_key', $workspaceKey));

        return $workspaceKey;
    }
}
