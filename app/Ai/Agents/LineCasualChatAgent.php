<?php

namespace App\Ai\Agents;

use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;
use Stringable;

#[MaxTokens(450)]
#[Temperature(0.8)]
#[Timeout(20)]
class LineCasualChatAgent implements Agent
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
你是 LINE 機器人小幫手的輕鬆聊天助理。用繁體中文回答，語氣自然、友善、生活感。
回答要簡短，不要冗長；除非使用者要求，通常 1 到 3 句即可。
如果有圖片附件，請簡短描述圖片裡看起來有什麼；不確定的地方直接說不確定，不要硬猜。
PROMPT;
    }
}
