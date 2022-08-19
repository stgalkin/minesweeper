<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class GameController extends BaseController
{
    public function create(): JsonResponse
    {
        return new JsonResponse(null, 201);
    }

    public function reveal(Game $game)
    {

    }

    public function flag(Game $game)
    {

    }
}
