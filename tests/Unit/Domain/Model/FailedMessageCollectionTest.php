<?php

declare(strict_types=1);

namespace Phpro\SuluMessengerFailedQueueBundle\Tests\Unit\Domain\Model;

use Phpro\SuluMessengerFailedQueueBundle\Domain\Model\FailedMessageCollection;
use Phpro\SuluMessengerFailedQueueBundle\Tests\Unit\Domain\Query\FailedMessages;
use PHPUnit\Framework\TestCase;

class FailedMessageCollectionTest extends TestCase
{
    private FailedMessageCollection $collection;

    protected function setUp(): void
    {
        $this->collection = new FailedMessageCollection(
            FailedMessages::testFailedMessage(1, 'Error 1'),
            FailedMessages::testFailedMessage(2, 'Error 2'),
        );
    }

    /** @test */
    public function it_is_iterable(): void
    {
        foreach ($this->collection as $message) {
            self::assertStringStartsWith('Error', $message->getError());
        }
    }

    /** @test */
    public function it_has_a_count(): void
    {
        self::assertSame(2, $this->collection->count());
    }

    /** @test */
    public function it_can_return_first_element_of_collection(): void
    {
        self::assertSame(1, $this->collection->first()?->getId());
    }
}
