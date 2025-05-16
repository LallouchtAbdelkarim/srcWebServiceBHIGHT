<?php

namespace App\Controller\Creances;

use App\Entity\Accord;
use App\Entity\BackgroundCourrier;
use App\Entity\Creance;
use App\Entity\StatusAccord;
use App\Repository\AccordNotesRepository;
use App\Repository\AccordPjRepository;
use App\Repository\AccordRepository;
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
use DateTime;
use App\Repository\UtilisateursRepository;
use App\Repository\TeamsRepository;

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
    private $MessageService;


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
    public function liste_creances_by_filtrage(Request $request,creancesRepo $creancesRepo): JsonResponse
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
            $idUser = $this->AuthService->returnUserId($request);
            $id = $request->get("id");

            $from = $request->get("from");

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

                $recent = false;
                if (isset($from)) {
                    $recent = $creancesRepo->addRecentCreance($id,$idUser );
                    $respObjects["recent"] = $recent->getId();
                }
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
    public function createAccord(Request $request,creancesRepo $creancesRepo, AccordNotesRepository $accordNotesRepo, AccordPjRepository  $accordPjRepository): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $creance = null;
            $createAccord = null;
            if($id != "undefined"){
                $creance = $creancesRepo->getOneCreance($id);
            }
            $dataJson = $request->request->get('data');
            $dataPaiement = json_decode($dataJson, true);
            // $dataPaiement = $data["data"];
            // $montant = $data["montant"];
            // $type_paiement = $data["type_paiment"];
            // $montant_a_Payer = $data["montant_a_Payer"];
            // $echeanciers = $data["echeanciers"];
            // $date_debut = $data["date_debut"];
            // $notes = $data["notes"];

            // Access other form fields
            $montant = $request->request->get('montant');
            $type_paiement = $request->request->get('type_paiment');
            $montant_a_Payer = $request->request->get('montant_a_Payer');
            $date_debut = $request->request->get('date_debut');
            $echeanciers = json_decode($request->request->get('echeanciers'), true);
            $frenquence = $request->request->get('frenquence');
            $list_creance = json_decode($request->request->get('list_creance'), true);
            $notes = json_decode($request->request->get('notes'), true);

            $montant_de_base = $request->request->get('montant_de_base');
            $fee_admin = $request->request->get('fee_admin');
            $fee_installment = $request->request->get('fee_installment');
            $interets = $request->request->get('interets');
            $remise = $request->request->get('remise');
            $accompte = $request->request->get('accompte');
            $etat = $request->request->get('etat');
            $type = $request->request->get('type');
            $id_debiteur_id = $request->request->get('debiteur');
            $id_payeur_id = $request->request->get('payeur');
            $promesse = $request->request->get('promesse');



            
            switch ($etat) {
                case 'en attente validation':
                    $etatNumeric = 9;
                    break;
                case 'en cours':
                    $etatNumeric = 0;
                    break;
                default:
                    // Handle unexpected values, if needed (optional)
                    $etatNumeric = null; // or set a default value like 0 or another appropriate value
                    break;
            }

            if($type == "3")
            {
                $etatNumeric = 7;
            }

            // Access uploaded files
            $attachments = $request->files->all()['attachments'] ?? [];
            $dataAttachments = $request->request->all()['attachments'] ?? [];

            $date_day = date("Y-m-d"); // Current date
            if($creance){

                // $accords = $creancesRepo->getAccords($id);

                // // Initialize flags to check if any accord has getIdStatut() = 1 or 0
                // $hasStatutOneOrZero = false;

                // // Iterate through the list of accords
                // foreach ($accords as $item) {
                //     $idStatut = $item["id_status_id"]; // Assuming getIdStatut() is a method on the accord object

                //     if ($idStatut == 1 || $idStatut == 0) {
                //         $hasStatutOneOrZero = true;
                //         break; // Exit the loop once we find a match
                //     }
                // }

                // if ($hasStatutOneOrZero) {

                //     $codeStatut = "ACCORD-ACTIF";

                // }

                if($montant_de_base> $creance["total_restant"])
                {
                    $codeStatut="ERROR_ACCORD_RESTANT";
                }
                else
                {
                    $type_select = $this->TypeService->checkType($type_paiement,"paiement");
                    if(strtotime($date_debut) >= strtotime($date_day)) {
                        if(count($dataPaiement) && $montant > 0 && $type_select){
    
                            $accompteExists = isset($accompte) && $accompte !== "" && $accompte != 0;
    
                            $echeanciersWithacc = $echeanciers;

                            // Adjust $echeanciers if $accompte exists
                            if ($accompteExists) {
                                $echeanciersWithacc += 1;
                            }
                        
    
                            if($echeanciersWithacc == count($dataPaiement)){
                                //if($creance["total_restant"] > 0 && ($creance["total_restant"] >= $montant_a_Payer)){
                                if($creance["total_restant"] > 0 ){
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
                                            "id_status_id"=>$etatNumeric,
                                            "date_premier_paiement"=>$date_debut,
                                            "date_fin_paiement"=>$lastElementPaiment["date"],
                                            "date_creation"=>"now()",
                                            "montant"=>$montant,
                                            "nbr_echeanciers"=>$echeanciers,
                                            "montant_a_payer"=>$montant_a_Payer,
                                            "frequence" => $frenquence,
                                            "montant_de_base" => $montant_de_base,
                                            "fee_admin" => $fee_admin,
                                            "fee_installment" => $fee_installment,
                                            "interets" => $interets,
                                            "remise" => $remise,
                                            "accompte" => $accompte,
                                            "etat" => $type,
                                            "id_debiteur_id" => $id_debiteur_id
                                        ];
                                        if(isset($id_payeur_id) && $id_payeur_id != 0 && $id_payeur_id != '' && $id_payeur_id != null) 
                                        {
                                            $dataAccord["id_payeur_id"] = $id_payeur_id;
                                        }
                                        $createAccord = $creancesRepo->CreateAccord($dataAccord);
                                        $createAccord = $creancesRepo->createCreanceAccord(['id_accord_id'=>$createAccord , 'id_creance_id'=>$id,'montant_accord'=>$montant_de_base ]);

                                        if($etatNumeric == 0 && $type!= 3)
                                        {
                                            $dataUpdateCreance = [
                                                "total_restant"=> $creance["total_restant"]-$montant_de_base,
                                            ];
                                            $updateCre = $creancesRepo->updateCreance($dataUpdateCreance,$creance["id"]);

                                        }

                                        if($etatNumeric == 9)
                                        {
                                            $idUser = $this->AuthService->returnUserId($request);
                                            $time = date("H:i:s", strtotime("00:00:00"));
                                            $task = $creancesRepo->addTask($creance, 12, $date_debut, $time, $idUser , $idUser , "Validation demande accord");

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
    
                                        if ($notes) {
                                            $dateCreation = new \DateTime();
                                            foreach ($notes as $noteContent) {
                                                $dataNote = [
                                                    'idAccord' => $createAccord, // Pass the Accord entity or its ID
                                                    'note' => $noteContent["content"],
                                                    'dateNote' => null, // Optional, if available
                                                    'dateCreation' => $dateCreation,
                                                ];
                                                $codeStatut="OK";
    
                                                $accordNotesRepo->createAccordNote($dataNote);
                                            }
                                        }
    
                                        foreach ($attachments as $index => $attachment) {
                                            $file = $attachment['file'] ?? null;
                                            $name = $dataAttachments[$index]['name'] ?? 'Fichier'.$index; // Default name if not provided
    
                                            if ($file) {
                                                // Example: Save file to a directory
                                                $fileContent = file_get_contents($file->getPathname());
                                                $fileBase64 = base64_encode($fileContent);
                                                $mimeType = mime_content_type($file->getPathname());
                                
                                                // Create the Base64 Data URI
                                                $dataUri = "data:$mimeType;base64," . $fileBase64;
                                                $accordPjRepository->saveFileBase64($createAccord, $dataUri,$name);
    
                                            }
                                        }
    
                                        if($promesse != 0)
                                        {
                                            $dataDetailsAccord = [
                                                "id_accord_id"=>$createAccord,
                                                "id_status_id"=>2,
                                            ];
                                            $updatePromesse = $creancesRepo->updatePromise($dataDetailsAccord,$promesse );

                                        }
    
                                        $codeStatut="OK";
    
                                    }
                                    else
                                    {
                                        $codeStatut = "ERROR_CREANCE";
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
                
                   

            }
            else if(isset($list_creance) && (count($list_creance) > 0)){
                $list_creance = $list_creance;
                // $hasStatutOneOrZero = false;
                // foreach ($list_creance as $l) {
                //     $accords = $creancesRepo->getAccords($l['id']);

                //     // Initialize flags to check if any accord has getIdStatut() = 1 or 0
    
                //     // Iterate through the list of accords
                //     foreach ($accords as $item) {

                //         $idStatut = $item['id_status_id']; // Assuming getIdStatut() is a method on the accord object
    
                //         if ($idStatut == 1 || $idStatut == 0) {
                //             $hasStatutOneOrZero = true;
                //             break; // Exit the loop once we find a match
                //         }
                //     }
    
                // }

               
                    $type_select = $this->TypeService->checkType($type_paiement,"paiement");

                    if(strtotime($date_debut) >= strtotime($date_day)) {
                        if(count($dataPaiement) && $montant > 0 && $type_select){
    
                            $accompteExists = isset($accompte) && $accompte !== "" && $accompte != 0;
    
                            $echeanciersWithacc = $echeanciers;
                            // Adjust $echeanciers if $accompte exists
                            if ($accompteExists) {
                                $echeanciersWithacc += 1;
                            }
                            
                            if($echeanciersWithacc == count($dataPaiement)){
                                
                                $total_restant = 0;
                                foreach ($list_creance as $l) {
                                    $total_restant =  $total_restant + $l["total_restant"];
                                }

                            
                                if($montant_de_base> $total_restant)
                                {
                                    $codeStatut="ERROR_ACCORD_RESTANT2";
                                }
                                else
                                {
                                    // if($creance["total_restant"] > 0 && ($creance["total_restant"] >= $montant_a_Payer)){
                                        $lastElementPaiment = end($dataPaiement);
        
                                        $dataAccord = [
                                            "id_users_id"=>$this->AuthService->returnUserId($request),
                                            "id_type_paiement_id"=>$type_paiement,
                                            "id_status_id"=>$etatNumeric,
                                            "date_premier_paiement"=>$date_debut,
                                            "date_fin_paiement"=>$lastElementPaiment["date"],
                                            "date_creation"=>"now()",
                                            "montant"=>$montant,
                                            "nbr_echeanciers"=>$echeanciers,
                                            "montant_a_payer"=>$montant_a_Payer,
                                            "frequence" => $frenquence,
                                            "montant_de_base" => $montant_de_base,
                                            "fee_admin" => $fee_admin,
                                            "fee_installment" => $fee_installment,
                                            "interets" => $interets,
                                            "remise" => $remise,
                                            "accompte" => $accompte,
                                            "etat" => $type,
                                            "id_debiteur_id" => $id_debiteur_id

                                        ];

                                        if(isset($id_payeur_id) && $id_payeur_id != 0 && $id_payeur_id != '' && $id_payeur_id != null) 
                                        {
                                            $dataAccord["id_payeur_id"] = $id_payeur_id;
                                        }


                                        $createAccord = $creancesRepo->CreateAccord($dataAccord);
                                        if($etatNumeric == 0 && $type!= 3)
                                        {

                                            usort($list_creance, function ($a, $b) {
                                                return $a["total_restant"] <=> $b["total_restant"];
                                            });
                                            
                                            // Process each creance
                                            foreach ($list_creance as &$creance) {
                                                if ($montant_de_base <= 0) {
                                                    break; // Stop if there's no amount left to pay
                                                }
                                            
                                                // Determine the amount to pay for this creance
                                                $amountToPay = min($montant_de_base, $creance["total_restant"]);
                                            
                                                // Update total_restant for this creance
                                                $creance["total_restant"] -= $amountToPay;
                                            
                                                // Update the creance in the database
                                                $dataUpdateCreance = [
                                                    "total_restant" => $creance["total_restant"],
                                                ];
                                                $creancesRepo->updateCreance($dataUpdateCreance, $creance["id"]);
                                            
                                                // Deduct the paid amount from montant_de_base
                                                $montant_de_base -= $amountToPay;

                                                $createAccord = $creancesRepo->createCreanceAccord(['id_accord_id'=>$createAccord , 'id_creance_id'=>$l["id"],'montant_accord'=>$amountToPay ]);

                                            }
                                            
    
                                        }


                                        if($etatNumeric == 9)
                                        {
                                            $idUser = $this->AuthService->returnUserId($request);
                                            $time = date("H:i:s", strtotime("00:00:00"));
                                            foreach ($list_creance as &$creance) {
                                                $c = $creancesRepo->getCreance($creance["id"]);
                                                $paramA = $creancesRepo->getParamActivity(12);
                                                $dateEcheance = new \DateTime($date_debut);

                                                // Ensure $time is appended if it's not part of $date_debut
                                                if (!empty($time)) {
                                                    $dateEcheance->setTime(...explode(':', $time));
                                                }
                                                $temps = \DateTime::createFromFormat('H:i:s', $time);

                                                $task = $creancesRepo->addTask($c, $paramA, $dateEcheance, $temps, $idUser , $idUser , "Validation demande accord");

                                            }

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

                                        if ($notes) {
                                            $dateCreation = new \DateTime();
                                            foreach ($notes as $noteContent) {
                                                $dataNote = [
                                                    'idAccord' => $createAccord, // Pass the Accord entity or its ID
                                                    'note' => $noteContent["content"],
                                                    'dateNote' => null, // Optional, if available
                                                    'dateCreation' => $dateCreation,
                                                ];
                                                $codeStatut="OK";

                                                $accordNotesRepo->createAccordNote($dataNote);
                                            }
                                        }

                                        foreach ($attachments as $index => $attachment) {
                                            $file = $attachment['file'] ?? null;
                                            $name = $dataAttachments[$index]['name'] ?? 'Fichier'.$index; // Default name if not provided

                                            if ($file) {
                                                // Example: Save file to a directory
                                                $fileContent = file_get_contents($file->getPathname());
                                                $fileBase64 = base64_encode($fileContent);
                                                $mimeType = mime_content_type($file->getPathname());
                                
                                                // Create the Base64 Data URI
                                                $dataUri = "data:$mimeType;base64," . $fileBase64;
                                                $accordPjRepository->saveFileBase64($createAccord, $dataUri,$name);

                                            }
                                        }
                                        
                                        if($promesse != 0)
                                        {
                                            $dataDetailsAccord = [
                                                "id_accord_id"=>$createAccord,
                                                "id_status_id"=>2,
                                            ];
                                            $updatePromesse = $creancesRepo->updatePromise($dataDetailsAccord,$promesse);

                                        }
                                        

                                        $codeStatut="OK";

                                    
                                // }else{
                                //     $codeStatut = "ERROR_CREANCE";
                                // }


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
            else{
                $codeStatut = "NOT_EXIST_ELEMENT";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        $respObjects["accord"] = $createAccord;
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
            $idtype = $data['type'];
            $email_id = isset($data['email']) ? $data['email'] : null;
            $telephone_id = isset($data['telephone']) ? $data['telephone'] : null;
            $adresse_id = isset($data['adresse']) ? $data['adresse'] : null;
            $activite = $data['activite'];

            if (isset($data['template']) && $data['template'] !== null) {

                $template = $data['template'];
                $email_id = isset($data['emails']) ? $data['emails'] : null;
                $telephone_id = isset($data['telephones']) ? $data['telephones'] : null;
                $adresse_id = isset($data['adresses']) ? $data['adresses'] : null;
    
            }
            else
            {

                $debiteur_id = $data['debiteur'];
                $personne_id = $data['personne'];
                $debiteur = $creancesRepo->getOneDebiteur($debiteur_id);
                $personne = $creancesRepo->getOnePersonne($personne_id);
                $email_id = isset($data['email']) ? $data['email'] : null;
                $telephone_id = isset($data['telephone']) ? $data['telephone'] : null;
                $adresse_id = isset($data['adresse']) ? $data['adresse'] : null;
    
            }




            $qualification = $creancesRepo->getParamActivity($idQualification);
            $type = $creancesRepo->getParamActivity($idtype);
            $creance = $creancesRepo->getCreance($idCreance);



            if($idQualification){
                

                if (isset($data['template']) && $data['template'] !== null) {

                    if($template == "Courrier")
                    {
                        foreach($adresse_id as $a)
                        {
                            $adresse = $creancesRepo->getOneAdresse($a);
                            $email = null;
                            $telephone = null;
                            $debiteur = $creancesRepo->getOneDebiteur($a["id_debiteur_id"]);
                            $personne = null;
                            $idUser = $this->AuthService->returnUserId($request);
                            $activity = $creancesRepo->addActivity($creance, $qualification, $assigned , $idUser , $comment, $type, $debiteur ,$personne, $email, $telephone, $adresse, $activite);
                
                            if($assigned == 1){
                                $assignedActivity = $creancesRepo->addAssignedActivity($activity , $idUser);
                            }else if ($assigned == 2){
                                $assignedTo = $data['assignedTo'];
                                $assignedActivity = $creancesRepo->addAssignedActivity($activity , $assignedTo);
                            }
                        }
                    }
                    else if($template == "Email")
                    {
                        foreach($email_id as $e)
                        {
                            $email = $creancesRepo->getOneEmail($e);
                            $adresse = null;
                            $telephone = null;
                            $debiteur = $creancesRepo->getOneDebiteur($e["id_debiteur_id"]);
                            $personne = null;
                            $idUser = $this->AuthService->returnUserId($request);
                            $activity = $creancesRepo->addActivity($creance, $qualification, $assigned , $idUser , $comment, $type, $debiteur ,$personne, $email, $telephone, $adresse, $activite);
                
                            if($assigned == 1){
                                $assignedActivity = $creancesRepo->addAssignedActivity($activity , $idUser);
                            }else if ($assigned == 2){
                                $assignedTo = $data['assignedTo'];
                                $assignedActivity = $creancesRepo->addAssignedActivity($activity , $assignedTo);
                            }
                        }
                    }
                    else
                    {
                        foreach($telephone_id as $t)
                        {
                            $telephone = $creancesRepo->getOneTelephone($t);
                            $adresse = null;
                            $email = null;
                            $debiteur = $creancesRepo->getOneDebiteur($t["id_debiteur_id"]);
                            $personne = null;
                            $idUser = $this->AuthService->returnUserId($request);
                            $activity = $creancesRepo->addActivity($creance, $qualification, $assigned , $idUser , $comment, $type, $debiteur ,$personne, $email, $telephone, $adresse, $activite);
                
                            if($assigned == 1){
                                $assignedActivity = $creancesRepo->addAssignedActivity($activity , $idUser);
                            }else if ($assigned == 2){
                                $assignedTo = $data['assignedTo'];
                                $assignedActivity = $creancesRepo->addAssignedActivity($activity , $assignedTo);
                            }
                        }

                    }
                }
                else
                {
                    $adresse = $creancesRepo->getOneAdresse($adresse_id);
                    $email = $creancesRepo->getOneEmail($email_id);
                    $telephone = $creancesRepo->getOneTelephone($telephone_id);
        
                    $idUser = $this->AuthService->returnUserId($request);
                    $activity = $creancesRepo->addActivity($creance, $qualification, $assigned , $idUser , $comment, $type, $debiteur ,$personne, $email, $telephone, $adresse, $activite);
        
                    if($assigned == 1){
                        $assignedActivity = $creancesRepo->addAssignedActivity($activity , $idUser);
                    }else if ($assigned == 2){
                        $assignedTo = $data['assignedTo'];
                        $assignedActivity = $creancesRepo->addAssignedActivity($activity , $assignedTo);
                    }
    
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
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $id_creance =  $data["id_creance"];
            $valueDate = $data['valueDate'];
            $montant = $data['montant'];
            $idTypePaiement = $data['idTypePaiement'];
            $id_user = $this->AuthService->returnUserId($request);
            $commentaire = $data['commentaire'];

            
            $creance = $creancesRepo->addPaiement($paiementRepo,$id_creance , $valueDate,$montant,  $idTypePaiement,$id_user , $commentaire);
            $codeStatut = "OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getAccordByDossier')]
    public function getAccordByDossier(Request $request,creancesRepo $creancesRepo): JsonResponse
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
                $idAccord = null;

                $d['id_type_paiement_id'] = 1;
                $d['id_status_id'] = 0;
                $d['etat'] = 1;
                $d['date_creation'] ="now()";
                $d["id_users_id"] = $this->AuthService->returnUserId($request);
                $d['montant_a_payer'] =$data['montant_a_Payer'];
                $d['fee_admin'] =$data['feeAdmin'];
                $d['fee_installment'] =$data['feeInstallment'];
                $d['id_debiteur_id'] =$data['debiteur'];
                $d['montant_de_base'] =$data['montant_de_base'];

                if(isset($data['payeur']) && $data['payeur'] !== 0 && $data['payeur'] !== '')
                {
                    $d['id_payeur_id'] = $data['payeur'] ;

                }

                $firstDate = null;
                $lastDate = null;
                $totalAmount = 0;
                $totalInstalments = 0;
                $phaseCount = count($data['phaseList']);

                foreach ($data['phaseList'] as $phase) {
                    // Determine the earliest firstDateInstalement
                    if ($firstDate === null || new \DateTime($phase['firstDateInstalement']) < new \DateTime($firstDate)) {
                        $firstDate = $phase['firstDateInstalement'];
                    }

                    // Determine the latest lastDateInstalement
                    if ($lastDate === null || new \DateTime($phase['lastDateInstalement']) > new \DateTime($lastDate)) {
                        $lastDate = $phase['lastDateInstalement'];
                    }

                    // Accumulate the total amount and total instalments
                    $totalAmount += $phase['instalmentAmount'];
                    $totalInstalments += $phase['instalment'];
                }

                // Calculate the average of instalmentAmount
                $averageAmount = $phaseCount > 0 ? $totalAmount / $phaseCount : 0;

                // Assign the calculated values to the accord data
                $d['date_premier_paiement'] = $firstDate;
                $d['date_fin_paiement'] = $lastDate;
                $d['montant'] = $averageAmount;
                $d['nbr_echeanciers'] = $totalInstalments;


                $createAccord = $creancesRepo->createAccord($d);
                
                $idAccord = $createAccord;
                if (isset($data["list_creance"]) && !empty($data["list_creance"])) {
                    for ($j=0; $j < count($data['list_creance']); $j++) { 
                        $creancesRepo->createCreanceAccord(['id_creance_id'=>$data["list_creance"][$j]['id'] ,'id_accord_id'=>$createAccord]);
                    }

                    usort($data["list_creance"], function ($a, $b) {
                        return $a["total_restant"] <=> $b["total_restant"];
                    });
                    
                    // Process each creance
                    foreach ($data["list_creance"] as &$creance) {
                        if ($data['montant_de_base'] <= 0) {
                            break; // Stop if there's no amount left to pay
                        }
                    
                        // Determine the amount to pay for this creance
                        $amountToPay = min($data['montant_de_base'], $creance["total_restant"]);
                    
                        // Update total_restant for this creance
                        $creance["total_restant"] -= $amountToPay;
                    
                        // Update the creance in the database
                        $dataUpdateCreance = [
                            "total_restant" => $creance["total_restant"],
                        ];
                        $creancesRepo->updateCreance($dataUpdateCreance, $creance["id"]);
                    
                        // Deduct the paid amount from montant_de_base
                        $data['montant_de_base'] -= $amountToPay;

                        $createAccord = $creancesRepo->createCreanceAccord(['id_accord_id'=>$createAccord , 'id_creance_id'=>$creance["id"],'montant_accord'=>$amountToPay ]);

                    }
                }
                else
                {

                    $creancesRepo->createCreanceAccord(['id_creance_id'=>$data["creance"]['id'] ,'id_accord_id'=>$createAccord,'montant_accord'=>$data['montant_de_base'] ]);

                    $dataUpdateCreance = [
                        "total_restant"=> $data['creance']["total_restant"]-$data['montant_de_base'],
                    ];
                    $updateCre = $creancesRepo->updateCreance($dataUpdateCreance,$data['creance']["id"]);

                    
                }

                for ($i=0; $i < count($data['phaseList']) ; $i++) { 

                    $firstDate = new \DateTime($data['phaseList'][$i]['firstDateInstalement']);
                    $lastDate = new \DateTime($data['phaseList'][$i]['lastDateInstalement']);
                    $intervalDays = (int)$lastDate->diff($firstDate)->days / $data['phaseList'][$i]['instalment'];
                    for ($o=0; $o < $data['phaseList'][$i]['instalment']; $o++) { 
                        $paymentDate = clone $firstDate; // Clone the start date to avoid modifying the original
                        $paymentDate->modify("+".($intervalDays * $o)." days");

                        $dataDetailsAccord = [
                            "id_accord_id"=>$createAccord,
                            "montant"=>$data['phaseList'][$i]['instalmentAmount'] / $data['phaseList'][$i]['instalment'],
                            "id_status_id"=>0,
                            "id_user_id"=>$this->AuthService->returnUserId($request),
                            "date_prev_paiement"=>$paymentDate->format('Y-m-d'),
                            "montant_restant"=>$data['phaseList'][$i]['instalmentAmount'] / $data['phaseList'][$i]['instalment'],
                            "montant_paiement"=>0,
                            "id_type_paiement_id"=>1,
                        ];
                        $createDt = $creancesRepo->createDetailsAccord($dataDetailsAccord);
                    }
                }

                if($data['promesse'] != 0)
                {

                    $dataDetailsAccord = [
                        "id_accord_id"=>$createAccord,
                        "id_status_id"=>2,
                    ];
                    $updatePromesse = $creancesRepo->updatePromise($dataDetailsAccord,$data['promesse']);

                }

            }else{
                $codeStatut="EMPTY_DATA";
            }

            

        } catch (\Exception $e) {
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }

         
        $respObjects["accord"] = $idAccord;
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
        try {
            $this->AuthService->checkAuth(0, $request);
            $id = $request->get('id');
        
            $typePaiement = $this->TypeService->getListeType("promise");
            $creance = $creancesRepo->getOneCreance($id);
            $idPtf = $creance['id_ptf_id'];
            $getReglePtf = $creancesRepo->getReglePtf($idPtf);
            $accords = $creancesRepo->getAccords($id);
        
            // Fetch details for each accord
            foreach ($accords as &$accord) { // Use reference (&) to modify the original array
                $accordId = $accord['id']; // Assuming `id` is the unique identifier for each accord
                $accord['details'] = $creancesRepo->getDetailsAccord($accordId); // Append details to the accord
            }
        
            $respObjects['regle'] = $getReglePtf;
            $respObjects['data'] = $typePaiement;
            $respObjects['creance'] = $creance;
            $respObjects["accord"] = $accords;
        
            $codeStatut = "OK";
        } catch (\Exception $e) {
            $codeStatut = "ERROR";
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

            $regleMontant = isset($data['regleMontant']) ? $data['regleMontant'] : false;
            $regleDate = isset($data['regleDate']) ? $data['regleDate'] : false;
            
            if(is_numeric($montant) && !empty($date)){

                $creance = $creancesRepo->getOneCreance($idCreance);
                if($creance)
                {
                    $promises = $creancesRepo->getListPromise($idCreance);
                    $hasEtatOne = false;

                    // Iterate over the promises list
                    foreach ($promises as $promise) {
                        if ($promise->getIdStatus()->getId() == 1) {
                            $hasEtatOne = true;
                            break; // Exit loop once we find the match
                        }
                    }

                    if ($hasEtatOne) {

                        $codeStatut="PROMISE-ACTIF";

                    }
                    else
                    {
                        $checkCondition = true;
                        if ($regleMontant) {
                            if ($regleMontant['typeColumn'] == "Montant") {
                                switch ($regleMontant['action']) {
                                    case 'egal':
                                        $checkCondition = ($montant == $regleMontant['value1']);
                                        break;
                                    case 'supOuEgal':
                                        $checkCondition = ($montant >= $regleMontant['value1']);
                                        break;
                                    case 'infOuEgal':
                                        $checkCondition = ($montant <= $regleMontant['value1']);
                                        break;
                                    case 'entre':
                                        $checkCondition = ($montant >= $regleMontant['value1'] && $montant <= $regleMontant['value2']);
                                        break;
                                }
                            }
                            if ($regleMontant['typeColumn'] == "Pourcentage") {
                                switch ($regleMontant['action']) {
                                    case 'supOuEgal':
                                        $percentageValue = $creance['total_restant'] * ($regleMontant['value1'] / 100);
                                        $checkCondition = ($montant >= $percentageValue);
                                        break;
                                    case 'infOuEgal':
                                        $percentageValue = $creance['total_restant'] * ($regleMontant['value1'] / 100);
                                        $checkCondition = ($montant <= $percentageValue);
                                        break;
                                }
                            }
                        }
                        
                        // Date Condition Check
                        if ($regleDate && $checkCondition) {
                            $inputDate = DateTime::createFromFormat('d/m/Y', $date);
                            
                            if ($regleDate['typeColumn'] == "Date agenda") {
                                $ruleDate = new DateTime($regleDate['value1']);
                                
                                switch ($regleDate['action']) {
                                    case 'egal':
                                        $checkCondition = ($inputDate == $ruleDate);
                                        break;
                                    case 'entre':
                                        $startDate = new DateTime($regleDate['value1']);
                                        $endDate = new DateTime($regleDate['value2']);
                                        $checkCondition = ($inputDate >= $startDate && $inputDate <= $endDate);
                                        break;
                                    case 'supOuEgal':
                                        $checkCondition = ($inputDate >= $ruleDate);
                                        break;
                                    case 'infOuEgal':
                                        $checkCondition = ($inputDate <= $ruleDate);
                                        break;
                                }
                            } else {
                                $currentDate = new DateTime();
                                $durationInDays = $inputDate->diff($currentDate)->days;
                        
                                switch ($regleDate['action']) {
                                    case 'supOuEgal':
                                        $checkCondition = ($durationInDays >= $regleDate['value1']);
                                        break;
                                    case 'infOuEgal':
                                        $checkCondition = ($durationInDays <= $regleDate['value1']);
                                        break;
                                }
                            }
                        }
                                            
    
                        if($checkCondition == true){
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
                    }
                  
    

                }
                else
                {
                    $codeStatut = 'NOT_EXIST_CREANCE';
                }
                


            }else{
                $codeStatut="EMPTY-PARAMS";
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

            $regleMontant = $data['regleMontant'] ;
            $regleDate = $data['regleDate'] ;

            if(is_numeric($montant) && !empty($date)){
                $promise = $creancesRepo->getPromise($id);
                if($promise)
                {
                    $creance = $creancesRepo->getOneCreance($promise->getIdCreance()->getId());
                    if($creance)
                    {
                        $checkCondition = true;
                        if ($regleMontant) {
                            if ($regleMontant['typeColumn'] == "Montant") {
                                switch ($regleMontant['action']) {
                                    case 'egal':
                                        $checkCondition = ($montant == $regleMontant['value1']);
                                        break;
                                    case 'supOuEgal':
                                        $checkCondition = ($montant >= $regleMontant['value1']);
                                        break;
                                    case 'infOuEgal':
                                        $checkCondition = ($montant <= $regleMontant['value1']);
                                        break;
                                    case 'entre':
                                        $checkCondition = ($montant >= $regleMontant['value1'] && $montant <= $regleMontant['value2']);
                                        break;
                                }
                            }
                            if ($regleMontant['typeColumn'] == "Pourcentage") {
                                switch ($regleMontant['action']) {
                                    case 'supOuEgal':
                                        $percentageValue = $creance['total_restant'] * ($regleMontant['value1'] / 100);
                                        $checkCondition = ($montant >= $percentageValue);
                                        break;
                                    case 'infOuEgal':
                                        $percentageValue = $creance['total_restant'] * ($regleMontant['value1'] / 100);
                                        $checkCondition = ($montant <= $percentageValue);
                                        break;
                                }
                            }
                        }
                        
                        // Date Condition Check
                        if ($regleDate && $checkCondition) {
                            $inputDate = DateTime::createFromFormat('d/m/Y', $date);
                            
                            if ($regleDate['typeColumn'] == "Date agenda") {
                                $ruleDate = new DateTime($regleDate['value1']);
                                
                                switch ($regleDate['action']) {
                                    case 'egal':
                                        $checkCondition = ($inputDate == $ruleDate);
                                        break;
                                    case 'entre':
                                        $startDate = new DateTime($regleDate['value1']);
                                        $endDate = new DateTime($regleDate['value2']);
                                        $checkCondition = ($inputDate >= $startDate && $inputDate <= $endDate);
                                        break;
                                    case 'supOuEgal':
                                        $checkCondition = ($inputDate >= $ruleDate);
                                        break;
                                    case 'infOuEgal':
                                        $checkCondition = ($inputDate <= $ruleDate);
                                        break;
                                }
                            } else {
                                $currentDate = new DateTime();
                                $durationInDays = $inputDate->diff($currentDate)->days;
                        
                                switch ($regleDate['action']) {
                                    case 'supOuEgal':
                                        $checkCondition = ($durationInDays >= $regleDate['value1']);
                                        break;
                                    case 'infOuEgal':
                                        $checkCondition = ($durationInDays <= $regleDate['value1']);
                                        break;
                                }
                            }
                        }
    
        
        
                        if($checkCondition){
                            $dateInsert = [
                                // 'id_type_id'=> $type,
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
                    }
                    else
                    {
                        $codeStatut = 'NOT_EXIST_CREANCE';
                    }
                   
                }
                else
                {
                    $codeStatut = "NOT_EXIST_PROMISE";
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
                $arrayData[$i]['user']= $data[$i]->getIdUser()->getId();
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
    public function addTask(Request $request, CreancesRepo $creancesRepo,
    UtilisateursRepository $utilisateursRepository,TeamsRepository $teamsRepository): JsonResponse
    {
        $respObjects = [];
        $codeStatut = "ERROR";

        try {
            $this->AuthService->checkAuth(0, $request);

            $data = json_decode($request->getContent(), true);
            $idQualification = $data['qualification'];
            $dateEcheance = new \DateTime($data['dateEcheance']);
            $assigned = isset($data['assigned']) ? $data['assigned'] : null;
            $assignedD = isset($data['assignedD']) ? $data['assignedD'] : null;
            $assignedU = isset($data['assignedTo']) ? $data['assignedTo'] : null;
            
            $idCreance = $data['idCreance'];
            $comment = $data['comment'];

            $time = \DateTime::createFromFormat('g:i A', $data['time']);

            $qualification = $creancesRepo->getParamActivity($idQualification);
            $creance = $creancesRepo->getCreance($idCreance);

            if($dateEcheance && $idQualification){
                $dep = null;
                if($assignedD){
                    $assignedD = $teamsRepository->find($assignedD); 
                    $dep = $assignedD->getIdDepartement();

                }
                if ($assignedU){
                    $assignedU = $utilisateursRepository->find($assignedU); 
                }
                $idUser = $this->AuthService->returnUserId($request);
                $task = $creancesRepo->addTaskWithAssigned($creance, $qualification, $dateEcheance, $time, $assigned , $idUser , $comment,$assignedU,$assignedD,$dep);
    
                /*if($assigned == 1){
                    $assignedTask = $creancesRepo->addAssignedTask($task , $idUser);
                }else if ($assigned == 2){
                    $assignedTo = $data['assignedTo'];
                    $assignedTask = $creancesRepo->addAssignedTask($task , $assignedTo);
                }*/
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
                $arrayData[$i]['activity']=  $data[$i]->getIdActivity() ? $data[$i]->getIdActivity()->getType() : null;
                $arrayData[$i]['date_echeance']= $data[$i]->getDateEcheance();
                $arrayData[$i]['temps']= $data[$i]->getTemps();
                $arrayData[$i]['assigned_type']= $data[$i]->getAssignedType();
                $arrayData[$i]['date_creation']= $data[$i]->getDateCreation();
                $arrayData[$i]['user']= $data[$i]->getCreatedBy() ? $data[$i]->getCreatedBy()->getNom() : null;
                $arrayData[$i]['assigned_user']= $data[$i]->getAssignedUser()  ? $data[$i]->getAssignedUser()->getNom() : null;
                $arrayData[$i]['id']= $data[$i]->getId();

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

            $arrayData['type'] = $data->getIdType() ? $data->getIdType()->getType() : null; // Adjust based on your entity
            $arrayData['montant'] = $data->getMontant();
            $arrayData['status'] = $data->getIdStatus() ? $data->getIdStatus()->getStatus() : null; // Adjust accordingly
            $arrayData['date'] = $data->getDate() ? $data->getDate()->format('Y-m-d') : null;
            $arrayData['commentaire'] = $data->getCommentaire();
            $arrayData['idCreance'] = $data->getIdCreance() ? $data->getIdCreance()->getId() : null;
            
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
    #[Route("/previewPdf/")]
    public function previewPdf(Request $request , creancesRepo $creancesRepo): JsonResponse
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        try {
            $data = json_decode($request->getContent(), true);
            $model = $data['templateSelected'] ?? null;
            $adresses = $data['adresseSelected'] ?? null;      

            $modele = $creancesRepo->getModelCourrier($model["id"]);
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
            $html = '';

            foreach ($adresses as $ad) {
                $adresse = $creancesRepo->getAdresse($ad["id"]);
            
                // HTML content for each address
                $html .= "<div style='font-family:dejavusans'>";
                $html .= $header;
                $html .= "<h1 class='title'>Object :" . htmlspecialchars($objet, ENT_QUOTES, 'UTF-8') . "</h1>";
                $html .= '<div style="text-align:right"><img src="profile_img/barcode.gif" /></div>';
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
            
                if ($background != "") {
                    $background = $this->em->getRepository(BackgroundCourrier::class)->find($background);
                    $background = $background->getUrl();
                    $html .= "<div style='position:relative;width:100%;min-height:1000px'>
                        <div style='position:absolute; top:0; bottom:0; left:0; right:0; z-index:-1;'>
                        <img src='".$background."' style='width:100%; height:100%; object-fit:cover;'>
                        </div>
                        <p>" . html_entity_decode($message) . "</p>
                    </div>";
                } else {
                    $html .= "<div style='position:relative;width:100%;min-height:1000px'>
                        <p>" . html_entity_decode($message) . "</p>
                    </div>";
                }
            
                $html .= "</div>";
                
                if($ad != end($adresses)){
                    $html .= "<div style='page-break-after: always;'></div>";
                }   
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
            $name = uniqid("message");
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

    #[Route('/deletePromise/{id}' )]
    public function deletePromise( $id, Request $request, creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            
            $promise = $creancesRepo->getPromise($id);
            if($promise)
            {
                $delete = $creancesRepo->deletePromise($id);
                $codeStatut="OK";

            }
            else
            {
                $codeStatut = "NOT_EXIST_PROMISE";
            }
            
            
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/deletePaiement/{id}' )]
    public function deletePaiement( $id, Request $request, creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            
            $codeStatut = $creancesRepo->deletePaiement($id);

         
            
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/deleteTask/{id}' )]
    public function deleteTask( $id, Request $request, creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            
            $codeStatut = $creancesRepo->deleteTask($id);

         
            
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }


    #[Route('/getBookmarks')]
    public function getBookmarks(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = $creancesRepo->getBookMarks($this->AuthService->returnUserId($request));
            $array = [];
            for ($i=0; $i < count($data); $i++) { 
                
                $array[$i] = $data[$i];
                $array[$i]["dn"] = $creancesRepo->getDonneurByPtf($data[$i]["id_ptf_id"]);
                $array[$i]["debiteur"] = $creancesRepo->getDebiteurByCreance($data[$i]["id"]);
                if (isset($array[$i]["debiteur"]) && isset($array[$i]["debiteur"]["id"])) {
                    $array[$i]["type_debiteur"] = $creancesRepo->getTypeDebiteur($data[$i]["id"], $array[$i]["debiteur"]["id"]);
                } else {
                    $array[$i]["type_debiteur"] = null; // Or handle it as needed
                }

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


    #[Route('/getRecentsCreance')]
    public function getRecentsCreance(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = $creancesRepo->getRecentsCreance($this->AuthService->returnUserId($request));
            
            $array = [];
            for ($i=0; $i < count($data); $i++) { 
                $array[$i] = $data[$i];
                $array[$i]["dn"] = $creancesRepo->getDonneurByPtf($data[$i]["id_ptf_id"]);
                $array[$i]["debiteur"] = $creancesRepo->getDebiteurByCreance($data[$i]["id"]);

                if (!empty($array[$i]["debiteur"])) {
                    $array[$i]["type_debiteur"] = $creancesRepo->getTypeDebiteur(
                        $data[$i]["id"], 
                        $array[$i]["debiteur"]["id"]
                    );
                } else {
                    $array[$i]["type_debiteur"] = null; // Or handle this case as needed
                }

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


    #[Route('/getAccord')]
    public function getAccord(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");

            $data = $creancesRepo->getAccordWithDetails($id);
            $typePaiement = $this->TypeService->getListeType("paiement");
            $respObjects["type_paiemennt"] = $typePaiement;
            $respObjects["data"] =$data;

            $codeStatut="OK";

        }
        catch(\Exception $e)
        {
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }


    #[Route('/saveDetailAccords',methods:"POST")]
    public function saveDetailAccords(Request $request,creancesRepo $creancesRepo, AccordNotesRepository $accordNotesRepo, AccordPjRepository  $accordPjRepository,
    AccordRepository $accordRepository): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);

            $id = $request->get("accord");

            if($id != "undefined"){
                $accord = $accordRepository->find($id);
            }

            if($accord)
            {
                $dataJson = $request->request->get('data');
                $dataPaiement = json_decode($dataJson, true);
                $notes = json_decode($request->request->get('notes'), true);
    
                // Access uploaded files
                $attachments = $request->files->all()['attachments'] ?? [];
                $dataAttachments = $request->request->all()['attachments'] ?? [];
    
                
                $totalP = 0;
                for ($i=0; $i < count($dataPaiement); $i++) { 
                    if($dataPaiement[$i]["id_status_id"] != 7 && $dataPaiement[$i]["id_status_id"] != 4)
                    {
                        $totalP = $totalP + $dataPaiement[$i]["montant"];
                    }
                }

                
                /*if($totalP == $accord->getMontantAPayer())
                {*/

                    $accordTotal = $accord->getMontantAPayer(); // Original total amount
                    $accordBase = $accord->getMontantDeBase(); // Original base amount
                    
                    
                    // Initialize fee, remise, and interest variables

                    $pRemise = 0;
                    $pInterets = 0;
                    
                    // Get total fees (admin + installment fees)
                    $Fees = $accord->getFeeAdmin() + $accord->getFeeInstallment();
                    
                    // Calculate remise as a percentage of the original total
                    $pRemise = ($accord->getRemise() / $accordTotal) * 100;
                    
                    // Get the interest percentage directly
                    $pInterets = $accord->getInterets();
                    
                    // Calculate the new remise and interest based on the new total ($totalP)
                    $newRemise = ($pRemise * $totalP) / 100;
                    $newInteret = ($pInterets * $totalP) / 100;
                    
                    // Calculate the new base amount (accordBase) using the adjusted values
                    $newAccordBase = $totalP - $Fees - $newInteret + $newRemise;

                    // Calculate the difference (though it isn't used in the final calculation)
                    $deff = $accordBase - $newAccordBase;
                    

                    $creances = $creancesRepo->getAccordCreances($id);
                    usort($creances, function ($a, $b) {
                        return $b->getDateCreation() <=> $a->getDateCreation(); // Descending order
                    });
                    
                    $totalRestantSum = 0;

                    // Loop through the creances and calculate the sum of 'total_restant'
                    foreach ($creances as $creance) {
                        // Assuming each $creance has a 'total_restant' property
                        $totalRestantSum += $creance['total_restant'];
                    }
                    
                    if($deff  < 0 && ($totalRestantSum - $deff) <0)
                    {
                        $codeStatut="CREANCE_RESTANT";
                    }
                    // else if ($deff  > 0 && ($newAccordBase > $totalRestantSum + $accordBase))
                    // {
                    //     $codeStatut="CREANCE_RESTANT2";

                    // }
                    else
                    {

                        // UPDATE TOTAL_RESTANT OF A CREANCE  
                        if ($deff > 0) {
                            foreach ($creances as &$creance) { // Use reference to modify array elements
                                $remainingCapacity = $creance['total_creance'] - $creance['total_restant'];
                                
                                if ($remainingCapacity > 0) {
                                    // Determine how much can be added to this creance
                                    $amountToAdd = min($deff, $remainingCapacity);
                                    $creance['total_restant'] += $amountToAdd; // Update the array
                                    
                                    $creanceUpdated = $this->em->getRepository(Creance::class)->find($creance['id']);
        
                                    if ($creanceUpdated) {
                                        // Update the 'total_restant' field
                                        $creanceUpdated->setTotalRestant($creance['total_restant']);
                                    }
                            
                                    // Decrease $deff by the amount added
                                    $deff -= $amountToAdd;
                                }
                                
                                // If $deff is fully distributed, stop processing further creances
                                if ($deff <= 0) {
                                    break;
                                }
                            }
                        } else if ($deff < 0) {
                            $deff = abs($deff); // Convert to positive for deduction
                            foreach ($creances as &$creance) { // Use reference to modify array elements
                                $currentRestant = $creance['total_restant'];
                                
                                if ($currentRestant > 0) {
                                    // Determine how much can be deducted from this creance
                                    $amountToDeduct = min($deff, $currentRestant);
                                    $creance['total_restant'] -= $amountToDeduct; // Update the array
                                    
                                    $creanceUpdated = $this->em->getRepository(Creance::class)->find($creance['id']);
        
                                    if ($creanceUpdated) {
                                        // Update the 'total_restant' field
                                        $creanceUpdated->setTotalRestant($creance['total_restant']);
                                    }

                                    // Decrease $deff by the amount deducted
                                    $deff -= $amountToDeduct;
                                }
                                
                                // If $deff is fully deducted, stop processing further creances
                                if ($deff <= 0) {
                                    break;
                                }
                            }
                        }
                        
                                      
                        $firstElementPaiement = reset($dataPaiement); // Get the first element
    
                        $lastElementPaiment = end($dataPaiement);
        
                        $dataAccord = [
                            "date_premier_paiement"=>$firstElementPaiement["date"],
                            "date_fin_paiement"=>$lastElementPaiment["date"],
                            "nbr_echeanciers"=>count($dataPaiement),
                            "remise"=>$newRemise,
                            "montant_a_payer"=>$totalP,
                            "montant_de_base"=>$newAccordBase,
                        ];
                        
                        $creancesRepo->UpdateAccordSave($id,$dataAccord);
    
                        $deleteDetails = $creancesRepo->deleteDetailsByAccordId($id);
    
    
                        for ($i=0; $i < count($dataPaiement); $i++) { 
                            $dataDetailsAccord = [
                                "id_accord_id"=>$id,
                                "montant"=>$dataPaiement[$i]["montant"],
                                "id_status_id"=>$dataPaiement[$i]["id_status_id"],
                                "id_user_id"=>$this->AuthService->returnUserId($request),
                                "date_prev_paiement"=>$dataPaiement[$i]["date"],
                                "montant_restant"=>$dataPaiement[$i]["montant"],
                                "montant_paiement"=>$dataPaiement[$i]["montant_paiement"],
                                "id_type_paiement_id"=>$dataPaiement[$i]["type_select"], 
                            ];
                            $createDt = $creancesRepo->createDetailsAccord($dataDetailsAccord);
                        }
        
                        $deleteNotes = $creancesRepo->deleteNotesByAccordId($id);
    
                        if ($notes) {
                            $dateCreation = new \DateTime();
                            foreach ($notes as $noteContent) {
                                $dataNote = [
                                    'idAccord' => $accord, // Pass the Accord entity or its ID
                                    'note' => $noteContent["content"],
                                    'dateNote' => null, // Optional, if available
                                    'dateCreation' => $dateCreation,
                                ];
                                $codeStatut="OK";
        
                                $accordNotesRepo->createAccordNote($dataNote);
                            }
                        }
        
                        $existingAttachments = $accordPjRepository->findBy(array("idAccord" => $id));  // Assuming this fetches all attachments related to the given accord ID
    
                        // Extract the names of the attachments in the current list
    
                        $currentNames = [];
                        foreach ($dataAttachments as $index => $attachment) {
                            $name = $dataAttachments[$index]['name'];
                            $currentNames[] = $name;
                        }
    
                        foreach ($attachments as $index => $attachment) {
                            $file = $attachment['file'] ?? null;
                            $name = $dataAttachments[$index]['name'] ?? 'Fichier'.$index; // Default name if not provided
        
                            if ($file) {
                                // Example: Save file to a directory
                                $fileContent = file_get_contents($file->getPathname());
                                $fileBase64 = base64_encode($fileContent);
                                $mimeType = mime_content_type($file->getPathname());
                
                                // Create the Base64 Data URI
                                $dataUri = "data:$mimeType;base64," . $fileBase64;
                                $accordPjRepository->saveFileBase64($id, $dataUri,$name);
        
                            }
                        }   
    
                        // Delete attachments from the database that are not in the current list
                        foreach ($existingAttachments as $existingAttachment) {
                            if (!in_array($existingAttachment->getNom(), $currentNames)) {
    
                                // If the attachment's name is not in the current list, delete it
                                $accordPjRepository->deleteAttachment($existingAttachment->getId());
                                
                            }
                        }
    
                        
                        $this->em->flush();

                        $codeStatut="OK";
    
                    }

    
                            
                // }else{
                //     $codeStatut="ERROR";
                // }
    
            }
        
                
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }


    #[Route('/updateEtatAccord',methods:"POST")]
    public function updateEtatAccord(Request $request,creancesRepo $creancesRepo, AccordNotesRepository $accordNotesRepo, AccordPjRepository  $accordPjRepository,
    AccordRepository $accordRepository): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);

            $id = $request->get("accord");
            $etat = $request->get("etat");
            $motif = $request->get("motif");
            $dossier = $request->get("dossier");

            if($id != "undefined"){
                $accord = $accordRepository->find($id);
            }

            if($accord)
            {

                $creances = $creancesRepo->getAccordCreances($id);
                usort($creances, function ($a, $b) {
                    return $b->getDateCreation() <=> $a->getDateCreation(); // Descending order
                });

                $montant_de_base = $accord->getMontantDeBase();
                
                if(($accord->getIdStatus()->getId() == 0 || $accord->getIdStatus()->getId() == 5) && ($etat == 6 || $etat == 4))
                {
                    foreach ($creances as &$creance) { // Use reference to modify array elements
                        $remainingCapacity = $creance['total_creance'] - $creance['total_restant'];
                        
                        if ($remainingCapacity > 0) {
                            // Determine how much can be added to this creance
                            $amountToAdd = min($montant_de_base, $remainingCapacity);
                            $creance['total_restant'] += $amountToAdd; // Update the array
                            
                            $creanceUpdated = $this->em->getRepository(Creance::class)->find($creance['id']);

                            if ($creanceUpdated) {
                                // Update the 'total_restant' field
                                $creanceUpdated->setTotalRestant($creance['total_restant']);
                            }
                    
                            // Decrease $deff by the amount added
                            $montant_de_base -= $amountToAdd;
                        }
                        
                        // If $deff is fully distributed, stop processing further creances
                        if ($montant_de_base <= 0) {
                            break;
                        }
                    }
                }

                if(($accord->getIdStatus()->getId() == 6 || $accord->getIdStatus()->getId() == 4 || $accord->getIdStatus()->getId() == 9 || $accord->getIdStatus()->getId() == 7) && ($etat == 5 || $etat == 0))
                {
                    $deff = abs($montant_de_base); // Convert to positive for deduction
                    foreach ($creances as &$creance) { // Use reference to modify array elements
                        $currentRestant = $creance['total_restant'];
                        
                        if ($currentRestant > 0) {
                            // Determine how much can be deducted from this creance
                            $amountToDeduct = min($deff, $currentRestant);
                            $creance['total_restant'] -= $amountToDeduct; // Update the array
                            
                            $creanceUpdated = $this->em->getRepository(Creance::class)->find($creance['id']);

                            if ($creanceUpdated) {
                                // Update the 'total_restant' field
                                $creanceUpdated->setTotalRestant($creance['total_restant']);
                            }

                            // Decrease $deff by the amount deducted
                            $deff -= $amountToDeduct;
                        }
                        
                        // If $deff is fully deducted, stop processing further creances
                        if ($deff <= 0) {
                            break;
                        }
                    }
                }

                //$Status = $this->em->getRepository(StatusAccord::class)->find($etat);


                $dataNote = [
                    'id_status_id' => $etat, // Pass the Accord entity or its ID
                    'motif' => $motif, // Optional, if available
                ];
                $creancesRepo->UpdateAccordEtat($id,$dataNote);

                $this->em->flush();

                $codeStatut="OK";


                
            }
        
                
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getLine();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    

    #[Route('/getActivite')]
    public function getActivite(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");

            $data = $creancesRepo->getOneActivite($id);
            if ($data) {
                $dataArray = [
                    'id' => $data->getId(),
                    'id_creance' => $data->getIdCreance() ? $data->getIdCreance()->getId() : null,
                    'param_activite' => $data->getIdParamActivite() ? $data->getIdParamActivite()->getType() : null,
                    'date_creation' => $data->getDateCreation() ? $data->getDateCreation()->format('Y-m-d H:i:s') : null,
                    'user' => $data->getCreatedBy() ? $data->getCreatedBy()->getNom() . ' ' . $data->getCreatedBy()->getPrenom() : null,  // Concatenating Nom and Prenom
                    'commentaire' => $data->getCommentaire(),
                    'param_parent' => $data->getIdParamParent() ? $data->getIdParamParent()->getType() : null
                ];
            };

            $respObjects["data"] =$dataArray;

            $codeStatut="OK";

        }
        catch(\Exception $e)
        {
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getEquipes',methods:"GET")]
    public function getEquipes(Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);

            $equipes = $creancesRepo->getEquipes();
            if($equipes){
                $array = [];
                for ($i=0; $i < count($equipes); $i++) { 
                    $array[$i]["id"] = $equipes[$i]->getId();
                    $array[$i]["team"] = $equipes[$i]->getTeam();
                }
                $respObjects["data"] =$array;
    
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

    #[Route('/getCalendrierTache',methods:"GET")]
    public function getCalendrier(Request $request,creancesRepo $creancesRepo,UtilisateursRepository $utilisateursRepository): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);


            $id = $request->get("id");
            $from = $request->get("from");
            
            if($from == 1)
            {
                $data = $creancesRepo->getCalendrierTache($id);
            }
            if($from == 2)
            {

                $user = $utilisateursRepository->find($id);
                $team = $user->getTeams();

                $data = $creancesRepo->getCalendrierTacheByTeam($team->getId());
            }
            if($from == 3)
            {

                $user = $utilisateursRepository->find($id);
                $team = $user->getTeams();
                $departement = $team->getIdDepartement();
                $data = $creancesRepo->getCalendrierTacheByDepartement($departement->getId());
            }

            $respObjects["data"] = $data;
            $codeStatut = "OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getCalendrierProduction',methods:"GET")]
    public function getCalendrierProduction(Request $request,creancesRepo $creancesRepo,
    UtilisateursRepository $utilisateursRepository, TeamsRepository $teamsRepository): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);


            $id = $request->get("id");
            $from = $request->get("from");
            
            if($from == 1)
            {
                $data = $creancesRepo->getProductionByUser($id);
            }
            if($from == 2)
            {
                $user = $utilisateursRepository->find($id);
                $team = $user->getTeams();
                $allUsers = $utilisateursRepository->findBy(['teams' => $team]);
                $ids = [];
                foreach ($allUsers as $user) {
                    $ids[] = $user->getId();
                }
                $data = $creancesRepo->getProductionByUsers($ids);
            }
            if($from == 3)
            {
                $user = $utilisateursRepository->find($id);
                $departement = $user->getTeams()->getIdDepartement();

                $allTeams = $teamsRepository->findBy(['idDepartement' => $departement]);
                $ids = [];
                foreach ($allTeams as $team) {
                    $allUsers = $utilisateursRepository->findBy(['teams' => $team]);
                    foreach ($allUsers as $user) {
                        $ids[] = $user->getId();
                    }
                }

                $data = $creancesRepo->getProductionByUsers($ids);
            }

            $respObjects["data"] = $data;
            $codeStatut = "OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }


    #[Route('/getCalendrierTacheByEquipe',methods:"GET")]
    public function getCalendrierByEquipe(Request $request,creancesRepo $creancesRepo,UtilisateursRepository $utilisateursRepository): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);


            $id = $request->get("id");
            
            if($id != "undefined"){

                $data = $creancesRepo->getCalendrierTacheByTeam($id);
            }
           

            $respObjects["data"] = $data;
            $codeStatut = "OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getCalendrierProductionByEquipe',methods:"GET")]
    public function getCalendrierProductionByEquipe(Request $request,creancesRepo $creancesRepo,
    UtilisateursRepository $utilisateursRepository, TeamsRepository $teamsRepository): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);


            $id = $request->get("id");
            
            if($id != "undefined"){
                $allUsers = $utilisateursRepository->findBy(['teams' => $id]);
                $ids = [];
                foreach ($allUsers as $user) {
                    $ids[] = $user->getId();
                }
                $data = $creancesRepo->getProductionByUsers($ids);
            }

            $respObjects["data"] = $data;
            $codeStatut = "OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
}


