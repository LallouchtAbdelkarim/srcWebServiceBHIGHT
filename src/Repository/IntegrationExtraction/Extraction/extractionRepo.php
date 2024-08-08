<?php

namespace App\Repository\IntegrationExtraction\Extraction;


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
    
    public function deleteModelExport($model){
        $this->em->remove($model);
        $this->em->flush();
    }
    
}