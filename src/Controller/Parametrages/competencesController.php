<?php

namespace App\Controller\Parametrages;

use App\Entity\DetailCompetence;
use App\Entity\DetailGroupeCompetence;
use App\Entity\GroupeCompetence;
use App\Entity\SousDetailGroupeCompetence;
use App\Repository\Parametrages\competenceRepo;
use App\Service\AuthService;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\Parametrages\Activities\activityRepo;
use Doctrine\DBAL\Connection;

#[Route('/API')]

class competencesController extends AbstractController
{
    private $MessageService;
    private $conn;

    private $AuthService;
    public $em;

    public function __construct(
        competenceRepo $competenceRepo,
        SerializerInterface $serializer,
        activityRepo $activityRepo,
        MessageService $MessageService,
        Connection $conn ,
        AuthService $AuthService,
        EntityManagerInterface $em)
    {
        $this->competenceRepo = $competenceRepo;
        $this->serializer = $serializer;
        $this->activityRepo = $activityRepo;
        $this->em = $em;
        $this->MessageService = $MessageService;
        $this->conn = $conn;
        $this->AuthService = $AuthService;
    }

    #[Route('/competences/listModels/')]
    public function index(competenceRepo $competenceRepo , Request $request): JsonResponse
    {
        // $this->AuthService->checkAuth(0,$request);
        $respObjects =array();
        $codeStatut = "ERREUR";
        try{
            $data = $competenceRepo->getListModels();
            $codeStatut = "OK";
            $respObjects["data"] = $data;
        }catch(\Exception $e){
            $codeStatut = "ERROR_EXCEPETION";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/competences/createCompetence', methods: ['POST'])]
    public function createModel(competenceRepo $competenceRepo ,activityRepo $activityRepo, Request $request): JsonResponse
    {
        $respObjects = array();
        $titre = $request->get("titre");
        $data = json_decode($request->getContent() , true);
        $list_check = $data["data_check"];
        $list_check_famile = $data["data_check_famille"];
        $dataGroupe = $data["data"];
        $codeStatut = "ERREUR";
        if(count($dataGroupe) >= 1 ){
            if(trim($titre) != ""  ){
                $comp = $competenceRepo->createModel($titre  );
                if($comp){
                    if($comp){
                        for ($i=0; $i < count($dataGroupe) ; $i++) { 
                            $p = $competenceRepo->getOneGroupeCompetence($dataGroupe[$i]);
                            $competenceRepo->createDetailModel2($p , $comp); 
                        }
                        $codeStatut="OK";
                    }
    
                }else{
                    $codeStatut = "ERROR";
                }
            }else{
                $codeStatut = "EMPTY-DATA";
            }
        }else{
            $codeStatut = "EMPTY-DATA";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/competences/updateCompetence', methods: ['POST'])]
    public function updateModel(competenceRepo $competenceRepo , Request $request): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);
        $respObjects = array();
        $titre = $request->get("titre");
        $id = $request->get("id");
        $codeStatut = "ERREUR";
        $comp = $competenceRepo->findModel($id);

        if(trim($titre) != ""  ){
            $activityRepo = $competenceRepo->updateModel($comp ,$titre  );
            if($activityRepo){
                $codeStatut = "OK";
            }else{
                $codeStatut = "ERROR";
            }
        }else{
            $codeStatut = "EMPTY-DATA";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/competences/createDetailCompetence/{id}',methods: ['POST'])]
    public function createDetailCompetence(competenceRepo $competenceRepo , Request $request ,activityRepo $activityRepo , $id): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);
        $codeStatut = "ERROR";
        $respObjects = array();
        $listParams = $activityRepo->getParamsActivity();
        $data = json_decode($request->getContent() , true);
        $comp = $competenceRepo->findModel($id);
        $list_check = $data["data_check"];
        $list_check_famile = $data["data_check_famille"];

        

        if($comp){
            for ($i=0; $i < count($list_check) ; $i++) { 
                $p = $activityRepo->getOneParam($list_check[$i]);
                $competenceRepo->createDetailModel($p , $comp); 
            }
            for ($j=0; $j < count($list_check_famile) ; $j++) { 
                # code...
                $famille = $activityRepo->getOneTypesOfSParametrages($list_check_famile[$j]["id"]);
                $competenceRepo->createDetailCompetenceFamille($famille , $comp); 
            }
            $codeStatut="OK";
        }else{
            $respObjects["codeStatut"] = "NOT_EXIST_ELEMENT";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/competences/updateDetailCompetence',methods: ['POST'])]
    public function updateDetailCompetence(competenceRepo $competenceRepo , Request $request ,activityRepo $activityRepo ): JsonResponse
    {
        $respObjects = array();
        $codeStatut = "ERROR";
        $listParams = $activityRepo->getParamsActivity();
        $data = json_decode($request->getContent() , true);
        $id = $data["id"];
        $comp = $competenceRepo->findModel($id);

        $list_check = $data["data_check"];
        $list_check_famile = $data["data_check_famille"];
        $dataGroupe = $data["data"];
        if($comp){
            $detailsCompetence = $this->em->getRepository(DetailCompetence::class)->findBy(['id_competence'=>$id]);
            foreach ($detailsCompetence as $value) {
                $this->em->remove($value);
            }
            $this->em->flush();

            for ($i=0; $i < count($dataGroupe) ; $i++) { 
                $p = $competenceRepo->getOneGroupeCompetence($dataGroupe[$i]);
                $competenceRepo->createDetailModel2($p , $comp); 
            }
                $codeStatut="OK";


            
            // if(count($list_check) >= 1 && count($list_check_famile) >= 1){
            //     $competenceRepo->resetComp($id);
            //     for ($i=0; $i < count($list_check) ; $i++) { 
            //         $p = $activityRepo->getOneParam($list_check[$i]);
            //         $competenceRepo->createDetailModel($p , $comp); 
            //     }
            //     for ($j=0; $j < count($list_check_famile) ; $j++) { 
            //         # code...
            //         $famille = $activityRepo->getOneTypesOfSParametrages($list_check_famile[$j]["id"]);
            //         $competenceRepo->createDetailCompetenceFamille($famille , $comp); 
            //     }
            //     $codeStatut="OK";
            // }
        }else{
            $codeStatut= "NOT_EXIST_M";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/competences/deleteCompetence')]
    public function deleteModel(competenceRepo $competenceRepo , Request $request): JsonResponse
    {
        $respObjects = array();
        try{
            $id = $request->get("id");
            $data = $competenceRepo->findModel($id);
            if($data){
                if($competenceRepo->checkCompetenceProfil($id)){
                    $respObjects["message"] = "Votre compétence déjà existe dans un profil";
                    $respObjects["codeStatut"] = "ERROR";
                }else{
                    $competenceRepo->deleteModel($id);
                    $respObjects["message"] = "success";
                    $respObjects["codeStatut"] = "OK";
                }
            }else{
                $respObjects["message"] = "Model n'existe pas";
                $respObjects["codeStatut"] = "ERREUR";
            }
        }catch(\Exception $e){
            $result = "Une erreur s'est produite".$e->getMessage();
            $respObjects["message"] = $result;
        }
        return $this->json($respObjects);
    }
    #[Route('/competences/detailsCompetence')]
    public function detailsCompetence(competenceRepo $competenceRepo ,activityRepo $activityRepo, Request $request): JsonResponse
    {
        $respObjects = array();
        $codeStatut = "ERROR";
        // $this->AuthService->checkAuth(0,$request);
        $id = $request->get("id");
        if(trim($id) != ""){
            $competence = $competenceRepo->findModel($id);
            dump($competence);
            if($competence){
                $codeStatut = "OK";
                $respObjects["data"]["competence"] = $competence;
                //Get list param
                $param_activite = $activityRepo->getParamsActivity();dump($id);
                // $param_list = array();
                // for($j = 0 ; $j < count($param_activite) ;$j++){
                //     $param_list[$j]["param"] = $param_activite[$j];
                //     $id_param = $param_list[$j]["param"]->getId();
                //     $id_competence = $competence->getId();
                //     $sql="select * from param_activite p where id =".$id_param." and  p.id in (SELECT interm.id_param_id  from detail_competence interm WHERE interm.id_competence_id = ".$id_competence."); ";
                //     $stmt = $this->conn->prepare($sql);
                //     $stmt = $stmt->executeQuery();
                //     $resulatList = $stmt->fetchAllAssociative();
                //     if($resulatList){
                //         $param_list[$j]["check"] =true;
                //     }else{
                //         $param_list[$j]["check"] =false;
                //     }
                // }
                // $familles = [];
                // $comp = $competenceRepo->getFamilles($id_competence);
                // for ($i=0; $i < count($comp); $i++) { 
                //     $familles[$i] = $comp[$i]->getIdFamille();
                // }
                // $respObjects["data"]["familles"] = $familles;
                // $respObjects["data"]["famillesNotSelected"] = $competenceRepo->getCompetencesNotSelected($id_competence);
                // $respObjects["data"]["sousFamillesSelected"] = $competenceRepo->getSousFamillesSelected($id_competence);
                

                $respObjects["data"]["params"]= $this->em->getRepository(DetailCompetence::class)->findBy(['id_competence'=>$id]);;
            }else{
                $codeStatut = "NOT_EXIST_M";
            }
        }else{
            $codeStatut = "EMPTY-DATA";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/competences/addGroupeCompetence',methods: ['POST'])]
    public function addGroupeCompetence(competenceRepo $competenceRepo , Request $request ,activityRepo $activityRepo ): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);
        $codeStatut = "ERROR";
        $respObjects = array();
        $listParams = $activityRepo->getParamsActivity();
        $data = json_decode($request->getContent() , true);
        $titre = $data["titre"];
        $activites = $data["data"];

        $dataDetails = $data["dataDetails"];

        
        if($titre != '' && !empty($titre) && count($data) >= 1){
            $competence = $competenceRepo->createGroupeConpetence($titre);
            if($competence != null){
                foreach ($activites as $value) {
                    // $activityRepo = $activityRepo->findParentActivity($value);
                    $typeParametrage = $activityRepo->getOneTypesOfSParametrages($value);
                    // if($activityRepo != null){
                    //     $competenceRepo->createDetailGroupe($competence , $activityRepo);
                    // }
                    // if($typeParametrage != null){
                        $detailGroupe = $competenceRepo->createDetailGroupe($competence , $typeParametrage);
                    // }
                    for ($i=0; $i < count($dataDetails) ; $i++) { 
                        if($dataDetails[$i]['id'] == $value){
                            for ($j=0; $j < count($dataDetails[$i]['params']); $j++) { 
                                $param = $activityRepo->getOneParam($dataDetails[$i]['params'][$j]);
                                $sousDetailGroupe = $competenceRepo->createSousDetailGroupe($detailGroupe , $param);
                            }
                        }
                    }
                }
                $codeStatut="OK";
            }else{
                $codeStatut = "ERROR";
            }
        }else{
            $respObjects["codeStatut"] = "EMPTY-DATA";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/competences/getGroupeCompetence')]
    public function getCompetence(competenceRepo $competenceRepo , Request $request ,activityRepo $activityRepo ): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);
        $codeStatut = "ERROR";
        $respObjects = array();
        try {

            $data = $competenceRepo->getGroupeCompetence();
            $respObjects["data"] = $data;
            $codeStatut = "OK";

        } catch (\Exception $e) {
            $codeStatut = "ERROR";
        }
       
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/competences/deleteGroupeCompetence' , methods: ['POST'])]
    public function deleteGroupeCompetence(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $id = $request->get('id');
            $groupe =  $this->em->getRepository(GroupeCompetence::class)->find($id);
            $checkIf = $this->em->getRepository(DetailCompetence::class)->findOneBy(['id_groupe'=>$id]);
            if(!$checkIf){
                if($groupe)
                {
                    $sousGroupe =  $this->em->getRepository(DetailGroupeCompetence::class)->findBy(['id_groupe'=>$groupe->getId()]);
                    foreach ($sousGroupe as $sous) {
                        $sousSousGroupe =  $this->em->getRepository(SousDetailGroupeCompetence::class)->findBy(['id_detail_groupe_competence'=>$sous->getId()]);
                        foreach ($sousSousGroupe as $sous1) {
                            $this->em->remove($sous1);
                        }
                        $this->em->remove($sous);
                    }
                    $this->em->remove($groupe);
                    $this->em->flush();
    
                    $codeStatut='OK';
                }
            }else{
                $codeStatut='EXIST_COMPETENCE';
            }
            
        }catch(\Exception $e){
        $respObjects["error"] = $e->getMessage();
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/competences/getOneGroupeCompetence')]
    public function getOneGroupeCompetence(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        // try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get('id');
            $result =  $this->em->getRepository(GroupeCompetence::class)->find($id);
            $sousEtap =  $this->em->getRepository(DetailGroupeCompetence::class)->findBy(['id_groupe'=>$result->getId()]);

            $details = [];
            $index = 0;
            foreach ($sousEtap as $value) {
                $details[$index]['id'] = $value->getIdFamille()->getId();
                $details1 =  $this->em->getRepository(SousDetailGroupeCompetence::class)->findBy(['id_detail_groupe_competence'=>$value->getId()]);
                for ($j=0; $j <count($details1); $j++) { 
                    $details[$index]['params'][$j] = $details1[$j]->getIdParam()->getId();
                    # code...
                }
                $index ++;
            }
            $sousEtap =  $this->em->getRepository(DetailGroupeCompetence::class)->findBy(['id_groupe'=>$result->getId()]);
            $data = [
                "titre"=>$result->getTitre(),
                "detailGroupeCompetence"=>$sousEtap,
                "dataDetailGroupeCompetence"=>$details,
            ];
            $respObjects["data"] = $data;
            $codeStatut="OK";
        // }catch(\Exception $e){
        //     $codeStatut="ERROR";
        // }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/competences/updateGroupeCompetence' , methods: ['POST'])]
    public function updateGroupeCompetence(activityRepo $activityRepo ,competenceRepo $competenceRepo, SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $id = $data['id'];
            $etapSelected =  $this->em->getRepository(GroupeCompetence::class)->find($id);
            if($etapSelected)
            {
                if(isset($data['titre'])  && isset($data['data']))
                {
                        
                    $etapSelected->setTitre($data['titre']);
                    $sousEtap =  $this->em->getRepository(DetailGroupeCompetence::class)->findBy(['id_groupe'=>$etapSelected->getId()]);

                    foreach ($sousEtap as $sous) {
                        $sousSousGroupe =  $this->em->getRepository(SousDetailGroupeCompetence::class)->findBy(['id_detail_groupe_competence'=>$sous->getId()]);
                        foreach ($sousSousGroupe as $sous1) {
                            $this->em->remove($sous1);
                        }
                        $this->em->remove($sous);
                    }
                    $this->em->flush();
                    $activites = $data['data'];
                    foreach ($activites as $value) {
                        $error=$value;
                        // $activityRepo = $activityRepo->findParentActivity($value);
                        // $activityRepo = $this->em->getRepository(ActiviteParent::class)->findOneBy(["id"=>$value]);
                        $typeParametrage = $activityRepo->getOneTypesOfSParametrages($value);
                        
                        if($typeParametrage != null){
                            $competenceRepo->createDetailGroupe($etapSelected , $typeParametrage);
                        }
                    }

                    $codeStatut='OK';
                   
                }else{
                    $codeStatut="EMPTY-DATA";
                }
            }
            
        }catch(\Exception $e){
        $respObjects["error"] = $error;
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["error"] = $error;

        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
}
