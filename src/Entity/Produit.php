<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    #[Assert\NotBlank(message: 'Le libellé est obligatoire')]
    #[Assert\Length(
        min: 3,
        max: 40,
        minMessage: 'Le libellé doit comporter au moins {{ limit }} caractères',
        maxMessage: 'Le libellé ne peut pas dépasser {{ limit }} caractères',
    )]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    #[Assert\NotBlank(message: 'Le prix est obligatoire')]
    #[Assert\Range(min: 0.1, max: 999)]
    private ?string $prix = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Type("\DateTime")]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\ManyToMany(targetEntity: Recette::class, mappedBy: 'produits')]
    private Collection $recettes;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'La description est obligatoire')]
    #[Assert\Length(
        min: 15,
        max: 255,
        minMessage: 'La description doit comporter au moins {{ limit }} caractères',
        maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères',
    )]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $cru = null;

    #[ORM\Column]
    private ?bool $cuit = null;

    #[ORM\Column]
    private ?bool $bio = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\Type("\DateTime")]
    private ?\DateTimeInterface $debutDisponibilite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\Type("\DateTime")]
    #[Assert\Range(minPropertyPath: "debutDisponibilite")]
    private ?\DateTimeInterface $finDisponibilite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

        public function __construct()
    {
        $this->dateCreation = new \DateTime('now');
        $this->recettes = new ArrayCollection();
    }

        public function getCategorie(): ?Categorie
        {
            return $this->categorie;
        }

        public function setCategorie(?Categorie $categorie): static
        {
            $this->categorie = $categorie;

            return $this;
        }

        /**
         * @return Collection<int, Recette>
         */
        public function getRecettes(): Collection
        {
            return $this->recettes;
        }

        public function addRecette(Recette $recette): static
        {
            if (!$this->recettes->contains($recette)) {
                $this->recettes->add($recette);
                $recette->addProduit($this);
            }

            return $this;
        }

        public function removeRecette(Recette $recette): static
        {
            if ($this->recettes->removeElement($recette)) {
                $recette->removeProduit($this);
            }

            return $this;
        }

        public function getDescription(): ?string
        {
            return $this->description;
        }

        public function setDescription(?string $description): static
        {
            $this->description = $description;

            return $this;
        }

        public function isCru(): ?bool
        {
            return $this->cru;
        }

        public function setCru(bool $cru): static
        {
            $this->cru = $cru;

            return $this;
        }

        public function isCuit(): ?bool
        {
            return $this->cuit;
        }

        public function setCuit(bool $cuit): static
        {
            $this->cuit = $cuit;

            return $this;
        }

        public function isBio(): ?bool
        {
            return $this->bio;
        }

        public function setBio(bool $bio): static
        {
            $this->bio = $bio;

            return $this;
        }

        public function getDebutDisponibilite(): ?\DateTimeInterface
        {
            return $this->debutDisponibilite;
        }

        public function setDebutDisponibilite(?\DateTimeInterface $debutDisponibilite): static
        {
            $this->debutDisponibilite = $debutDisponibilite;

            return $this;
        }

        public function getFinDisponibilite(): ?\DateTimeInterface
        {
            return $this->finDisponibilite;
        }

        public function setFinDisponibilite(?\DateTimeInterface $finDisponibilite): static
        {
            $this->finDisponibilite = $finDisponibilite;

            return $this;
        }

}
