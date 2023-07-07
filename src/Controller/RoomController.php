<?php

namespace App\Controller;

use App\Entity\Room;
use App\Event\UpdateRoomEvent;
use App\Service\RoomFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class RoomController extends BaseController
{
    public function __construct(
        private readonly RoomFactory              $roomFactory,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {
    }

    #[Route('/rooms/', name: 'api_rooms', methods: 'GET')]
    public function index(Request $request): Response
    {
        try {
            $rooms = $this->getManagerRegistry()->getRepository(Room::class)->findAllFilteredQuery($request);
            $response = $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($rooms));
            return $response;
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/rooms/reserved-count', methods: ['GET'])]
    public function pendingCount(Request $request): Response
    {
        try {
            $count = $this->getManagerRegistry()->getRepository(Room::class)->getRoomCountByStatus(Room::ROOM_STATUS_BOOKED);

            return $this->getApiService()->setResponse($count);
        } catch(\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/rooms/create', name: 'api_rooms_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $room = $this->roomFactory->create($data, true);

            return $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($room));
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/rooms/{id}', name: 'api_rooms_show', methods: ['GET'])]
    public function show($id): Response
    {
        try {
            $room = $this->getManagerRegistry()->getRepository(Room::class)->find($id);

            if (!$room) {
                throw $this->createNotFoundException();
            }

            return $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($room));
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/rooms/{id}/update', name: 'api_rooms_update', methods: ['PUT', 'PATCH'])]
    public function update($id, Request $request): Response
    {
        try {
            $user = $this->getUser();

            $data = json_decode($request->getContent(), true);
            $room = $this->getManagerRegistry()->getRepository(Room::class)->find($id);

            if (!$room) {
                throw $this->createNotFoundException();
            }

            $this->roomFactory->update($room, $data, $user);

            $this->eventDispatcher->dispatch(new UpdateRoomEvent($room), UpdateRoomEvent::NAME);

            return $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($room));
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/rooms/{id}/delete', name: 'api_rooms_delete', methods: ['DELETE'])]
    public function delete($id): Response
    {
        try {
            $room = $this->getManagerRegistry()->getRepository(Room::class)->find($id);

            if (!$room) {
                throw $this->createNotFoundException();
            }

            $this->getManagerRegistry()->getRepository(Room::class)->remove($room, true);

            return $this->getApiService()->setResponse('La salle a été supprimée');
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }
}