<?php

declare(strict_types=1);

use Phpro\SuluMessengerFailedQueueBundle\Domain\Model\FailedMessageCollection;
use Phpro\SuluMessengerFailedQueueBundle\Domain\Model\FailedMessageList;
use Phpro\SuluMessengerFailedQueueBundle\Domain\Query\FetchMessagesInterface;
use Phpro\SuluMessengerFailedQueueBundle\Domain\Query\SearchCriteria;
use Phpro\SuluMessengerFailedQueueBundle\Presentation\Controller\Admin\ListController;
use Phpro\SuluMessengerFailedQueueBundle\Tests\Unit\Domain\Query\FailedMessages;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sulu\Component\Rest\ListBuilder\ListRestHelperInterface;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class ListControllerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|SerializerInterface $serializer;
    private ObjectProphecy|ListRestHelperInterface $listRestHelper;
    private FetchMessagesInterface|ObjectProphecy $fetchMessages;
    private ListController $controller;

    protected function setUp(): void
    {
        $this->serializer = $this->prophesize(SerializerInterface::class);
        $this->listRestHelper = $this->prophesize(ListRestHelperInterface::class);
        $this->fetchMessages = $this->prophesize(FetchMessagesInterface::class);

        $this->controller = new ListController(
            $this->serializer->reveal(),
            $this->listRestHelper->reveal(),
            $this->fetchMessages->reveal(),
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
    public function it_can_fetch_a_paginated_list(): void
    {
        $this->listRestHelper->getSortColumn()->willReturn(null);
        $this->listRestHelper->getSortOrder()->willReturn(null);
        $this->listRestHelper->getSearchPattern()->willReturn(null);
        $this->listRestHelper->getPage()->willReturn(1);
        $this->listRestHelper->getLimit()->willReturn($limit = 2);
        $this->listRestHelper->getOffset()->willReturn($offset = 0);

        $this->fetchMessages->__invoke(new SearchCriteria(
            '',
            null,
            null,
            $offset,
            $limit
        ))->willReturn(new FailedMessageList(
            new FailedMessageCollection(
                FailedMessages::testFailedMessage(1, 'Error 1'),
                FailedMessages::testFailedMessage(2, 'Error 2'),
            ),
            2
        ))->shouldBeCalledOnce();

        $this->serializer->serialize(Argument::type('array'), 'json')
            ->willReturn($serializedData = '{"_embedded": {"phpro_messenger_failed_queue": []}, "limit": 10, "total": 2, "page": 1, "pages": 1}');

        $response = ($this->controller)();

        self::assertSame($serializedData, $response->getContent());
        self::assertSame(200, $response->getStatusCode());
    }

    /** @test */
    public function it_can_fetch_a_paginated_filtered_and_sorted_list(): void
    {
        $this->listRestHelper->getSortColumn()->willReturn($sortColumn = 'id');
        $this->listRestHelper->getSortOrder()->willReturn($sortOrder = 'ASC');
        $this->listRestHelper->getSearchPattern()->willReturn($searchPattern = 'Error 1');
        $this->listRestHelper->getPage()->willReturn(1);
        $this->listRestHelper->getLimit()->willReturn($limit = 2);
        $this->listRestHelper->getOffset()->willReturn($offset = 0);

        $this->fetchMessages->__invoke(new SearchCriteria(
            $searchPattern,
            $sortColumn,
            $sortOrder,
            $offset,
            $limit
        ))->willReturn(new FailedMessageList(
            new FailedMessageCollection(
                FailedMessages::testFailedMessage(1, 'Error 1'),
            ),
            2
        ))->shouldBeCalledOnce();

        $this->serializer->serialize(Argument::type('array'), 'json')
            ->willReturn($serializedData = '{"_embedded": {"phpro_messenger_failed_queue": []}, "limit": 10, "total": 1, "page": 1, "pages": 1}');

        $response = ($this->controller)();

        self::assertSame($serializedData, $response->getContent());
        self::assertSame(200, $response->getStatusCode());
    }
}
