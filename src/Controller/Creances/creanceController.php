<?php

namespace App\Controller\Creances;

use App\Entity\BackgroundCourrier;
use App\Entity\Creance;
use App\Repository\DonneurOrdreAndPTF\donneurRepo;
use App\Repository\Creances\creancesRepo;
use App\Repository\Encaissement\paiementRepo;
use App\Repository\Parametrages\Activities\activityRepo;
use App\Repository\Users\userRepo;
use Spipu\Html2Pdf\Html2Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\AuthService;
use App\Service\MessageService;
use App\Service\typeService;

#[Route('/API/creances')]


class creanceController extends AbstractController
{
    private  $integrationRepo;
    private  $activityRepo;
    private  $donneurRepo;
    private  $affichageRepo;
    private  $serializer;
    public $em;
    private $conn;
    private $AuthService;
    private $TypeService;



    public function __construct(
        creancesRepo $creancesRepo,
        userRepo $userRepo,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        MessageService $MessageService,
        Connection $conn,
        AuthService $AuthService,
        TypeService $TypeService,
        activityRepo $activityRepo
        )
    {
        $this->conn = $conn;
        $this->creancesRepo = $creancesRepo;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->MessageService = $MessageService;
        $this->AuthService = $AuthService;
        $this->userRepo = $userRepo;
        $this->TypeService = $TypeService;
        $this->activityRepo= $activityRepo;
    }
    #[Route('/liste_creances_by_filtrage',methods:"POST")]
    public function liste_dossiers_by_filtrage(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            //Vérifier date 
            $data = $creancesRepo->getListesCreancesByFiltrages($data_list);
            $array = [];
            for ($i=0; $i < count($data); $i++) { 
                $array[$i] = $data[$i];
                $array[$i]["dn"] = $creancesRepo->getDonneurByPtf($data[$i]["id_ptf_id"]);
                $array[$i]["debiteur"] = $creancesRepo->getDebiteurByCreance($data[$i]["id"]);
                $array[$i]["type_debiteur"] = $creancesRepo->getTypeDebiteur($data[$i]["id"] , $array[$i]["debiteur"]["id"]);
            }
            $respObjects["data"] =$array;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/checkIfCreanceExist',methods:"POST")]
    public function checkCreanceExist(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $creance = $creancesRepo->getOneCreance($id);
            if($creance){
                $codeStatut="OK";
            }else{
                $codeStatut = "NOT_EXIST_ELEMENT";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getCreance')]
    public function getCreance(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $creance = $creancesRepo->getOneCreance($id);
            if($creance){
                $respObjects["data"] = $creance;
                $respObjects["ptf"] = $creancesRepo->getPtf($creance['id_ptf_id']);
                $respObjects["donneur"] = $creancesRepo->getDonneurByPtf($creance['id_ptf_id']);
                $typePaiement = $this->TypeService->getListeType("paiement");
                $respObjects["type_paiemennt"] = $typePaiement;
                $respObjects["status_paiement"] =$creancesRepo->getStatusPaiement();
                $respObjects["nbr_deb"] =$creancesRepo->getNbrDeb($id);
                $respObjects["queue"] =$creancesRepo->getQueueCreance($id);
                $respObjects["status_accord"] =$creancesRepo->getStatusAccord();
                $respObjects["accord"] =$creancesRepo->getAccords($id);
                $respObjects["activite_creance"] =$creancesRepo->getActiviteCreance($id);
                $respObjects["paiement"] =$creancesRepo->getPaiement($id);
                $respObjects["list_creance"] =$creancesRepo->getListeOtherCreance($creance['id_dossier_id']);
                $respObjects["allDebiteur"] =$creancesRepo->getListesDebiteurByDossier($id);

                
                $codeStatut="OK";
            }else{
                $codeStatut = "NOT_EXIST_ELEMENT";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getTypeDonneur')]
    public function getTypeDonneur(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $respObjects["data"]  =$creancesRepo->getTypeDonneurOrdre();
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getTypeCreance')]
    public function getTypeCreance(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $respObjects["data"]  =$creancesRepo->getTypeCreance();
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getTypeCreanceByTypeDn')]
    public function getTypeCreanceByTypeDn(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $respObjects["data"]  =$creancesRepo->getTypeCreanceByTypeDn($id);
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getTypeDetailsCreance')]
    public function getTypeDetailsCreance(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $respObjects["data"]  =$creancesRepo->getTypeDetailsCreance($id);
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getTypeDetailsCreanceMultiple')]
    public function getTypeDetailsCreanceMultiple(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data =explode(',',$request->get("liste"));
            $respObjects["data"]  =$creancesRepo->getTypeDetailsCreanceMultiple($data);
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    
    #[Route('/createAccord',methods:"POST")]
    public function createAccord(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $creance = null;
            if($id != "undefined"){
                $creance = $creancesRepo->getOneCreance($id);
            }
            $data = json_decode($request->getContent(), true);
            $dataPaiement = $data["data"];
            $montant = $data["montant"];
            $type_paiement = $data["type_paiment"];
            $montant_a_Payer = $data["montant_a_Payer"];
            $echeanciers = $data["echeanciers"];
            $date_debut = $data["date_debut"];
            $date_day = date("Y-m-d"); // Current date
            if($creance){
                $type_select = $this->TypeService->checkType($type_paiement,"paiement");
                if(strtotime($date_debut) >= strtotime($date_day)) {
                    if(count($dataPaiement) && $montant > 0 && $type_select){
                        if($echeanciers == count($dataPaiement)){
                            if($creance["total_restant"] > 0 && ($creance["total_restant"] >= $montant_a_Payer)){
                                $totalP = 0;
                                for ($i=0; $i < count($dataPaiement); $i++) { 
                                    $totalP = $totalP + $dataPaiement[$i]["montant"];
                                }
                                if($totalP == $montant_a_Payer)
                                {
                                    $lastElementPaiment = end($dataPaiement);

                                    $dataAccord = [
                                        "id_users_id"=>$this->AuthService->returnUserId($request),
                                        "id_type_paiement_id"=>$type_paiement,
                                        "id_status_id"=>0,
                                        "date_premier_paiement"=>$date_debut,
                                        "date_fin_paiement"=>$lastElementPaiment["date"],
                                        "date_creation"=>"now()",
                                        "montant"=>$montant,
                                        "nbr_echeanciers"=>$echeanciers,
                                        "montant_a_payer"=>$montant_a_Payer,
                                    ];
                                    $createAccord = $creancesRepo->CreateAccord($dataAccord);
                                    $createAccord = $creancesRepo->createCreanceAccord(['id_accord_id'=>$createAccord , 'id_creance_id'=>$id]);

                                    for ($i=0; $i < count($dataPaiement); $i++) { 
                                        $dataDetailsAccord = [
                                            "id_accord_id"=>$createAccord,
                                            "montant"=>$dataPaiement[$i]["montant"],
                                            "id_status_id"=>0,
                                            "id_user_id"=>$this->AuthService->returnUserId($request),
                                            "date_prev_paiement"=>$dataPaiement[$i]["date"],
                                            "montant_restant"=>$dataPaiement[$i]["montant"],
                                            "montant_paiement"=>0,
                                            "id_type_paiement_id"=>$type_paiement,
                                        ];
                                        $createDt = $creancesRepo->createDetailsAccord($dataDetailsAccord);
                                    }

                                    $codeStatut="OK";

                                }
                                
                            }else{
                                $codeStatut = "ERROR_CREANCE";
                            }
                        }else{
                            $codeStatut = "ERROR_ECHEANCIERS";
                        }
                    }else{
                        $codeStatut="ERROR";
                    }
                }
                else{
                    $codeStatut="ERROR_DATE";
                }
            }
            else if(isset($data['list_creance']) && (count($data['list_creance']) > 0)){
                $list_creance = $data['list_creance'];
                $type_select = $this->TypeService->checkType($type_paiement,"paiement");

                if(strtotime($date_debut) >= strtotime($date_day)) {
                    if(count($dataPaiement) && $montant > 0 && $type_select){
                        if($echeanciers == count($dataPaiement)){
                            // if($creance["total_restant"] > 0 && ($creance["total_restant"] >= $montant_a_Payer)){
                                    $lastElementPaiment = end($dataPaiement);

                                    $dataAccord = [
                                        "id_users_id"=>$this->AuthService->returnUserId($request),
                                        "id_type_paiement_id"=>$type_paiement,
                                        "id_status_id"=>0,
                                        "date_premier_paiement"=>$date_debut,
                                        "date_fin_paiement"=>$lastElementPaiment["date"],
                                        "date_creation"=>"now()",
                                        "montant"=>$montant,
                                        "nbr_echeanciers"=>$echeanciers,
                                        "montant_a_payer"=>$montant_a_Payer,
                                    ];
                                    $createAccord = $creancesRepo->CreateAccord($dataAccord);
                                    foreach ($list_creance as $l) {
                                        $createAccord = $creancesRepo->createCreanceAccord(['id_accord_id'=>$createAccord , 'id_creance_id'=>$l["id"]]);
                                    }

                                    for ($i=0; $i < count($dataPaiement); $i++) { 
                                        $dataDetailsAccord = [
                                            "id_accord_id"=>$createAccord,
                                            "montant"=>$dataPaiement[$i]["montant"],
                                            "id_status_id"=>0,
                                            "id_user_id"=>$this->AuthService->returnUserId($request),
                                            "date_prev_paiement"=>$dataPaiement[$i]["date"],
                                            "montant_restant"=>$dataPaiement[$i]["montant"],
                                            "montant_paiement"=>0,
                                            "id_type_paiement_id"=>$type_paiement,
                                        ];
                                        $createDt = $creancesRepo->createDetailsAccord($dataDetailsAccord);
                                    }

                                    $codeStatut="OK";

                                
                            // }else{
                            //     $codeStatut = "ERROR_CREANCE";
                            // }
                        }else{
                            $codeStatut = "ERROR_ECHEANCIERS";
                        }
                    }else{
                        $codeStatut="ERROR";
                    }
                }
                else{
                    $codeStatut="ERROR_DATE";
                }
            }
            else{
                $codeStatut = "NOT_EXIST_ELEMENT";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/addActivity',methods:"POST")]
    public function addActivity(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        // $respObjects =array();
        // $codeStatut="ERROR";
        // try{
        //     $this->AuthService->checkAuth(0,$request);
        //     $data = json_decode($request->getContent(), true);
        //     $id_creance =  $data["id_creance"];
        //     $creance = $creancesRepo->getOneCreance($id_creance);
        //     if($creance){
        //         $id_param = $data["id_param"];
        //         $checkParam = $this->activityRepo->getOneParam($id_param);
        //         if($checkParam){
        //             $creancesRepo->createActivity($id_creance , $id_param , $creance['id_dossier_id']);
                    
        //             $codeStatut="OK";
        //         }else{
        //             $codeStatut = "NOT_EXIST_ELEMENT";
        //         }
        //     }else{
        //         $codeStatut = "NOT_EXIST_ELEMENT";
        //     }
        // }catch(\Exception $e){
        //     $codeStatut="ERROR";
        //     $respObjects["err"] = $e->getMessage();
        // }
        // $respObjects["codeStatut"] = $codeStatut;
        // $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        // return $this->json($respObjects);
        $respObjects = [];
        $codeStatut = "ERROR";

        try {
            $this->AuthService->checkAuth(0, $request);

            $data = json_decode($request->getContent(), true);
            $idQualification = $data['qualification'];
            $assigned = $data['assigned'];
           
            $idCreance = $data['idCreance'];
            $comment = $data['comment'];


            $qualification = $creancesRepo->getParamActivity($idQualification);
            $creance = $creancesRepo->getCreance($idCreance);

            if($idQualification){
                
                $idUser = $this->AuthService->returnUserId($request);
                $task = $creancesRepo->addActivity($creance, $qualification, $assigned , $idUser , $comment);
    
                if($assigned == 1){
                    $assignedTask = $creancesRepo->addAssignedActivity($task , $idUser);
                }else if ($assigned == 2){
                    $assignedTo = $data['assignedTo'];
                    $assignedTask = $creancesRepo->addAssignedActivity($task , $assignedTo);
                }
                $codeStatut = "OK";
                
            }else{
                $codeStatut="EMPTY-DATA";
            }

        } catch (\Exception $e) {
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);

        return new JsonResponse($respObjects);
    }

    #[Route('/addPaiement',methods:"POST")]
    public function addPaiement(Request $request,creancesRepo $creancesRepo , paiementRepo $paiementRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        // try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $id_creance =  $data["id_creance"];
            $valueDate = $data['valueDate'];
            $montant = $data['montant'];
            $idTypePaiement = $data['idTypePaiement'];
            $id_user = $this->AuthService->returnUserId($request);
            $commentaire = $data['commentaire'];

            
            $creance = $creancesRepo->addPaiement($paiementRepo,$id_creance , $valueDate,$montant,  $idTypePaiement,$id_user , $commentaire);
        // }catch(\Exception $e){
        //     $codeStatut="ERROR";
        //     $respObjects["err"] = $e->getMessage();
        // }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getAccordByDossier')]
    public function getAccord(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            
            $data = $creancesRepo->getListeAccordByDossier($id);
            $respObjects["data"] = $data;
            $codeStatut="OK";

        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getListeCreanceByDoss')]
    public function getListeCreanceByDoss(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            
            $data = $creancesRepo->getListeCreanceByDoss($id);
            $typePaiement = $this->TypeService->getListeType("paiement");
            $respObjects["type_paiemennt"] = $typePaiement;
            $respObjects["data"] = $data;
            $codeStatut="OK";

        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    #[Route('/calculateLastDate')]
    public function calculateLastDate(Request $request, creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects = array();
        $codeStatut = "ERROR";
        try {
            $this->AuthService->checkAuth(0, $request);
            
            $instalment = $request->get("instalment");
            $firstDate = $request->get("date");

            
            // Parse the first date and calculate the last date
            $firstDateTime = new \DateTime($firstDate);
            $lastDateTime = clone $firstDateTime;
            $lastDateTime->modify('+' . ($instalment - 1) . ' months');

            $respObjects["lastDate"] = $lastDateTime->format('d-m-Y');
            $codeStatut = "OK";

        } catch (\Exception $e) {
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/savePhaseListe')]
    public function savePhaseListe(Request $request, creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects = array();
        $codeStatut = "ERROR";
        try {
            $this->AuthService->checkAuth(0, $request);
            $data = json_decode($request->getContent(), true);
            
            if(isset($data['list_creance']) || isset($data['creance'])){
                $codeStatut="OK";
                for ($i=0; $i < count($data['phaseList']) ; $i++) { 
                    $d['id_type_paiement_id'] = 1;
                    $d['id_status_id'] = 1;
                    $d['date_premier_paiement'] = $data['phaseList'][$i]['firstDateInstalement'];
                    $d['date_fin_paiement'] = $data['phaseList'][$i]['lastDateInstalement'];
                    $d['montant'] = $data['phaseList'][$i]['instalmentAmount'];
                    $d['frequence'] = $data['phaseList'][$i]['instalment'];
                    $d['etat'] =0;
                    $d['montant_a_payer'] =0;
                    $d['date_creation'] ="now()";
                    $d["id_users_id"] = $this->AuthService->returnUserId($request);
                    
                    $createAccord = $creancesRepo->createAccord($d);
                    if(isset($data["list_creance"])){
                        for ($j=0; $j < count($data['list_creance']); $j++) { 
                            $creancesRepo->createCreanceAccord(['id_creance_id'=>$data["list_creance"][$j]['id'] ,'id_accord_id'=>$createAccord]);
                        }
                    }
                    

                    $firstDate = new \DateTime($data['phaseList'][$i]['firstDateInstalement']);
                    $lastDate = new \DateTime($data['phaseList'][$i]['lastDateInstalement']);
                    $intervalDays = (int)$lastDate->diff($firstDate)->days / $data['phaseList'][$i]['instalment'];

                    for ($o=0; $o < $data['phaseList'][$i]['instalment']; $o++) { 
                        $paymentDate = clone $firstDate; // Clone the start date to avoid modifying the original
                        $paymentDate->modify("+".($intervalDays * $o)." days");

                        $dataDetailsAccord = [
                            "id_accord_id"=>$createAccord,
                            "montant"=>$data['phaseList'][$i]['instalmentAmount'] / $data['phaseList'][$i]['instalment'],
                            "id_status_id"=>1,
                            "id_user_id"=>$this->AuthService->returnUserId($request),
                            "date_prev_paiement"=>$paymentDate->format('Y-m-d'),
                            "montant_restant"=>$data['phaseList'][$i]['instalmentAmount'] / $data['phaseList'][$i]['instalment'],
                            "montant_paiement"=>0,
                            "id_type_paiement_id"=>1,
                        ];
                        $createDt = $creancesRepo->createDetailsAccord($dataDetailsAccord);
                    }
                }
            }else{
                $codeStatut="EMPTY_DATA";
            }

            

        } catch (\Exception $e) {
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/addInBookmark/{id}' , methods:['POST']) ]
    public function addInBookmark($id ,Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $idBookmark = $request->get("idBookmark");
            $checkBookmark = $creancesRepo->checkBookmark($id , $this->AuthService->returnUserId($request));
            $isBookmark = false;
            if($checkBookmark){
                $creancesRepo->deleteBookMark($idBookmark); 
                $codeStatut="OK";
            }else{
                $checkBookmark = $creancesRepo->addBookmark($id , $this->AuthService->returnUserId($request));
                $codeStatut="OK";
                $isBookmark = true;
            }
            $respObjects["isBookmark"] = $isBookmark;

        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getBookmark/{id}')]
    public function getBookmark($id,Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $checkBookmark = $creancesRepo->checkBookmark($id , $this->AuthService->returnUserId($request));
            $respObjects['data'] = $checkBookmark ? $checkBookmark->getId()  : null ;
            $respObjects['isBookmark'] = $checkBookmark ? true : false;

            $codeStatut="OK";

        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getTypePromise')]
    public function getTypePromise(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get('id');

            $typePaiement = $this->TypeService->getListeType("promise");
            $creance = $creancesRepo->getOneCreance($id);
            $idPtf = $creance['id_ptf_id'];
            $getReglePtf = $creancesRepo->getReglePtf($idPtf);

            $respObjects['regle'] =$getReglePtf;
            $respObjects['data'] =$typePaiement;
            $respObjects['creance'] =$creance;

            $codeStatut="OK";

        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/createPromise' , methods:['POST'])]
    public function createPromise(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $date = $data['date'] ;
            $montant = $data['montant']; 
            $commentaire = $data['commentaire']; 
            $idCreance = $data['idCreance'] ;
            $type = $data['type'] ;

            if(is_numeric($montant) && !empty($date)){

                $creance = $creancesRepo->getOneCreance($idCreance);
                $idPtf = $creance['id_ptf_id'];
                $getReglePtf = $creancesRepo->getReglePtf($idPtf);

                $checkCondition = true;
                for ($i=0; $i < count($getReglePtf) ; $i++) { 

                    if($getReglePtf[$i]->getTypeColumn() == 'Montant' && $getReglePtf[$i]->getAction() == 'supOuEgal' && $getReglePtf[$i]->getValue1() > $montant && $getReglePtf[$i]->getType() == "Promesse" ){
                        $checkCondition = false;
                        break;
                    }

                    if($getReglePtf[$i]->getTypeColumn() == 'Montant' && $getReglePtf[$i]->getAction() == 'infOuEgal' && $getReglePtf[$i]->getValue1() < $montant && $getReglePtf[$i]->getType() == "Promesse" ){
                        $checkCondition = false;
                        break;
                    }

                    if($getReglePtf[$i]->getTypeColumn() == 'Montant' && $getReglePtf[$i]->getAction() == 'egal' && $getReglePtf[$i]->getValue1() != $montant && $getReglePtf[$i]->getType() == "Promesse" ){
                        $checkCondition = false;
                        break;
                    }
                    
                    if($getReglePtf[$i]->getTypeColumn() == 'Pourcentage' && $getReglePtf[$i]->getType() == "Promesse"){
                        $montantByTaux =  ($getReglePtf[$i]->getValue1() * $creance['total_creance'] )/100 ;
                        
                        if( $getReglePtf[$i]->getAction() == 'supOuEgal' && $montantByTaux > $montant ){
                            $checkCondition = false;
                            break;
                        }

                        if( $getReglePtf[$i]->getAction() == 'infOuEgal' && $montantByTaux < $montant ){
                            $checkCondition = false;
                            break;
                        }

                        if( $getReglePtf[$i]->getAction() == 'egal' && $montantByTaux != $montant ){
                            $checkCondition = false;
                            break;
                        }
                    }
                }

                if($checkCondition){
                    $dateInsert = [
                        'id_type_id'=> $type,
                        'id_creance_id'=> $idCreance,
                        'id_user_id'=> $this->AuthService->returnUserId($request),
                        'date'=> $date,
                        'montant'=> $montant,
                        'commentaire'=> $commentaire,
                        'date_creation'=> 'now()',
                        'id_status_id'=> 1,
                    ];
                    $creancesRepo->createPromise($dateInsert);
                    $codeStatut="OK";

                }else{
                    $codeStatut="ERROR_DATA_PROMISE";
                }

            }else{
                $codeStatut="EMPTY_DATA";
            }

        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    #[Route('/updatePromise/{id}' , methods:['POST'])]
    public function updatePromise( $id, Request $request, creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $date = $data['date'] ;
            $montant = $data['montant']; 
            $commentaire = $data['commentaire']; 
            $type = $data['type'] ;

            if(is_numeric($montant) && !empty($date)){
                $promise = $creancesRepo->getPromise($id);

                $creance = $creancesRepo->getOneCreance($promise->getId());
                $idPtf = $creance['id_ptf_id'];
                $getReglePtf = $creancesRepo->getReglePtf($idPtf);

                $checkCondition = true;
                for ($i=0; $i < count($getReglePtf) ; $i++) { 

                    if($getReglePtf[$i]->getTypeColumn() == 'Montant' && $getReglePtf[$i]->getAction() == 'supOuEgal' && $getReglePtf[$i]->getValue1() > $montant && $getReglePtf[$i]->getType() == "Promesse" ){
                        $checkCondition = false;
                        break;
                    }

                    if($getReglePtf[$i]->getTypeColumn() == 'Montant' && $getReglePtf[$i]->getAction() == 'infOuEgal' && $getReglePtf[$i]->getValue1() < $montant && $getReglePtf[$i]->getType() == "Promesse" ){
                        $checkCondition = false;
                        break;
                    }

                    if($getReglePtf[$i]->getTypeColumn() == 'Montant' && $getReglePtf[$i]->getAction() == 'egal' && $getReglePtf[$i]->getValue1() != $montant && $getReglePtf[$i]->getType() == "Promesse" ){
                        $checkCondition = false;
                        break;
                    }
                    
                    if($getReglePtf[$i]->getTypeColumn() == 'Pourcentage' && $getReglePtf[$i]->getType() == "Promesse"){
                        $montantByTaux =  ($getReglePtf[$i]->getValue1() * $creance['total_creance'] )/100 ;
                        
                        if( $getReglePtf[$i]->getAction() == 'supOuEgal' && $montantByTaux > $montant ){
                            $checkCondition = false;
                            break;
                        }

                        if( $getReglePtf[$i]->getAction() == 'infOuEgal' && $montantByTaux < $montant ){
                            $checkCondition = false;
                            break;
                        }

                        if( $getReglePtf[$i]->getAction() == 'egal' && $montantByTaux != $montant ){
                            $checkCondition = false;
                            break;
                        }
                    }
                }

                if($checkCondition){
                    $dateInsert = [
                        'id_type_id'=> $type,
                        'id_user_id'=> $this->AuthService->returnUserId($request),
                        'date'=> $date,
                        'montant'=> $montant,
                        'commentaire'=> $commentaire,
                        'date_creation'=> 'now()',
                        'id_status_id'=> 1,
                    ];
                    $creancesRepo->updatePromise($dateInsert , $id);
                    $codeStatut="OK";

                }else{
                    $codeStatut="ERROR_DATA_PROMISE";
                }
            }else{
                $codeStatut="EMPTY_DATA";
            }


        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getListePromise/{idCreance}' )]
    public function getListePromise( $idCreance, Request $request, creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            
            $data = $creancesRepo->getListPromise($idCreance);
            $arrayData = [];
            for ($i=0; $i < count($data); $i++) { 
                $arrayData[$i]['id']= $data[$i]->getId();
                $arrayData[$i]['type']= $data[$i]->getIdType();
                $arrayData[$i]['user']= $data[$i]->getIdUser();
                $arrayData[$i]['montant']= $data[$i]->getMontant();
                $arrayData[$i]['status']= $data[$i]->getIdStatus();
                $arrayData[$i]['date']= $data[$i]->getDate();
            }
            $respObjects["data"] = $arrayData;
            $codeStatut="OK";
            
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/addTask', methods: ['POST'])]
    public function addTask(Request $request, CreancesRepo $creancesRepo): JsonResponse
    {
        $respObjects = [];
        $codeStatut = "ERROR";

        try {
            $this->AuthService->checkAuth(0, $request);

            $data = json_decode($request->getContent(), true);
            $idQualification = $data['qualification'];
            $dateEcheance = new \DateTime($data['dateEcheance']);
            $assigned = $data['assigned'];
           
            $idCreance = $data['idCreance'];
            $comment = $data['comment'];

            $time = \DateTime::createFromFormat('g:i A', $data['time']);

            $qualification = $creancesRepo->getParamActivity($idQualification);
            $creance = $creancesRepo->getCreance($idCreance);

            if($dateEcheance && $idQualification){
                
                $idUser = $this->AuthService->returnUserId($request);
                $task = $creancesRepo->addTask($creance, $qualification, $dateEcheance, $time, $assigned , $idUser , $comment);
    
                if($assigned == 1){
                    $assignedTask = $creancesRepo->addAssignedTask($task , $idUser);
                }else if ($assigned == 2){
                    $assignedTo = $data['assignedTo'];
                    $assignedTask = $creancesRepo->addAssignedTask($task , $assignedTo);
                }
                $codeStatut = "OK";
                
            }else{
                $codeStatut="EMPTY-DATA";
            }

        } catch (\Exception $e) {
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);

        return new JsonResponse($respObjects);
    }

    #[Route('/getListeTask/{idCreance}' )]
    public function getListeTask( $idCreance, Request $request, creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            
            $data = $creancesRepo->getListTask($idCreance);
            $arrayData = [];
            for ($i=0; $i < count($data); $i++) { 
                $arrayData[$i]['activity']= $data[$i]->getIdActivity();
                $arrayData[$i]['date_echeance']= $data[$i]->getDateEcheance();
                $arrayData[$i]['temps']= $data[$i]->getTemps();
                $arrayData[$i]['assigned_type']= $data[$i]->getAssignedType();
                $arrayData[$i]['date_creation']= $data[$i]->getDateCreation();
                $arrayData[$i]['user']= $data[$i]->getCreatedBy();
                $arrayData[$i]['assigned_user']= $creancesRepo->getAssignedTask($data[$i]->getId());
            }
            $respObjects["data"] = $arrayData;
            $codeStatut="OK";
            
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getPromise/{id}' )]
    public function getListePromisse( $id, Request $request, creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            
            $data = $creancesRepo->getPromise($id);
            $arrayData = [];

            $arrayData['type']= $data->getIdType();
            $arrayData['user']= $data->getIdUser();
            $arrayData['montant']= $data->getMontant();
            $arrayData['status']= $data->getIdStatus();
            $arrayData['date']= $data->getDate();

            $respObjects["data"] = $arrayData;
            $codeStatut="OK";
            
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getTask/{idCreance}' )]
    public function getTask( $idCreance, Request $request, creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            
            $data = $creancesRepo->getTask($idCreance);

            $arrayData['activity']= $data->getIdActivity();
            $arrayData['date_echeance']= $data->getDateEcheance();
            $arrayData['temps']= $data->getTemps();
            $arrayData['assigned_type']= $data->getAssignedType();
            $arrayData['date_creation']= $data->getDateCreation();
            $arrayData['user']= $data->getCreatedBy();
            $arrayData['assigned_user']= $creancesRepo->getAssignedTask($data->getId());
            $respObjects["data"] = $arrayData;

            $codeStatut="OK";
            
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getTemplate/{type}' )]
    public function getTemplate( $type, Request $request, creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            
            $respObjects["data"] = $this->TypeService->getListeTemplate($type);
            $codeStatut="OK";

        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getEmails/{id}')]
    public function getEmails( $id , Request $request, creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $creance = $creancesRepo->getOneCreance($id);
            if($creance){
                $data = $creancesRepo->getEmailsByCr($id);
                
                $respObjects["data"] = $data;
                $codeStatut="OK";
            }else{
                $codeStatut = "NOT_EXIST_ELEMENT";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getAddresses/{id}')]
    public function getAddresses( $id , Request $request, creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $creance = $creancesRepo->getOneCreance($id);
            if($creance){
                $data = $creancesRepo->getAddressesCr($id);
                $respObjects["data"] = $data;
                $codeStatut="OK";
            }else{
                $codeStatut = "NOT_EXIST_ELEMENT";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    #[Route('/getTelephones/{id}')]
    public function getTelephones( $id , Request $request, creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $creance = $creancesRepo->getOneCreance($id);
            if($creance){
                $data = $creancesRepo->getTelephonesCr($id);
                $respObjects["data"] = $data;
                $codeStatut="OK";
            }else{
                $codeStatut = "NOT_EXIST_ELEMENT";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route("/previewPdf/{id}/{idAdresse}")]
    public function previewPdf(Request $request , $id , $idAdresse, creancesRepo $creancesRepo): JsonResponse
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        try {
            $modele = $this->creancesRepo->getModelCourrier($id);
            $adresse = $this->creancesRepo->getAdresse($idAdresse);
            $data = json_decode($request->getContent(), true);
            $objet = $modele->getObjet();
            $message = $modele->getMessage();
            $background = '';
            if($modele->getIdBackground()){
                $background = $modele->getIdBackground()->getId();
            }
            
            // Header
            $header = '<style>
            .background{
                        width:100px;height:100px;
                    }
            </style><div style="text-align:center"><img src="profile_img/logoCourrier/header2.png"  /></div>';
            $html = "<div style='font-family:dejavusans'>";
            $html .= $header;
            $html .= "<h1 class='title'>Object :" . htmlspecialchars($objet, ENT_QUOTES, 'UTF-8') . "</h1>";
            $html .= '<div style="text-align:right"><img src="profile_img/barcode.gif"  /></div>';
            $html .= '
                <div style="margin-top: 20px; margin-bottom: 50px;">
                    <table style="width: 100%;">
                        <tr>
                        <td style="width: 70%;">
                             <div>
                            <b>Réference : 123456789</b>
                            </div>
                        </td>
                        <td style="width: 30%;">
                            <div>
                                <b><span style="text-transform: uppercase;">'.$adresse->getIdDebiteur()->getNom().'</span> <span style="text-transform: capitalize;">'.$adresse->getIdDebiteur()->getPrenom().'</span></b><br><br>
                                <b>'.$adresse->getAdresseComplet().'</b><br><br>
                                <b>'.$adresse->getCodePostal().'</b><br><br>
                                <b>'.$adresse->getPays().'</b><br><br>
                            </div>
                        </td>
                        </tr>
                    </table>
                </div>
            ';
           
            
            if($background != ""){
                $background = $this->em->getRepository(BackgroundCourrier::class)->find($background);
                $background = $background->getUrl();
                $html .= "<div style='position:relative;width:100%;min-height:1000px'>
                    <div style='position:absolute; top:0; bottom:0; left:0; right:0; z-index:-1;'>
                    <img src='".$background."' style='width:100%; height:100%; object-fit:cover;'>
                    </div>
                    <p>" . html_entity_decode($message) . "</p>
                </div>";
                $html .="</div>";
            }else{
                $html .= "<div style='position:relative;width:100%;min-height:1000px'>
                    <p>" . html_entity_decode($message) . "</p>
                </div>";
                $html .="</div>";
            }


            $html2pdf = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', array(10, 10, 10, 10),false); 
            $html2pdf->pdf->SetFont('dejavusans', '', 12); 

            // Write HTML to PDF
            $html2pdf->writeHTML($html);
        
            // Output PDF as string
            $pdfContent = $html2pdf->output('', 'S');
        
            // Encode PDF content to base64
            $base64Content = base64_encode($pdfContent);
        
            // Prepare response data
            $file = [
                'content' => $base64Content,
                'filename' => 'previewPdf.pdf',
                'type' => 'application/pdf'
            ];
        
            // Serialize response to JSON
            $json = $this->serializer->serialize($file, 'json');
        
            // Return JsonResponse with PDF data
            return new JsonResponse($json, Response::HTTP_OK, [], true);
        
        } catch (\Exception $e) {
            // Handle exceptions
            $response = "Exception- " . $e->getMessage();
        
            // Return error response
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route("/previewSMS/{id}/{idTel}")]
    public function previewSMS(Request $request, $id, $idTel, creancesRepo $creancesRepo): Response
    {
        try {
            $modele = $creancesRepo->getModelSMS($id);
            $message = $modele->getMessage();
            $message=str_replace("<p>","",$message);
            $message=str_replace("</p>","",$message);
            $message=str_replace("&nbsp;","",$message);
            
            // No need to format the message further if it contains HTML tags
            $formattedMessage = $this->formatMessageForTxt($message);
            
            // Create a Response object for the .txt file
            $name = uniqid("sms");
            $response = new Response($formattedMessage);
            $response->headers->set('Content-Type', 'text/plain');
            $response->headers->set('Content-Disposition', 'attachment; filename="'.$name.'.txt"');
            
            return $response;
            
        } catch (\Exception $e) {
            // Handle exceptions
            $responseContent = "Exception - " . $e->getMessage();
            
            // Return error response
            return new JsonResponse($responseContent, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function formatMessageForTxt(string $message): string
    {
        // Since the message contains HTML, we don't need additional formatting
        return $message;
    }
}
