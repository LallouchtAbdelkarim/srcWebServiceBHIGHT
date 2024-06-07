<?php

namespace App\Controller\Workflow;

use App\Entity\EvenementWorkflow;
use App\Entity\ParamActivite;
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
use App\Repository\Parametrages\Activities\activityRepo;

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
    public $activiteRepo;


    
    public function __construct(
        AuthService $AuthService,
        workflowRepo $workflowRepo,
        segementationRepo $segementationRepo,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        Connection $conn,
        MessageService $MessageService,
        GeneralService $GeneralService,
        activityRepo $activityRepo,

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
        $this->activityRepo = $activityRepo;
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
                if($workflow['workflow']['id_status_id']['id'] == 1){
                    $event = $workflowRepo->getAllQueueEventByWorkflow($id);
                    for ($i=0; $i <count($event) ; $i++) { 
                        $event[$i]['status'] = $workflowRepo->getStatusEvent($event[$i]['id_statut_id']);
                        $event[$i]['eventAction'] = $workflowRepo->getEvenementByQueueEvent($event[$i]['id']);
                        $event[$i]['queueDetail'] = $workflowRepo->getDetail($event[$i]['id_element']);
                    }
                    $respObjects['event'] = $event;
                }
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

    #[Route('/getHistoriqueQueue' )]
    public function getHistoriqueQueue(Request $request , workflowRepo $workflowRepo ,segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        $objects = [];
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get('id');
            $objects['historique'] = $workflowRepo->getHistoriqueByQueue($id);
            $respObjects['data'] = $objects;
            $codeStatut='OK';
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
                $dataAcivity = $dataRequest["dataActivitySplit"];
                $dateWorkflow = new DataWorkflow();
                $dateWorkflow->setData($data);
                $dateWorkflow->setDataActivity($dataAcivity);
                $dateWorkflow->setIdWorkflow($workflow);
                $this->em->persist($dateWorkflow);
                $this->em->flush();
                //Save events
                $this->processWorkflowData($data , $id);
                
                $idEvent = $this->workflowRepo->getFirstEvent($id);
                //Save detail queue
                $this->workflowRepo->saveQueueWorkflow($id , $idEvent['id']);
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
                            //TODO:dump($component['branches'][$key][0]['id']);
                            $splitParent = $this->workflowRepo->getSplitQueueByCle($component['id'],$key);
                            /* Save queue from data */
                            //Code **
                            $split = $this->workflowRepo->getEvenmentWorkflow2($branchComponent['id'], $id);
                            $QueSplit = $this->workflowRepo->addQueueSplit($split->getId(), $key, $branchComponent['id'] , $splitParent->getId());
                            /* Save queue from origine data */
                            //Code **
                            $result = null;
                            foreach ($dataSplit as $item) {
                                if ($item['id'] === $branchComponent['id']) {
                                    $result = $item;
                                    break;
                                }
                            }
                            
                            for ($i=0; $i < count($result) ; $i++) { 
                                if($result['data'][$i]['titre'] == $key){
                                    break;
                                }
                            }

                            $this->addDataCritere($QueSplit , $result['data'][$i]['data']);

                        } else {
                            $split = $this->workflowRepo->getEvenmentWorkflow2($component['id'], $id);
                            $QueSplit = $this->workflowRepo->addQueueSplit($split->getId(), $key, $component['id'] , 0);
                            /* Save queue from origine data */
                            //Code **
                            $result = null;
                            foreach ($dataSplit as $item) {
                                if ($item['id'] === $component['id']) {
                                    $result = $item;
                                    break;
                                }
                            }
                            
                            for ($i=0; $i < count($result) ; $i++) { 
                                if($result['data'][$i]['titre'] == $key){
                                    break;
                                }
                            }
                            $this->addDataCritere($QueSplit , $result['data'][$i]['data']);
                        }
                    }
                }
            }
            $this->processComponent2($component, $id, $components);
        }
    }
    
    private function processComponent2(array $component, $id, array $workflowData)
    {
        // if (isset($component['branches']) && is_array($component['branches'])) {
        //     foreach ($component['branches'] as $branchName => $branchComponents) {
        //         foreach ($branchComponents as $branchComponent) {
        //             if ($branchComponent['id_element'] == "Split flow step") {
        //                 foreach ($branchComponent['branches'] as $key => $branch) {
        //                     if ($branch['id_element'] == "Split flow step" ) {
        //                         //TODO:dump($branchComponent['branches'][$key][0]['id']);
        //                         /* Save queue from data */
        //                         //Code **

        //                     } else {
        //                         //TODO:dump($key);
        //                         $split = $this->workflowRepo->getEvenmentWorkflow2($branchComponent['id'], $id);
        //                         $QueSplit = $this->workflowRepo->addQueueSplit($split->getId(), $key, $branchComponent['id'],0);
        //                         /* Save queue from origin data */
        //                         //Code **

        //                     }
        //                 }
        //             }
        //             $this->processComponent2($branchComponent, $id, $workflowData);
        //         }
        //     }
        // }
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
        //TODO:Sauvguarde event
        $delay = 0;

        if($branchComponent['id_element'] == 'delay'){
            $type = 3;
            $number_of_days =  $branchComponent['name']; 
            preg_match('/\d+/', $number_of_days, $matches);
            $delay = intval($matches[0]);
        }
        elseif ($branchComponent['id_element'] == 'Split flow step') {
            $type = 2;
        }else if($branchComponent['id_element'] == 'Decision step'){
            $type = 4;
            $entity->setIdActivityP($branchComponent['activityDecision']);
        }else if($branchComponent['id_element'] == 'Split activity'){
            $type = 5;
        }

        $entity->setType($type);
        $event_workflow = null;
        if($type == 1 || $type == 5 || $type == 4){
            $event_workflow = $this->em->getRepository(EvenementWorkflow::class)->findOneBy(array("event"=>$branchComponent['id_element']));
        }

        if($type == 5){
            $event_workflow = $this->em->getRepository(EvenementWorkflow::class)->findOneBy(array("event"=>$branchComponent['id_element']));
        }
        
        $entity->setIdEvent($event_workflow);
        $entity->setDelayAction($delay);
        $this->em->persist($entity);
        $this->em->flush();

        if($type == 4){
            //TODO:Save resulat id 
            $getResultsByParent = $this->workflowRepo->getResultsByParent($branchComponent['activityDecision']);
            for ($i=0; $i < count($getResultsByParent); $i++) { 
                $this->workflowRepo->saveDetailAction($entity->getId() , $getResultsByParent[$i]['id'] , null ,1 , null);
            }
        }

        if($type == 5){
            //TODO:Save resulat parametre
            $data = $this->workflowRepo->getDataWorkflow($id);
            $dateActivity = $data->getDataActivity();
            for ($i=0; $i < count($dateActivity); $i++) { 
                if($dateActivity[$i]['id'] == $branchComponent['id']){
                    $allQualification = 0;
                    $dateActivity[$i]['allQualification'] == '2' ?  $allQualification = 1 : $allQualification = 0;
                    $detail = $this->workflowRepo->saveDetailAction($entity->getId() , null ,$dateActivity[$i]['titre'], 2 , $allQualification);
                    if($allQualification != 1){
                        for ($j=0; $j < count($dateActivity[$i]['data']); $j++) { 
                            $this->workflowRepo->saveChildDetailAction($detail , $dateActivity[$i]['data'][$j]['id']);   
                        }
                    }
                }
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
                        $eventAction = $workflowRepo->getEventAction($listQueue[$i]['id_event_action_id']);
                        $eventDetails = $workflowRepo->getEvenmentWorkflow($eventAction['id_event_id']);
                        if($eventDetails['is_system'] == 1){
                            //Sauvguarde d'une place pour  
                            $workflowRepo->saveSystemQueueProcess($listQueue[$i]['id'] , $listQueue[$i]['id_event_action_id'] );
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

    #[Route('/approbationEventSystem',methods : ["POST"])]
    public function approbationEventSystem(Request $request , workflowRepo $workflowRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR"; 
        try{
            // $this->AuthService->checkAuth(0,$request);

            //TODO:QueueSystem en pré de lenvoi
            $listeQueueSystem = $workflowRepo->getQueueSystemByEtat(2);
            foreach ($listeQueueSystem as $queueSystem ) {
                $eventAction = $workflowRepo->getEventAction($queueSystem['id_event']);
                $cle = $eventAction['cle'];

                //TODO:Récuperer all event
                $dataWorkflow = $workflowRepo->getDataWorkflow($eventAction['id_workflow_id']);
                $dataWorkflowArray = $dataWorkflow->getData();

                $step = $this->GeneralService->getStep($dataWorkflowArray, $cle);

                if($eventAction['eventWorkflow']['event'] == 'Multiple events'){
                    for ($i=0; $i < count($step['data']); $i++) { 
                        //TODO: Process for data system
                        /*Il faut saisez la functionnalité de renvoi du systém
                        //TODO:code ..
                        */
                        $workflowRepo->addHistoriqueQueueEvent($queueSystem['id_event'] , "L'événement de ".$step['data'][$i]['evenement']." été appliqué." , 1 ,$cle);
                    }
                }else{
                    $workflowRepo->addHistoriqueQueueEvent($queueSystem['id_event'] , "L'événement de ".$step['name']." été appliqué." , 1 ,$cle);
                }
                $workflowRepo->updateStatusSystemQueueEvent($queueSystem['id'] , 1);

                //TODO: Update évenement  
                $workflowRepo->updateStatutQueueEvent($queueSystem['id_queue_event_id'] , 4);

                //TODO: Passer à l'étape suivante

                $nextStep = $this->GeneralService->getNextStep($dataWorkflowArray, $cle);
                if($nextStep['id_element'] == 'delay'){
                    $eventActionDelay = $workflowRepo->getEventActionByCle($nextStep['id']);
                    
                    if($eventActionDelay['delay_action'] == '0'){
                        $nextStepDelay = $this->GeneralService->getNextStep($dataWorkflowArray, $nextStep['id']);
                        
                        //TODO:Si delay action == 0 go to next event
                        $eventActionAssigner = $workflowRepo->getEventActionByCle($nextStepDelay['id']);
                        $workflowRepo->updateQueueEventByIdEventAction($queueSystem['id_queue_event_id'] , $eventActionAssigner['id']);

                        //TODO:Assignatin d'acivité
                        $checkEventAction = $workflowRepo->getEventActionByCle2($nextStepDelay['id']);
                        //dump($checkEventAction);
                        if($checkEventAction->getIdEvent()->getIsSystem() != 1){
                            $checkIfUserLibre = $workflowRepo->checkIfUserLibre(); 

                            if(!$checkIfUserLibre){
                                $workflowRepo->addHistoriqueWorkflow($eventAction['id_workflow_id'] , "Aucun utilisateur disponible !");
                            }else{
                                $userLibre = $workflowRepo->getUserLibre();
                                $workflowRepo->assignTask($userLibre , $queueSystem['id_queue_event_id'] , 2);
                                $workflowRepo->updateStatutQueueEvent($queueSystem['id_queue_event_id'], 3);
                            }
                        }
                        
                    }else if ($eventActionDelay['delay_action'] != '0'){
                        $eventActionDelay = $workflowRepo->getEventActionByCle($nextStep['id']);
                        $workflowRepo->updateQueueEventByIdEventAction($queueSystem['id_queue_event_id'] , $eventActionDelay['id']);
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

    #[Route('/approbationSplitActivity',methods : ["POST"])]
    public function approbationSplitActivity(Request $request , workflowRepo $workflowRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR"; 
        try{
            $this->AuthService->checkAuth(0,$request);
            $idQueueEventUser = $request->get('idQueueEventUser');
            $idQualification = $request->get('idQualification');

            $qualification = $this->workflowRepo->getOneParam($idQualification);
            $eventQueueUser = $workflowRepo->getQueueEventUser($idQueueEventUser);

            if($eventQueueUser && $eventQueueUser->getIdStatus()->getId() == 2){
                if($eventQueueUser->getIdQueueEvent()->getIdEventAction()->getIdEvent()->getEvent() == 'Split activity'){
                    $idWorkflow = $eventQueueUser->getIdQueueEvent()->getIdEventAction()->getIdWorkflow();
                    $data = $workflowRepo->getDataWorkflow($idWorkflow);
                    $idCle = $eventQueueUser->getIdQueueEvent()->getIdEventAction()->getCle();
                    $dataActivity = $data->getDataActivity();

                    $isAllQualification = false;
                    $array = [];
                    $index = 0;
                    $titreQualification = '';
                    //TODO:Récuperer split activity consserne 
                    for ($i=0; $i < count($dataActivity); $i++) { 
                        if($dataActivity[$i]['id'] == $idCle){
                            if($dataActivity[$i]['allQualification'] == 2){
                                $isAllQualification = true;
                                $titreQualification = $dataActivity[$i]['titre'];
                            }
                            $array[$index] = $dataActivity[$i];
                            $index++;
                        }
                    }
                    
                    //TODO:Récuperer the key of qualification
                    $isInAllQualification = true;
                    if($isAllQualification){
                        for ($i=0; $i < count($dataActivity); $i++) {
                            for ($j=0; $j < count($dataActivity[$i]['data']); $j++) { 
                                if($dataActivity[$i]['data'][$j]['id'] == $idQualification){
                                    $isInAllQualification = false;
                                    $titreQualification = $dataActivity[$i]['titre'];
                                }
                            }
                        }
                    }
                    //TODO:Traitement after 
                    $eventQueue = $eventQueueUser->getIdQueueEvent();
                    $event = $this->getEventsAfterKey($data->getData() , $idCle , $titreQualification);
                    
                    if($event[0]['id_element'] == 'stop'){
                        $eventQueue->setType(1);
                    }else if($event[0]['id_element'] == 'End partial'){
                        $eventQueue->setType(2);
                    }else{
                        $eventAction = $workflowRepo->getEventActionByCle2($event[0]['id']);
                        $eventQueue->setIdEventAction($eventAction);
                        $workflowRepo->updateStatutQueueEvent($eventQueue->getId() , 2);
                    }

                    $workflowRepo->updateStatutQueueEventUser($eventQueueUser->getId(),1);
                    
                    $workflowRepo->addHistoriqueQueueEvent($eventQueueUser->getIdQueueEvent()->getId() , 'L é\'venement a éte chargé par la qualification <b>'.$qualification->getIdBranche()->getTitre().'</b>:('.$qualification->getType().') ' , 1 , $idCle);
                    $this->em->flush();
                    $codeStatut="OK";   
                }
            }else{
                $codeStatut="ERROR_EVENT";
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
    
    #[Route('/configureWorkflow',methods : ["POST"])]
    public function configureWorkflow(Request $request , workflowRepo $workflowRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR"; 
        try{
            $this->AuthService->checkAuth(0,$request);
            $dataRequest = json_decode($request->getContent(), true);
            
            $dateStart = $dataRequest['dateStart'];
            $dateStart = new \DateTime($dateStart);
            $workflow = $workflowRepo->getWorkflow($dataRequest["id"]);
            if($workflow->getIdStatus()->getId() == 2 || $workflow->getIdStatus()->getId() == 3){
                if($dateStart >= new \DateTime("now")){
                    $status = $workflowRepo->getStatusWorkflow(3);
                    
                    $workflow->setDateStart($dateStart);
                    $workflow->setIdStatus($status);

                    $this->em->flush();
                    $respObjects['data']= $dateStart;
                    $codeStatut="OK";
    
                }else{
                    $codeStatut="ERROR_DATE";
                }
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

    public function sauvguardeSplit(Request $request,workflowRepo $workflowRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        $data = json_decode($request->getContent(), true);
        $idSplit = 1;//TODO:Force
        $segment = $workflowRepo->getListeSplitQueueById($idSplit);
        // try {
            /*for ($s=0; $s < count($segment) ; $s++) {
                $entities = json_decode($segment[$s]['entities']);
                
                if(in_array('creance',$entities))
                {
                    $queryEntities = "debt_force_seg.dt_debiteur deb,debt_force_seg.dt_creance c";
                    $queryConditions = " ";
                    $param = array();
                    $id = $segment[$s]["id"];
                    $groupe = $workflowRepo->getCritereSplit($id);
                    $queryConditions = " ";
                    $requetOutput = $this->segementationRepo->getRequeteCreance($id , $groupe , $queryEntities,$queryConditions,$param);
                    $queryConditions = $requetOutput["queryConditions"];
                    $queryEntities = $requetOutput["queryEntities"];
                    $param = $requetOutput["param"];
                    if($queryConditions != " "){
                        $queryEntities = strtolower($queryEntities);

                        $rqCreance = "SELECT DISTINCT c.id  FROM  ". $queryEntities . " where " . $queryConditions. "" ; 
                        $stmt = $this->conn->prepare($rqCreance);
                        foreach ($param as $key => $value) {
                            $stmt->bindValue($key, $value); // Assuming parameters are 1-indexed
                        }
                        $stmt = $stmt->executeQuery();
                        $resultCreance = $stmt->fetchAll();
                        
                        if(count($resultCreance) >= 1)
                        {
                            $sql="UPDATE `segmentation` SET `id_status_id`='3' WHERE  id = ".$id."";
                            $stmt = $this->conn->prepare($sql)->executeQuery(); 
                            for ($r=0; $r < count($resultCreance); $r++) { 
                                $sql="insert into `debt_force_seg`.`seg_creance`(id_seg,id_creance) values(".$id.",".$resultCreance[$r]["id"].")";
                                $stmt = $this->conn->prepare($sql)->executeQuery();
                            }
                        }else{
                            $sql="UPDATE `segmentation` SET `id_status_id`='4' WHERE  id = ".$id."";
                            $stmt = $this->conn->prepare($sql)->executeQuery(); 
                        }
                    }
                }
                if(in_array('dossier',$entities))
                {
                    $queryEntities = "debt_force_seg.dt_debiteur deb,debt_force_seg.dt_creance c";
                    $queryConditions = " ";
                    $param = array();
                    $id = $segment[$s]["id"];
                    $groupe = $workflowRepo->getCritereSplit($id);
                    $queryConditions = " ";
                    $requetOutput = $this->segementationRepo->getRequeteCreance($id , $groupe , $queryEntities,$queryConditions,$param);
                    $queryConditions = $requetOutput["queryConditions"];
                    $queryEntities = $requetOutput["queryEntities"];
                    $param = $requetOutput["param"];
                    $queryEntities = strtolower($queryEntities);
                    
                    $rqCreance = "SELECT DISTINCT c.id  FROM  ". $queryEntities . " where " . $queryConditions. "" ; 

                    $rqDossier = "SELECT doss.id FROM debt_force_seg.dt_Dossier doss WHERE doss.id IN (
                        SELECT (c1.id_dossier_id) from debt_force_seg.dt_Creance c1 where c1.id in (".$rqCreance.")
                    )";
                    
                    $stmt = $this->conn->prepare($rqDossier);
                    foreach ($param as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                    $stmt = $stmt->executeQuery();
                    $resultDossier = $stmt->fetchAll();

                    if(count($resultDossier) >= 1)
                    {
                        $sql="UPDATE `segmentation` SET `id_status_id`='3' WHERE  id = ".$id."";
                        $stmt = $this->conn->prepare($sql)->executeQuery(); 
                        for ($r=0; $r < count($resultDossier); $r++) { 
                            $sql="insert into `debt_force_seg`.`seg_dossier`(id_seg,id_dossier) values(".$id.",".$resultDossier[$r]["id"].")";
                            $stmt = $this->conn->prepare($sql)->executeQuery();
                        }
                    }else{
                        $sql="UPDATE `segmentation` SET `id_status_id`='4' WHERE  id = ".$id."";
                        $stmt = $this->conn->prepare($sql)->executeQuery(); 
                    }
                }
                if(in_array('telephone',$entities))
                {
                    $queryEntities = "debt_force_seg.dt_debiteur deb,debt_force_seg.dt_creance c";
                    $queryConditions = " ";
                    $param = array();
                    $id = $segment[$s]["id"];
                    $groupe = $workflowRepo->getCritereSplit($id);
                    $queryConditions = " ";
                    $requetOutput = $this->segementationRepo->getRequeteCreance($id , $groupe , $queryEntities,$queryConditions,$param);
                    $queryConditions = $requetOutput["queryConditions"];
                    $queryEntities = $requetOutput["queryEntities"];
                    $param = $requetOutput["param"];
                    $queryEntities = strtolower($queryEntities);

                    $rqCreance = "SELECT DISTINCT c.id  FROM  ". $queryEntities . " where " . $queryConditions. "" ; 

                    $rqTelephone = "SELECT tel1.id FROM debt_force_seg.dt_Telephone tel1 WHERE (tel1.id_debiteur_id) IN (
                        SELECT debi.id FROM debt_force_seg.dt_Debiteur debi WHERE debi.id IN (
                        SELECT (t1.id_debiteur_id) 
                        FROM Type_Debiteur t1 
                        WHERE t1.id_creance IN (".$rqCreance."))
                    )";
                    // $query = $this->em->createQuery($rqTelephone);
                    // $query->setParameters($param);
                    // $resultTelephone = $query->getResult();
                    $stmt = $this->conn->prepare($rqTelephone);
                    foreach ($param as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                    $stmt = $stmt->executeQuery();
                    $resultTelephone = $stmt->fetchAll();

                    if(count($resultTelephone) >= 1)
                    {
                        $sql="UPDATE `segmentation` SET `id_status_id`='3' WHERE  id = ".$id."";
                        $stmt = $this->conn->prepare($sql)->executeQuery(); 
                        for ($r=0; $r < count($resultTelephone); $r++) { 
                            $sql="insert into `debt_force_seg`.`seg_telephone`(id_seg,id_telephone) values(".$id.",".$resultTelephone[$r]["id"].")";
                            $stmt = $this->conn->prepare($sql)->executeQuery();
                        }
                    }else{
                        $sql="UPDATE `segmentation` SET `id_status_id`='4' WHERE  id = ".$id."";
                        $stmt = $this->conn->prepare($sql)->executeQuery(); 
                    }
                }
                if(in_array('adresse',$entities))
                {
                    $queryEntities = "debiteur deb,creance c";
                    $queryConditions = " ";
                    $param = array();
                    $id = $segment[$s]["id"];
                    $groupe = $workflowRepo->getCritereSplit($id);
                    $queryConditions = " ";
                    $requetOutput = $this->segementationRepo->getRequeteCreance($id , $groupe , $queryEntities,$queryConditions,$param);
                    $queryConditions = $requetOutput["queryConditions"];
                    $queryEntities = $requetOutput["queryEntities"];
                    $param = $requetOutput["param"];
                    $queryEntities = strtolower($queryEntities);

                    $rqCreance = "SELECT DISTINCT c.id  FROM  ". $queryEntities . " where " . $queryConditions. "" ; 

                    $rqAdresse = "SELECT tel1.id FROM adresse tel1 WHERE (tel1.id_debiteur_id) IN (
                        SELECT debi.id FROM Debiteur debi WHERE debi.id IN (
                        SELECT (t1.id_debiteur_id) 
                        FROM Type_Debiteur t1 
                        WHERE t1.id_creance IN (".$rqCreance."))
                    )";
                    $stmt = $this->conn->prepare($rqAdresse);
                    foreach ($param as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                    $stmt = $stmt->executeQuery();
                    $resultAdresse = $stmt->fetchAll();

                    if(count($resultAdresse) >= 1)
                    {
                        $sql="UPDATE `segmentation` SET `id_status_id`='3' WHERE  id = ".$id."";
                        $stmt = $this->conn->prepare($sql)->executeQuery(); 
                        for ($r=0; $r < count($resultAdresse); $r++) { 
                            $sql="insert into `debt_force_seg`.`seg_adresse`(id_seg,id_adresse) values(".$id.",".$resultAdresse[$r]["id"].")";
                            $stmt = $this->conn->prepare($sql)->executeQuery();
                        }
                    }else{
                        $sql="UPDATE `segmentation` SET `id_status_id`='4' WHERE  id = ".$id."";
                        $stmt = $this->conn->prepare($sql)->executeQuery(); 
                    }
                }
                if(in_array('debiteur',$entities))
                {
                    if(in_array('creance',$entities))
                    {
                        $rqDeb = "SELECT debi.id FROM debt_force_seg.dt_debiteur debi WHERE debi.id IN (
                            SELECT (t1.id_debiteur) 
                            FROM Type_Debiteur t1 
                            WHERE t1.id_creance IN (".$rqCreance.")
                        )";
                        
                        $stmt = $this->conn->prepare($rqDeb);
                        foreach ($param as $key => $value) {
                            $stmt->bindValue($key, $value);
                        }
                        $stmt = $stmt->executeQuery();
                        $resultDebi = $stmt->fetchAll();
    
                        if(count($resultDebi) >= 1)
                        {
                            $sql="UPDATE `segmentation` SET `id_status_id`='3' WHERE  id = ".$id."";
                            $stmt = $this->conn->prepare($sql)->executeQuery(); 
                            for ($r=0; $r < count($resultDebi); $r++) { 
                                $sql="insert into `debt_force_seg`.`seg_debiteur`(id_seg,id_debiteur) values(".$id.",".$resultDebi[$r]["id"].")";
                                $stmt = $this->conn->prepare($sql)->executeQuery();
                            }
                        }else{
                            $sql="UPDATE `segmentation` SET `id_status_id`='4' WHERE  id = ".$id."";
                            $stmt = $this->conn->prepare($sql)->executeQuery(); 
                        }
                    }
                    else{
                        $queryEntities = "debt_force_seg.dt_debiteur deb,debt_force_seg.dt_creance c";
                        $queryConditions = " ";
                        $param = array();
                        $id = $segment[$s]["id"];
                        $groupe = $workflowRepo->getCritereSplit($id);
                        $queryConditions = " ";
                        $requetOutput = $this->segementationRepo->getRequeteCreance($id , $groupe , $queryEntities,$queryConditions,$param);
                        $queryConditions = $requetOutput["queryConditions"];
                        $queryEntities = $requetOutput["queryEntities"];
                        $param = $requetOutput["param"];
                        $queryEntities = strtolower($queryEntities);
                        $rqCreance = "SELECT DISTINCT c.id  FROM  ". $queryEntities . " where " . $queryConditions. "" ; 

                        $rqDeb = "SELECT debi.id FROM debiteur debi WHERE debi.id IN (
                            SELECT (t1.id_debiteur_id) 
                            FROM Type_Debiteur t1 
                            WHERE t1.id_creance IN (".$rqCreance.")
                        )";
                        $stmt = $this->conn->prepare($rqDeb);
                        foreach ($param as $key => $value) {
                            $stmt->bindValue($key, $value);
                        }
                        $stmt = $stmt->executeQuery();
                        $resultDebi = $stmt->fetchAll();
                        if(count($resultDebi) >= 1)
                        {
                            $sql="UPDATE `segmentation` SET `id_status_id`='3' WHERE  id = ".$id."";
                            $stmt = $this->conn->prepare($sql)->executeQuery(); 
                            for ($r=0; $r < count($resultDebi); $r++) { 
                                $sql="insert into `debt_force_seg`.`seg_debiteur`(id_seg,id_debiteur) values(".$id.",".$resultDebi[$r]["id"].")";
                                $stmt = $this->conn->prepare($sql)->executeQuery();
                            }
                        }else{
                            $sql="UPDATE `segmentation` SET `id_status_id`='4' WHERE  id = ".$id."";
                            $stmt = $this->conn->prepare($sql)->executeQuery(); 
                        }
                    }   
                }
            }*/
        // } catch (\Exception $e) {
        //     $respObjects["err"] = $e->getMessage();
        // }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    #[Route('/getListeFournisseurs')]
    public function getListeFournisseurs(Request $request , workflowRepo $workflowRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
          
            $this->AuthService->checkAuth(0,$request);
            $fournisseur = $workflowRepo->getListeFournisseurs();
            $typeAssignation = $workflowRepo->getTypeAssignation();

            $objet['fournisseur'] = $fournisseur;
            $objet['typeAssignation'] = $typeAssignation;

            $respObjects["data"] = $objet;
            $codeStatut="OK";

        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getTypesOfSParametragesByEvenment')]
    public function getListTypeParamsByWorkflow(Request $request , workflowRepo $workflowRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $event = $request->get('event');
            $result = $workflowRepo->getTypesOfSParametragesByEvenment($event);
            $respObjects["data"] = $result;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    function getEventsAfterKey($data, $key, $title) {
        // Helper function to search recursively
        function searchBranches($item, $key, $title) {
            if (isset($item['id']) && $item['id'] === $key) {
                if (isset($item['branches']) && isset($item['branches'][$title])) {
                    return $item['branches'][$title];
                }
            }
            if (isset($item['branches'])) {
                foreach ($item['branches'] as $branch) {
                    foreach ($branch as $subItem) {
                        $result = searchBranches($subItem, $key, $title);
                        if ($result !== null) {
                            return $result;
                        }
                    }
                }
            }
            return null;
        }
    
        // Iterate over the main data array
        foreach ($data as $item) {
            $result = searchBranches($item, $key, $title);
            if ($result !== null) {
                return $result;
            }
        }
        return null;
    }
    
  
}