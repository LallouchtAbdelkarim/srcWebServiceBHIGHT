<?php

namespace App\Repository\Parametrages;

use App\Entity\CompetenceProfil;
use App\Entity\CritereModelFacturation;
use App\Entity\Competence;
use App\Entity\DetailCompetence;
use App\Entity\DetailCompetenceFamilles;
use App\Entity\ModelFacturation;
use App\Entity\RegleModelFacturation;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\DetailGroupeCompetence;
use App\Entity\GroupeCompetence;

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
        $data->setTitre($titre);
        $data->setDateCreation(new \DateTime);
        $this->em->persist($data);
        $this->em->flush();
        if($data){
            return $data;
        }else{
            return null;
        }
    }
    public function resetComp($id){
        $sql = 'DELETE FROM `detail_competence` WHERE id_competence_id = '.$id.';
        DELETE FROM `detail_competence_familles` WHERE id_competence_id = '.$id.';
        ';
        $stmt = $this->conn->prepare($sql); 
        $stmt = $stmt->executeQuery();
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
    public function checkCompetenceProfil($id){
        $model = $this->em->getRepository(CompetenceProfil::class)->findOneBy(["id_competence"=>$id]);
        if($model){
            return $model;
        }else{
            return null;
        }
    }
    public function getFamilles($id){
        $model = $this->em->getRepository(DetailCompetenceFamilles::class)->findBy(["id_competence"=>$id]);
        if($model){
            return $model;
        }else{
            return [];
        }
    }

    public function getCompetencesNotSelected($id){
        $query = $this->em->createQuery(
            'SELECT t
            FROM App\Entity\TypeParametrage t
            
            where t.id not in (SELECT identity(c.id_famille) from App\Entity\DetailCompetenceFamilles c where identity(c.id_competence) = :id) '
        );
        $query->setParameter('id',$id);
        return $query->getResult();
    }
    public function getSousFamillesSelected($id){
        $query = $this->em->createQuery(
            'SELECT t
            FROM App\Entity\ParamActivite t
            
            where t.id  in (SELECT identity(c.id_param) from App\Entity\DetailCompetence c where identity(c.id_competence) = :id) '
        );
        $query->setParameter('id',$id);
        return $query->getResult();
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
        // try{
            $model = $this->em->getRepository(Competence::class)->findOneBy(["id"=>$id]);
            $detail_model = $this->em->getRepository(DetailCompetence::class)->findBy(["id_competence"=>$id]);
            foreach ($detail_model as $item) {
                $this->em->remove($item);
            }
            $this->em->remove($model);
            $this->em->flush();
            $statut = "OK";
            return $statut;
        // }catch(\Exception $e){
        //     $statut = "NO";
		// 	return $statut;
        // }
    }

    public function createDetailModel( $p , $comp){
        $interm_param = new DetailCompetence();
        $interm_param->setIdGroupe($p);
        $interm_param->setIdCompetence($comp);
        $this->em->persist($interm_param);
        $this->em->flush();
    }
    public function createDetailModel2( $p , $comp){
        $interm_param = new DetailCompetence();
        $interm_param->setIdGroupe($p);
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
    public function createGroupeConpetence($titre  ){
        $data = new GroupeCompetence();
        $data->setTitre($titre);
        $data->setDateCreation(new \DateTime);
        $this->em->persist($data);
        $this->em->flush();
        if($data){
            return $data;
        }else{
            return null;
        }
    }
    public function createDetailGroupe($groupe  ,$act){
        $act_res = new DetailGroupeCompetence();
        $act_res ->setIdGroupe($groupe);
        // $act_res ->setIdActivite($act);
        $act_res ->setIdFamille($act);
        $this->em->persist($act_res);
        $this->em->flush();
        return $act_res;
        
    }
    public function getGroupeCompetence(){
        $model = $this->em->getRepository(GroupeCompetence::class)->findAll();
        if($model){
            return $model;
        }else{
            return [];
        }
    }
    public function getOneGroupeCompetence($id){
        $model = $this->em->getRepository(GroupeCompetence::class)->find($id);
        if($model){
            return $model;
        }else{
            return null;
        }
    }
}