<?php

namespace App\Controller\Workflow;

use App\Entity\EvenementWorkflow;
use App\Entity\Workflow;
use App\Repository\Workflow\workflowRepo;
use App\Repository\Sgementaion\segementationRepo;
use App\Service\AuthService;
use App\Service\GeneralService;
use App\Service\MessageService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\DataWorkflow;
use App\Entity\IntermWorkflowSegmentation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Utilisateurs;
use App\Entity\EventAction;

#[Route('/API/workflow')]
class WorkflowController extends AbstractController
{
    private $MessageService;
    private $GeneralService;
    private $AuthService;
    public $workflowRepo;
    private $conn;
    public $em;

    public $segementationRepo;


    public function __construct(
        AuthService $AuthService,
        workflowRepo $workflowRepo,
        segementationRepo $segementationRepo,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        Connection $conn,
        MessageService $MessageService,
        GeneralService $GeneralService,
        )
        {
        $this->em = $em;
        $this->segementationRepo = $segementationRepo;
        $this->workflowRepo = $workflowRepo;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->AuthService = $AuthService;
        $this->MessageService = $MessageService;
        $this->conn = $conn;
        $this->GeneralService = $GeneralService;
    }
    #[Route('/getListeWorkflow')]
    public function listeWorkflow(Request $request,workflowRepo $workflowRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);

