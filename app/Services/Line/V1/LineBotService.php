<?php

namespace App\Services\Line\V1;

use App\Models\Line\LineUser;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Api\MessagingApiBlobApi;
use LINE\Clients\MessagingApi\Configuration;
use LINE\Clients\MessagingApi\Model\ImageMessage;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Constants\HTTPHeader;
use LINE\Constants\MessageContentProviderType;
use LINE\Constants\MessageType;
use LINE\Parser\EventRequestParser;
use LINE\Parser\Exception\InvalidEventRequestException;
use LINE\Parser\Exception\InvalidSignatureException;
use LINE\Webhook\Model\AudioMessageContent;
use LINE\Webhook\Model\FileMessageContent;
use LINE\Webhook\Model\FollowEvent;
use LINE\Webhook\Model\ImageMessageContent;
use LINE\Webhook\Model\JoinEvent;
use LINE\Webhook\Model\LeaveEvent;
use LINE\Webhook\Model\LocationMessageContent;
use LINE\Webhook\Model\MemberJoinedEvent;
use LINE\Webhook\Model\MemberLeftEvent;
use LINE\Webhook\Model\MessageEvent;
use LINE\Webhook\Model\PostbackEvent;
use LINE\Webhook\Model\StickerMessageContent;
use LINE\Webhook\Model\TextMessageContent;
use LINE\Webhook\Model\UnfollowEvent;
use LINE\Webhook\Model\UnsendEvent;
use LINE\Webhook\Model\VideoMessageContent;
use LINE\Webhook\Model\VideoPlayCompleteEvent;
use Throwable;

class LineBotService
{
    private MessagingApiApi $messagingApi;

    private MessagingApiBlobApi $messagingBlobApi;

    private LineAiChatService $aiChatService;

    public function __construct(?LineAiChatService $aiChatService = null)
    {
        $config = new Configuration();
        $config->setAccessToken((string) config('services.line.channel_access_token'));

        $client = new Client();
        $this->messagingApi = new MessagingApiApi(client: $client, config: $config);
        $this->messagingBlobApi = new MessagingApiBlobApi(client: $client, config: $config);
        $this->aiChatService = $aiChatService ?? app(LineAiChatService::class);
    }

