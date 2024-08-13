<?php

namespace App\Repository\Missions;

use App\Entity\DetailMission;
use App\Entity\FileMissions;
use App\Entity\Missions;
use App\Entity\StatusMissions;
use App\Entity\Utilisateurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
class missionsRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    
    public function getTypeMissions(){
        $sql="SELECT * FROM `type_missions` ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getAllIFilleMissions()
    {
        $resultList = $this->em->getRepository(FileMissions::class)->findAll();
        if($resultList){
            return $resultList; 
        }else{
            return [];
        }
    }

    public function getOneFile($id){
        $sql="SELECT * FROM `file_missions` where id = ".$id." ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAssociative();

        if($result){
            $sql="SELECT * FROM `type_missions` where id = ".$result["id_type_missions_id"]." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $result["type_missions"] = $type;
        }
        return $result;
    }
    public function getDetailsFile($id){
        $sql="SELECT * FROM `details_file` where id_file_missions_id = ".$id." ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAll();
        return $result;
    }
    public function getAgent($id){
        $result = $this->em->getRepository(Utilisateurs::class)->find($id);
        return $result;
    }
    public function createMission($idAgent , $idFile , $date_debut, $date_fin){
        $status = $this->em->getRepository(StatusMissions::class)->find(2);
        $h=new Missions();
        $h->setIdUsers($idAgent);
        $h->setIdFile($idFile);
        $h->setIdStatus($status);
        $h->setDateCreation(new \DateTime());
        $h->setDateDebut(new \DateTime($date_debut));
        $h->setDateFin(new \DateTime($date_fin));
        $this->em->persist($h);
        $this->em->flush();
        return $h;
    }
    public function getFile($id)
    {
        $resultList = $this->em->getRepository(FileMissions::class)->find($id);
        return $resultList; 
    }
    public function getListeMissions()
    {
        $resultList = $this->em->getRepository(Missions::class)->findAll();
        if($resultList){
            return $resultList; 
        }else{
            return [];
        }
    }

    public function getMissionsByFile($id)
    {
        $resultList = $this->em->getRepository(Missions::class)->findBy(['id_file'=>$id]);
        if($resultList){
            return $resultList; 
        }else{
            return [];
        }
    }
    public function getMissionsDetails($id)
    {
        $resultList = $this->em->getRepository(DetailMission::class)->findBy(['id_mission'=>$id]);
        return $resultList; 
    }
    
    public function getOneMissions($id)
    {
        $resultList = $this->em->getRepository(Missions::class)->find($id);
        return $resultList; 
    }
    
    public function getDetailsByFileAndUser($id , $idFile){
        $sql="SELECT 
            df.id, 
            df.id_file_missions_id, 
            df.numero_dossier, 
            df.adresse, 
            df.is_in_missions, 
            dm.id AS detail_mission_id, 
            dm.etat, 
            m.id AS mission_id, 
            m.id_status_id, 
            m.id_users_id, 
            m.date_creation
        FROM 
            details_file df
        INNER JOIN 
            detail_mission dm ON df.id = dm.id_detail_file_id
        INNER JOIN 
            missions m ON dm.id_mission_id = m.id
        WHERE 
            m.id_users_id =".$id." 
            and df.id_file_missions_id = ".$idFile."";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAll();
        return $result;
    }

    public function getDossierPreAffectation(){
        $sql="SELECT * FROM `dossier` where id_status_assign_id = 1 ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAll();
        for ($i=0; $i <count($result) ; $i++) { 
            # code...
            $sql="SELECT * FROM `portefeuille` where id = ".$result[$i]["id_ptf_id"]." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $result[$i]["ptf"] = $type;
        }
        return $result;
    }

    public function getDossierBySeg($id){
        $sql="select * from dossier d where d.id in (select s.id_dossier from debt_force_seg.seg_dossier s where s.id_seg = ".$id.");";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAll();
        for ($i=0; $i <count($result) ; $i++) { 
            # code...
            $sql="SELECT * FROM `portefeuille` where id = ".$result[$i]["id_ptf_id"]." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $result[$i]["ptf"] = $type;
        }
        return $result;
    }

    public function getDossiersByCriteres($data){
     
        
        $numero_dossier = isset($data['numero_dossier']) ? $data['numero_dossier'] : '';
        $numero_creance = isset($data['numero_creance']) ? $data['numero_creance'] : '' ;
        $cin_debiteur = isset($data['cin_debiteur']) ? $data['cin_debiteur'] : '' ;

        

        $query = 'SELECT DISTINCT d.* FROM dossier d ';
        $compl = " ";

        if($numero_creance != ''){
            $query .= ' INNER JOIN creance c ON d.id = c.id_dossier_id';
        }

        if($cin_debiteur != ''){

            if($numero_creance == ""){
                $query .= 'INNER JOIN creance c ON d.id = c.id_dossier_id';
            }
            $query .= '
             INNER JOIN type_debiteur t ON c.id = t.id_creance_id
            INNER JOIN debiteur deb ON t.id_debiteur_id = deb.id';
        }


        //TODO:Dossier

        if ($numero_dossier != "") {
            $compl .= ' WHERE d.numero_dossier = "'.$numero_dossier.'" ';
        }
        
        
        //TODO:deb
        if ($cin_debiteur != "") {
            $compl .= ' AND deb.cin like "'.$cin_debiteur.'" ';
        }
        
        //TODO:deb
        if ($numero_creance != "") {
            $compl .= ' AND c.numero_creance like "'.$numero_creance.'" ';
        }

        $query = $query. ' '.$compl;
       
        $stmt = $this->conn->prepare($query);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();

        // $array = array();
        
        for ($i=0; $i <count($resulat) ; $i++) { 
            # code...
            $sql="SELECT * FROM `portefeuille` where id = ".$resulat[$i]["id_ptf_id"]." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $resulat[$i]["ptf"] = $type;
        }
        
        return $resulat;

    }

    
    
    
}

