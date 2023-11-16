<?php

namespace App\Entity;

use App\Repository\FacilityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Objective;

use DateTimeZone;
use DateTime;

#[ORM\Entity(repositoryClass: FacilityRepository::class)]
#[ORM\MaxDepth(1)]
class Facility
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'facility', targetEntity: Room::class, orphanRemoval: true)]
    private Collection $rooms;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sector = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ? Objective $objective = null;

    public function __construct()
    {
        $this->rooms = new ArrayCollection();
        $this->objective = new Objective;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Room $room): self
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms->add($room);
            $room->setFacility($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): self
    {
        if ($this->rooms->removeElement($room)) {
            // set the owning side to null (unless already changed)
            if ($room->getFacility() === $this) {
                $room->setFacility(null);
            }
        }

        return $this;
    }

    public function getSector(): ?string
    {
        return $this->sector;
    }

    public function setSector(?string $sector): self
    {
        $this->sector = $sector;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getObjective(): ? Objective
    {
        if (!$this->objective) {
            $this->objective = new Objective();
            $now = new \DateTime("now", new DateTimeZone('Europe/Paris'));
            $now->setTime(date('H'), date('i'), date('s'));
            $this->objective->setStartDate($now);
        }
        return $this->objective;
    }

    public function setObjective(?Objective $objective)
    {
        $this->objective = $objective;
    }

}