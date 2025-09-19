<?php

declare(strict_types=1);

namespace Phpro\SuluMessengerFailedQueueBundle\Tests\Unit\Domain\Query;

use Phpro\SuluMessengerFailedQueueBundle\Domain\Model\FailedMessage;

class FailedMessages
{
    public static function testFailedMessage(int $id = 1, string $error = 'Error message'): FailedMessage
    {
        return new FailedMessage(
            $id,
            \stdClass::class,
            $error,
            \Exception::class,
            '0',
            new \DateTimeImmutable('2023-01-01')
        );
    }
}
