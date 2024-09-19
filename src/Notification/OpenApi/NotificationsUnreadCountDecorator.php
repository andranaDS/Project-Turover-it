<?php

namespace App\Notification\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class NotificationsUnreadCountDecorator implements OpenApiFactoryInterface
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

        $openApi->getPaths()->addPath('/notifications/unread/count', new PathItem(get: new Operation(
            'get_notificationUnreadCount',
            ['Notification'],
            [
                Response::HTTP_OK => [
                    'description' => 'Unread notifications count',
                ],
            ],
            'Retrieves the unread notifications count of the current recruiter.',
            'Retrieves the unread notifications count of the current recruiter.',
        )));

        return $openApi;
    }
}
