<?php

namespace App\Controller;

use App\Entity\Discipline;
use App\Service\DisciplineFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class DisciplineController extends BaseController
{
    public function __construct(
        private readonly DisciplineFactory        $disciplineFactory,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {
    }

    #[Route('/disciplines/', name: 'api_disciplines', methods: 'GET')]
    public function index(Request $request): Response
    {
        try {
            $disciplines = $this->getManagerRegistry()->getRepository(Discipline::class)->findAllFilteredQuery($request);
            $response = $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($disciplines));
            return $response;
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/disciplines/create', name: 'api_disciplines_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $discipline = $this->disciplineFactory->create($data, true);

            return $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($discipline));
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/disciplines/{id}', name: 'api_disciplines_show', methods: ['GET'])]
    public function show($id): Response
    {
        try {
            $discipline = $this->getManagerRegistry()->getRepository(Discipline::class)->find($id);

            if (!$discipline) {
                throw $this->createNotFoundException();
            }

            return $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($discipline));
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/disciplines/{id}/update', name: 'api_disciplines_update', methods: ['PUT', 'PATCH'])]
    public function update($id, Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $discipline = $this->getManagerRegistry()->getRepository(Discipline::class)->find($id);

            if (!$discipline) {
                throw $this->createNotFoundException();
            }

            $this->disciplineFactory->update($discipline, $data);

            return $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($discipline));
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/disciplines/{id}/delete', name: 'api_disciplines_delete', methods: ['DELETE'])]
    public function delete($id): Response
    {
        try {
            $discipline = $this->getManagerRegistry()->getRepository(Discipline::class)->find($id);

            if (!$discipline) {
                throw $this->createNotFoundException();
            }

            $this->getManagerRegistry()->getRepository(Discipline::class)->remove($discipline, true);

            return $this->getApiService()->setResponse('Le cours a été supprimé');
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }
}