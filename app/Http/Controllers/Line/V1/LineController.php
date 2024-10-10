<?php

namespace App\Http\Controllers\Line\V1;

use App\Http\Controllers\Controller;
use App\Models\LineUser;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
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

class LineController extends Controller
{
    public function webhook(Request $request)
    {
        info(json_encode($request->input(), JSON_UNESCAPED_UNICODE));

        try {
            $parsedEvents = EventRequestParser::parseEventRequest(
                json_encode($request->input(), JSON_UNESCAPED_UNICODE),
                config('services.line.channel_secret'),
                $request->header(HTTPHeader::LINE_SIGNATURE)
            );
        } catch (InvalidSignatureException $e) {
            Log::error($e->getMessage());
        } catch (InvalidEventRequestException $e) {
            Log::error($e->getMessage());
        }

        $client = new Client();
        $config = new Configuration();
        $config->setAccessToken(config('services.line.channel_access_token'));
        $messagingApi = new MessagingApiApi(
            client: $client,
            config: $config,
        );

        $messagingBlobApi = new MessagingApiBlobApi(
            client: $client,
            config: $config,
        );

        foreach ($parsedEvents->getEvents() as $event) {
            // 訊息事件
            if ($event instanceof MessageEvent) {
                $message = $event->getMessage();

                // 文字訊息
                if ($message instanceof TextMessageContent) {
                    $messagingApi->replyMessage(new ReplyMessageRequest([
                        'replyToken' => $event->getReplyToken(),
                        'messages' => [
                            new TextMessage([
                                'type' => 'text',
                                'text' => $message->getText(),
                            ]),
                        ],
                    ]));

                    return response('OK', 200);
                }

                // 圖片訊息
                if ($message instanceof ImageMessageContent) {
                    $contentProvider = $message->getContentProvider();

                    if ($contentProvider->getType() === MessageContentProviderType::EXTERNAL) {
                        $messagingApi->replyMessage(new ReplyMessageRequest([
                            'replyToken' => $event->getReplyToken(),
                            'messages' => [
                                new ImageMessage([
                                    'type' => MessageType::IMAGE,
                                    'originalContentUrl' => $contentProvider->getOriginalContentUrl(),
                                    'previewImageUrl' => $contentProvider->getPreviewImageUrl(),
                                ]),
                            ],
                        ]));

                        return response('OK', 200);
                    }

                    $contentId = $message->getId();
                    $sfo = $messagingBlobApi->getMessageContent($contentId);

                    $imageName = str()->ulid() . '.webp';
                    Image::read($sfo->fread($sfo->getSize()))->scaleDown(1200)->toWebp()->save("lineimg/{$imageName}");

                    $messagingApi->replyMessage(new ReplyMessageRequest([
                        'replyToken' => $event->getReplyToken(),
                        'messages' => [
                            new TextMessage([
                                'type' => 'text',
                                'text' => asset("lineimg/{$imageName}"),
                            ]),
                        ],
                    ]));

                    return response('OK', 200);
                }

                // 影片訊息
                if ($message instanceof VideoMessageContent) {
                }

                // 聲音訊息
                if ($message instanceof AudioMessageContent) {
                }

                // 檔案訊息
                if ($message instanceof FileMessageContent) {
                }

                // 位置訊息
                if ($message instanceof LocationMessageContent) {
                }

                // 貼圖訊息
                if ($message instanceof StickerMessageContent) {
                }

                return response('OK', 200);
            }

            // 取消發送事件
            if ($event instanceof UnsendEvent) {
            }

            // 追蹤事件
            if ($event instanceof FollowEvent) {
                $source = $event->getSource();
                $user = $messagingApi->getProfile($source->offsetGet('userId'));

                LineUser::create([
                    'user_id' => $user->getUserId(),
                    'name' => $user->getDisplayName(),
                ]);

                $messagingApi->replyMessage(new ReplyMessageRequest([
                    'replyToken' => $event->getReplyToken(),
                    'messages' => [
                        new TextMessage([
                            'type' => 'text',
                            'text' => '歡迎加入，9527 是你的終生代號',
                        ]),
                    ],
                ]));

                return response('OK', 200);
            }

            // 封鎖事件
            if ($event instanceof UnfollowEvent) {
                $source = $event->getSource();
                $user = $messagingApi->getProfile($source->offsetGet('userId'));

                LineUser::where('user_id', $user->getUserId())->update([
                    'status' => false,
                ]);

                return response('OK', 200);
            }

            // 加入事件
            if ($event instanceof JoinEvent) {
            }

            // 離開事件
            if ($event instanceof LeaveEvent) {
            }

            // 成員加入事件
            if ($event instanceof MemberJoinedEvent) {
            }

            // 成員離開事件
            if ($event instanceof MemberLeftEvent) {
            }

            // 回發事件
            if ($event instanceof PostbackEvent) {
            }

            // 影片觀看完成事件
            if ($event instanceof VideoPlayCompleteEvent) {
            }
        }

        return response('OK', 200);
    }
}
