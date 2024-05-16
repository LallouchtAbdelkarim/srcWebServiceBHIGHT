<?php

namespace App\Repository\Creances;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
class creancesRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    public function getListesCreancesByFiltrages($data){
        $numero_creance = $data["numero_creance"];
        $date_echeance = $data["date_echeance"];
        $agent = $data["agent"];
        $ptf = $data["ptf"];

        $cin = $data["cin"];
        $raison_social = $data["raison_social"];
        $date_naissance = $data["date_naissance"];

        $tel = $data["tel"];
        $addr = $data["addr"];

        $query = 'SELECT DISTINCT c.*
        FROM creance c
        INNER JOIN type_debiteur t ON c.id = t.id_creance_id
        INNER JOIN debiteur deb ON t.id_debiteur_id = deb.id';

        if ($tel != "") {
            $query .= ' INNER JOIN telephone tel ON deb.id = tel.id_debiteur_id';
        }
        if ($addr != "") {
            $query .= ' INNER JOIN adresse ad ON deb.id = ad.id_debiteur_id';
        }

        if ($ptf != "") {
            $query .= ' AND c.id_ptf_id = "'.$ptf.'"';
        }

         if ($numero_creance != "") {
            $query .= ' WHERE c.numero_creance = "'.$numero_creance.'" ';
        }
        if ($agent != "") {
            $query .= ' AND c.id_users_id = "'.$agent.'"';
        }
        if ($cin != "") {
            $query .= ' AND deb.cin like "'.$cin.'" ';
        }
        if($raison_social != ""){
            $query .= ' AND deb.raison_social like "'.$raison_social.'" '; 
        }
        if ($date_echeance != "") {
            $dateDebut = $date_echeance . " 00:00:00";
            $dateFin = $date_echeance . " 23:59:59";
            $query .= ' AND c.date_echeance BETWEEN "'.$dateDebut.'" AND "'.$dateFin.'"';
        }
        if($date_naissance != ""){
            $dateDebut = $date_naissance. " 00:00:00";
            $dateFin = $date_naissance. " 23:59:59";
            $query .= ' AND deb.date_naissance BETWEEN "'.$dateDebut.'" AND "'.$dateFin.'"';
        }
        $stmt = $this->conn->prepare($query);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getDonneurByPtf($id_ptf){
        $sql="SELECT * FROM `donneur_ordre` where id in (select p.id_donneur_ordre_id from portefeuille p where p.id = ".$id_ptf.");";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAssociative();
        return $resulat;
    }
    public function getDebiteurByCreance($id_creance){
        $sql="SELECT * FROM `debiteur` where id in (select p.id_debiteur_id from type_debiteur p where p.id_creance_id = ".$id_creance."  and (p.id_type_id = 6 or 1=1));";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAssociative();

        return $resulat;
    }

    public function getTypeDebiteur($id_creance , $id_debiteur){
        $sql="SELECT * FROM `details_type_deb` where id in (select p.id_type_id from type_debiteur p where p.id_creance_id = ".$id_creance." and id_debiteur_id = ".$id_debiteur.")";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAssociative();
        return $resulat;
    }
    public function getOneCreance($id_creance){
        $sql="SELECT * FROM `creance` where id = ".$id_creance."";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAssociative();
        return $resulat;
    }
    
    
    public function getStatusPaiement(){
        $sql="SELECT * FROM `status_paiement`";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getStatusAccord(){
        $sql="SELECT * FROM `status_accord`";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function createAccord($data){
        $sql = "INSERT INTO `accord` (";
        $values = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $values[] = "`$key`";
        }
        $sql .= implode(', ', $values) . ')';
        
        $sql .= ' VALUES (';  

        $params = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $params[] = '"'.$value.'"';
        }
        $sql .= implode(', ', $params) . ')';
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        if($stmt){
            $sql="SELECT max(id) FROM `accord`";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $resulat = $stmt->fetchOne();
            return $resulat;
        }else{
            return null;
        }
    }
    public function createDetailsAccord($data){
        $sql = "INSERT INTO `details_accord` (";
        $values = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $values[] = "`$key`";
        }
        $sql .= implode(', ', $values) . ')';
        
        $sql .= ' VALUES (';  

        $params = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $params[] = '"'.$value.'"';
        }
        $sql .= implode(', ', $params) . ')';
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
    }
    public function getTypeDonneurOrdre(){
        $sql="SELECT * FROM `type_donneur`";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getTypeCreance(){
        $sql="SELECT * FROM `type_creance`";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getTypeCreanceByTypeDn($id){
        $sql="SELECT * FROM `type_creance` where id_type_donneur_id = ".$id."";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getTypeDetailsCreance($id){
        $sql="SELECT * FROM `details_type_creance` where id_type_creance_id = ".$id."";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }

    public function getTypeDetailsCreanceMultiple($data){
        $sql="SELECT * FROM `details_type_creance` ";
        for ($i=0; $i <count($data) ; $i++) { 
            if($i == 0){
                $sql .= "WHERE id_type_creance_id =".$data[$i]." ";
            }else{
                $sql .= "OR id_type_creance_id =".$data[$i]." ";
            }
        }
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    
}