<?php

namespace App\Controller\Parametrages;

use App\Entity\CritereModelFacturation;
use App\Entity\DetailCritereModelFacturation;
use App\Entity\RegleModelFacturation;
use App\Repository\Parametrages\facturationRepo;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\DBAL\Connection;

#[Route('/API')]
class facturationController extends AbstractController
{
    private $MessageService;

    public function __construct(
        facturationRepo $facturationRepo,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        MessageService $MessageService,
        Connection $conn
    )
    {
        $this->conn = $conn;
        $this->facturationRepo = $facturationRepo;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->MessageService = $MessageService;
    }
    #[Route('/facturation/listModels', name: 'app_parametrages_affichages_affichage')]
    public function index(facturationRepo $facturationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $data = $facturationRepo->getListModels();
            $codeStatut="OK";
            $respObjects["data"] = $data;
        }catch(\Exception $e){
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    // #[Route('/facturation/createModel', methods: ['POST'])]
    // public function createModel(facturationRepo $facturationRepo , Request $request): JsonResponse
    // {
    //     $respObjects = array();
    //     $codeStatut = "ERROR";
    //     $data = json_decode($request->getContent(), true);
    //     $titre = $data[0]["titre"];
    //     $objet = $data[0]["objet"];
    //     if(trim($titre) != "" && trim($objet) != "" ){
    //         $activityRepo = $facturationRepo->createModel($titre , $objet );
    //         if($activityRepo){
    //             $codeStatut = "OK";
    //             $respObjects["data"] = $activityRepo;
    //         }else{
    //             $codeStatut = "ERROR";
    //         }
    //     }else{
    //         $codeStatut="EMPTY-DATA";
    //     }
    //     $respObjects["codeStatut"] = $codeStatut;
    //     $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
    //     return $this->json($respObjects);
    // }
    #[Route('/facturation/updateModel', methods: ['POST'])]
    public function updateModel(facturationRepo $facturationRepo , Request $request): JsonResponse
    {
        $respObjects = array();
        $titre = $request->get("titre");
        $objet = $request->get("objet");
        $id = $request->get("id");
        $findModel = $facturationRepo->findModel($id);
        if($findModel){
            if(trim($titre) != "" && trim($objet) ){
                $activityRepo = $facturationRepo->updateModel($titre , $objet , $id);
                if($activityRepo){
                    $respObjects["message"] = "success";
                }else{
                    $respObjects["message"] = "une erreur s'est produite !";
                }
            }else{
                $respObjects["message"] = "Un champ oubliguatoire est vide !";
            }
        }else{
            $respObjects["message"] = "Model n'existe pas";
        }
        return $this->json($respObjects);
    }
    #[Route('/facturation/deleteModel')]
    public function deleteModel(facturationRepo $facturationRepo , Request $request): JsonResponse
    {
        $respObjects = array();
        try{
            $id = $request->get("id");
            $data = $facturationRepo->findModel($id);
            if($data){
                $facturationRepo->deleteModel($id);
                $respObjects["message"] = "success";
            }else{
                $respObjects["message"] = "Model n'existe pas";
            }
        }catch(\Exception $e){
            $result = "Une erreur s'est produite".$e->getMessage();
            $respObjects["message"] = $result;
        }
        return $this->json($respObjects);
    }  

    #[Route('/facturation/createModel', methods: ['POST'])]
    public function createDetailModel(facturationRepo $facturationRepo , Request $request): JsonResponse
    {
        $respObjects = array();
        $codeStatut = "ERROR";
        try {
        $titre_model = $request->get("titre");
        $objet_model = $request->get("objet");
        $data_list = json_decode($request->getContent(), true);
        if(trim($titre_model) != "" ){
            $model = $facturationRepo->createModel($titre_model , $objet_model );
            if($model){
                if($data_list){
                    for ($i=0; $i < count($data_list); $i++) { 
                        # code...
                        $titre = $data_list[$i]["titre"];
                        $regle = $facturationRepo->createRegle($titre  , $model);
                        $criteres = $data_list[$i]["criteres"];
                        for ($j=0; $j < count($criteres); $j++) { 
                            # code...
                            $table_critere =  $criteres[$j]["table_name"];
                            $column_critere = $criteres[$j]["column_name"];
                            $action_critere = $criteres[$j]["action_name"];
                            $valeur1_critere = $criteres[$j]["valeur1"];
                            $valeur2_critere = $criteres[$j]["valeur2"];
                            $operator = "";
                            if($j>0){
                                $operator = $criteres[$j]["operator"];
                            }
                            $type_critere = $criteres[$j]["type"];
                            $critere = $facturationRepo->createCritere($table_critere,$column_critere,$action_critere,$valeur1_critere,$valeur2_critere,$type_critere,$operator,$regle);

                            //Add details search inputs
                            $details_search = $criteres[$j]["details_multiple_search"];
                            for ($dt=0; $dt < count($details_search) ; $dt++) { 
                                # code...
                                if($details_search[$dt]["isChecked"] == true){
                                    $detail_critere = $facturationRepo->createDetailCritere($table_critere,$column_critere,"like",$valeur1_critere,$valeur2_critere,$type_critere,$critere,"multiple","AND");
                                }
                            }
                            $details_criteres = $criteres[$j]["details_critere"];
                            for ($k=0; $k < count($details_criteres) ; $k++) { 
                                # code...
                                $column_critere = $details_criteres[$k]["column_name"];
                                $action_critere = $details_criteres[$k]["action_name"];
                                $valeur1_critere = $details_criteres[$k]["valeur1"];
                                $valeur2_critere = $details_criteres[$k]["valeur2"];
                                $type_critere = $details_criteres[$k]["type"];
                                $type_detail = "detail";
                                $operator_detail = $details_criteres[$k]["operator"];
                                $detail_critere = $facturationRepo->createDetailCritere($table_critere,$column_critere,$action_critere,$valeur1_critere,$valeur2_critere,$type_critere,$critere,$type_detail,$operator_detail);
                            }
                        }
                    }
                    $codeStatut = "OK";
                }else{
                    $codeStatut = "ERROR";
                }
                }else{
                    $codeStatut = "ERROR";
                }
        }else{
            $codeStatut="EMPTY-DATA";
        }
        } catch (\Exception $e) {
            $respObjects["err"]=$e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/facturation/getModelDetails', methods: ['GET'])]
    public function getModels(facturationRepo $facturationRepo , Request $request): JsonResponse
    {
        $codeStatut = "ERROR";
        $respObjects = array();
        $id = $request->get("id");
        $findModel = $facturationRepo->findModel($id);
        if($findModel){
            $data = $facturationRepo->getModelDetails($id);
            $respObjects["data"] = $data;
            $codeStatut = "OK";
        }else{
            $codeStatut = "NOT_EXIST_M";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/facturation/deleteCritere')]
    public function deleteCritere(facturationRepo $facturationRepo , Request $request): JsonResponse
    {
        $respObjects = array();
        try{
            $id = $request->get("id");
            $data = $facturationRepo->findCritere($id);
            if($data){
                $facturationRepo->deleteCritere($id);
                $respObjects["message"] = "success";
                $respObjects["codeStatut"] = "OK";
            }else{
                $respObjects["message"] = "Critere n'existe pas";
                $respObjects["codeStatut"] = "NOT_EXIST";
            }
        }catch(\Exception $e){
            $result = "Une erreur s'est produite".$e->getMessage();
            $respObjects["message"] = $result;
        }
        return $this->json($respObjects);
    }
    #[Route('/facturation/deleteRegle')]
    public function deleteRegle(facturationRepo $facturationRepo , Request $request): JsonResponse
    {
        $respObjects = array();
        try{
            $id = $request->get("id");
            $data = $facturationRepo->findRegle($id);
            if($data){
                $facturationRepo->deleteRegle($id);
                $respObjects["message"] = "success";
                $respObjects["codeStatut"] = "OK";
            }else{
                $respObjects["message"] = "Regle n'existe pas";
                $respObjects["codeStatut"] = "NOT_EXIST";
            }
        }catch(\Exception $e){
            $result = "Une erreur s'est produite".$e->getMessage();
            $respObjects["message"] = $result;
        }
        return $this->json($respObjects);
    }
    #[Route('/facturation/updateRegleWithDetails')]
    public function updateRegleWithDetails(facturationRepo $facturationRepo , Request $request): JsonResponse
    {
        $respObjects = array();
        try{
            $id = $request->get("id");
            $data = $facturationRepo->findModel($id);
            if($data){
                $regles_facturation = $facturationRepo->findRegleByModel($id);
                 //----Update regle déja exist
                foreach($regles_facturation as $rg){
                    $i = $rg->getId();
                    $table_critere_exist = json_decode($request->get("table_regle_exist".$i));
                    $column_critere_exist = json_decode($request->get("column_regle_exist".$i));
                    $action_critere_exist = json_decode($request->get("action_regle_exist".$i));

                    $valeur1_critere_exist = json_decode($request->get("valeur1_exist".$i));
                    $valeur2_critere_exist = json_decode($request->get("valeur2_exist".$i));
                    if($table_critere_exist){
                        for($j = 0 ; $j < count($table_critere_exist) ; $j++){
                            $critere = $facturationRepo->createCritere($table_critere_exist[$j],$column_critere_exist[$j],$action_critere_exist[$j],$valeur1_critere_exist[$j],$valeur2_critere_exist[$j],$rg);
                        }
                    }
                }

                //Add new regle
                $regles = $request->get("regles");
                $regles = json_decode($request->get("regles"));
                $i=1;
                foreach($regles as $r){
                    $regle = $facturationRepo->createRegle($r , $data);
                    $table_critere = json_decode($request->get("table".$i),true);
                    $column_critere = json_decode($request->get("column".$i),true);
                    $action_critere = json_decode($request->get("action".$i),true);
                    $valeur1_critere = json_decode($request->get("valeur1".$i),true);
                    $valeur2_critere = json_decode($request->get("valeur2".$i),true);
                    if($table_critere){
                        for($j = 0 ; $j < count($table_critere) ; $j++){
                            $valeur2 = "";
                            if($valeur2_critere){
                                if (array_key_exists($j,$valeur2_critere)){
                                    $valeur2 = $valeur2_critere[$j];
                                }
                            }
                            $critere = $facturationRepo->createCritere($table_critere[$j],$column_critere[$j],$action_critere[$j],$valeur1_critere[$j],$valeur2,$regle);
                        }
                     }
                    $i++;
                }
            }else{
                $respObjects["message"] = "Regle n'existe pas";
                $respObjects["codeStatut"] = "NOT_EXIST";
            }
        }catch(\Exception $e){
            $result = "Une erreur s'est produite".$e->getMessage();
            $respObjects["message"] = $result;
        }
        return $this->json($respObjects);
    }
    #[Route('/facturation/search_value_critere')]
        
    public function search_value_critere(facturationRepo $facturationRepo , Request $request): JsonResponse
    {
        $respObjects = array();
        try {
            //code...
            $table = trim($request->get('table_select'));
            $column = trim($request->get('column_select'));
            $value = trim($request->get('value_search'));
    
            $sql="select * from ".$table." where ".$column." = '".$value."'  ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $resulatList = $stmt->fetchAll();
            $respObjects["data"]=$resulatList;
            $codeStatut="OK";

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        $respObjects["err"]=$e->getMessage();
        }
        $respObjects["codeStatut"]=$codeStatut;
        return $this->json($respObjects);
    }   
}
