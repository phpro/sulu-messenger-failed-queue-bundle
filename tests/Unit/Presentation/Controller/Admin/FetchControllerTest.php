<?php

declare(strict_types=1);

use Phpro\SuluMessengerFailedQueueBundle\Domain\Query\FetchMessageInterface;
use Phpro\SuluMessengerFailedQueueBundle\Presentation\Controller\Admin\FetchController;
use Phpro\SuluMessengerFailedQueueBundle\Tests\Unit\Domain\Query\FailedMessages;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class FetchControllerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|SerializerInterface $serializer;
    private FetchMessageInterface|ObjectProphecy $fetchMessage;
    private FetchController $controller;

    protected function setUp(): void
    {
        $this->serializer = $this->prophesize(SerializerInterface::class);
        $this->fetchMessage = $this->prophesize(FetchMessageInterface::class);
        $this->controller = new FetchController(
            $this->serializer->reveal(),
            $this->fetchMessage->reveal()
        );
    }

    /** @test */
    public function it_is_a_secured_controller(): void
    {
        self::assertInstanceOf(SecuredControllerInterface::class, $this->controller);
        self::assertSame('phpro_failed_queue', $this->controller->getSecurityContext());
        self::assertSame('en', $this->controller->getLocale(new Request()));
    }

    /** @test */
    public function it_can_fetch_one_failed_message(): void
    {
        $this->fetchMessage->__invoke($id = 1, true)
            ->willReturn($failedMessage = FailedMessages::testFailedMessage());

        $this->serializer->serialize($failedMessage, 'json')
            ->willReturn($serializedData = '{"id": 1, "error": "Error 1"}');

        $response = ($this->controller)($id);

        self::assertSame($serializedData, $response->getContent());
        self::assertSame(200, $response->getStatusCode());
    }
}
