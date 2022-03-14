<?php

namespace App\Entity;

use App\Repository\InscriptionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InscriptionsRepository::class)
 */
class Inscriptions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_inscription;

    /**
     * @ORM\ManyToOne(targetEntity=Sorties::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sorties;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=true)
     */
    private $participants;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->date_inscription;
    }

    public function setDateInscription(\DateTimeInterface $date_inscription): self
    {
        $this->date_inscription = $date_inscription;

        return $this;
    }

    public function getSorties(): ?Sorties
    {
        return $this->sorties;
    }

    public function setSorties(?Sorties $sorties): self
    {
        $this->sorties = $sorties;

        return $this;
    }

    public function getParticipants(): ?Participant
    {
        return $this->participants;
    }

    public function setParticipants(?Participant $participants): self
    {
        $this->participants = $participants;

        return $this;
    }
}
