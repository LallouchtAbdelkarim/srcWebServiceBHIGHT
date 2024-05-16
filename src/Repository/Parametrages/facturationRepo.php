<?php

namespace App\Repository\Parametrages;

use App\Entity\CritereModelFacturation;
use App\Entity\DetailCritereModelFacturation;
use App\Entity\ModelFacturation;
use App\Entity\RegleModelFacturation;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class facturationRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    public function getListModels(){
        $resultList = $this->em->getRepository(ModelFacturation::class)->findAll();
        return $resultList;
    }
    public function createModel($titre , $objet ){
        $model = new ModelFacturation();
        $model->setTitre($titre);
        $model->setObjet($objet);
        $model->setDateCreation(new \DateTime);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }
    public function findModel($id){
        $model = $this->em->getRepository(ModelFacturation::class)->findOneBy(["id"=>$id]);
        if($model){
            return $model;
        }else{
            return $model;
        }
    }
    public function getModels(){
        $model = $this->em->getRepository(ModelFacturation::class)->findAll();
        if($model){
            return $model;
        }else{
            return $model;
        }
    }
    
    public function updateModel($titre , $objet ,$id){
        $sql = "UPDATE `model_facturation` SET `titre`=:titre,`objet`=:objet WHERE `id`=:id";
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
    public function deleteModel($id){
        try{
            $model = $this->em->getRepository(ModelFacturation::class)->findOneBy(["id"=>$id]);
            $detail_model = $this->em->getRepository(DetailModelFacturation::class)->findBy(["id_model_facturation"=>$id]);
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
    public function createRegle($r,$model){
        $reglefact = new RegleModelFacturation();
        $reglefact->setNom($r);
        $reglefact->setIdModel($model);
        $this->em->persist($reglefact);
        $this->em->flush();
        return $reglefact;
    }
    public function createCritere($table_critere,$column_critere,$action_critere,$valeur1_critere,$valeur2,$type_critere,$operator,$regle){
        $critere = new CritereModelFacturation();
        $critere->setTableName($table_critere);
        $critere->setColumnName($column_critere);
        $critere->setAction($action_critere);
        $critere->setValeur1($valeur1_critere);
        $critere->setTypeColumn($type_critere);
        $critere->setValeur2($valeur2);
        $critere->setOperator($operator);
        $critere->setIdRegle($regle);
        $this->em->persist($critere);
        $this->em->flush();
        return $critere;
    }

    public function createDetailCritere($table_critere,$column_critere,$action_critere,$valeur1_critere,$valeur2,$type_critere,$critere ,$type_detail,$operator_detail){
        $detailCritere = new DetailCritereModelFacturation();
        $detailCritere->setTableName($table_critere);
        $detailCritere->setColumnName($column_critere);
        $detailCritere->setAction($action_critere);
        $detailCritere->setValeur1($valeur1_critere);
        $detailCritere->setTypeColumn($type_critere);
        $detailCritere->setValeur2($valeur2);
        $detailCritere->setIdCritere($critere);
        $detailCritere->setTypeDetail($type_detail);
        $detailCritere->setOperator($operator_detail);
        $this->em->persist($detailCritere);
        $this->em->flush();
    }
    public function getModelDetails($id){
        //Get data 
        $data_list = [];
        $regles_facturation = $this->em->getRepository(RegleModelFacturation::class)->findBy(['id_model' => $id]);
        if($regles_facturation){
            for($i = 0 ; $i<count($regles_facturation) ; $i++){
                $data_list[$i]["regles"] = $regles_facturation[$i];
                $critere_facturation = $this->em->getRepository(CritereModelFacturation::class)->findBy(['idRegle' =>  $regles_facturation[$i]->getId()]);
                if(count($critere_facturation) == 0){
                    $data_list[$i]["criteres"] = [];
                }else{
                    for($j = 0 ; $j<count($critere_facturation) ; $j++){
                        $data_list[$i]["criteres"][$j] = $critere_facturation[$j];
                    }   
                }
            }
        }
        return $data_list;
    }
    public function findCritere($id){
        $critere = $this->em->getRepository(CritereModelFacturation::class)->findOneBy(["id"=>$id]);
        if($critere){
            return $critere;
        }else{
            return $critere;
        }
    }
    public function findRegle($id){
        $elem = $this->em->getRepository(RegleModelFacturation::class)->findOneBy(["id"=>$id]);
        if($elem){
            return $elem;
        }else{
            return $elem;
        }
    }
    public function findRegleByModel($id){
        $elem = $this->em->getRepository(RegleModelFacturation::class)->findBy(["id_model"=>$id]);
        if($elem){
            return $elem;
        }else{
            return $elem;
        }
    }
    public function deleteCritere($id){
        try{
            $elem = $this->em->getRepository(CritereModelFacturation::class)->findOneBy(["id"=>$id]);
            $this->em->remove($elem);
            $this->em->flush();
            $statut = "OK";
            return $statut;
        }catch(\Exception $e){
            $statut = "NO";
			return $statut;
        }
    }
    public function deleteRegle($id){
        try{
            $elem = $this->em->getRepository(RegleModelFacturation::class)->findOneBy(["id"=>$id]);
            $childElem = $this->em->getRepository(CritereModelFacturation::class)->findOneBy(["id_regle"=>$id]);
            foreach ($childElem as $value) {
                $this->em->remove($value);
            }
            $this->em->remove($elem);
            $this->em->flush();
            $statut = "OK";
            return $statut;
        }catch(\Exception $e){
            $statut = "NO";
			return $statut;
        }
    }
    public function search_value_critere($table , $column ,$value){
        $sql="select * from ".$table." where ".$column." = '".$value."'  ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
    }

    public function deleteRegles($id){
        $sql='DELETE FROM `detail_critere_model_facturation` WHERE `id_critere_id` IN (SELECT `id` FROM `critere_model_facturation` WHERE `id_regle_id` IN (SELECT `id` FROM `regle_model_facturation` WHERE `id_model_id` = '.$id.'));

        DELETE FROM `critere_model_facturation` WHERE `id_regle_id` IN (SELECT `id` FROM `regle_model_facturation` WHERE `id_model_id` = '.$id.');
        
        DELETE FROM `regle_model_facturation` WHERE `id_model_id` = '.$id.';
        ';
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
    }
}