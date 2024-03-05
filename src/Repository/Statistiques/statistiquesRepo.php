<?php

namespace App\Repository\Statistiques;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class statistiquesRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    public function getTotalCreance()
    {
        $sql="SELECT count(c.id) FROM `creance` c ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchOne();
        return $statut;
    }
    public function getTotalCreanceNonSuccess()
    {
        // $sql="SELECT count(c.id) FROM `creance` c  where c.total";
        // $stmt = $this->conn->prepare($sql);
        // $stmt = $stmt->executeQuery();
        // $statut = $stmt->fetchOne();
        // return $statut;
    }
    
    public function getUsers()
    {
        $sql="SELECT * FROM `utilisateurs`  ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchAll();
        return $statut;
    }
    public function saveStatistiques($param)
    {
        // $sql="INSERT INTO `revenu`( id_type_revenu_id, id_debiteur_id , revenu , adresse) VALUES (:id_type_revenu_id,:id_debiteur_id,:revenu , :adresse);";
        $sql = "INSERT INTO `statistiques` (";
        $values = [];
        foreach ($param as $key => $value) {
            // Enclose column names within backticks if needed
            $values[] = "`$key`";
        }
        $sql .= implode(', ', $values) . ')';
        
        $sql .= ' VALUES (';  

        $params = [];
        foreach ($param as $key => $value) {
            // Enclose column names within backticks if needed
            $params[] = '"'.$value.'"';
        }
        $sql .= implode(', ', $params) . ')';
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
    }
}