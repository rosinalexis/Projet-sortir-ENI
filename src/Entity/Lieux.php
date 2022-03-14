<?php

namespace App\Entity;

use App\Repository\LieuxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LieuxRepository::class)
 */
class Lieux
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("lieu:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Groups("lieu:read")
     */
    private $nom_lieu;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Groups("lieu:read")
     */
    private $rue;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("lieu:read")
     */
    private $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("lieu:read")
     */
    private $longitude;

    /**
     * @ORM\ManyToOne(targetEntity=Villes::class, inversedBy="lieux")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("lieu:read")
     */
    private $villes;

    /**
     * @ORM\OneToMany(targetEntity=Sorties::class, mappedBy="lieux")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sorties;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomLieu(): ?string
    {
        return $this->nom_lieu;
    }

    public function setNomLieu(string $nom_lieu): self
    {
        $this->nom_lieu = $nom_lieu;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(?string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getVilles(): ?Villes
    {
        return $this->villes;
    }

    public function setVilles(?Villes $villes): self
    {
        $this->villes = $villes;

        return $this;
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
            $sorty->setLieux($this);
        }

        return $this;
    }

    public function removeSorty(Sorties $sorty): self
    {
        if ($this->sorties->removeElement($sorty)) {
            // set the owning side to null (unless already changed)
            if ($sorty->getLieux() === $this) {
                $sorty->setLieux(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNomLieu();
    }
}
