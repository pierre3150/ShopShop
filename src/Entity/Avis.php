<?php

declare (strict_types = 1);

namespace MyApp\Entity;

use MyApp\Entity\Produit;

class Avis
{
    private ?int $id = null;
    private string $description;
    private int $numerotaiton;
    private Produit $produit;

    public function __construct(?int $id, string $description, int $numerotation, Produit $produit)
    {
        $this->id = $id;
        $this->description = $description;
        $this->numerotation = $numerotation;
        $this->produit = $produit;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getNumerotation(): int
    {
        return $this->numerotation;
    }

    public function setNumerotation(?int $numerotation): void
    {
        $this->numerotation = $numerotation;
    }

    public function getProduit(): Produit
    {
        return $this->produit;
    }
    
    public function setProduit(Produit $produit): void
    {
        $this->produit = $produit;
    }
}
