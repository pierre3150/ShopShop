<?php

declare (strict_types = 1);

namespace MyApp\Entity;

use MyApp\Entity\Cart;
use MyApp\Entity\Produit;

class CartItem
{
    private ?int $id;
    private int $quantity;
    private string $unit;
    private Cart $cart;
    private Produit $produit;

    public function __construct(?int $id, int $quantity, string $unit, Cart $cart, Produit $produit)
    {
        $this->id = $id;
        $this->quantity = $quantity;
        $this->unit = $unit;
        $this->cart = $cart;
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

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): void
    {
        $this->unit = $unit;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }
    
    public function setCart(Cart $cart): void
    {
        $this->cart = $cart;
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
