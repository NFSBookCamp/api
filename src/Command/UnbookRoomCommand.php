<?php

namespace App\Command;

use App\Repository\RoomRepository;
use App\Entity\Room;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:unbook-room',
    description: 'Add a short description for your command',
)]
class UnbookRoomCommand extends Command
{
    public function __construct(private RoomRepository $roomRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $bookedRooms = $this->roomRepository->findBy(['reserved' => true]);

        if (count($bookedRooms) > 0) {
            $i = 0;
            foreach ($bookedRooms as $room) {
                $now = new \DateTime();
                $reservationEndDate = $room->getBookedOn();

                if($reservationEndDate < $now) {
                    $room->setReserved(false);
                    $room->setBookedBy(null);
                    $room->setBookedAt(null);
                    $room->setBookedOn(null);
                    $room->setDiscipline(null);
                    $room->setStatus(Room::ROOM_STATUS_VACANT);

                    $i++;

                    $this->roomRepository->save($room, true);
                }
            }

            $io->success($i . ' salles libérées.');
        } else {
            $io->success('0 résultat');
        }

        return Command::SUCCESS;
    }
}
