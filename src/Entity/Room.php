<?php

namespace App\Entity;

use App\Entity\Common\DatedInterface;
use App\Entity\Common\DatedTrait;
use App\Entity\Common\SlugInterface;
use App\Entity\Common\SlugTrait;
use App\Repository\RoomRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room implements DatedInterface, SlugInterface
{
    use DatedTrait;
    use SlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $number = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $booked_at = null;

    #[ORM\Column(nullable: true)]
    private ?int $booking_delay = null;

    #[ORM\Column(nullable: true)]
    private array $caracteristics = [];

    #[ORM\ManyToOne(inversedBy: 'rooms')]
    private ?Account $booked_by = null;

    #[ORM\ManyToOne]
    private ?Discipline $discipline = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getBookedAt(): ?\DateTimeInterface
    {
        return $this->booked_at;
    }

    public function setBookedAt(?\DateTimeInterface $booked_at): self
    {
        $this->booked_at = $booked_at;

        return $this;
    }

    public function getBookingDelay(): ?int
    {
        return $this->booking_delay;
    }

    public function setBookingDelay(?int $booking_delay): self
    {
        $this->booking_delay = $booking_delay;

        return $this;
    }

    public function getCaracteristics(): array
    {
        return $this->caracteristics;
    }

    public function setCaracteristics(?array $caracteristics): self
    {
        $this->caracteristics = $caracteristics;

        return $this;
    }

    public function getBookedBy(): ?Account
    {
        return $this->booked_by;
    }

    public function setBookedBy(?Account $booked_by): self
    {
        $this->booked_by = $booked_by;

        return $this;
    }

    public function getDiscipline(): ?Discipline
    {
        return $this->discipline;
    }

    public function setDiscipline(?Discipline $discipline): self
    {
        $this->discipline = $discipline;

        return $this;
    }
}
