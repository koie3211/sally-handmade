<?php

namespace App\Http\Controllers\Line\V1;

use App\Http\Controllers\Controller;
use App\Services\Line\V1\LineBotService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LineController extends Controller
{
    public function __construct(
        private readonly LineBotService $lineBotService
    ) {
    }

    public function webhook(Request $request): Response
    {
        return $this->lineBotService->handleWebhook($request);
    }
}
