<?php

namespace App\Ai\Agents\Budget;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;

class SpendingAdvisorAgent implements Agent
{
    use Promptable;

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
你是一位專業的個人理財顧問，專門協助家庭管理日常收支。
請根據使用者提供的消費資料，用繁體中文給予具體、實用的節省建議。

回應格式要求：
- 提供 3-5 點具體建議
- 每點建議需包含：問題分析 + 改善方法
- 語氣親切、鼓勵，不批判
- 如有異常消費（單筆金額遠超平均），請特別指出
- 最後給予一句鼓勵的話
INSTRUCTIONS;
    }
}
