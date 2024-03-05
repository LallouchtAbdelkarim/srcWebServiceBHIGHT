<?php

namespace App\Repository\Parametrages;

use App\Entity\CritereModelFacturation;
use App\Entity\Competence;
use App\Entity\DetailCompetence;
use App\Entity\DetailCompetenceFamilles;
use App\Entity\ModelFacturation;
use App\Entity\RegleModelFacturation;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class competenceRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    public function getListModels(){
        
        $resultList = $this->em->getRepository(Competence::class)->findBy([],['id' => 'DESC'] );
        return $resultList;
    }
    public function createModel($titre  ){
        $data = new Competence();
        $data->setTitre("titre");
        $data->setDateCreation(new \DateTime);
        $this->em->persist($data);
        $this->em->flush();
        if($data){
            return $data;
        }else{
            return null;
        }
    }
    public function updateModel($comp , $titre){
        $comp->setTitre($titre);
        $this->em->flush();
        return true;
    }
    public function findModel($id){
        $model = $this->em->getRepository(Competence::class)->findOneBy(["id"=>$id]);
        if($model){
            return $model;
        }else{
            return null;
        }
    }
    // public function updateModel($titre , $objet ,$id){
    //     $sql = "UPDATE `model_facturation` SET `titre`=:titre,`objet`=:objet WHERE `id`=:id";
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->bindParam('titre', $titre); 
    //     $stmt->bindParam('objet', $objet); 
    //     $stmt->bindParam('id', $id); 
    //     $stmt = $stmt->executeQuery();
    //     if($stmt){
    //         return true;
    //     }else{
    //         return false;
    //     }
    // }
    public function deleteModel($id){
        try{
            $model = $this->em->getRepository(Competence::class)->findOneBy(["id"=>$id]);
            $detail_model = $this->em->getRepository(DetailCompetence::class)->findBy(["id_competence"=>$id]);
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

    public function createDetailModel( $p , $comp){
        $interm_param = new DetailCompetence();
        $interm_param->setIdParam($p);
        $interm_param->setIdCompetence($comp);
        $this->em->persist($interm_param);
        $this->em->flush();
    }
    public function createDetailCompetenceFamille( $p , $comp){
        $interm_param = new DetailCompetenceFamilles();
        $interm_param->setIdFamille($p);
        $interm_param->setIdCompetence($comp);
        $this->em->persist($interm_param);
        $this->em->flush();
        return $interm_param;
    }
}