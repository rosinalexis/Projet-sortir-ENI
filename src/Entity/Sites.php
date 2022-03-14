<?php

namespace App\Entity;

use App\Repository\SitesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SitesRepository::class)
 */
class Sites
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nom_site;

    /**
     * @ORM\OneToMany(targetEntity=Participant::class, mappedBy="sites")
     */
    private $participants;

    /**
     * @ORM\OneToMany(targetEntity=Sorties::class, mappedBy="site")
     */
    private $sorties;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->sorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomSite(): ?string
    {
        return $this->nom_site;
    }

    public function setNomSite(string $nom_site): self
    {
        $this->nom_site = $nom_site;

        return $this;
    }

    /**
     * @return Collection|Participant[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
            $participant->setSites($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getSites() === $this) {
                $participant->setSites(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNomSite();
    }

    /**
     * @return Collection|Sorties[]
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sorties $sorty): self
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties[] = $sorty;
            $sorty->setSite($this);
        }

        return $this;
    }

    public function removeSorty(Sorties $sorty): self
    {
        if ($this->sorties->removeElement($sorty)) {
            // set the owning side to null (unless already changed)
            if ($sorty->getSite() === $this) {
                $sorty->setSite(null);
            }
        }

        return $this;
    }
}
