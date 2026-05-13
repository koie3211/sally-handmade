<?php

namespace App\Services\Line\V1;

use App\Ai\Agents\LineCasualChatAgent;
use Illuminate\Support\Facades\Log;
use Throwable;

class LineAiChatService
{
    private const EMPTY_PROMPT_REPLY = '請在 ai 後面加上想問的內容，例如：ai 幫我想一個手作靈感。';

    private const FAILURE_REPLY = '我剛剛有點恍神，晚點再問我一次好嗎？';

    public function resolveReply(string $text): ?string
    {
        $prompt = $this->extractPrompt($text);

        if ($prompt === null) {
            return null;
        }

        if ($prompt === '') {
            return self::EMPTY_PROMPT_REPLY;
        }

        try {
            $response = (new LineCasualChatAgent)->prompt($prompt);
        } catch (Throwable $e) {
            Log::warning('line.webhook.ai_reply_failed', [
                'error' => $e->getMessage(),
            ]);

            return self::FAILURE_REPLY;
        }

        $reply = trim((string) $response);

        return $reply === '' ? self::FAILURE_REPLY : str($reply)->limit(1900)->toString();
    }

    public function extractPrompt(string $text): ?string
    {
        $text = trim($text);

        if (preg_match('/^ai(?:\s+|$)/iu', $text) === 1) {
            return trim((string) preg_replace('/^ai(?:\s+)?/iu', '', $text, 1));
        }

        if (preg_match('/^莎(?:\s+|$)/u', $text) === 1) {
            return trim((string) preg_replace('/^莎(?:\s+)?/u', '', $text, 1));
        }

        return null;
    }
}
