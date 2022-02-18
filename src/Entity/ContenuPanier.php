<?php

namespace App\Entity;

use App\Repository\ContenuPanierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContenuPanierRepository::class)]
class ContenuPanier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: 'contenuPaniers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit;

    #[ORM\ManyToOne(targetEntity: Panier::class, inversedBy: 'contenuPaniers')]
    private ?Panier $panier;

    #[ORM\Column(type: 'integer')]
    private ?int $quantite;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $date_ajout;

    public function __construct()
    {
        $this->setDateAjout(new \DateTimeImmutable());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): self
    {
        $this->panier = $panier;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getDateAjout(): ?\DateTimeImmutable
    {
        return $this->date_ajout;
    }

    public function setDateAjout(\DateTimeImmutable $date_ajout): self
    {
        $this->date_ajout = $date_ajout;

        return $this;
    }
}
