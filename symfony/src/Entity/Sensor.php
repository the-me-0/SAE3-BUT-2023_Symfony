<?php

namespace App\Entity;

use App\Repository\SensorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SensorRepository::class)]
#[ORM\UniqueConstraint(
    name: 'sensor_tag_unique',
    columns: ['tag']
)]
#[ORM\UniqueConstraint(
    name: 'sensor_num_unique',
    columns: ['num']
)]
<<<<<<< HEAD
=======
#[ORM\MaxDepth(1)]
>>>>>>> 8df0513a3810338a9dc3a94aa0caac74c6ba07fd
class Sensor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $num = null;

    #[ORM\OneToOne(inversedBy: 'sensor')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Room $room = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $enabled = false;

    #[ORM\Column]
    private ?int $tag = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNum(): ?string
    {
        return $this->num;
    }

    public function setNum(string $num): self
    {
        $this->num = $num;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getTag(): ?int
    {
        return $this->tag;
    }

    public function setTag(int $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
}
