<?php

namespace App\Controller;

use App\Entity\History;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class HistoryController extends BaseController
{
    #[Route('/history/', name: 'api_history', methods: 'GET')]
    public function index(): Response
    {
        try {
            $history = $this->getManagerRegistry()->getRepository(History::class)->findAll();
            $response = $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($history));
            return $response;
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/history/{id}', name: 'api_history_show', methods: ['GET'])]
    public function show($id): Response
    {
        try {
            $history = $this->getManagerRegistry()->getRepository(History::class)->find($id);

            if (!$history) {
                throw $this->createNotFoundException();
            }

            return $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($history));
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/history/{id}/delete', name: 'api_history_delete', methods: ['DELETE'])]
    public function delete($id): Response
    {
        try {
            $history = $this->getManagerRegistry()->getRepository(History::class)->find($id);

            if (!$history) {
                throw $this->createNotFoundException();
            }

            $this->getManagerRegistry()->getRepository(History::class)->remove($history, true);

            return $this->getApiService()->setResponse('L\'entrée d\'historiques a été supprimée');
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }
}