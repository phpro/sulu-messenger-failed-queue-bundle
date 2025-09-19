<?php

declare(strict_types=1);

namespace Phpro\SuluMessengerFailedQueueBundle\Domain\Query;

use Phpro\SuluMessengerFailedQueueBundle\Domain\Model\FailedMessageList;

interface FetchMessagesInterface
{
    public function __invoke(SearchCriteria $criteria): FailedMessageList;
}
