<?php

namespace App\Repository\Sgementaion;
use App\Entity\CritereParentSeg;
use App\Entity\CritereSegment;
use App\Entity\DetailCritereSegment;
use App\Entity\DetailsSeg;
use App\Entity\GroupeCritere;
use App\Entity\IntermGroupeCritere;
use App\Entity\Queue;
use App\Entity\QueueSplit;
use App\Entity\SegCritere;
use App\Entity\Segmentation;
use App\Entity\SegGroupeCritere;
use App\Entity\SegValues;
use App\Entity\SplitCritere;
use App\Entity\SplitGroupeCritere;
use App\Entity\StatusQueue;
use App\Entity\StatusSeg;
use App\Entity\TypeQueue;
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
    public function getListeSgementById($id){
        $sql="SELECT * FROM `segmentation` s WHERE s.id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchAll();
        return $statut;
    }

    public function getListeSegmentation(){
        $sql="SELECT * FROM `segmentation` s";
        $stmt = $this->conn->prepare($sql);
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
        $sql="SELECT MAX(d.id) FROM segmentation d";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchOne();
        if($resulat){
            return $resulat;
        }
        return 0;
    }
    
    public function createGroupeCritereRepoSplit($groupe , $id_seg , $priority){
        $seg = $this->em->getRepository(QueueSplit::class)->findOneBy(["id"=>$id_seg]);
        $model = new SplitGroupeCritere();
        $model->setGroupe($groupe);
        $model->setIdQueueSplit($seg);
        // $model->setPriority($priority);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }
    public function createCritereSplit($critere , $id_groupe , $type){
        $model = new SplitCritere();
        $model->setCritere($critere);
        $model->setIdGroupe($id_groupe);
        $model->setType($type);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }

    public function createValuesSplit($value1 ,$value2 ,$id_critere,$action,$value_view){
        $sql="INSERT INTO `split_values_critere`( `value1`, `value2`, `id_split_critere_id`,`action`,`value`) VALUES (:value1,:value2,:id_critere,:action,:value_view)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":value1",$value1);
        $stmt->bindValue(":value2",$value2);
        $stmt->bindValue(":id_critere",$id_critere);
        $stmt->bindValue(":action",$action);
        $stmt->bindValue(":value_view",$value_view);
        $stmt = $stmt->executeQuery();   
        return $stmt;     
    }
    public function getRequeteCreance($id , $groupe,$queryEntities,$queryConditions,$param){
        for ($j=0; $j < count($groupe) ; $j++) {
            if(0 == $j)
            {
                $operateur0[$j] =" ";
            }
            else
            {
                $operateur0[$j] = " and ";
            }
            
            if($groupe[$j]['groupe'] == "Creance"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }
                    if($criteres[$k]["critere"] == "type créance"){
                        # code...
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            # code...
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( (c.id_type_creance_id) LIKE :type_creance".$k."_".$i.") ";
                            $param['type_creance'.$k.'_'.$i] = $details[$i]["value1"];
                        }
                    }
                    if($criteres[$k]["critere"] == "date écheance"){
                        # code...
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            # code...
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                            $end = $this->GeneralService->dateEnd($details[$i]["value2"]);

                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                            
                                // Check if it's "supérieur" or "inférieur" and assign the appropriate operator
                                $operator = $details[$i]["action"] == "2" ? ">" : "<";
                                
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.date_echeance $operator :date_echeance" . $k . "_" . $i . ")";
                                $param['date_echeance' . $k . '_' . $i] = $start;
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                            
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." " . $operateur[$k] . " " . $operateur1[$i] . " (c.date_echeance BETWEEN :date_echeance1" . $k . "_" . $i . " AND :date_echeance2" . $k . "_" . $i . ")";
                                $param['date_echeance1' . $k . '_' . $i] = $start;
                                $param['date_echeance2' . $k . '_' . $i] = $end;
                            }
                        }
                    }
                    if($criteres[$k]["critere"] == "total creance"){
                        # code...
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            # code...
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            
                            // $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.total_creance between :total_creance1".$k."_".$i." and :total_creance2".$k."_".$i.") ";
                            // $param['total_creance1'.$k.'_'.$i] = $details[$i]["value1"];
                            // $param['total_creance2'.$k.'_'.$i] = $details[$i]["value2"];

                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." " . $operateur[$k] . " " . $operateur1[$i] . " (c.total_creance " . ($details[$i]["action"] == "2" ? ">" : "<") . " :total_creance" . $k . "_" . $i . ")";
                                $param['total_creance' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.total_creance BETWEEN :total_creance1" . $k . "_" . $i . " AND :total_creance2" . $k . "_" . $i . ")";
                                $param['total_creance1' . $k . '_' . $i] = $details[$i]["value1"];
                                $param['total_creance2' . $k . '_' . $i] = $details[$i]["value2"];
                            }
                        }
                    }
                    if($criteres[$k]["critere"] == "total restant"){
                        # code...
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            # code...
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }

                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.total_restant " . ($details[$i]["action"] == "2" ? ">" : "<") . " :total_restant" . $k . "_" . $i . ")";
                                $param['total_restant' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.total_restant BETWEEN :total_restant1" . $k . "_" . $i . " AND :total_restant2" . $k . "_" . $i . ")";
                                $param['total_restant1' . $k . '_' . $i] = $details[$i]["value1"];
                                $param['total_restant2' . $k . '_' . $i] = $details[$i]["value2"];
                            }
                        }
                    }
                    
                }
            }
            if($groupe[$j]['groupe'] == "Garantie"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }
                    if($criteres[$k]["critere"] == "type garantie"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_garantie_creance gc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_garantie_creance gc";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_garantie g") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_garantie g";
                            } 

                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = (gc.id_creance_id) and (gc.id_garantie_id) = g.id and  g.type_garantie LIKE :type_garantie".$k."_".$i.") ";
                            $param['type_garantie'.$k.'_'.$i] = $details[$i]["value1"]; 
                        }
                    }
                    if($criteres[$k]["critere"] == "Taux"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_garantie_creance gc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_garantie_creance gc";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_garantie g") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_garantie g";
                            }

                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ") ." " . $operateur[$k] . " " . $operateur1[$i] . " ( c.id = (gc.id_creance_id) and (gc.id_garantie_id) = g.id and  g.taux " . ($details[$i]["action"] == "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (gc.id_creance_id) and (gc.id_garantie_id) = g.id and  g.taux BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                            }
                        }
                    }
                    
                }
            }
            if($groupe[$j]['groupe'] == "Donneur ordre"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }
                    if($criteres[$k]["critere"] == "type donneur ordre"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_portefeuille p") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_portefeuille p";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_ponneur_Ordre dn") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_donneur_Ordre dn";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." ".$operateur[$k]." ".$operateur1[$i]." (  p.id = (c.id_ptf_id) and (p.id_donneur_ordre_id) = dn.id and  (dn.id_type_id) = :type_donneur".$k."_".$i.") ";
                            $param['type_donneur'.$k.'_'.$i] = $details[$i]["value1"];
                        }
                    }
                }
            }
            if($groupe[$j]['groupe'] == "Porte feuille"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }

                    if($criteres[$k]["critere"] == "Date début gestion"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_portefeuille p") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_portefeuille p";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_donneur_Ordre dn") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_donneur_Ordre dn";
                            }
                            
                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                            
                                // Check if it's "supérieur" or "inférieur" and assign the appropriate operator
                                $operator = $details[$i]["action"] == "2" ? ">" : "<";
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (   p.id = (c.id_ptf_id) and p.date_debut_gestion $operator :date_debut_gestion" . $k . "_" . $i . ")";
                                $param['date_debut_gestion' . $k . '_' . $i] = $start;

                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (    p.id = (c.id_ptf_id) and p.date_debut_gestion BETWEEN :date_debut_gestion1" . $k . "_" . $i . " AND :date_debut_gestion2" . $k . "_" . $i . ")";
                                $param['date_debut_gestion1' . $k . '_' . $i] = $start;
                                $param['date_debut_gestion2' . $k . '_' . $i] = $end;
                            }
                        }
                    }
                    if($criteres[$k]["critere"] == "Date fin gestion"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_portefeuille p") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_portefeuille p";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_donneur_Ordre dn") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_donneur_Ordre dn";
                            }

                            // $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                            // $end = $this->GeneralService->dateEnd($details[$i]["value2"]);

                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                            
                                // Check if it's "supérieur" or "inférieur" and assign the appropriate operator
                                $operator = $details[$i]["action"] == "2" ? ">" : "<";
                                
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( p.id = (d.id_ptf_id) and p.date_fin_gestion $operator :date_fin_gestion" . $k . "_" . $i . ")";
                                $param['date_fin_gestion' . $k . '_' . $i] = $start;

                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                            
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (  p.id = (d.id_ptf_id) and p.date_fin_gestion BETWEEN :date_fin_gestion1" . $k . "_" . $i . " AND :date_fin_gestion2" . $k . "_" . $i . ")";
                                $param['date_fin_gestion1' . $k . '_' . $i] = $start;
                                $param['date_fin_gestion2' . $k . '_' . $i] = $end;
                            }
                        }
                    }
                }
            }
            if($groupe[$j]['groupe'] == "Detail créance"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }
                    if($criteres[$k]["critere"] == "principale"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_detail_Creance dc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_detail_Creance dc";
                            }
                            /*$queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(dc.id_creance) and   dc.principale between :VALUE1".$k."_".$i." and :VALUE2".$k."_".$i.") ";
                            $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                            $param['VALUE2'.$k.'_'.$i] = $details[$i]["value2"];*/
                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (dc.id_creance_id) AND dc.principale " . ($details[$i]["action"] == "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (dc.id_creance_id) AND dc.principale BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                            }
                        }
                    }
                    if($criteres[$k]["critere"] == "frais"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_detail_Creance dc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_detail_Creance dc";
                            }
                            // $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(dc.id_creance) and   dc.frais between :VALUE1".$k."_".$i." and :VALUE2".$k."_".$i.") ";
                            // $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                            // $param['VALUE2'.$k.'_'.$i] = $details[$i]["value2"];

                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (dc.id_creance_id) AND dc.frais " . ($details[$i]["action"] == "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (dc.id_creance_id) AND dc.frais BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                            }
                        }
                    }
                    if($criteres[$k]["critere"] == "interet"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_detail_creance dc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_detail_creance dc";
                            }
                            // $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(dc.id_creance) and   dc.interet between :VALUE1".$k."_".$i." and :VALUE2".$k."_".$i.") ";
                            // $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                            // $param['VALUE2'.$k.'_'.$i] = $details[$i]["value2"];
                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (dc.id_creance_id) AND dc.interet " . ($details[$i]["action"] == "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (dc.id_creance_id) AND dc.interet BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                            }
                        }
                    }
                }
            }
            if($groupe[$j]['groupe'] == "Téléphone"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }
                    if($criteres[$k]["critere"] == "Type téléphone"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_type_debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_type_debiteur t";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Telephone tel") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Telephone tel";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (t.id_creance_id)  and (tel.id_debiteur)=deb.id  and  (tel.id_type_tel_id) like :typeTel".$k."_".$i." ) ";
                            $param['typeTel'.$k.'_'.$i] = $details[$i]["value1"];
                        }
                    }
                    if($criteres[$k]["critere"] == "Status téléphone"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_type_debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_type_debiteur t";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Telephone tel") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Telephone tel";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (t.id_creance_id)  and (tel.id_debiteur)=deb.id  and  (tel.id_status_id) like :statusTel".$k."_".$i." ) ";
                            $param['statusTel'.$k.'_'.$i] = $details[$i]["value1"];
                        }
                    }
                }
            }
            if($groupe[$j]['groupe'] == "Adresse"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }
                    if($criteres[$k]["critere"] == "Type adresse"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_type_debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_type_debiteur t";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Adresse ad") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Adresse ad";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (t.id_creance_id)  and (ad.id_debiteur)=deb.id  and  (ad.id_type_adresse_id) like :typeAdresse".$k."_".$i." ) ";
                            $param['typeAdresse'.$k.'_'.$i] = $details[$i]["value1"];
                        }
                    }
                    if($criteres[$k]["critere"] == "Status adresse"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_type_debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_type_debiteur t";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Adresse ad") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Adresse ad";
                            }
                            $queryConditions .=  (0 == $k ? $operateur0[$j] : " ")." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (t.id_creance_id)  and (ad.id_debiteur)=deb.id  and  (ad.id_status_id) like :statusAdr".$k."_".$i." ) ";
                            $param['statusAdr'.$k.'_'.$i] = $details[$i]["value1"];
                        }
                    }
                }
            }
            if($groupe[$j]['groupe'] == "Débiteur"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }
                    if($criteres[$k]["critere"] == "Personne"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_type_debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_type_debiteur t";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (t.id_creance_id)  and  deb.type_personne like :VALUE1".$k."_".$i." ) ";
                            $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                        }
                    }
                    if($criteres[$k]["critere"] == "Type débiteur"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_type_debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_type_debiteur t";
                            }

                            $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                            $end = $this->GeneralService->dateEnd($details[$i]["value2"]);

                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (t.id_creance_id)   and  (t.id_type_id) like :VALUE1".$k."_".$i." ) ";
                            $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                        }
                    }
                }
            }

            if($groupe[$j]['groupe'] == "Procédure judiciaire"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }
                    if($criteres[$k]["critere"] == "Type procédure"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Proc_Creance pc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Proc_Creance pc";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Proc_Judicaire pj") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Proc_Judicaire pj";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (pc.id_creance_id) and (pc.id_proc_id) = pj.id and  pj.type_proc_judicaire LIKE :type_proc_judicaire".$k."_".$i.") ";
                            $param['type_proc_judicaire'.$k.'_'.$i] = $details[$i]["value1"]; 
                        }
                    }
                }
            }

            if($groupe[$j]['groupe'] == "Emploi"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }
                    if($criteres[$k]["critere"] == "Status emploi"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_type_debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_type_debiteur t";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Emploi em") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Emploi em";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (t.id_creance_id) and t.id_debiteur = (em.id_debiteur_id) and (em.id_status_id) like :status_emploi".$k."_".$i." ) ";
                            $param['status_emploi'.$k.'_'.$i] = $details[$i]["value1"]; 
                        }
                    }
                    if($criteres[$k]["critere"] == "Date début"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_type_debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_type_debiteur t";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Emploi em") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Emploi em";
                            }

                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (t.id_creance_id) AND t.id_debiteur = (em.id_debiteur_id) AND (em.id_status_id) AND em.dateDebut " . ($details[$i]["action"] == "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (t.id_creance_id) AND t.id_debiteur = (em.id_debiteur_id) AND (em.id_status_id) AND em.dateDebut BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                            }
                            
                        }
                    }

                    if($criteres[$k]["critere"] == "Date fin"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_type_debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_type_debiteur t";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Emploi em") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Emploi em";
                            }

                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (t.id_creance_id) AND t.id_debiteur = (em.id_debiteur_id) AND (em.id_status_id) AND em.dateFin " . ($details[$i]["action"] == "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (t.id_creance_id) AND t.id_debiteur = (em.id_debiteur_id) AND (em.id_status_id) AND em.dateFin BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                            }
                        }
                    }
                }
            }

            if($groupe[$j]['groupe'] == "Employeur"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }
                    if($criteres[$k]["critere"] == "Status employeur"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_type_debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_type_debiteur t";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Employeur emp") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Employeur emp";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (t.id_creance_id) and t.id_debiteur = (emp.id_debiteur_id) and (emp.id_status_id) like :status_employeur".$k."_".$i." ) ";
                            $param['status_employeur'.$k.'_'.$i] = $details[$i]["value1"]; 
                        }
                    }
                    
                }
            }
            if($groupe[$j]['groupe'] == "Accord"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }
                    if($criteres[$k]["critere"] == "Status accord"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Accord ac") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Accord ac";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Creance_Accord ca") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Creance_Accord ca";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = (ca.id_creance_id) and (ac.id_status_id) like :status_accord".$k."_".$i." ) ";
                            $param['status_accord'.$k.'_'.$i] = $details[$i]["value1"]; 
                        }
                    }
                    if($criteres[$k]["critere"] == "Date création"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Accord ac") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Accord ac";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Creance_Accord ca") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Creance_Accord ca";
                            }

                            $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (ca.id_creance_id) AND ac.dateCreation " . ($details[$i]["action"] == "2" ? ">" : "<") . " :dateStart_" . $k . "_" . $i . ")";
                                $param['dateStart_' . $k . '_' . $i] = $start;
                            } elseif ($details[$i]["action"] == "1") {
                                $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (ca.id_creance_id) AND ac.dateCreation BETWEEN :dateStart_" . $k . "_" . $i . " AND :dateFin_" . $k . "_" . $i . ")";
                                $param['dateStart_' . $k . '_' . $i] = $start;
                                $param['dateFin_' . $k . '_' . $i] = $end;
                            }
                        }
                    }

                    if($criteres[$k]["critere"] == "Montant"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Accord ac") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Accord ac";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Creance_Accord ca") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Creance_Accord ca";
                            }

                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (ca.id_creance_id) AND ac.montant " . ($details[$i]["action"] == "2" ? ">" : "<") . " :valueMontant1_" . $k . "_" . $i . ")";
                                $param['valueMontant1_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (ca.id_creance_id) AND ac.montant BETWEEN :valueMontant1_" . $k . "_" . $i . " AND :valueMontant2_" . $k . "_" . $i . ")";
                                $param['valueMontant1_' . $k . '_' . $i] =  $details[$i]["value1"];
                                $param['valueMontant2_' . $k . '_' . $i] =  $details[$i]["value2"];
                            }
                        }
                    }

                    if($criteres[$k]["critere"] == "Montant à payer"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Accord ac") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Accord ac";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Creance_Accord ca") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Creance_Accord ca";
                            }

                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (ca.id_creance_id) AND ac.montant_a_payer " . ($details[$i]["action"] == "2" ? ">" : "<") . " :valueMontant1_" . $k . "_" . $i . ")";
                                $param['valueMontant1_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (ca.id_creance_id) AND ac.montant_a_payer BETWEEN :valueMontant1_" . $k . "_" . $i . " AND :valueMontant2_" . $k . "_" . $i . ")";
                                $param['valueMontant1_' . $k . '_' . $i] =  $details[$i]["value1"];
                                $param['valueMontant2_' . $k . '_' . $i] =  $details[$i]["value2"];
                            }
                        }
                    }
                }
            }
            if($groupe[$j]['groupe'] == "Paiement"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }
                    if($criteres[$k]["critere"] == "Type paiement"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Accord ac") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Accord ac";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Paiement pm") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Paiement pm";
                            }
                            
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = (pm.id_creance_id) and (pm.id_type_paiement_id) like :typeP".$k."_".$i." ) ";
                            $param['typeP'.$k.'_'.$i] = $details[$i]["value1"]; 
                        }
                    }

                    if($criteres[$k]["critere"] == "Date paiement"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Accord ac") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Accord ac";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Paiement pm") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Paiement pm";
                            }
                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (pm.id_creance_id) AND pm.date_paiement " . ($details[$i]["action"] == "2" ? ">" : "<") . " :dateStart_" . $k . "_" . $i . ")";
                                $param['dateStart_' . $k . '_' . $i] = $start;
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (pm.id_creance_id) AND pm.date_paiement BETWEEN :dateStart_" . $k . "_" . $i . " AND :dateFin_" . $k . "_" . $i . ")";
                                $param['dateStart_' . $k . '_' . $i] = $start;
                                $param['dateFin_' . $k . '_' . $i] = $end;
                            }
                        }
                    }
                    
                    if($criteres[$k]["critere"] == "Montant de paiment"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Accord ac") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Accord ac";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Paiement pm") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Paiement pm";
                            }
                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (pm.id_creance_id) AND pm.montant " . ($details[$i]["action"] == "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                $param['dateStart_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (pm.id_creance_id) AND pm.montant BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] =  $details[$i]["value1"];
                                $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                            }
                        }
                    }
                }
            }
            if($groupe[$j]['groupe'] == "Dossier"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }
                    if($criteres[$k]["critere"] == "Qualification dossier"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Dossier dss") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Dossier dss";
                            }
                            
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  dss.id = (c.id_dossier_id)  and  (dss.id_qualification_id) = :qualification".$k."_".$i.") ";
                            $param['qualification'.$k.'_'.$i] = $details[$i]["value1"]; 
                        }
                    }
                }
            }
            if($groupe[$j]['groupe'] == "Activités"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }
                    if($criteres[$k]["critere"] == "Familles d'activités"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Param_Critere pc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Param_Critere pc";
                            }
                            
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( (c.id_activite_id) = :activite".$k."_".$i.") ";
                            $param['activite'.$k.'_'.$i] = $details[$i]["value1"]; 
                        }
                    }
                }
            }
            if($groupe[$j]['groupe'] == "Facture"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }   
                    if($criteres[$k]["critere"] == "Année"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Facture f") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Facture f";
                            }
                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $start = $this->GeneralService->yearStart($details[$i]["value1"]);
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (f.id_creance_id) AND f.date_creation " . ($details[$i]["action"] == "2" ? ">" : "<") . " :dateStart_" . $k . "_" . $i . ")";
                                $param['dateStart_' . $k . '_' . $i] = $start;
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $start = $this->GeneralService->yearStart($details[$i]["value1"]);
                                $end = $this->GeneralService->yearEnd($details[$i]["value2"]);
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (f.id_creance_id) AND f.date_creation BETWEEN :dateStart_" . $k . "_" . $i . " AND :dateFin_" . $k . "_" . $i . ")";
                                $param['dateStart_' . $k . '_' . $i] = $start;
                                $param['dateFin_' . $k . '_' . $i] = $end;
                            }
                        }
                    }
                    if($criteres[$k]["critere"] == "Total TTC"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Facture f") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Facture f";
                            }
                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $montant1 = $details[$i]["value1"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (f.id_creance_id) AND f.total_ttc " . ($details[$i]["action"] == "2" ? ">" : "<") . " :totalTtc_" . $k . "_" . $i . ")";
                                $param['totalTtc_' . $k . '_' . $i] = $montant1;
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $montant1 =  $details[$i]["value1"];
                                $montant2 =  $details[$i]["value2"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (f.id_creance_id) AND f.total_ttc BETWEEN :totalTtc_1" . $k . "_" . $i . " AND :totalTtc_2" . $k . "_" . $i . ")";
                                $param['totalTtc_1' . $k . '_' . $i] = $montant1;
                                $param['totalTtc_2' . $k . '_' . $i] = $montant2;
                            }
                        }
                    }
                    if($criteres[$k]["critere"] == "Type paiement"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Facture f") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Facture f";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = (f.id_creance_id) AND (f.id_type_paiement_id) like :typeP".$k."_".$i." ) ";
                            $param['typeP'.$k.'_'.$i] = $details[$i]["value1"];  
                        }
                    }
                    if($criteres[$k]["critere"] == "Status"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Facture f") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Facture f";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = (f.id_creance_id) AND (f.id_status_id) like :statusP".$k."_".$i." ) ";
                            $param['statusP'.$k.'_'.$i] = $details[$i]["value1"];  
                        }
                    }
                    if($criteres[$k]["critere"] == "Taux_honoraire"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Facture f") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Facture f";
                            }
                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $montant1 = $details[$i]["value1"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( c.taux_honoraire " . ($details[$i]["action"] == "2" ? ">" : "<") . " :taux_honoraire_" . $k . "_" . $i . ")";
                                $param['taux_honoraire_' . $k . '_' . $i] = $montant1;
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $montant1 =  $details[$i]["value1"];
                                $montant2 =  $details[$i]["value2"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( c.taux_honoraire BETWEEN :totalTtc_1" . $k . "_" . $i . " AND :totalTtc_2" . $k . "_" . $i . ")";
                                $param['totalTtc_1' . $k . '_' . $i] = $montant1;
                                $param['totalTtc_2' . $k . '_' . $i] = $montant2;
                            }
                        }
                    }

                    if($criteres[$k]["critere"] == "Honoraire_petentiel"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Facture f") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Facture f";
                            }
                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $montant1 = $details[$i]["value1"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (  c.honoraire_petentiel " . ($details[$i]["action"] == "2" ? ">" : "<") . " :honoraire_" . $k . "_" . $i . ")";
                                $param['honoraire_' . $k . '_' . $i] = $montant1;
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $montant1 =  $details[$i]["value1"];
                                $montant2 =  $details[$i]["value2"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (  c.honoraire_petentiel BETWEEN :honoraire__1" . $k . "_" . $i . " AND :honoraire__2" . $k . "_" . $i . ")";
                                $param['honoraire__1' . $k . '_' . $i] = $montant1;
                                $param['honoraire__2' . $k . '_' . $i] = $montant2;
                            }
                        }
                    }
                    if($criteres[$k]["critere"] == "Honoraire_facturé"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Facture f") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Facture f";
                            }
                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $montant1 = $details[$i]["value1"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( c.id = (f.id_creance_id) AND c.honoraire_facture " . ($details[$i]["action"] == "2" ? ">" : "<") . " :honoraire_" . $k . "_" . $i . ")";
                                $param['honoraire_' . $k . '_' . $i] = $montant1;
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $montant1 =  $details[$i]["value1"];
                                $montant2 =  $details[$i]["value2"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( c.id = (f.id_creance_id) AND c.honoraire_facture BETWEEN :honoraire__1" . $k . "_" . $i . " AND :honoraire__2" . $k . "_" . $i . ")";
                                $param['honoraire__1' . $k . '_' . $i] = $montant1;
                                $param['honoraire__2' . $k . '_' . $i] = $montant2;
                            }
                        }
                    }
                    if($criteres[$k]["critere"] == "Honoraire_petentiel_restant"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Facture f") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Facture f";
                            }
                            if ($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $montant1 = $details[$i]["value1"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (  c.honoraire_restant " . ($details[$i]["action"] == "2" ? ">" : "<") . " :honoraire_" . $k . "_" . $i . ")";
                                $param['honoraire_' . $k . '_' . $i] = $montant1;
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $montant1 =  $details[$i]["value1"];
                                $montant2 =  $details[$i]["value2"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (  c.honoraire_restant BETWEEN :honoraire__1" . $k . "_" . $i . " AND :honoraire__2" . $k . "_" . $i . ")";
                                $param['honoraire__1' . $k . '_' . $i] = $montant1;
                                $param['honoraire__2' . $k . '_' . $i] = $montant2;
                            }
                        }
                    }

                }
            }
            if($groupe[$j]['groupe'] == "Cadrages"){
                $criteres = $groupe[$j]["criteres"];
                for ($k= 0; $k < count($criteres);$k++){
                    if($k==0)
                    {
                        $operateur[$k]="";
                    }
                    else
                    {
                        $operateur[$k]=" and ";
                    }  
                    if($criteres[$k]["critere"] == "Type de cadrages"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Cadrages cd") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Cadrages cd";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Cadrages_Creance cc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Cadrages_Creance cc";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = (cc.id_creance_id) AND  cd.id = (cc.id_cadrage_id) AND cd.type like :typeCad".$k."_".$i." ) ";
                            $param['typeCad'.$k.'_'.$i] = $details[$i]["value1"];  

                        }
                    }
                    if($criteres[$k]["critere"] == "Date de retour"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Cadrages cd") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Cadrages cd";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Cadrages_Creance cc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Cadrages_Creance cc";
                            }
                            if($details[$i]["action"] == "2" || $details[$i]["action"] == "3") {
                                // If "supérieur" or "inférieur"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (cc.id_creance_id) AND  cd.id = (cc.id_cadrage_id) AND cd.date_retour " . ($details[$i]["action"] == "2" ? ">" : "<") . " :dateRStart_" . $k . "_" . $i . ")";
                                $param['dateRStart_' . $k . '_' . $i] = $start;
                            } elseif ($details[$i]["action"] == "1") {
                                // If "between"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (cc.id_creance_id) AND  cd.id = (cc.id_cadrage_id) AND cd.date_retour BETWEEN :dateRStart_" . $k . "_" . $i . " AND :dateRFin_" . $k . "_" . $i . ")";
                                $param['dateRStart_' . $k . '_' . $i] = $start;
                                $param['dateRFin_' . $k . '_' . $i] = $end;
                            }
                        }
                    }
                    if($criteres[$k]["critere"] == "Status de cadrages"){
                        $details = $criteres[$k]["details"];
                        for ($i=0; $i < count($details ); $i++) { 
                            if($i==0)
                            {
                                $operateur1[$i]="";
                            }
                            else
                            {
                                $operateur1[$i]=" or ";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Cadrages cd") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Cadrages cd";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Cadrages_Creance cc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Cadrages_Creance cc";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = (cc.id_creance_id) AND  cd.id = (cc.id_cadrage_id) AND cd.etat like :etatCad".$k."_".$i." ) ";
                            $param['etatCad'.$k.'_'.$i] = $details[$i]["value1"];  
                        }
                    }
                    
                   
                }
            }
        }
        return ["queryConditions"=>$queryConditions ,"queryEntities"=>$queryEntities , 'param'=>$param ];
    }
    
    public function deleteQueue($idQueue){
        $sql="
            -- Replace :queue_id with the actual queue ID you want to delete

            -- Step 1: Delete from queue_values
            DELETE qv FROM `queue_values` qv
            JOIN `queue_critere` qc ON qv.id_critere_id = qc.id
            JOIN `queue_groupe_critere` qgc ON qc.id_groupe_id = qgc.id
            JOIN `queue` q ON qgc.id_queue_id = q.id
            WHERE q.id = :queue_id;

            -- Step 2: Delete from queue_critere
            DELETE qc FROM `queue_critere` qc
            JOIN `queue_groupe_critere` qgc ON qc.id_groupe_id = qgc.id
            JOIN `queue` q ON qgc.id_queue_id = q.id
            WHERE q.id = :queue_id;

            -- Step 3: Delete from queue_groupe_critere
            DELETE qgc FROM `queue_groupe_critere` qgc
            JOIN `queue` q ON qgc.id_queue_id = q.id
            WHERE q.id = :queue_id;

            -- Step 4: Delete from queue
            DELETE FROM `queue` WHERE id = :queue_id;";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":queue_id",$idQueue);
        $stmt = $stmt->executeQuery();
    }
    public function deleteSegmentation($id){
    
        $listQueue = $this->em->getRepository(Queue::class)->findBy(['id_segmentation' => $id]);

        foreach ($listQueue as $q) {
            $this->deleteQueue($q->getId());  
        }

        $sql = "
        DELETE seg_values
        FROM seg_values
        INNER JOIN seg_critere ON seg_values.id_critere_id = seg_critere.id
        INNER JOIN seg_groupe_critere ON seg_critere.id_groupe_id = seg_groupe_critere.id
        WHERE seg_groupe_critere.id_seg_id = :id;
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);  
        $stmt->execute();

        $sql = "
        DELETE seg_critere
        FROM seg_critere
        INNER JOIN seg_groupe_critere ON seg_critere.id_groupe_id = seg_groupe_critere.id
        WHERE seg_groupe_critere.id_seg_id = :id;
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);  
        $stmt->execute();
        $sql = "
        DELETE FROM seg_groupe_critere
        WHERE id_seg_id = :id;
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);  
        $stmt->execute();
        $sql = "
        DELETE FROM interm_workflow_segmentation WHERE id_segmentaion_id = :id;
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);  
        $stmt->execute();
        $sql = "
        DELETE FROM segmentation
        WHERE id = :id;
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);  
        $stmt->execute();
    }
    
    public function getListSegment(){
        $sql="SELECT * FROM `segmentation` s ";
        $stmt = $this->conn->prepare($sql);
        // $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchAll();
        return $statut;
    }
    
    public function getTypesQueue($type){
        $resultList = $this->em->getRepository(TypeQueue::class)->find($type);
        return $resultList;
    }
    public function getStatusQueue($status){
        $resultList = $this->em->getRepository(StatusQueue::class)->find($status);
        return $resultList;
    }
    public function copyQueue(Segmentation $seg){

        $type = $this->getTypesQueue(1);
        $getStatusQueue = $this->getStatusQueue(1);
        
        $model = new Queue();
        $model->setTitre('Seg-'.$seg->getCleIdentifiant());
        $model->setDescription('');
        $model->setIdSegmentation($seg);
        $model->setIdType($type);
        $model->setIdStatus($getStatusQueue);
        $model->setDateCreation(new \DateTime("now"));
        $model->setActive(true);
        $model->setPriority(0);
        $model->setAssignedStrategy(false);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }
    public function copyDataQueue($idSeg , $idQueue){
        //Adresse
        $sql = '
        INSERT INTO debt_force_seg.queue_adresse (id_seg, id_adresse, id_queue)
        SELECT id_seg, id_adresse, "'.$idQueue.'"
        FROM debt_force_seg.seg_adresse
        WHERE id_seg = "'.$idSeg.'"';
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        //Tel
        $sql = '
        INSERT INTO debt_force_seg.queue_telephone (id_seg, id_telephone, id_queue)
        SELECT id_seg, id_telephone, "'.$idQueue.'"
        FROM debt_force_seg.seg_telephone
        WHERE id_seg = "'.$idSeg.'"';
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();

        //Tel
        $sql = '
        INSERT INTO debt_force_seg.queue_dossier (id_seg, id_dossier, id_queue)
        SELECT id_seg, id_dossier, "'.$idQueue.'"
        FROM debt_force_seg.seg_dossier
        WHERE id_seg = "'.$idSeg.'"';
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();

        //Tel
        $sql = '
        INSERT INTO debt_force_seg.queue_debiteur (id_seg, id_debiteur, id_queue)
        SELECT id_seg, id_debiteur, "'.$idQueue.'"
        FROM debt_force_seg.seg_debiteur
        WHERE id_seg = "'.$idSeg.'"';
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();

        //Tel
        $sql = '
        INSERT INTO debt_force_seg.queue_creance (id_seg, id_creance, id_queue)
        SELECT id_seg, id_creance, "'.$idQueue.'"
        FROM debt_force_seg.seg_creance
        WHERE id_seg = "'.$idSeg.'"';
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
    }

    public function getSegmentation($id){
        $resultList = $this->em->getRepository(Segmentation::class)->find($id);
        return $resultList;
    }

    public function clearSegmentation($id){
        $sql = '
       DELETE FROM `debt_force_seg`.`seg_adresse` WHERE id_seg = '.$id.';
        DELETE FROM `debt_force_seg`.`seg_creance` WHERE id_seg = '.$id.';
        DELETE FROM `debt_force_seg`.`seg_debiteur` WHERE id_seg = '.$id.';
        DELETE FROM `debt_force_seg`.`seg_dossier` WHERE id_seg = '.$id.';
        DELETE FROM `debt_force_seg`.`seg_telephone` WHERE id_seg = '.$id.';';
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
    }

    
}