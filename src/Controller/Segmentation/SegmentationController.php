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
        // try{
            // $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $titre = $data["titre"];
            $description = $data["description"];
            $entity = $data["entity"];
            $entity = ['creance','debiteur','dossier','telephone','adresse'];
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
                                         
                                        if(isset($critere[$j]) && isset($critere[$j]["type"]) && ($critere[$j]["type"] == 'montant' || $critere[$j]["type"] == 'drop_down' )) {
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
                                $this->sauvguardeSegementation($request , $segementationRepo,$segId);
                        }
                        else{
                            $codeStatut = "ERROR";
                        }
                    }else{
                        $codeStatut = "ERROR-EMPTY-PARAMS";
                    }
                // }
            }
        // }catch(\Exception $e){
        //     $codeStatut = "ERROR";
        //     $respObjects["msg"] = $e->getMessage();
        // }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/sauvguardeSegementation', methods: ['POST'])]
    public function sauvguardeSegementation(Request $request,segementationRepo $segementationRepo , $idSeg): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        //$segment = $segementationRepo->getListeSgementByStatus(1);
        $segment = $segementationRepo->getListeSgementById($idSeg);
        // try {
            for ($s=0; $s < count($segment) ; $s++) {
                $entities = json_decode($segment[$s]['entities']);
                if(in_array('creance',$entities))
                {
                    $queryEntities = "debt_force_seg.dt_debiteur deb,debt_force_seg.dt_creance c";
                    $queryConditions = " ";
                    $param = array();
                    $id = $segment[$s]["id"];
                    $groupe = $segementationRepo->getCritereSegmentation($id);
                    $queryConditions = " ";
                    $requetOutput = $this->getRequeteCreance($id , $groupe , $queryEntities,$queryConditions,$param);
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
                    $groupe = $segementationRepo->getCritereSegmentation($id);
                    $queryConditions = " ";
                    $requetOutput = $this->getRequeteCreance($id , $groupe , $queryEntities,$queryConditions,$param);
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
                    $groupe = $segementationRepo->getCritereSegmentation($id);
                    $queryConditions = " ";
                    $requetOutput = $this->getRequeteCreance($id , $groupe , $queryEntities,$queryConditions,$param);
                    $queryConditions = $requetOutput["queryConditions"];
                    $queryEntities = $requetOutput["queryEntities"];
                    $param = $requetOutput["param"];
                    $queryEntities = strtolower($queryEntities);

                    $rqCreance = "SELECT DISTINCT c.id  FROM  ". $queryEntities . " where " . $queryConditions. "" ;

                    $rqTelephone = "SELECT tel1.id FROM debt_force_seg.dt_Telephone tel1 WHERE (tel1.id_debiteur_id) IN (
                        SELECT debi.id FROM debt_force_seg.dt_Debiteur debi WHERE debi.id IN (
                        SELECT (t1.id_debiteur_id)
                        FROM debt_force_seg.dt_type_debiteur t1
                        WHERE t1.id_creance_id IN (".$rqCreance."))
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
                    $groupe = $segementationRepo->getCritereSegmentation($id);
                    $queryConditions = " ";
                    $requetOutput = $this->getRequeteCreance($id , $groupe , $queryEntities,$queryConditions,$param);
                    $queryConditions = $requetOutput["queryConditions"];
                    $queryEntities = $requetOutput["queryEntities"];
                    $param = $requetOutput["param"];
                    $queryEntities = strtolower($queryEntities);

                    $rqCreance = "SELECT DISTINCT c.id  FROM  ". $queryEntities . " where " . $queryConditions. "" ;

                    $rqAdresse = "SELECT tel1.id FROM debt_force_seg.dt_adresse tel1 WHERE (tel1.id_debiteur_id) IN (
                        SELECT debi.id FROM debt_force_seg.dt_Debiteur debi WHERE debi.id IN (
                        SELECT (t1.id_debiteur_id)
                        FROM debt_force_seg.dt_Type_Debiteur t1
                        WHERE t1.id_creance_id IN (".$rqCreance."))
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
                            SELECT (t1.id_debiteur_id)
                            FROM debt_force_seg.dt_Type_Debiteur t1
                            WHERE t1.id_creance_id IN (".$rqCreance.")
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
                        $groupe = $segementationRepo->getCritereSegmentation($id);
                        $queryConditions = " ";
                        $requetOutput = $this->getRequeteCreance($id , $groupe , $queryEntities,$queryConditions,$param);
                        $queryConditions = $requetOutput["queryConditions"];
                        $queryEntities = $requetOutput["queryEntities"];
                        $param = $requetOutput["param"];
                        $queryEntities = strtolower($queryEntities);
                        $rqCreance = "SELECT DISTINCT c.id  FROM  ". $queryEntities . " where " . $queryConditions. "" ;
                        $rqDeb = "SELECT debi.id FROM debt_force_seg.dt_debiteur debi WHERE debi.id IN (
                            SELECT (t1.id_debiteur_id)
                            FROM debt_force_seg.dt_Type_Debiteur t1
                            WHERE t1.id_creance_id IN (".$rqCreance.")
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
            }
        // } catch (\Exception $e) {
        //     $respObjects["err"] = $e->getMessage();
        // }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/updateDataSegmentation', methods: ['POST'])]
    public function updateDataSegmentation(Request $request,segementationRepo $segementationRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        //$segment = $segementationRepo->getListeSgementByStatus(1);
        $sql="CALL CopyTableStructuresForTables()";
        $stmt = $this->conn->prepare($sql)->executeQuery();

        $segment = $segementationRepo->getListeSegmentation();
        // try {
            for ($s=0; $s < count($segment) ; $s++) {
                $entities = json_decode($segment[$s]['entities']);
                if(in_array('creance',$entities))
                {
                    $queryEntities = "debt_force_seg.dt_debiteur deb,debt_force_seg.dt_creance c";
                    $queryConditions = " ";
                    $param = array();
                    $id = $segment[$s]["id"];
                    $groupe = $segementationRepo->getCritereSegmentation($id);
                    $queryConditions = " ";
                    $requetOutput = $this->getRequeteCreance($id , $groupe , $queryEntities,$queryConditions,$param);
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
                        $resultCreance = $stmt->fetchAll();dump($resultCreance);
                        if(count($resultCreance) >= 1)
                        {
                            $sql="DELETE FROM `debt_force_seg`.`seg_creance` WHERE id_seg = ".$id."";
                            $stmt = $this->conn->prepare($sql)->executeQuery();

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
                    $groupe = $segementationRepo->getCritereSegmentation($id);
                    $queryConditions = " ";
                    $requetOutput = $this->getRequeteCreance($id , $groupe , $queryEntities,$queryConditions,$param);
                    $queryConditions = $requetOutput["queryConditions"];
                    $queryEntities = $requetOutput["queryEntities"];
                    $param = $requetOutput["param"];
                    $queryEntities = strtolower($queryEntities);

                    $sql="DELETE FROM `debt_force_seg`.`seg_dossier` WHERE id_seg = ".$id."";
                        $stmt = $this->conn->prepare($sql)->executeQuery();

                   
                    $rqCreance = "SELECT DISTINCT c.id  FROM  ". $queryEntities . " where " . $queryConditions. "" ;

                    $rqDossier = "SELECT DISTINCT doss.id FROM debt_force_seg.dt_Dossier doss WHERE doss.id IN (
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
                    $groupe = $segementationRepo->getCritereSegmentation($id);
                    $queryConditions = " ";
                    $requetOutput = $this->getRequeteCreance($id , $groupe , $queryEntities,$queryConditions,$param);
                    $queryConditions = $requetOutput["queryConditions"];
                    $queryEntities = $requetOutput["queryEntities"];
                    $param = $requetOutput["param"];
                    $queryEntities = strtolower($queryEntities);

                    $rqCreance = "SELECT DISTINCT c.id  FROM  ". $queryEntities . " where " . $queryConditions. "" ;

                    $rqTelephone = "SELECT tel1.id FROM debt_force_seg.dt_Telephone tel1 WHERE (tel1.id_debiteur_id) IN (
                        SELECT debi.id FROM debt_force_seg.dt_Debiteur debi WHERE debi.id IN (
                        SELECT (t1.id_debiteur_id)
                        FROM debt_force_seg.dt_type_debiteur t1
                        WHERE t1.id_creance_id IN (".$rqCreance."))
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
                        $sql="DELETE FROM `debt_force_seg`.`seg_telephone` WHERE id_seg = ".$id."";
                        $stmt = $this->conn->prepare($sql)->executeQuery();

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
                    $groupe = $segementationRepo->getCritereSegmentation($id);
                    $queryConditions = " ";
                    $requetOutput = $this->getRequeteCreance($id , $groupe , $queryEntities,$queryConditions,$param);
                    $queryConditions = $requetOutput["queryConditions"];
                    $queryEntities = $requetOutput["queryEntities"];
                    $param = $requetOutput["param"];
                    $queryEntities = strtolower($queryEntities);

                    $rqCreance = "SELECT DISTINCT c.id  FROM  ". $queryEntities . " where " . $queryConditions. "" ;

                    $rqAdresse = "SELECT tel1.id FROM debt_force_seg.dt_adresse tel1 WHERE (tel1.id_debiteur_id) IN (
                        SELECT debi.id FROM debt_force_seg.dt_Debiteur debi WHERE debi.id IN (
                        SELECT (t1.id_debiteur_id)
                        FROM debt_force_seg.dt_Type_Debiteur t1
                        WHERE t1.id_creance_id IN (".$rqCreance."))
                    )";
                    $stmt = $this->conn->prepare($rqAdresse);
                    foreach ($param as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                    $stmt = $stmt->executeQuery();
                    $resultAdresse = $stmt->fetchAll();

                    if(count($resultAdresse) >= 1)
                    {
                        $sql="DELETE FROM `debt_force_seg`.`seg_adresse` WHERE id_seg = ".$id."";
                        $stmt = $this->conn->prepare($sql)->executeQuery();

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
                            SELECT (t1.id_debiteur_id)
                            FROM debt_force_seg.dt_Type_Debiteur t1
                            WHERE t1.id_creance_id IN (".$rqCreance.")
                        )";
                       
                        $stmt = $this->conn->prepare($rqDeb);
                        foreach ($param as $key => $value) {
                            $stmt->bindValue($key, $value);
                        }
                        $stmt = $stmt->executeQuery();
                        $resultDebi = $stmt->fetchAll();
   
                        if(count($resultDebi) >= 1)
                        {
                            $sql="DELETE FROM `debt_force_seg`.`seg_debiteur` WHERE id_seg = ".$id."";
                            $stmt = $this->conn->prepare($sql)->executeQuery();

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
                        $groupe = $segementationRepo->getCritereSegmentation($id);
                        $queryConditions = " ";
                        $requetOutput = $this->getRequeteCreance($id , $groupe , $queryEntities,$queryConditions,$param);
                        $queryConditions = $requetOutput["queryConditions"];
                        $queryEntities = $requetOutput["queryEntities"];
                        $param = $requetOutput["param"];
                        $queryEntities = strtolower($queryEntities);
                        $rqCreance = "SELECT DISTINCT c.id  FROM  ". $queryEntities . " where " . $queryConditions. "" ;
                        $rqDeb = "SELECT debi.id FROM debt_force_seg.dt_debiteur debi WHERE debi.id IN (
                            SELECT (t1.id_debiteur_id)
                            FROM debt_force_seg.dt_Type_Debiteur t1
                            WHERE t1.id_creance_id IN (".$rqCreance.")
                        )";
                        $stmt = $this->conn->prepare($rqDeb);
                        foreach ($param as $key => $value) {
                            $stmt->bindValue($key, $value);
                        }
                        $stmt = $stmt->executeQuery();
                        $resultDebi = $stmt->fetchAll();
                        if(count($resultDebi) >= 1)
                        {
                            $sql="DELETE FROM `debt_force_seg`.`seg_debiteur` WHERE id_seg = ".$id."";
                            $stmt = $this->conn->prepare($sql)->executeQuery();

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
        // } catch (\Exception $e) {
        //     $respObjects["err"] = $e->getMessage();
        // }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    

    public function getRequeteCreance($id , $groupe,$queryEntities,$queryConditions,$param){
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
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( (c.id_type_creance_id) LIKE :type_creance".$k."_".$i.") ";
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
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.total_restant " . ($details[$i]["action"] === "2" ? ">" : "<") . " :total_restant" . $k . "_" . $i . ")";
                                $param['total_restant' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.total_restant BETWEEN :total_restant1" . $k . "_" . $i . " AND :total_restant2" . $k . "_" . $i . ")";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_garantie_creance gc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_garantie_creance gc";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_garantie g") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_garantie g";
                            } 

                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = (gc.id_creance_id) and (gc.id_garantie_id) = g.id and  g.type_garantie LIKE :type_garantie".$k."_".$i.") ";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_garantie_creance gc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_garantie_creance gc";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_garantie g") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_garantie g";
                            }

                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ") ." " . $operateur[$k] . " " . $operateur1[$i] . " ( c.id = (gc.id_creance_id) and (gc.id_garantie_id) = g.id and  g.taux " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (gc.id_creance_id) and (gc.id_garantie_id) = g.id and  g.taux BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_portefeuille p") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_portefeuille p";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_ponneur_Ordre dn") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_donneur_Ordre dn";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." ".$operateur[$k]." ".$operateur1[$i]." (  p.id = (c.id_ptf_id) and (p.id_donneur_ordre_id) = dn.id and  (dn.id_type_id) = :type_donneur".$k."_".$i.") ";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_portefeuille p") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_portefeuille p";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_donneur_Ordre dn") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_donneur_Ordre dn";
                            }
                            
                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                            
                                // Check if it's "supérieur" or "inférieur" and assign the appropriate operator
                                $operator = $details[$i]["action"] === "2" ? ">" : "<";
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (   p.id = (c.id_ptf_id) and p.date_debut_gestion $operator :date_debut_gestion" . $k . "_" . $i . ")";
                                $param['date_debut_gestion' . $k . '_' . $i] = $start;

                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (    p.id = (c.id_ptf_id) and p.date_debut_gestion BETWEEN :date_debut_gestion1" . $k . "_" . $i . " AND :date_debut_gestion2" . $k . "_" . $i . ")";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_portefeuille p") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_portefeuille p";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_donneur_Ordre dn") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_donneur_Ordre dn";
                            }

                            // $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                            // $end = $this->GeneralService->dateEnd($details[$i]["value2"]);

                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                            
                                // Check if it's "supérieur" or "inférieur" and assign the appropriate operator
                                $operator = $details[$i]["action"] === "2" ? ">" : "<";
                                
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( p.id = (d.id_ptf_id) and p.date_fin_gestion $operator :date_fin_gestion" . $k . "_" . $i . ")";
                                $param['date_fin_gestion' . $k . '_' . $i] = $start;

                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                            
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (  p.id = (d.id_ptf_id) and p.date_fin_gestion BETWEEN :date_fin_gestion1" . $k . "_" . $i . " AND :date_fin_gestion2" . $k . "_" . $i . ")";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_detail_Creance dc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_detail_Creance dc";
                            }
                            /*$queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(dc.id_creance) and   dc.principale between :VALUE1".$k."_".$i." and :VALUE2".$k."_".$i.") ";
                            $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                            $param['VALUE2'.$k.'_'.$i] = $details[$i]["value2"];*/
                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (dc.id_creance_id) AND dc.principale " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (dc.id_creance_id) AND dc.principale BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_detail_Creance dc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_detail_Creance dc";
                            }
                            // $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(dc.id_creance) and   dc.frais between :VALUE1".$k."_".$i." and :VALUE2".$k."_".$i.") ";
                            // $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                            // $param['VALUE2'.$k.'_'.$i] = $details[$i]["value2"];

                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (dc.id_creance_id) AND dc.frais " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (dc.id_creance_id) AND dc.frais BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_detail_creance dc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_detail_creance dc";
                            }
                            // $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = identity(dc.id_creance) and   dc.interet between :VALUE1".$k."_".$i." and :VALUE2".$k."_".$i.") ";
                            // $param['VALUE1'.$k.'_'.$i] = $details[$i]["value1"];
                            // $param['VALUE2'.$k.'_'.$i] = $details[$i]["value2"];
                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (dc.id_creance_id) AND dc.interet " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (dc.id_creance_id) AND dc.interet BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_type_debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_type_debiteur t";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Telephone tel") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Telephone tel";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (t.id_creance_id)  and (tel.id_debiteur_id)=deb.id  and  (tel.id_type_tel_id) like :typeTel".$k."_".$i." ) ";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Type_Debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Type_Debiteur t";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Telephone tel") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Telephone tel";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (t.id_creance_id)  and (tel.id_debiteur)=deb.id  and  (tel.id_status_id) like :statusTel".$k."_".$i." ) ";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Type_Debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Type_Debiteur t";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Adresse ad") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Adresse ad";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (t.id_creance_id)  and (ad.id_debiteur)=deb.id  and  (ad.id_type_adresse_id) like :typeAdresse".$k."_".$i." ) ";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Type_Debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Type_Debiteur t";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Adresse ad") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Adresse ad";
                            }
                            $queryConditions .=  (0 == $k ? $operateur0[$j] : " ")." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (t.id_creance_id)  and (ad.id_debiteur)=deb.id  and  (ad.id_status_id) like :statusAdr".$k."_".$i." ) ";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Type_Debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Type_Debiteur t";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (t.id_creance_id)  and  deb.type_personne like :VALUE1".$k."_".$i." ) ";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Type_Debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Type_Debiteur t";
                            }

                            $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                            $end = $this->GeneralService->dateEnd($details[$i]["value2"]);

                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (t.id_creance_id)   and  (t.id_type_id) like :VALUE1".$k."_".$i." ) ";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Proc_Creance pc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Proc_Creance pc";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Proc_Judicaire pj") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Proc_Judicaire pj";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (pc.id_creance_id) and (pc.id_proc_id) = pj.id and  pj.type_proc_judicaire LIKE :type_proc_judicaire".$k."_".$i.") ";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Type_Debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Type_Debiteur t";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Emploi em") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Emploi em";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (t.id_creance_id) and t.id_debiteur = (em.id_debiteur_id) and (em.id_status_id) like :status_emploi".$k."_".$i." ) ";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Type_Debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Type_Debiteur t";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Emploi em") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Emploi em";
                            }

                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (t.id_creance_id) AND t.id_debiteur = (em.id_debiteur_id) AND (em.id_status_id) AND em.dateDebut " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (t.id_creance_id) AND t.id_debiteur = (em.id_debiteur_id) AND (em.id_status_id) AND em.dateDebut BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Type_Debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Type_Debiteur t";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Emploi em") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Emploi em";
                            }

                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (t.id_creance_id) AND t.id_debiteur = (em.id_debiteur_id) AND (em.id_status_id) AND em.dateFin " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                $param['value1_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (t.id_creance_id) AND t.id_debiteur = (em.id_debiteur_id) AND (em.id_status_id) AND em.dateFin BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Type_Debiteur t") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Type_Debiteur t";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Employeur emp") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Employeur emp";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( c.id = (t.id_creance_id) and t.id_debiteur = (emp.id_debiteur_id) and (emp.id_status_id) like :status_employeur".$k."_".$i." ) ";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Accord ac") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Accord ac";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Creance_Accord ca") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Creance_Accord ca";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = (ca.id_creance_id) and (ac.id_status_id) like :status_accord".$k."_".$i." ) ";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Accord ac") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Accord ac";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Creance_Accord ca") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Creance_Accord ca";
                            }

                            $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (ca.id_creance_id) AND ac.dateCreation " . ($details[$i]["action"] === "2" ? ">" : "<") . " :dateStart_" . $k . "_" . $i . ")";
                                $param['dateStart_' . $k . '_' . $i] = $start;
                            } elseif ($details[$i]["action"] === "1") {
                                $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (ca.id_creance_id) AND ac.dateCreation BETWEEN :dateStart_" . $k . "_" . $i . " AND :dateFin_" . $k . "_" . $i . ")";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Accord ac") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Accord ac";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Creance_Accord ca") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Creance_Accord ca";
                            }

                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (ca.id_creance_id) AND ac.montant " . ($details[$i]["action"] === "2" ? ">" : "<") . " :valueMontant1_" . $k . "_" . $i . ")";
                                $param['valueMontant1_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (ca.id_creance_id) AND ac.montant BETWEEN :valueMontant1_" . $k . "_" . $i . " AND :valueMontant2_" . $k . "_" . $i . ")";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Accord ac") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Accord ac";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Creance_Accord ca") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Creance_Accord ca";
                            }

                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (ca.id_creance_id) AND ac.montant_a_payer " . ($details[$i]["action"] === "2" ? ">" : "<") . " :valueMontant1_" . $k . "_" . $i . ")";
                                $param['valueMontant1_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (ca.id_creance_id) AND ac.montant_a_payer BETWEEN :valueMontant1_" . $k . "_" . $i . " AND :valueMontant2_" . $k . "_" . $i . ")";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Accord ac") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Accord ac";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Paiement pm") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Paiement pm";
                            }
                            
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = (pm.id_creance_id) and (pm.id_type_paiement_id) like :typeP".$k."_".$i." ) ";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Accord ac") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Accord ac";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Paiement pm") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Paiement pm";
                            }
                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (pm.id_creance_id) AND pm.date_paiement " . ($details[$i]["action"] === "2" ? ">" : "<") . " :dateStart_" . $k . "_" . $i . ")";
                                $param['dateStart_' . $k . '_' . $i] = $start;
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (pm.id_creance_id) AND pm.date_paiement BETWEEN :dateStart_" . $k . "_" . $i . " AND :dateFin_" . $k . "_" . $i . ")";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Accord ac") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Accord ac";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Paiement pm") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Paiement pm";
                            }
                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (pm.id_creance_id) AND pm.montant " . ($details[$i]["action"] === "2" ? ">" : "<") . " :value1_" . $k . "_" . $i . ")";
                                $param['dateStart_' . $k . '_' . $i] = $details[$i]["value1"];
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (pm.id_creance_id) AND pm.montant BETWEEN :value1_" . $k . "_" . $i . " AND :value2_" . $k . "_" . $i . ")";
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Dossier dss") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Dossier dss";
                            }
                            
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  dss.id = (c.id_dossier_id)  and  (dss.id_qualification_id) = :qualification".$k."_".$i.") ";
                            $param['qualification'.$k.'_'.$i] = $details[$i]["value1"]; 
                        }
                    }
                }
            }
            if($groupe[$j]['groupe'] == "Activités"){
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
                    if($criteres[$k]["critere"] == "Familles d'activités"){
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Param_Critere pc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Param_Critere pc";
                            }
                            
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." ( (c.id_activite_id) = :activite".$k."_".$i.") ";
                            $param['activite'.$k.'_'.$i] = $details[$i]["value1"]; 
                        }
                    }
                }
            }
            if($groupe[$j]['groupe'] == "Facture"){
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
                    if($criteres[$k]["critere"] == "Année"){
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Facture f") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Facture f";
                            }
                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $start = $this->GeneralService->yearStart($details[$i]["value1"]);
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (f.id_creance_id) AND f.date_creation " . ($details[$i]["action"] === "2" ? ">" : "<") . " :dateStart_" . $k . "_" . $i . ")";
                                $param['dateStart_' . $k . '_' . $i] = $start;
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $start = $this->GeneralService->yearStart($details[$i]["value1"]);
                                $end = $this->GeneralService->yearEnd($details[$i]["value2"]);
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (f.id_creance_id) AND f.date_creation BETWEEN :dateStart_" . $k . "_" . $i . " AND :dateFin_" . $k . "_" . $i . ")";
                                $param['dateStart_' . $k . '_' . $i] = $start;
                                $param['dateFin_' . $k . '_' . $i] = $end;
                            }
                        }
                    }
                    if($criteres[$k]["critere"] == "Total TTC"){
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Facture f") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Facture f";
                            }
                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $montant1 = $details[$i]["value1"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (f.id_creance_id) AND f.total_ttc " . ($details[$i]["action"] === "2" ? ">" : "<") . " :totalTtc_" . $k . "_" . $i . ")";
                                $param['totalTtc_' . $k . '_' . $i] = $montant1;
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $montant1 =  $details[$i]["value1"];
                                $montant2 =  $details[$i]["value2"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (f.id_creance_id) AND f.total_ttc BETWEEN :totalTtc_1" . $k . "_" . $i . " AND :totalTtc_2" . $k . "_" . $i . ")";
                                $param['totalTtc_1' . $k . '_' . $i] = $montant1;
                                $param['totalTtc_2' . $k . '_' . $i] = $montant2;
                            }
                        }
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Facture f") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Facture f";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = (f.id_creance_id) AND (f.id_type_paiement_id) like :typeP".$k."_".$i." ) ";
                            $param['typeP'.$k.'_'.$i] = $details[$i]["value1"];  
                        }
                    }
                    if($criteres[$k]["critere"] == "Status"){
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Facture f") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Facture f";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = (f.id_creance_id) AND (f.id_status_id) like :statusP".$k."_".$i." ) ";
                            $param['statusP'.$k.'_'.$i] = $details[$i]["value1"];  
                        }
                    }
                    if($criteres[$k]["critere"] == "Taux_honoraire"){
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Facture f") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Facture f";
                            }
                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $montant1 = $details[$i]["value1"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( c.taux_honoraire " . ($details[$i]["action"] === "2" ? ">" : "<") . " :taux_honoraire_" . $k . "_" . $i . ")";
                                $param['taux_honoraire_' . $k . '_' . $i] = $montant1;
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $montant1 =  $details[$i]["value1"];
                                $montant2 =  $details[$i]["value2"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( c.taux_honoraire BETWEEN :totalTtc_1" . $k . "_" . $i . " AND :totalTtc_2" . $k . "_" . $i . ")";
                                $param['totalTtc_1' . $k . '_' . $i] = $montant1;
                                $param['totalTtc_2' . $k . '_' . $i] = $montant2;
                            }
                        }
                    }

                    if($criteres[$k]["critere"] == "Honoraire_petentiel"){
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Facture f") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Facture f";
                            }
                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $montant1 = $details[$i]["value1"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (  c.honoraire_petentiel " . ($details[$i]["action"] === "2" ? ">" : "<") . " :honoraire_" . $k . "_" . $i . ")";
                                $param['honoraire_' . $k . '_' . $i] = $montant1;
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $montant1 =  $details[$i]["value1"];
                                $montant2 =  $details[$i]["value2"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (  c.honoraire_petentiel BETWEEN :honoraire__1" . $k . "_" . $i . " AND :honoraire__2" . $k . "_" . $i . ")";
                                $param['honoraire__1' . $k . '_' . $i] = $montant1;
                                $param['honoraire__2' . $k . '_' . $i] = $montant2;
                            }
                        }
                    }
                    if($criteres[$k]["critere"] == "Honoraire_facturé"){
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Facture f") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Facture f";
                            }
                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $montant1 = $details[$i]["value1"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( c.id = (f.id_creance_id) AND c.honoraire_facture " . ($details[$i]["action"] === "2" ? ">" : "<") . " :honoraire_" . $k . "_" . $i . ")";
                                $param['honoraire_' . $k . '_' . $i] = $montant1;
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $montant1 =  $details[$i]["value1"];
                                $montant2 =  $details[$i]["value2"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " ( c.id = (f.id_creance_id) AND c.honoraire_facture BETWEEN :honoraire__1" . $k . "_" . $i . " AND :honoraire__2" . $k . "_" . $i . ")";
                                $param['honoraire__1' . $k . '_' . $i] = $montant1;
                                $param['honoraire__2' . $k . '_' . $i] = $montant2;
                            }
                        }
                    }
                    if($criteres[$k]["critere"] == "Honoraire_petentiel_restant"){
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Facture f") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Facture f";
                            }
                            if ($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $montant1 = $details[$i]["value1"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (  c.honoraire_restant " . ($details[$i]["action"] === "2" ? ">" : "<") . " :honoraire_" . $k . "_" . $i . ")";
                                $param['honoraire_' . $k . '_' . $i] = $montant1;
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $montant1 =  $details[$i]["value1"];
                                $montant2 =  $details[$i]["value2"];
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (  c.honoraire_restant BETWEEN :honoraire__1" . $k . "_" . $i . " AND :honoraire__2" . $k . "_" . $i . ")";
                                $param['honoraire__1' . $k . '_' . $i] = $montant1;
                                $param['honoraire__2' . $k . '_' . $i] = $montant2;
                            }
                        }
                    }

                }
            }
            if($groupe[$j]['groupe'] == "Cadrages"){
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
                    if($criteres[$k]["critere"] == "Type de cadrages"){
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Cadrages cd") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Cadrages cd";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Cadrages_Creance cc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Cadrages_Creance cc";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = (cc.id_creance_id) AND  cd.id = (cc.id_cadrage_id) AND cd.type like :typeCad".$k."_".$i." ) ";
                            $param['typeCad'.$k.'_'.$i] = $details[$i]["value1"];  

                        }
                    }
                    if($criteres[$k]["critere"] == "Date de retour"){
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Cadrages cd") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Cadrages cd";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Cadrages_Creance cc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Cadrages_Creance cc";
                            }
                            if($details[$i]["action"] === "2" || $details[$i]["action"] === "3") {
                                // If "supérieur" or "inférieur"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (cc.id_creance_id) AND  cd.id = (cc.id_cadrage_id) AND cd.date_retour " . ($details[$i]["action"] === "2" ? ">" : "<") . " :dateRStart_" . $k . "_" . $i . ")";
                                $param['dateRStart_' . $k . '_' . $i] = $start;
                            } elseif ($details[$i]["action"] === "1") {
                                // If "between"
                                $start = $this->GeneralService->dateStart($details[$i]["value1"]);
                                $end = $this->GeneralService->dateEnd($details[$i]["value2"]);
                                $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." " . $operateur[$k] . " " . $operateur1[$i] . " (c.id = (cc.id_creance_id) AND  cd.id = (cc.id_cadrage_id) AND cd.date_retour BETWEEN :dateRStart_" . $k . "_" . $i . " AND :dateRFin_" . $k . "_" . $i . ")";
                                $param['dateRStart_' . $k . '_' . $i] = $start;
                                $param['dateRFin_' . $k . '_' . $i] = $end;
                            }
                        }
                    }
                    if($criteres[$k]["critere"] == "Status de cadrages"){
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
                            if(strpos($queryEntities,",debt_force_seg.dt_Cadrages cd") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Cadrages cd";
                            }
                            if(strpos($queryEntities,",debt_force_seg.dt_Cadrages_Creance cc") == false)
                            {
                                $queryEntities .= ",debt_force_seg.dt_Cadrages_Creance cc";
                            }
                            $queryConditions .= (0 == $k ? $operateur0[$j] : " ")." "." ".$operateur[$k]." ".$operateur1[$i]." (  c.id = (cc.id_creance_id) AND  cd.id = (cc.id_cadrage_id) AND cd.etat like :etatCad".$k."_".$i." ) ";
                            $param['etatCad'.$k.'_'.$i] = $details[$i]["value1"];  
                        }
                    }
                    
                   
                }
            }
        }
        return ["queryConditions"=>$queryConditions ,"queryEntities"=>$queryEntities , 'param'=>$param ];
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
    #[Route('/getParamActivites')]
    public function getTypeFamilles(Request $request,segementationRepo $segementationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $respObjects["data"]  =$segementationRepo->getTypeFamilles($id);
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getTypeFamillesQualification')]
    public function getTypeFamillesQualification(Request $request,segementationRepo $segementationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $respObjects["data"]  =$segementationRepo->getQualification($id);
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
                $entities = json_decode($data["segmentation"]['entities']);
                $entitiesValue = [];

                for ($i=0; $i < count($entities); $i++) {  
                    if('creance' == $entities[$i]){
                        $entitiesValue[$i]['entities'] = $entities[$i];
                        $entitiesValue[$i]['value'] = $segementationRepo->getValueSegment($id, $entities[$i]);
                    }else if('dossier' == $entities[$i]) {
                        $entitiesValue[$i]['entities'] = $entities[$i];
                        $entitiesValue[$i]['value'] = $segementationRepo->getValueSegment($id, $entities[$i]);;
                    }else if('debiteur' == $entities[$i]) {
                        $entitiesValue[$i]['entities'] = $entities[$i];
                        $entitiesValue[$i]['value'] = $segementationRepo->getValueSegment($id, $entities[$i]);;
                    }else if('telephone' == $entities[$i]) {
                        $entitiesValue[$i]['entities'] = $entities[$i];
                        $entitiesValue[$i]['value'] = $segementationRepo->getValueSegment($id, $entities[$i]);;
                    }else if('adresse' == $entities[$i]) {
                        $entitiesValue[$i]['entities'] = $entities[$i];
                        $entitiesValue[$i]['value'] = $segementationRepo->getValueSegment($id, $entities[$i]);;
                    }
                }
                
                $respObjects["entities"] = $entitiesValue;
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

    #[Route('/deleteSegmentation', methods: ['POST'])]
    public function deleteSegmentation(Request $request,segementationRepo $segementationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");

            $segementationRepo->deleteSegmentation($id);
            $codeStatut="OK";
           
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
}