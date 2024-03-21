<?php

declare (strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Cart;
use MyApp\Entity\CartItem;
use MyApp\Entity\Produit;
use MyApp\Entity\Type;
use PDO;

class CartItemModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllCartItem(): array
    {
        $sql = "SELECT ci.id as idCartItem, quantity, unit, c.id as idCart, creationdate, status, user FROM CartItem ci inner join type c on ci.cart = c.id";
        $stmt = $this->db->query($sql);
        $cartitem = [];

        while ($row = $stmt->fetch()) {
            $user = new Cart($row['idUser'], $row['creationdate'], $row['status'], $user);
            $cartitem[] = new CartItem($row['idCart'], $row['quantity'], $row['unit'], $cart);
        }

        return $cartitem;
    }

    public function createCartItem(CartItem $cartitem): bool
    {
        $sql = "INSERT INTO CartItem (quantity,unit,cart,produit) VALUES (:quantity,:unit,:cart,:produit)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':quantity', $cartitem->getQuantity(), PDO::PARAM_STR);
        $stmt->bindValue(':unit', $cartitem->getUnit(), PDO::PARAM_STR);
        $stmt->bindValue(':cart', $cartitem->getCart()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':produit', $cartitem->getProduit()->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteCartItem(int $id): bool
    {
        $sql = "DELETE FROM CartItem WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateCartItem(CartItem $cartitem): bool
    {
        $sql = "UPDATE CartItem SET quantity = :quantity, unit = :unit, cart = :cart WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':quantity', $cartitem->getQuantity(), PDO::PARAM_STR);
        $stmt->bindValue(':unit', $cartitem->getUnit(), PDO::PARAM_STR);
        $stmt->bindValue(':idCart', $cartitem->getCart()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':id', $cartitem->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getOneCart(int $id): ?CartItem
    {
        $sql = "SELECT * from CartItem where id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new Cart($row['id'], $row['quantity'], $row['unit'], $cart);
    }

    public function getCartItemById(int $id, Cart $cart): array
    {
        $sql = "SELECT ci.id as idCartItem, quantity, unit, p.id as idProduit, name, descriptions, price, image, home, stock, t.id as idType, label FROM CartItem ci inner join produits p on ci.produit = p.id inner join type t ON p.type = t.id where ci.cart = :id;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $cartItems = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $type = new Type($row['idType'], $row['label']);
            $produit = new Produit($row['idProduit'], $row['name'], $row['descriptions'], $row['price'], $row['image'], $row['home'], $row['stock'], $type);
            $cartItems[] = new CartItem($row['idCartItem'], $row['quantity'], $row['unit'], $cart, $produit);
        }

        return $cartItems;
    }

}
