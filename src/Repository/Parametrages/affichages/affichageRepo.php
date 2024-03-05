<?php

namespace App\Repository\Parametrages\affichages;

use App\Entity\DetailModelAffichage;
use App\Entity\ModelAffichage;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class affichageRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    public function getListModels(){
        $resultList = $this->em->getRepository(ModelAffichage::class)->findAll();
        return $resultList;
    }
    public function createModel($titre , $objet, $ptf ){
        $sql = "INSERT INTO model_affichage ( `titre`, `date_creation`, `objet`,`id_ptf_id`) VALUES (:titre,SYSDATE(),:objet,:ptf)";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindParam('titre', $titre);
        $stmt->bindParam('objet', $objet);
        $stmt->bindParam('ptf', $ptf);
        $stmt = $stmt->executeQuery();
        if($stmt){
            $sql = "SELECT MAX(id) FROM model_affichage";
            $max = $this->conn->executeQuery($sql)->fetchOne();

            return $max;
        }else{
            return false;
        }
    }
    public function updateModel($titre , $objet ,$id){
        $sql = "UPDATE `model_affichage` SET `titre`=:titre,`objet`=:objet WHERE `id`=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('titre', $titre); 
        $stmt->bindParam('objet', $objet); 
        $stmt->bindParam('id', $id); 
        $stmt = $stmt->executeQuery();
        if($stmt){
            return true;
        }else{
            return false;
        }
    }
    public function findModel($id){
        $model = $this->em->getRepository(ModelAffichage::class)->findOneBy(["id"=>$id]);
        if($model){
            return $model;
        }else{
            return $model;
        }
    }
    public function findDetailModel($id){
        $model = $this->em->getRepository(DetailModelAffichage::class)->findOneBy(["id"=>$id]);
        if($model){
            return $model;
        }else{
            return $model;
        }
    }
    public function deleteModel($id){
        try{
            $model = $this->em->getRepository(ModelAffichage::class)->findOneBy(["id"=>$id]);
            $detail_model = $this->em->getRepository(DetailModelAffichage::class)->findBy(["id_model_affichage"=>$id]);
            foreach ($detail_model as $item) {
                $this->em->remove($item);
            }
            $this->em->remove($model);
            $this->em->flush();
            $statut = "OK";
            return $statut;
        }catch(\Exception $e){
            $statut = "NO";
			return $statut;
        }
    }
    public function createDetailModel($id_model_affichage_id,$table_name , $champ_name ,$length , $etat , $type_creance , $type_champ , $required){
        $sql = "INSERT INTO `detail_model_affichage`( `id_model_affichage_id`, `table_name`, `champ_name`, `length`, `etat`, `type_creance`, `type_champ`, `required`) VALUES (:id_model_affichage_id , :table_name, :champ_name, :length, :etat, :type_creance, :type_champ, :required)";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindParam('id_model_affichage_id', $id_model_affichage_id);
        $stmt->bindParam('table_name', $table_name);
        $stmt->bindParam('champ_name', $champ_name);
        $stmt->bindParam('length', $length);
        $stmt->bindParam('champ_name', $champ_name);
        $stmt->bindParam('etat', $etat);
        $stmt->bindParam('type_creance', $type_creance);
        $stmt->bindParam('type_champ', $type_champ);
        $stmt->bindParam('required', $required);
        $stmt = $stmt->executeQuery();
        if($stmt){
            return true;
        }else{
            return false;
        }
    }

    public function listDetailsModels($id , $type){
        $resultList = [];
        if($type == "creance"){
            $param = ["creance","detail_creance"];
            $sql2 = "SELECT * FROM `detail_model_affichage` d where d.table_name = :tb1 or d.table_name = :tb2 and d.id_model_affichage_id 
            in (select m.id from model_affichage m where m.id_ptf_id = :id) ";
            $stmt = $this->conn->prepare($sql2);
            $stmt->bindParam('tb1', $param[0]);
            $stmt->bindParam('tb2', $param[1]);
            $stmt->bindParam('id', $id);
            $stmt = $stmt->executeQuery();
            $resultList = $stmt->fetchAll();
        }else if($type == "debiteur"){
            $param = ["debiteur","emploie"];
            $sql2 = "SELECT * FROM `detail_model_affichage` d where d.table_name = :tb1 or d.table_name = :tb2 and d.id_model_affichage_id in (select m.id from model_affichage m where m.id_ptf_id = :id) ";
            $stmt = $this->conn->prepare($sql2);
            $stmt->bindParam('tb1', $param[0]);
            $stmt->bindParam('tb2', $param[1]);
            $stmt->bindParam('id', $id);
            $stmt = $stmt->executeQuery();
            $resultList = $stmt->fetchAll();
        }else if($type == "dossier"){
            $param = ["dossier"];
            $sql2 = "SELECT * FROM `detail_model_affichage` d where d.table_name = :tb1  and d.id_model_affichage_id in (select m.id from model_affichage m where m.id_ptf_id = :id) ";
            $stmt = $this->conn->prepare($sql2);
            $stmt->bindParam('tb1', $param[0]);
            $stmt->bindParam('id', $id);
            $stmt = $stmt->executeQuery();
            $resultList = $stmt->fetchAll();
        }else if($type == "garantie"){
            $param = ["garantie"];
            $sql2 = "SELECT * FROM `detail_model_affichage` d where d.table_name = :tb1  and d.id_model_affichage_id in (select m.id from model_affichage m where m.id_ptf_id = :id) ";
            $stmt = $this->conn->prepare($sql2);
            $stmt->bindParam('tb1', $param[0]);
            $stmt->bindParam('id', $id);
            $stmt = $stmt->executeQuery();
            $resultList = $stmt->fetchAll();
        }else if($type == "proc_judicaire"){
            $param = ["proc_judicaire"];
            $sql2 = "SELECT * FROM `detail_model_affichage` d where d.table_name = :tb1  and d.id_model_affichage_id in (select m.id from model_affichage m where m.id_ptf_id = :id) ";
            $stmt = $this->conn->prepare($sql2);
            $stmt->bindParam('tb1', $param[0]);
            $stmt->bindParam('id', $id);
            $stmt = $stmt->executeQuery();
            $resultList = $stmt->fetchAll();
        }
        return $resultList;
    }

    public function groupingListDetailsModels($id,$type){
        // $sql2 = "SELECT d.table_name FROM `detail_model_affichage` d where d.table_name = :tb1 and d.table_name = :tb2 and d.id_model_affichage_id in (select m.id from model_affichage m where m.id_ptf_id = :id) group by d.table_name";
        // $stmt = $this->conn->prepare($sql2);
        // // $stmt->bindParam('id', $id);
        // $stmt = $stmt->executeQuery();
        // $resultList = $stmt->fetchAll();
        // return $resultList;

        $resultList = [];
        if($type == "creance"){
            $param = ["creance","detail_creance"];
            $sql2 = "SELECT d.table_name FROM `detail_model_affichage` d where d.table_name = :tb1 or d.table_name = :tb2 and d.id_model_affichage_id in (select m.id from model_affichage m where m.id_ptf_id = :id) group by d.table_name";
            $stmt = $this->conn->prepare($sql2);
            $stmt->bindParam('tb1', $param[0]);
            $stmt->bindParam('tb2', $param[1]);
            $stmt->bindParam('id', $id);
            $stmt = $stmt->executeQuery();
            $resultList = $stmt->fetchAll();
        }else if($type == "debiteur"){
            $param = ["debiteur","emploie"];
            $sql2 = "SELECT d.table_name FROM `detail_model_affichage` d where d.table_name = :tb1 or d.table_name = :tb2 and d.id_model_affichage_id in (select m.id from model_affichage m where m.id_ptf_id = :id) group by d.table_name";
            $stmt = $this->conn->prepare($sql2);
            $stmt->bindParam('tb1', $param[0]);
            $stmt->bindParam('tb2', $param[1]);
            $stmt->bindParam('id', $id);
            $stmt = $stmt->executeQuery();
            $resultList = $stmt->fetchAll();
        }else if($type == "dossier"){
            $param = ["dossier"];
            $sql2 = "SELECT d.table_name FROM `detail_model_affichage` d where d.table_name = :tb1  and d.id_model_affichage_id in (select m.id from model_affichage m where m.id_ptf_id = :id) group by d.table_name";
            $stmt = $this->conn->prepare($sql2);
            $stmt->bindParam('tb1', $param[0]);
            $stmt->bindParam('id', $id);
            $stmt = $stmt->executeQuery();
            $resultList = $stmt->fetchAll();
        }else if($type == "garantie"){
            $param = ["garantie"];
            $sql2 = "SELECT d.table_name FROM `detail_model_affichage` d where d.table_name = :tb1  and d.id_model_affichage_id in (select m.id from model_affichage m where m.id_ptf_id = :id) group by d.table_name";
            $stmt = $this->conn->prepare($sql2);
            $stmt->bindParam('tb1', $param[0]);
            $stmt->bindParam('id', $id);
            $stmt = $stmt->executeQuery();
            $resultList = $stmt->fetchAll();
        }else if($type == "proc"){
            $param = ["garantie"];
            $sql2 = "SELECT d.table_name FROM `detail_model_affichage` d where d.table_name = :tb1  and d.id_model_affichage_id in (select m.id from model_affichage m where m.id_ptf_id = :id) group by d.table_name";
            $stmt = $this->conn->prepare($sql2);
            $stmt->bindParam('tb1', $param[0]);
            $stmt->bindParam('id', $id);
            $stmt = $stmt->executeQuery();
            $resultList = $stmt->fetchAll();
        }
        return $resultList;
    }

    public function updateDetailModel($id,$table_name , $champ_name ,$length , $etat , $type_creance , $type_champ , $required){
        $sql = "UPDATE `detail_model_affichage` SET table_name = :table_name,  champ_name = :champ_name,   length=:length ,   etat=:etat ,  type_creance=:type_creance ,  type_champ=:type_champ ,  required=:required where id=:id";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindParam('table_name', $table_name);
        $stmt->bindParam('id', $id);
        $stmt->bindParam('champ_name', $champ_name);
        $stmt->bindParam('length', $length);
        $stmt->bindParam('etat', $etat);
        $stmt->bindParam('type_creance', $type_creance);
        $stmt->bindParam('type_champ', $type_champ);
        $stmt->bindParam('required', $required);
        $stmt = $stmt->executeQuery();
        if($stmt){
            return true;
        }else{
            return false;
        }
    }
    public function deleteDetailModel($id){
        try{
            $detail_model = $this->em->getRepository(DetailModelAffichage::class)->findOneBy(["id"=>$id]);
            $this->em->remove($detail_model);
            $this->em->flush();
            $statut = "OK";
            return $statut;
        }catch(\Exception $e){
            $statut = "NO";
			return $statut;
        }
    }
}