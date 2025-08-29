<?php

namespace App\Controller;

use App\Entity\Touit;
use App\Service\TouitService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/touits')]
class TouitController extends AbstractController
{
    public function __construct(
        private TouitService        $service,
        private SerializerInterface $serializer
    )
    {
    }

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->service->list());
    }

    #[Route('/{id<\d+>}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        return $this->json($this->service->get($id));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        /** @var Touit $t */
        $t = $this->serializer->deserialize($request->getContent(), Touit::class, 'json');
        $saved = $this->service->create($t);
        return $this->json($saved, 201);
    }

    #[Route('/{id<\d+>}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->service->delete($id);
        return new JsonResponse(null, 204);
    }
}
