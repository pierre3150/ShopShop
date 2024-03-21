<?php

declare (strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Produit;
use MyApp\Entity\Type;
use PDO;

class ProduitModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllProduits(): array
    {
        $sql = "SELECT p.id as idProduit, name, descriptions, price, home, stock t.id as idType, label FROM produits p inner join type t on p.type = t.id order by name";
        $stmt = $this->db->query($sql);
        $produit = [];

        while ($row = $stmt->fetch()) {
            $type = new Type($row['idType'], $row['label']);
            $produit[] = new Produit($row['idProduit'], $row['name'], $row['descriptions'], $row['price'], $row['home'], $row['stock'], $type);
        }

        return $produit;
    }

    public function createProduit(Produit $produit): bool
    {
        $sql = "INSERT INTO produits (name,descriptions,price,image,home,type, stock) VALUES (:name,:descriptions,:price,:image,:home,:type,:stock)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $produit->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':descriptions', $produit->getDescriptions(), PDO::PARAM_STR);
        $stmt->bindValue(':price', $produit->getPrice(), PDO::PARAM_STR);
        $stmt->bindValue(':image', $produit->getImage(), PDO::PARAM_STR);
        $stmt->bindValue(':home', $produit->getHome(), PDO::PARAM_INT);
        $stmt->bindValue(':stock', $produit->getStock(), PDO::PARAM_INT);
        $stmt->bindValue(':type', $produit->getType()->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteProduit(int $id): bool
    {
        $sql = "DELETE FROM produits WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateProduit(Produit $produit): bool
    {
        $sql = "UPDATE produit SET name = :name, descriptions = :descriptions, price = :price, type = :type WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $produit->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':descriptions', $produit->getDescriptions(), PDO::PARAM_STR);
        $stmt->bindValue(':price', $produit->getPrice(), PDO::PARAM_STR);
        $stmt->bindValue(':image', $produit->getImage(), PDO::PARAM_STR);
        $stmt->bindValue(':home', $produit->getHome(), PDO::PARAM_INT);
        $stmt->bindValue(':stock', $produit->getStock(), PDO::PARAM_INT);
        $stmt->bindValue(':idType', $product->getType()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':id', $produit->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getOneProduit(int $id): ?Produit
    {
        $sql = "SELECT p.id as idProduit, name, descriptions, price, image, home, stock, t.id as idType, label FROM produits p inner join type t on p.type = t.id where p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        $type = new Type($row['idType'], $row['label']);
        return new Produit($row['idProduit'], $row['name'], $row['descriptions'], $row['price'], $row['image'], $row['home'], $row['stock'], $type);
    }

    public function getAllProduitByType(Type $type): array
    {
        $sql = "SELECT p.id as idProduit, name, descriptions, price, image, home, stock, t.id as idType, label FROM produits p inner join type t on p.type = t.id where type = :type and stock > 0 order by name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':type', $type->getId(), PDO::PARAM_INT);
        $stmt->execute();
        $produits = [];

        while ($row = $stmt->fetch()) {
            $type = new Type($row['idType'], $row['label']);
            $produits[] = new Produit($row['idProduit'], $row['name'], $row['descriptions'], $row['price'], $row['image'], $row['home'], $row['stock'], $type);
        }

        return $produits;
    }

    public function getAllProduitByHome(): array
    {
        $sql = "SELECT p.id as idProduit, name, descriptions, price, image, home, stock, t.id as idType, label FROM produits p inner join type t on p.type = t.id where home = 1 and stock > 0 order by name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $produits = [];

        while ($row = $stmt->fetch()) {
            $type = new Type($row['idType'], $row['label']);
            $produits[] = new Produit($row['idProduit'], $row['name'], $row['descriptions'], $row['price'], $row['image'], $row['home'], $row['stock'], $type);
        }

        return $produits;
    }

    public function getAllProduitByHomeadmin(): array
    {
        $sql = "SELECT p.id as idProduit, name, descriptions, price, image, home, stock, t.id as idType, label FROM produits p inner join type t on p.type = t.id where home = 1 and stock <= 3 order by name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $produits = [];

        while ($row = $stmt->fetch()) {
            $type = new Type($row['idType'], $row['label']);
            $produits[] = new Produit($row['idProduit'], $row['name'], $row['descriptions'], $row['price'], $row['image'], $row['home'], $row['stock'], $type);
        }

        return $produits;
    }

    public function getProduitById(int $id): ?Produit
    {
        $sql = "SELECT p.id as idProduit, name, descriptions, price, image, home, stock, t.id as idType, label FROM produits p inner join type t on p.type = t.id where p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        if ($row) {
            $type = new Type($row['idType'], $row['label']);
            return new Produit($row['idProduit'], $row['name'], $row['descriptions'], $row['price'], $row['image'], $row['home'], $row['stock'], $type);
        } else {
            return null;
        }
    }

    public function getPriceById($id)
    {
        $sql = "SELECT price FROM produits Produit INNER JOIN type Type ON Produit.type = Type.id WHERE Produit.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        if ($row) {
            return $row['price']; // Retourner directement le prix sans créer un objet Produit
        } else {
            return null;
        }
    }

}
