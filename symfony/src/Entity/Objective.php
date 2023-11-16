<?php

namespace App\Entity;

use App\Repository\ObjectiveRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use DateTimeInterface;
use DateTimeZone;

#[ORM\Entity(repositoryClass: ObjectiveRepository::class)]
#[ORM\MaxDepth(1)]
class Objective
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    
    #[ORM\Column]
    private ?int $temperature = 20;

    
    #[ORM\Column]
    private ?int $humidity = 40;

    
    #[ORM\Column]
    private ?int $eCO2 = 400;

    #[ORM\Column]
    private ?int $gapTemperature = 2;

    #[ORM\Column]
    private ?int $gapHumidity = 10;

    #[ORM\Column]
    private ?int $gapECO2 = 50;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $start_date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $end_date = null;

    #[ORM\Column]
    private ?bool $personal = false;


    public function __construct($temp = 25, $hum = 35, $eco2 = 400)
    {
        $now = new \DateTime("now", new DateTimeZone('Europe/Paris'));
        $this->setStartDate($now);
        $this->temperature = $temp;
        $this->humidity = $hum;
        $this->eCO2 = $eco2;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemperature(): ?int
    {
        return $this->temperature;
    }

    public function setTemperature(int $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getHumidity(): ?int
    {
        return $this->humidity;
    }

    public function setHumidity(int $humidity): self
    {
        $this->humidity = $humidity;

        return $this;
    }

    public function getECO2(): ?int
    {
        return $this->eCO2;
    }

    public function setECO2(int $eCO2): self
    {
        $this->eCO2 = $eCO2;

        return $this;
    }

    public function getGapTemperature(): ?int
    {
        return $this->gapTemperature;
    }

    public function setGapTemperature(int $gapTemperature): self
    {
        $this->gapTemperature = $gapTemperature;

        return $this;
    }

    public function getGapHumidity(): ?int
    {
        return $this->gapHumidity;
    }

    public function setGapHumidity(int $gapHumidity): self
    {
        $this->gapHumidity = $gapHumidity;

        return $this;
    }

    public function getGapECO2(): ?int
    {
        return $this->gapECO2;
    }

    public function setGapECO2(int $gapECO2): self
    {
        $this->gapECO2 = $gapECO2;

        return $this;
    }

    public function toArray()
    {
        return [
            'temperature' => $this->getTemperature(),
            'humidity' => $this->getHumidity(),
            'eCO2' => $this->getECO2(),
            'gapTemperature' => $this->getGapTemperature(),
            'gapHumidity' => $this->getGapHumidity(),
            'gapECO2' => $this->getGapECO2(),
        ];
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(?\DateTimeInterface $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(?\DateTimeInterface $end_date): self
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function isPersonal(): ?bool
    {
        return $this->personal;
    }

    public function setPersonal(bool $personal): self
    {
        $this->personal = $personal;

        return $this;
    }

    public function getPersonal(): ?bool
    {
        return $this->personal;
    }

}