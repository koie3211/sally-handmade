<?php

namespace App\Http\Controllers\Registrar\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registrar\V1\RegistrarAssistantRequest;
use App\Services\Registrar\V1\RegistrarAiAssistantService;
use Illuminate\Http\JsonResponse;

class AssistantController extends Controller
{
    public function __invoke(RegistrarAssistantRequest $request, RegistrarAiAssistantService $assistant): JsonResponse
    {
        return response()->json([
            'data' => [
                'reply' => $assistant->reply($request->validated('message')),
            ],
        ]);
    }
}
