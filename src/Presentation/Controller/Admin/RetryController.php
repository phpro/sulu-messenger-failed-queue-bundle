<?php

declare(strict_types=1);

namespace Phpro\SuluMessengerFailedQueueBundle\Presentation\Controller\Admin;

use Phpro\SuluMessengerFailedQueueBundle\Domain\Command\RetryHandlerInterface;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function Psl\Type\int;
use function Psl\Type\shape;
use function Psl\Type\vec;

#[Route(path: '/messenger-failed-queue/retry', name: 'phpro.messenger_failed_queue_retry', options: ['expose' => true], methods: ['PUT'])]
final class RetryController extends AbstractSecuredMessengerFailedQueueController implements SecuredControllerInterface
{
    public function __construct(
        private readonly RetryHandlerInterface $handler,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        try {
            $data = shape([
                'identifiers' => vec(int()),
            ])->coerce($request->toArray());

            foreach ($data['identifiers'] as $id) {
                ($this->handler)($id);
            }

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return new JsonResponse(['detail' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
