<?php

declare (strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Avis;
use MyApp\Entity\Produit;
use MyApp\Entity\Type;
use PDO;

class AvisModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllAvis(): array
    {
        $sql = "SELECT a.id as idAvis, a.description, numerotation, p.id as idProduit, name, p.descriptions, price, image, home, stock, t.id as idType, label FROM avis a inner join produits p on a.produit = p.id inner join type t on p.type = t.id";
        $stmt = $this->db->query($sql);
        $avis = [];

        while ($row = $stmt->fetch()) {
            $type = new Type($row['idType'], $row['label']);
            $produit = new Produit($row['idProduit'], $row['name'], $row['descriptions'], $row['price'], $row['image'], $row['home'], $row['stock'], $type);
            $avis[] = new Avis($row['idAvis'], $row['description'], $row['numerotation'], $produit);
        }

        return $avis;
    }

    public function getAllAvisById(int $id): array
    {
        $sql = "SELECT a.id as idAvis, a.description, numerotation, p.id as idProduit, name, p.descriptions, price, image, home, stock, t.id as idType, label FROM avis a inner join produits p on a.produit = p.id inner join type t on p.type = t.id WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $avis = [];
        while ($row = $stmt->fetch()) {
            $type = new Type($row['idType'], $row['label']);
            $produit = new Produit($row['idProduit'], $row['name'], $row['descriptions'], $row['price'], $row['image'], $row['home'], $row['stock'], $type);
            $avis[] = new Avis($row['idAvis'], $row['description'], $row['numerotation'], $produit);
        }

        return $avis;
    }

    public function createAvis(Avis $avis): bool
    {
        $sql = "INSERT INTO avis (description, numerotation, produit) VALUES (:description, :numerotation, :produit)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':description', $avis->getDescription(), PDO::PARAM_STR);
        $stmt->bindValue(':numerotation', $avis->getNumerotation(), PDO::PARAM_STR);
        $stmt->bindValue(':produit', $avis->getProduit()->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

}
