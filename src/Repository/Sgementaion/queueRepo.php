<?php

namespace App\Repository\Sgementaion;
use App\Entity\QueueCritere;
use App\Entity\QueueGroupe;
use App\Entity\Queue;
use App\Entity\QueueGroupeCritere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class queueRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }

    public function getListeGroupeQueue(){
        $resultList = $this->em->getRepository(QueueGroupe::class)->findAll();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function updateGroupe($id , $titre , $description){
        $data =  array();
        $sql="UPDATE `queue_groupe` SET `titre`=:titre , `description`=:description WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt->bindValue(":titre",$titre);
        $stmt->bindValue(":description",$description);
        $stmt = $stmt->executeQuery();
        // return $data;
    }
    public function addGroupe( $titre , $description){
        $data =  array();
        $sql="INSERT INTO `queue_groupe`( `titre`, `description`) VALUES (:titre,:description)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":titre",$titre);
        $stmt->bindValue(":description",$description);
        $stmt = $stmt->executeQuery();
        // return $data;
    }
    public function deleteGroupeQueue($id){
        $data =  array();
        $sql="DELETE FROM `queue_groupe` WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        // return $data;
    }
    public function getTypesQueue(){
        $sql="SELECT * FROM `type_queue` s ";
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
    public function getListeSgementationByGroupe2($id_type,$id_groupe){
        $sql="SELECT * FROM `segmentation` s WHERE s.id and s.type = ".$id_type."";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchAll();
        return $statut;
    }
    
    public function getListeQueue($id_type,$id_groupe){
        $resultList = $this->em->getRepository(Queue::class)->findBy(["id_type"=>$id_type,"queue_groupe"=>$id_groupe],["id"=>"DESC"]);

        // $sql="SELECT* from queue q WHERE q.id_type_id = '".$id_type."' and q.queue_groupe_id = '".$id_groupe."'";
        // $stmt = $this->conn->prepare($sql);
        // // $stmt->bindValue(":id",$id);
        // $stmt = $stmt->executeQuery();
        // $data = $stmt->fetchAll();
        return $resultList;
    }
    public function getLastQueue(){
        $sql="Select max(id) from queue";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        return $data;
    }
    
    public function findGroupe($id){
        $sql="Select * from queue_groupe where id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        return $data;
    }
    
    public function checkQueueInGroupe($id){
        $sql="Select * from queue where queue_groupe_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        return $data;
    }
    public function getCritereQueue($id){
        $sql2 = "select * from queue_groupe_critere where id_queue_id = :id";
        $stmt = $this->conn->prepare($sql2);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $liste_groupe = $stmt->fetchAll();
        $array_data = [];
        for ($i=0; $i < count($liste_groupe); $i++) { 
            $array_data[$i] = $liste_groupe[$i];
            $groupID = $liste_groupe[$i]["id"];
            $sql2 = "select * from queue_critere where id_groupe_id = " . $groupID;
            $stmt = $this->conn->prepare($sql2);
            $stmt = $stmt->executeQuery();
            $criteria = $stmt->fetchAll();
            $array_data[$i]["criteres"] = $criteria;
            for ($j=0; $j < count($array_data[$i]["criteres"]); $j++) { 
                $critereId = $array_data[$i]["criteres"][$j]["id"];
                $sql2 = "select * from queue_values where id_critere_id = " . $critereId;
                $stmt = $this->conn->prepare($sql2);
                $stmt = $stmt->executeQuery();
                $details = $stmt->fetchAll();
                $array_data[$i]["criteres"][$j]["details"] = $details;
            }
        }
        return $array_data;
    }
    public function clearCritereByQueue($id){
        $sql="DELETE FROM queue_values
        WHERE id_critere_id IN (
            SELECT queue_critere.id
            FROM queue_critere
            JOIN queue_groupe_critere ON queue_critere.id_groupe_id = queue_groupe_critere.id
            WHERE queue_groupe_critere.id_queue_id = :id);

            DELETE FROM queue_critere
            WHERE id_groupe_id IN (
                SELECT id
                FROM queue_groupe_critere
                WHERE id_queue_id = :id
            );
            DELETE FROM queue_groupe_critere
            WHERE id_queue_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
    }
    public function createQueue( $titre,$description,$queue_groupe_id ,$id_type_id , $segment,$active){
        $sql="INSERT INTO `queue`( `queue_groupe_id`, `titre`, `description`, `id_segmentation_id`, `id_type_id` , `active` , `date_creation`) 
        VALUES (:queue_groupe_id,:titre,:description,:segment,:id_type_id , :active , now());";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":titre",$titre);
        $stmt->bindValue(":description",$description);
        $stmt->bindValue(":queue_groupe_id",$queue_groupe_id);
        $stmt->bindValue(":id_type_id",$id_type_id);
        $stmt->bindValue(":segment",$segment);
        $stmt->bindValue(":active",$active);
        $stmt = $stmt->executeQuery();
    }
    public function findQueueByTitre($titre){
        $sql="Select * from queue where titre = :titre";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":titre",$titre);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        return $data;
    }      
    public function findQueue($id){
        $sql="Select * from queue where id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchAll();
        return $data;
    }
    public function createQueueCritere($critere , $id_groupe , $type){
        $model = new QueueCritere();
        $model->setCritere($critere);
        $model->setIdGroupe($id_groupe);
        $model->setType($type);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }
    public function createGroupeCritereRepo($groupe , $id_queue){
        $Queue = $this->em->getRepository(Queue::class)->findOneBy(["id"=>$id_queue]);
        $model = new QueueGroupeCritere();
        $model->setGroupe($groupe);
        $model->setIdQueue($Queue);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }

    public function createQueueValues($value1 ,$value2 ,$id_critere){
        $sql="INSERT INTO `queue_values`( `value1`, `value2`, `id_critere_id`) VALUES (:value1,:value2,:id_critere)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":value1",$value1);
        $stmt->bindValue(":value2",$value2);
        $stmt->bindValue(":id_critere",$id_critere);
        $stmt = $stmt->executeQuery();   
        return $stmt;     
    }
    public function updatePriority($id , $priority){
        $sql="UPDATE `queue` SET `priority`=:priority where id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":priority",$priority);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();   
        return $stmt;     
    }
    public function getSegmentationNonAssigne(){
        $sql="Select * from segmentation where id_status_id = 3 and id not in (select q.id_segmentation_id from queue q) ORDER BY `id` DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchAll();
        return $data;
    }
    public function getListeQueueByStatus($status){
        $sql="SELECT * FROM `queue` s WHERE s.id_status_id = :status";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":status",$status);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchAll();
        return $statut;
    }
    public function checkIfInSeg($id_creance , $id_segment){
        $sql="SELECT s.id FROM `debt_force_seg`.`seg_creance` s WHERE s.id_creance = :creance and s.id_seg = :id_seg";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":creance",$id_creance);
        $stmt->bindValue(":id_seg",$id_segment);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchOne();
        return $statut;
    }
    
}