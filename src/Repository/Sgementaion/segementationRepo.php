<?php

namespace App\Repository\Sgementaion;
use App\Entity\CritereParentSeg;
use App\Entity\CritereSegment;
use App\Entity\DetailCritereSegment;
use App\Entity\DetailsSeg;
use App\Entity\GroupeCritere;
use App\Entity\IntermGroupeCritere;
use App\Entity\SegCritere;
use App\Entity\Segmentation;
use App\Entity\SegGroupeCritere;
use App\Entity\StatusSeg;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class segementationRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    public function getListeSegment(){
        $resultList = $this->em->getRepository(Segmentation::class)->findAll();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getOneSegment($id){
        $sql="SELECT s.* FROM `debt_force`.`segmentation` s WHERE s.id =:id_seg";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_seg",$id);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchAssociative();
        return $statut;
    }
    public function getListeSgementByStatus($status){
        $sql="SELECT * FROM `segmentation` s WHERE s.id_status_id = :status";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":status",$status);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchAll();
        return $statut;
    }
    
    public function getListeSgementationByGroupe($id_type,$id_groupe){
        $sql="SELECT * FROM `segmentation` s WHERE s.id in (SELECT q.id_segmentation_id from queue q WHERE q.id_type_id = '".$id_type."' and q.queue_groupe_id = '".$id_groupe."')";
        $stmt = $this->conn->prepare($sql);
        // $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchAll();
        return $statut;
    }
    public function createSegment1($cle,$nom , $type , $description,$entity,$typeWorkflow){
        
        $status = $this->em->getRepository(StatusSeg::class)->findOneBy(['id'=>1]);
        $model = new Segmentation();
        $model->setCleIdentifiant($cle);
        $model->setNomSegment($nom);
        $model->setType($type);
        $model->setEntities($entity);
        $model->setDescription($description);
        $model->setDateCreation(new \DateTime);
        $model->setIdStatus($status);
        $this->em->persist($model);
        $this->em->flush();

        $sql="INSERT INTO `interm_workflow_segmentation`( `id_segmentaion_id`, `id_type_id`) VALUES (:value1,:value2)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":value1",$model->getId());
        $stmt->bindValue(":value2",$typeWorkflow);
        $stmt = $stmt->executeQuery();  

        return $model;
    }
    public function createSegCritere($critere , $id_groupe , $type){
        $model = new SegCritere();
        $model->setCritere($critere);
        $model->setIdGroupe($id_groupe);
        $model->setType($type);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }
    public function createGroupeCritereRepo($groupe , $id_seg , $priority){
        $seg = $this->em->getRepository(Segmentation::class)->findOneBy(["id"=>$id_seg]);
        $model = new SegGroupeCritere();
        $model->setGroupe($groupe);
        $model->setIdSeg($seg);
        $model->setPriority($priority);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }
    public function getCritereMultiple(){
        $sql="SELECT id FROM `param_critere` where type='multiple_check' ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAll();
        return $result;
    }
    public function getListeSgementationByGroupe2($id_type,$id_groupe){
        $sql="SELECT * FROM `segmentation` s WHERE s.id and s.type = ".$id_type."  ORDER BY s.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAll();
        $array = [];
        for ($i=0; $i < count($result); $i++) { 
            $array[$i] = $result[$i];
            $sql="SELECT * FROM `status_seg` s WHERE s.id = ".$result[$i]["id_status_id"]." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $status = $stmt->fetchAssociative();
            $array[$i]["status"] = $status;

            $sql="SELECT count(id) FROM `debt_force_seg`.`seg_creance` s WHERE s.id = ".$result[$i]["id"]." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $status = $stmt->fetchOne();
            $array[$i]["nbr_creance"] = $status;
        } 
        return $array;
    }
    public function deleteCritere($id){
        $sql="
        DELETE FROM seg_values WHERE id_critere_id IN (
            SELECT id FROM seg_critere WHERE id_groupe_id IN (
                SELECT id FROM seg_groupe_critere WHERE id_seg_id = :id_seg_id
            )
        );
        
        DELETE FROM seg_critere WHERE id_groupe_id IN (
            SELECT id FROM seg_groupe_critere WHERE id_seg_id = :id_seg_id
        );
        
        DELETE FROM seg_groupe_critere WHERE id_seg_id = :id_seg_id;
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_seg_id",$id);
        $stmt = $stmt->executeQuery();
    }
    public function getValueSegment($id,$entity){
        $table = 'seg_'.$entity.'';
        $sql="SELECT count(id) FROM debt_force_seg.".$table." s WHERE s.id_seg = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchOne();
        return $result;
        
    }
    public function getDetailsSegment($id){
        $sql="SELECT * FROM `segmentation` s WHERE s.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAssociative();
        $array["segmentation"]=$result;

        if($result){
            $sql="SELECT * FROM `type_workflow_segmentation` t WHERE t.id in (select i.id_type_id from interm_workflow_segmentation i where i.id_segmentaion_id = :id)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id",$id);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $array["type"]=$type;

            $sql="SELECT * FROM `type_workflow_segmentation` t WHERE t.id in (select i.id_type_id from interm_workflow_segmentation i where i.id_segmentaion_id = :id and i.id_workflow_id)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id",$id);
            $stmt = $stmt->executeQuery();
            $assigne = $stmt->fetchOne();
            $isExist = $assigne > 0;
            $array["assigne"]=$isExist;

            $sql="SELECT * FROM `status_seg` s WHERE s.id = ".$result["id_status_id"]." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $status = $stmt->fetchAssociative();
            $array["status"] = $status;

            $sql="SELECT count(id) FROM `debt_force_seg`.`seg_creance` s WHERE s.id = ".$result["id"]." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $status = $stmt->fetchOne();
            $array["nbr_creance"] = $status;

            $sql="SELECT count(id) FROM `segmentation` s WHERE s.id = ".$result["id"]." and s.id in (select q.id_segmentation_id from queue q)";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $count = $stmt->fetchOne();
            $isExist = $count > 0;
            $array["assigneToQueue"] = $isExist;
        }
        return $array;
    }
    
    public function createSegValues($value1 ,$value2 ,$id_critere,$action,$value_view){
        $sql="INSERT INTO `seg_values`( `value1`, `value2`, `id_critere_id`,`action`,`value_view`) VALUES (:value1,:value2,:id_critere,:action,:value_view)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":value1",$value1);
        $stmt->bindValue(":value2",$value2);
        $stmt->bindValue(":id_critere",$id_critere);
        $stmt->bindValue(":action",$action);
        $stmt->bindValue(":value_view",$value_view);
        
        $stmt = $stmt->executeQuery();   
        return $stmt;     
    }
    public function findSegByTitre($titre){
        $sql="Select * from segmentation where nom_segment = :titre";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":titre",$titre);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        return $data;
    }
    public function getTypeWorkflowSeg(){
        $sql="Select * from type_workflow_segmentation ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchAll();
        return $data;
    }
    public function getCritereSegmentation($id){
        $sql2 = "select * from seg_groupe_critere where id_seg_id = :id ORDER BY priority ASC";
        $stmt = $this->conn->prepare($sql2);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $liste_groupe = $stmt->fetchAll();
        $array_data = [];
        for ($i=0; $i < count($liste_groupe); $i++) { 
            $array_data[$i] = $liste_groupe[$i];
            $groupID = $liste_groupe[$i]["id"];
            $sql2 = "select * from seg_critere where id_groupe_id = " . $groupID;
            $stmt = $this->conn->prepare($sql2);
            $stmt = $stmt->executeQuery();
            $criteria = $stmt->fetchAll();
            $array_data[$i]["criteres"] = $criteria;
            for ($j=0; $j < count($array_data[$i]["criteres"]); $j++) { 
                $critereId = $array_data[$i]["criteres"][$j]["id"];
                $sql2 = "select * from seg_values where id_critere_id = " . $critereId;
                $stmt = $this->conn->prepare($sql2);
                $stmt = $stmt->executeQuery();
                $details = $stmt->fetchAll();
                $array_data[$i]["criteres"][$j]["details"] = $details;
            }
        }
        return $array_data;
    }
    public function getTypeDetailsCreance($id){
        $sql="SELECT * FROM `details_values_critere` where id_parent_type_creance = ".$id."";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getTypeFamilles($id){
        $sql="SELECT * FROM `details_values_critere`  WHERE id_critere_id = 38  and id_champ in (select t.id from param_activite t where t.id_branche_id = ".$id." and t.activite_p is null);";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getQualification($idActivity){
        $sql="SELECT * FROM `details_values_critere`  WHERE id_critere_id = 38  and id_champ in (select t.id from param_activite t where  t.activite_p = ".$idActivity.");";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    
    public function getDetailsSecteurActiviteInPramas($id){
        $sql="SELECT * FROM `details_values_critere` where id_parent_secteur_activite = ".$id."";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getValuesSelectedInSegment($id){
        $array = array();
        $sql="SELECT * FROM seg_values v WHERE v.id_critere_id in (select s.id from seg_critere s WHERE s.id_groupe_id in (SELECT g.id from seg_groupe_critere g WHERE g.id_seg_id = ".$id."));";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        for ($i=0; $i < count($resulat); $i++) { 
            # code...
            $array[$i]=$resulat[$i];

            $sql="SELECT * FROM seg_critere v WHERE v.id = ".$resulat[$i]["id_critere_id"]."";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $res= $stmt->fetchAssociative();
            $array[$i]["critere"]=$res;
        }
        return $array;
    }
    public function getMaxSEG(){
        $array = array();
        $sql="SELECT d.id FROM segmentation d";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchOne();
        if($resulat){
            return $resulat;
        }
        return 0;
    }
    

}