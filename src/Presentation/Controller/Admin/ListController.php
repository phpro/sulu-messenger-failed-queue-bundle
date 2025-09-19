<?php

declare(strict_types=1);

namespace Phpro\SuluMessengerFailedQueueBundle\Presentation\Controller\Admin;

use Phpro\SuluMessengerFailedQueueBundle\Domain\Query\FetchMessagesInterface;
use Phpro\SuluMessengerFailedQueueBundle\Domain\Query\SearchCriteria;
use Sulu\Component\Rest\ListBuilder\ListRestHelperInterface;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

use function Psl\Type\int;

#[Route(path: '/messenger-failed-queue', name: 'phpro.messenger_failed_queue_list', options: ['expose' => true], methods: ['GET'])]
final class ListController extends AbstractSecuredMessengerFailedQueueController implements SecuredControllerInterface
{
    public const RESOURCE_KEY = 'phpro_messenger_failed_queue';

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ListRestHelperInterface $listRestHelper,
        private readonly FetchMessagesInterface $fetchMessages,
    ) {
    }

    public function __invoke(): Response
    {
        $limit = int()->coerce($this->listRestHelper->getLimit());

        $failedMessageList = ($this->fetchMessages)(
            new SearchCriteria(
                (string) $this->listRestHelper->getSearchPattern(),
                $this->listRestHelper->getSortColumn(),
                $this->listRestHelper->getSortOrder(),
                (int) $this->listRestHelper->getOffset(),
                $limit
            )
        );

        $listRepresentation = new PaginatedRepresentation(
            $failedMessageList->failedMessageCollection(),
            self::RESOURCE_KEY,
            (int) $this->listRestHelper->getPage(),
            $limit,
            $failedMessageList->totalCount()
        );

        return new JsonResponse(
            $this->serializer->serialize($listRepresentation->toArray(), 'json'),
            json: true
        );
    }
}
