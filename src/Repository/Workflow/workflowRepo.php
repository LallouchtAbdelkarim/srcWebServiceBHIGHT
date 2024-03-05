<?php

namespace App\Repository\Workflow;
use App\Entity\EventBasedDecision;
use App\Entity\EventSelect;
use App\Entity\EventSelectChild;
use App\Entity\IntermWorkflowSegmentation;
use App\Entity\NoteWorkflow;
use App\Entity\ObjectDetail;
use App\Entity\ScenarioObjectMapping;
use App\Entity\Segmentation;
use App\Entity\ObjectWorkflow;
use App\Entity\ObjectConnection;
use App\Entity\Utilisateurs;
use App\Entity\Scenario;
use App\Entity\Workflow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class workflowRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    public function getListeWorkflow(){
        // $resultList = $this->em->getRepository(Workflow::class)->findAll();

        $sql2 = "select * from workflow";
        $stmt = $this->conn->prepare($sql2);
        $stmt = $stmt->executeQuery();
        $resultList = $stmt->fetchAll();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getTypeAgent(){
        $sql2 = "select * from type_agent";
        $stmt = $this->conn->prepare($sql2);
        $stmt = $stmt->executeQuery();
        $resultList = $stmt->fetchAll();
        return $resultList;
    }
    public function getTypeSendCommunication(){
        $sql2 = "select * from type_send_communication";
        $stmt = $this->conn->prepare($sql2);
        $stmt = $stmt->executeQuery();
        $resultList = $stmt->fetchAll();
        return $resultList;
    }
    public function getTypeCampagne(){
        $sql2 = "select * from type_campagne";
        $stmt = $this->conn->prepare($sql2);
        $stmt = $stmt->executeQuery();
        $resultList = $stmt->fetchAll();
        return $resultList;
    }
    public function getModelsCampagne($type){
        $resultList = [];
        if('1' == $type){
            $sql2 = "select * from model_courier";
            $stmt = $this->conn->prepare($sql2);
            $stmt = $stmt->executeQuery();
            $resultList = $stmt->fetchAll();

        }elseif ('2' == $type){
            $sql2 = "select * from model_sms";
            $stmt = $this->conn->prepare($sql2);
            $stmt = $stmt->executeQuery();
            $resultList = $stmt->fetchAll();

        }elseif ('3' == $type){
            $sql2 = "select * from model_email";
            $stmt = $this->conn->prepare($sql2);
            $stmt = $stmt->executeQuery();
            $resultList = $stmt->fetchAll();
        }
        return $resultList;
    }
    public function getTypeApprovalStep(){
        $sql2 = "select * from type_approval_step";
        $stmt = $this->conn->prepare($sql2);
        $stmt = $stmt->executeQuery();
        $resultList = $stmt->fetchAll();
        return $resultList;
    }
    public function getTypeCall(){
        $sql2 = "select * from type_appel";
        $stmt = $this->conn->prepare($sql2);
        $stmt = $stmt->executeQuery();
        $resultList = $stmt->fetchAll();
        return $resultList;
    }
    public function getListeSegmentByType($type){
        $resultList = $this->em->getRepository(Segmentation::class)->findBy(["type"=>$type , "id_status"=>3]);
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getModelByType($type){
        switch ($type) {
            case 'Courrier':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\ModelCourier r ');
                $resultList = $query->getResult();
                if ($resultList) {
                    return $resultList;
                }
                break;
            case 'Email':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\ModelEmail r ');
                $resultList = $query->getResult();
                if ($resultList) {
                    return $resultList;
                }
                break;
            case 'SMS':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\ModelSMS r ');
                $resultList = $query->getResult();
                if ($resultList) {
                    return $resultList;
                }
                break;
            default:
                // If $type doesn't match any expected types, return false
                return null;
        }
        // If none of the cases matched, return false
        return null;
    }
    public function createObject($id_workflow , $id_type , $uid , $id_object){
        $sql = "INSERT INTO `object_workflow`(`id_workflow_id`, `type`, `uid`, `id_object`) VALUES (:id_workflow,:type,:uid,:id_object)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id_workflow', $id_workflow); 
        $stmt->bindParam('type', $id_type); 
        $stmt->bindParam('uid', $uid); 
        $stmt->bindParam('id_object', $id_object); 

        $stmt = $stmt->executeQuery();
        if($stmt){
            return true;
        }else{
            return false;
        }
    }
    public function findOneObject( $uid ){
        $resultList = $this->em->getRepository(ObjectWorkflow::class)->findOneBy(array("Uid"=>$uid));
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function findOneObjectById( $id ){
        $resultList = $this->em->getRepository(ObjectWorkflow::class)->findOneBy(array("id"=>$id));
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function createWorkflow($titre , $user , $type){
        $model = new Workflow();
        $model->setTitre($titre);
        $model->setEtat(0);
        $model->setType($type);
        $model->setIdUser($user);
        $model->setDateCreation(new \DateTime);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }

    public function checkSegmentation($idSeg , $type){
        $sql2 = "SELECT * FROM `interm_workflow_segmentation` WHERE id_segmentaion_id = ".$idSeg." && id_type_id = ".$type." && id_workflow_id is null";
        $stmt = $this->conn->prepare($sql2);
        $stmt = $stmt->executeQuery();
        $resultList = $stmt->fetchAll();
        if($resultList){
            return true;
        }
        return false;
    }
    public function createIntermSegWorkflow($workflow   , $seg){
        $model = new IntermWorkflowSegmentation();
        $model->setIdWorkflow($workflow);
        $model->setIdSegmentaion($seg);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }    
    public function createNoteWorkflow( $id_workflow , $description){
        $model = new NoteWorkflow();
        $model->setNoteWorkflow($description);
        $model->setIdWorkflow($id_workflow);
        $model->setDateCreation(new \DateTime);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }
    
    public function createSenario($id_workflow){
        $model = new Scenario();
        $model->setIdWorkflow($id_workflow);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }
    public function createScenarioMapp($scenario,$object_senario ,$position){
        $model = new ScenarioObjectMapping();
        $model->setIdScenario($scenario);
        $model->setIdObject($object_senario);
        $model->setPosition($position);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }
    public function findSegment($id , $type){
        $groupe = $this->em->getRepository(Segmentation::class)->findOneBy(["id"=>$id , "type"=>$type]);
        if($groupe){
            return $groupe;
        }else{
            return $groupe;
        }
    }
    public function createObjectConnection($uid_from,$uid_to){
        $model = new ObjectConnection();
        $model->setIdFromObject($uid_from);
        $model->setIdToObject($uid_to);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }
    public function findOneUser( $id ){
        $resultList = $this->em->getRepository(Utilisateurs::class)->findOneBy(array("id"=>$id));
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getWorkflow( $id ){
        $resultList = $this->em->getRepository(Workflow::class)->findOneBy(array("id"=>$id));
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getWorkflow2( $id ){
        $sql2 = "select * from workflow where id = ".$id."";
        $stmt = $this->conn->prepare($sql2);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAssociative();
        $sql = "select * from utilisateurs where id = ".$result['id_user_id'];
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $user = $stmt->fetchAssociative();

        $sql = "select * from type_workflow_segmentation where id in (select id_type_id from interm_workflow_segmentation where id_workflow_id =".$result['id'].");";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $interm = $stmt->fetchAssociative();

        $sql = "select * from queue q where q.id_segmentation_id in (select i.id_segmentaion_id from interm_workflow_segmentation i where i.id_workflow_id = ".$result['id'].");";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $queue = $stmt->fetchAll();
        
        $resultList["workflow"] = $result;
        $resultList["user"] = $user;
        $resultList["interm"] = $interm;
        $resultList["queue"] = $queue;

        return $resultList;
    }
    public function getDeailObject( $id ){
        $resultList = $this->em->getRepository(ObjectDetail::class)->findOneBy(array("id"=>$id));
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getListeObjet(){
        $sql2 = "select * from object_workflow";
        $stmt = $this->conn->prepare($sql2);
        $stmt = $stmt->executeQuery();
        $resultList = $stmt->fetchAll();
        return $resultList;
    }
    public function geListeGroupe(){
        $sql2 = "select * from param_groupe_critere   ";
        $stmt = $this->conn->prepare($sql2);
        $stmt = $stmt->executeQuery();
        $liste_groupe = $stmt->fetchAll();

        $array_data = [];
        for ($i=0; $i < count($liste_groupe); $i++) { 
            $array_data[$i] = $liste_groupe[$i];
            $groupID = $liste_groupe[$i]["id"];
                  $sql2 = "select * from param_critere where id_groupe_critere_id = " . $groupID;
            $stmt = $this->conn->prepare($sql2);
            $stmt = $stmt->executeQuery();
            $criteria = $stmt->fetchAll();
            $array_data[$i]["criteres"] = $criteria;
            for ($j=0; $j < count($array_data[$i]["criteres"]); $j++) { 
                $critereId = $array_data[$i]["criteres"][$j]["id"];
                $sql2 = "select * from details_values_critere where id_critere_id = " . $critereId;
                $stmt = $this->conn->prepare($sql2);
                $stmt = $stmt->executeQuery();
                $details = $stmt->fetchAll();
                $array_data[$i]["criteres"][$j]["details"] = $details;
            }
        }
        return $array_data;
    }

    
    public function getListeEvent($id){
        $sql2 = "select * from event_based_decision where id_workflow_id = ".$id."";
        $stmt = $this->conn->prepare($sql2);
        $stmt = $stmt->executeQuery();
        $resultList = $stmt->fetchAll();
        return $resultList;
    }
    public function getDetailListeObjet($id){
        $sql2 = "select * from object_detail where id_object_workflow_id = ".$id." ";
        $stmt = $this->conn->prepare($sql2);
        $stmt = $stmt->executeQuery();
        $resultList = $stmt->fetchAll();
        return $resultList;
        
    }
    public function getListeEventSelect($id){
        $resultList = $this->em->getRepository(EventSelect::class)->findOneBy(array("id_event_based_decision"=>$id));
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getListeEventSelectChild($id){
        $resultList = $this->em->getRepository(EventSelectChild::class)->findBy(array("id_event_based_decision"=>$id));
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    
    public function createEventBased($workflow){
        $entity = new EventBasedDecision();
        $entity->setDateCreation(new \DateTime);
        $entity->setIdWorkflow($workflow);
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }
    public function createEventBasedSelect($id_event_based,$id_detail_check){
        $entity = new EventSelect();
        $entity->setIdDetailCheck($id_detail_check);
        $entity->setIdEventBasedDecision($id_event_based);
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }
    public function createEventBasedCheck($id_event_based,$id_detail_check){
        $entity = new EventSelectChild();
        $entity->setIdDetailCheck($id_detail_check);
        $entity->setIdEventBasedDecision($id_event_based);
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }
}