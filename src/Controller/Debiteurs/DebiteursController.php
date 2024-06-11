<?php

namespace App\Controller\Debiteurs;

use App\Repository\Debiteurs\debiteursRepo;
use App\Repository\Dossiers\dossiersRepo;
use App\Repository\Users\userRepo;
use App\Service\typeService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\AuthService;
use App\Service\MessageService;
use App\Service\GeneralService;

#[Route('/API/debiteurs')]

class DebiteursController extends AbstractController
{
    private  $integrationRepo;
    private  $donneurRepo;
    private  $debiteursRepo;
    private  $affichageRepo;
    private  $serializer;
    public $em;
    private $conn;
    private $AuthService;
    private $GeneralService;

    private $TypeService;



    public function __construct(
        dossiersRepo $dossiersRepo,
        debiteursRepo $debiteursRepo,
        userRepo $userRepo,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        MessageService $MessageService,
        Connection $conn,
        AuthService $AuthService,
        GeneralService $GeneralService,
        typeService $TypeService
        )
    {
        $this->conn = $conn;
        $this->dossiersRepo = $dossiersRepo;
        $this->debiteursRepo = $debiteursRepo;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->MessageService = $MessageService;
        $this->AuthService = $AuthService;
        $this->GeneralService = $GeneralService;
        $this->TypeService = $TypeService;
        $this->userRepo = $userRepo;
    }
    #[Route('/liste_debiteurs')]
    public function liste_debiteurs(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = $debiteursRepo->getListesDebiteurs();

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
    #[Route('/checkDebiteur')]
    public function checkDebiteur(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $checkDebiteur = $this->debiteursRepo->checkDebiteur($id);
            if($checkDebiteur){
                $respObjects["data"] = $checkDebiteur;
                $codeStatut="OK";
                $geRelations = $this->debiteursRepo->getRelationByDebt($id);
                $respObjects["personnes"] = $geRelations;
                $contacts = $this->debiteursRepo->getContacts($id);
                $respObjects["contacts"] = $contacts;
            }else{
                $codeStatut="NOT_EXIST_ELEMENT";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getDetailsDebiteurs')]
    public function getDetailsDebiteurs(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $checkDebiteur = $this->debiteursRepo->checkDebiteur($id);
            if($checkDebiteur){
                $respObjects["data"] = $checkDebiteur;
                $geRelations = count($this->debiteursRepo->getRelationByDebt($id));
                $respObjects["nbr_relation"] = $geRelations;
                $codeStatut="OK";
            }else{
                $codeStatut="NOT_EXIST_ELEMENT";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/filtrerDebiteurs',methods:"POST")]
    public function filtrerDebiteurs(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $date_debut_echeance = ($data_list["date_debut_echeance"]);
            $date_fin_echeance = ($data_list["date_fin_echeance"]);

            if(($date_debut_echeance != "" && $date_fin_echeance != "")  &&$date_fin_echeance < $date_debut_echeance){
                $codeStatut="ERROR_DATE";
            }else{
                $data = $debiteursRepo->getListesDebiteursByFiltrages($data_list);
                $array = array();
                for ($i=0; $i < count($data) ; $i++) { 
                    $array[$i] = $data[$i];
                    $array[$i]["creance"] = $debiteursRepo->getListesCreancesByDebiteurs($data[$i]["id"]);
                    for ($j=0; $j < count( $array[$i]["creance"]); $j++)
                    {
                        $array[$i]["creance"][$j]["type_debiteur"] = $debiteursRepo->getTypeDebiteur( $array[$i]["creance"][$j]["id"] , $data[$i]["id"]);
                    }
                }
                $respObjects["data"] = $array;
                $codeStatut="OK";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/listeRevenu')]
    public function listeRevenu(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id_deb = $request->get("id");
            $type = $request->get("type");
            $data = $debiteursRepo->getListeParamsByDeb($type , $id_deb);
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
    #[Route('/addRevenu',methods:"POST")]
    public function addRevenu(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            if($data_list["revenu"] != "" || $data_list["id_debiteur_id"] != "" || $data_list["adresse"] != "" || $data_list["id_type_revenu_id"] != ""  ){
                $checkType = $this->TypeService->checkType($data_list["id_type_revenu_id"] , "revenu");
                $checkDebiteur = $this->debiteursRepo->checkDebiteur($data_list["id_debiteur_id"]);
                // dump($checkDebiteur);
                if($checkType && $checkDebiteur){
                    $data = $debiteursRepo->createRevenu($data_list);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
            // $respObjects["data"] = $data;
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/updateRevenu',methods:"POST")]
    public function updateRevenu(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            if($data_list["revenu"] != ""  || $data_list["adresse"] != "" || $data_list["id_type_revenu_id"] != ""  ){
                $id = $request->get("id");
                $checkType = $this->TypeService->checkType($data_list["id_type_revenu_id"] , "revenu");
                // $checkDebiteur = $this->debiteursRepo->checkDebiteur($data_list["id_debiteur_id"]);
                if($checkType ){
                    $data = $debiteursRepo->updateRevenu($data_list,$id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/deleteRevenu',methods:"POST")]
    public function deleteRevenu(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id = $request->get("id");
            if($id != "" ){
                $checkRevenu = $this->TypeService->checkElement($id ,"revenu");
                if($checkRevenu ){
                    $data = $debiteursRepo->deleteRevenu($id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/listeCharge')]
    public function listeCharge(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id_deb = $request->get("id");
            $type = $request->get("type");
            $data = $debiteursRepo->getListeParamsByDeb($type , $id_deb);
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
    #[Route('/addCharge',methods:"POST")]
    public function addCharge(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            if($data_list["charge"] != "" || $data_list["id_debiteur_id"] != "" || $data_list["adresse"] != "" || $data_list["id_type_charge_id"] != ""  ){
                $date_debut = $data_list["date_debut"];
                $date_fin = $data_list["date_fin"];
                $checkType = $this->TypeService->checkType($data_list["id_type_charge_id"] , "charge");
                $checkDebiteur = $this->debiteursRepo->checkDebiteur($data_list["id_debiteur_id"]);
                if($checkType && $checkDebiteur){
                    $date = DateTime::createFromFormat('Y-m-d', $date_debut);
                    $dateF = DateTime::createFromFormat('Y-m-d', $date_fin);
                    if (($date && $date->format('Y-m-d') === $date_debut ) && ($dateF && $dateF->format('Y-m-d') === $date_fin ) ) {
                        $checkDetailsDate = $this->GeneralService->checkDateDebutDatefin($date_debut , $date_fin);
                        if($checkDetailsDate){
                            $data = $debiteursRepo->createCharge($data_list);
                            $codeStatut="OK";
                        }else{
                            $codeStatut="ERROR_DATE";
                        }
                    }else{
                        $codeStatut="ERROR_SAISAIE";
                    }
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/updateCharge',methods:"POST")]
    public function updateCharge(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id = $request->get('id');
            if($data_list["charge"] != "" || $data_list["id_debiteur_id"] != "" || $data_list["adresse"] != "" || $data_list["id_type_charge_id"] != ""  ){
                $date_debut = $data_list["date_debut"];
                $date_fin = $data_list["date_fin"];
                $checkType = $this->TypeService->checkType($data_list["id_type_charge_id"] , "charge");
                $checkDebiteur = $this->debiteursRepo->checkDebiteur($data_list["id_debiteur_id"]);
                if($checkType && $checkDebiteur){
                    $date = DateTime::createFromFormat('Y-m-d', $date_debut);
                    $dateF = DateTime::createFromFormat('Y-m-d', $date_fin);
                    if (($date && $date->format('Y-m-d') === $date_debut ) && ($dateF && $dateF->format('Y-m-d') === $date_fin ) ) {
                        $checkDetailsDate = $this->GeneralService->checkDateDebutDatefin($date_debut , $date_fin);
                        if($checkDetailsDate){
                            $id = $request->get("id");
                            $checkType = $this->TypeService->checkElement($id , "charge");
                            if($checkType ){
                                $data = $debiteursRepo->updateCharge($data_list , $id);
                                $codeStatut="OK";
                            }else{
                                $codeStatut="NOT_EXIST_ELEMENT";
                            }
                        }else{
                            $codeStatut="ERROR_DATE";
                        }
                    }else{
                        $codeStatut="ERROR_SAISAIE";
                    }
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/deleteCharge',methods:"POST")]
    public function deleteCharge(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id = $request->get("id");
            if($id != "" ){
                $checkCharge = $this->TypeService->checkElement($id ,"charge");
                if($checkCharge ){
                    $data = $debiteursRepo->deleteCharge($id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/listeFoncier')]
    public function listeFoncier(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id_deb = $request->get("id");
            $type = $request->get("type");
            $data = $debiteursRepo->getListeParamsByDeb($type , $id_deb);
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
    #[Route('/addFoncier',methods:"POST")]
    public function addFoncier(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            if($data_list["numero"] != "" || $data_list["ville"] != "" || $data_list["nature"] != ""  ||  $data_list["id_debiteur_id"] != ""   ){
                $checkDebiteur = $this->debiteursRepo->checkDebiteur($data_list["id_debiteur_id"]);
                if($checkDebiteur){
                    $data = $debiteursRepo->createFoncier($data_list);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/updateFoncier',methods:"POST")]
    public function updateFoncier(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            if($data_list["numero"] != "" || $data_list["ville"] != "" || $data_list["nature"] != ""  ||  $data_list["id_debiteur_id"] != ""   ){
                $id = $request->get("id");
                $checkElement = $this->TypeService->checkElement($id , "foncier");
                if($checkElement ){
                    $data = $debiteursRepo->updateFoncier($data_list,$id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/deleteFoncier',methods:"POST")]
    public function deleteFoncier(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id = $request->get("id");
            if($id != "" ){
                $checkCharge = $this->TypeService->checkElement($id ,"foncier");
                if($checkCharge ){
                    $data = $debiteursRepo->deleteFoncier($id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getOneEmployeur')]
    public function getOneEmployeur(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id_employeur = $request->get("id_employeur");
            $id_deb = $request->get("id_deb");
            $employeur = $debiteursRepo->getOneEmployeur($id_employeur , $id_deb);
            if($employeur){
                $respObjects["data"] = $employeur;
                $respObjects["status_employeur"] = $debiteursRepo->getStatusEmployeur();
                $codeStatut = "OK";
            }else{
                $codeStatut="NOT_EXIST_ELEMENT";
            }

        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/listeDetailsEmployeur')]
    public function listeDetailsEmployeur(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id_deb = $request->get("id");
            $respObjects["status_employeur"] = $debiteursRepo->getStatusEmployeur();

            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/listeEmployeur')]
    public function listeEmployeur(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id_deb = $request->get("id");
            $type = $request->get("type");
            $data = $debiteursRepo->getListeParamsByDeb($type , $id_deb);
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
    #[Route('/addEmployeur',methods:"POST")]
    public function addEmployeur(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            if($data_list["employeur"] != "" || $data_list["montant"] != "" || $data_list["poste"] != "" ||  $data_list["id_debiteur_id"] != ""   ){
                $checkDebiteur = $this->debiteursRepo->checkDebiteur($data_list["id_debiteur_id"]);
                if($checkDebiteur){
                    $data = $debiteursRepo->createEmployeur($data_list);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/updateEmployeur',methods:"POST")]
    public function updateEmployeur(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            if($data_list["employeur"] != "" ||  $data_list["poste"] != "" ||  $data_list["id_debiteur_id"] != ""   ){
                $id = $request->get("id");
                $checkElement = $this->TypeService->checkElement($id , "employeur");
                if($checkElement ){
                    $data = $debiteursRepo->updateEmployeur($data_list,$id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/deleteEmployeur',methods:"POST")]
    public function deleteEmployeur(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id = $request->get("id");
            if($id != "" ){
                $checkCharge = $this->TypeService->checkElement($id ,"foncier");
                if($checkCharge ){
                    $data = $debiteursRepo->deleteEmployeur($id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/listeCompteBancaire')]
    public function listeCompteBancaire(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id_deb = $request->get("id");
            $type = $request->get("type");
            $data = $debiteursRepo->getListeParamsByDeb($type , $id_deb);
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
    #[Route('/addCompteBancaire',methods:"POST")]
    public function addCompteBancaire(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            if($data_list["banque"] != "" || $data_list["rib"] != ""){
                $checkDebiteur = $this->debiteursRepo->checkDebiteur($data_list["id_debiteur_id"]);
                if($checkDebiteur){
                    $data = $debiteursRepo->createCompteBancaire($data_list);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/updateCompteBancaire',methods:"POST")]
    public function updateCompteBancaire(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            if($data_list["banque"] != "" || $data_list["rib"] != ""){
                $id = $request->get("id");
                $checkElement = $this->TypeService->checkElement($id , "compte_bancaire");
                if($checkElement ){
                    $data = $debiteursRepo->updateCompteBancaire($data_list,$id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/deleteCompteBancaire',methods:"POST")]
    public function deleteCompteBancaire(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id = $request->get("id");
            if($id != "" ){
                $checkCharge = $this->TypeService->checkElement($id ,"compte_bancaire");
                if($checkCharge ){
                    $data = $debiteursRepo->deleteCompteBancaire($id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    //Historique d'emploi
    #[Route('/listeHistoriqueEmploi')]
    public function listeHistoriqueEmploi(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id_deb = $request->get("id");
            $type = $request->get("type");
            $data = $debiteursRepo->getListeParamsByDeb($type , $id_deb);
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
    #[Route('/addHistoriqueEmploi',methods:"POST")]
    public function addHistoriqueEmploi(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $date_debut = $data_list["date_debut"];
            $date_fin = $data_list["date_fin"];
            if($data_list["nom_empl"] != ""){
                $checkDebiteur = $this->debiteursRepo->checkDebiteur($data_list["id_debiteur_id"]);
                if($checkDebiteur){
                    $date = DateTime::createFromFormat('Y-m-d', $date_debut);
                    $dateF = DateTime::createFromFormat('Y-m-d', $date_fin);
                    if (($date && $date->format('Y-m-d') === $date_debut ) && ($dateF && $dateF->format('Y-m-d') === $date_fin ) ) {
                        $checkDetailsDate = $this->GeneralService->checkDateDebutDatefin($date_debut , $date_fin);
                        if($checkDetailsDate){
                            $data = $debiteursRepo->createHistoriqueEmploi($data_list);
                            $codeStatut="OK";
                        }else{
                            $codeStatut="ERROR_DATE";
                        }
                    }else{
                        $codeStatut="ERROR_SAISAIE";
                    }
                   
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/updateHistoriqueEmploi',methods:"POST")]
    public function updateHistoriqueEmploi(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            if($data_list["nom_empl"] != ""){
                $id = $request->get("id");
                $checkElement = $this->TypeService->checkElement($id , "historique_emploi");
                if($checkElement ){
                    $data = $debiteursRepo->updateHistoriqueEmploi($data_list,$id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/deleteHistoriqueEmploi',methods:"POST")]
    public function deleteHistoriqueEmploi(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id = $request->get("id");
            if($id != "" ){
                $checkCharge = $this->TypeService->checkElement($id ,"historique_emploi");
                if($checkCharge ){
                    $data = $debiteursRepo->deleteHistoriqueEmploi($id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    //Historique d'emploi
    #[Route('/listeDetailsEmploi')]
    public function listeDetailsEmploi(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id_deb = $request->get("id");
            $respObjects["classification"] = $debiteursRepo->getClassification();
            $respObjects["status_emploi"] = $debiteursRepo->getStatusEmploi();

            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    //Historique d'emploi
    #[Route('/listeProfessionByProfession')]
    public function professionByClassification(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id = $request->get("id");
            $respObjects["profession"] = $debiteursRepo->getProfession($id);
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/listeEmploi')]
    public function listeEmploi(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id_deb = $request->get("id");
            $type = $request->get("type");
            $data = $debiteursRepo->getListeParamsByDeb($type , $id_deb);
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
    #[Route('/addEmploi',methods:"POST")]
    public function addEmploi(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $date_debut = $data_list["date_debut"];
            $date_fin = $data_list["date_fin"];
            if($data_list["nom_empl"] != ""){
                $checkDebiteur = $this->debiteursRepo->checkDebiteur($data_list["id_debiteur_id"]);
                if($checkDebiteur){
                    $date = DateTime::createFromFormat('Y-m-d', $date_debut);
                    $dateF = DateTime::createFromFormat('Y-m-d', $date_fin);
                    if (($date && $date->format('Y-m-d') === $date_debut ) && ($dateF && $dateF->format('Y-m-d') === $date_fin   ) ) {
                        $checkDetailsDate = $this->GeneralService->checkDateDebutDatefin($date_debut , $date_fin);
                        if($checkDetailsDate ){
                            $data = $debiteursRepo->createEmploi($data_list);
                            $codeStatut="OK";
                        }else{
                            $codeStatut="ERROR_DATE";
                        }
                    }else{
                        if($date_debut == '' && $date_fin == ''){
                            $data = $debiteursRepo->createEmploi($data_list);
                            $codeStatut="OK";
                        }else{
                            $codeStatut="ERROR_DATE";
                        }
                    }
                   
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/updateEmploi',methods:"POST")]
    public function updateEmploi(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            if($data_list["nom_empl"] != ""){
                $id = $request->get("id");
                $checkElement = $this->TypeService->checkElement($id , "emploi");
                if($checkElement ){
                    $data = $debiteursRepo->updateEmploi($data_list,$id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getOneEmploi')]
    public function getOneEmploi(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id_emploi = $request->get("id_emploi");
            $id_deb = $request->get("id_deb");
            $emploi = $debiteursRepo->getOneEmploi($id_emploi , $id_deb);
            if($emploi){
                $respObjects["data"] = $emploi;
                $respObjects["classification"] = $debiteursRepo->getClassification();
                $respObjects["status_emploi"] = $debiteursRepo->getStatusEmploi();
                $respObjects["classification_select"] = $debiteursRepo->getClassificationByProfession($emploi["profession_id"]);
                $codeStatut = "OK";
            }else{
                $codeStatut="NOT_EXIST_ELEMENT";
            }

        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/deleteEmploi',methods:"POST")]
    public function deleteEmploi(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id = $request->get("id");
            if($id != "" ){
                $checkEmploi = $this->TypeService->checkElement($id ,"emploi");
                if($checkEmploi ){
                    $data = $debiteursRepo->deleteEmploi($id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
            $respObjects["ddd"] = $id;

        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    //liste tele
    #[Route('/getContactsDebiteur')]
    public function getContactsDebiteur(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id_deb = $request->get("id");
            $type = $request->get("type");
            $adresse = array();
            $data = $debiteursRepo->getAllDetailsDeb($id_deb);
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
    //liste tele
    #[Route('/listeTel')]
    public function listeTel(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id_deb = $request->get("id");
            $type = $request->get("type");
            $data = $debiteursRepo->getListeParamsByDeb($type , $id_deb);
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
    #[Route('/getListeTypeTelephone')]
    public function getListeTypeTelephone(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
           
            $checkType = $this->TypeService->getListeType("telephone");
            $liste_status = $this->TypeService->getListeStatus("telephone");

            $respObjects["data"] = $checkType;
            $respObjects["status"] = $liste_status;

            $codeStatut="OK";
            
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getListeTypeAdresse')]
    public function getListeTypeAdresse(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $checkType = $this->TypeService->getListeType("adresse");
            $liste_status = $this->TypeService->getListeStatus("adresse");
            $respObjects["data"] = $checkType;
            $respObjects["status"] = $liste_status;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getListeTypeEmail')]
    public function getListeTypeEmail(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $checkType = $this->TypeService->getListeType("email");
            $listeStatus = $this->TypeService->getListeStatus("email");
            $respObjects["data"] = $checkType;
            $respObjects["status"] = $listeStatus;
            $codeStatut="OK";
            
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/addTelephone',methods:"POST")]
    public function addTelephone(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        // try {
            $this->AuthService->checkAuth(0, $request);
            $data_list = json_decode($request->getContent(), true);
        
            $data_list["code_p"] = str_replace(" ", "", $data_list["code_p"]);
            $data_list["numero"] = str_replace(" ", "", $data_list["numero"]);
        
            if ($data_list["numero"] != "" || $data_list["origine"] != "" || $data_list["id_type_tel_id"] != "" || $data_list["status"] != "" || $data_list["active"] != "" || $data_list["code_p"] != "") {
                $checkDebiteur = $this->debiteursRepo->checkDebiteur($data_list["id_debiteur_id"]);
                $checkType = $this->TypeService->checkType($data_list["id_type_tel_id"], "tel");
        
                if ($checkDebiteur && $checkType) {
                    if (is_numeric($data_list["numero"]) && is_numeric($data_list["code_p"])) {
                        if ($debiteursRepo->checkCodePays($data_list["code_p"])) {
                            try {
                                // Verify the phone number format
                                $data_list['numero'] = $this->verifyPhoneNumber($data_list['numero'], $data_list['code_p']);
                                
                                $data_list['id_type_source_id'] = 4;
                                $data = $debiteursRepo->createTelephone($data_list);
                                $codeStatut = "OK";
                            } catch (Exception $e) {
                                $codeStatut = $e->getMessage();
                            }
                        } else {
                            $codeStatut = "FORMAT_INCORRECT";
                        }
                    } else {
                        $codeStatut = "FORMAT_INCORRECT";
                    }
                } else {
                    $codeStatut = "NOT_EXIST_ELEMENT";
                }
            } else {
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
        // } catch (\Exception $e) {
        //     $codeStatut = "ERROR";
        //     $respObjects["err"] = $e->getMessage();
        // }
        
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/updateTelephone',methods:"POST")]
    public function updateTelephone(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            if($data_list["numero"] != "" || $data_list["origine"] != "" || $data_list["status"] != "" || $data_list["active"] != ""){
                $id = $request->get("id");
                $checkElement = $this->TypeService->checkElement($id , "telephone");
                if($checkElement ){
                    $data = $debiteursRepo->updateTelephone($data_list,$id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/deleteTelephone',methods:"POST")]
    public function deleteTelephone(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id = $request->get("id");
            $id_debiteur = $request->get("id_debiteur");
            if($id != "" ){
                $data = $debiteursRepo->checkIfDoubleTeleAndNotActive($id_debiteur);
                if($data > 1)
                {
                    $checkCharge = $this->TypeService->checkElement($id ,"telephone");
                    if($checkCharge ){
                        $data = $debiteursRepo->deleteTelephone($id);
                        $codeStatut="OK";
                    }else{
                        $codeStatut="NOT_EXIST_ELEMENT";
                    }
                }
                else{
                    $codeStatut="ONE_TEL";
                }

            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getTelephoneById',methods:"GET")]
    public function getTelephoneById(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0, $request);
            $id = $request->get("id");
            $respObjects["data"] = $debiteursRepo->getTelephoneById($id);;
            $codeStatut="OK";
        } catch (\Exception $e) {
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    //Adresse
    #[Route('/listeAdresse')]
    public function listeAdresse(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id_deb = $request->get("id");
            $type = $request->get("type");
            $data = $debiteursRepo->getListeParamsByDeb($type , $id_deb);
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
    #[Route('/addAdresse',methods:"POST")]
    public function addAdresse(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            if($data_list["adresse_complet"] != "" || $data_list["code_postal"] != "" ||  $data_list["pays"] != "" || $data_list["ville"] != "" || $data_list["status"] != ""  || $data_list["verifier"] != ""){
                $checkDebiteur = $this->debiteursRepo->checkDebiteur($data_list["id_debiteur_id"]);
                $checkType = $this->TypeService->checkType($data_list["id_type_adresse_id"] , "adresse");
                if($checkDebiteur && $checkType){
                    $data = $debiteursRepo->createAdresse($data_list);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/updateAdresse',methods:"POST")]
    public function updateAdresse(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            if($data_list["adresse_complet"] != "" || $data_list["code_postal"] != "" ||  $data_list["pays"] != "" || $data_list["ville"] != "" || $data_list["status"] != ""  || $data_list["verifier"] != ""){
                $id = $request->get("id");
                $checkElement = $this->TypeService->checkElement($id , "adresse");
                if($checkElement ){
                    $data = $debiteursRepo->updateAdresse($data_list,$id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/deleteAdresse',methods:"POST")]
    public function deleteAdresse(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id = $request->get("id");
            if($id != "" ){
                $checkCharge = $this->TypeService->checkElement($id ,"adresse");
                if($checkCharge ){
                    $data = $debiteursRepo->deleteAdresse($id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/addEmail',methods:"POST")]
    public function addEmail(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            if($data_list["email"] != ""){
                $checkDebiteur = $this->debiteursRepo->checkDebiteur($data_list["id_debiteur_id"]);
                $checkType = $this->TypeService->checkType($data_list["id_type_email_id"] , "email");
                $checkStatus = $this->TypeService->getOneStatus( "email" , $data_list["id_status_email_id"] );
                // if($checkDebiteur && $checkType && $checkStatus){
                if($this->isValidEmail($data_list["email"])){
                    $data_list['id_type_source_id'] = 4;
                    $data = $debiteursRepo->createEmail($data_list);
                    $codeStatut="OK";
                }else{
                    $codeStatut="INVALID_EMAIL";
                }
                // }else{
                //     $codeStatut="NOT_EXIST_ELEMENT";
                // }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/updateEmail',methods:"POST")]
    public function updateEmail(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            if($data_list["email"] != ""){
                $id = $request->get("id");
                $checkElement = $this->TypeService->checkElement($id , "email");
                $checkDebiteur = $this->debiteursRepo->checkDebiteur($data_list["id_debiteur_id"]);
                $checkType = $this->TypeService->checkType($data_list["id_type_email_id"] , "email");
                $checkStatus = $this->TypeService->getOneStatus( "email" , $data_list["id_status_email_id"] );
                // if($checkDebiteur && $checkType && $checkStatus){
                    if($checkElement ){
                        $data = $debiteursRepo->updateEmail($data_list,$id);
                        $codeStatut="OK";
                    }else{
                        $codeStatut="NOT_EXIST_ELEMENT";
                    }
                // }else{
                //     $codeStatut="NOT_EXIST_ELEMENT";
                // }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/deleteEmail',methods:"POST")]
    public function deleteEmail(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id = $request->get("id");
            if($id != "" ){
                $checkCharge = $this->TypeService->checkElement($id ,"email");
                if($checkCharge ){
                    $data = $debiteursRepo->deleteEmail($id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getIdenticatifPays')]
    public function getIdenticatifPays(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id = $request->get("id");
            $data = $debiteursRepo->getIdenticatifPays();
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
    #[Route('/getAdresseById',methods:"GET")]
    public function getAdresseById(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0, $request);
            $id = $request->get("id");
            $respObjects["data"] = $debiteursRepo->getAdresseById($id);;
            $codeStatut="OK";
        } catch (\Exception $e) {
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getEmailById',methods:"GET")]
    public function getEmailById(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0, $request);
            $id = $request->get("id");
            $respObjects["data"] = $debiteursRepo->getEmailById($id);;
            $codeStatut="OK";
        } catch (\Exception $e) {
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getListeTypeRelation',methods:"GET")]
    public function getListeTypeRelation(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0, $request);

            $listeType = $this->TypeService->getListeType("relation");
            $respObjects["data"] = $listeType;
            $codeStatut="OK";

        } catch (\Exception $e) {
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/listeRelation')]
    public function listeRelation(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id_deb = $request->get("id");
            $type = $request->get("type");
            $data = $debiteursRepo->getListeParamsByDeb($type , $id_deb);
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
    #[Route('/addRelation',methods:"POST")]
    public function addRelation(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $numero = $data_list["numero"] ;
            $nom = $data_list["nom"] ;
            $prenom = $data_list["prenom"] ;
            $adresse = $data_list["adresse"] ;

            if($nom != ""  || $numero != "" ||  $adresse != ""  || $data_list["id_type_relation_id"] != ""  ){
                $checkType = $this->TypeService->checkType($data_list["id_type_relation_id"] , "relation");
                $checkDebiteur = $this->debiteursRepo->checkDebiteur($data_list["id_debiteur_id"]);
                if($checkType && $checkDebiteur){
                    $data = $debiteursRepo->createRelation($data_list);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/updateRelation',methods:"POST")]
    public function updateRelation(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id = $request->get('id');
            $numero = $data_list["numero"] ;
            $nom = $data_list["nom"] ;
            $prenom = $data_list["prenom"] ;
            $adresse = $data_list["adresse"] ;

            if($nom != ""  || $numero != "" ||  $adresse != ""  || $data_list["id_type_relation_id"] != ""  ){
                $checkType = $this->TypeService->checkType($data_list["id_type_relation_id"] , "relation");
                $checkDebiteur = $this->debiteursRepo->checkDebiteur($data_list["id_debiteur_id"]);
                if($checkType && $checkDebiteur){
                    $data = $debiteursRepo->updateRelation($data_list , $id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/deleteRelation',methods:"POST")]
    public function deleteRelation(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id = $request->get("id");
            if($id != "" ){
                $checkCharge = $this->TypeService->checkElement($id ,"relation");
                if($checkCharge ){
                    $data = $debiteursRepo->deleteRelation($id);
                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }


    #[Route('/getRelationById')]
    public function getRelationById(Request $request,debiteursRepo $debiteursRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            // $this->AuthService->checkAuth(0, $request);
            $id = $request->get("id");
            $respObjects["data"] = $debiteursRepo->getRelationById($id);;
            $codeStatut="OK";
        } catch (\Exception $e) {
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    function verifyPhoneNumber($phoneNumber, $countryCode) {
        if ($countryCode == "212") {
            // If the phone number starts with '0', remove the '0'
            if ($phoneNumber[0] == '0') {
                $phoneNumber = substr($phoneNumber, 1);
                // Verify that the remaining number has 9 digits
                if (strlen($phoneNumber) == 9) {
                    if (in_array($phoneNumber[0], ['5', '6', '7', '8'])) {
                        return $phoneNumber;
                    }else{
                         throw new \Exception("FORMAT_INCORRECT");
                    }
                } else {
                    throw new \Exception("FORMAT_INCORRECT");
                }
            } else {
                // If the phone number does not start with '0', verify the second digit
                if (in_array($phoneNumber[0], ['5', '6', '7', '8'])) {
                    return $phoneNumber;
                } else {
                    throw new \Exception("FORMAT_INCORRECT");
                }
            }
        } else {
            return $phoneNumber; // No additional verification for other country codes
        }
    }
    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
}
