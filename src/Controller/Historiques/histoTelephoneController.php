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

    #[Route('/histo_telephone' , methods:['POST'])]
    public function histo_telephone(Request $request,histoRepo $histoRepo , userRepo $userRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $idPtf = $data_list['idPtf'];
            //Vérifier date 
            if($request->get("day") == "1"){
                $date_debut = date("Y-m-d")." 00:00:00";
                $date_fin = date("Y-m-d")." 23:59:59";
            }else{
                $date_debut = $data_list["date_debut"]." 00:00:00";
                $date_fin = $data_list["date_fin"]." 23:59:59";
            }
            $data = $histoRepo->getListeTel($date_debut  , $date_fin ,  $idPtf);

            $array=[];
            for($i=0; $i < count($data); $i++)
            {
                $array[$i] = $data[$i];
                $array[$i]["id_type_tel_id"] = $this->TypeService->getTypeById($data[$i]["id_type_tel_id"], "tel");
                // $array[$i]["id_users_id"] = $userRepo->getUser($data[$i]["id_users_id"]);
                $array[$i]["is_status_id"] = $this->TypeService->getOneStatus("telephone",$data[$i]["id_status_id"]);
            }


            $data2 = $histoRepo->getHistoTelephone($date_debut , $date_fin , $idPtf);
            $array2=[];
            for($i=0; $i < count($data2); $i++)
            {
                $array2[$i] = $data2[$i];
                $array2[$i]["id_type_tel_id"] = $this->TypeService->getTypeById($data2[$i]["id_type_tel_id"], "tel");
                // $array2[$i]["id_users_id"] = $userRepo->getUser($data[$i]["id_users_id"]);
                $array2[$i]["is_status_id"] = $this->TypeService->getOneStatus("telephone",$data2[$i]["id_status_id"]);
            }
            
            $respObjects["data"] = $array;
            $respObjects["data_supprime"] = $array2;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/histo_adresse', methods:['POST'])]
    public function histo_adresse(Request $request,histoRepo $histoRepo , userRepo $userRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $idPtf = $data_list['idPtf'];
            //Vérifier date 
            if($request->get("day") == "1"){
                $date_debut = date("Y-m-d")." 00:00:00";
                $date_fin = date("Y-m-d")." 23:59:59";
            }else{
                $date_debut = $data_list["date_debut"]." 00:00:00";
                $date_fin = $data_list["date_fin"]." 23:59:59";
            }
            $data = $histoRepo->getHistoAdresse($date_debut , $date_fin , $idPtf);
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

            $date_debut . $date_fin;

            $data2 = $histoRepo->getListeAdresse($date_debut , $date_fin , $idPtf);
            $array2 = [];
            if($data2)
            {
                for($i=0; $i < count($data2); $i++)
                {
                    $array2[$i] = $data2[$i];
                    $array2[$i]["type_adresse"] = $this->TypeService->getTypeById($data2[$i]["id_type_adresse_id"], "adresse");
                }
            }
            
            $respObjects["data"] =  $array2;
            $respObjects["data_supprime"] = $array;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    
    #[Route('/histo_emploi',methods:['POST'])]
    public function histo_emploi(Request $request,histoRepo $histoRepo , userRepo $userRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $idPtf = $data_list['idPtf'];
            //Vérifier date 
            if($request->get("day") == "1"){
                $date_debut = date("Y-m-d")." 00:00:00";
                $date_fin = date("Y-m-d")." 23:59:59";
            }else{
                $date_debut = $data_list["date_debut"]." 00:00:00";
                $date_fin = $data_list["date_fin"]." 23:59:59";
            }
            $data = $histoRepo->getHistoEmploi($date_debut , $date_fin , $idPtf);
            $array= array();
            if($data)
            {
                for($i=0; $i < count($data); $i++)
                {
                    $array[$i] = $data[$i];
                    // $array[$i]["type_tel"] = $this->TypeService->getTypeById($data[$i]["id_type_emploi_id"], "emploi");
                    // $array[$i]["utilisateurs"] = $userRepo->getUser($data[$i]["id_users_id"]);
                }
            }
            $respObjects["data_supprime"] = $array;

            $data2 = $histoRepo->getListeEmploi($date_debut , $date_fin , $idPtf);
            $array2= array();
            if($data2)
            {
                for($i=0; $i < count($data2); $i++)
                {
                    $array2[$i] = $data2[$i];
                    // $array2[$i]["type_emlpoi"] = $this->TypeService->getTypeById($data2[$i]["id_type_emlpoi_id"], "emlpoi");
                }
            }
            $respObjects["data"] = $array2;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }


    #[Route('/histo_employeur',methods:['POST'])]
    public function histo_employeur(Request $request,histoRepo $histoRepo , userRepo $userRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $idPtf = $data_list['idPtf'];
            //Vérifier date 
            if($request->get("day") == "1"){
                $date_debut = date("Y-m-d")." 00:00:00";
                $date_fin = date("Y-m-d")." 23:59:59";
            }else{
                $date_debut = $data_list["date_debut"]." 00:00:00";
                $date_fin = $data_list["date_fin"]." 23:59:59";
            }

            $data = $histoRepo->getListeEmployeur($date_debut , $date_fin , $idPtf);
            $array = [];
            if($data)
            {
                for($i=0; $i < count($data); $i++)
                {
                    $array[$i] = $data[$i];
                    // $array[$i]["type_employeur"] = $this->TypeService->getTypeById($data[$i]["id_type_employeur_id"], "employeur");
                }
            }
            $respObjects["data"] = $array;

            $data2 = $histoRepo->getHistoEmployeur($date_debut , $date_fin , $idPtf);
            $array2= array();
            if($data2)
            {
                for($i=0; $i < count($data2); $i++)
                {
                    $array2[$i] = $data2[$i];
                    // $array2[$i]["type_tel"] = $this->TypeService->getTypeById($data2[$i]["id_type_employeur_id"], "employeur");
                    // $array2[$i]["utilisateurs"] = $userRepo->getUser($data2[$i]["id_users_id"]);
                }
            }
            $respObjects["data_supprime"] = $array2;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/histo_email',methods:['POST'])]
    public function histo_email(Request $request,histoRepo $histoRepo , userRepo $userRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $idPtf = $data_list['idPtf'];
            //Vérifier date 
            if($request->get("day") == "1"){
                $date_debut = date("Y-m-d")." 00:00:00";
                $date_fin = date("Y-m-d")." 23:59:59";
            }else{
                $date_debut = $data_list["date_debut"]." 00:00:00";
                $date_fin = $data_list["date_fin"]." 23:59:59";
            }

            $data = $histoRepo->getListeEmail($date_debut , $date_fin , $idPtf);
            $array = [];
            if($data)
            {
                for($i=0; $i < count($data); $i++)
                {
                    $array[$i] = $data[$i];
                    $array[$i]["type_email"] = $this->TypeService->getTypeById($data[$i]["id_type_email_id"], "email");
                    $array[$i]["status_email"] = $this->TypeService->getOneStatus( "status" , $data[$i]["id_status_email_id"]);
                }
            }
            $respObjects["data"] = $array;

            $data2 = $histoRepo->getHistoEmail($date_debut , $date_fin , $idPtf);
            $array2= array();
            if($data2)
            {
                for($i=0; $i < count($data2); $i++)
                {
                    $array2[$i] = $data2[$i];
                    // $array2[$i]["type_tel"] = $this->TypeService->getTypeById($data2[$i]["id_type_employeur_id"], "employeur");
                    $array2[$i]["type_email"] = $this->TypeService->getTypeById($data2[$i]["id_type_email"], "email");
                    $array2[$i]["status_email"] = $this->TypeService->getOneStatus( "email" , $data2[$i]["id_status_email"]);
                }
            }
            $respObjects["data_supprime"] = $array2;
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

