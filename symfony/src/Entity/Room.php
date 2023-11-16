<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
#[ORM\MaxDepth(1)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $surface = null;

    #[ORM\Column(nullable: true)]
    private ?int $nb_windows = null;

    #[ORM\ManyToOne(inversedBy: 'rooms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Facility $facility = null;

    #[ORM\OneToOne(mappedBy: 'room', targetEntity: Sensor::class, orphanRemoval: true)]
    private Sensor|null $sensor = null;
<<<<<<< HEAD

    #[ORM\Column(nullable: false)]
    private ?string $num = null;
=======
>>>>>>> 8df0513a3810338a9dc3a94aa0caac74c6ba07fd

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Objective $objective = null;

    #[ORM\Column(length: 25)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $floor = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'rooms')]
    private Collection $owner;

    #[ORM\Column]
    private ?bool $private = null;

    public function __construct()
<<<<<<< HEAD
    {}
=======
    {
        $this->owner = new ArrayCollection();
        $this->objective = new Objective;
    }
>>>>>>> 8df0513a3810338a9dc3a94aa0caac74c6ba07fd

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSurface(): ?int
    {
        return $this->surface;
    }

    public function setSurface(?int $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function getNbWindows(): ?int
    {
        return $this->nb_windows;
    }

    public function setNbWindows(?int $nb_windows): self
    {
        $this->nb_windows = $nb_windows;

        return $this;
    }

    public function getFacility(): ?Facility
    {
        return $this->facility;
    }

    public function setFacility(?Facility $facility): self
    {
        $this->facility = $facility;

        return $this;
    }

    public function getSensor(): Sensor|null
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $sensor): self
    {
        $this->sensor->add($sensor);
        $sensor->setRoom($this);

        return $this;
    }

<<<<<<< HEAD
    public function getNum(): ?string
    {
        return $this->num;
    }

    public function setNum(string $num): self
    {
        $this->num = $num;
        return $this;
    }

=======
>>>>>>> 8df0513a3810338a9dc3a94aa0caac74c6ba07fd
    public function __toString()
    {
        return $this->name;
    }

    public function getObjective(): ?Objective
    {
        return $this->objective;
    }

    public function setObjective(?Objective $objective): self
    {
        $this->objective = $objective;

        return $this;
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

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(int $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getOwner(): Collection
    {
        return $this->owner;
    }

    public function addOwner(User $owner): self
    {
        if (!$this->owner->contains($owner)) {
            $this->owner->add($owner);
        }

        return $this;
    }

    public function removeOwner(User $owner): self
    {
        $this->owner->removeElement($owner);

        return $this;
    }

    public function isPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): self
    {
        $this->private = $private;

        return $this;
    }
}
