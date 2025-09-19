<?php

declare(strict_types=1);

namespace Phpro\SuluMessengerFailedQueueBundle\Domain\Query;

use Phpro\SuluMessengerFailedQueueBundle\Domain\Model\FailedMessage;

use Symfony\Component\Messenger\Envelope;

use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;

use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Serializer\SerializerInterface;

use function Psl\Type\instance_of;

final class FetchMessage implements FetchMessageInterface
{
    public function __construct(
        private readonly ListableReceiverInterface $receiver,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function __invoke(int $messageId, bool $withDetails): FailedMessage
    {
        $message = $this->receiver->find($messageId);
        instance_of(Envelope::class)->assert($message);

        $messageIdStamp = $message->last(TransportMessageIdStamp::class);
        $lastErrorStamp = $message->last(ErrorDetailsStamp::class);
        $lastRedeliveryStamp = $message->last(RedeliveryStamp::class);

        $failedMessage = new FailedMessage(
            ($messageIdStamp) ? (int) $messageIdStamp->getId() : 0,
            $message->getMessage()::class,
            ($lastErrorStamp) ? $lastErrorStamp->getExceptionMessage() : 'Unknown error',
            ($lastErrorStamp) ? $lastErrorStamp->getExceptionClass() : 'Unknown error class',
            ($lastErrorStamp) ? (string) $lastErrorStamp->getExceptionCode() : '0',
            ($lastRedeliveryStamp) ? $lastRedeliveryStamp->getRedeliveredAt() : new \DateTimeImmutable(),
        );

        if (!$withDetails) {
            return $failedMessage;
        }

        return $failedMessage
            ->withMessageDetails($this->serializer->serialize($message->getMessage(), 'json'))
            ->withMessageBusName($message->last(BusNameStamp::class)?->getBusName())
            ->withMessageOriginalTransport($message->last(SentToFailureTransportStamp::class)?->getOriginalReceiverName())
            ->withErrorTrace($lastErrorStamp?->getFlattenException()?->getAsString())
            ->withFailedDates(...array_map(
                fn (RedeliveryStamp $stamp): \DateTimeInterface => $stamp->getRedeliveredAt(),
                $message->all(RedeliveryStamp::class)
            ));
    }
}
