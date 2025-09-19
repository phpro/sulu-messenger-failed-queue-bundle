<?php

declare(strict_types=1);

namespace Phpro\SuluMessengerFailedQueueBundle\Domain\Query;

use Phpro\SuluMessengerFailedQueueBundle\Domain\Model\FailedMessageCollection;

use Phpro\SuluMessengerFailedQueueBundle\Domain\Model\FailedMessageList;
use Phpro\SuluMessengerFailedQueueBundle\Domain\Repository\FailedQueueRepositoryInterface;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;

final class FetchMessages implements FetchMessagesInterface
{
    public function __construct(
        private readonly FailedQueueRepositoryInterface $repository,
        private readonly FetchMessageInterface $fetchMessage,
    ) {
    }

    public function __invoke(SearchCriteria $criteria): FailedMessageList
    {
        $messages = [];
        foreach ($this->repository->findMessageIds($criteria) as $messageId) {
            try {
                $messages[] = ($this->fetchMessage)($messageId, withDetails: false);
            } catch (MessageDecodingFailedException) {
            }
        }

        return new FailedMessageList(
            new FailedMessageCollection(...$messages),
            $this->repository->count($criteria),
        );
    }
}
