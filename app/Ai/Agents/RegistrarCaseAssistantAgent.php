<?php

namespace App\Ai\Agents;

use App\Ai\Tools\Registrar\ListAwaitingPaymentCasesTool;
use App\Ai\Tools\Registrar\ListCasesByAccountantTool;
use App\Ai\Tools\Registrar\ListRegistrarCasesTool;
use App\Ai\Tools\Registrar\ListStaleRegistrarCasesTool;
use App\Ai\Tools\Registrar\ShowRegistrarCaseSummaryTool;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Stringable;

#[MaxTokens(900)]
#[Temperature(0.2)]
#[Timeout(30)]
class RegistrarCaseAssistantAgent implements Agent, HasTools
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
你是工商登記後台的 AI 小助手。請一律使用繁體中文回答，語氣清楚、簡短、專業。

你只能協助查詢與整理工商登記案件資料。需要案件資料時，必須使用提供的工具查詢；不要編造案件、狀態、日期、金額或承辦人。
第一版能力是唯讀：你不能新增、修改、刪除案件，也不能承諾「已更新」、「已送出」、「已結案」等寫入結果。

回答案件列表時，優先列出客戶簡稱、客戶代碼、狀態、目前流程、承辦會計師與更新時間。若結果很多，先摘要前幾筆並提醒實際總數。
如果使用者要求的條件不明確，先用合理預設查詢，並簡短說明可再指定狀態、會計師或天數。
如果工具查不到資料，請直接說目前查不到符合條件的案件。
PROMPT;
    }

    public function tools(): iterable
    {
        return [
            new ListRegistrarCasesTool,
            new ShowRegistrarCaseSummaryTool,
            new ListAwaitingPaymentCasesTool,
            new ListStaleRegistrarCasesTool,
            new ListCasesByAccountantTool,
        ];
    }
}
