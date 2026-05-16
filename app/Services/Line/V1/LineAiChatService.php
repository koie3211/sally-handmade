<?php

namespace App\Services\Line\V1;

use App\Ai\Agents\LineCasualChatAgent;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Files;
use Throwable;

class LineAiChatService
{
    private const EMPTY_PROMPT_REPLY = '請在 ai 後面加上想問的內容，例如：ai 幫我想一個手作靈感。';

    private const FAILURE_REPLY = '我剛剛有點恍神，晚點再問我一次好嗎？';

    private const INVALID_IMAGE_URL_REPLY = '目前只能分析 sallyhandmade.com 網域的圖片網址（jpg、jpeg、png、webp、gif）。';

    private const DEFAULT_IMAGE_PROMPT = '請簡短描述這張圖片裡有什麼。';

    private const ALLOWED_IMAGE_HOST = 'sallyhandmade.com';

    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    public function resolveReply(string $text): ?string
    {
        $prompt = $this->extractPrompt($text);

        if ($prompt === null) {
            return null;
        }

        if ($prompt === '') {
            return self::EMPTY_PROMPT_REPLY;
        }

        $imageUrl = $this->extractFirstUrl($prompt);
        if ($imageUrl !== null) {
            if (! $this->isAllowedImageUrl($imageUrl)) {
                return self::INVALID_IMAGE_URL_REPLY;
            }

            return $this->analyzeImageUrl($imageUrl, $this->removeUrlFromPrompt($prompt, $imageUrl));
        }

        return $this->promptAgent($prompt);
    }

    public function analyzeImageUrl(string $imageUrl, ?string $prompt = null): string
    {
        if (! $this->isAllowedImageUrl($imageUrl)) {
            return self::INVALID_IMAGE_URL_REPLY;
        }

        $prompt = trim((string) $prompt);

        return $this->promptAgent(
            $prompt === '' ? self::DEFAULT_IMAGE_PROMPT : $prompt,
            [Files\Image::fromUrl($imageUrl)]
        );
    }

    public function isAllowedImageUrl(string $url): bool
    {
        $parts = parse_url($url);
        $scheme = mb_strtolower((string) ($parts['scheme'] ?? ''));
        $host = mb_strtolower((string) ($parts['host'] ?? ''));
        $path = (string) ($parts['path'] ?? '');

        if (! in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        if ($host !== self::ALLOWED_IMAGE_HOST && ! str_ends_with($host, '.'.self::ALLOWED_IMAGE_HOST)) {
            return false;
        }

        $extension = mb_strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, self::IMAGE_EXTENSIONS, true);
    }

    public function extractFirstUrl(string $text): ?string
    {
        preg_match('/https?:\/\/[^\s<>"\']+/iu', $text, $matches);

        return isset($matches[0]) ? rtrim($matches[0], '.,!?，。！？、') : null;
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

    private function promptAgent(string $prompt, array $attachments = []): string
    {
        try {
            $response = (new LineCasualChatAgent)->prompt($prompt, attachments: $attachments);
        } catch (Throwable $e) {
            Log::warning('line.webhook.ai_reply_failed', [
                'error' => $e->getMessage(),
            ]);

            return self::FAILURE_REPLY;
        }

        $reply = trim((string) $response);

        return $reply === '' ? self::FAILURE_REPLY : str($reply)->limit(1900)->toString();
    }

    private function removeUrlFromPrompt(string $prompt, string $url): string
    {
        return trim(str_replace($url, '', $prompt));
    }
}
