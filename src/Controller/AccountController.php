<?php

namespace App\Controller;

use App\Entity\Account;
use App\Service\AccountFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class AccountController extends BaseController
{
    public function __construct(
        private readonly AccountFactory              $accountFactory,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {
    }
    
    #[Route('/accounts/', name: 'api_accounts', methods: 'GET')]
    public function index(Request $request): Response
    {
        try {
            $accounts = $this->getManagerRegistry()->getRepository(Account::class)->findAllFilteredQuery($request);

            return $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($accounts));
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/accounts/count', methods: ['GET'])]
    public function count(Request $request): Response
    {
        try {
            $accountType = $request->query->get('accountType');

            $count = $this->getManagerRegistry()->getRepository(Account::class)->getAccountCountByType($accountType);

            return $this->getApiService()->setResponse($count);
        } catch(\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/accounts/pending-count', methods: ['GET'])]
    public function pendingCount(Request $request): Response
    {
        try {
            $count = $this->getManagerRegistry()->getRepository(Account::class)->getAccountCountByStatus(Account::ACCOUNT_STATUS_PENDING);

            return $this->getApiService()->setResponse($count);
        } catch(\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/accounts/create', name: 'api_accounts_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $account = $this->accountFactory->create($data, true);

            return $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($account));
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/accounts/{id}', name: 'api_accounts_show', methods: ['GET'])]
    public function show($id): Response
    {
        try {
            $account = $this->getManagerRegistry()->getRepository(Account::class)->find($id);

            if (!$account) {
                throw $this->createNotFoundException();
            }

            return $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($account));
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/accounts/{id}/update', name: 'api_accounts_update', methods: ['PUT', 'PATCH'])]
    public function update($id, Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $account = $this->getManagerRegistry()->getRepository(Account::class)->find($id);

            if (!$account) {
                throw $this->createNotFoundException();
            }

            $this->accountFactory->update($account, $data);

            return $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($account));
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/accounts/{id}/delete', name: 'api_accounts_delete', methods: ['DELETE'])]
    public function delete($id): Response
    {
        try {
            $account = $this->getManagerRegistry()->getRepository(Account::class)->find($id);

            if (!$account) {
                throw $this->createNotFoundException();
            }

            $this->getManagerRegistry()->getRepository(Account::class)->remove($account, true);

            return $this->getApiService()->setResponse('L\'utilisateur et son compte ont été supprimés');
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }
}