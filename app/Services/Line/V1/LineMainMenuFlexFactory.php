<?php

namespace App\Services\Line\V1;

use LINE\Clients\MessagingApi\Model\FlexBubble;
use LINE\Clients\MessagingApi\Model\FlexMessage;

class LineMainMenuFlexFactory
{
    /**
     * 主選單 Flex：簡單、可讓群組內一鍵觸發小互動（皆走 postback 於 LineBotService 實作）。
     */
    public static function make(): FlexMessage
    {
        $bubble = new FlexBubble([
            'type' => 'bubble',
            'size' => 'kilo',
            'header' => [
                'type' => 'box',
                'layout' => 'vertical',
                'paddingAll' => '16px',
                'backgroundColor' => '#5B21B6',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => 'Sally 手作小幫手',
                        'color' => '#FFFFFF',
                        'size' => 'lg',
                        'weight' => 'bold',
                    ],
                    [
                        'type' => 'text',
                        'text' => '群組靈感＆破冰 demo',
                        'color' => '#E9D5FF',
                        'size' => 'xs',
                        'margin' => 'sm',
                    ],
                ],
            ],
            'body' => [
                'type' => 'box',
                'layout' => 'vertical',
                'spacing' => 'md',
                'paddingAll' => '12px',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => '底下按鈕是給群組裡玩的小互動：隨機靈感、隨機幸運、甚至「先說話的勇氣」都交給機器人。',
                        'wrap' => true,
                        'size' => 'sm',
                        'color' => '#555555',
                    ],
                ],
            ],
            'footer' => [
                'type' => 'box',
                'layout' => 'vertical',
                'spacing' => 'sm',
                'paddingAll' => '12px',
                'contents' => [
                    self::postbackButton('今日手氣', '手抽一句今日幸運，破冰用', 'action=fortune'),
                    self::postbackButton('靈感一抽', '不知道要做什麼就按這個', 'action=idea'),
                    self::postbackButton('誰先說', '丟一個隨機開場，讓討論動起來', 'action=picker'),
                    self::postbackButton('重開主選單', '把這則訊息再顯示一次', 'action=menu'),
                ],
            ],
        ]);

        return new FlexMessage([
            'type' => 'flex',
            'altText' => 'Sally 手作小幫手主選單',
            'contents' => $bubble,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private static function postbackButton(string $label, string $hint, string $data): array
    {
        return [
            'type' => 'box',
            'layout' => 'vertical',
            'spacing' => 'xs',
            'contents' => [
                [
                    'type' => 'button',
                    'style' => 'primary',
                    'height' => 'sm',
                    'color' => '#7C3AED',
                    'action' => [
                        'type' => 'postback',
                        'label' => $label,
                        'data' => $data,
                    ],
                ],
                [
                    'type' => 'text',
                    'text' => $hint,
                    'size' => 'xxs',
                    'color' => '#888888',
                    'offsetTop' => '2px',
                ],
            ],
        ];
    }
}
