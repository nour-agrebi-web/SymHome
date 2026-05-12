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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    /**
     * @var Collection<int, Meuble>
     */
    #[ORM\OneToMany(targetEntity: Meuble::class, mappedBy: 'categorie')]
    private Collection $meubles;

    public function __construct()
    {
        $this->meubles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Meuble>
     */
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
            // set the owning side to null (unless already changed)
            if ($meuble->getCategorie() === $this) {
                $meuble->setCategorie(null);
            }
        }

        return $this;
    }
}