            $data = $workflowRepo->getListeWorkflow();
            $codeStatut = "OK";
            $respObjects["data"] = $data;
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getListeSegmentByType')]
    public function getListeSegmentByType(Request $request,workflowRepo $workflowRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $type = $request->get("type");
            if($type == "" || !$type){
                $codeStatut="EMPTY-DATA";
            }else{
                $data = $workflowRepo->getListeSegmentByType($type);
                $codeStatut = "OK";
                $respObjects["data"] = $data;
            }
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getListeObjet')]

    public function getListeObjet(Request $request,workflowRepo $workflowRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = $workflowRepo->getListeObjet();
            $codeStatut = "OK";
            $respObjects["data"] = $data;
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    #[Route('/getDetailListeObjet')]
    public function getDetailListeObjet(Request $request,workflowRepo $workflowRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id=$request->get("id");
            $findObje = $this->workflowRepo->findOneObjectById($id);
            if(!$findObje){
                $codeStatut="NOT_EXIST_ELEMENT";
            }else{
                $data = $workflowRepo->getDetailListeObjet($id);
                $codeStatut = "OK";
                $respObjects["data"] = $data;
            }
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/createWorkflow' , methods : ["POST"])]
    public function createWorkflow(Request $request , workflowRepo $workflowRepo ,segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $titre = $data['titre'];
            $type_select = $data['type_select'];
            $arraySegmentation = $data['arraySegmentation'];
            $notes = $data['notes'];

            if($titre == "" || $type_select == 0 || count($arraySegmentation) == 0)
            {
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }else{
                $userId = $this->AuthService->returnUserId($request);
                $user = $this->em->getRepository(Utilisateurs::class)->findOneBy(["id"=>$userId]);
                
                $checkSg = false;
                for ($i=0; $i < count($data['arraySegmentation']); $i++) { 
                    $checkSegmentation = $workflowRepo->checkSegmentation($data['arraySegmentation'][$i] , $type_select);
                    
                    if($checkSegmentation ){
                        $checkSg = true;
                    }
                }

                if($checkSg){

                    $workflow = $workflowRepo->createWorkflow($titre ,$user,$type_select);
                    for ($i=0; $i < count($data['arraySegmentation']); $i++) { 
                        $intermSegWork = $this->em->getRepository(IntermWorkflowSegmentation::class)->findOneBy(["id_workflow"=>null , "id_segmentaion"=>$data['arraySegmentation'] , "id_type" =>$type_select]);
                        $intermSegWork->setIdWorkflow($workflow);
                        $this->em->flush();
                    }

                    if($notes != ""){
                        $workflowRepo->createNoteWorkflow($workflow , $notes);
                    }
                    $respObjects["data"]["id"] = $workflow->getId();
                    $codeStatut = "OK";

                }else{
                    $codeStatut="ERROR-SEG1";
                }
            }
            
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getWorkflow/{id}' )]
    public function getWorkflow(Request $request ,$id, workflowRepo $workflowRepo ,segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            // $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);

            $workflow = $workflowRepo->getWorkflow2($id);
            if($workflow)
            {
                $respObjects['data'] = $workflow;
                $codeStatut="OK";
            }else{
                $codeStatut="NOT_EXIST";
            }
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    public function getWorkflowDetails(Request $request ,$id, workflowRepo $workflowRepo ,segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);

            $workflow = $workflowRepo->getWorkflowDetails($id);
            if($workflow)
            {
                $respObjects['data'] = $workflow;
                $codeStatut="OK";
            }else{
                $codeStatut="NOT_EXIST";
            }
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    #[Route('/generateWorkflowOld' , methods : ["POST"])]
    public function generateWorkflowOld(Request $request , segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            //Create workflow : 
            $titre = $data["details"]["titre"];
            $description = $data["details"]["description"];
            $id_segment = $segementationRepo->findSegment($data["details"]["id_segmentation"]);

            $user = $this->workflowRepo->findOneUser(1);
            $workflow = $this->workflowRepo->createWorkflow($titre , $description , $id_segment,$user);
            $data_workflow = $data["sequence"];
            foreach ($data_workflow as $value) {
                $componentType = $value["componentType"];
                $uid = $value["id"];
                $id_object = $value["id_object"];

                $this->workflowRepo->createObject($workflow->getId() , $componentType , $uid , $id_object);

                if(isset($value["branches"])){
                    if(isset($value["branches"]["true"])){ 
                        $this->buildArray($value["branches"]["true"] , $workflow->getId() );
                    }
                    if(isset($value["branches"]["false"])){
                        $this->buildArray($value["branches"]["false"] ,$workflow->getId() );
                    }
                }
            }
            
            $scenario_workflow = $data["scenario"];
            foreach ($scenario_workflow as $value) {
                $scenario = $this->workflowRepo->createSenario($workflow);
                $details_scenario = $value["details"];
                $position = 1;
                foreach ($details_scenario as $scenario_details) {
                    $object_senario = $this->workflowRepo->findOneObject($scenario_details);
                    $createObjectMapp = $this->workflowRepo->createScenarioMapp($scenario,$object_senario,$position);
                    $position ++;
                }
            }

            $mapping_workflow = $data["mapping"];
            foreach ($mapping_workflow as $value) {
                $uid_from =$this->workflowRepo->findOneObject($value["from"]); 
                $uid_to =$this->workflowRepo->findOneObject($value["to"]); 
                $createObjectMapp = $this->workflowRepo->createObjectConnection($uid_from,$uid_to);
            }
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    public function buildArray($array , $id_workflow ){
        $arrayLoop = [
            "array" => ""
        ];

        foreach ($array as $value) {
            $componentType = $value["componentType"];
            $uid = $value["id"];
            $id_object = $value["id_object"];

            $this->workflowRepo->createObject($id_workflow, $componentType , $uid , $id_object);
            if(isset($value["branches"])){
                if(isset($value["branches"]["true"])){
                    $this->buildArray($value["branches"]["true"],$id_workflow);
                }
                if(isset($value["branches"]["false"])){
                    $this->buildArray($value["branches"]["false"],$id_workflow);
                }
            }
        }
        
        return $arrayLoop;
    }
    #[Route('/generateWorkflow',methods : ["POST"])]
    public function generateWorkflow(Request $request , segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $id_user = $this->AuthService->returnUserId($request);

            //Create workflow : 
            $titre = $data["details"]["titre"];
            $description = $data["details"]["description"];
            $segmentation = $data["details"]["segmentation"];
            $type_workflow = $data["details"]["type_workflow"];

            if($titre != "" && $description != "" && $type_workflow != "" && count($segmentation) >= 1){
                // if($type_workflow == 1 ||  $type_workflow == 2 ){
                    $response="";
                    foreach ($segmentation as $segment) {
                        $s = $this->workflowRepo->findSegment($segment , $type_workflow);
                        if(!$s){
                            $response="ERROR";
                            break;
                        }
                    }

                    if($response == ""){
                        $user = $this->workflowRepo->findOneUser($id_user);
                        $workflow = $this->workflowRepo->createWorkflow($titre , $user,$type_workflow);
                        $note_workflow = $this->workflowRepo->createNoteWorkflow($workflow , $description);
                        foreach ($segmentation as $segment) {
                            $s = $this->workflowRepo->findSegment($segment , $type_workflow);
                            $interm = $this->workflowRepo->createIntermSegWorkflow($workflow , $s);
                        }
                        $codeStatut="OK";
                    }
                    
                // }else if($type_workflow == 3){
                //     if(count($segmentation) > 1){
                //         //tt
                //     }else{
                //         $codeStatut="ERROR_SEGMENTATION";
                //     }
                // }
            }else{
                $codeStatut="EMPTY-DATA";
            }
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/start-workflow',methods : ["POST"])]
    public function startWorkflow(Request $request , segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $id_user = $this->AuthService->returnUserId($request);
            
            //Create workflow : 
            $data_req = $data["sequence"];
            $i=0;
            foreach ($data_req as $value) {
                $i++;
            }
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/create-event-based',methods : ["POST"])]
    public function createEvenetBased(Request $request , segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $id=$request->get("id");
            $workflow = $this->workflowRepo->getWorkflow($id);
            if($workflow){
                for ($i=0; $i < count($data); $i++) { 
                    $event_based = $this->workflowRepo->createEventBased($workflow);
    
                    $detialId = $data[$i]["event_select"];
                    $detailObjet = $this->workflowRepo->getDeailObject($detialId);

                    if($detailObjet){
                        $this->workflowRepo->createEventBasedSelect($event_based , $detailObjet);
                    }

                    $detailsCheck = $data[$i]["event_check"];
                    for ($j=0; $j < count($detailsCheck) ; $j++) { 
                        $detailObjet = $this->workflowRepo->getDeailObject($detailsCheck[$j]);
                        $this->workflowRepo->createEventBasedCheck($event_based , $detailObjet);
                        $codeStatut="OK";
                    }
                }
            }else{
                $codeStatut="NOT_EXIST_ELEMENT";
            }
            
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/check-event-based',methods : ["POST"])]
    public function checkEvenetBased(Request $request , segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $id=$request->get("id");
            $workflow = $this->workflowRepo->getWorkflow($id);
            if($workflow){
                $id_detail_select = $data["detail_select"];
                $id_detail_check = $data["detail_check"];
                $sql="SELECT * from event_select_child where id=3 and id_event_based_decision_id=4 and id_event_based_decision_id in (select s.id_event_based_decision_id from event_select s where 
                s.id=3)";
                $stmt = $this->conn->prepare($sql);
                // $stmt->bindValue(":id",$id);
                $stmt = $stmt->executeQuery();
                $statut = $stmt->fetchOne();
            }else{
                $codeStatut="NOT_EXIST_ELEMENT";
            }
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/get_liste_event')]
    public function get_liste_event(Request $request , segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $id=$request->get("id");
            $workflow = $this->workflowRepo->getWorkflow($id);
            if($workflow){
                $liste_event = $this->workflowRepo->getListeEvent($id);
                $array_data = array();
                for ($i=0; $i < count($liste_event); $i++) { 
                    $array_data[$i]=$liste_event[$i];
                    $array_data[$i]["event_select"]= $this->workflowRepo->getListeEventSelect($liste_event[$i]["id"]);
                    $array_data[$i]["event_select_child"]= $this->workflowRepo->getListeEventSelectChild($liste_event[$i]["id"]);
                }
                $respObjects["data"] = $array_data;
                $codeStatut="OK";
            }else{
                $codeStatut="NOT_EXIST_ELEMENT";
            }
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/get_liste_critere')]
    public function get_liste_critere(Request $request , segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $id=$request->get("id");
            $liste_groupe = $this->workflowRepo->geListeGroupe();
            $startYear = 2023;
            $endYear = 2030;

            $yearsArray = range($startYear, $endYear);
            $respObjects["data"] = $liste_groupe;
            $respObjects["years"] = $yearsArray;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/get_all_details_event')]
    public function getAllDetailsEvent(Request $request , workflowRepo $workflowRepo,segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR"; 
        try{
            $this->AuthService->checkAuth(0,$request);
            $array = array();
            //Details call customer
            $array["call_customer"]["type_agent"] = $this->workflowRepo->getTypeAgent();
            $array["call_customer"]["type_call"] = $this->workflowRepo->getTypeCall();

            //Details call customer
            $array["send_communicaion"]["type_communication"] = $this->workflowRepo->getTypeSendCommunication();
            //Details approval step
            $array["approval_step"]["type_approval"] = $this->workflowRepo->getTypeApprovalStep();
            $array["campagne"]["typecampagne"] = $this->workflowRepo->getTypeCampagne();
            $array["assign_externe"] =  $this->workflowRepo->getTypeAgent();

            $respObjects["data"] =$array;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getModelsCampagne')]
    public function getModelsCampagne(Request $request , workflowRepo $workflowRepo,segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR"; 
        try{
            $this->AuthService->checkAuth(0,$request);
            $array = array();
            $type = $request->get('type');
            $data= $this->workflowRepo->getModelsCampagne($type);
            $respObjects["data"] =$data;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getModelByType')]
    public function getModelByType(Request $request , workflowRepo $workflowRepo,segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR"; 
        try{
            $this->AuthService->checkAuth(0,$request);
            $type = $request->get("type");
            $data = $workflowRepo->getModelByType($type);
            $respObjects["data"] = $data;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    
    #[Route('/saveWorkflow',methods : ["POST"])]
    public function saveWorkflow(Request $request , workflowRepo $workflowRepo,segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR"; 
        // try{
            $this->AuthService->checkAuth(0,$request);
            $array = array();
            $dataRequest = json_decode($request->getContent(), true);
            $id = $request->get('id');
            $workflow = $workflowRepo->getWorkflow($id);
            if(!$workflow){
                $codeStatut="NOT_EXIST";
            }else{
                $data = $dataRequest["data"];
                $dateWorkflow = new DataWorkflow();
                $dateWorkflow->setData($data);
                $dateWorkflow->setIdWorkflow($workflow);
                $this->em->persist($dateWorkflow);
                $this->em->flush();
                //Save events
                $this->processWorkflowData($data , $id);
    
                $idEvent = $this->workflowRepo->getFirstEvent($id);
                //Save detail queue
                $this->workflowRepo->saveQueueWorkflow($id , $idEvent);
                // $this->saveSplitQueue($data,$id);
                
                $this->saveSplitQueue($dataRequest["data"], $id , $dataRequest["dataSplit"]);

                // $workflow = $this->workflowRepo->updateStatutWorkflow($id, 2);
                $codeStatut="OK";
            }
        // }catch(\Exception $e){
        //     $codeStatut = "ERREUR";
        //     $respObjects["err"] = $e->getMessage();
        // }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    private function processWorkflowData(array $components , $id)
    {
        foreach ($components as $component) {
            if($component['name'] != "Stop" && $component['name'] != "Start" ){
                $this->createEventAction($component , $id );
            }
            $this->processComponent($component , $id);
        }
    }

    private function processComponent(array $component , $id)
    {
        if (isset($component['branches']) && is_array($component['branches'])) {
            foreach ($component['branches'] as $branchName => $branchComponents) {
                foreach ($branchComponents as $branchComponent) {
                    if($branchComponent['name'] != "Stop" && $branchComponent['name'] != "Start" ){
                        $this->createEventAction($branchComponent , $id );
                    }
                    $this->processComponent($branchComponent , $id);
                }
            }
        }
    }

    #[Route('/saveSplitData',methods : ["POST"])]
    public function saveSplitData(Request $request , workflowRepo $workflowRepo,segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR"; 
        try{
            $this->AuthService->checkAuth(0,$request);
            $array = array();
            $data = json_decode($request->getContent(), true);
            $id = $request->get('id');
            $workflow = $workflowRepo->getWorkflow($id);
            if(!$workflow){
                $codeStatut="NOT_EXIST";
            }else{
                $data = $workflowRepo->getDataWorkflow($id);
                $workflowData = $data->getData();
               // $this->saveSplitQueue($workflowData , $id);
                $codeStatut="OK";
            }
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    private function saveSplitQueue(array $components, $id , $dataSplit)
    {
        foreach ($components as $component) {
            if ($component['id_element'] == "Split flow step") {
                foreach ($component['branches'] as $key => $branch) {
                    foreach ($branch as $branchComponent) {
                        if ($branchComponent['id_element'] == "Split flow step" ) {
                            dump($component['branches'][$key][0]['id']);
                            /* Save queue from data */
                            //Code **
                            
                        } else {
                            $split = $this->workflowRepo->getEvenmentWorkflow2($component['id'], $id);
                            $QueSplit = $this->workflowRepo->addQueueSplit($split->getId(), $key, $component['id']);
                            /* Save queue from origine data */
                            //Code **
                            // dump($branchComponent);
                            // dump($dataSplit[]);
                            $result = null;
                            
                            foreach ($dataSplit as $item) {
                                if ($item['id'] === $component['id']) {
                                    $result = $item;
                                    break;
                                }
                            }
                            $this->addDataCritere($QueSplit , $result['data'][0]['data']);
                        }
                    }
                }
            }
            $this->processComponent2($component, $id, $components);
        }
    }
    
    private function processComponent2(array $component, $id, array $workflowData)
    {
        if (isset($component['branches']) && is_array($component['branches'])) {
            foreach ($component['branches'] as $branchName => $branchComponents) {
                foreach ($branchComponents as $branchComponent) {
                    if ($branchComponent['id_element'] == "Split flow step") {
                        foreach ($branchComponent['branches'] as $key => $branch) {
                            if ($branch['id_element'] == "Split flow step" ) {
                                dump($branchComponent['branches'][$key][0]['id']);
                                /* Save queue from data */
                                //Code **
                            } else {
                                dump($key);
                                $split = $this->workflowRepo->getEvenmentWorkflow2($branchComponent['id'], $id);
                                $QueSplit = $this->workflowRepo->addQueueSplit($split->getId(), $key, $branchComponent['id']);
                                /* Save queue from origin data */
                                //Code **
                            }
                        }
                    }
                    $this->processComponent2($branchComponent, $id, $workflowData);
                }
            }
        }
    }

    private function saveQueueDataFromOrigin(){

    }
    
    private function saveQueueDataFromSplit(){
        
    }

    private function addDataCritere($splitQueueId,$data_critere){
        $codeStatut = "ERROR";
        for ($i=0; $i < count($data_critere); $i++) { 
            $titre_groupe = $data_critere[$i]["groupe"]["titre_groupe"];
            $createGroupeQueue = $this->segementationRepo->createGroupeCritereRepoSplit($titre_groupe,$splitQueueId , null);
            $critere = $data_critere[$i]["critere"];
            for ($j=0; $j < count($critere); $j++) { 
                $createQueueCritere = $this->segementationRepo->createCritereSplit($critere[$j]["critere"] , $createGroupeQueue , $critere[$j]["type"]);
                if($critere[$j]["type"] == 'multiple_check'){
                    $values = $critere[$j]['values'];
                    for ($v=0; $v < count($values) ; $v++) {
                        if(isset($values[$v]["selected"]) && $values[$v]["selected"] == true ){
                            /*$inArray = false;
                            for ($a=0; $a < count($arrayMultiple); $a++) {
                                if($arrayMultiple[$a]['id'] == 888){
                                        $inArray = true;
                                        break;
                                }
                            }*/
                            //Si pour il value n'exist ce forme string like type_persone il faut suvguarde value not id_champ
                            if( $values[$v]["id_critere_id"] == 1 || 
                                $values[$v]["id_critere_id"] == 17 || 
                                $values[$v]["id_critere_id"] == 6 || 
                                $values[$v]["id_critere_id"] == 7 || 
                                $values[$v]["id_critere_id"] == 11 || 
                                $values[$v]["id_critere_id"] == 12 || 
                                $values[$v]["id_critere_id"] == 14 || 
                                $values[$v]["id_critere_id"] == 15 ||
                                $values[$v]["id_critere_id"] == 3 ||
                                $values[$v]["id_critere_id"] == 17 ||
                                $values[$v]["id_critere_id"] == 20 ||
                                $values[$v]["id_critere_id"] == 22 ||
                                $values[$v]["id_critere_id"] == 25 ||
                                $values[$v]["id_critere_id"] == 26 ||
                                $values[$v]["id_critere_id"] == 27 ||
                                $values[$v]["id_critere_id"] == 32 ||
                                $values[$v]["id_critere_id"] == 35  ||
                                $values[$v]["id_critere_id"] == 38 || 
                                $values[$v]["id_critere_id"] == 41 ||
                                $values[$v]["id_critere_id"] == 42 ||
                                $values[$v]["id_critere_id"] == 44 
                                )
                            {
                                $value1 =  $values[$v]["id_champ"];
                                $this->segementationRepo->createValuesSplit($value1 , '' , $createQueueCritere->getId(),null , $values[$v]["value"]);
                            }
                            else
                            {
                                $value1 =  $values[$v]["value"];
                                $this->segementationRepo->createValuesSplit($value1 , '' , $createQueueCritere->getId(),null , $values[$v]["value"]);
                            }
                        }
                    }
                }
                 
                if(isset($critere[$j]) && isset($critere[$j]["type"]) && ($critere[$j]["type"] == 'montant' || $critere[$j]["type"] == 'drop_down' )) {
                    $values = $critere[$j]['values'];
                    $action = $critere[$j]['action'] ;
                    for ($q=0; $q < count($values) ; $q++) {
                        $value1 =  $values["value1"];
                        $value2 =  $values["value2"] ?? "";
                    }
                    $this->segementationRepo->createSegValues($value1 , $value2 , $createQueueCritere->getId(),$action,null);
                }

                if(isset($critere[$j]) && isset($critere[$j]["type"]) && $critere[$j]["type"] == 'date') {
                    $values = $critere[$j]['values'];
                    $action = $critere[$j]['action'] ;
                    for ($q=0; $q < count($values) ; $q++) {
                        $value1 =  $values["value1"];
                        $value2 =  $values["value2"] ?? "";
                    }
                    $this->segementationRepo->createSegValues($value1 , $value2 , $createQueueCritere->getId(),$action,null);
                }

            }
            // $priority ++;
            $codeStatut="OK";
        }
        return $codeStatut = "OK";
    }
    
    private function isSplitStep(array $components, $componentId)
    {
        foreach ($components as $component) {
            if ($component['id'] === $componentId && $component['id_element'] === 'Split flow step') {
                return true;
            }
        }
        return false;
    }

    public function createEventAction($branchComponent , $id ){
        $id_workflow = $this->em->getRepository(Workflow::class)->findOneBy(array("id"=>$id));
        $entity = new EventAction();
        $entity->setIdWorkflow($id_workflow);
        $entity->setCle($branchComponent['id']);
        $type = 1;
        
        $delay = 0;
        if($branchComponent['id_element'] == 'delay'){
            $type = 3;
            $number_of_days =  $branchComponent['name']; 
            // Use a regular expression to extract the number
            preg_match('/\d+/', $number_of_days, $matches);
            $delay = intval($matches[0]);
        }
        elseif ($branchComponent['id_element'] == 'Split flow step') {
            $type = 2;
        }else if($branchComponent['id_element'] == 'Decision step'){
            $type = 4;
            $entity->setIdActivityP($branchComponent['activityDecision']);

            

        }

        $entity->setType($type);
        $event_workflow = null;
        if($type == 1){
            $event_workflow = $this->em->getRepository(EvenementWorkflow::class)->findOneBy(array("event"=>$branchComponent['id_element']));
        }
        $entity->setIdEvent($event_workflow);
        $entity->setDelayAction($delay);
        $this->em->persist($entity);
        $this->em->flush();
        if($type == 4){
            $getResultsByParent = $this->workflowRepo->getResultsByParent($branchComponent['activityDecision']);
            for ($i=0; $i < count($getResultsByParent); $i++) { 
                $this->workflowRepo->saveDetailAction($entity->getId() , $getResultsByParent[$i]['id'] );
            }
        }
    }
    
    #[Route('/workflowProccess',methods : ["POST"])]
    public function workflowProccess(Request $request , workflowRepo $workflowRepo,segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR"; 
        try{
            $workflowListe = $workflowRepo->getWorkflowForProcess();
            foreach ($workflowListe as $workflow) {
                $id = $workflow->getId();
                $statut = $workflow->getIdStatus()->getId();
                if($statut == 3 || $statut == 1){
                    /** Start process if not start */
                    if($statut == 3){
                        $dateDay = new \DateTime();
                        $dateDay = $dateDay->format("Y-m-d");
                        $dateWorkflow = $workflow->getDateStart()->format("Y-m-d");
                        if($dateDay == $dateWorkflow){
                            $workflowRepo->updateStatutWorkflow($id , 1);
                        }
                    }

                    //Récuperer les actions que il a aucune action 
                    $listQueue = $workflowRepo->getQueueEvent($id); 
                    $checkIfUserLibre = $workflowRepo->checkIfUserLibre(); 
                    for ($i=0; $i < count($listQueue); $i++) {
                        $eventDetails = $workflowRepo->getEvenmentWorkflow($listQueue[$i]['id_event_action_id']);
                        if($eventDetails['is_system'] == 1){
                            //Sauvguarde d'une place pour  
                        }else{
                            if(!$checkIfUserLibre){
                                $workflowRepo->addHistoriqueWorkflow($id , "Aucun utilisateur disponible !");
                            }else{
                                $userLibre = $workflowRepo->getUserLibre();

                                $workflowRepo->assignTask($userLibre , $listQueue[$i]['id'] , 2);
                                $workflowRepo->updateStatutQueueEvent( $listQueue[$i]['id'], 3);
                            }
                        }
                    }
                }
            }
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/approbationEvent',methods : ["POST"])]
    public function approbationEvent(Request $request , workflowRepo $workflowRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR"; 
        try{
            $this->AuthService->checkAuth(0,$request);
            $idEventQueue = $request->get('idEventQueue');
            $idWorkflow =  $request->get('idWorkflow');
            $eventQueue = $workflowRepo->getEventByWorkflow($idEventQueue);
            if($eventQueue->getIdStatut()->getId() == 3){
                $data = $workflowRepo->getDataWorkflow($idWorkflow);
                $workflowData = $data->getData();
                $cleEvent = $eventQueue->getIdEventAction()->getCle();
        
                $nextStep = $this->GeneralService->getNextStep($workflowData, $cleEvent);
    
                // dump();
                $evenmentWorkflow = $workflowRepo->getEvenmentWorkflow($eventQueue->getIdEventAction()->getIdEvent()->getId());
                $cle=$eventQueue->getIdEventAction()->getCle();
                $workflowRepo->addHistoriqueQueueEvent($eventQueue->getId() , "L'événement de ".$evenmentWorkflow['titre']." été appliqué." , 1 ,$cle);
    
                if(isset($nextStep['id'])){
                    $actionEvent = $workflowRepo->getEvenmentWorkflow2($nextStep['id'] , $idWorkflow);
                    $eventQueue->setIdEventAction($actionEvent);

                    //Get statuts
                    $statutEvent = $workflowRepo->getStatutQueueEvent(2);

                    //Get status
                    $eventQueue->setIdStatut($statutEvent);
                    $this->em->flush();
                }
    
                $codeStatut="OK";
            }
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    #[Route('/approbationDecision',methods : ["POST"])]
    public function approbationDecision(Request $request , workflowRepo $workflowRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR"; 
        try{
            //Abrovation step 
            $this->AuthService->checkAuth(0,$request);
            $idEventQueue = $request->get('idEventQueue');
            $idWorkflow =  $request->get('idWorkflow');
            //$idEtap =  $request->get('idEtap');
            $eventQueue = $workflowRepo->getEventByWorkflow($idEventQueue);
            // I wil save any add result or etap 
            

            $codeStatut="OK";

        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    
    #[Route('/getAllQueueInDelay',methods : ["POST"])]
    public function getAllQueueInDelay(Request $request , workflowRepo $workflowRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR"; 
        // try{
            $listWorkflow = $workflowRepo->getWorkflowInProcess();
            foreach ($listWorkflow as $workflow ) {
                $queueInDelay = $workflowRepo->getAllQueueInDelay($workflow->getId());
                if(isset($queueInDelay)){
                    foreach ($queueInDelay as $queue) {

                        $eventQueue = $workflowRepo->getEventByWorkflow($queue['id']);
                        $data = $workflowRepo->getDataWorkflow($workflow->getId());
                        
                        $workflowData = $data->getData();
                        $cleEvent = $eventQueue->getIdEventAction()->getCle();
                        $previousStep = $this->GeneralService->getPreviousStep($workflowData, $cleEvent);
                        $delayNumber = $eventQueue->getIdEventAction()->getDelayAction();

                        $dateEventApprov = $workflowRepo->getDateOfApprovEvent($previousStep['id']);
                        
                        if($dateEventApprov){
                            $date = $dateEventApprov['date_action'];
                            $dateSetAction = date('Y-m-d', strtotime($date. ' + '.$delayNumber.'day'));
                            $dateDay = new \DateTime();
                            $dateDay = $dateDay->format('Y-m-d');
                            
                            //If date day
                            if($dateSetAction == $dateDay)
                            {
                                $nextStep = $this->GeneralService->getNextStep($workflowData, $cleEvent);
                                if(isset($nextStep['id'])){
                                    $actionEvent = $workflowRepo->getEvenmentWorkflow2($nextStep['id'] , $workflow->getId());
                                    $eventQueue->setIdEventAction($actionEvent);
                                    
                                    //Get statuts
                                    $statutEvent = $workflowRepo->getStatutQueueEvent(2);
                                    //Get status
                                    $eventQueue->setIdStatut($statutEvent);
                                    $this->em->flush();
                                }
                            }
                        }

                    }
                }
            }

            $codeStatut="OK";
                
            
        // }catch(\Exception $e){
        //     $codeStatut = "ERREUR";
        //     $respObjects["err"] = $e->getMessage();
        // }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

}