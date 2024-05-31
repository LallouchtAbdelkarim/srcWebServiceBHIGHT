<?php

namespace App\Repository\Parametrages\Activities;

use App\Entity\Activite;
use App\Entity\ActiviteParent;
use App\Entity\DetailGroupeCompetence;
use App\Entity\Etap;
use App\Entity\EtapActivite;
use App\Entity\IntermResultatActivite;
use App\Entity\ParamActivite;
use App\Entity\ResultatActivite;
use App\Entity\SousEtap;
use App\Entity\TypeParametrage;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class activityRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    public function createParentActivity($titre , $note ){
        $parentAct = new ActiviteParent();
        $parentAct->setTitre($titre);
        $parentAct->setNote($note);
        $parentAct->setDateCreation(new \DateTime());
        $this->em->persist($parentAct);
        $this->em->flush();
        if($parentAct){
            return $parentAct;
        }else{
            return null;
        }
    }
    public function updateParentActivity($id,$titre , $note ){
        $sql = "UPDATE `activite_parent` SET `titre`=:titre,`note`=:note WHERE `id`=:id";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindParam('titre', $titre);
        $stmt->bindParam('note', $note);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        if($stmt){
            return true;
        }else{
            return false;
        }
    }
    public function findParentActivity($id){
        $parent = $this->em->getRepository(ActiviteParent::class)->findOneBy(["id"=>$id]);
        if($parent){
            return $parent;
        }else{
            return $parent;
        }
    }
    public function deleteParentActivity($id){
        $result = "";
        try{
            $parentActivity = $this->em->getRepository(ActiviteParent::class)->findOneBy(['id'=>$id]);
            if($parentActivity){
                $activities =  $this->em->getRepository(Activite::class)->findBy(['id_parent_activite'=>$parentActivity->getId()]);
                foreach ($activities as $act) {
                    $results =  $this->em->getRepository(ResultatActivite::class)->findBy(['id_activite'=>$act->getId()]);
                        foreach ($results as $res) {
                            $this->em->remove($res);
                        }
                    $steps =  $this->em->getRepository(EtapActivite::class)->findBy(['id_activite'=>$act->getId()]);
                        foreach ($steps as $step) {
                            $this->em->remove($step);
                        }
                    $this->em->remove($act);
                }
            }
            $this->em->remove($parentActivity);
            $this->em->flush();
            $result = "success";
            return $result;
		}
		catch(\Exception $e){
            $result = "Une erreur s'est produite".$e->getMessage();
			return $result;
		}
    }
    public function getAllParentActivity(){
        $resultList = $this->em->getRepository(ActiviteParent::class)->findBy([],["id"=>"DESC"]);
        return $resultList;
    }
    public function getOneParentActivityByTitre($titre){
        $resultList = $this->em->getRepository(ActiviteParent::class)->findBy(["titre"=>$titre]);
        return $resultList;
    }
    public function getTypesOfSParametrages(){
        $resultList = $this->em->getRepository(TypeParametrage::class)->findAll();
        return $resultList;
    }
    public function getOneTypesOfSParametrages($id){
        $resultList = $this->em->getRepository(TypeParametrage::class)->findOneBy(["id"=>$id]);
        return $resultList;
    }
    public function getDetailsOfActivitie(){
        $resultatList = array();
        $parent_details = array();
        $sql2 = "select count(id) from activite_parent";
        $stmt = $this->conn->prepare($sql2);
        $stmt = $stmt->executeQuery();
        $count_act = $stmt->fetchFirstColumn();
        $count_act = $count_act[0];

        $sql="select * from activite_parent ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $parent = $stmt->fetchAll();

        for ($i=0; $i < count($parent); $i++) { 
            $parent_details[$i]["parent"] =  $parent[$i];
            $activite =  $this->em->getRepository(Activite::class)->findBy(['id_parent_activite' => $parent[$i]["id"]]);
            $parent_details[$i]["activites"] =  $activite;
        }
        $resultatList["count_parent"] = $count_act;
        $resultatList["dataParent"] = $parent_details;
        return $resultatList;
    }

    public function getResultats($id){
        // $sql2 = "select * from resultat_activite where id_activite_id = ".$id;
        // $stmt = $this->conn->prepare($sql2);
        // $stmt = $stmt->executeQuery();
        $resultatList =  $this->em->getRepository(ResultatActivite::class)->findBy(['id_activite' => $id]);
        return $resultatList;
    }
    public function getDetailsOfTypeParametrages(){
        $resultatList = array();
        $type_parametre_details = array();
        $sql="select * from type_parametrage ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $type = $stmt->fetchAll();

        for ($j=0; $j < count($type) ; $j++) { 
            $type_parametre_details[$j]["type"] =  $type[$j];
            $sql="select * from param_activite where id_branche_id = ".$type[$j]["id"]." limit 0,6 ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $params = $stmt->fetchAll();
            $type_parametre_details[$j]["params"] =  $params;

            $sql="select count(id) from param_activite where id_branche_id = ".$type[$j]["id"]."  ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $count_param = $stmt->fetchFirstColumn();
            $type_parametre_details[$j]["count_param"] =  $count_param[0];
        }
        $resultatList = $type_parametre_details;
        return $resultatList;
    }
    public function getOneParam($id){
        $param =  $this->em->getRepository(ParamActivite::class)->findOneBy(["id"=>$id]);
        if($param){
            return $param;
        }else{
            return null;
        }
    }
    public function getParamsActivity(){
        $param =  $this->em->getRepository(ParamActivite::class)->findAll();
        if($param){
            return $param;
        }else{
            return null;
        }
    }
    public function getParamsActivityByType($id){
        $param =  $this->em->getRepository(ParamActivite::class)->findBy(["id_branche"=>$id , "typeActivite"=>1]);
        if($param){
            return $param;
        }else{
            return null;
        }
    }
    public function getListResultatByAct($id){
        $param =  $this->em->getRepository(ParamActivite::class)->findBy(["activite_p"=>$id]);
        if($param){
            return $param;
        }else{
            return null;
        }
    }
    
    public function createActivity($parentAct , $param , $i){
        try{
            $activite_ = new Activite();
            $activite_->setIdParam($param);
            $activite_->setDateCreation(new \DateTime());
            $activite_->setIdParentActivite($parentAct);
            $activite_->setNumLink($i);
            $this->em->persist($activite_);
            $this->em->flush();
            return $activite_;
        }catch (\Exception $e){
            $codeStatut = "ERREUR";
			return $codeStatut;
        }
    }
    public function createResult($activite_ , $key ,$param, $i , $skip){
        try{
            $res = new ResultatActivite();
            $res->setNom("");
            $res->setIdActivite($activite_);
            $res->setOrdre($key);
            $res->setIdParam($param);
            $res->setNumero($i);
            $res->setSkip($skip);
            $this->em->persist($res);
            $this->em->flush();
            return $res;
        }catch (\Exception $e){
            $codeStatut = "ERREUR";
			return $codeStatut;
        }
    }

    public function createEtap($activite_  ,$etap){
        try{
            $EtapActivite = new EtapActivite();
            $EtapActivite->setTitre("");
            $EtapActivite->setIdActivite($activite_);
            $EtapActivite->setEtat(0);
            // $etap->setIdParam($param);
            $EtapActivite->setIdEtap($etap);
            $this->em->persist($EtapActivite);
            $this->em->flush();
            return $EtapActivite;
        }catch (\Exception $e){
            $codeStatut = "ERREUR";
			return $codeStatut;
        }
    }
    public function createEtapParam($titre , $familles){
        $etap = new Etap();
        $etap->setTitre($titre);
        $etap->setIdFamille($familles);
        $etap->setDateCreation(new \DateTime());
        $this->em->persist($etap);
        $this->em->flush();
        return $etap;
    }
    public function createSousEtap($id ,$idEtap , $type , $order ){
        $etap = new SousEtap();
        $etap->setIdEtap($idEtap);
        $etap->setIdParam($id);
        $etap->setOrderEtap($order);
        $etap->setEtatApproval($type);
        $this->em->persist($etap);
        $this->em->flush();
        return $etap;
    }
    public function createIntermResult($resultatLink  ,$act_link){
        try{
            $act_res = new IntermResultatActivite();
            $act_res ->setIdResultat($resultatLink);
            $act_res ->setIdActivite($act_link);
            $this->em->persist($act_res);
            $this->em->flush();
            return $act_res;
        }catch (\Exception $e){
            $codeStatut = "ERREUR";
			return $codeStatut;
        }
    }
    public function findActivity($id){
        $data = $this->em->getRepository(Activite::class)->findOneBy(["id"=>$id]);
        if($data){
            return $data;
        }else{
            return $data;
        }
    }
    public function getOneEtap($id){
        $etapSelected =  $this->em->getRepository(Etap::class)->find($id);
        if($etapSelected){
            return $etapSelected;
        }else{
            return $etapSelected;
        }
    }
    public function createParam($activite_p , $code_type , $type , $famille , $typeActivite){
        $etap = new ParamActivite();
        $etap->setActiviteP($activite_p);
        $etap->setType($type);
        $etap->setCodeType($code_type);
        $etap->setIdBranche($famille);
        $etap->setTypeActivite($typeActivite);
        $this->em->persist($etap);
        $this->em->flush();
        return $etap;
    }
    public function updateParam($id,$activite_p , $code_type , $type , $famille , $typeActivite){
        $etap =  $this->em->getRepository(ParamActivite::class)->fin($id);
        $etap->setActiviteP($activite_p);
        $etap->setType($type);
        $etap->setCodeType($code_type);
        $etap->setIdBranche($famille);
        $etap->setTypeActivite($typeActivite);
        $this->em->flush();
        return $etap;
    }
    
}