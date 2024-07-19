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
        // try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $creance = $creancesRepo->getOneCreance($id);
            if($creance){
                $data = json_decode($request->getContent(), true);
                $dataPaiement = $data["data"];
                $montant = $data["montant"];
                $type_paiement = $data["type_paiment"];
                $montant_a_Payer = $data["montant_a_Payer"];
                $echeanciers = $data["echeanciers"];
                $date_debut = $data["date_debut"];
                $date_day = date("Y-m-d"); // Current date

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
                                        "id_status_id"=>1,
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
                                            "id_status_id"=>1,
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
            }else{
                $codeStatut = "NOT_EXIST_ELEMENT";
            }
        // }catch(\Exception $e){
        //     $codeStatut="ERROR";
        //     $respObjects["err"] = $e->getMessage();
        // }
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

    

}
