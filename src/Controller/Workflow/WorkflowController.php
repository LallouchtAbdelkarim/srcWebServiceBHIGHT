<?php

namespace App\Controller\Workflow;

use App\Entity\EvenementWorkflow;
use App\Entity\Workflow;
use App\Repository\Workflow\workflowRepo;
use App\Repository\Sgementaion\segementationRepo;
use App\Service\AuthService;
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
    private $AuthService;
    public $workflowRepo;
    private $conn;
    public $em;


    public function __construct(
        AuthService $AuthService,
        workflowRepo $workflowRepo,
        segementationRepo $segementationRepo,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        Connection $conn,
        MessageService $MessageService,
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
            $this->AuthService->checkAuth(0,$request);
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
        try{
            $this->AuthService->checkAuth(0,$request);
            $array = array();
            $data = json_decode($request->getContent(), true);
            $id = $request->get('id');
            $workflow = $workflowRepo->getWorkflow($id);
            if(!$workflow){
                $codeStatut="NOT_EXIST";
            }else{
                $data = $data["data"];
                $fact = new DataWorkflow();
                $fact->setData($data);
                $fact->setIdWorkflow($workflow);
                $this->em->persist($fact);
                $this->em->flush();
                //Save events
                $this->processWorkflowData($data , $id);
    
                $idEvent = $this->workflowRepo->getFirstEvent($id);
                //Save detail queue
                $this->workflowRepo->saveQueueWorkflow($id , $idEvent);
                $workflow = $this->workflowRepo->updateStatutWorkflow($id, 2);
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
    private function processWorkflowData(array $components , $id)
    {
        foreach ($components as $component) {
            if($component['name'] != "Stop" && $component['name'] != "Start" ){
                $this->createEventAction($component , $id);
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
                        $this->createEventAction($branchComponent , $id);
                    }
                    $this->processComponent($branchComponent , $id);
                }
            }
        }
    }
    public function createEventAction($branchComponent , $id){
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
                                $assignTask = $workflowRepo->assignTask($userLibre , $listQueue[$i]['id'] , 2);
                                $updateEtatQueue = $workflowRepo->updateStatutQueueEvent( $listQueue[$i]['id'], 3);
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
            $idEventQueue = $request->get('idEventQueue');
            $idWorkflow = 21;
            $eventQueue = $workflowRepo->getEventByWorkflow($idEventQueue);
            $data = $workflowRepo->getDataWorkflow($idWorkflow);
            $workflowData = $data->getData();
            $cleEvent = $eventQueue->getIdEventAction()->getCle();
    
            $nextStep = $this->getNextStep($workflowData, $cleEvent);
            
            if(isset($nextStep['id'])){
                $actionEvent = $workflowRepo->getEvenmentWorkflow2($nextStep['id'] , $idWorkflow);
                $eventQueue->setIdEventAction($actionEvent);
                //Get statuts
                $statutEvent = $workflowRepo->getStatutQueueEvent(2);
                //Get status
                $eventQueue->setIdStatut($actionEvent);
                $this->em->flush();
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
    private function getNextStep(array $workflowData, string $cleEvent)
    {
        $nextStep = null;
        $foundCurrent = false;

        foreach ($workflowData as $index => $component) {
            // Check if this is the current component
            if ($component['id'] === $cleEvent) {
                $foundCurrent = true;
                // Check if there is a next component in the sequence
                if (isset($workflowData[$index + 1])) {
                    $nextStep = $workflowData[$index + 1];
                }
                break;
            }

            // If the component is a switch, check its branches
            if (isset($component['branches'])) {
                foreach ($component['branches'] as $branchComponents) {
                    foreach ($branchComponents as $branchComponent) {
                        if ($branchComponent['id'] === $cleEvent) {
                            $foundCurrent = true;
                            // Check if there is a next component in the branch sequence
                            if (isset($branchComponents[$index + 1])) {
                                $nextStep = $branchComponents[$index + 1];
                            }
                            break 3; // Exit all loops
                        }
                    }
                }
            }
        }

        if (!$foundCurrent) {
            // Handle case where current component is not found (e.g., error or end of workflow)
            throw new \Exception("Current component with id $cleEvent not found in workflow data.");
        }
        return $nextStep;
    }

}