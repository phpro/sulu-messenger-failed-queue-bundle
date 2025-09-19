<?php

declare(strict_types=1);

namespace Phpro\SuluMessengerFailedQueueBundle\Presentation\Controller\Admin;

use Phpro\SuluMessengerFailedQueueBundle\Infrastructure\Sulu\Admin\MessengerFailedQueueAdmin;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractSecuredMessengerFailedQueueController implements SecuredControllerInterface
{
    public function getSecurityContext(): string
    {
        return MessengerFailedQueueAdmin::SECURITY_CONTEXT;
    }

    public function getLocale(Request $request): string
    {
        return $request->getLocale();
    }
}
