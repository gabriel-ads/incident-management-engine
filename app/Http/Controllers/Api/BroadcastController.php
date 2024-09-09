<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Tymon\JWTAuth\Facades\JWTAuth;

class BroadcastController extends Controller
{
    public function authorizeChannel(Request $request, $channel)
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Verifique se o usuário está autenticado e autorizado a acessar o canal
        if ($user) {
            return response()->json(['auth' => true]);
        }

        return response()->json(['auth' => false], 403);
    }
}
