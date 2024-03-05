<?php

namespace App\Repository\Historiques;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
class histoRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    
    public function getHistoTelephone($date_debut , $date_fin){
        $sql="SELECT * FROM `histo_telephone` where date_creation between '".$date_debut."' and '".$date_fin."'";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getListeTel(){
        $sql="SELECT * FROM `telephone`";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getListeAdresse(){
        $sql="SELECT * FROM `adresse`";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getHistoAdresse($date_debut , $date_fin){
        $sql="SELECT * FROM `histo_adresse` where date_creation between '".$date_debut."' and '".$date_fin."'";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getListeEmploi(){
        $sql="SELECT * FROM `emploi`";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getHistoEmploi($date_debut , $date_fin){
        $sql="SELECT * FROM `histo_emploi` where date_creation between '".$date_debut."' and '".$date_fin."'";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getListeEmployeur(){
        $sql="SELECT * FROM `emploi`";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getHistoEmployeur($date_debut , $date_fin){
        $sql="SELECT * FROM `histo_emploi` where date_creation between '".$date_debut."' and '".$date_fin."'";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
}