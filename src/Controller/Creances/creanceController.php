<?php

namespace App\Controller\Creances;

use App\Entity\Creance;
use App\Repository\DonneurOrdreAndPTF\donneurRepo;
use App\Repository\Creances\creancesRepo;
use App\Repository\Encaissement\paiementRepo;
use App\Repository\Parametrages\Activities\activityRepo;
use App\Repository\Users\userRepo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $id_creance =  $data["id_creance"];
            $creance = $creancesRepo->getOneCreance($id_creance);
            if($creance){
                $id_param = $data["id_param"];
                $checkParam = $this->activityRepo->getOneParam($id_param);
                if($checkParam){
                    $creancesRepo->createActivity($id_creance , $id_param , $creance['id_dossier_id']);
                    
                    $codeStatut="OK";
                }else{
                    $codeStatut = "NOT_EXIST_ELEMENT";
                }
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
    
                if($assigned == 1){dump($assigned);
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

}
