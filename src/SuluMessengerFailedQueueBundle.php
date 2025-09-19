<?php

declare(strict_types=1);

namespace Phpro\SuluMessengerFailedQueueBundle;

use Phpro\SuluMessengerFailedQueueBundle\Infrastructure\Symfony\DependencyInjection\SuluMessengerFailedQueueExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SuluMessengerFailedQueueBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        return new SuluMessengerFailedQueueExtension();
    }
}
