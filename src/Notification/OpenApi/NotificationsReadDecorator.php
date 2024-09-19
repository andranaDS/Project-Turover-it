<?php

namespace App\Notification\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class NotificationsReadDecorator implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    public function __construct(
        OpenApiFactoryInterface $decorated
    ) {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $openApi->getPaths()->addPath('/notifications/read', new PathItem(post: new Operation(
            'get_notificationRead',
            ['Notification'],
            [
                Response::HTTP_OK => [
                    'description' => 'Mark all unread notifications as read',
                ],
            ],
            'Mark all unread notifications as read.',
            'Mark all unread notifications as read.',
        )));

        return $openApi;
    }
}
