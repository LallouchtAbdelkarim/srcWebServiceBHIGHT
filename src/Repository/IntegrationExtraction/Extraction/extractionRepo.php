<?php

namespace App\Repository\IntegrationExtraction\Extraction;


use App\Entity\ColumnModelExport;
use App\Entity\HistoriqueDemandeCadrage;
use App\Entity\ModelExport;
use App\Entity\Portefeuille;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class extractionRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    
    public function getDataExtraction($typeCreance ,$ptf, $maxTotal , $minTotal , $maxRestant , $minRestant){
        $params = [];
        $sql = "SELECT d.* 
                FROM debiteur d 
                INNER JOIN type_debiteur t ON t.id_debiteur_id = d.id 
                INNER JOIN creance c ON c.id = t.id_creance_id
                WHERE 1=1";

        if (!empty($typeCreance) && $typeCreance !== "undefined") {
            $sql .= ' AND c.id_type_creance_id = :typeCreance';
            $params[':typeCreance'] = $typeCreance;
        } elseif (!empty($ptf) && $ptf !== "undefined") {
            $sql .= ' AND c.id_ptf_id = :ptf';
            $params[':ptf'] = $ptf;
        }

        if (!empty($maxTotal) && $maxTotal !== "undefined") {
            $sql .= ' AND c.total_creance >= :maxTotal';
            $params[':maxTotal'] = $maxTotal;
        }

        if (!empty($minTotal) && $minTotal !== "undefined") {
            $sql .= ' AND c.total_creance <= :minTotal';
            $params[':minTotal'] = $minTotal;
        }

        if (!empty($maxRestant) && $maxRestant !== "undefined") {
            $sql .= ' AND c.total_restant >= :maxRestant';
            $params[':maxRestant'] = $maxRestant;
        }

        if (!empty($minRestant) && $minRestant !== "undefined") {
            $sql .= ' AND c.total_restant <= :minRestant';
            $params[':minRestant'] = $minRestant;
        }

        $stmt = $this->conn->prepare($sql);
        
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        } 
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchAll();
        return $data;
    }
    
    public function addHistoDemandeCadrage($count , $type , $idPtf){
        $portefeuille = $this->em->getRepository(Portefeuille::class)->find($idPtf);
        $h=new HistoriqueDemandeCadrage();
        $h->setNbrDebiteurs($count);
        $h->setTypeCadrage($type);
        $h->setIdPtf($portefeuille);
        $h->setDateCreation(new \DateTime("now"));
        $this->em->persist($h);
        $this->em->flush();
    }
    public function getHistoriqueCadrage(){
        $portefeuille = $this->em->getRepository(HistoriqueDemandeCadrage::class)->findBy([],["id"=>"DESC"]);
        return $portefeuille;
    }
    public function getDemandeHistoriqueCadrage($id){
        $portefeuille = $this->em->getRepository(HistoriqueDemandeCadrage::class)->find($id);
        return $portefeuille;
    }
    
    public function getAllModelExport($type){
        $entity = $this->em->getRepository(ModelExport::class)->findAll();
        return $entity;
    }
    public function saveModelExport($titre, $data, ?ModelExport $model = null): ModelExport {
        if (!$model) {
            $model = new ModelExport();
            $model->setDateCreation(new \DateTime("now"));
        }
        $model->setTitre($titre);
        $model->setEntities($data);
        $model->setType(1);
        $this->em->persist($model);
        $this->em->flush();
    
        return $model;
    }
    
    public function getModelExport($id){
        $entity = $this->em->getRepository(ModelExport::class)->find($id);
        return $entity;
    }
    public function getColumnModelExport($id){
        $entity = $this->em->getRepository(ColumnModelExport::class)->findBy(['id_model'=>$id]);
        return $entity;
    }
    
    
    public function deleteModelExport($model){
        $this->em->remove($model);
        $this->em->flush();
    }
    public function deleteColumnModelExport($model){
        $this->em->remove($model);
        $this->em->flush();
    }
    

    public function saveColumnEntity($entities  , $selectedColumns , $model){
        foreach ($selectedColumns as $selected => $selectedValue ) {
            if(in_array($selected , $entities)){
                foreach ($selectedValue as $key => $value) {
                    if($value){
                        $column = new ColumnModelExport();
                        $column->setIdModel($model);
                        $column->setColumnName($key);
                        $column->setEntity($selected);
                        $this->em->persist($column);
                        $this->em->flush();
                    }
                }
            }
        }
    }

    public function getColumnModel($id , $entity){
        $entity = $this->em->getRepository(ColumnModelExport::class)->findBy(['id_model'=>$id , 'entity'=>$entity]);
        return $entity;
    }

    public function getDataBySegment($tableName , $id, $idSegmentation){
        if($tableName == 'dossier'){
            $columns = $this->getColumnModel($id,ucfirst($tableName));
            $columnsTable = '';
            for ($i=0; $i < count($columns); $i++) { 
                $laison = ',';
                if ($i === count($columns) - 1) {  // Check if it's the last iteration
                    $laison = ' ';  // or you can leave it empty
                }
                $columnsTable .= 'd.'.$columns[$i]->getColumnName().' '.$laison;
            }
            $sql = 'SELECT '.$columnsTable. ' from dossier d where d.id in (select s.id_dossier from debt_force_seg.seg_dossier s where s.id_seg = '.$idSegmentation.');';
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchAllAssociative();
            return $data;
        }
        else if($tableName == 'creance'){
            $columns = $this->getColumnModel($id,ucfirst($tableName));
            $columnsTable = '';
            for ($i=0; $i < count($columns); $i++) { 
                $laison = ',';
                if ($i === count($columns) - 1) {  // Check if it's the last iteration
                    $laison = ' ';  // or you can leave it empty
                }
                $columnsTable .= 'd.'.$columns[$i]->getColumnName().' '.$laison;
            }
            $sql = 'SELECT '.$columnsTable. ' from creance d where d.id in (select s.id_creance from debt_force_seg.seg_creance s where s.id_seg = '.$idSegmentation.');';
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchAllAssociative();
            return $data;
        }
        else if($tableName == 'debiteur'){
            $columns = $this->getColumnModel($id,ucfirst($tableName));
            $columnsTable = '';
            for ($i=0; $i < count($columns); $i++) { 
                $laison = ',';
                if ($i === count($columns) - 1) {  // Check if it's the last iteration
                    $laison = ' ';  // or you can leave it empty
                }
                $columnsTable .= 'd.'.$columns[$i]->getColumnName().' '.$laison;
            }
            $sql = 'SELECT '.$columnsTable. ' from debiteur d where d.id in (select s.id_debiteur from debt_force_seg.seg_debiteur s where s.id_seg = '.$idSegmentation.');';
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchAllAssociative();
            return $data;
        }
        else if($tableName == 'telephone'){
            $columns = $this->getColumnModel($id,ucfirst($tableName));
            $columnsTable = '';
            for ($i=0; $i < count($columns); $i++) { 
                $laison = ',';
                if ($i === count($columns) - 1) {  // Check if it's the last iteration
                    $laison = ' ';  // or you can leave it empty
                }
                $columnsTable .= 'd.'.$columns[$i]->getColumnName().' '.$laison;
            }
            $sql = 'SELECT '.$columnsTable. ' from telephone d where d.id in (select s.id_telephone from debt_force_seg.seg_telephone s where s.id_seg = '.$idSegmentation.');';
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchAllAssociative();
            return $data;
        }
        else if($tableName == 'adresse'){
            $columns = $this->getColumnModel($id,ucfirst($tableName));
            $columnsTable = '';
            for ($i=0; $i < count($columns); $i++) { 
                $laison = ',';
                if ($i === count($columns) - 1) {  // Check if it's the last iteration
                    $laison = ' ';  // or you can leave it empty
                }
                $columnsTable .= 'd.'.$columns[$i]->getColumnName().' '.$laison;
            }
            $sql = 'SELECT '.$columnsTable. ' from adresse d where d.id in (select s.id_adresse from debt_force_seg.seg_adresse s where s.id_seg = '.$idSegmentation.');';
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchAllAssociative();
            return $data;
        }
    }
}