<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Meuble::class, orphanRemoval: true)]
    private Collection $meubles;

    public function __construct()
    {
        $this->meubles = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->nom ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getMeubles(): Collection
    {
        return $this->meubles;
    }

    public function addMeuble(Meuble $meuble): static
    {
        if (!$this->meubles->contains($meuble)) {
            $this->meubles->add($meuble);
            $meuble->setCategorie($this);
        }

        return $this;
    }

    public function removeMeuble(Meuble $meuble): static
    {
        if ($this->meubles->removeElement($meuble)) {
            if ($meuble->getCategorie() === $this) {
                $meuble->setCategorie(null);
            }
        }

        return $this;
    }
}