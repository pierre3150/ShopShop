<?php

declare (strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Cart;
use MyApp\Entity\User;
use PDO;

class CartModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllCart(): array
    {
        $sql = "SELECT c.id as idCart, creationdate, status, u.id as idUser, email, lastname, firstname, password, roles FROM Cart c inner join type u on c.user = u.id";
        $stmt = $this->db->query($sql);
        $cart = [];

        while ($row = $stmt->fetch()) {
            $user = new User($row['idUser'], $row['email'], $row['lastname'], $row['firstname'], $row['password'], $row['roles']);
            $cart[] = new Cart($row['idCart'], $row['creationdate'], $row['status'], $user);
        }

        return $cart;
    }

    public function createCart(Cart $cart): bool
    {
        $sql = "INSERT INTO Cart (creationdate, status, user) VALUES (:creationdate, :status, :user)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':creationdate', $cart->getCreationdate(), PDO::PARAM_STR);
        $stmt->bindValue(':status', $cart->getStatus(), PDO::PARAM_STR);
        $stmt->bindValue(':user', $cart->getUser()->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteCart(int $id): bool
    {
        $sql = "DELETE FROM Cart WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateCart(Cart $cart): bool
    {
        $sql = "UPDATE Cart SET creationdate = :creationdate, status = :status, user = :user WHERE Id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':creationdate', $cart->getCreationdate(), PDO::PARAM_STR);
        $stmt->bindValue(':status', $cart->getStatus(), PDO::PARAM_STR);
        $stmt->bindValue(':user', $cart->getUser()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':id', $cart->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getOneCart(int $id): ?Cart
    {
        $sql = "SELECT c.id as idCart, creationdate, status, u.id as idUser, email, lastname, firstname, password, roles FROM Cart c inner join User u on c.user = u.id where c.Id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        $user = new User($row['idUser'], $row['email'], $row['lastname'], $row['firstname'], $row['password'], json_decode($row['roles']));
        return new Cart($row['idCart'], $row['creationdate'], $row['status'], $user);
    }

    public function getCartById(int $id): array
    {
        $sql = "SELECT c.id as idCart, creationdate, status, u.id as idUser, email, lastname, firstname, password, roles FROM Cart c inner join User u on c.user = u.id where u.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $cart = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User($row['idUser'], $row['email'], $row['lastname'], $row['firstname'], $row['password'], json_decode($row['roles']));
            $cart[] = new Cart($row['idCart'], $row['creationdate'], $row['status'], $user);
        }

        return $cart;
    }

    public function getCartByIdId(int $id): ?Cart
    {
        $sql = "SELECT c.id as idCart, creationdate, status, u.id as idUser, email, lastname, firstname, password, roles FROM Cart c inner join User u on c.user = u.id where c.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        $user = new User($row['idUser'], $row['email'], $row['lastname'], $row['firstname'], $row['password'], json_decode($row['roles']));
        return new Cart($row['idCart'], $row['creationdate'], $row['status'], $user);
    }

}
