<?php

namespace App\Entity;

use App\Entity\Common\DatedInterface;
use App\Entity\Common\SlugInterface;
use \App\Entity\Common\DatedTrait;
use App\Entity\Common\SlugTrait;
use App\Repository\HistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "bookcamp_history")]
#[ORM\Entity(repositoryClass: HistoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class History implements DatedInterface, SlugInterface
{
    use DatedTrait;
    use SlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'logs')]
    private ?Room $room = null;

    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(nullable: false)]
    private ?Discipline $discipline = null;

    #[ORM\ManyToOne(inversedBy: 'logs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $booked_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $room_booked_at = null;

    #[ORM\Column(nullable: true)]
    private ?int $room_booking_delay = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->setSlug($this->getRoom()->getSlug());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function getDiscipline(): ?Discipline
    {
        return $this->discipline;
    }

    public function setDiscipline(Discipline $discipline): self
    {
        $this->discipline = $discipline;

        return $this;
    }

    public function getBookedBy(): ?Account
    {
        return $this->booked_by;
    }

    public function setBookedBy(Account $booked_by): self
    {
        $this->booked_by = $booked_by;

        return $this;
    }

    public function getRoomBookedAt(): ?\DateTimeInterface
    {
        return $this->room_booked_at;
    }

    public function setRoomBookedAt(?\DateTimeInterface $room_booked_at): self
    {
        $this->room_booked_at = $room_booked_at;

        return $this;
    }

    public function getRoomBookingDelay(): ?int
    {
        return $this->room_booking_delay;
    }

    public function setRoomBookingDelay(?int $room_booking_delay): self
    {
        $this->room_booking_delay = $room_booking_delay;

        return $this;
    }
}
