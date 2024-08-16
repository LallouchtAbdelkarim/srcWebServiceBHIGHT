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
    public function addHistorique($id){
      $sql="INSERT INTO histo_telephone (id,numero,origine,id_status_id,id_type_tel_id,note1,type,date_creation,id_dn,id_debiteur_id,id_integration,date_action)
        SELECT id, numero, origine, id_status_id, id_type_tel_id, note1, type, date_creation, id_dn, id_debiteur_id, id_integration, SYSDATE()
        FROM telephone WHERE id = :id;" ;
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
    }

    public function addHistoriqueEmail($id){
        $sql="INSERT INTO historique_email (id_debiteur,email,status,id_type_email,active,id_integration,id_type_source,id_status_email,date_creation,date_action
)
        SELECT  id_debiteur_id, email, status, id_type_email_id, active, id_integration, id_type_source_id, id_status_email_id, date_creation, SYSDATE()
        FROM
            email
        WHERE
            id = :id;
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
    }

    public function addHistoriqueAdresse($id){
        $sql="INSERT INTO histo_adresse (  origine, id_status_id, id_type_adresse_id, id_users_id, type, date_creation, adresse_complet, pays, verifier, code_postal, province, source, region, ville, id_debiteur_id, date_action)
        SELECT  origine, id_status_id, id_type_adresse_id, NULL,  NULL, date_creation, adresse_complet, pays, verifier, code_postal, province, source, region, ville, id_debiteur_id, SYSDATE()
        FROM
            adresse
        WHERE
            id = :id;

                ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
    }
    

    public function addHistoriqueEmployeur($id){
        $sql="INSERT INTO histo_employeur ( id_debiteur, employeur, entreprise, adresse_employeur, poste, id_status, etat, id_integration, date_action
            )
            SELECT id_debiteur_id, employeur, entreprise, adresse_employeur, poste, id_status_id, etat, id_integration, SYSDATE()
            FROM
                employeur
            WHERE
                id = :id;
                ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
    }

    public function addHistoriqueEmploi($id){
        $sql="INSERT INTO historique_emploi ( id_status_id, date_debut, date_fin, date_dernier_salaire, date_naissance,  nom_empl, salaire, profession,  id_debiteur, date_action
            )
            SELECT  id_status_id, date_debut, date_fin, date_dernier_salaire, NULL,  nom_empl, salaire, NULL,  id_debiteur_id, SYSDATE()
            FROM emploi
            WHERE id = :id;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
    }
    
    public function getHistoTelephone($date_debut , $date_fin , $idPtf){
        $sql="SELECT * FROM `histo_telephone` h where (h.date_action between '".$date_debut."' and '".$date_fin."') ";
        if($idPtf != 0){
            $sql .= "and h.id_debiteur_id in (select t.id_debiteur_id from type_debiteur t where t.id_creance_id in (select c.id from creance c where c.id_ptf_id = ".$idPtf."))";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }

    public function getListeTel($date_debut , $date_fin , $idPtf){
        $sql="SELECT * FROM `telephone` h where (h.date_creation between '".$date_debut."' and '".$date_fin."')";
        if($idPtf != 0){
            $sql .= "and h.id_debiteur_id in (select t.id_debiteur_id from type_debiteur t where t.id_creance_id in (select c.id from creance c where c.id_ptf_id = ".$idPtf."))";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getListeAdresse($date_debut , $date_fin , $idPtf){
        $sql="SELECT * FROM `adresse` h where (h.date_creation between '".$date_debut."' and '".$date_fin."')";
        if($idPtf != 0){
            $sql .= "and h.id_debiteur_id in (select t.id_debiteur_id from type_debiteur t where t.id_creance_id in (select c.id from creance c where c.id_ptf_id = ".$idPtf."))";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getHistoAdresse($date_debut , $date_fin , $idPtf){
        $sql="SELECT * FROM `histo_adresse` h where (h.date_action between '".$date_debut."' and '".$date_fin."')";
        if($idPtf != 0){
            $sql .= "and h.id_debiteur_id in (select t.id_debiteur_id from type_debiteur t where t.id_creance_id in (select c.id from creance c where c.id_ptf_id = ".$idPtf."))";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getListeEmploi($date_debut , $date_fin , $idPtf){
        $sql="SELECT * FROM `emploi` h where (h.date_creation between '".$date_debut."' and '".$date_fin."')";
        if($idPtf != 0){
            $sql .= "and h.id_debiteur_id in (select t.id_debiteur_id from type_debiteur t where t.id_creance_id in (select c.id from creance c where c.id_ptf_id = ".$idPtf."))";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getHistoEmploi($date_debut , $date_fin , $idPtf){
        $sql="SELECT * FROM `historique_emploi` h where (h.date_action between '".$date_debut."' and '".$date_fin."')";
        if($idPtf != 0){
            $sql .= "and h.id_debiteur in (select t.id_debiteur_id from type_debiteur t where t.id_creance_id in (select c.id from creance c where c.id_ptf_id = ".$idPtf."))";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getListeEmployeur($date_debut , $date_fin , $idPtf){
        $sql="SELECT * FROM `employeur` h where (h.date_creation between '".$date_debut."' and '".$date_fin."')";
        if($idPtf != 0){
            $sql .= "and h.id_debiteur_id in (select t.id_debiteur_id from type_debiteur t where t.id_creance_id in (select c.id from creance c where c.id_ptf_id = ".$idPtf."))";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getHistoEmployeur($date_debut , $date_fin , $idPtf){
        $sql="SELECT * FROM `histo_employeur` h where (h.date_action between '".$date_debut."' and '".$date_fin."')";
        if($idPtf != 0){
            $sql .= "and h.id_debiteur in (select t.id_debiteur_id from type_debiteur t where t.id_creance_id in (select c.id from creance c where c.id_ptf_id = ".$idPtf."))";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getListeEmail($date_debut , $date_fin , $idPtf){
        $sql="SELECT * FROM `email` h where (h.date_creation between '".$date_debut."' and '".$date_fin."')";
        if($idPtf != 0){
            $sql .= "and h.id_debiteur_id in (select t.id_debiteur_id from type_debiteur t where t.id_creance_id in (select c.id from creance c where c.id_ptf_id = ".$idPtf."))";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getHistoEmail($date_debut , $date_fin , $idPtf){
        $sql="SELECT * FROM `historique_email`  h where (h.date_action between '".$date_debut."' and '".$date_fin."')";
        if($idPtf != 0){
            $sql .= "and h.id_debiteur in (select t.id_debiteur_id from type_debiteur t where t.id_creance_id in (select c.id from creance c where c.id_ptf_id = ".$idPtf."))";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }

    public function getListTelAdd($date_debut , $date_fin , $idPtf , $idImport){
        $sql="SELECT * FROM `integration` h where (h.date_fin_execution_3 between '".$date_debut."' and '".$date_fin."')";
        
        if($idPtf != 0){
            $sql .= "and h.id_ptf_id  ".$idPtf.")";
        }

        if($idImport != 0){
            $sql .= "and h.id  ".$idImport.")";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();

        for ($i=0; $i <count($resulat); $i++) { 
            $query = "SELECT count(id) from telephone where id_integration = ".$resulat[$i]['id']."";
            $stmt = $this->conn->prepare($query);
            $stmt = $stmt->executeQuery();
            $getOne = $stmt->fetchOne();
            $resulat[$i]['nbr'] = $getOne; 
        }
        for ($i=0; $i <count($resulat); $i++) { 
            $query = "SELECT * from portefeuille where id = ".$resulat[$i]['id_ptf_id']."";
            $stmt = $this->conn->prepare($query);
            $stmt = $stmt->executeQuery();
            $ptf = $stmt->fetchAssociative();
            $resulat[$i]['ptf'] = $ptf; 
        }

        return $resulat;
    }
    public function getListAdresseAdd($date_debut , $date_fin , $idPtf , $idImport){
        $sql="SELECT * FROM `integration` h where (h.date_fin_execution_3 between '".$date_debut."' and '".$date_fin."')";
        
        if($idPtf != 0){
            $sql .= "and h.id_ptf_id  ".$idPtf.")";
        }

        if($idImport != 0){
            $sql .= "and h.id  ".$idImport.")";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();

        for ($i=0; $i <count($resulat); $i++) { 
            $query = "SELECT count(id) from adresse where id_integration = ".$resulat[$i]['id']."";
            $stmt = $this->conn->prepare($query);
            $stmt = $stmt->executeQuery();
            $getOne = $stmt->fetchOne();
            $resulat[$i]['nbr'] = $getOne; 
        }
        for ($i=0; $i <count($resulat); $i++) { 
            $query = "SELECT * from portefeuille where id = ".$resulat[$i]['id_ptf_id']."";
            $stmt = $this->conn->prepare($query);
            $stmt = $stmt->executeQuery();
            $ptf = $stmt->fetchAssociative();
            $resulat[$i]['ptf'] = $ptf; 
        }

        return $resulat;
    }
    
    

    public function getListEmploiAdd($date_debut , $date_fin , $idPtf , $idImport){
        $sql="SELECT * FROM `integration` h where (h.date_fin_execution_3 between '".$date_debut."' and '".$date_fin."')";
        
        if($idPtf != 0){
            $sql .= "and h.id_ptf_id  ".$idPtf.")";
        }

        if($idImport != 0){
            $sql .= "and h.id  ".$idImport.")";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();

        for ($i=0; $i <count($resulat); $i++) { 
            $query = "SELECT count(id) from emploi where id_integration = ".$resulat[$i]['id']."";
            $stmt = $this->conn->prepare($query);
            $stmt = $stmt->executeQuery();
            $getOne = $stmt->fetchOne();
            $resulat[$i]['nbr'] = $getOne; 
        }
        for ($i=0; $i <count($resulat); $i++) { 
            $query = "SELECT * from portefeuille where id = ".$resulat[$i]['id_ptf_id']."";
            $stmt = $this->conn->prepare($query);
            $stmt = $stmt->executeQuery();
            $ptf = $stmt->fetchAssociative();
            $resulat[$i]['ptf'] = $ptf; 
        }

        return $resulat;
    }

    public function getListEmailAdd($date_debut , $date_fin , $idPtf , $idImport){
        $sql="SELECT * FROM `integration` h where (h.date_fin_execution_3 between '".$date_debut."' and '".$date_fin."')";
        
        if($idPtf != 0){
            $sql .= "and h.id_ptf_id  ".$idPtf.")";
        }

        if($idImport != 0){
            $sql .= "and h.id  ".$idImport.")";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();

        for ($i=0; $i <count($resulat); $i++) { 
            $query = "SELECT count(id) from email where id_integration = ".$resulat[$i]['id']."";
            $stmt = $this->conn->prepare($query);
            $stmt = $stmt->executeQuery();
            $getOne = $stmt->fetchOne();
            $resulat[$i]['nbr'] = $getOne; 
        }
        for ($i=0; $i <count($resulat); $i++) { 
            $query = "SELECT * from portefeuille where id = ".$resulat[$i]['id_ptf_id']."";
            $stmt = $this->conn->prepare($query);
            $stmt = $stmt->executeQuery();
            $ptf = $stmt->fetchAssociative();
            $resulat[$i]['ptf'] = $ptf; 
        }

        return $resulat;
    }
    
    public function getListEmployeurAdd($date_debut , $date_fin , $idPtf , $idImport){
        $sql="SELECT * FROM `integration` h where (h.date_fin_execution_3 between '".$date_debut."' and '".$date_fin."')";
        
        if($idPtf != 0){
            $sql .= "and h.id_ptf_id  ".$idPtf.")";
        }

        if($idImport != 0){
            $sql .= "and h.id  ".$idImport.")";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();

        for ($i=0; $i <count($resulat); $i++) { 
            $query = "SELECT count(id) from employeur where id_integration = ".$resulat[$i]['id']."";
            $stmt = $this->conn->prepare($query);
            $stmt = $stmt->executeQuery();
            $getOne = $stmt->fetchOne();
            $resulat[$i]['nbr'] = $getOne; 
        }
        for ($i=0; $i <count($resulat); $i++) { 
            $query = "SELECT * from portefeuille where id = ".$resulat[$i]['id_ptf_id']."";
            $stmt = $this->conn->prepare($query);
            $stmt = $stmt->executeQuery();
            $ptf = $stmt->fetchAssociative();
            $resulat[$i]['ptf'] = $ptf; 
        }
        return $resulat;
    }
}