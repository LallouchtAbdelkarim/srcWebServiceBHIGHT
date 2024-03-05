<?php

namespace App\Controller\Segmentation;

use App\Repository\Sgementaion\queueRepo;
use App\Service\GeneralService;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\Sgementaion\segementationRepo;
use App\Service\AuthService;
use Doctrine\DBAL\Connection;
#[Route('/API/queue')]

class QueueController extends AbstractController
{
    private $MessageService;
    private $AuthService;

    private $GeneralService;
    private $conn;


    public function __construct(
        AuthService $AuthService,
        segementationRepo $segementationRepo,
        queueRepo $queueRepo,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        GeneralService $GeneralService,
        MessageService $MessageService,
        Connection $conn,
    )
    {
        $this->segementationRepo = $segementationRepo;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->AuthService = $AuthService;
        $this->MessageService = $MessageService;
        $this->GeneralService = $GeneralService;
        $this->queueRepo = $queueRepo;
        $this->conn = $conn;
    }
    #[Route('/getListeGroupeQueue')]
    public function listeGroupe(Request $request,queueRepo $queueRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = $queueRepo->getListeGroupeQueue();
            $codeStatut = "OK";
            $respObjects["data"] = $data;
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getListeSegmentByGroupe')]
    public function getListeSegmentByGroupe(Request $request,queueRepo $queueRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $id_type = $request->get("id_type");
            $id_groupe = $request->get("id_groupe");
            $this->AuthService->checkAuth(0,$request);
            $data = $queueRepo->getListeSgementationByGroupe($id_type,$id_groupe);
            $codeStatut = "OK";
            $respObjects["data"] = $data;
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
   
    #[Route('/updateGroupe', methods: ['POST'])]
    public function updateGroupe(Request $request,queueRepo $queueRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $titre_groupe = $request->get("titre");
            $id = $request->get("id");
            $description = $request->get("description");

            if(!$queueRepo->findGroupe($id)){
                $codeStatut= "NOT_EXIST_ELEMENT";
            }else{
                $data = $queueRepo->updateGroupe($id,$titre_groupe , $description);
                $codeStatut = "OK";
            }
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        $respObjects["codeStatuddt"] = $e->getMessage();

        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/addGroupe', methods: ['POST'])]
    public function addGroupe(Request $request,queueRepo $queueRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $titre_groupe = $request->get("titre");
            $description = $request->get("description");
            $id = $request->get("id");
            if($titre_groupe == ''){
                $codeStatut= "ERROR-EMPTY-PARAMS";
            }else{
                $data = $queueRepo->addGroupe($titre_groupe,$description);
                $codeStatut = "OK";
            }
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/deleteGroupe', methods: ['POST'])]
    public function deleteGroupe(Request $request,queueRepo $queueRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            if($queueRepo->checkQueueInGroupe($id)){
                $codeStatut= "NOT_EXIST_ELEMENT";
            }else{
                $data = $queueRepo->deleteGroupeQueue($id);
                $codeStatut = "OK";
            }
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getListeQueue')]
    public function listeQueue(Request $request,queueRepo $queueRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $id_type = $request->get("id_type");
            $id_groupe = $request->get("id_groupe");
            $this->AuthService->checkAuth(0,$request);
            $data = $queueRepo->getListeQueue($id_type,$id_groupe);
            $codeStatut = "OK";
            $respObjects["data"] = $data;
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getTypes')]
    public function listeTypes(Request $request,queueRepo $queueRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = $queueRepo->getTypesQueue();
            $codeStatut = "OK";
            $respObjects["data"] = $data;
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/addQueue', methods: ['POST'])]
    public function addQueue(Request $request,queueRepo $queueRepo , segementationRepo $segementationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $titre = $data["titre"];
            $description = $data["description"];
            $id_type_id = $data["id_type_id"]; 
            $queue_groupe_id = $data["queue_groupe_id"];
            $active = $data["active"];
            $seg = $data["segmentation"];
            if($titre == "" ){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }else{
                if($queueRepo->findQueueByTitre($titre)){
                    $codeStatut = "ELEMENT_DEJE_EXIST";
                }else{
                    // $createSegment = $segementationRepo->createSegment1($titre , 1);
                    if($id_type_id == 1 || $id_type_id == 2 || $queueRepo->findGroupe($queue_groupe_id))
                    {
                        $createQueue = $queueRepo->createQueue($titre,$description,$queue_groupe_id ,$id_type_id , $seg ,$active );
                        $data_critere = $data["data"];
                        for ($i=0; $i < count($data_critere); $i++) { 
                            $titre_groupe = $data_critere[$i]["groupe"]["titre_groupe"];
                            $queueId = $queueRepo->getLastQueue();
                            $createGroupeQueue = $queueRepo->createGroupeCritereRepo($titre_groupe,$queueId);
                            $critere = $data_critere[$i]["critere"];
                            $createQueueCritere = $queueRepo->createQueueCritere($critere["critere"] , $createGroupeQueue , $critere["type"]);
                            if($critere["type"] == 'multiple_check'){
                               $values = $data_critere[$i]['values'];
                               for ($j=0; $j < count($values) ; $j++) {
                                    if(isset($values[$j]["selected"]) && $values[$j]["selected"] == true ){
                                        if($values[$j]["id_critere_id"] == 1 || $values[$j]["id_critere_id"] == 17 || $values[$j]["id_critere_id"] == 6 || $values[$j]["id_critere_id"] == 7 || $values[$j]["id_critere_id"] == 11 || $values[$j]["id_critere_id"] == 12 || $values[$j]["id_critere_id"] == 14 || $values[$j]["id_critere_id"] == 15 )
                                        {
                                            $value1 =  $values[$j]["id_champ"];
                                            $queueRepo->createSegValues($value1 , '' , $createQueueCritere->getId());
                                        }
                                        else
                                        {
                                            $value1 =  $values[$j]["value"];
                                            $queueRepo->createSegValues($value1 , '' , $createQueueCritere->getId());
                                        }
                                    }
                                }
                            }
                            if($critere["type"] == 'montant'){
                                $values = $data_critere[$i]['values'];
                                for ($q=0; $q < count($values) ; $q++) {
                                    $value1 =  $values["value1"];
                                    $value2 =  $values["value2"];
                                }
                                $queueRepo->createQueueValues($value1 , $value2 , $createQueueCritere->getId());
                            }
                            if($critere["type"] == 'date'){
                                $values = $data_critere[$i]['values'];
                                for ($q=0; $q < count($values) ; $q++) {
                                    $value1 =  $values["value1"];
                                    $value2 =  $values["value2"];
                                }
                                $queueRepo->createQueueValues($value1 , $value2 , $createQueueCritere->getId());
                            }
                        }
                        $codeStatut="OK";
                    }else{
                        $codeStatut = "ERROR-EMPTY-PARAMS";
                    }
                }
            }
        }catch(\Exception $e){
            $codeStatut = "ERROR";
            $respObjects["msg"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getCritereQueue')]
    public function getCritereQueue(Request $request,queueRepo $queueRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id_queue = $request->get("id_queue");
            $queue = $queueRepo->findQueue($id_queue);
            if($queue){
                $data = $queueRepo->getCritereQueue($id_queue);
                $codeStatut = "OK";
                $respObjects["data"] = $data;
            }else{
                $codeStatut="NOT_EXIST_ELEMENT";
            }
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/clearCritereByQueue', methods: ['POST'])]
    public function clearCritereByQueue(Request $request,queueRepo $queueRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id_queue = $request->get("id");
            $queue = $queueRepo->findQueue($id_queue);
            if($queue){
                $queue = $queue[0];
                if($queue["assigned_strategy"] != 1){
                    $data = $queueRepo->clearCritereByQueue($id_queue);
                    $codeStatut = "OK";
                }else{
                    $codeStatut = "QUEUE_LIE_WORKFLOW";
                }
            }else{
                $codeStatut="NOT_EXIST_ELEMENT";
            }
            
        }catch(\Exception $e){
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getSegmentationNonAssigne')]
    public function getSegmentationNonAssigne(Request $request,queueRepo $queueRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id_queue = $request->get("id");
            $data = $queueRepo->getSegmentationNonAssigne();
            $respObjects["data"] = $data;
            $codeStatut = "OK";
            
        }catch(\Exception $e){
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/updatePriority', methods: ['POST'])]
    public function updatePriority(Request $request,queueRepo $queueRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id_queue = $request->get("id");
            $data = json_decode($request->getContent(), true);
            // dump($data);
            for ($i=0; $i < count($data) ; $i++) { 
                # code...
                // dump($data[$i]["id"]);
                $queueRepo->updatePriority($data[$i]["id"] , $data[$i]["priority"]);
            }
            $codeStatut="OK";
            
        }catch(\Exception $e){
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/sauvguardeQueue', methods: ['POST'])]
    public function sauvguardeQueue(Request $request,segementationRepo $segementationRepo , queueRepo $queueRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        // try{
            // $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $queue = $queueRepo->getListeQueueByStatus(1);
            for ($s=0; $s < count($queue) ; $s++) { 
                $queryEntities = "App\Entity\Debiteur deb,App\Entity\Creance c";
                $queryConditions = " ";
                $param = array();
                $id = $queue[$s]["id"];
                $id_segment = $queue[$s]["id_segmentation_id"];
                $groupe = $queueRepo->getCritereQueue($id);
                for ($j=0; $j < count($groupe) ; $j++) { 
                    if($groupe[$j]['groupe'] == "Creance"){
                        $criteres = $groupe[$j]["criteres"];
                        for ($k= 0; $k < count($criteres);$k++){
                            if($k==0)
                            {
                                $operateur[$k]="";
                            }
                            else
                            {
                                $operateur[$k]=" and ";
                            }
                            if($criteres[$k]["critere"] == "type créance"){
                                # code...
                                $details = $criteres[$k]["details"];
                                for ($i=0; $i < count($details ); $i++) { 
                                    # code...
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    $queryConditions .= " ".$operateur[$k]." ".$operateur1[$i]." ( identity(c.id_type_creance) LIKE :type_creance".$k."_".$i.") ";
                                    $param['type_creance'.$k.'_'.$i] = $details[$i]["value1"];
                                }
                            }
                            if($criteres[$k]["critere"] == "date écheance"){
                                # code...
                                $details = $criteres[$k]["details"];
                                for ($i=0; $i < count($details ); $i++) { 
                                    # code...
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                    $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
    
                                    $queryConditions .= " ".$operateur[$k]." ".$operateur1[$i]." ( c.date_echeance between :date_echeance1".$k."_".$i." and :date_echeance2".$k."_".$i.") ";
                                    $param['date_echeance1'.$k.'_'.$i] = $start;
                                    $param['date_echeance2'.$k.'_'.$i] = $end;
                                }
                            }
                            if($criteres[$k]["critere"] == "total creance"){
                                # code...
                                $details = $criteres[$k]["details"];
                                for ($i=0; $i < count($details ); $i++) { 
                                    # code...
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    $queryConditions .= " ".$operateur[$k]." ".$operateur1[$i]." ( c.total_creance between :total_creance1".$k."_".$i." and :total_creance2".$k."_".$i.") ";
                                    $param['total_creance1'.$k.'_'.$i] = $details[$i]["value1"];
                                    $param['total_creance2'.$k.'_'.$i] = $details[$i]["value2"];
                                }
                            }
                            if($criteres[$k]["critere"] == "total restant"){
                                # code...
                                $details = $criteres[$k]["details"];
                                for ($i=0; $i < count($details ); $i++) { 
                                    # code...
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    $queryConditions .= " ".$operateur[$k]." ".$operateur1[$i]." ( c.totalRestant between :total_restant1".$k."_".$i." and :total_restant2".$k."_".$i.") ";
                                    $param['total_restant1'.$k.'_'.$i] = $details[$i]["value1"];
                                    $param['total_restant2'.$k.'_'.$i] = $details[$i]["value2"];
                                }
                            }
                            
                        }
                    }
                    if($groupe[$j]['groupe'] == "Garantie"){
                        $criteres = $groupe[$j]["criteres"];
                        for ($k= 0; $k < count($criteres);$k++){
                            if($k==0)
                            {
                                $operateur[$k]="";
                            }
                            else
                            {
                                $operateur[$k]=" and ";
                            }
                            if($criteres[$k]["critere"] == "type garantie"){
                                $details = $criteres[$k]["details"];
                                for ($i=0; $i < count($details ); $i++) { 
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    if(strpos($queryEntities,",App\Entity\GarantieCreance gc") == false)
                                    {
                                        $queryEntities .= ",App\Entity\GarantieCreance gc";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Garantie g") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Garantie g";
                                    }
                                    $queryConditions .= " ".$operateur[$k]." ".$operateur1[$i]." (  c.id = identity(gc.id_creance) and identity(gc.id_garantie) = g.id and  g.type_garantie LIKE :type_garantie".$k."_".$i.") ";
                                    $param['type_garantie'.$k.'_'.$i] = $details[$i]["value1"]; 
                                }
                            }
                        }
                    }
                    if($groupe[$j]['groupe'] == "Donneur ordre"){
                        $criteres = $groupe[$j]["criteres"];
                        for ($k= 0; $k < count($criteres);$k++){
                            if($k==0)
                            {
                                $operateur[$k]="";
                            }
                            else
                            {
                                $operateur[$k]=" and ";
                            }
                            if($criteres[$k]["critere"] == "type donneur ordre"){
                                $details = $criteres[$k]["details"];
                                for ($i=0; $i < count($details ); $i++) { 
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Portefeuille p") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Portefeuille p";
                                    }
                                    if(strpos($queryEntities,",App\Entity\DonneurOrdre dn") == false)
                                    {
                                        $queryEntities .= ",App\Entity\DonneurOrdre dn";
                                    }
                                    $queryConditions .= " ".$operateur[$k]." ".$operateur1[$i]." (  p.id = identity(c.id_ptf) and identity(p.id_donneur_ordre) = dn.id and  identity(dn.id_type) = :type_donneur".$k."_".$i.") ";
                                    $param['type_donneur'.$k.'_'.$i] = $details[$i]["value1"];dump($details[$i]["value1"]); 
                                }
                            }
                        }
                    }
                    /*if($groupe[$j]['groupe'] == "Porte feuille"){
                        $criteres = $groupe[$j]["criteres"];
                        for ($k= 0; $k < count($criteres);$k++){
                            if($k==0)
                            {
                                $operateur[$k]="";
                            }
                            else
                            {
                                $operateur[$k]=" and ";
                            }
                            if($criteres[$k]["critere"] == "Secteur d'activité"){
                                $details = $criteres[$k]["details"];
                                for ($i=0; $i < count($details ); $i++) { 
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Portefeuille p") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Portefeuille p";
                                    }
                                    if(strpos($queryEntities,",App\Entity\DonneurOrdre dn") == false)
                                    {
                                        $queryEntities .= ",App\Entity\DonneurOrdre dn";
                                    }
                                    $queryConditions .= " ".$operateur[$k]." ".$operateur1[$i]." (  p.id = identity(c.id_ptf) and  identity(p.id_detail_secteur_activite) = :secteur_act".$k."_".$i.") ";
                                    $param['secteur_act'.$k.'_'.$i] = $details[$i]["value1"];
                                }
                            }
                        }
                    }*/
                    if($groupe[$j]['groupe'] == "Detail créance"){
                        $criteres = $groupe[$j]["criteres"];
                        for ($k= 0; $k < count($criteres);$k++){
                            if($k==0)
                            {
                                $operateur[$k]="";
                            }
                            else
                            {
                                $operateur[$k]=" and ";
                            }
                            if($criteres[$k]["critere"] == "principale"){
                                $details = $criteres[$k]["details"];
                                for ($i=0; $i < count($details ); $i++) { 
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    if(strpos($queryEntities,",App\Entity\DetailCreance dc") == false)
                                    {
                                        $queryEntities .= ",App\Entity\DetailCreance dc";
                                    }
                                    $queryConditions .= " ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(dc.id_creance) and   dc.principale between :VALUE1".$k."_".$i." and :VALUE2".$k."_".$i.") ";
                                    $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                    $param['VALUE2'.$k.'_'.$i] = $details[$i]["value2"];
                                }
                            }
                            if($criteres[$k]["critere"] == "frais"){
                                $details = $criteres[$k]["details"];
                                for ($i=0; $i < count($details ); $i++) { 
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    if(strpos($queryEntities,",App\Entity\DetailCreance dc") == false)
                                    {
                                        $queryEntities .= ",App\Entity\DetailCreance dc";
                                    }
                                    $queryConditions .= " ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(dc.id_creance) and   dc.frais between :VALUE1".$k."_".$i." and :VALUE2".$k."_".$i.") ";
                                    $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                    $param['VALUE2'.$k.'_'.$i] = $details[$i]["value2"];
                                }
                            }
                            if($criteres[$k]["critere"] == "interet"){
                                $details = $criteres[$k]["details"];
                                for ($i=0; $i < count($details ); $i++) { 
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    if(strpos($queryEntities,",App\Entity\DetailCreance dc") == false)
                                    {
                                        $queryEntities .= ",App\Entity\DetailCreance dc";
                                    }
                                    $queryConditions .= " ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(dc.id_creance) and   dc.interet between :VALUE1".$k."_".$i." and :VALUE2".$k."_".$i.") ";
                                    $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                    $param['VALUE2'.$k.'_'.$i] = $details[$i]["value2"];
                                }
                            }
                        }
                    }
                    if($groupe[$j]['groupe'] == "Téléphone"){
                        $criteres = $groupe[$j]["criteres"];
                        for ($k= 0; $k < count($criteres);$k++){
                            if($k==0)
                            {
                                $operateur[$k]="";
                            }
                            else
                            {
                                $operateur[$k]=" and ";
                            }
                            if($criteres[$k]["critere"] == "Type téléphone"){
                                $details = $criteres[$k]["details"];
                                for ($i=0; $i < count($details ); $i++) { 
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                    {
                                        $queryEntities .= ",App\Entity\TypeDebiteur t";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Telephone tel") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Telephone tel";
                                    }
                                    $queryConditions .= " ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(t.id_creance)  and (tel.id_debiteur)=deb.id  and  identity(tel.id_type_tel) like :VALUE1".$k."_".$i." ) ";
                                    $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                }
                            }
                            if($criteres[$k]["critere"] == "Status téléphone"){
                                $details = $criteres[$k]["details"];
                                for ($i=0; $i < count($details ); $i++) { 
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                    {
                                        $queryEntities .= ",App\Entity\TypeDebiteur t";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Telephone tel") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Telephone tel";
                                    }
                                    $queryConditions .= " ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(t.id_creance)  and (tel.id_debiteur)=deb.id  and  identity(tel.id_status) like :VALUE1".$k."_".$i." ) ";
                                    $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                }
                            }
                        }
                    }
                    if($groupe[$j]['groupe'] == "Adresse"){
                        $criteres = $groupe[$j]["criteres"];
                        for ($k= 0; $k < count($criteres);$k++){
                            if($k==0)
                            {
                                $operateur[$k]="";
                            }
                            else
                            {
                                $operateur[$k]=" and ";
                            }
                            if($criteres[$k]["critere"] == "Type adresse"){
                                $details = $criteres[$k]["details"];
                                for ($i=0; $i < count($details ); $i++) { 
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                    {
                                        $queryEntities .= ",App\Entity\TypeDebiteur t";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Adresse ad") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Adresse ad";
                                    }
                                    $queryConditions .= " ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(t.id_creance)  and (ad.id_debiteur)=deb.id  and  identity(ad.id_type_adresse) like :VALUE1".$k."_".$i." ) ";
                                    $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                }
                            }
                            if($criteres[$k]["critere"] == "Status adresse"){
                                $details = $criteres[$k]["details"];
                                for ($i=0; $i < count($details ); $i++) { 
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                    {
                                        $queryEntities .= ",App\Entity\TypeDebiteur t";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Adresse ad") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Adresse ad";
                                    }
                                    $queryConditions .= " ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(t.id_creance)  and (ad.id_debiteur)=deb.id  and  identity(ad.id_status) like :VALUE1".$k."_".$i." ) ";
                                    $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                }
                            }
                        }
                    }
                    if($groupe[$j]['groupe'] == "Débiteur"){
                        $criteres = $groupe[$j]["criteres"];
                        for ($k= 0; $k < count($criteres);$k++){
                            if($k==0)
                            {
                                $operateur[$k]="";
                            }
                            else
                            {
                                $operateur[$k]=" and ";
                            }
                            if($criteres[$k]["critere"] == "Personne"){
                                $details = $criteres[$k]["details"];
                                for ($i=0; $i < count($details ); $i++) { 
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                    {
                                        $queryEntities .= ",App\Entity\TypeDebiteur t";
                                    }
                                    $queryConditions .= " ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(t.id_creance)  and  deb.type_personne like :VALUE1".$k."_".$i." ) ";
                                    $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                }
                            }
                            if($criteres[$k]["critere"] == "Type débiteur"){
                                $details = $criteres[$k]["details"];
                                for ($i=0; $i < count($details ); $i++) { 
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                    {
                                        $queryEntities .= ",App\Entity\TypeDebiteur t";
                                    }
                                    // if(strpos($queryEntities,",App\Entity\DetailsTypeDeb dt") == false)
                                    // {
                                    //     $queryEntities .= ",App\Entity\DetailsTypeDeb dt";
                                    // }
                                    $queryConditions .= " ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(t.id_creance)   and  identity(t.id_type) like :VALUE1".$k."_".$i." ) ";
                                    $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                }
                            }
                        }
                    }
                }
                if($queryConditions != " "){
                    $query = "SELECT DISTINCT c.id  FROM  ". $queryEntities . " where " . $queryConditions. "" ; dump($query);
                    $query = $this->em->createQuery($query);
                    $query->setParameters($param);
                    $result = $query->getResult();
                    if(count($result) >= 1)
                    {
                        $sql="UPDATE `queue` SET `id_status_id`='3' WHERE  id = ".$id."";
                        $stmt = $this->conn->prepare($sql)->executeQuery(); 
                        for ($r=0; $r < count($result); $r++) {
                            $isInSeg = $queueRepo->checkIfInSeg($result[$r]["id"] ,$id_segment );
                            if($isInSeg){
                                $sql="insert into `debt_force_seg`.`queue_creance`(id_seg,id_creance,id_queue) values(".$id_segment.",".$result[$r]["id"].",".$id.")";dump($sql);
                                $stmt = $this->conn->prepare($sql)->executeQuery(); 
                            }
                        }
                    }else{
                        $sql="UPDATE `queue` SET `id_status_id`='4' WHERE  id = ".$id."";
                        $stmt = $this->conn->prepare($sql)->executeQuery(); 
                    }
                }
            }
        // }catch(\Exception $e){
        //     $codeStatut = "ERROR";
        //     $respObjects["msg"] = $e->getMessage();
        // }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
}   
