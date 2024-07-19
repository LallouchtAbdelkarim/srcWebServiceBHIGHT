<?php

namespace App\Repository\Dossiers;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
class dossiersRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    public function getListesDossiers(){
        $sql="select * from  v_dossier LIMIT 0,7;";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function findDossier($id){
        $sql="select * from  dossier where id = ".$id."";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function findDebiteursByDossier($id){
        $sql="select * from  dossier where id = ".$id."";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getListesDossiersByFiltrages($data){
        $num_dossier = $data["num_dossier"];
        $date_fin_prevesionnel = $data["date_fin_prevesionnel"];
        $ptf = $data["ptf"];
        $agent = $data["agent"];

        $cin = $data["cin"];
        $raison_social = $data["raison_social"];

        $tel = $data["tel"];
        $addr = $data["addr"];
        $date_naissance = $data["date_naissance"];
        $date_echeance = $data["date_echeance"];
        $num_creance = $data["num_creance"];
        

        $query = 'SELECT DISTINCT d.*
          FROM dossier d
          INNER JOIN creance c ON d.id = c.id_dossier_id
          INNER JOIN type_debiteur t ON c.id = t.id_creance_id
          INNER JOIN debiteur deb ON t.id_debiteur_id = deb.id';

        if ($tel != "") {
            $query .= ' INNER JOIN telephone tel ON deb.id = tel.id_debiteur_id';
        }
        if ($addr != "") {
            $query .= ' INNER JOIN adresse ad ON deb.id = ad.id_debiteur_id';
        }

        if ($num_dossier != "") {
            $query .= ' WHERE d.numero_dossier = "'.$num_dossier.'" ';
        }
        if ($agent != "") {
            $query .= ' AND d.id_users_id = "'.$agent.'"';
        }
        if ($ptf != "") {
            $query .= ' AND d.id_ptf_id = "'.$ptf.'"';
        }
        if ($date_fin_prevesionnel != "") {
            $dateDebut = $date_fin_prevesionnel . " 00:00:00";
            $dateFin = $date_fin_prevesionnel . " 23:59:59";
            $query .= ' AND d.date_fin_prevesionnel BETWEEN "'.$dateDebut.'" AND "'.$dateFin.'"';
        }
        if ($date_echeance != "") {
            $dateDebut = $date_echeance . " 00:00:00";
            $dateFin = $date_echeance . " 23:59:59";
            $query .= ' AND c.date_echeance BETWEEN "'.$dateDebut.'" AND "'.$dateFin.'"';
        }
        if ($cin != "") {
            $query .= ' AND deb.cin like "'.$cin.'" ';
        }
        if($raison_social != ""){
            $query .= ' AND deb.raison_social like "'.$raison_social.'" '; 
        }

        if($tel != ""){
            $query .=' AND tel.numero = "'.$tel.'" AND tel.active = 1';
        }
        if($addr != ""){
            $query .= ' AND ad.adresse_complet = "'.$addr.'" AND ad.verifier = 1'; 
        }
        if($date_naissance != ""){
            $dateDebut = $date_naissance. " 00:00:00";
            $dateFin = $date_naissance. " 23:59:59";
            $query .= ' AND deb.date_naissance BETWEEN "'.$dateDebut.'" AND "'.$dateFin.'"';
        }
        if($date_echeance != ""){
            $dateDebut = $date_echeance. " 00:00:00";
            $dateFin = $date_echeance. " 23:59:59";
            $query .= ' AND c.date_echeance BETWEEN "'.$dateDebut.'" AND "'.$dateFin.'"';
        }
        if($num_creance != ""){
            $query .= ' AND c.numero_creance like "'.$num_creance.'" ';
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getDebiteurByDossier($id){
        $sql="SELECT  deb.* FROM debiteur deb where deb.id in (select dd.id_debiteur_id from debi_doss dd where dd.id_dossier_id = :id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        return $resulat;
    }

    public function getDetailsCreanceByIdDossier($id){
        $sql="SELECT SUM(total_creance) FROM `creance` WHERE id_dossier_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $total_creance = $stmt->fetchOne();
        $sql="SELECT SUM(total_restant) FROM `creance` WHERE id_dossier_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $total_restant = $stmt->fetchOne();

        $result["total_creance"] = $total_creance;
        $result["total_restant"] = $total_restant;
        return $result;
    }
    public function getCreanceByIdDossier($id){
        $sql="SELECT * FROM `creance` WHERE id_dossier_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAll();
        for ($i=0; $i < count($result); $i++) { 
            $sql="SELECT * FROM `details_type_creance` WHERE id = :idType";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('idType', $result[$i]['id_type_creance_id']);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $result[$i]['type_creance'] = $type;
        }
        return $result;
    }
    

    public function getListesDebiteurByDossier($id){
        $sql="SELECT  deb.* FROM debiteur deb where deb.id in (select dd.id_debiteur_id from debi_doss dd where dd.id_dossier_id = :id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        // Fetch type information for each telephone
        
        for ($i=0; $i <count($resulat) ; $i++) { 
            $sql = "SELECT * FROM `details_type_deb` dt where dt.id in (select d.id_type_id from type_debiteur d where d.id_creance_id in (select c.id from creance c where c.id_dossier_id = :id));";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $resulat[$i]['id']);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $resulat[$i]["type"] = $type;
        }
        return $resulat;
    }
    public function getListesAdresse($id){
        $sql="SELECT DISTINCT ad.*
        FROM adresse ad
        INNER JOIN debiteur deb ON ad.id_debiteur_id = deb.id
        INNER JOIN type_debiteur t ON deb.id = t.id_debiteur_id
        INNER JOIN creance c ON t.id_creance_id = c.id
        WHERE ad.verifier = 1
        AND c.id_dossier_id = :id;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        return $resulat;
    }
    
    public function getTypeAdresse($type_adresse){
        $sql="SELECT * from type_adresse where id =:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $type_adresse);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        if($resulat){
            return $resulat[0];
        }
        return $resulat;
    }
    public function getTypeTel($type_tel){
        $sql="SELECT * from type_tel where id =:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $type_tel);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        if($resulat){
            return $resulat[0];
        }
        return $resulat;
    }
    public function getTypePaiem($type_id){
        $sql="SELECT * from type_paiement where id =:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $type_id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        if($resulat){
            return $resulat[0]['type'];
        }
        return $resulat;
    }
    public function getTotalCreanceByAcc($id){
        $sql="SELECT SUM(c.total_creance) as sum_creance from creance c 
        where c.id in (select ca.id_creance_id from creance_accord ca where ca.id_accord_id = :id );";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        if($resulat){
            return $resulat[0]['sum_creance'];
        }
        return $resulat;
    }
    public function getMontantRestant($id){
        $sql="SELECT SUM(d.montant_restant) as rest from details_accord d 
        where d.id_accord_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        if($resulat){
            return $resulat[0]['rest'];
        }
        return $resulat;
    }
    
    public function getTypeEmail($type_email){
        $sql="SELECT * from type_email where id =:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $type_email);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        if($resulat){
            return $resulat[0];
        }
        return $resulat;
    }
    public function getEmailDebiteur($id){
        $sql="SELECT DISTINCT ad.*
        FROM email ad
        INNER JOIN debiteur deb ON ad.id_debiteur_id = deb.id
        INNER JOIN type_debiteur t ON deb.id = t.id_debiteur_id
        INNER JOIN creance c ON t.id_creance_id = c.id
        WHERE ad.status = 1
        AND c.id_dossier_id = :id;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        return $resulat;
    }
    public function getListesTel($id){
        $sql="SELECT DISTINCT tel.* FROM telephone tel 
        INNER JOIN debiteur deb ON tel.id_debiteur_id = deb.id 
        INNER JOIN type_debiteur t ON deb.id = t.id_debiteur_id
        INNER JOIN creance c ON t.id_creance_id = c.id WHERE tel.active = 1 AND c.id_dossier_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        return $resulat;
    }
    public function getHistoriqueDossier($id){
        $sql="select h.* from historique_dossier h where h.id_dossier_id in (select d.id FROM dossier d where id = :id);";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        return $resulat;
    }
    public function getAccord($id){
        $sql="select h.* from historique_dossier h where h.id_dossier_id in (select d.id FROM dossier d where id = :id);";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        return $resulat;
    }
    public function getListeNote($id){
        $sql="select h.* from note_dossier h where h.id_dossier_id = :id ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        return $resulat;
    }
    
    public function createNoteDossier($id , $note){
        $sql="INSERT INTO `note_dossier`( `id_dossier_id`, `note`,`date_creation`) VALUES (:id,:note,now());";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->bindParam('note', $note);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        return $resulat;
    }
    public function getAccords($id){
        $sql="SELECT distinct a.* FROM accord a INNER JOIN creance_accord ca ON a.id = ca.id_accord_id
        INNER JOIN creance c ON ca.id_creance_id = c.id
        WHERE c.id_dossier_id = :id;
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        return $resulat;
    }
    public function getAccordsArchives($id){
        $sql="SELECT distinct a.* FROM accord a INNER JOIN creance_accord ca ON a.id = ca.id_accord_id
        INNER JOIN creance c ON ca.id_creance_id = c.id
        WHERE c.id_dossier_id = :id and a.etat = 6";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        return $resulat;
    }
    public function getCourrier($id){
        $sql="select * from courrier where id_dossier_id =:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        return $resulat;
    }
    public function getSms($id){
        $sql="select * from sms where id_dossier_id =:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        return $resulat;
    }
    public function getEmail($id){
        $sql="select * from email_camp where id_dossier_id =:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        return $resulat;
    }
    public function getProcessByIdUser($idDossier , $idUser){
        $sql="select * from queue_event_user where id_user_id =:idUser and id_status_id = 2 and id_queue_event_id in (select q.id from queue_event q where q.id_element = :idDossier);";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('idUser', $idUser);
        $stmt->bindParam('idDossier', $idDossier);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        
        for ($i=0; $i < count($resulat); $i++) { 
            $sql="select * from queue_event where id =:id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $resulat[$i]['id_queue_event_id']);
            $stmt = $stmt->executeQuery();
            $queue = $stmt->fetchAssociative();
            $resulat[$i]['queue_event'] = $queue; 

            $sql="select * from event_action where id =:id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $queue['id_event_action_id']);
            $stmt = $stmt->executeQuery();
            $action = $stmt->fetchAssociative();
            $resulat[$i]['event_action'] = $action; 

            $sql="select * from evenement_workflow where id =:id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $action['id_event_id']);
            $stmt = $stmt->executeQuery();
            $evenement = $stmt->fetchAssociative();
            $resulat[$i]['evenement'] = $evenement; 
        }

        return $resulat;
    }

    public function getProcessWorkflow(){
        
    }
}