<?php

declare(strict_types=1);

namespace Phpro\SuluMessengerFailedQueueBundle\Domain\Repository;

use Phpro\SuluMessengerFailedQueueBundle\Domain\Query\SearchCriteria;

interface FailedQueueRepositoryInterface
{
    /**
     * @return int[]
     */
    public function findMessageIds(SearchCriteria $criteria): array;

    public function count(SearchCriteria $criteria): int;
}
