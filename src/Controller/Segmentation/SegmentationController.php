<?php

namespace App\Controller\Segmentation;

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


#[Route('/API/segmentation')]

class SegmentationController extends AbstractController
{
    private $MessageService;
    private $AuthService;
    private $GeneralService;

    private $conn;

    public function __construct(
        AuthService $AuthService,
        segementationRepo $segementationRepo,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        GeneralService $GeneralService,
        MessageService $MessageService,
        Connection $conn,

    )
    {
        $this->conn = $conn;
        $this->segementationRepo = $segementationRepo;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->AuthService = $AuthService;
        $this->MessageService = $MessageService;
        $this->GeneralService = $GeneralService;
    }
    
    #[Route('/getListeSegmentByGroupe')]
    public function getListeSegmentByGroupe(Request $request,segementationRepo $segementationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $id_type = $request->get("id_type");
            $id_groupe = $request->get("id_groupe");
            $this->AuthService->checkAuth(0,$request);
            $data = $segementationRepo->getListeSgementationByGroupe($id_type,$id_groupe);
            $codeStatut = "OK";
            $respObjects["data"] = $data;
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        $respObjects["ERRR"] = $e->getMessage();

        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/addSeg', methods: ['POST'])]
    public function addSeg(Request $request,segementationRepo $segementationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $titre = $data["titre"];
            $description = $data["description"];
            $entity = $data["entity"];
            $cle = $data["cle"];
            // $id_type_id = $data["id_type_id"]; 
            // $queue_groupe_id = $data["queue_groupe_id"];
            // $active = $data["active"];
            $type = $data["type"];

            if($titre == "" ){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }else{
                // if($segementationRepo->findSegByTitre($titre)){
                //     $codeStatut = "ELEMENT_DEJE_EXIST";
                // }else{
                    $arrayMultiple = $segementationRepo->getCritereMultiple();
                    

                    if((count($entity) >= 1)){
                        $createSegment = $segementationRepo->createSegment1($cle,$titre , 1 ,$description , json_encode($entity),$type);
                        if($createSegment){
                            // if($id_type_id == 1 || $id_type_id == 2 || $segementationRepo->findGroupe($queue_groupe_id))
                            // {
                                // $createQueue = $segementationRepo->createQueue($titre,$description,$queue_groupe_id ,$id_type_id , $createSegment->getId() ,$active );
                                $data_critere = $data["data"];
                                $segId = $createSegment->getId();
                                $priority = 1;
                                for ($i=0; $i < count($data_critere); $i++) { 
                                    $titre_groupe = $data_critere[$i]["groupe"]["titre_groupe"];
                                    $createGroupeQueue = $segementationRepo->createGroupeCritereRepo($titre_groupe,$segId , $priority);
                                    $critere = $data_critere[$i]["critere"];
                                    for ($j=0; $j < count($critere); $j++) { 
                                        $createQueueCritere = $segementationRepo->createSegCritere($critere[$j]["critere"] , $createGroupeQueue , $critere[$j]["type"]);
                                        if($critere[$j]["type"] == 'multiple_check'){
                                            $values = $critere[$j]['values'];
                                            for ($v=0; $v < count($values) ; $v++) {
                                                if(isset($values[$v]["selected"]) && $values[$v]["selected"] == true ){
                                                    /*$inArray = false;
                                                    for ($a=0; $a < count($arrayMultiple); $a++) {dump($a);
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
                                                        $values[$v]["id_critere_id"] == 35 
                                                        )
                                                    {
                                                        $value1 =  $values[$v]["id_champ"];
                                                        $segementationRepo->createSegValues($value1 , '' , $createQueueCritere->getId(),null , $values[$v]["value"]);
                                                    }
                                                    else
                                                    {
                                                        $value1 =  $values[$v]["value"];
                                                        $segementationRepo->createSegValues($value1 , '' , $createQueueCritere->getId(),null , $values[$v]["value"]);
                                                    }
                                                }
                                            }
                                        }
                                         
                                        if(isset($critere[$j]) && isset($critere[$j]["type"]) && $critere[$j]["type"] == 'montant') {
                                            $values = $critere[$j]['values'];
                                            $action = $critere[$j]['action'] ;
                                            for ($q=0; $q < count($values) ; $q++) {
                                                $value1 =  $values["value1"];
                                                $value2 =  $values["value2"] ?? "";
                                            }
                                            $segementationRepo->createSegValues($value1 , $value2 , $createQueueCritere->getId(),$action,null);
                                        }

                                        if(isset($critere[$j]) && isset($critere[$j]["type"]) && $critere[$j]["type"] == 'date') {
                                            $values = $critere[$j]['values'];
                                            $action = $critere[$j]['action'] ;
                                            for ($q=0; $q < count($values) ; $q++) {
                                                $value1 =  $values["value1"];
                                                $value2 =  $values["value2"] ?? "";
                                            }
                                            $segementationRepo->createSegValues($value1 , $value2 , $createQueueCritere->getId(),$action,null);
                                        }

                                    }
                                    $priority ++;
                                }
                                $codeStatut="OK";
                            // }else{
                            //     $codeStatut = "ERROR-EMPTY-PARAMS";
                            // }
                        }
                        else{
                            $codeStatut = "ERROR";
                        }
                    }else{
                        $codeStatut = "ERROR-EMPTY-PARAMS";
                    }
                // }
            }
        }catch(\Exception $e){
            $codeStatut = "ERROR";
            $respObjects["msg"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/sauvguardeSegementation', methods: ['POST'])]
    public function sauvguardeSegementation(Request $request,segementationRepo $segementationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        $data = json_decode($request->getContent(), true);
        $segment = $segementationRepo->getListeSgementByStatus(1);dump($segment);
        for ($s=0; $s < count($segment) ; $s++) {
            $entities = json_decode($segment[$s]['entities']);
            
            if(in_array('creance',$entities))
            {
                $queryEntities = "App\Entity\Debiteur deb,App\Entity\Creance c";
                $queryConditions = " ";
                $param = array();
                $id = $segment[$s]["id"];
                $groupe = $segementationRepo->getCritereSegmentation($id);
                for ($j=0; $j < count($groupe) ; $j++) {
                    if(0 == $j)
                    {
                        $operateur0[$j] =" ";
                    }
                    else
                    {
                        
                        $operateur0[$j] = " and ";
                    }

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
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( identity(c.id_type_creance) LIKE :type_creance".$k."_".$i.") ";
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

                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                    
                                        // Check if it's "supérieur" or "inférieur" and assign the appropriate operator
                                        $operator = $details[$i]["action"] === "2" ? ">" : "<";
                                        
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.date_echeance $operator :date_echeance" . $k . "_" . $i . ")";
                                        $param['date_echeance' . $k . '_' . $i] = $start;
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                        $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                                    
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." " . $operateur[$k] . " " . $operateur1[$i] . " (c.date_echeance BETWEEN :date_echeance1" . $k . "_" . $i . " AND :date_echeance2" . $k . "_" . $i . ")";
                                        $param['date_echeance1' . $k . '_' . $i] = $start;
                                        $param['date_echeance2' . $k . '_' . $i] = $end;
                                    }
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
                                    // $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.total_creance between :total_creance1".$k."_".$i." and :total_creance2".$k."_".$i.") ";
                                    // $param['total_creance1'.$k.'_'.$i] = $details[$i]["value1"];
                                    // $param['total_creance2'.$k.'_'.$i] = $details[$i]["value2"];

                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." " . $operateur[$k] . " " . $operateur1[$i] . " (c.total_creance " . ($details[$i]["action"] === "2" ? ">" : "<") . " :total_creance" . $k . "_" . $i . ")";
                                        $param['total_creance' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.total_creance BETWEEN :total_creance1" . $k . "_" . $i . " AND :total_creance2" . $k . "_" . $i . ")";
                                        $param['total_creance1' . $k . '_' . $i] = $details[$i]["value1"];
                                        $param['total_creance2' . $k . '_' . $i] = $details[$i]["value2"];
                                    }
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

                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.totalRestant " . ($details[$i]["action"] === "2" ? ">" : "<") . " :total_restant" . $k . "_" . $i . ")";
                                        $param['total_restant' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.totalRestant BETWEEN :total_restant1" . $k . "_" . $i . " AND :total_restant2" . $k . "_" . $i . ")";
                                        $param['total_restant1' . $k . '_' . $i] = $details[$i]["value1"];
                                        $param['total_restant2' . $k . '_' . $i] = $details[$i]["value2"];
                                    }

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

                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = identity(gc.id_creance) and identity(gc.id_garantie) = g.id and  g.type_garantie LIKE :type_garantie".$k."_".$i.") ";
                                    $param['type_garantie'.$k.'_'.$i] = $details[$i]["value1"]; 
                                }
                            }
                            if($criteres[$k]["critere"] == "Taux"){
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

                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ") ." " . $operateur[$k] . " " . $operateur1[$i] . " ( c.id = identity(gc.id_creance) and identity(gc.id_garantie) = g.id and  g.taux " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(gc.id_creance) and identity(gc.id_garantie) = g.id and  g.taux BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                        $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                                    }
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
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." ".$operateur[$k]." ".$operateur1[$i]." (  p.id = identity(c.id_ptf) and identity(p.id_donneur_ordre) = dn.id and  identity(dn.id_type) = :type_donneur".$k."_".$i.") ";
                                    $param['type_donneur'.$k.'_'.$i] = $details[$i]["value1"];dump($details[$i]["value1"]); 
                                }
                            }
                        }
                    }
                    if($groupe[$j]['groupe'] == "Porte feuille"){
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

                            if($criteres[$k]["critere"] == "Date début gestion"){
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
                                    
                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                    
                                        // Check if it's "supérieur" or "inférieur" and assign the appropriate operator
                                        $operator = $details[$i]["action"] === "2" ? ">" : "<";
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (   p.id = identity(c.id_ptf) and p.date_debut_gestion $operator :date_debut_gestion" . $k . "_" . $i . ")";
                                        $param['date_debut_gestion' . $k . '_' . $i] = $start;

                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                        $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (    p.id = identity(c.id_ptf) and p.date_debut_gestion BETWEEN :date_debut_gestion1" . $k . "_" . $i . " AND :date_debut_gestion2" . $k . "_" . $i . ")";
                                        $param['date_debut_gestion1' . $k . '_' . $i] = $start;
                                        $param['date_debut_gestion2' . $k . '_' . $i] = $end;
                                    }
                                }
                            }
                            if($criteres[$k]["critere"] == "Date fin gestion"){
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

                                    // $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                    // $end = $this->GeneralService->dateEnd($details[$i]["value2"]);

                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                    
                                        // Check if it's "supérieur" or "inférieur" and assign the appropriate operator
                                        $operator = $details[$i]["action"] === "2" ? ">" : "<";
                                        
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( p.id = identity(d.id_ptf) and p.date_fin_gestion $operator :date_fin_gestion" . $k . "_" . $i . ")";
                                        $param['date_fin_gestion' . $k . '_' . $i] = $start;

                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                        $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                                    
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (  p.id = identity(d.id_ptf) and p.date_fin_gestion BETWEEN :date_fin_gestion1" . $k . "_" . $i . " AND :date_fin_gestion2" . $k . "_" . $i . ")";
                                        $param['date_fin_gestion1' . $k . '_' . $i] = $start;
                                        $param['date_fin_gestion2' . $k . '_' . $i] = $end;
                                    }
                                }
                            }
                        }
                    }
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
                                    /*$queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(dc.id_creance) and   dc.principale between :VALUE1".$k."_".$i." and :VALUE2".$k."_".$i.") ";
                                    $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                    $param['VALUE2'.$k.'_'.$i] = $details[$i]["value2"];*/
                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(dc.id_creance) AND dc.principale " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(dc.id_creance) AND dc.principale BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                        $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                                    }
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
                                    // $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(dc.id_creance) and   dc.frais between :VALUE1".$k."_".$i." and :VALUE2".$k."_".$i.") ";
                                    // $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                    // $param['VALUE2'.$k.'_'.$i] = $details[$i]["value2"];

                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(dc.id_creance) AND dc.frais " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(dc.id_creance) AND dc.frais BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                        $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                                    }
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
                                    // $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(dc.id_creance) and   dc.interet between :VALUE1".$k."_".$i." and :VALUE2".$k."_".$i.") ";
                                    // $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                    // $param['VALUE2'.$k.'_'.$i] = $details[$i]["value2"];
                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(dc.id_creance) AND dc.interet " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(dc.id_creance) AND dc.interet BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                        $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                                    }
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
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(t.id_creance)  and (tel.id_debiteur)=deb.id  and  identity(tel.id_type_tel) like :typeTel".$k."_".$i." ) ";
                                    $param['typeTel'.$k.'_'.$i] = $details[$i]["value1"];
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
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(t.id_creance)  and (tel.id_debiteur)=deb.id  and  identity(tel.id_status) like :statusTel".$k."_".$i." ) ";
                                    $param['statusTel'.$k.'_'.$i] = $details[$i]["value1"];
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
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(t.id_creance)  and (ad.id_debiteur)=deb.id  and  identity(ad.id_type_adresse) like :typeAdresse".$k."_".$i." ) ";
                                    $param['typeAdresse'.$k.'_'.$i] = $details[$i]["value1"];
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
                                    $queryConditions .=  (0 == $k ? $operateur0[$j] : " ")." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(t.id_creance)  and (ad.id_debiteur)=deb.id  and  identity(ad.id_status) like :statusAdr".$k."_".$i." ) ";
                                    $param['statusAdr'.$k.'_'.$i] = $details[$i]["value1"];
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
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(t.id_creance)  and  deb.type_personne like :VALUE1".$k."_".$i." ) ";
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

                                    $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                    $end = $this->GeneralService->dateEnd($details[$i]["value2"]);

                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(t.id_creance)   and  identity(t.id_type) like :VALUE1".$k."_".$i." ) ";
                                    $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                }
                            }
                        }
                    }

                    if($groupe[$j]['groupe'] == "Procédure judiciaire"){
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
                            if($criteres[$k]["critere"] == "Type procédure"){
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
                                    if(strpos($queryEntities,",App\Entity\ProcCreance pc") == false)
                                    {
                                        $queryEntities .= ",App\Entity\ProcCreance pc";
                                    }
                                    if(strpos($queryEntities,",App\Entity\ProcJudicaire pj") == false)
                                    {
                                        $queryEntities .= ",App\Entity\ProcJudicaire pj";
                                    }
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(pc.id_creance) and identity(pc.id_proc) = pj.id and  pj.type_proc_judicaire LIKE :type_proc_judicaire".$k."_".$i.") ";
                                    $param['type_proc_judicaire'.$k.'_'.$i] = $details[$i]["value1"]; 
                                }
                            }
                        }
                    }

                    if($groupe[$j]['groupe'] == "Emploi"){
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
                            if($criteres[$k]["critere"] == "Status emploi"){
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
                                    if(strpos($queryEntities,",App\Entity\Emploi em") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Emploi em";
                                    }
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(t.id_creance) and t.id_debiteur = identity(em.id_debiteur) and identity(em.id_status) like :status_emploi".$k."_".$i." ) ";
                                    $param['status_emploi'.$k.'_'.$i] = $details[$i]["value1"]; 
                                }
                            }
                            if($criteres[$k]["critere"] == "Date début"){
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
                                    if(strpos($queryEntities,",App\Entity\Emploi em") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Emploi em";
                                    }

                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(t.id_creance) AND t.id_debiteur = identity(em.id_debiteur) AND identity(em.id_status) AND em.dateDebut " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(t.id_creance) AND t.id_debiteur = identity(em.id_debiteur) AND identity(em.id_status) AND em.dateDebut BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                        $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                                    }
                                    
                                }
                            }

                            if($criteres[$k]["critere"] == "Date fin"){
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
                                    if(strpos($queryEntities,",App\Entity\Emploi em") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Emploi em";
                                    }

                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(t.id_creance) AND t.id_debiteur = identity(em.id_debiteur) AND identity(em.id_status) AND em.dateFin " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(t.id_creance) AND t.id_debiteur = identity(em.id_debiteur) AND identity(em.id_status) AND em.dateFin BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                        $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                                    }
                                }
                            }
                        }
                    }

                    if($groupe[$j]['groupe'] == "Employeur"){
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
                            if($criteres[$k]["critere"] == "Status employeur"){
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
                                    if(strpos($queryEntities,",App\Entity\Employeur emp") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Employeur emp";
                                    }
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(t.id_creance) and t.id_debiteur = identity(emp.id_debiteur) and identity(emp.id_status) like :status_employeur".$k."_".$i." ) ";
                                    $param['status_employeur'.$k.'_'.$i] = $details[$i]["value1"]; 
                                }
                            }
                            
                        }
                    }
                    if($groupe[$j]['groupe'] == "Accord"){
                        $criteres = $groupe[$j]["criteres"];dump($criteres);
                        for ($k= 0; $k < count($criteres);$k++){
                            if($k==0)
                            {
                                $operateur[$k]="";
                            }
                            else
                            {
                                $operateur[$k]=" and ";
                            }
                            if($criteres[$k]["critere"] == "Status accord"){
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
                                    if(strpos($queryEntities,",App\Entity\Accord ac") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Accord ac";
                                    }
                                    if(strpos($queryEntities,",App\Entity\CreanceAccord ca") == false)
                                    {
                                        $queryEntities .= ",App\Entity\CreanceAccord ca";
                                    }
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = identity(ca.id_creance) and identity(ac.id_status) like :status_accord".$k."_".$i." ) ";
                                    $param['status_accord'.$k.'_'.$i] = $details[$i]["value1"]; 
                                }
                            }
                            if($criteres[$k]["critere"] == "Date création"){
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
                                    if(strpos($queryEntities,",App\Entity\Accord ac") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Accord ac";
                                    }
                                    if(strpos($queryEntities,",App\Entity\CreanceAccord ca") == false)
                                    {
                                        $queryEntities .= ",App\Entity\CreanceAccord ca";
                                    }

                                    $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(ca.id_creance) AND ac.dateCreation " . ($details[$i]["action"] === "2" ? ">" : "<") . " :dateStart_" . $k . "_" . $i . ")";
                                        $param['dateStart_' . $k . '_' . $i] = $start;
                                    } elseif ($details[$i]["action"] === "1") {
                                        $end = $this->GeneralService->dateEnd($details[$i]["value2"]);dump($end);
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(ca.id_creance) AND ac.dateCreation BETWEEN :dateStart_" . $k . "_" . $i . " AND :dateFin_" . $k . "_" . $i . ")";
                                        $param['dateStart_' . $k . '_' . $i] = $start;
                                        $param['dateFin_' . $k . '_' . $i] = $end;
                                    }
                                }
                            }

                            if($criteres[$k]["critere"] == "Montant"){
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
                                    if(strpos($queryEntities,",App\Entity\Accord ac") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Accord ac";
                                    }
                                    if(strpos($queryEntities,",App\Entity\CreanceAccord ca") == false)
                                    {
                                        $queryEntities .= ",App\Entity\CreanceAccord ca";
                                    }

                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(ca.id_creance) AND ac.montant " . ($details[$i]["action"] === "2" ? ">" : "<") . " :valueMontant1_" . $k . "_" . $i . ")";
                                        $param['valueMontant1_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(ca.id_creance) AND ac.montant BETWEEN :valueMontant1_" . $k . "_" . $i . " AND :valueMontant2_" . $k . "_" . $i . ")";
                                        $param['valueMontant1_' . $k . '_' . $i] =  $details[$i]["value1"];
                                        $param['valueMontant2_' . $k . '_' . $i] =  $details[$i]["value2"];
                                    }
                                }
                            }

                            if($criteres[$k]["critere"] == "Montant à payer"){
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
                                    if(strpos($queryEntities,",App\Entity\Accord ac") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Accord ac";
                                    }
                                    if(strpos($queryEntities,",App\Entity\CreanceAccord ca") == false)
                                    {
                                        $queryEntities .= ",App\Entity\CreanceAccord ca";
                                    }

                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(ca.id_creance) AND ac.montant_a_payer " . ($details[$i]["action"] === "2" ? ">" : "<") . " :valueMontant1_" . $k . "_" . $i . ")";
                                        $param['valueMontant1_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(ca.id_creance) AND ac.montant_a_payer BETWEEN :valueMontant1_" . $k . "_" . $i . " AND :valueMontant2_" . $k . "_" . $i . ")";
                                        $param['valueMontant1_' . $k . '_' . $i] =  $details[$i]["value1"];
                                        $param['valueMontant2_' . $k . '_' . $i] =  $details[$i]["value2"];
                                    }
                                }
                            }
                        }
                    }

                    if($groupe[$j]['groupe'] == "Paiement"){
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
                            if($criteres[$k]["critere"] == "Type paiement"){
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
                                    if(strpos($queryEntities,",App\Entity\Accord ac") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Accord ac";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Paiement pm") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Paiement pm";
                                    }
                                    
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = identity(pm.id_creance) and identity(pm.id_type_paiement) like :typeP".$k."_".$i." ) ";
                                    $param['typeP'.$k.'_'.$i] = $details[$i]["value1"]; 
                                }
                            }

                            if($criteres[$k]["critere"] == "Date paiement"){
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
                                    if(strpos($queryEntities,",App\Entity\Accord ac") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Accord ac";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Paiement pm") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Paiement pm";
                                    }
                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(pm.id_creance) AND pm.date_paiement " . ($details[$i]["action"] === "2" ? ">" : "=") . " :dateStart_" . $k . "_" . $i . ")";
                                        $param['dateStart_' . $k . '_' . $i] = $start;
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                        $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(pm.id_creance) AND pm.date_paiement BETWEEN :dateStart_" . $k . "_" . $i . " AND :dateFin_" . $k . "_" . $i . ")";
                                        $param['dateStart_' . $k . '_' . $i] = $start;
                                        $param['dateFin_' . $k . '_' . $i] = $end;
                                    }
                                    
                                }
                            }
                            
                            if($criteres[$k]["critere"] == "Montant de paiment"){
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
                                    if(strpos($queryEntities,",App\Entity\Accord ac") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Accord ac";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Paiement pm") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Paiement pm";
                                    }
                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(pm.id_creance) AND pm.montant " . ($details[$i]["action"] === "2" ? ">" : "=") . " :value1_" . $k . "_" . $i . ")";
                                        $param['dateStart_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = identity(pm.id_creance) AND pm.montant BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] =  $details[$i]["value1"];
                                        $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                                    }
                                }
                            }
                        }
                    }
                    if($groupe[$j]['groupe'] == "Dossier"){
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
                            if($criteres[$k]["critere"] == "Qualification dossier"){
                                $details = $criteres[$k]["details"];dump($details);
                                for ($i=0; $i < count($details ); $i++) { 
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Dossier dss") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Dossier dss";
                                    }
                                    
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  dss.id = identity(c.id_dossier)  and  identity(dss.id_qualification) = :qualification".$k."_".$i.") ";
                                    $param['qualification'.$k.'_'.$i] = $details[$i]["value1"]; 
                                }
                            }
                        }
                    }
                }
                if($queryConditions != " "){
                    $rqCreance = "SELECT DISTINCT c.id  FROM  ". $queryEntities . " where " . $queryConditions. "" ; 
                    $query = $this->em->createQuery($rqCreance);
                    $query->setParameters($param);
                    $resultCreance = $query->getResult();

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
                $queryEntities = "App\Entity\Debiteur deb,App\Entity\Dossier d";
                $queryConditions = " ";
                $param = array();
                $id = $segment[$s]["id"];
                $groupe = $segementationRepo->getCritereSegmentation($id);
                for ($j=0; $j < count($groupe) ; $j++) {
                    if($j==0)
                    {
                        $operateur0[$j]="";
                    }
                    else
                    {
                        $operateur0[$j] =" and ";
                    }
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
                            if(strpos($queryEntities,",App\Entity\Creance c") == false)
                            {
                                $queryEntities .= ",App\Entity\Creance c";
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
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( identity(c.id_dossier) = d.id and identity(c.id_type_creance) LIKE :type_creance".$k."_".$i.") ";
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
    
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( identity(c.id_dossier) = d.id and c.date_echeance between :date_echeance1".$k."_".$i." and :date_echeance2".$k."_".$i.") ";
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
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( identity(c.id_dossier) = d.id and c.total_creance between :total_creance1".$k."_".$i." and :total_creance2".$k."_".$i.") ";
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
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( identity(c.id_dossier) = d.id and c.totalRestant between :total_restant1".$k."_".$i." and :total_restant2".$k."_".$i.") ";
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
                            if(strpos($queryEntities,",App\Entity\Creance c") == false)
                            {
                                $queryEntities .= ",App\Entity\Creance c";
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
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( identity(c.id_dossier) = d.id and c.id = identity(gc.id_creance) and identity(gc.id_garantie) = g.id and g.taux between :taux_garantie1".$k."_".$i." and :taux_garantie2".$k."_".$i.") ";
                                    $param['taux_garantie1'.$k.'_'.$i] = $details[$i]["value1"];
                                    $param['taux_garantie2'.$k.'_'.$i] = $details[$i]["value2"];
                                }
                            }
                            if($criteres[$k]["critere"] == "Taux"){
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
                                    
                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( identity(c.id_dossier) = d.id and c.id = identity(gc.id_creance) and identity(gc.id_garantie) = g.id and  g.taux " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (identity(c.id_dossier) = d.id and c.id = identity(gc.id_creance) and identity(gc.id_garantie) = g.id and  g.taux BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                        $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                                    }
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
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  p.id = identity(d.id_ptf) and identity(p.id_donneur_ordre) = dn.id and  identity(dn.id_type) = :type_donneur".$k."_".$i.") ";
                                    $param['type_donneur'.$k.'_'.$i] = $details[$i]["value1"];
                                }
                            }
                        }
                    }
                    if($groupe[$j]['groupe'] == "Porte feuille"){
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
                            
                            if($criteres[$k]["critere"] == "Date début gestion"){
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
                                    
                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                    
                                        // Check if it's "supérieur" or "inférieur" and assign the appropriate operator
                                        $operator = $details[$i]["action"] === "2" ? ">" : "<";
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( p.id = identity(d.id_ptf) and p.date_debut_gestion $operator :date_debut_gestion" . $k . "_" . $i . ")";
                                        $param['date_debut_gestion' . $k . '_' . $i] = $start;

                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                        $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (  p.id = identity(d.id_ptf) and p.date_debut_gestion BETWEEN :date_debut_gestion1" . $k . "_" . $i . " AND :date_debut_gestion2" . $k . "_" . $i . ")";
                                        $param['date_debut_gestion1' . $k . '_' . $i] = $start;
                                        $param['date_debut_gestion2' . $k . '_' . $i] = $end;
                                    }
                                }
                            }

                            if($criteres[$k]["critere"] == "Date fin gestion"){
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

                                    // $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                    // $end = $this->GeneralService->dateEnd($details[$i]["value2"]);

                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                    
                                        // Check if it's "supérieur" or "inférieur" and assign the appropriate operator
                                        $operator = $details[$i]["action"] === "2" ? ">" : "<";
                                        
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( p.id = identity(d.id_ptf) and p.date_fin_gestion $operator :date_fin_gestion" . $k . "_" . $i . ")";
                                        $param['date_fin_gestion' . $k . '_' . $i] = $start;

                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                        $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                                    
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (  p.id = identity(d.id_ptf) and p.date_fin_gestion BETWEEN :date_fin_gestion1" . $k . "_" . $i . " AND :date_fin_gestion2" . $k . "_" . $i . ")";
                                        $param['date_fin_gestion1' . $k . '_' . $i] = $start;
                                        $param['date_fin_gestion2' . $k . '_' . $i] = $end;
                                    }
                                }
                            }
                        }
                    }
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

                            if(strpos($queryEntities,",App\Entity\Creance c") == false)
                            {
                                $queryEntities .= ",App\Entity\Creance c";
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
                                    // $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( identity(c.id_dossier) = d.id and c.id = identity(dc.id_creance) and   dc.principale between :VALUE1".$k."_".$i." and :VALUE2".$k."_".$i.") ";
                                    // $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                    // $param['VALUE2'.$k.'_'.$i] = $details[$i]["value2"];

                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (identity(c.id_dossier) = d.id and c.id = identity(dc.id_creance) and dc.principale " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (identity(c.id_dossier) = d.id and c.id = identity(dc.id_creance) and dc.principale BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                        $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                                    }
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
                                    // $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( identity(c.id_dossier) = d.id and c.id = identity(dc.id_creance) and   dc.frais between :VALUE1".$k."_".$i." and :VALUE2".$k."_".$i.") ";
                                    // $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                    // $param['VALUE2'.$k.'_'.$i] = $details[$i]["value2"];
                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (identity(c.id_dossier) = d.id and c.id = identity(dc.id_creance) and   dc.frais " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (identity(c.id_dossier) = d.id and c.id = identity(dc.id_creance) and   dc.frais BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                        $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                                    }
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
                                    // $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( identity(c.id_dossier) = d.id and c.id = identity(dc.id_creance) and   dc.interet between :VALUE1".$k."_".$i." and :VALUE2".$k."_".$i.") ";
                                    // $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                    // $param['VALUE2'.$k.'_'.$i] = $details[$i]["value2"];
                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (identity(c.id_dossier) = d.id and c.id = identity(dc.id_creance) and   dc.interet " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (identity(c.id_dossier) = d.id and c.id = identity(dc.id_creance) and   dc.interet BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                        $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                                    }
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
                                    if(strpos($queryEntities,",App\Entity\DebiDoss DD") == false)
                                    {
                                        $queryEntities .= ",App\Entity\DebiDoss DD";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Telephone tel") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Telephone tel";
                                    }
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." ".$operateur[$k]." ".$operateur1[$i]." ( d.id = identity(DD.id_dossier)  and (tel.id_debiteur)=deb.id  and  identity(tel.id_type_tel) like :typeTel".$k."_".$i." ) ";
                                    $param['typeTel'.$k.'_'.$i] = $details[$i]["value1"];
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
                                    if(strpos($queryEntities,",App\Entity\DebiDoss DD") == false)
                                    {
                                        $queryEntities .= ",App\Entity\DebiDoss DD";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Telephone tel") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Telephone tel";
                                    }
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( d.id = identity(DD.id_dossier)  and (tel.id_debiteur)=deb.id  and  identity(tel.id_status) like :statusTel".$k."_".$i." ) ";
                                    $param['statusTel'.$k.'_'.$i] = $details[$i]["value1"];
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
                                    if(strpos($queryEntities,",App\Entity\DebiDoss DD") == false)
                                    {
                                        $queryEntities .= ",App\Entity\DebiDoss DD";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Adresse ad") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Adresse ad";
                                    }
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  d.id = identity(DD.id_dossier)  and (ad.id_debiteur)=deb.id  and  identity(ad.id_type_adresse) like :VALUE1".$k."_".$i." ) ";
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
                                    if(strpos($queryEntities,",App\Entity\DebiDoss DD") == false)
                                    {
                                        $queryEntities .= ",App\Entity\DebiDoss DD";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Adresse ad") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Adresse ad";
                                    }
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  d.id = identity(DD.id_dossier)  and (ad.id_debiteur)=deb.id  and  identity(ad.id_status) like :VALUE1".$k."_".$i." ) ";
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
                                    if(strpos($queryEntities,",App\Entity\DebiDoss DD") == false)
                                    {
                                        $queryEntities .= ",App\Entity\DebiDoss DD";
                                    }
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  d.id = identity(DD.id_dossier)  and identity(c.id_dossier) = d.id and c.id = identity(t.id_creance)  and  deb.type_personne like :VALUE1".$k."_".$i." ) ";
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
                                    if(strpos($queryEntities,",App\Entity\DebiDoss DD") == false)
                                    {
                                        $queryEntities .= ",App\Entity\DebiDoss DD";
                                    }
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  d.id = identity(DD.id_dossier)  and identity(c.id_dossier) = d.id and c.id = identity(t.id_creance)   and  identity(t.id_type) like :VALUE1".$k."_".$i." ) ";
                                    $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                                }
                            }
                        }
                    }

                    if($groupe[$j]['groupe'] == "Procédure judiciaire"){
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
                            if($criteres[$k]["critere"] == "Type procédure"){
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
                                    if(strpos($queryEntities,",App\Entity\ProcCreance pc") == false)
                                    {
                                        $queryEntities .= ",App\Entity\ProcCreance pc";
                                    }
                                    if(strpos($queryEntities,",App\Entity\ProcJudicaire pj") == false)
                                    {
                                        $queryEntities .= ",App\Entity\ProcJudicaire pj";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Creance c";
                                    }
                                    
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( identity(c.id_dossier) = d.id  and c.id = identity(pc.id_creance) and identity(pc.id_proc) = pj.id and  pj.type_proc_judicaire LIKE :type_proc_judicaire".$k."_".$i.") ";
                                    $param['type_proc_judicaire'.$k.'_'.$i] = $details[$i]["value1"]; 
                                }
                            }
                        }
                    }

                    if($groupe[$j]['groupe'] == "Emploi"){
                        $criteres = $groupe[$j]["criteres"];
                        for ($k= 0; $k < count($criteres);$k++){
                            if($k==0)
                            {
                                $operateur[$k]=" ";
                            }
                            else
                            {
                                $operateur[$k]=" and ";
                            }

                            if($criteres[$k]["critere"] == "Status emploi"){
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
                                    if(strpos($queryEntities,",App\Entity\Emploi em") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Emploi em";
                                    }
                                    if(strpos($queryEntities,",App\Entity\DebiDoss DD") == false)
                                    {
                                        $queryEntities .= ",App\Entity\DebiDoss DD";
                                    }
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." ".$operateur[$k]." ".$operateur1[$i]." ( d.id = identity(DD.id_dossier) and identity(DD.id_debiteur) = identity(em.id_debiteur) and identity(em.id_status) like :status_emploi".$k."_".$i." ) ";
                                    $param['status_emploi'.$k.'_'.$i] = $details[$i]["value1"]; 
                                }
                            }

                            if($criteres[$k]["critere"] == "Date début"){
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
                                    if(strpos($queryEntities,",App\Entity\Emploi em") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Emploi em";
                                    } 
                                    if(strpos($queryEntities,",App\Entity\DebiDoss DD") == false)
                                    {
                                        $queryEntities .= ",App\Entity\DebiDoss DD";
                                    }
                                    if($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (d.id = identity(DD.id_dossier) and identity(DD.id_debiteur) = identity(em.id_debiteur) AND em.dateDebut " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (d.id = identity(DD.id_dossier) and identity(DD.id_debiteur) = identity(em.id_debiteur)  AND em.dateDebut BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                        $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                                    }
                                }
                            }

                            if($criteres[$k]["critere"] == "Date fin"){
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
                                    if(strpos($queryEntities,",App\Entity\Emploi em") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Emploi em";
                                    }
                                    if(strpos($queryEntities,",App\Entity\DebiDoss DD") == false)
                                    {
                                        $queryEntities .= ",App\Entity\DebiDoss DD";
                                    }
                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (d.id = identity(DD.id_dossier) and identity(DD.id_debiteur) = identity(em.id_debiteur)  AND em.dateFin " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (d.id = identity(DD.id_dossier) and identity(DD.id_debiteur) = identity(em.id_debiteur) AND em.dateFin BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                                        $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                                    }
                                }
                            }
                        }
                    }

                    if($groupe[$j]['groupe'] == "Employeur"){
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
                            if($criteres[$k]["critere"] == "Status employeur"){
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
                                    if(strpos($queryEntities,",App\Entity\Employeur emp") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Employeur emp";
                                    }
                                    if(strpos($queryEntities,",App\Entity\DebiDoss DD") == false)
                                    {
                                        $queryEntities .= ",App\Entity\DebiDoss DD";
                                    }
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(t.id_creance) and t.id_debiteur = identity(emp.id_debiteur) and identity(emp.id_status) like :status_employeur".$k."_".$i." ) ";
                                    $param['status_employeur'.$k.'_'.$i] = $details[$i]["value1"]; 
                                }
                            }
                        }
                    }

                    if($groupe[$j]['groupe'] == "Accord"){
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
                            if($criteres[$k]["critere"] == "Status accord"){
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
                                    if(strpos($queryEntities,",App\Entity\Accord ac") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Accord ac";
                                    }
                                    if(strpos($queryEntities,",App\Entity\CreanceAccord ca") == false)
                                    {
                                        $queryEntities .= ",App\Entity\CreanceAccord ca";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Creance c";
                                    }
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (d.id =  c.id_dossier and c.id = identity(ca.id_creance) and identity(ac.id_status) like :status_accord".$k."_".$i." ) ";
                                    $param['status_accord'.$k.'_'.$i] = $details[$i]["value1"]; 
                                }
                            }
                            if($criteres[$k]["critere"] == "Date création"){
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
                                    if(strpos($queryEntities,",App\Entity\Accord ac") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Accord ac";
                                    }
                                    if(strpos($queryEntities,",App\Entity\CreanceAccord ca") == false)
                                    {
                                        $queryEntities .= ",App\Entity\CreanceAccord ca";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Creance c";
                                    }

                                    $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (d.id =  c.id_dossier and c.id = identity(ca.id_creance) AND ac.dateCreation " . ($details[$i]["action"] === "2" ? ">" : "<") . " :dateStart_" . $k . "_" . $i . ")";
                                        $param['dateStart_' . $k . '_' . $i] = $start;
                                    } elseif ($details[$i]["action"] === "1") {
                                        $end = $this->GeneralService->dateEnd($details[$i]["value2"]);dump($end);
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (d.id =  c.id_dossier and c.id = identity(ca.id_creance) AND ac.dateCreation BETWEEN :dateStart_" . $k . "_" . $i . " AND :dateFin_" . $k . "_" . $i . ")";
                                        $param['dateStart_' . $k . '_' . $i] = $start;
                                        $param['dateFin_' . $k . '_' . $i] = $end;
                                    }
                                }
                            }

                            if($criteres[$k]["critere"] == "Montant"){
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
                                    if(strpos($queryEntities,",App\Entity\Accord ac") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Accord ac";
                                    }
                                    if(strpos($queryEntities,",App\Entity\CreanceAccord ca") == false)
                                    {
                                        $queryEntities .= ",App\Entity\CreanceAccord ca";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Creance c";
                                    }
                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (d.id =  c.id_dossier and c.id = identity(ca.id_creance) AND ac.montant " . ($details[$i]["action"] === "2" ? ">" : "<") . " :valueMontant1_" . $k . "_" . $i . ")";
                                        $param['valueMontant1_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (d.id =  c.id_dossier and c.id = identity(ca.id_creance) AND ac.montant BETWEEN :valueMontant1_" . $k . "_" . $i . " AND :valueMontant2_" . $k . "_" . $i . ")";
                                        $param['valueMontant1_' . $k . '_' . $i] =  $details[$i]["value1"];
                                        $param['valueMontant2_' . $k . '_' . $i] =  $details[$i]["value2"];
                                    }
                                }
                            }

                            if($criteres[$k]["critere"] == "Montant à payer"){
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
                                    if(strpos($queryEntities,",App\Entity\Accord ac") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Accord ac";
                                    }
                                    if(strpos($queryEntities,",App\Entity\CreanceAccord ca") == false)
                                    {
                                        $queryEntities .= ",App\Entity\CreanceAccord ca";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Creance c";
                                    }
                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (d.id =  c.id_dossier and c.id = identity(ca.id_creance) AND ac.montant_a_payer " . ($details[$i]["action"] === "2" ? ">" : "<") . " :valueMontant1_" . $k . "_" . $i . ")";
                                        $param['valueMontant1_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (d.id =  c.id_dossier and c.id = identity(ca.id_creance) AND ac.montant_a_payer BETWEEN :valueMontant1_" . $k . "_" . $i . " AND :valueMontant2_" . $k . "_" . $i . ")";
                                        $param['valueMontant1_' . $k . '_' . $i] =  $details[$i]["value1"];
                                        $param['valueMontant2_' . $k . '_' . $i] =  $details[$i]["value2"];
                                    }
                                }
                            }
                        }
                    }

                    if($groupe[$j]['groupe'] == "Paiement"){
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
                            if($criteres[$k]["critere"] == "Type paiement"){
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

                                    if(strpos($queryEntities,",App\Entity\Accord ac") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Accord ac";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Paiement pm") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Paiement pm";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Creance c";
                                    }

                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (d.id =  c.id_dossier and c.id = identity(pm.id_creance) and identity(pm.id_type_paiement) like :typeP".$k."_".$i." ) ";
                                    $param['typeP'.$k.'_'.$i] = $details[$i]["value1"]; 
                                }
                            }

                            if($criteres[$k]["critere"] == "Date paiement"){
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

                                    if(strpos($queryEntities,",App\Entity\Accord ac") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Accord ac";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Paiement pm") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Paiement pm";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Creance c";
                                    }

                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( d.id =  identity(c.id_dossier) and c.id = identity(pm.id_creance) AND pm.date_paiement " . ($details[$i]["action"] === "2" ? ">" : "=") . " :dateStart_" . $k . "_" . $i . ")";
                                        $param['dateStart_' . $k . '_' . $i] = $start;
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                        $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (d.id =  identity(c.id_dossier) and c.id = identity(pm.id_creance) AND pm.date_paiement BETWEEN :dateStart_" . $k . "_" . $i . " AND :dateFin_" . $k . "_" . $i . ")";
                                        $param['dateStart_' . $k . '_' . $i] = $start;
                                        $param['dateFin_' . $k . '_' . $i] = $end;
                                    }
                                    
                                }
                            }
                            
                            if($criteres[$k]["critere"] == "Montant de paiment"){
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
                                    if(strpos($queryEntities,",App\Entity\Accord ac") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Accord ac";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Paiement pm") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Paiement pm";
                                    }
                                    if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                    {
                                        $queryEntities .= ",App\Entity\Creance c";
                                    }
                                    if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                        // If "supérieur" or "inférieur"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( d.id =  identity(c.id_dossier) and c.id = identity(pm.id_creance) AND pm.montant " . ($details[$i]["action"] === "2" ? ">" : "=") . " :value1_" . $k . "_" . $i . ")";
                                        $param['dateStart_' . $k . '_' . $i] = $details[$i]["value1"];
                                    } elseif ($details[$i]["action"] === "1") {
                                        // If "between"
                                        $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( d.id =  identity(c.id_dossier) and c.id = identity(pm.id_creance) AND pm.montant BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
                                        $param['value1_' . $k . '_' . $i] =  $details[$i]["value1"];
                                        $param['value2_' . $k . '_' . $i] = $details[$i]["value2"];
                                    }
                                }
                            }
                        }
                    }

                    if($groupe[$j]['groupe'] == "Dossier"){
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
                            if($criteres[$k]["critere"] == "Qualification dossier"){
                                $details = $criteres[$k]["details"];dump($details);
                                for ($i=0; $i < count($details ); $i++) { 
                                    if($i==0)
                                    {
                                        $operateur1[$i]="";
                                    }
                                    else
                                    {
                                        $operateur1[$i]=" or ";
                                    }
                                    
                                    
                                    $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( identity(dss.id_qualification) = :qualification".$k."_".$i.") ";
                                    $param['qualification'.$k.'_'.$i] = $details[$i]["value1"]; 
                                }
                            }
                        }
                    }
                }
                if($queryConditions != " "){
                    $rqDossier = "SELECT DISTINCT d.id  FROM  ". $queryEntities . " where " . $queryConditions. "" ; 
                    $query = $this->em->createQuery($rqDossier);
                    $query->setParameters($param);
                    $resultDossier = $query->getResult();
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
            }

            if(in_array('debiteur',$entities))
            {
                if(in_array('creance',$entities))
                {
                    $rqDeb = "SELECT debi.id FROM App\Entity\Debiteur debi WHERE debi.id IN (
                        SELECT IDENTITY(t1.id_debiteur) 
                        FROM App\Entity\TypeDebiteur t1 
                        WHERE t1.id_creance IN (".$rqCreance.")
                    )";
                    $query = $this->em->createQuery($rqDeb);
                    $query->setParameters($param);
                    $resultDebi = $query->getResult();

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
                else if(in_array('dossier',$entities))
                {
                    $rqDeb = "SELECT debi.id FROM App\Entity\Debiteur debi WHERE debi.id IN (
                        SELECT IDENTITY(t1.id_debiteur) 
                        FROM App\Entity\DebiDoss t1 
                        WHERE t1.id_dossier IN (".$rqDossier.")
                    )";
                    $query = $this->em->createQuery($rqDeb);
                    $query->setParameters($param);
                    $resultDebi = $query->getResult();

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
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getTypeWorkflowSeg')]
    public function getTypeWorkflowSeg(Request $request,segementationRepo $segementationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $respObjects["data"]= $segementationRepo->getTypeWorkflowSeg();
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    

   
    #[Route('/getTypeDetailsCreance')]
    public function getTypeDetailsCreance(Request $request,segementationRepo $segementationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $respObjects["data"]  =$segementationRepo->getTypeDetailsCreance($id);
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getDetailsSecteurActiviteInPramas')]
    public function getDetailsSecteurActiviteInPramas(Request $request,segementationRepo $segementationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $respObjects["data"]  =$segementationRepo->getDetailsSecteurActiviteInPramas($id);
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getListeSegmentByGroupe2')]
    public function getListeSegmentByGroupe2(Request $request,segementationRepo $segementationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id_type = $request->get("id_type");
            $id_groupe = $request->get("id_groupe");
            $data = $segementationRepo->getListeSgementationByGroupe2($id_type,$id_groupe);
            $codeStatut = "OK";
            $respObjects["data"] = $data;
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/detailsSegmentation')]
    public function detailsSegmentation(segementationRepo $segementationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $data = $segementationRepo->getDetailsSegment($id);
            if($data["segmentation"]){
                $respObjects["data"] = $data;
                $respObjects["criteres"] = $segementationRepo->getCritereSegmentation($id);
                $codeStatut = "OK";
            }else{
                $codeStatut="NOT_EXIST_ELEMENT";
            }
        }catch (\Exception $e) {
            $respObjects["msg"] = $e->getMessage();
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }

    #[Route('/updateSeg/{id}')]
    public function updateSeg(segementationRepo $segementationRepo , $id,SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try {
           $dataSeg = $segementationRepo->getDetailsSegment($id);
          
           $data = json_decode($request->getContent(), true);
           $titre = $data["titre"];
           $description = $data["description"];
           $entity = $data["entity"];
           $cle = $data["cle"];
           $type = $data["type"];

           if($titre == "" ){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }else{
                $arrayMultiple = $segementationRepo->getCritereMultiple();
                if((count($entity) >= 1)){
                    
                    if($dataSeg){
                        // if($id_type_id == 1 || $id_type_id == 2 || $segementationRepo->findGroupe($queue_groupe_id))
                        // {
                            // $createQueue = $segementationRepo->createQueue($titre,$description,$queue_groupe_id ,$id_type_id , $createSegment->getId() ,$active );
                            $data_critere = $data["data"];
                            $segId =  $id;
                            $priority = 1;
                            if($data_critere >= 1){

                                $segementationRepo->deleteCritere($id);

                                for($i=0; $i < count($data_critere); $i++) { 
                                    $titre_groupe = $data_critere[$i]["groupe"]["titre_groupe"];
                                    $createGroupeQueue = $segementationRepo->createGroupeCritereRepo($titre_groupe,$segId , $priority);
                                    $critere = $data_critere[$i]["critere"];

                                    for ($j=0; $j < count($critere); $j++) { 
                                        $createQueueCritere = $segementationRepo->createSegCritere($critere[$j]["critere"] , $createGroupeQueue , $critere[$j]["type"]);
                                        if($critere[$j]["type"] == 'multiple_check'){
                                            $values = $critere[$j]['values'];
                                            for ($v=0; $v < count($values) ; $v++) {
                                                if(isset($values[$v]["selected"]) && $values[$v]["selected"] == true ){
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
                                                        $values[$v]["id_critere_id"] == 35 
                                                        )
                                                    {
                                                        $value1 =  $values[$v]["id_champ"];
                                                        $segementationRepo->createSegValues($value1 , '' , $createQueueCritere->getId(),null , $values[$v]["value"]);
                                                    }
                                                    else
                                                    {
                                                        $value1 =  $values[$v]["value"];
                                                        $segementationRepo->createSegValues($value1 , '' , $createQueueCritere->getId(),null , $values[$v]["value"]);
                                                    }
                                                }
                                            }
                                        }
                                        if(isset($critere[$j]) && isset($critere[$j]["type"]) && $critere[$j]["type"] == 'montant') {
                                            $values = $critere[$j]['values'];
                                            $action = $critere[$j]['action'] ;
                                            for ($q=0; $q < count($values) ; $q++) {
                                                $value1 =  $values["value1"];
                                                $value2 =  $values["value2"] ?? "";
                                            }
                                            $segementationRepo->createSegValues($value1 , $value2 , $createQueueCritere->getId(),$action,null);
                                        }

                                        if(isset($critere[$j]) && isset($critere[$j]["type"]) && $critere[$j]["type"] == 'date') {
                                            $values = $critere[$j]['values'];
                                            $action = $critere[$j]['action'] ;
                                            for ($q=0; $q < count($values) ; $q++) {
                                                $value1 =  $values["value1"];
                                                $value2 =  $values["value2"] ?? "";
                                            }
                                            $segementationRepo->createSegValues($value1 , $value2 , $createQueueCritere->getId(),$action,null);
                                        }

                                    }
                                    $priority ++;
                                }
                            }
                            $codeStatut="OK";
                        // }else{
                        //     $codeStatut = "ERROR-EMPTY-PARAMS";
                        // }
                    }
                    else{
                        $codeStatut = "ERROR";
                    }
                }else{
                    $codeStatut = "ERROR-EMPTY-PARAMS";
                }
            // }
        }

           $codeStatut='OK';
        }catch (\Exception $e) {
            $respObjects["msg"] = $e->getMessage();
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }
    #[Route('/getCritereSegmentation')]
    public function getCritereSegmentation(Request $request,segementationRepo $segementationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $queue = $segementationRepo->findQueue($id);
            if($queue){
                $data = $segementationRepo->getCritereSegmentation($id);
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
    #[Route('/getValuesSelectedInSegment')]
    public function getValuesSelectedInSegment(Request $request,segementationRepo $segementationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            
            $data = $segementationRepo->getValuesSelectedInSegment($id);
            $codeStatut = "OK";
            $respObjects["data"] = $data;
            
        }catch(\Exception $e){
            $respObjects["err"] = $e->getMessage();
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/majQueueSauvguarde')]
    public function majQueueSauvguarde(Request $request,segementationRepo $segementationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            
        }catch(\Exception $e){
            $respObjects["err"] = $e->getMessage();
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getCleGenerate')]
    public function getCleGenerate(Request $request,segementationRepo $segementationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $maxID = $segementationRepo->getMaxSEG();
            
            $data = "SG".uniqid().($maxID+1);
            $codeStatut = "OK";
            $respObjects["data"] = $data;
            
        }catch(\Exception $e){
            $respObjects["err"] = $e->getMessage();
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
}

