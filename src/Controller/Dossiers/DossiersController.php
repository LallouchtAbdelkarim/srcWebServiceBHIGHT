<?php

namespace App\Controller\Dossiers;

use App\Repository\DonneurOrdreAndPTF\donneurRepo;
use App\Repository\Dossiers\dossiersRepo;
use App\Repository\Users\userRepo;
use App\Service\GeneralService;
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

#[Route('/API/dossiers')]
class DossiersController extends AbstractController
{
    private  $integrationRepo;
    private  $donneurRepo;
    private  $affichageRepo;
    private  $serializer;
    public $em;
    private $conn;
    private $AuthService;

    public $generalService;

    public function __construct(
        dossiersRepo $dossiersRepo,
        userRepo $userRepo,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        MessageService $MessageService,
        Connection $conn,
        AuthService $AuthService,
        GeneralService $generalService
        )
    {
        $this->conn = $conn;
        $this->dossiersRepo = $dossiersRepo;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->MessageService = $MessageService;
        $this->AuthService = $AuthService;
        $this->userRepo = $userRepo;
        $this->generalService = $generalService;
    }

    #[Route('/liste_dossiers', name: 'app_dossiers_dossiers')]
    public function liste_dossiers(Request $request,dossiersRepo $dossiersRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = $dossiersRepo->getListesDossiers();
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
    #[Route('/liste_dossiers_by_filtrage',methods:"POST")]
    public function liste_dossiers_by_filtrage(Request $request,dossiersRepo $dossiersRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            //Vérifier date 
            $data = $dossiersRepo->getListesDossiersByFiltrages($data_list);
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
    #[Route('/details_dossier')]
    public function details_debiteur_by_dossier(Request $request,dossiersRepo $dossiersRepo , donneurRepo $donneurRepo,userRepo $userRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $idUser = $this->AuthService->returnUserId($request);
            
            $id = $request->get("id");
            $check_doss = $dossiersRepo->findDossier($id);
            if(!$check_doss){
                $codeStatut="NOT_EXIST_ELEMENT";
            }else{
                $dossier = $check_doss[0];
                $array_doss=array();
                $array_accord=array();
                $array_doss["dossier"]=$dossier;
                $array_doss["user"]=null;
                
                if($dossier["id_users_id"]){
                    $array_doss["user"] = $userRepo->getOneUser($dossier["id_users_id"]);
                }   
                if($dossier["id_ptf_id"]){
                    $array_doss["ptf"] = $donneurRepo->getOnePtf($dossier["id_ptf_id"]);
                } 

                $accords = $dossiersRepo->getAccords($id);
                for ($i=0; $i < count($accords); $i++) { 
                    $array_accord[$i] = $accords[$i];
                    $array_accord[$i]["type_paiement"] = $dossiersRepo->getTypePaiem($accords[$i]['id_type_paiement_id']);
                    $array_accord[$i]["total_creance"] = $dossiersRepo->getTotalCreanceByAcc($accords[$i]["id"]);
                    $array_accord[$i]["montant_restant"] = number_format((float)$dossiersRepo->getMontantRestant($accords[$i]["id"]), 2, '.', '');
                }

                $debiteur = $dossiersRepo->getDebiteurByDossier($id);
                $array_deb = $dossiersRepo->getListesDebiteurByDossier($id);
                $adresse = $dossiersRepo->getListesAdresse($id);
                $email_deb = $dossiersRepo->getEmailDebiteur($id);
                $histo = $dossiersRepo->getHistoriqueDossier($id);
                $accords = $dossiersRepo->getAccords($id);
                $courriers = $dossiersRepo->getCourrier($id);
                $sms = $dossiersRepo->getSms($id);
                $email = $dossiersRepo->getEmail($id);
                $tel = $dossiersRepo->getListesTel($id);
                $notes = $dossiersRepo->getListeNote($id);
                $creance = $dossiersRepo->getCreanceByIdDossier($id);
                $process = $dossiersRepo->getProcessByIdUser($id , $idUser);
                $nb_accord = $dossiersRepo->getNbrAccord($id);
                

                
                $respObjects["total"]  = $dossiersRepo->getDetailsCreanceByIdDossier($id);
                $respObjects["dossier"] = $array_doss;
                $respObjects["allDebiteur"] = $array_deb;
                $respObjects["debiteur"] = $debiteur;
                $respObjects["adresse"] = $adresse;
                $respObjects["tel"] = $tel;
                $respObjects["email_deb"] = $email_deb;
                $respObjects["debiteur"] = $debiteur;
                $respObjects["notes"] = $notes;
                $respObjects["creance"] = $creance;
                $respObjects["process"] = $process;

                if(isset($debiteur[0])){
                    $respObjects["debiteur"] = $debiteur[0];
                }
                if(isset($adresse[0])){
                    $respObjects["adresse"] = $adresse[0];
                    $type_adresse = $adresse[0]["id_type_adresse_id"];
                    $respObjects["type_adresse"] = $dossiersRepo->getTypeAdresse($type_adresse);
                    $respObjects["type_adresse"] = $dossiersRepo->getTypeAdresse($type_adresse);
                }
                if(isset($tel[0])){
                    $respObjects["tel"] = $tel[0];
                    $type_tel = $tel[0]["id_type_tel_id"];
                    $respObjects["type_tel"] = $dossiersRepo->getTypeTel($type_tel);
                }
                
                if(isset($email_deb[0])){
                    $respObjects["email_deb"] = $email_deb[0];
                    $type_email = $email_deb[0]["id_type_email_id"];
                    $respObjects["type_email"] = $dossiersRepo->getTypeEmail($type_email);
                }

                $respObjects["histo"] = $histo;
                $respObjects["accords"] = $array_accord;
                $respObjects["courriers"] = $courriers;
                $respObjects["sms"] = $sms;
                $respObjects["email"] = $email;
                $respObjects["nbr_accord"] = $nb_accord;
                $respObjects["nbr_note"] = $dossiersRepo->getNbrNote($id);
                $respObjects["nbr_pj"] = $dossiersRepo->getNbrPj($id);
                
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
    #[Route('/check_dossier')]
    public function check_dossier(Request $request,dossiersRepo $dossiersRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $check_doss = $dossiersRepo->findDossier($id);
            if(!$check_doss){
                $codeStatut="NOT_EXIST_ELEMENT";
            }else{
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
    #[Route('/create_note_dossier',methods:"POST")]
    public function create_note_dossier(Request $request,dossiersRepo $dossiersRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $id = $data_list["id"];
            $check_doss = $dossiersRepo->findDossier($id);
            if(!$check_doss){
                $codeStatut="NOT_EXIST_ELEMENT";
            }else{
                if(isset($data_list["note"])){
                    $note = $data_list["note"];
                    if($note != ""){
                        $dossiersRepo->createNoteDossier($id , $note);
                        $codeStatut="OK";
                    }
                }
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/attacheFile',methods:"POST")]
    public function attacheFile(Request $request,dossiersRepo $dossiersRepo): Response
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            

        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    
    
}
