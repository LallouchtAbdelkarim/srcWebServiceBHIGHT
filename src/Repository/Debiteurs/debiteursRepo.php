<?php

namespace App\Repository\Debiteurs;

use App\Entity\CorresColu;
use App\Entity\ImportType;
use App\Entity\ModelImport;
use App\Entity\Telephone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\RelationDebiteur;
class debiteursRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    public function getListesDebiteurs(){
        $sql="select * from  debiteur LIMIT 0,7;";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getClassification(){
        $sql="select * from  classification";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getProfession($id){
        $sql="select * from  profession where id_classification_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getStatusEmploi(){
        $sql="select * from  status_emploi";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getStatusEmployeur(){
        $sql="select * from  status_employeur";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getOneEmployeur($id_empolyeur , $id_deb){
        $sql="select * from  employeur where id = :id_empolyeur and id_debiteur_id = :id_deb";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id_empolyeur', $id_empolyeur);
        $stmt->bindParam('id_deb', $id_deb);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAssociative();
        if($resulat){
            return $resulat;
        }else{
            return null;
        }
    }
    public function getListesCreancesByDebiteurs($id){
        $id_debiteur = $id;

        $query = 'SELECT DISTINCT c.*
        FROM creance c
        INNER JOIN type_debiteur t ON c.id = t.id_creance_id
        INNER JOIN debiteur deb ON t.id_debiteur_id = deb.id';

        if ($id_debiteur != "") {
            $query .= ' AND deb.id like "'.$id_debiteur.'" ';
        }
        $stmt = $this->conn->prepare($query);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    
    public function getListesDebiteursByFiltrages($data){
        $date_debut_echeance = $data["date_debut_echeance"];
        $date_fin_echeance = $data["date_fin_echeance"];
        $num_creance = $data["num_creance"];

        $cin = $data["cin"];
        $raison_social = $data["raison_social"];
        $date_naissance = $data["date_naissance"];

        $tel = $data["tel"];
        $addr = $data["addr"];

        $query = 'SELECT DISTINCT d.* FROM debiteur d
        JOIN type_debiteur tp ON d.id = tp.id_debiteur_id
        JOIN creance c ON tp.id_creance_id = c.id ';

        if ($tel != "") {
            $query .= ' INNER JOIN telephone tel ON d.id = tel.id_debiteur_id';
        }
        if ($addr != "") {
            $query .= ' INNER JOIN adresse ad ON d.id = ad.id_debiteur_id';
        }

        //Creance
        if($num_creance != ""){
            $query .= ' AND c.numero_creance like "'.$num_creance.'" ';
        }
        if ($date_debut_echeance != "" && $date_fin_echeance != "" ) {
            $dateDebut = $date_debut_echeance . " 00:00:00";
            $dateFin = $date_fin_echeance . " 23:59:59";
            $query .= ' AND c.date_echeance BETWEEN "'.$dateDebut.'" AND "'.$dateFin.'"';
        }
        //Débiteurs
        if ($cin != "") {
            $query .= ' AND d.cin like "'.$cin.'" ';
        }
        if($raison_social != ""){
            $query .= ' AND d.raison_social like "'.$raison_social.'" ';
        }
        if($date_naissance != ""){
            $dateDebut = $date_naissance. " 00:00:00";
            $dateFin = $date_naissance. " 23:59:59";
            $query .= ' AND d.date_naissance BETWEEN "'.$dateDebut.'" AND "'.$dateFin.'"';
        }

        if($tel != ""){
            $query .=' AND tel.numero = "'.$tel.'" AND tel.active = 1';
        }
        if($addr != ""){
            $query .= ' AND ad.adresse_complet = "'.$addr.'" AND ad.verifier = 1'; 
        }
                
        $stmt = $this->conn->prepare($query);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function checkDebiteur($id){
        $sql="select * from  debiteur where id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAssociative();
        if($resulat){
            return $resulat;
        }else{
            return null;
        }
    }


    public function getTypeDebiteur($id_creance , $id_debiteur){
        $sql="SELECT * FROM `details_type_deb` where id in (select p.id_type_id from type_debiteur p where p.id_creance_id = ".$id_creance." and id_debiteur_id = ".$id_debiteur.")";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAssociative();
        return $resulat;
    }
    public function createRevenu($data){
        // $sql="INSERT INTO `revenu`( id_type_revenu_id, id_debiteur_id , revenu , adresse) VALUES (:id_type_revenu_id,:id_debiteur_id,:revenu , :adresse);";
        $sql = "INSERT INTO `revenu` (";
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
    
    public function getRelationByDebt($id){
        $sql="SELECT * FROM personne where id in (select r.id_personne_id from relation_debiteur r WHERE r.id_debiteur_id = :id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();

        $personnes = array();
        for ($i=0; $i < count($resulat) ; $i++) { 
            $personnes[$i] = $resulat[$i];
            $sql="SELECT * FROM relation where id in (select r.id_relation_id from relation_debiteur r WHERE r.id_debiteur_id = :id and id_personne_id = :id_personne)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $id);
            $stmt->bindParam('id_personne', $resulat[$i]["id"]);
            $stmt = $stmt->executeQuery();
            $relation = $stmt->fetchAssociative();
            $personnes[$i]["relation"] = $relation;
        }
        if($personnes){
            return $personnes;
        }else{
            return [];
        }
    }
    
        // public function getContacts($id){
    //     $sql="SELECT * FROM telephone where id in (select r.id_personne_id from relation_debiteur r WHERE r.id_debiteur_id = :id)";
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->bindParam('id', $id);
    //     $stmt = $stmt->executeQuery();
    //     $resulat = $stmt->fetchAll();
    //     if($resulat){
    //         return $resulat;
    //     }else{
    //         return null;
    //     }
    // }
    public function getContacts($id){
        $contacts = array();
        $active =  true;
        $sql="SELECT * FROM telephone where active = :active and id_debiteur_id = :id ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('active', $active);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $telephone = $stmt->fetchAssociative();
        $contacts["telephone"] = $telephone;
        if($telephone){
            $sql="SELECT * FROM type_tel where id = :id ";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $contacts["telephone"]["id_type_tel_id"]);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $contacts["telephone"]["type"] = $type;
        }

        $sql="SELECT * FROM adresse where verifier = :active and id_debiteur_id = :id ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('active', $active);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $adresse = $stmt->fetchAssociative();
        $contacts["adresse"] = $adresse;
        if($adresse){
            $sql="SELECT * FROM type_adresse where id = :id ";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $contacts["adresse"]["id_type_adresse_id"]);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $contacts["adresse"]["type"] = $type;
        }

        $sql="SELECT * FROM email where active = :active and id_debiteur_id = :id ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('active', $active);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $email = $stmt->fetchAssociative();
        $contacts["email"] = $email;
        if($email){
            $sql="SELECT * FROM type_email where id = :id ";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $contacts["email"]["id_type_email_id"]);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $contacts["email"]["type"] = $type;
        }
        return $contacts;
    }
    public function getAllDetailsDeb($id){
        $contacts = array();
        $active = true;

        // Fetch all telephone information
        $sql = "SELECT * FROM telephone WHERE  id_debiteur_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $telephones = $stmt->fetchAll();
        $contacts["telephone"] = array();

        // Fetch type information for each telephone
        foreach ($telephones as $i => $telephone) {
            $sql = "SELECT * FROM type_tel WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $telephone["id_type_tel_id"]);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $telephone["type"] = $type;


            $sql = "SELECT * FROM status_telephone WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $telephone["id_status_id"]);
            $stmt = $stmt->executeQuery();
            $status = $stmt->fetchAssociative();
            $telephone["status"] = $status;
            $contacts["telephone"][$i] = $telephone;

            $sql = "SELECT * FROM type_source WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $telephone["id_type_source_id"]);
            $stmt = $stmt->executeQuery();
            $source = $stmt->fetchAssociative();
            $telephone["source"] = $source;
            $contacts["telephone"][$i] = $telephone;
        }

        // Fetch all address information
        $sql = "SELECT * FROM adresse WHERE  id_debiteur_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $addresses = $stmt->fetchAll();
        $contacts["adresse"] = array();

        // Fetch type information for each address
        foreach ($addresses as $i => $address) {
            $sql = "SELECT * FROM type_adresse WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $address["id_type_adresse_id"]);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $address["type"] = $type;
            $contacts["adresse"][$i] = $address;

            $sql = "SELECT * FROM type_source WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $address["id_type_source_id"]);
            $stmt = $stmt->executeQuery();
            $source = $stmt->fetchAssociative();
            $address["source"] = $source;
            $contacts["adresse"][$i] = $address;

            $sql = "SELECT * FROM status_adresse WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $address["id_status_id"]);
            $stmt = $stmt->executeQuery();
            $status = $stmt->fetchAssociative();
            $address["status"] = $status;
            $contacts["adresse"][$i] = $address;
        }

        // Fetch all email information
        $sql = "SELECT * FROM email WHERE  id_debiteur_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $emails = $stmt->fetchAll();
        $contacts["email"] = array();

        // Fetch type information for each email
        foreach ($emails as $i => $email) {
            $sql = "SELECT * FROM type_email WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $email["id_type_email_id"]);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $email["type"] = $type;
            $contacts["email"][$i] = $email;

            $sql = "SELECT * FROM type_source WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $email["id_type_source_id"]);
            $stmt = $stmt->executeQuery();
            $source = $stmt->fetchAssociative();
            $email["source"] = $source;
            $contacts["email"][$i] = $email;

            $sql = "SELECT * FROM status_email WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $email["id_status_email_id"]);
            $stmt = $stmt->executeQuery();
            $status = $stmt->fetchAssociative();
            $email["status"] = $status;
            $contacts["email"][$i] = $email;
        }

          // Fetch all emploi information
          $sql = "SELECT * FROM emploi WHERE  id_debiteur_id = :id";
          $stmt = $this->conn->prepare($sql);
          $stmt->bindParam('id', $id);
          $stmt = $stmt->executeQuery();
          $emploi = $stmt->fetchAll();
          $contacts["emploi"] = array();
  
          // Fetch type information for each telephone
          foreach ($emploi as $i => $emp) {
              $sql = "SELECT * FROM status_emploi WHERE id = :id";
              $stmt = $this->conn->prepare($sql);
              $stmt->bindParam('id', $emp["id_status_id"]);
              $stmt = $stmt->executeQuery();
              $status = $stmt->fetchAssociative();
              $emp["status"] = $status;
              $contacts["emploi"][$i] = $emp;
          }

           // Fetch all emploi information
           $sql = "SELECT * FROM employeur WHERE  id_debiteur_id = :id";
           $stmt = $this->conn->prepare($sql);
           $stmt->bindParam('id', $id);
           $stmt = $stmt->executeQuery();
           $employeur = $stmt->fetchAll();
           $contacts["employeur"] = array();
           
           // Fetch type information for each telephone
           foreach ($employeur as $i => $empl) {
               $sql = "SELECT * FROM status_employeur WHERE id = :id";
               $stmt = $this->conn->prepare($sql);
               $stmt->bindParam('id', $empl["id_status_id"]);
               $stmt = $stmt->executeQuery();
               $status = $stmt->fetchAssociative();
               $empl["status"] = $status;
               $contacts["employeur"][$i] = $empl;
           }

        return $contacts;

    }

    public function updateRevenu($data , $id){
        // $primaryKey = "id";
        $sql = "UPDATE `revenu` SET ";
        $setClauses = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
                $setClauses[] = "`$key` = :$key";
        }
        $sql .= implode(', ', $setClauses);
        // You need to specify which row to update, typically using the primary key
        $sql .= " WHERE `id` = :id";
        $stmt = $this->conn->prepare($sql);
        // Bind parameters for the SET clauses
        foreach ($data as $key => $value) {
            $stmt->bindParam($key, $value);  // Use $key directly as the parameter name
        }
        // Bind the primary key parameter
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }
    public function deleteRevenu( $id){
        // $primaryKey = "id";
        $sql = "DELETE from `revenu` WHERE `id` = :id ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }
    public function getListeParamsByDeb($type , $id){
        switch ($type) {
            case 'revenu':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\Revenu r where r.id_debiteur = :id')
                    ->setParameters([
                        'id' => $id
                    ]);
                $resultList = $query->getResult();
                return $resultList;
                break;
            case 'employeur':
                $sql="SELECT * FROM employeur where  id_debiteur_id = :id ";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam('id', $id);
                $stmt = $stmt->executeQuery();
                return $resultList = $stmt->fetchAll();
                break;
            case 'emploi':
                $sql="SELECT * FROM emploi where  id_debiteur_id = :id ";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam('id', $id);
                $stmt = $stmt->executeQuery();
                return $resultList = $stmt->fetchAll();
                break;
            case 'historique_emploi':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\HistoriqueEmploi r where r.id_debiteur = :id')
                    ->setParameters([
                        'id' => $id
                    ]);
                $resultList = $query->getResult();
                return $resultList;
                break;
            case 'employeur':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\Employeur r where r.id_debiteur = :id')
                    ->setParameters([
                        'id' => $id
                    ]);
                $resultList = $query->getResult();
                return $resultList;
                break;
            case 'CompteBancaire':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\CompteBancaire r where r.id_debiteur = :id')
                    ->setParameters([
                        'id' => $id
                    ]);
                $resultList = $query->getResult();
                return $resultList;
                break;
            case 'foncier':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\Foncier r where r.id_debiteur = :id')
                    ->setParameters([
                        'id' => $id
                    ]);
                $resultList = $query->getResult();
                return $resultList;
                break;
    
            case 'adresse':
                $sql="SELECT * FROM adresse where  id_debiteur_id = :id ";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam('id', $id);
                $stmt = $stmt->executeQuery();
                return $resultList = $stmt->fetchAll();
                break;

            case 'charge':
                // Check if $id matches 'charge' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\Charge r where r.id_debiteur = :id')
                    ->setParameters([
                        'id' => $id
                    ]);
                $resultList = $query->getResult();
                // if ($resultList) {
                // }
                return $resultList;
                break;

            case 'email':
                // Check if $id matches 'email' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\Charge r where r.id_debiteur = :id')
                    ->setParameters([
                        'id' => $id
                    ]);
                $resultList = $query->getResult();
                return $resultList;
                break;
            case 'relation':
                // Check if $id matches 'email' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\Relation r ')
                    ->setParameters([
                        'id' => $id
                    ]);
                $resultList = $query->getResult();
                return $resultList;
                break;

            default:
                // If $type doesn't match any expected types, return false
                return [];
        }

        // If none of the cases matched, return false
        return null;
    }
    public function createCharge($data){
        // $sql="INSERT INTO `revenu`( id_type_revenu_id, id_debiteur_id , revenu , adresse) VALUES (:id_type_revenu_id,:id_debiteur_id,:revenu , :adresse);";
        $sql = "INSERT INTO `charge` (";
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
    public function updateCharge($data , $id){
        // $primaryKey = "id";
        
        $sql = "UPDATE `charge` SET ";
        $setClauses = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
                $setClauses[] = "`$key` = :$key";
        }
        $sql .= implode(', ', $setClauses);
        // You need to specify which row to update, typically using the primary key
        $sql .= " WHERE `id` = :id";
        $stmt = $this->conn->prepare($sql);
        // Bind parameters for the SET clauses
        foreach ($data as $key => $value) {
            $stmt->bindParam($key, $value);  // Use $key directly as the parameter name
        }
        // Bind the primary key parameter
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }
    public function deleteCharge( $id){
        // $primaryKey = "id";
        $sql = "DELETE from `charge` WHERE `id` = :id ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }
    public function createFoncier($data){
        // $sql="INSERT INTO `revenu`( id_type_revenu_id, id_debiteur_id , revenu , adresse) VALUES (:id_type_revenu_id,:id_debiteur_id,:revenu , :adresse);";
        $sql = "INSERT INTO `foncier` (";
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
    public function updateFoncier($data , $id){
        // $primaryKey = "id";
        
        $sql = "UPDATE `foncier` SET ";
        $setClauses = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
                $setClauses[] = "`$key` = :$key";
        }
        $sql .= implode(', ', $setClauses);
        // You need to specify which row to update, typically using the primary key
        $sql .= " WHERE `id` = :id";
        $stmt = $this->conn->prepare($sql);
        // Bind parameters for the SET clauses
        foreach ($data as $key => $value) {
            $stmt->bindParam($key, $value);  // Use $key directly as the parameter name
            // Bind the primary key parameter
        }
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }
    public function deleteFoncier( $id){
        // $primaryKey = "id";
        $sql = "DELETE from `foncier` WHERE `id` = :id ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }
    public function createEmployeur($data){
        // $sql="INSERT INTO `revenu`( id_type_revenu_id, id_debiteur_id , revenu , adresse) VALUES (:id_type_revenu_id,:id_debiteur_id,:revenu , :adresse);";
        $sql = "INSERT INTO `employeur` (";
        $values = [];
        $params = [];

        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $values[] = "`$key`";
            // Use placeholders for values except for 'statut'
            if ($key === 'statut') {
                // Set 'statut' to the default value '0'
                $params[] = "0";
            } else {
                $params[] = ":" . $key;
            }
        }

        $sql .= implode(', ', $values) . ')';
        $sql .= ' VALUES (' . implode(', ', $params) . ')';

        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            // Bind values for all columns except 'statut'
            if ($key !== 'statut') {
                $stmt->bindValue(":" . $key, $value);
            }
        }
        $result = $stmt->execute();
    }
    public function updateEmployeur($data , $id){
        // $primaryKey = "id";
        $sql = "UPDATE `employeur` SET ";
        $setClauses = [];
        foreach ($data as $key => $value) {
                $setClauses[] = "`$key` = :$key";
        }
        $sql .= implode(', ', $setClauses);
        $sql .= " WHERE `id` = :id";
        $stmt = $this->conn->prepare($sql);
        // Bind parameters for the SET clauses

        // dump($data);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindParam("id", $id);
        // dump($stmt);
        $stmt = $stmt->executeQuery();
    }
    public function deleteEmployeur( $id){
        // $primaryKey = "id";
        $sql = "DELETE from `employeur` WHERE `id` = :id ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }
    public function HistoriqueEmploi($data){
        // $sql="INSERT INTO `revenu`( id_type_revenu_id, id_debiteur_id , revenu , adresse) VALUES (:id_type_revenu_id,:id_debiteur_id,:revenu , :adresse);";
        $sql = "INSERT INTO `compte_bancaire` (";
        $values = [];
        $params = [];

        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $values[] = "`$key`";
            // Use placeholders for values except for 'statut'
            if ($key === 'status') {
                // Set 'statut' to the default value '0'
                $params[] = "0";
            } else {
                $params[] = ":" . $key;
            }
        }

        $sql .= implode(', ', $values) . ')';
        $sql .= ' VALUES (' . implode(', ', $params) . ')';

        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            // Bind values for all columns except 'statut'
            if ($key !== 'status') {
                $stmt->bindValue(":" . $key, $value);
            }
        }
        $result = $stmt->execute();
    }
    public function updateCompteBancaire($data , $id){
        // $primaryKey = "id";
        $sql = "UPDATE `compte_bancaire` SET ";
        $setClauses = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
                $setClauses[] = "`$key` = :$key";
        }
        $sql .= implode(', ', $setClauses);
        // You need to specify which row to update, typically using the primary key
        $sql .= " WHERE `id` = :id";
        $stmt = $this->conn->prepare($sql);
        // Bind parameters for the SET clauses
        foreach ($data as $key => $value) {
            $stmt->bindParam($key, $value);  // Use $key directly as the parameter name
            // Bind the primary key parameter
        }
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }
    public function deleteCompteBancaire( $id){
        // $primaryKey = "id";
        $sql = "DELETE from `compte_bancaire` WHERE `id` = :id ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }
    public function createHistoriqueEmploi($data){
        // $sql="INSERT INTO `revenu`( id_type_revenu_id, id_debiteur_id , revenu , adresse) VALUES (:id_type_revenu_id,:id_debiteur_id,:revenu , :adresse);";
        $sql = "INSERT INTO `historique_emploi` (";
        $values = [];
        $params = [];

        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $values[] = "`$key`";
            // Use placeholders for values except for 'statut'
            if ($key === 'status') {
                // Set 'statut' to the default value '0'
                $params[] = "0";
            } else {
                $params[] = ":" . $key;
            }
        }

        $sql .= implode(', ', $values) . ')';
        $sql .= ' VALUES (' . implode(', ', $params) . ')';

        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            // Bind values for all columns except 'statut'
            if ($key !== 'status') {
                $stmt->bindValue(":" . $key, $value);
            }
        }
        $result = $stmt->execute();
    }
    public function updateHistoriqueEmploi($data , $id){
        // $primaryKey = "id";
        $sql = "UPDATE `historique_emploi` SET ";
        $setClauses = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
                $setClauses[] = "`$key` = :$key";
        }
        $sql .= implode(', ', $setClauses);
        // You need to specify which row to update, typically using the primary key
        $sql .= " WHERE `id` = :id";
        $stmt = $this->conn->prepare($sql);
        // Bind parameters for the SET clauses
        foreach ($data as $key => $value) {
            $stmt->bindParam($key, $value);  // Use $key directly as the parameter name
            // Bind the primary key parameter
        }
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }
    public function deleteHistoriqueEmploi( $id){
        // $primaryKey = "id";
        $sql = "DELETE from `historique_emploi` WHERE `id` = :id ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }

    public function createEmploi($data){
        // $sql="INSERT INTO `revenu`( id_type_revenu_id, id_debiteur_id , revenu , adresse) VALUES (:id_type_revenu_id,:id_debiteur_id,:revenu , :adresse);";
        $sql = "INSERT INTO `emploi` (";
        $values = [];
        $params = [];

        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $values[] = "`$key`";
            // Use placeholders for values except for 'statut'
            if ($key === 'status') {
                // Set 'statut' to the default value '0'
                $params[] = "0";
            } else {
                $params[] = ":" . $key;
            }
        }

        $sql .= implode(', ', $values) . ')';
        $sql .= ' VALUES (' . implode(', ', $params) . ')';

        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            // Bind values for all columns except 'statut'
            if ($key !== 'status') {
                $stmt->bindValue(":" . $key, $value);
            }
        }
        $result = $stmt->execute();
    }
    public function updateEmploi($data , $id){
        // $primaryKey = "id";
        $sql = "UPDATE `emploi` SET ";
        $setClauses = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
                $setClauses[] = "`$key` = :$key";
        }
        $sql .= implode(', ', $setClauses);
        // You need to specify which row to update, typically using the primary key
        $sql .= " WHERE `id` = :id";
        $stmt = $this->conn->prepare($sql);
        // Bind parameters for the SET clauses
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }
    public function deleteEmploi( $id){
        // $primaryKey = "id";
        $sql = "DELETE from `emploi` WHERE `id` = :id ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }
    public function getOneEmploi($id_emploi , $id_deb){
        $sql="select * from  emploi where id = :id_emploi and id_debiteur_id = :id_deb";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id_emploi', $id_emploi);
        $stmt->bindParam('id_deb', $id_deb);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAssociative();
        if($resulat){
            return $resulat;
        }else{
            return null;
        }
    }
    public function getClassificationByProfession($idProfession){
        $sql="select id from  classification where id in (select p.id_classification_id from profession p where p.id = :idProfession)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('idProfession', $idProfession);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchOne();
        if($resulat){
            return $resulat;
        }else{
            return null;
        }
    }
    public function createTelephone($data){
        // $sql="INSERT INTO `revenu`( id_type_revenu_id, id_debiteur_id , revenu , adresse) VALUES (:id_type_revenu_id,:id_debiteur_id,:revenu , :adresse);";
        $sql = "INSERT INTO `telephone` (";
        $values = [];
        $params = [];

        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $values[] = "`$key`";
            $params[] = ":" . $key;
        }

        $sql .= implode(', ', $values) . ')';
        $sql .= ' VALUES (' . implode(', ', $params) . ')';

        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            // Bind values for all columns except 'statut'
                $stmt->bindValue(":" . $key, $value);
        }
        $result = $stmt->execute();
    }
    public function updateTelephone($data , $id){
        // $primaryKey = "id";
        $sql = "UPDATE `telephone` SET ";
        $setClauses = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $setClauses[] = "`$key` = :$key";
        }
        $sql .= implode(', ', $setClauses);
        // You need to specify which row to update, typically using the primary key
        $sql .= " WHERE `id` = :id";
        $stmt = $this->conn->prepare($sql);

        // Bind the ID parameter
        $stmt->bindValue(":id", $id);

        // Bind parameters for the SET clauses
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);  // Use $key directly as the parameter name
        }

        // Execute the statement
        $stmt->execute();
    }

    public function checkIfDoubleTeleAndNotActive($id_debiteur){
        $sql = "SELECT count(id) from telephone where id_debiteur_id =:id_debiteur and active = 1 ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id_debiteur", $id_debiteur);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchOne();
        return $resulat;
    }
    public function deleteTelephone( $id){
        $sql = "DELETE from `telephone` WHERE `id` = :id ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }
    public function createAdresse($data){
        // $sql="INSERT INTO `revenu`( id_type_revenu_id, id_debiteur_id , revenu , adresse) VALUES (:id_type_revenu_id,:id_debiteur_id,:revenu , :adresse);";
        $sql = "INSERT INTO `adresse` (";
        $values = [];
        $params = [];

        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $values[] = "`$key`";
            $params[] = ":" . $key;
        }

        $sql .= implode(', ', $values) . ')';
        $sql .= ' VALUES (' . implode(', ', $params) . ')';

        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            // Bind values for all columns except 'statut'
                $stmt->bindValue(":" . $key, $value);
        }
        $result = $stmt->execute();
    }
    public function updateAdresse($data , $id){
        // $primaryKey = "id";
        $sql = "UPDATE `adresse` SET ";
        $setClauses = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $setClauses[] = "`$key` = :$key";
        }
        $sql .= implode(', ', $setClauses);
        // You need to specify which row to update, typically using the primary key
        $sql .= " WHERE `id` = :id";
        $stmt = $this->conn->prepare($sql);

        // Bind the ID parameter
        $stmt->bindValue(":id", $id);

        // Bind parameters for the SET clauses
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);  // Use $key directly as the parameter name
        }
        // Execute the statement
        $stmt->execute();
    }
    public function deleteAdresse( $id){
        // $primaryKey = "id";
        $sql = "DELETE from `adresse` WHERE `id` = :id ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }
    public function createEmail($data){
        // $sql="INSERT INTO `revenu`( id_type_revenu_id, id_debiteur_id , revenu , adresse) VALUES (:id_type_revenu_id,:id_debiteur_id,:revenu , :adresse);";
        $sql = "INSERT INTO `email` (";
        $values = [];
        $params = [];

        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $values[] = "`$key`";
            $params[] = ":" . $key;
        }

        $sql .= implode(', ', $values) . ')';
        $sql .= ' VALUES (' . implode(', ', $params) . ')';

        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            // Bind values for all columns except 'statut'
                $stmt->bindValue(":" . $key, $value);
        }
        $result = $stmt->execute();
    }
    public function updateEmail($data , $id){
        // $primaryKey = "id";
        $sql = "UPDATE `email` SET ";
        $setClauses = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $setClauses[] = "`$key` = :$key";
        }
        $sql .= implode(', ', $setClauses);
        // You need to specify which row to update, typically using the primary key
        $sql .= " WHERE `id` = :id";
        $stmt = $this->conn->prepare($sql);

        // Bind the ID parameter
        $stmt->bindValue(":id", $id);

        // Bind parameters for the SET clauses
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);  // Use $key directly as the parameter name
        }
        // Execute the statement
        $stmt->execute();
    }
    public function deleteEmail( $id){
        // $primaryKey = "id";
        $sql = "DELETE from `email` WHERE `id` = :id ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }
    public function getIdenticatifPays(){
        $sql = "select * from `paysindicatif`  ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        return $result = $stmt->fetchAll();
    }
    
    public function checkCodePays($codeP){
        $sql = "select * from `paysindicatif` where Indicatif = :codeP  ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("codeP", $codeP);
        $stmt = $stmt->executeQuery();
        return $result = $stmt->fetchOne();
    }

    public function getTelephoneById($id){
        $sql = "select * from `telephone` where id = :id  ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAssociative();

        $sql = "select * from `type_source` where id = :id  ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $result['id_type_source_id']);
        $stmt = $stmt->executeQuery();
        $source = $stmt->fetchAssociative();
        $result['id_type_source_id'] = $source; 

        $sql = "select * from `type_tel` where id = :id  ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $result['id_type_tel_id']);
        $stmt = $stmt->executeQuery();
        $type_telephone = $stmt->fetchAssociative();
        $result['id_type_tel_id'] = $type_telephone; 

        $sql = "select * from `status_telephone` where id = :id  ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $result['id_status_id']);
        $stmt = $stmt->executeQuery();
        $type_telephone = $stmt->fetchAssociative();
        $result['id_status_id'] = $type_telephone; 
        return $result;
    }
    

    public function getAdresseById($id){
        $sql = "select * from `adresse` where id = :id  ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAssociative();

        $sql = "select * from `type_source` where id = :id  ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $result['id_type_source_id']);
        $stmt = $stmt->executeQuery();
        $source = $stmt->fetchAssociative();
        $result['id_type_source_id'] = $source; 

        $sql = "select * from `type_adresse` where id = :id  ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $result['id_type_tel_id']);
        $stmt = $stmt->executeQuery();
        $type_telephone = $stmt->fetchAssociative();
        $result['id_type_tel_id'] = $type_telephone; 

        $sql = "select * from `status_adresse` where id = :id  ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $result['id_status_id']);
        $stmt = $stmt->executeQuery();
        $type_telephone = $stmt->fetchAssociative();
        $result['id_status_id'] = $type_telephone; 
        return $result;
    }
    public function getEmailById($id){
        $sql = "select * from `email` where id = :id  ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAssociative();

        $sql = "select * from `type_source` where id = :id  ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $result['id_type_source_id']);
        $stmt = $stmt->executeQuery();
        $source = $stmt->fetchAssociative();
        $result['id_type_source_id'] = $source; 

        $sql = "select * from `type_email` where id = :id  ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $result['id_type_email_id']);
        $stmt = $stmt->executeQuery();
        $type_email = $stmt->fetchAssociative();
        $result['id_type_email_id'] = $type_email; 

        $sql = "select * from `status_email` where id = :id  ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $result['id_status_id']);
        $stmt = $stmt->executeQuery();
        $type_email = $stmt->fetchAssociative();
        $result['id_status_id'] = $type_email; 
        return $result;
    }

    public function createRelation($data){
        // $sql="INSERT INTO `revenu`( id_type_revenu_id, id_debiteur_id , revenu , adresse) VALUES (:id_type_revenu_id,:id_debiteur_id,:revenu , :adresse);";
        $sql = "INSERT INTO `personne` (";
        $values = [];
        $params = [];

        foreach ($data as $key => $value) {
            if($key != "id_type_relation_id" && $key != "id_debiteur_id"){
                // Enclose column names within backticks if needed
                $values[] = "`$key`";
                $params[] = ":" . $key;
            }
        }

        $sql .= implode(', ', $values) . ')';
        $sql .= ' VALUES (' . implode(', ', $params) . ')';

        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            if($key != "id_type_relation_id" && $key != "id_debiteur_id"){
            // Bind values for all columns except 'statut'
                $stmt->bindValue(":" . $key, $value);
            }
        }
        $result = $stmt->execute();

        $sql = "select max(id) from `personne`";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $typeRelation = $stmt->fetchOne();
        
        $sql = "INSERT INTO `relation_debiteur`(`id_debiteur_id`, `id_personne_id`, `id_relation_id`) VALUES (:id_debiteur_id, :id_personne_id, :id_relation_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue("id_debiteur_id", $data['id_debiteur_id']);
        $stmt->bindValue("id_relation_id", $data['id_type_relation_id']); // Correct parameter name
        $stmt->bindValue("id_personne_id", $typeRelation);
        $result = $stmt->execute();
    }
    public function updateRelation($data , $id){
        // $primaryKey = "id";
        $sql = "UPDATE `personne` SET ";
        $setClauses = [];
        foreach ($data as $key => $value) {
            if($key != "id_type_relation_id" && $key != "id_debiteur_id"){
                // Enclose column names within backticks if needed
                $setClauses[] = "`$key` = :$key";
            }
        }
        $sql .= implode(', ', $setClauses);
        // You need to specify which row to update, typically using the primary key
        $sql .= " WHERE `id` = :id";
        $stmt = $this->conn->prepare($sql);

        // Bind the ID parameter
        $stmt->bindValue(":id", $id);

        // Bind parameters for the SET clauses
        foreach ($data as $key => $value) {
            if($key != "id_type_relation_id" && $key != "id_debiteur_id"){
                $stmt->bindValue(":$key", $value);  // Use $key directly as the parameter name
            }
        }
        // Execute the statement
        $stmt->execute();
    }
    public function deleteRelation( $id){
        $gp = $this->em->getRepository(RelationDebiteur::class)->findBy(array('id_personne' => $id));
        foreach ($gp as $item) {
            $this->em->remove($item);
        }
        $this->em->flush();
        
        // $primaryKey = "id";
        $sql = "DELETE from `personne` WHERE `id` = :id ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }

    
    public function getRelationById($id){
        $sql = "select * from `personne` where id = 10";
        $stmt = $this->conn->prepare($sql);
        // $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAssociative();

        $sql = "select * from `relation_debiteur` where id_personne_id = :id  ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
        $source = $stmt->fetchAssociative();
        $result['id_type_relation_id'] = $source; 

        return $result;
    }
    
    
}