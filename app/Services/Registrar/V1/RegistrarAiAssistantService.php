<?php

namespace App\Services\Registrar\V1;

use App\Ai\Agents\RegistrarCaseAssistantAgent;
use Illuminate\Support\Facades\Log;
use Throwable;

class RegistrarAiAssistantService
{
    private const EMPTY_MESSAGE_REPLY = '請輸入想查詢的案件問題，例如：列出待收款案件。';

    private const FAILURE_REPLY = 'AI 小助手暫時無法回覆，請稍後再試。';

    public function reply(string $message): string
    {
        $message = trim($message);

        if ($message === '') {
            return self::EMPTY_MESSAGE_REPLY;
        }

        try {
            $response = (new RegistrarCaseAssistantAgent)->prompt($message);
        } catch (Throwable $e) {
            Log::warning('registrar.assistant.prompt_failed', [
                'error' => $e->getMessage(),
            ]);

            return self::FAILURE_REPLY;
        }

        $reply = trim((string) $response);

        return $reply === '' ? self::FAILURE_REPLY : str($reply)->limit(4000)->toString();
    }
}
