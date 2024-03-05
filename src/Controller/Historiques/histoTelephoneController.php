<?php

namespace App\Controller\Historiques;

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
use App\Repository\Historiques\histoRepo;
use App\Repository\Users\userRepo;
#[Route('/API/histo')]
class histoTelephoneController extends AbstractController
{
    private  $integrationRepo;
    private  $donneurRepo;
    private  $affichageRepo;
    private  $serializer;
    public $em;
    private $conn;
    private $AuthService;
    private $TypeService;

    public function __construct(
        histoRepo $histoRepo,
        userRepo $userRepo,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        MessageService $MessageService,
        Connection $conn,
        AuthService $AuthService,
        TypeService $TypeService
        )
    {
        $this->conn = $conn;
        $this->histoRepo = $histoRepo;
        $this->userRepo = $userRepo;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->MessageService = $MessageService;
        $this->AuthService = $AuthService;
        $this->TypeService = $TypeService;
    }
    #[Route('/histo_telephone_supprimer')]
    public function liste_telephone_supprimer(Request $request,histoRepo $histoRepo , userRepo $userRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            //Vérifier date 
            if($request->get("day") == "1"){
                $date_debut = date("Y-m-d")." 00:00:00";
                $date_fin = date("Y-m-d")." 23:59:59";
            }else{
                $date_debut = $request->get("date_debut")." 00:00:00";
                $date_fin = $request->get("date_fin")." 23:59:59";
            }
            $data = $histoRepo->getHistoTelephone($date_debut , $date_fin);
            $array= array();
            if($data)
            {
                for($i=0; $i < count($data); $i++)
                {
                    $array[$i] = $data[$i];
                    $array[$i]["id_type_tel_id"] = $this->TypeService->getTypeById($data[$i]["id_type_tel_id"], "tel");
                    $array[$i]["id_users_id"] = $userRepo->getUser($data[$i]["id_users_id"]);
                    $array[$i]["is_status_id"] = $this->TypeService->getOneStatus("telephone",$data[$i]["id_status_id"]);
                }
            }
            $respObjects["data"] = $array;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/histo_telephone_ajouter')]
    public function histo_telephone_ajouter(Request $request,histoRepo $histoRepo , userRepo $userRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            //Vérifier date 
            if($request->get("day") == "1"){
                $date_debut = date("Y-m-d")." 00:00:00";
                $date_fin = date("Y-m-d")." 23:59:59";
            }else{
                $date_debut = $request->get("date_debut")." 00:00:00";
                $date_fin = $request->get("date_fin")." 23:59:59";
            }
            $data = $histoRepo->getListeTel();
            if($data)
            {
                for($i=0; $i < count($data); $i++)
                {
                    $array[$i] = $data[$i];
                    $array[$i]["id_type_tel_id"] = $this->TypeService->getTypeById($data[$i]["id_type_tel_id"], "tel");
                    // $array[$i]["id_users_id"] = $userRepo->getUser($data[$i]["id_users_id"]);
                    $array[$i]["is_status_id"] = $this->TypeService->getOneStatus("telephone",$data[$i]["id_status_id"]);
                }
            }
            $respObjects["data"] = $array;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/histo_adresse_supprimer')]
    public function liste_adresse_supprimer(Request $request,histoRepo $histoRepo , userRepo $userRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            //Vérifier date 
            if($request->get("day") == "1"){
                $date_debut = date("Y-m-d")." 00:00:00";
                $date_fin = date("Y-m-d")." 23:59:59";
            }else{
                $date_debut = $request->get("date_debut")." 00:00:00";
                $date_fin = $request->get("date_fin")." 23:59:59";
            }
            $data = $histoRepo->getHistoAdresse($date_debut , $date_fin);
            $array= array();
            if($data)
            {
                for($i=0; $i < count($data); $i++)
                {
                    $array[$i] = $data[$i];
                    $array[$i]["type_tel"] = $this->TypeService->getTypeById($data[$i]["id_type_adresse_id"], "adresse");
                    $array[$i]["utilisateurs"] = $userRepo->getUser($data[$i]["id_users_id"]);
                }
            }
            $respObjects["data"] = $array;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/histo_adresse_ajouter')]
    public function histo_adresse_ajouter(Request $request,histoRepo $histoRepo , userRepo $userRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            //Vérifier date 
            if($request->get("day") == "1"){
                $date_debut = date("Y-m-d")." 00:00:00";
                $date_fin = date("Y-m-d")." 23:59:59";
            }else{
                $date_debut = $request->get("date_debut")." 00:00:00";
                $date_fin = $request->get("date_fin")." 23:59:59";
            }

            $data = $histoRepo->getListeAdresse();
            if($data)
            {
                for($i=0; $i < count($data); $i++)
                {
                    $array[$i] = $data[$i];
                    $array[$i]["type_adresse"] = $this->TypeService->getTypeById($data[$i]["id_type_adresse_id"], "adresse");
                }
            }
            $respObjects["data"] = $array;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/histo_emploi_supprimer')]
    public function liste_emploi_supprimer(Request $request,histoRepo $histoRepo , userRepo $userRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            //Vérifier date 
            if($request->get("day") == "1"){
                $date_debut = date("Y-m-d")." 00:00:00";
                $date_fin = date("Y-m-d")." 23:59:59";
            }else{
                $date_debut = $request->get("date_debut")." 00:00:00";
                $date_fin = $request->get("date_fin")." 23:59:59";
            }
            $data = $histoRepo->getHistoEmploi($date_debut , $date_fin);
            $array= array();
            if($data)
            {
                for($i=0; $i < count($data); $i++)
                {
                    $array[$i] = $data[$i];
                    $array[$i]["type_tel"] = $this->TypeService->getTypeById($data[$i]["id_type_emploi_id"], "emploi");
                    $array[$i]["utilisateurs"] = $userRepo->getUser($data[$i]["id_users_id"]);
                }
            }
            $respObjects["data"] = $array;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/histo_emlpoi_ajouter')]
    public function histo_emlpoi_ajouter(Request $request,histoRepo $histoRepo , userRepo $userRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            //Vérifier date 
            if($request->get("day") == "1"){
                $date_debut = date("Y-m-d")." 00:00:00";
                $date_fin = date("Y-m-d")." 23:59:59";
            }else{
                $date_debut = $request->get("date_debut")." 00:00:00";
                $date_fin = $request->get("date_fin")." 23:59:59";
            }

            $data = $histoRepo->getListeEmploi();
            if($data)
            {
                for($i=0; $i < count($data); $i++)
                {
                    $array[$i] = $data[$i];
                    $array[$i]["type_emlpoi"] = $this->TypeService->getTypeById($data[$i]["id_type_emlpoi_id"], "emlpoi");
                }
            }
            $respObjects["data"] = $array;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/histo_employeur_supprimer')]
    public function liste_employeur_supprimer(Request $request,histoRepo $histoRepo , userRepo $userRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            //Vérifier date 
            if($request->get("day") == "1"){
                $date_debut = date("Y-m-d")." 00:00:00";
                $date_fin = date("Y-m-d")." 23:59:59";
            }else{
                $date_debut = $request->get("date_debut")." 00:00:00";
                $date_fin = $request->get("date_fin")." 23:59:59";
            }
            $data = $histoRepo->getHistoEmployeur($date_debut , $date_fin);
            $array= array();
            if($data)
            {
                for($i=0; $i < count($data); $i++)
                {
                    $array[$i] = $data[$i];
                    $array[$i]["type_tel"] = $this->TypeService->getTypeById($data[$i]["id_type_employeur_id"], "employeur");
                    $array[$i]["utilisateurs"] = $userRepo->getUser($data[$i]["id_users_id"]);
                }
            }
            $respObjects["data"] = $array;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/histo_employeur_ajouter')]
    public function histo_employeur_ajouter(Request $request,histoRepo $histoRepo , userRepo $userRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            //Vérifier date 
            if($request->get("day") == "1"){
                $date_debut = date("Y-m-d")." 00:00:00";
                $date_fin = date("Y-m-d")." 23:59:59";
            }else{
                $date_debut = $request->get("date_debut")." 00:00:00";
                $date_fin = $request->get("date_fin")." 23:59:59";
            }

            $data = $histoRepo->getListeEmployeur();
            if($data)
            {
                for($i=0; $i < count($data); $i++)
                {
                    $array[$i] = $data[$i];
                    $array[$i]["type_employeur"] = $this->TypeService->getTypeById($data[$i]["id_type_employeur_id"], "employeur");
                }
            }
            $respObjects["data"] = $array;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
}

