<?php

namespace App\Repository\Workflow;
use App\Entity\DataWorkflow;
use App\Entity\EvenementWorkflow;
use App\Entity\EventAction;
use App\Entity\EventBasedDecision;
use App\Entity\EventSelect;
use App\Entity\EventSelectChild;
use App\Entity\IntermWorkflowSegmentation;
use App\Entity\NoteWorkflow;
use App\Entity\ObjectDetail;
use App\Entity\QueueEvent;
use App\Entity\QueueSplit;
use App\Entity\ScenarioObjectMapping;
use App\Entity\Segmentation;
use App\Entity\ObjectWorkflow;
use App\Entity\ObjectConnection;
use App\Entity\StatusWorkflow;
use App\Entity\Utilisateurs;
use App\Entity\Scenario;
use App\Entity\Workflow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\StatutQueueEvent;


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
    public function getTypeAssignation(){
        $sql2 = "select * from type_assignation";
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
        $query = $this->em->createQuery('SELECT d FROM App\Entity\Segmentation d WHERE d.id  IN (SELECT IDENTITY(cr.id_segmentaion) FROM App\Entity\IntermWorkflowSegmentation cr WHERE IDENTITY(cr.id_workflow) IS NULL )
        AND d.id in (SELECT IDENTITY(q.id_segmentation) from App\Entity\Queue q where IDENTITY(q.id_status) = 3 )');
        $resultList = $query->getResult();

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
        $statut = $this->em->getRepository(StatusWorkflow::class)->find(2);
        $model = new Workflow();
        $model->setTitre($titre);
        $model->setType($type);
        $model->setIdUser($user);
        $model->setIdStatus($statut);
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
        
        $status = "select * from status_workflow where id = ".$result['id_status_id']."";
        $stmt = $this->conn->prepare($status);
        $stmt = $stmt->executeQuery();
        $resultStatuts = $stmt->fetchAssociative();
        $result['id_status_id'] = $resultStatuts;

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
    public function getWorkflowDetails( $id ){
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
    private function createEventAction($id_workflow , $cle , $idEvent , $name){
        if($name != "Stop" && $name != "Start" ){
            $id_workflow = $this->getWorkflow($id_workflow);
            $entity = new EventAction();
            $entity->setIdWorkflow($id_workflow);
            $entity->setCle($cle);
            $entity->setIdEvent($idEvent);
            $this->em->persist($entity);
            $this->em->flush();
            return $entity;
        }
        return null;
    }
    private function getListeSegmentationByWorkflow($id_workflow ){
        $sql2= "Select * from segmentation s where s.id in (
            SELECT i.id_segmentaion_id FROM `interm_workflow_segmentation` i 
            where i.id_workflow_id =  :id_workflow
        )";
        $stmt = $this->conn->prepare($sql2);
        $stmt->bindParam('id_workflow', $id_workflow); 
        $stmt = $stmt->executeQuery();
        $resultList = $stmt->fetchAll();
        return $resultList;
    }
    
    
    public function saveQueueWorkflow($id , $idEvent){
        //Select queue of workflow
        $sql="select q.id from queue q where q.id_segmentation_id in 
        (select i.id_segmentaion_id from interm_workflow_segmentation i where i.id_workflow_id =:id_workflow) ORDER by q.priority ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id_workflow', $id); 
        $stmt = $stmt->executeQuery();
        $resultList = $stmt->fetchAll();

        for ($i=0; $i < count($resultList); $i++) { 
            $idQueue = $resultList[$i]['id'];
            $sql="select s.entities , s.id from segmentation s where s.id in (select q.id_segmentation_id from queue q where q.id = :idQueue)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('idQueue', $idQueue); 
            $stmt = $stmt->executeQuery();
            $entities = $stmt->fetchAssociative();

            $listeEntities = json_decode($entities['entities']);

            if(in_array('creance',$listeEntities)){
                $persistDetailQueue = "
                    INSERT INTO queue_event (`id_event_action_id`,`id_statut_id`, `id_queue_detail`, `statut_workflow`,`type`) 
                    SELECT 
                        :idEvent , 2 , qc.id , 0 , 1
                    FROM debt_force_seg.queue_creance qc where qc.id_queue = :id_queue";
                $stmt = $this->conn->prepare($persistDetailQueue);
                $stmt->bindParam('id_queue', $idQueue); 
                $stmt->bindParam('idEvent', $idEvent); 
                $stmt = $stmt->executeQuery();
            }

            if(in_array('dossier',$listeEntities)){
                $persistDetailQueue = "
                    INSERT INTO queue_event (`id_event_action_id`,`id_statut_id`, `id_queue_detail`, `statut_workflow`,`type`) 
                    SELECT 
                        :idEvent , 2 , qc.id , 0 , 2
                    FROM debt_force_seg.queue_dossier qc where qc.id_queue = :id_queue";
                $stmt = $this->conn->prepare($persistDetailQueue);
                $stmt->bindParam('id_queue', $idQueue); 
                $stmt->bindParam('idEvent', $idEvent); 
                $stmt = $stmt->executeQuery();
            }

            if(in_array('telephone',$listeEntities)){
                $persistDetailQueue = "
                    INSERT INTO queue_event (`id_event_action_id`,`id_statut_id`, `id_queue_detail`, `statut_workflow`, `type`) 
                    SELECT 
                        :idEvent , 2 , qc.id , 0 , 3
                    FROM debt_force_seg.queue_telephone qc where qc.id_queue = :id_queue";
                $stmt = $this->conn->prepare($persistDetailQueue);
                $stmt->bindParam('id_queue', $idQueue); 
                $stmt->bindParam('idEvent', $idEvent); 
                $stmt = $stmt->executeQuery();
            }

            if(in_array('adresse',$listeEntities)){
                $persistDetailQueue = "
                    INSERT INTO queue_event (`id_event_action_id`,`id_statut_id`, `id_queue_detail`, `statut_workflow`,`type`) 
                    SELECT 
                        :idEvent , 2 , qc.id , 0 , 4
                    FROM debt_force_seg.queue_adresse qc where qc.id_queue = :id_queue";
                $stmt = $this->conn->prepare($persistDetailQueue);
                $stmt->bindParam('id_queue', $idQueue); 
                $stmt->bindParam('idEvent', $idEvent); 
                $stmt = $stmt->executeQuery();
            }

            if(in_array('debiteur',$listeEntities)){
                $persistDetailQueue = "
                    INSERT INTO queue_event (`id_event_action_id`,`id_statut_id`, `id_queue_detail`, `statut_workflow`,`type`) 
                    SELECT 
                        :idEvent , 2 , qc.id , 0 , 5
                    FROM debt_force_seg.queue_debiteur qc where qc.id_queue = :id_queue";
                $stmt = $this->conn->prepare($persistDetailQueue);
                $stmt->bindParam('id_queue', $idQueue); 
                $stmt->bindParam('idEvent', $idEvent); 
                $stmt = $stmt->executeQuery();
            }
        }
    }
    public function getFirstEvent($id){
        $sql="select e.id from event_action e where e.id_workflow_id = :id ORDER BY e.id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id); 
        $stmt = $stmt->executeQuery();
        $resultList = $stmt->fetchOne();
        return $resultList;
    }

    public function updateStatutWorkflow($id , $etat){
        $statut = $this->em->getRepository(StatusWorkflow::class)->find(1);
        $workflow = $this->getWorkflow($id);
        $workflow->setIdStatus($statut);
        $this->em->flush();
    }
    public function getWorkflowForProcess(){
        $query = $this->em->createQuery(
            'SELECT w from App\Entity\Workflow w where (w.id_status = 1 or w.id_status = 3) ORDER BY w.id ASC'
        );
        $workflow = $query->getResult();
        return $workflow;
    }
    public function getWorkflowInProcess(){
        $query = $this->em->createQuery(
            'SELECT w from App\Entity\Workflow w where (w.id_status = 1) ORDER BY w.id ASC'
        );
        $workflow = $query->getResult();
        return $workflow;
    }
    public function getQueueEvent($id){
        $sql="SELECT * from queue_event w where (w.id_statut_id = 2) and w.id_event_action_id 
        in (select e.id from event_action e where e.id_workflow_id = :idWorkflow ) Limit 0,10";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('idWorkflow', $id); 
        $stmt = $stmt->executeQuery();
        $workflow = $stmt->fetchAll();
        return $workflow;
    }
    public function checkIfUserLibre(){
        $sql="SELECT u.id 
        FROM utilisateurs u 
        WHERE u.id IN (
            SELECT q.id_user_id 
            FROM queue_event_user q 
            GROUP BY q.id_user_id 
            HAVING COUNT(q.id_status_id = 2) < 5
        ) OR u.id not in (SELECT q.id_user_id 
            FROM queue_event_user q );";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $workflow = $stmt->fetchOne();
        return $workflow;
    }
    public function getUserLibre(){
        $sql="SELECT u.id 
        FROM utilisateurs u where id_type_user_id  != 1
        AND u.id IN (
            SELECT q.id_user_id 
            FROM queue_event_user q 
            GROUP BY q.id_user_id 
            HAVING COUNT(q.id_status_id = 2) < 5
        ) OR u.id not in (SELECT q.id_user_id 
            FROM queue_event_user q );";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $workflow = $stmt->fetchOne();
        return $workflow;
    }

    public function getEvenmentWorkflow($id){
        $sql="SELECT * from evenement_workflow where id = :id ;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id); 
        $stmt = $stmt->executeQuery();
        $workflow = $stmt->fetchAssociative();
        return $workflow;
    }
    public function addHistoriqueWorkflow($id , $histo){
        $sql="INSERT INTO `historique_workflow`( `historique`, `id_workflow_id`) VALUES (:histo,:id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id); 
        $stmt->bindParam('histo', $histo); 
        $stmt = $stmt->executeQuery();
    }
    public function assignTask($idUser , $idEvent , $statut){
        $sql="INSERT INTO `queue_event_user`(`id_queue_event_id`, `id_user_id`, `id_status_id`) VALUES (:idEvent,:idUser,:statut)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('idUser', $idUser); 
        $stmt->bindParam('idEvent', $idEvent); 
        $stmt->bindParam('statut', $statut); 
        $stmt = $stmt->executeQuery();
        return true;
    }
    public function updateStatutQueueEvent($idQueue , $statut){
        $sql="UPDATE `queue_event` SET `id_statut_id` = :statut WHERE `queue_event`.`id` = :id;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $idQueue); 
        $stmt->bindParam('statut', $statut); 
        $stmt = $stmt->executeQuery();
        return true;
    }
    public function getDataWorkflow($idWorkflow){
        $statut = $this->em->getRepository(DataWorkflow::class)->findOneBy(['id_workflow'=>$idWorkflow],["id"=>"DESC"]);
        return $statut;
    }
    public function getEventByWorkflow($eventQueue){
        $entity = $this->em->getRepository(QueueEvent::class)->findOneBy(['id'=>$eventQueue]);
        return $entity;
    }
    public function getEvenmentWorkflow2($cle , $id_workflow){
        $entity = $this->em->getRepository(EventAction::class)->findOneBy(['cle'=>$cle , 'id_workflow'=>$id_workflow]);
        return $entity;
    }

    public function getStatutQueueEvent($idStatut){
        $entity = $this->em->getRepository(StatutQueueEvent::class)->findOneBy(['id'=>$idStatut]);
        return $entity;
    }
    
    public function addHistoriqueQueueEvent($id_queue_event_id , $note  , $etat ,$cle){
        $sql="INSERT INTO `historique_queue_event`(`id_queue_event_id`, `note`, `date_action`, `etat`) VALUES (:id_queue_event_id, :note, now(), :etat , :cle)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id_queue_event_id', $id_queue_event_id); 
        $stmt->bindParam('note', $note); 
        $stmt->bindParam('etat', $etat); 
        $stmt->bindParam('cle', $cle); 
        $stmt = $stmt->executeQuery();
    }

    public function getAllQueueInDelay($idWorkflow){
        $sql="SELECT * FROM `queue_event` q where q.id_event_action_id  in (select a.id from event_action a where a.type = 3 and a.id_workflow_id = :id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $idWorkflow); 
        $stmt = $stmt->executeQuery();
        $entity = $stmt->fetchAll();
        return $entity;
    }
    public function getDateOfApprovEvent($cle){
        $sql="SELECT h.id , h.date_action from historique_queue_event h where h.etat = 1 and  h.cle =  :cle  order by h.id desc";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('cle', $cle); 
        $stmt = $stmt->executeQuery();
        $entity = $stmt->fetchAssociative();
        return $entity;
    }
    public function addQueueSplit($idEvent , $name , $cle,$isChild){dump($isChild);
        $sql="INSERT INTO `queue_split`(`id_event_action_id`, `name`, `cle`,`is_child`) VALUES (:idEvent, :name, :cle,".$isChild.")";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('idEvent', $idEvent); 
        $stmt->bindParam('name', $name); 
        $stmt->bindParam('cle', $cle); 
        // $stmt->bindParam('ischild', $ischild); 
        $stmt = $stmt->executeQuery();

        $sql="SELECT max(id) from queue_split ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $entity = $stmt->fetchOne();
        return $entity;
    }

    public function addSplitQueueDetail($idQueueSplit){
        $sql="INSERT INTO `queue_split`(`id_event_action_id`, `name`, `cle`) VALUES (:idEvent, :name, :cle)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('idEvent', $idEvent); 
        $stmt->bindParam('name', $name); 
        $stmt->bindParam('cle', $cle); 
        $stmt = $stmt->executeQuery();
    }
    
    public function getResultsByParent($id){
        $sql="select r.* from resultat_activite r where r.id_activite_id in (select a.id from activite a where a.id_parent_activite_id = :id);";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id); 
        $stmt = $stmt->executeQuery();
        $entity = $stmt->fetchAll();
        return $entity;
    }

    public function saveDetailAction($idEvent , $decisionId){
        $sql="INSERT INTO `detail_event_action`(`id_event_action_id`, `id_decision_step`) VALUES (:idEvent,:decisionId)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('idEvent', $idEvent); 
        $stmt->bindParam('decisionId', $decisionId); 
        $stmt = $stmt->executeQuery();
    }
    public function getCritereSplit($id){
        $sql2 = "select * from split_groupe_critere where id_queue_split_id = :id ORDER BY priority ASC";
        $stmt = $this->conn->prepare($sql2);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $liste_groupe = $stmt->fetchAll();
        $array_data = [];
        for ($i=0; $i < count($liste_groupe); $i++) { 
            $array_data[$i] = $liste_groupe[$i];
            $groupID = $liste_groupe[$i]["id"];
            $sql2 = "select * from split_critere where id_groupe_id = " . $groupID;
            $stmt = $this->conn->prepare($sql2);
            $stmt = $stmt->executeQuery();
            $criteria = $stmt->fetchAll();
            $array_data[$i]["criteres"] = $criteria;
            for ($j=0; $j < count($array_data[$i]["criteres"]); $j++) { 
                $critereId = $array_data[$i]["criteres"][$j]["id"];
                $sql2 = "select * from split_values_critere where id_critere_id = " . $critereId;
                $stmt = $this->conn->prepare($sql2);
                $stmt = $stmt->executeQuery();
                $details = $stmt->fetchAll();
                $array_data[$i]["criteres"][$j]["details"] = $details;
            }
        }
        return $array_data;
    }
    public function getListeSplitQueueById($id){
        $sql="SELECT * FROM `queue_split` s WHERE s.id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchAll();
        return $statut;
    }
    public function getSplitQueueByCle($cle ,$name){
        $entity = $this->em->getRepository(QueueSplit::class)->findOneBy(['cle'=>$cle , 'name'=>$name ],["cle"=>"DESC"]);
        return $entity;
    }

}