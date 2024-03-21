<?php

declare (strict_types = 1);

namespace MyApp\Entity;

use MyApp\Entity\Type;

class Produit
{
    private ?int $id;
    private string $name;
    private string $descriptions;
    private float $price;
    private Type $type;
    private string $image;
    private int $home;
    private int $stock;

    public function __construct(?int $id, string $name, string $descriptions, float $price, string $image, int $home, int $stock, Type $type )
    {
        $this->id = $id;
        $this->name = $name;
        $this->descriptions = $descriptions;
        $this->price = $price;
        $this->type = $type;
        $this->image = $image;
        $this->home = $home;
        $this->stock = $stock;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescriptions(): ?string
    {
        return $this->descriptions;
    }

    public function setDescriptions(?string $descriptions): void
    {
        $this->descriptions = $descriptions;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function getType(): Type
    {
        return $this->type;
    }
    
    public function setType(Type $type): void
    {
        $this->type = $type;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getHome(): int
    {
        return $this->home;
    }

    public function setHome(?int $home): void
    {
        $this->home = $home;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): void
    {
        $this->stock = $stock;
    }
}
