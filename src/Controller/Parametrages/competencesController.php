<?php

namespace App\Controller\Parametrages;

use App\Entity\DetailCompetence;
use App\Entity\DetailCompetenceFamilles;
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
        $this->AuthService->checkAuth(0,$request);

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
        return $this->json($respObjects);    }
    #[Route('/competences/createCompetence', methods: ['POST'])]
    public function createModel(competenceRepo $competenceRepo ,activityRepo $activityRepo, Request $request): JsonResponse
    {
        $respObjects = array();
        $titre = $request->get("titre");
        $codeStatut = "ERREUR";
        if(trim($titre) != ""  ){
            $comp = $competenceRepo->createModel($titre  );
            if($comp){
                $data = json_decode($request->getContent() , true);
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
                }

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
        if($comp){
            for ($i=0; $i < count($listParams); $i++) { 
                # code...
                $param = $activityRepo->getParamsActivity()[$i];
                if(in_array("".$param->getId()."", $list_check)){
                    $find_interm_param = $this->em->getRepository(DetailCompetence::class)->findOneBy(['id_competence' => $id , 'id_param' => $param->getId() ]);
                    if(!$find_interm_param){
                        $competenceRepo->createDetailModel($param , $comp);
                    }
                }else{
                   $find_interm_param_uncheck = $this->em->getRepository(DetailCompetence::class)->findOneBy(['id_competence' => $id , 'id_param' =>  $param->getId()]);
                    // if($find_interm_param_uncheck){
                        $this->em->remove($find_interm_param_uncheck);
                        $this->em->flush();
                    // }
                }                
            }
            $codeStatut = "OK";
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
                $competenceRepo->deleteModel($id);
                $respObjects["message"] = "success";
                $respObjects["codeStatut"] = "OK";
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
        $id = $request->get("id");
        if(trim($id) != ""){
            $competence = $competenceRepo->findModel($id);
            if($competence){
                $codeStatut = "OK";
                $respObjects["data"]["competence"] = $competence;
                //Get list param
                $param_activite = $activityRepo->getParamsActivity();
                $param_list = array();
                for($j = 0 ; $j < count($param_activite) ;$j++){
                    $param_list[$j]["param"] = $param_activite[$j];
                    $id_param = $param_list[$j]["param"]->getId();
                    $id_competence = $competence->getId();
                    $sql="select * from param_activite p where id =".$id_param." and  p.id in (SELECT interm.id_param_id  from detail_competence interm WHERE interm.id_competence_id = ".$id_competence."); ";
                    $stmt = $this->conn->prepare($sql);
                    $stmt = $stmt->executeQuery();
                    $resulatList = $stmt->fetchAllAssociative();
                    if($resulatList){
                        $param_list[$j]["check"] =true;
                    }else{
                        $param_list[$j]["check"] =false;
                    }
                }
                $respObjects["data"]["params"]= $param_list;
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
}
