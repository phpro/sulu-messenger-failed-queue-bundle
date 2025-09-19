<?php

declare(strict_types=1);

namespace Phpro\SuluMessengerFailedQueueBundle\Domain\Query;

use Phpro\SuluMessengerFailedQueueBundle\Domain\Model\FailedMessage;

interface FetchMessageInterface
{
    public function __invoke(int $messageId, bool $withDetails): FailedMessage;
}