    public function handleWebhook(Request $request): Response
    {
        $payload = $request->getContent();
        Log::info('line.webhook.received', ['payload' => $payload]);

        try {
            $parsedEvents = EventRequestParser::parseEventRequest(
                $payload,
                (string) config('services.line.channel_secret'),
                $request->header(HTTPHeader::LINE_SIGNATURE)
            );
        } catch (InvalidSignatureException|InvalidEventRequestException $e) {
            Log::warning('line.webhook.invalid_request', ['error' => $e->getMessage()]);

            return response('Invalid request', 400);
        }

        foreach ($parsedEvents->getEvents() as $event) {
            try {
                $this->handleEvent($event);
            } catch (Throwable $e) {
                Log::error('line.webhook.handle_event_failed', [
                    'event_class' => get_class($event),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response('OK', 200);
    }

    private function handleEvent(object $event): void
    {
        if ($event instanceof MessageEvent) {
            $this->handleMessageEvent($event);

            return;
        }

        if ($event instanceof UnsendEvent) {
            $this->handleUnsendEvent($event);

            return;
        }

        if ($event instanceof FollowEvent) {
            $this->handleFollowEvent($event);

            return;
        }

        if ($event instanceof UnfollowEvent) {
            $this->handleUnfollowEvent($event);

            return;
        }

        if ($event instanceof JoinEvent) {
            $this->handleJoinEvent($event);

            return;
        }

        if ($event instanceof LeaveEvent) {
            $this->handleLeaveEvent($event);

            return;
        }

        if ($event instanceof MemberJoinedEvent) {
            $this->handleMemberJoinedEvent($event);

            return;
        }

        if ($event instanceof MemberLeftEvent) {
            $this->handleMemberLeftEvent($event);

            return;
        }

        if ($event instanceof PostbackEvent) {
            $this->handlePostbackEvent($event);

            return;
        }

        if ($event instanceof VideoPlayCompleteEvent) {
            $this->handleVideoPlayCompleteEvent($event);
        }
    }

    private function handleMessageEvent(MessageEvent $event): void
    {
        $message = $event->getMessage();

        if ($message instanceof TextMessageContent) {
            $this->handleTextMessage($event, $message);

            return;
        }

        if ($message instanceof ImageMessageContent) {
            $this->handleImageMessage($event, $message);

            return;
        }

        if ($message instanceof VideoMessageContent) {
            $this->handleVideoMessage($event, $message);

            return;
        }

        if ($message instanceof AudioMessageContent) {
            $this->handleAudioMessage($event, $message);

            return;
        }

        if ($message instanceof FileMessageContent) {
            $this->handleFileMessage($event, $message);

            return;
        }

        if ($message instanceof LocationMessageContent) {
            $this->handleLocationMessage($event, $message);

            return;
        }

        if ($message instanceof StickerMessageContent) {
            $this->handleStickerMessage($event, $message);
        }
    }

    private function handleUnsendEvent(UnsendEvent $event): void
    {
        // TODO: 預留收回訊息事件處理（審計/刪除追蹤）
        Log::info('line.webhook.unsend_event.received', ['message_id' => $event->getUnsend()->getMessageId()]);
    }

    private function handleFollowEvent(FollowEvent $event): void
    {
        $source = $event->getSource();
        $userId = (string) $source->offsetGet('userId');
        $profile = $this->messagingApi->getProfile($userId);

        LineUser::updateOrCreate(
            ['user_id' => $profile->getUserId()],
            [
                'name' => $profile->getDisplayName(),
                'status' => true,
            ]
        );

        $this->replyText($event->getReplyToken(), '歡迎加入，輸入 help 查看可用功能。');
    }

    private function handleUnfollowEvent(UnfollowEvent $event): void
    {
        $source = $event->getSource();
        $userId = (string) $source->offsetGet('userId');

        LineUser::where('user_id', $userId)->update([
            'status' => false,
        ]);
    }

    private function handleJoinEvent(JoinEvent $event): void
    {
        // TODO: 預留加入群組/聊天室事件處理
        Log::info('line.webhook.join_event.received');
    }

    private function handleLeaveEvent(LeaveEvent $event): void
    {
        // TODO: 預留離開群組/聊天室事件處理
        Log::info('line.webhook.leave_event.received');
    }

    private function handleMemberJoinedEvent(MemberJoinedEvent $event): void
    {
        // TODO: 預留群組成員加入事件處理
        Log::info('line.webhook.member_joined_event.received');
    }

    private function handleMemberLeftEvent(MemberLeftEvent $event): void
    {
        // TODO: 預留群組成員離開事件處理
        Log::info('line.webhook.member_left_event.received');
    }

    private function handlePostbackEvent(PostbackEvent $event): void
    {
        $data = (string) $event->getPostback()->getData();
        parse_str($data, $params);
        $action = is_string($params['action'] ?? null) ? $params['action'] : null;

        match ($action) {
            'fortune' => $this->replyText($event->getReplyToken(), $this->randomFortuneMessage()),
            'idea' => $this->replyText($event->getReplyToken(), $this->randomIdeaMessage()),
            'picker' => $this->replyText($event->getReplyToken(), $this->randomWhoSpeaksMessage()),
            'menu' => $this->replyMainMenuFlex($event->getReplyToken()),
            default => $this->replyText($event->getReplyToken(), '還不認得這個按鈕，試試看輸入 help 或 menu 開主選單。'),
        };
    }

    private function handleVideoPlayCompleteEvent(VideoPlayCompleteEvent $event): void
    {
        // TODO: 預留影片播放完成事件處理（行為追蹤/獎勵流程）
        Log::info('line.webhook.video_play_complete_event.received');
    }

    private function handleTextMessage(MessageEvent $event, TextMessageContent $message): void
    {
        $normalized = mb_strtolower(trim($message->getText()));
        if (in_array($normalized, ['help', 'menu', '功能'], true)) {
            $this->replyMainMenuFlex($event->getReplyToken());

            return;
        }

        $aiReply = $this->aiChatService->resolveReply($message->getText());
        if ($aiReply !== null) {
            $this->replyText($event->getReplyToken(), $aiReply);

            return;
        }

        $reply = $this->resolveTextReply($message->getText());
        $this->replyText($event->getReplyToken(), $reply);
    }

    private function handleImageMessage(MessageEvent $event, ImageMessageContent $message): void
    {
        $contentProvider = $message->getContentProvider();

        if ($contentProvider->getType() === MessageContentProviderType::EXTERNAL) {
            $this->messagingApi->replyMessage(new ReplyMessageRequest([
                'replyToken' => $event->getReplyToken(),
                'messages' => [
                    new ImageMessage([
                        'type' => MessageType::IMAGE,
                        'originalContentUrl' => $contentProvider->getOriginalContentUrl(),
                        'previewImageUrl' => $contentProvider->getPreviewImageUrl(),
                    ]),
                ],
            ]));

            return;
        }

        $contentId = $message->getId();
        $sfo = $this->messagingBlobApi->getMessageContent($contentId);

        $imageName = str()->ulid().'.webp';
        Image::decode($sfo->fread($sfo->getSize()))
            ->scaleDown(1200)
            ->save("line/uploads/img/{$imageName}");

        $imageUrl = asset("line/uploads/img/{$imageName}");
        $this->replyTextMessages($event->getReplyToken(), [
            $imageUrl,
            $this->aiChatService->analyzeImageUrl($imageUrl),
        ]);
    }

    private function handleVideoMessage(MessageEvent $event, VideoMessageContent $message): void
    {
        // TODO: 預留影片訊息處理（轉檔、儲存、回覆）
        Log::info('line.webhook.video_message.received', ['message_id' => $message->getId()]);
    }

    private function handleAudioMessage(MessageEvent $event, AudioMessageContent $message): void
    {
        // TODO: 預留語音訊息處理（語音辨識、儲存、回覆）
        Log::info('line.webhook.audio_message.received', ['message_id' => $message->getId()]);
    }

    private function handleFileMessage(MessageEvent $event, FileMessageContent $message): void
    {
        // TODO: 預留檔案訊息處理（檔案下載、驗證、儲存）
        Log::info('line.webhook.file_message.received', ['message_id' => $message->getId()]);
    }

    private function handleLocationMessage(MessageEvent $event, LocationMessageContent $message): void
    {
        // TODO: 預留位置訊息處理（地理編碼、附近門市）
        Log::info('line.webhook.location_message.received', ['title' => $message->getTitle()]);
    }

    private function handleStickerMessage(MessageEvent $event, StickerMessageContent $message): void
    {
        // TODO: 預留貼圖訊息處理（互動回覆、貼圖分析）
        Log::info('line.webhook.sticker_message.received', ['sticker_id' => $message->getStickerId()]);
    }

    private function resolveTextReply(string $text): string
    {
        return match (mb_strtolower(trim($text))) {
            'ping' => 'pong',
            'id' => '你可以先用這個 LINE ID 到網站做綁定流程（下一步可接綁定功能）。',
            default => $text,
        };
    }

    private function replyMainMenuFlex(string $replyToken): void
    {
        $this->messagingApi->replyMessage(new ReplyMessageRequest([
            'replyToken' => $replyToken,
            'messages' => [
                LineMainMenuFlexFactory::make(),
            ],
        ]));
    }

    private function randomFortuneMessage(): string
    {
        $lines = [
            '今日手氣：靈感像棉花糖分開包裝，一口接一口，停不下來。',
            '今日手氣：剪刀石頭布你贏，但裁布線要先打結。',
            '今日手氣：適合從小零件做起，最後變大作品的那種小確幸感。',
            '今日手氣：問問群組夥伴「如果你只能選一色線，會選什麼？」從顏色開始聊。',
            '今日手氣：隨便翻一頁圖片或雜誌，看到的第一個圖形就是你的今日主題。',
        ];

        return $lines[array_rand($lines)];
    }

    private function randomIdeaMessage(): string
    {
        $lines = [
            '靈感一抽：做一個能放 3 根筆的布筆套，內層可撞色。',
            '靈感一抽：幫家裡的遙控器做「防摔束口袋」，團圓閒聊就聊「最近最常按的按鈕」。',
            '靈感一抽：用布條捲一個小杯墊，再跟群友交換一個「不務正業用途」的點子。',
            '靈感一抽：以「一個圓」為主題，做實用或沒用都可以，重點是讓大家猜用途。',
            '靈感一抽：兩人一人出一個材料，湊一個群組小挑戰接力。',
        ];

        return $lines[array_rand($lines)];
    }

    private function randomWhoSpeaksMessage(): string
    {
        $lines = [
            '先說話的勇氣交給宇宙：在群裡丟一個 @，請那人先分享「本週最療癒的一件事」。沒有 @ 的話，就從丟出這則的人開始。',
            '先說話的勇氣交給隨機：從 A 到 Z 喊一個字母，最後接力的那位先開口。',
            '先說話的勇氣交給猜拳：群組內 3、2、1 出拳，贏的指定下一個人說一句。',
            '先說話的勇氣交給倒數：大家同時丟一個「正在做的事」，最先跟手作有關的那個人先當小隊長。',
        ];

        return $lines[array_rand($lines)];
    }

    private function replyText(string $replyToken, string $text): void
    {
        $this->replyTextMessages($replyToken, [$text]);
    }

    private function replyTextMessages(string $replyToken, array $texts): void
    {
        $this->replyMessages($replyToken, array_map(
            fn (string $text) => $this->makeTextMessage($text),
            $texts
        ));
    }

    private function makeTextMessage(string $text): TextMessage
    {
        return new TextMessage([
            'type' => MessageType::TEXT,
            'text' => $text,
        ]);
    }

    private function replyMessages(string $replyToken, array $messages): void
    {
        $this->messagingApi->replyMessage(new ReplyMessageRequest([
            'replyToken' => $replyToken,
            'messages' => $messages,
        ]));
    }
}
