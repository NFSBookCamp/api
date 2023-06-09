<?php

namespace App\Controller;

use App\Event\UpdateUserEvent;
use App\Service\UserFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class UserController extends BaseController
{
    public function __construct(
        private readonly UserFactory              $userFactory,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {
    }

    #[Route('/users/', name: 'api_users', methods: 'GET')]
    public function index(): Response
    {
        try {
            $users = $this->getUserRepository()->findAll();
            $response = $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($users));
            $response->headers->set('Content-Range', 'users 0-20/' . count($users));
            return $response;
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/users/create', name: 'api_users_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $user = $this->userFactory->create($data, true);

            return $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($user));
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/users/{id}', name: 'api_users_show', methods: ['GET'])]
    public function show($id): Response
    {
        try {
            $user = $this->getUserRepository()->find($id);

            if (!$user) {
                throw $this->createNotFoundException();
            }

            return $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($user));
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/users/{id}/update', name: 'api_users_update', methods: ['PUT', 'PATCH'])]
    public function update($id, Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->getUserRepository()->find($id);

            if (!$user) {
                throw $this->createNotFoundException();
            }

            $this->userFactory->update($user, $data);

            return $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($user));
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/users/{id}/delete', name: 'api_users_delete', methods: ['DELETE'])]
    public function delete($id): Response
    {
        try {
            $user = $this->getUserRepository()->find($id);

            if (!$user) {
                throw $this->createNotFoundException();
            }

            $this->getUserRepository()->remove($user, true);

            return $this->getApiService()->setResponse('L\'utilisateur a été supprimé');
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }
}
