<?php

namespace App\Entity;

use App\Repository\ArchiveRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArchiveRepository::class)
 */
class Archive
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomLieu;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $etatSortie;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomSite;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nomSortie;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateDebutSortie;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dureeSortie;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateClotureInscription;

    /**
     * @ORM\Column(type="integer")
     */
    private $nnInscriptionMaxSortie;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptionSortie;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $urlPhotoSortie;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nomOrganisateur;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $emailOrganisateur;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $participantsInscrit = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomOrganisateur(): ?string
    {
        return $this->nomOrganisateur;
    }

    public function setNomOrganisateur(string $nomOrganisateur): self
    {
        $this->nomOrganisateur = $nomOrganisateur;

        return $this;
    }

    public function getNomLieu(): ?string
    {
        return $this->nomLieu;
    }

    public function setNomLieu(string $nomLieu): self
    {
        $this->nomLieu = $nomLieu;

        return $this;
    }

    public function getEtatSortie(): ?string
    {
        return $this->etatSortie;
    }

    public function setEtatSortie(string $etatSortie): self
    {
        $this->etatSortie = $etatSortie;

        return $this;
    }

    public function getNomSite(): ?string
    {
        return $this->nomSite;
    }

    public function setNomSite(string $nomSite): self
    {
        $this->nomSite = $nomSite;

        return $this;
    }

    public function getNomSortie(): ?string
    {
        return $this->nomSortie;
    }

    public function setNomSortie(string $nomSortie): self
    {
        $this->nomSortie = $nomSortie;

        return $this;
    }

    public function getDateDebutSortie(): ?\DateTimeInterface
    {
        return $this->dateDebutSortie;
    }

    public function setDateDebutSortie(\DateTimeInterface $dateDebutSortie): self
    {
        $this->dateDebutSortie = $dateDebutSortie;

        return $this;
    }

    public function getDureeSortie(): ?int
    {
        return $this->dureeSortie;
    }

    public function setDureeSortie(int $dureeSortie): self
    {
        $this->dureeSortie = $dureeSortie;

        return $this;
    }

    public function getDateClotureInscription(): ?\DateTimeInterface
    {
        return $this->dateClotureInscription;
    }

    public function setDateClotureInscription(\DateTimeInterface $dateClotureInscription): self
    {
        $this->dateClotureInscription = $dateClotureInscription;

        return $this;
    }

    public function getNnInscriptionMaxSortie(): ?int
    {
        return $this->nnInscriptionMaxSortie;
    }

    public function setNnInscriptionMaxSortie(int $nnInscriptionMaxSortie): self
    {
        $this->nnInscriptionMaxSortie = $nnInscriptionMaxSortie;

        return $this;
    }

    public function getDescriptionSortie(): ?string
    {
        return $this->descriptionSortie;
    }

    public function setDescriptionSortie(?string $descriptionSortie): self
    {
        $this->descriptionSortie = $descriptionSortie;

        return $this;
    }

    public function getUrlPhotoSortie(): ?string
    {
        return $this->urlPhotoSortie;
    }

    public function setUrlPhotoSortie(?string $urlPhotoSortie): self
    {
        $this->urlPhotoSortie = $urlPhotoSortie;

        return $this;
    }

    public function getEmailOrganisateur(): ?string
    {
        return $this->emailOrganisateur;
    }

    public function setEmailOrganisateur(string $emailOrganisateur): self
    {
        $this->emailOrganisateur = $emailOrganisateur;

        return $this;
    }

    public function getParticipantsInscrit(): ?array
    {
        return $this->participantsInscrit;
    }

    public function setParticipantsInscrit(array $participantsInscrit): self
    {
        $this->participantsInscrit = $participantsInscrit;

        return $this;
    }
}
