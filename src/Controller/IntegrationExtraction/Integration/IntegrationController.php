<?php

namespace App\Controller\IntegrationExtraction\Integration;
use App\Entity\ActionsImport;
USE App\Entity\Customer\actionsImportDbi;
use App\Entity\CorresColu;
use App\Entity\Debiteur;
use App\Entity\DetailsImport;
use App\Entity\Import;
use App\Entity\ImportTypeCreance;
use App\Entity\ImportTypeDebiteur;
use App\Entity\ImportTypeGarantie;
use App\Entity\DetailModelAffichage;
use App\Entity\IntegDebiteur;
use App\Entity\IntegDossier;
use App\Entity\Integration;
use App\Entity\ImportDonneurOrdreBack;
use App\Entity\ModelImport;
use App\Entity\ColumnsParams;
use App\Entity\TypeDebiteur;
use App\Entity\TypeTel;
use App\Entity\Utilisateurs;
use App\Repository\DonneurOrdreAndPTF\donneurRepo;
use App\Repository\IntegrationExtraction\Integration\integrationRepo;
use App\Repository\Parametrages\affichages\affichageRepo;
use App\Service\GeneralService;
use App\Service\MessageService;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\TypeAdresse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\DBAL\Connection;
use App\Service\AuthService;
use App\Entity\ImportType;
use App\Entity\ProcessIntegration;
use Doctrine\Persistence\ManagerRegistry;
use GenderApi\Client as GenderApiClient;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use ZipArchive;

#[Route('/API/integration')]

class IntegrationController extends AbstractController
{
    private  $integrationRepo;
    private  $donneurRepo;
    private  $affichageRepo;
    private  $serializer;
    public  $em;
    private $conn;
    public $AuthService;
    public function __construct(
        integrationRepo $integrationRepo,
        affichageRepo $affichageRepo,
        donneurRepo $donneurRepo,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        MessageService $MessageService,
        FileService $FileService,
        Connection $conn,
        AuthService $AuthService,
        GeneralService $generalService,
        )
    {
        $this->conn = $conn;
        $this->AuthService = $AuthService;
        $this->integrationRepo = $integrationRepo;
        $this->affichageRepo = $affichageRepo;
        $this->donneurRepo = $donneurRepo;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->MessageService = $MessageService;
        $this->FileService = $FileService;
        $this->generalService = $generalService;
    
    }
    #[Route('/checkStepsIntegration', methods: ['POST'])]
    public function checkStep(integrationRepo $integrationRepo , Request $request , SerializerInterface $serializer): JsonResponse
    {
        try{
            $this->AuthService->checkAuth(0,$request);
            $codeStatut="ERROR";
            $respObjects = array();
            $titre=$request->get("titre");
            $ptf = $request->get("ptf");
    
            $num_creance = $request->get("num_creance");
    
            $cin_debiteur = $request->get("cin_debiteur");
            $garantie_champ = $request->get("garantie_champ");
            $financement_champ = $request->get("financement_champ");
            $proc_champ = $request->get("proc_champ");
            $tel_champ = $request->get("tel_champ");
            $adresse_champ = $request->get("adresse_champ");
            $array_list  = json_decode($request->get("array_action"), true);
            if(!empty($titre) || $titre != ""  || $ptf != "" || empty($ptf) != ""){
                //Debiteur details
                if(in_array("debiteur" , $array_list) ){
                    if(!empty($_FILES['debiteur_file']['name'])){
                        $requiredHeaders=array();
                        $file = $_FILES['debiteur_file'];
                        $fileCheckDebiteur=$this->FileService->checkFile($file);
                        if($fileCheckDebiteur["codeStatut"] == "OK" ){
                            if($cin_debiteur == 1){
                                $check_action=true;
                                $codeStatut="OK";
                            }else{
                                $codeStatut="CIN_DEBITEUR_IS_EMPTY";
                            }
                        }else{
                            $codeStatut=$fileCheckDebiteur["codeStatut"] ;
                        }
                    }else{
                        $codeStatut="EMPTY_FILE";
                    }
                }else{
                    $codeStatut="REQUIRED_CREANCE";
                }

                //Créance details
                if(in_array("creance" , $array_list) && $check_action = true){
                    if(!empty($_FILES['creance_file']['name'])){
                        $requiredHeaders=array();
                        $file = $_FILES['creance_file'];
                        $fileCheckCreance=$this->FileService->checkFile($file);
                        if($fileCheckCreance["codeStatut"] == "OK" ){
                            if($num_creance == 1){
                                $codeStatut="OK";
                                $check_action=true;
                            }else{
                                $codeStatut="NUM_CREANCE_IS_EMPTY";
                            }
                        }else{
                            $codeStatut=$fileCheckCreance["codeStatut"] ;
                        }
                    }else{
                        $codeStatut="EMPTY_FILE";
                    }
                }else{
                    $codeStatut="REQUIRED_CREANCE";
                }

                //Garantie
                if(in_array("garantie" , $array_list) && $check_action=true){
                    if(!empty($_FILES['garantie_file']['name'])){
                        $requiredHeaders=array();
                        $file = $_FILES['garantie_file'];
                        $fileCheckGarantie=$this->FileService->checkFile($file);
                        if($fileCheckGarantie["codeStatut"] == "OK" ){
                            if($garantie_champ == 1){
                                $codeStatut="OK";
                                $check_action=true;
                            }else{
                                $codeStatut="GARANTIE_IS_EMPTY";
                            }
                        }else{
                            $codeStatut=$fileCheckGarantie["codeStatut"] ;
                        }
                    }else{
                        $codeStatut="EMPTY_FILE";
                    }
                }

                //detail_financement
                if(in_array("detail_financement" , $array_list) && $check_action=true){
                    if(!empty($_FILES['detail_financement_file']['name'])){
                        $requiredHeaders=array();
                        $file = $_FILES['detail_financement_file'];
                        $fileCheckDetailFianacement=$this->FileService->checkFile($file);
                        if($fileCheckDetailFianacement["codeStatut"] == "OK" ){
                            if($financement_champ == 1){
                                $codeStatut="OK";
                                $check_action=true;
                            }else{
                                $codeStatut="FINANCEMENT_IS_EMPTY";
                            }
                        }else{
                            $codeStatut=$fileCheckDetailFianacement["codeStatut"] ;
                        }
                    }else{
                        $codeStatut="EMPTY_FILE";
                    }
                }

                //Procédure judicaire
                if(in_array("proc" , $array_list) && $check_action=true){
                    if(!empty($_FILES['proc_file']['name'])){
                        $requiredHeaders=array();
                        $file = $_FILES['proc_file'];
                        $fileCheckProcedureJudicaire=$this->FileService->checkFile($file);
                        if($fileCheckProcedureJudicaire["codeStatut"] == "OK" ){
                            if($proc_champ == 1){
                                $codeStatut="OK";
                                $check_action=true;
                            }else{
                                $codeStatut="PROC_IS_EMPTY";
                            }
                        }else{
                            $codeStatut=$fileCheckProcedureJudicaire["codeStatut"] ;
                        }
                    }else{
                        $codeStatut="EMPTY_FILE";
                    }
                }
                //Télephone 
                if(in_array("tel" , $array_list) && $check_action=true){
                    if(!empty($_FILES['tel_file']['name'])){
                        $requiredHeaders=array();
                        $file = $_FILES['tel_file'];
                        $fileCheckTel=$this->FileService->checkFile($file);
                        if($fileCheckTel["codeStatut"] == "OK" ){
                            if($tel_champ == 1){
                                $codeStatut="OK";
                                $check_action=true;
                            }else{
                                $codeStatut="TEL_IS_EMPTY";
                            }
                        }else{
                            $codeStatut=$fileCheckTel["codeStatut"] ;
                        }
                    }else{
                        $codeStatut="EMPTY_FILE";
                    }
                }
            }   
            else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $respObjects["err"] = $e->getMessage();
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"]=$codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }
    #[Route('/verificationCountRow', methods: ['POST'])]
    public function verificationCountRow(integrationRepo $integrationRepo , donneurRepo $donneurRepo , Request $request , SerializerInterface $serializer): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);
        $codeStatut="ERROR";
        $respObjects = array();
        try{
            function getRowCount($file) {
                $rowCount = 0;
                $handle = fopen($file, "r");
                while (fgets($handle) !== false) {
                    $rowCount++;
                }
                fclose($handle);
                return $rowCount;
            }
           
            if(isset($_FILES['debiteur_file']['tmp_name'])){
                $debiteur_file = $_FILES['debiteur_file']['tmp_name'];
                $debiteurRowCount = getRowCount($debiteur_file);
                if(isset($_FILES['creance_file']['tmp_name'])){
                    $creance_file = $_FILES['creance_file']['tmp_name'];
                    $creanceRowCount = getRowCount($creance_file);
                    if($debiteurRowCount == $creanceRowCount ){

                        if(isset($_FILES['dossier_file']['tmp_name'])){
                            $dossier_file = $_FILES['dossier_file']['tmp_name'];
                            $dossierRowCount = getRowCount($dossier_file);
                            if($creanceRowCount == $dossierRowCount ){

                                if(isset($_FILES['garantie_file']['tmp_name'])){
                                    $garantie_file = $_FILES['garantie_file']['tmp_name'];
                                    $garantieRowCount = getRowCount($garantie_file);
                                    if($dossierRowCount == $garantieRowCount ){

                                        if(isset($_FILES['garantie_file']['tmp_name'])){
                                            $garantie_file = $_FILES['garantie_file']['tmp_name'];
                                            $garantieRowCount = getRowCount($garantie_file);
                                            if($dossierRowCount == $garantieRowCount ){

                                                if(isset($_FILES['proc_file']['tmp_name'])){
                                                    $proc_file = $_FILES['proc_file']['tmp_name'];
                                                    $procRowCount = getRowCount($proc_file);
                                                    if($garantieRowCount == $procRowCount ){
                                                        
                                                        if(isset($_FILES['telephone_file']['tmp_name'])){
                                                            $telephone_file = $_FILES['telephone_file']['tmp_name'];
                                                            $telephoneRowCount = getRowCount($telephone_file);
                                                            if($procRowCount == $telephoneRowCount ){
                                                                
                                                                if(isset($_FILES['adresse_file']['tmp_name'])){
                                                                    $adresse_file = $_FILES['adresse_file']['tmp_name'];
                                                                    $adresseRowCount = getRowCount($adresse_file);
                                                                    if($telephoneRowCount == $adresseRowCount ){
                                                                        $codeStatut="OK";
                                                                    }else{
                                                                        $codeStatut = "ERROR_FILES";
                                                                    }
                                                                }else{
                                                                    $codeStatut="OK";
                                                                }

                                                            }else{
                                                                $codeStatut = "ERROR_FILES";
                                                            }
                                                        }else{
                                                            $codeStatut="OK";
                                                        }

                                                    }else{
                                                        $codeStatut = "ERROR_FILES";
                                                    }
                                                }else{
                                                    $codeStatut="OK";
                                                }
                                                
                                            }else{
                                                $codeStatut = "ERROR_FILES";
                                            }
                                        }else{
                                            $codeStatut="OK";
                                        }

                                    }else{
                                        $codeStatut = "ERROR_FILES";
                                    }
                                }else{
                                    $codeStatut="OK";
                                }

                            }else{
                                $codeStatut = "ERROR_FILES";
                            }
                        }else{
                            $codeStatut="OK";
                        }

                    }else{
                        $codeStatut = "ERROR_FILES";
                    }
                }else{
                    $codeStatut="OK";
                }
            }else{
                $codeStatut = "ERROR_FILES";
            }

        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["errr"] = $e->getMessage();
        }
        $respObjects["codeStatut"]=$codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }
    public function trimHeaders($filePath)
    {
        // Open the CSV file for reading and writing
        // $handle = fopen($filePath, "r+");
        
        // // Check if the file is opened successfully
        // if ($handle !== FALSE) {
        //     // Read the header row
        //     $headerRow = fgetcsv($handle, 1000000, ";");
        //     // dump($headerRow);
            
        //     // dump($headerRow);
        //     // Trim each header
        //     $headerRow = array_map('trim', $headerRow);

        //     // // Rewind to the beginning of the file
        //     // rewind($handle);
        //     fseek($handle, 0);


        //     // Write the trimmed header row back into the file
        //     fputcsv($handle, $headerRow);

        //     // Close the file handle
        //     fclose($handle);
            
        //     return true;
        // } else {
        //     // Failed to open the file for reading and writing
        //     return false;
        // }
        return true;

    }

    
    #[Route('/setIntegration1', methods: ['POST'])]
    public function setIntegration1(integrationRepo $integrationRepo , donneurRepo $donneurRepo , Request $request , SerializerInterface $serializer): JsonResponse
    {
        // $this->AuthService->checkAuth(0,$request);
        $codeStatut="ERROR";
        $respObjects = array();
        $titre=$request->get("titre");
        $ptf = $request->get("ptf");
        $id = $request->get("id");
        
        $num_creance = $request->get("num_creance");
        $cin_debiteur = $request->get("cin_debiteur");
        $type_debiteur = $request->get("type_debiteur");
        $raison_sociale = $request->get("raison_sociale");
        $garantie_champ = $request->get("garantie_champ");
        $financement_champ = $request->get("financement_champ");
        $proc_champ = $request->get("proc_champ");
        $tel_champ = $request->get("tel_champ");
        $adresse_champ = $request->get("adresse_champ");
        $details_model = $request->get("details_model");
        $details_model_creance = $request->get("details_model_creance");
        $details_model_debiteur = $request->get("details_model_debiteur");
        $details_model_garantie = $request->get("details_model_garantie");
        $details_model_dossier = $request->get("details_model_dossier");
        $details_model_proc = $request->get("details_model_proc");
        $details_model_telephone = $request->get("details_model_telephone");
        $details_model_email = $request->get("details_model_email");

        $emploi_champ = $request->get("emploi_champ");
        $employeur_champ = $request->get("employeur_champ");
        $isMaj = $request->get("isMaj");

        try{
            if(!empty($titre) || $titre != ""  || $ptf != "" || empty($ptf) != ""){
                //Debiteur details
                $statutIntgeration = $integrationRepo->getStautsId(1);
                $ptf_ = $donneurRepo->getOnePtf($ptf);
                // if($isMaj == 0){
                // }else{
                //     $integration = $this->em->getRepository(Integration::class)->find($id);
                //     $resetImport = $integrationRepo->resetImport($id);
                // }
                $integration = new Integration();
                $integration->setTitre($titre);
                $integration->setDateCreation(new \DateTime());
                $integration->setEtat(1);
                $integration->setStatus($statutIntgeration);
                $integration->setIdPtf($ptf_);
                // $integration->setIsMaj(0);
                $this->em->persist($integration);
                $this->em->flush();
                $test=false ;
                if($request->get("debiteur_in_step") == "1"){
                    if(!empty($_FILES['debiteur_file']['name'])){
                        $requiredHeaders=array();
                        $file = $_FILES['debiteur_file'];
                        $fileCheckDebiteur=$this->FileService->checkFile($file);
                        if($fileCheckDebiteur["codeStatut"] == "OK" ){
                            // if(($cin_debiteur == 1 ||$raison_sociale == 1 )){
                                $file = $_FILES['debiteur_file'];
                                $fileCheck=$this->FileService->checkFile($file);
                                $nom = $fileCheckDebiteur["nom"];
                                $extension_upload=$fileCheckDebiteur["extension_upload"];
                                $fileTmpLoc=$fileCheckDebiteur["fileTmpLoc"];
                                $details_model_debiteur  = json_decode($request->get("details_model_debiteur"), true);

                                $model_import = $integrationRepo->createModel($details_model_debiteur , "debiteur");
                                if($model_import){
                                    //Create import debiteur
                                    $import = new Import();
                                    $import->setDateCreation(new \DateTime());
                                    $import->setEtat(0);
                                    $import->setIdModel($model_import);
                                    $import->setIdIntegration($integration);
                                    $import->setOrderImport(1);
                                    $import->setUrl("");
                                    $import->setType("debiteur");
                                    $this->em->persist($import);
                                    $this->em->flush();
                                
                                    //Import CR
                                    $filesystem = new Filesystem();
                                    $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/fichiers/import/';
                                    $folderPath = $publicDirectory . 'integration-num-'.$integration->getId()."/import-debiteur-num-".$import->getId();
                                    $fileStore = "fichiers/import/integration-num-".$integration->getId() ."/import-debiteur-num-".$import->getId()."/". $nom . '.' . $extension_upload;
                                    //----Create file if n'existe pas
                                    $filesystem->mkdir($folderPath);
                                    move_uploaded_file($fileTmpLoc, $fileStore);
                                    $import->setUrl($fileStore);

                                    $this->em->flush();
                                    $codeStatut="OK";
                                    $csvData = file_get_contents($import->getUrl());
                                    $rows = array_map('str_getcsv', explode("\n", $csvData));
                                    $numberOfFiles = 3;
                                    $rowsPerFile = ceil(count($rows) / $numberOfFiles);
                                    $chunks = array_chunk($rows, $rowsPerFile);
                                    $header = array_shift($rows);
                                    $import->setNbrLignes(count($rows) - 2);
                                    $this->em->flush();

                                    if (!is_dir($folderPath)) {
                                        mkdir($folderPath);
                                    }

                                    $order=1;
                                    for ($i = 0; $i < count($chunks); $i++) {
                                        $outputFileName = sprintf('output_file_%d.csv', $i + 1);
                                        $folderPath = "fichiers/import/integration-num-".$integration->getId()."/import-debiteur-num-".$import->getId()."/split_files/";
                                        $filesystem->mkdir($folderPath);
                                        $outputFilePath = $folderPath . $outputFileName;

                                        if($order == 1){
                                            $outputCsvData = implode("\n", array_map('implode', $chunks[$i]));
                                        }else{
                                            // Convert header and data rows to strings
                                            $headerString = implode(",", $header);
                                            $dataStrings = array_map(function ($row) {
                                                return implode(",", $row);
                                            }, $chunks[$i]);
                                            // Combine header and data rows as CSV lines
                                            $outputCsvLines = array_merge(array($headerString), $dataStrings);
                                            // Convert the CSV lines to a single CSV string
                                            $outputCsvData = implode("\n", $outputCsvLines);
                                        }
                                        // try {
                                            // Move the file to the public/split_files directory
                                            file_put_contents($outputFilePath, $outputCsvData);
                                            $detail = new DetailsImport();
                                            $filesystem = new Filesystem();
                                            $detail->setUrl($outputFilePath);
                                            $detail->setIdImport($import);
                                            $detail->setOrdre($order);
                                            $detail->setEtat(0);
                                            $this->em->persist($detail);
                                            $this->em->flush();
                                            $order++;
                                            $codeStatut="OK";
                                        // } catch (\Exception $e) {
                                        //     $codeStatut="ERROR";
                                        // }
                                    }
                                }else{
                                    $codeStatut="EMPTY_FILE";
                                }
                            // $codeStatut="OK";

                            // }else{
                            //     $codeStatut="CIN_DEBITEUR_OU_RS_IS_EMPTY";
                            // }
                        }else{
                            $codeStatut=$fileCheckDebiteur["codeStatut"] ;
                        }

                    }else{
                        $codeStatut="EMPTY_FILE";
                    }
                }else{
                    $codeStatut="REQUIRED_DEBITEUR";
                }
                if($codeStatut=="OK"){
                    if($request->get("creance_in_step") == "1"){
                        if(!empty($_FILES['creance_file']['name'])){
                            $requiredHeaders=array();
                            $file = $_FILES['creance_file'];
                            $fileCheckCreance=$this->FileService->checkFile($file);
                            if($fileCheckCreance["codeStatut"] == "OK" ){
                                if($num_creance == 1){
                                    $ptf_ = $donneurRepo->getOnePtf($ptf);
                                    //Check donneurordre
                                    $nom = $fileCheckCreance["nom"];
                                    $extension_upload=$fileCheckCreance["extension_upload"];
                                    $fileTmpLoc=$fileCheckCreance["fileTmpLoc"];
                                    $details_model_creance  = json_decode($request->get("details_model_creance"), true);

                                    $model_import = $integrationRepo->createModel($details_model_creance , "creance");
                                    // $model_import =$this->em->getRepository(ModelImport::class)->findOneBy(["id"=>30]);
                                    if($model_import){
                                        //Create import debiteur
                                        $import = new Import();
                                        $import->setDateCreation(new \DateTime());
                                        $import->setEtat(0);
                                        $import->setIdModel($model_import);
                                        $import->setIdIntegration($integration);
                                        $import->setOrderImport(1);
                                        $import->setUrl("");
                                        $import->setType("creance");
                                        $this->em->persist($import);
                                        $this->em->flush();
                                    
                                        //Import CR
                                        $filesystem = new Filesystem();
                                        $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/fichiers/import/';
                                        $folderPath = $publicDirectory . 'integration-num-'.$integration->getId()."/import-creance-num-".$import->getId();
                                        $fileStore = "fichiers/import/integration-num-".$integration->getId() ."/import-creance-num-".$import->getId()."/". $nom . '.' . $extension_upload;
                                        //----Create file if n'existe pas
                                        $filesystem->mkdir($folderPath);
                                        move_uploaded_file($fileTmpLoc, $fileStore);
                                        $this->trimHeaders($fileStore);
                                        $import->setUrl($fileStore);
                                        $this->em->flush();
                                        $codeStatut="OK";
                                        $csvData = file_get_contents($import->getUrl());
                                        $rows = array_map('str_getcsv', explode("\n", $csvData));
                                        $numberOfFiles = 3;
                                        $rowsPerFile = ceil(count($rows) / $numberOfFiles);
                                        $chunks = array_chunk($rows, $rowsPerFile);
                                        $header = array_shift($rows);
                                        $import->setNbrLignes(count($rows) - 2);
                                        $this->em->flush();

                                        if (!is_dir($folderPath)) {
                                            mkdir($folderPath);
                                        }
                                        $order=1;
                                        for ($i = 0; $i < count($chunks); $i++) {
                                            $outputFileName = sprintf('output_file_%d.csv', $i + 1);
                                            $folderPath = "fichiers/import/integration-num-".$integration->getId()."/import-creance-num-".$import->getId()."/split_files/";
                                            $filesystem->mkdir($folderPath);
                                            $outputFilePath = $folderPath . $outputFileName;
                                            if($order == 1){
                                                $outputCsvData = implode("\n", array_map('implode', $chunks[$i]));
                                            }else{
                                                // Convert header and data rows to strings
                                                $headerString = implode(",", $header);
                                                $dataStrings = array_map(function ($row) {
                                                    return implode(",", $row);
                                                }, $chunks[$i]);
                                                // Combine header and data rows as CSV lines
                                                $outputCsvLines = array_merge(array($headerString), $dataStrings);
                                                // Convert the CSV lines to a single CSV string
                                                $outputCsvData = implode("\n", $outputCsvLines);
                                            }
                                            try {
                                                // Move the file to the public/split_files directory
                                                file_put_contents($outputFilePath, $outputCsvData);
                                                $detail = new DetailsImport();
                                                $filesystem = new Filesystem();
                                                $detail->setUrl($outputFilePath);
                                                $detail->setIdImport($import);
                                                $detail->setEtat(0);
                                                $detail->setOrdre($order);
                                                $this->em->persist($detail);
                                                $this->em->flush();
                                                $order++;
                                                $codeStatut="OK";
                                            } catch (\Exception $e) {
                                                $codeStatut="ERROR";
                                            }
                                        }
                                        $codeStatut="OK";
                                    }else{
                                        $codeStatut="EMPTY_FILE";
                                    }
                                }else{
                                    $codeStatut="ID_DEBITEUR_IS_EMPTY";
                                }
                            }else{
                                $codeStatut=$fileCheckDebiteur["codeStatut"] ;
                            }
                        }else{
                            $codeStatut="EMPTY_FILE";
                        }
                    }else{
                        $codeStatut="REQUIRED_CREANCE";
                    }
                }else{
                    $integration->setEtat(0);
                    // $codeStatut="ERROR_IMPORT";
                }
                
                if($codeStatut=="OK"){
                    if($request->get("dossier_in_step") == "1"){
                        if(!empty($_FILES['dossier_file']['name'])){
                            $requiredHeaders=array();
                            $file = $_FILES['dossier_file'];
                            $fileCheckDossier=$this->FileService->checkFile($file);
                            if($fileCheckDossier["codeStatut"] == "OK" ){
                                if($num_creance == 1){
                                    $ptf_ = $donneurRepo->getOnePtf($ptf);
                                    //Check donneurordre
                                    $nom = $fileCheckDossier["nom"];
                                    $extension_upload=$fileCheckDossier["extension_upload"];
                                    $fileTmpLoc=$fileCheckDossier["fileTmpLoc"];
                                    $details_model_dossier  = json_decode($request->get("details_model_dossier"), true);
                                    $model_import = $integrationRepo->createModel($details_model_dossier , "dossier");
                                    // $model_import =$this->em->getRepository(ModelImport::class)->findOneBy(["id"=>30]);
                                    if($model_import){
                                        //Create import debiteur
                                        $import = new Import();
                                        $import->setDateCreation(new \DateTime());
                                        $import->setEtat(0);
                                        $import->setIdModel($model_import);
                                        $import->setIdIntegration($integration);
                                        $import->setOrderImport(1);
                                        $import->setUrl("");
                                        $import->setType("dossier");
                                        $this->em->persist($import);
                                        $this->em->flush();

                                        $import->setNbrLignes(count($rows) - 2);
                                        $this->em->flush();
                                        //Import CR
                                        $filesystem = new Filesystem();
                                        $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/fichiers/import/';
                                        $folderPath = $publicDirectory . 'integration-num-'.$integration->getId()."/import-dossier-num-".$import->getId();
                                        $fileStore = "fichiers/import/integration-num-".$integration->getId() ."/import-dossier-num-".$import->getId()."/". $nom . '.' . $extension_upload;
                                        //----Create file if n'existe pas
                                        $filesystem->mkdir($folderPath);
                                        move_uploaded_file($fileTmpLoc, $fileStore);
                                        $import->setUrl($fileStore);
                                        $this->em->flush();
                                        $csvData = file_get_contents($import->getUrl());
                                    $rows = array_map('str_getcsv', explode("\n", $csvData));
                                    $numberOfFiles = 3;
                                    $rowsPerFile = ceil(count($rows) / $numberOfFiles);
                                    $chunks = array_chunk($rows, $rowsPerFile);
                                    $header = array_shift($rows);

                                    if (!is_dir($folderPath)) {
                                        mkdir($folderPath);
                                    }
                                    $order=1;
                                    for ($i = 0; $i < count($chunks); $i++) {
                                        $outputFileName = sprintf('output_file_%d.csv', $i + 1);
                                        $folderPath = "fichiers/import/integration-num-".$integration->getId()."/import-dossier-num-".$import->getId()."/split_files/";
                                        $filesystem->mkdir($folderPath);
                                        $outputFilePath = $folderPath . $outputFileName;

                                        if($order == 1){
                                            $outputCsvData = implode("\n", array_map('implode', $chunks[$i]));
                                        }else{
                                            // Convert header and data rows to strings
                                            $headerString = implode(",", $header);
                                            $dataStrings = array_map(function ($row) {
                                                return implode(",", $row);
                                            }, $chunks[$i]);
                                            // Combine header and data rows as CSV lines
                                            $outputCsvLines = array_merge(array($headerString), $dataStrings);
                                            // Convert the CSV lines to a single CSV string
                                            $outputCsvData = implode("\n", $outputCsvLines);
                                        }
                                        try {
                                            // Move the file to the public/split_files directory
                                            file_put_contents($outputFilePath, $outputCsvData);
                                            $detail = new DetailsImport();
                                            $filesystem = new Filesystem();
                                            $detail->setUrl($outputFilePath);
                                            $detail->setIdImport($import);
                                            $detail->setOrdre($order);
                                            $detail->setEtat(0);
                                            $this->em->persist($detail);
                                            $this->em->flush();
                                            $order++;
                                            $codeStatut="OK";
                                        } catch (\Exception $e) {
                                            $codeStatut="ERROR";
                                        }
                                    }
                                        $codeStatut="OK";
                                    }else{
                                        $codeStatut="EMPTY_FILE";
                                    }
                                }else{
                                    $codeStatut="ID_DEBITEUR_IS_EMPTY";
                                }
                            }else{
                                $codeStatut=$fileCheckDebiteur["codeStatut"] ;
                            }
                        }else{
                            $codeStatut="EMPTY_FILE";
                        }
                    }else{
                        $codeStatut="REQUIRED_DOSSIER";
                    }
                }
                if($codeStatut=="OK"){
                    if($request->get("garantie_in_step") == "1"){
                        if(!empty($_FILES['garantie_file']['name'])){
                            $requiredHeaders=array();
                            $file = $_FILES['garantie_file'];
                            $fileCheckGarantie=$this->FileService->checkFile($file);
                            if($fileCheckGarantie["codeStatut"] == "OK" ){
                                if($num_creance == 1){
                                    $ptf_ = $donneurRepo->getOnePtf($ptf);
                                    //Check donneurordre
                                    $nom = $fileCheckGarantie["nom"];
                                    $extension_upload=$fileCheckGarantie["extension_upload"];
                                    $fileTmpLoc=$fileCheckGarantie["fileTmpLoc"];
                                    $details_model_garantie  = json_decode($request->get("details_model_garantie"), true);
    
                                    $model_import = $integrationRepo->createModel($details_model_garantie , "garantie");
                                    // $model_import =$this->em->getRepository(ModelImport::class)->findOneBy(["id"=>30]);
                                    if($model_import){
                                        //Create import debiteur
                                        $import = new Import();
                                        $import->setDateCreation(new \DateTime());
                                        $import->setEtat(0);
                                        $import->setIdModel($model_import);
                                        $import->setIdIntegration($integration);
                                        $import->setOrderImport(1);
                                        $import->setUrl("");
                                        $import->setType("garantie");
                                        $this->em->persist($import);
                                        $this->em->flush();
                                        //Import CR
                                        $filesystem = new Filesystem();
                                        $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/fichiers/import/';
                                        $folderPath = $publicDirectory . 'integration-num-'.$integration->getId()."/import-garantie-num-".$import->getId();
                                        $fileStore = "fichiers/import/integration-num-".$integration->getId() ."/import-garantie-num-".$import->getId()."/". $nom . '.' . $extension_upload;
                                        //----Create file if n'existe pas
                                        $filesystem->mkdir($folderPath);
                                        move_uploaded_file($fileTmpLoc, $fileStore);
                                        $import->setUrl($fileStore);
                                        $this->em->flush();
                                        $csvData = file_get_contents($import->getUrl());
                                        $rows = array_map('str_getcsv', explode("\n", $csvData));
                                        $numberOfFiles = 3;
                                        $rowsPerFile = ceil(count($rows) / $numberOfFiles);
                                        $chunks = array_chunk($rows, $rowsPerFile);
                                        $header = array_shift($rows);
                                        $import->setNbrLignes(count($rows) - 2);
                                        $this->em->flush();
                                    if (!is_dir($folderPath)) {
                                        mkdir($folderPath);
                                    }
                                    $order=1;
                                    for ($i = 0; $i < count($chunks); $i++) {
                                        $outputFileName = sprintf('output_file_%d.csv', $i + 1);
                                        $folderPath = "fichiers/import/integration-num-".$integration->getId()."/import-garantie-num-".$import->getId()."/split_files/";
                                        $filesystem->mkdir($folderPath);
                                        $outputFilePath = $folderPath . $outputFileName;

                                        if($order == 1){
                                            $outputCsvData = implode("\n", array_map('implode', $chunks[$i]));
                                        }else{
                                            // Convert header and data rows to strings
                                            $headerString = implode(",", $header);
                                            $dataStrings = array_map(function ($row) {
                                                return implode(",", $row);
                                            }, $chunks[$i]);
                                            // Combine header and data rows as CSV lines
                                            $outputCsvLines = array_merge(array($headerString), $dataStrings);
                                            // Convert the CSV lines to a single CSV string
                                            $outputCsvData = implode("\n", $outputCsvLines);
                                        }
                                        try {
                                            // Move the file to the public/split_files directory
                                            file_put_contents($outputFilePath, $outputCsvData);
                                            $detail = new DetailsImport();
                                            $filesystem = new Filesystem();
                                            $detail->setUrl($outputFilePath);
                                            $detail->setIdImport($import);
                                            $detail->setOrdre($order);
                                            $detail->setEtat(0);
                                            $this->em->persist($detail);
                                            $this->em->flush();
                                            $order++;
                                            $codeStatut="OK";
                                        } catch (\Exception $e) {
                                            $codeStatut="ERROR";
                                        }
                                    }
                                        $codeStatut="OK";
                                    }else{
                                        $codeStatut="EMPTY_FILE";
                                    }
                                }else{
                                    $codeStatut="ID_DEBITEUR_IS_EMPTY";
                                }
                            }else{
                                $codeStatut=$fileCheckDebiteur["codeStatut"] ;
                            }
                        }else{
                            $codeStatut="EMPTY_FILE";
                        }
                    }
                }
                if($codeStatut=="OK"){
                    if($request->get("proc_in_step") == "1"){
                        if(!empty($_FILES['proc_file']['name'])){
                            $requiredHeaders=array();
                            $file = $_FILES['proc_file'];
                            $fileCheckGarantie=$this->FileService->checkFile($file);
                            if($fileCheckGarantie["codeStatut"] == "OK" ){
                                $ptf_ = $donneurRepo->getOnePtf($ptf);
                                //Check donneurordre
                                $nom = $fileCheckGarantie["nom"];
                                $extension_upload=$fileCheckGarantie["extension_upload"];
                                $fileTmpLoc=$fileCheckGarantie["fileTmpLoc"];
                                $details_model_proc  = json_decode($request->get("details_model_proc"), true);

                                $model_import = $integrationRepo->createModel($details_model_proc , "proc");
                                // $model_import =$this->em->getRepository(ModelImport::class)->findOneBy(["id"=>30]);
                                if($model_import){
                                    //Create import debiteur
                                    $import = new Import();
                                    $import->setDateCreation(new \DateTime());
                                    $import->setEtat(0);
                                    $import->setIdModel($model_import);
                                    $import->setIdIntegration($integration);
                                    $detail->setEtat(0);
                                    $import->setOrderImport(1);
                                    $import->setUrl("");
                                    $import->setType("proc");
                                    $this->em->persist($import);
                                    $this->em->flush();
                                    //Import CR
                                    $filesystem = new Filesystem();
                                    $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/fichiers/import/';
                                    $folderPath = $publicDirectory . 'integration-num-'.$integration->getId()."/import-proc-num-".$import->getId();
                                    $fileStore = "fichiers/import/integration-num-".$integration->getId() ."/import-proc-num-".$import->getId()."/". $nom . '.' . $extension_upload;
                                    //----Create file if n'existe pas
                                    $filesystem->mkdir($folderPath);
                                    move_uploaded_file($fileTmpLoc, $fileStore);
                                    $import->setUrl($fileStore);
                                    $this->em->flush();
                                    $csvData = file_get_contents($import->getUrl());
                                    $rows = array_map('str_getcsv', explode("\n", $csvData));
                                    $numberOfFiles = 3;
                                    $rowsPerFile = ceil(count($rows) / $numberOfFiles);
                                    $chunks = array_chunk($rows, $rowsPerFile);
                                    $header = array_shift($rows);
                                    $import->setNbrLignes(count($rows) - 2);
                                    $this->em->flush();

                                    if (!is_dir($folderPath)) {
                                        mkdir($folderPath);
                                    }
                                    $order=1;
                                    for ($i = 0; $i < count($chunks); $i++) {
                                        $outputFileName = sprintf('output_file_%d.csv', $i + 1);
                                        $folderPath = "fichiers/import/integration-num-".$integration->getId()."/import-proc-num-".$import->getId()."/split_files/";
                                        $filesystem->mkdir($folderPath);
                                        $outputFilePath = $folderPath . $outputFileName;

                                        if($order == 1){
                                            $outputCsvData = implode("\n", array_map('implode', $chunks[$i]));
                                        }else{
                                            // Convert header and data rows to strings
                                            $headerString = implode(",", $header);
                                            $dataStrings = array_map(function ($row) {
                                                return implode(",", $row);
                                            }, $chunks[$i]);
                                            // Combine header and data rows as CSV lines
                                            $outputCsvLines = array_merge(array($headerString), $dataStrings);
                                            // Convert the CSV lines to a single CSV string
                                            $outputCsvData = implode("\n", $outputCsvLines);
                                        }
                                        try {
                                            // Move the file to the public/split_files directory
                                            file_put_contents($outputFilePath, $outputCsvData);
                                            $detail = new DetailsImport();
                                            $filesystem = new Filesystem();
                                            $detail->setUrl($outputFilePath);
                                            $detail->setIdImport($import);
                                            $detail->setEtat(0);
                                            $detail->setOrdre($order);
                                            $this->em->persist($detail);
                                            $this->em->flush();
                                            $order++;
                                            $codeStatut="OK";
                                        } catch (\Exception $e) {
                                            $codeStatut="ERROR";
                                        }
                                    }
                                    $codeStatut="OK";
                                }else{
                                    $codeStatut="EMPTY_FILE";
                                }
                            }else{
                                $codeStatut=$fileCheckDebiteur["codeStatut"] ;
                            }
                        }else{
                            $codeStatut="EMPTY_FILE";
                        }
                    }
                }

                if($codeStatut=="OK"){
                    if($request->get("telephone_in_step") == "1"){
                        if(!empty($_FILES['telephone_file']['name'])){
                            $requiredHeaders=array();
                            $file = $_FILES['telephone_file'];
                            $fileCheckTelephone=$this->FileService->checkFile($file);
                            if($fileCheckTelephone["codeStatut"] == "OK" ){
                                $ptf_ = $donneurRepo->getOnePtf($ptf);
                                //Check donneurordre
                                $nom = $fileCheckTelephone["nom"];
                                $extension_upload=$fileCheckTelephone["extension_upload"];
                                $fileTmpLoc=$fileCheckTelephone["fileTmpLoc"];
                                $details_model_telephone  = json_decode($request->get("details_model_telephone"), true);

                                $model_import = $integrationRepo->createModel($details_model_telephone , "telephone");
                                if($model_import){
                                    //Create import debiteur
                                    $import = new Import();
                                    $import->setDateCreation(new \DateTime());
                                    $import->setEtat(0);
                                    $import->setIdModel($model_import);
                                    $import->setIdIntegration($integration);
                                    $import->setOrderImport(1);
                                    $import->setUrl("");
                                    $import->setType("telephone");
                                    $this->em->persist($import);
                                    $this->em->flush();
                                    //Import CR
                                    $filesystem = new Filesystem();
                                    $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/fichiers/import/';
                                    $folderPath = $publicDirectory . 'integration-num-'.$integration->getId()."/import-telephone-num-".$import->getId();
                                    $fileStore = "fichiers/import/integration-num-".$integration->getId() ."/import-telephone-num-".$import->getId()."/". $nom . '.' . $extension_upload;
                                    //----Create file if n'existe pas
                                    $filesystem->mkdir($folderPath);
                                    move_uploaded_file($fileTmpLoc, $fileStore);
                                    $import->setUrl($fileStore);
                                    $this->em->flush();
                                    

                                    $csvData = file_get_contents($import->getUrl());
                                    $rows = array_map('str_getcsv', explode("\n", $csvData));
                                    $numberOfFiles = 3;
                                    $rowsPerFile = ceil(count($rows) / $numberOfFiles);
                                    $chunks = array_chunk($rows, $rowsPerFile);
                                    $header = array_shift($rows);

                                    $import->setNbrLignes(count($rows) - 2);
                                    $this->em->flush();
                                    if (!is_dir($folderPath)) {
                                        mkdir($folderPath);
                                    }
                                    $order=1;
                                    for ($i = 0; $i < count($chunks); $i++) {
                                        $outputFileName = sprintf('output_file_%d.csv', $i + 1);
                                        $folderPath = "fichiers/import/integration-num-".$integration->getId()."/import-telephone-num-".$import->getId()."/split_files/";
                                        $filesystem->mkdir($folderPath);
                                        $outputFilePath = $folderPath . $outputFileName;

                                        if($order == 1){
                                            $outputCsvData = implode("\n", array_map('implode', $chunks[$i]));
                                        }else{
                                            // Convert header and data rows to strings
                                            $headerString = implode(",", $header);
                                            $dataStrings = array_map(function ($row) {
                                                return implode(",", $row);
                                            }, $chunks[$i]);
                                            // Combine header and data rows as CSV lines
                                            $outputCsvLines = array_merge(array($headerString), $dataStrings);
                                            // Convert the CSV lines to a single CSV string
                                            $outputCsvData = implode("\n", $outputCsvLines);
                                        }
                                        try {
                                            // Move the file to the public/split_files directory
                                            file_put_contents($outputFilePath, $outputCsvData);
                                            $detail = new DetailsImport();
                                            $filesystem = new Filesystem();
                                            $detail->setUrl($outputFilePath);
                                            $detail->setIdImport($import);
                                            $detail->setEtat(0);
                                            $detail->setOrdre($order);
                                            $this->em->persist($detail);
                                            $this->em->flush();
                                            $order++;
                                            $codeStatut="OK";
                                        } catch (\Exception $e) {
                                            $codeStatut="ERROR";
                                        }
                                    }
                                }else{
                                    $codeStatut="EMPTY_FILE";
                                }
                            }else{
                                $codeStatut=$fileCheckTelephone["codeStatut"] ;
                            }
                        }else{
                            $codeStatut="EMPTY_FILE";
                        }
                    }
                }
                if($codeStatut=="OK"){
                    if($request->get("adresse_in_step") == "1"){
                        if(!empty($_FILES['adresse_file']['name'])){
                            $requiredHeaders=array();
                            $file = $_FILES['adresse_file'];
                            $fileCheckAdresse=$this->FileService->checkFile($file);
                            if($fileCheckAdresse["codeStatut"] == "OK" ){
                                $ptf_ = $donneurRepo->getOnePtf($ptf);
                                //Check donneurordre
                                $nom = $fileCheckAdresse["nom"];
                                $extension_upload=$fileCheckAdresse["extension_upload"];
                                $fileTmpLoc=$fileCheckAdresse["fileTmpLoc"];
                                $details_model_adresse  = json_decode($request->get("details_model_adresse"), true);
                                $model_import = $integrationRepo->createModel($details_model_adresse , "adresse");
                                if($model_import){
                                    //Create import debiteur
                                    $import = new Import();
                                    $import->setDateCreation(new \DateTime());
                                    $import->setEtat(0);
                                    $import->setIdModel($model_import);
                                    $import->setIdIntegration($integration);
                                    $import->setOrderImport(1);
                                    $import->setUrl("");
                                    $import->setType("adresse");
                                    $this->em->persist($import);
                                    $this->em->flush();
                                    //Import CR
                                    $filesystem = new Filesystem();
                                    $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/fichiers/import/';
                                    $folderPath = $publicDirectory . 'integration-num-'.$integration->getId()."/import-adresse-num-".$import->getId();
                                    $fileStore = "fichiers/import/integration-num-".$integration->getId() ."/import-adresse-num-".$import->getId()."/". $nom . '.' . $extension_upload;
                                    //----Create file if n'existe pas
                                    $filesystem->mkdir($folderPath);
                                    move_uploaded_file($fileTmpLoc, $fileStore);
                                    $import->setUrl($fileStore);
                                    $this->em->flush();
                                    $csvData = file_get_contents($import->getUrl());
                                    $rows = array_map('str_getcsv', explode("\n", $csvData));
                                    $numberOfFiles = 3;
                                    $rowsPerFile = ceil(count($rows) / $numberOfFiles);
                                    $chunks = array_chunk($rows, $rowsPerFile);
                                    $header = array_shift($rows);
                                    $import->setNbrLignes(count($rows) - 2);
                                    $this->em->flush();

                                    if (!is_dir($folderPath)) {
                                        mkdir($folderPath);
                                    }
                                    $order=1;
                                    for ($i = 0; $i < count($chunks); $i++) {
                                        $outputFileName = sprintf('output_file_%d.csv', $i + 1);
                                        $folderPath = "fichiers/import/integration-num-".$integration->getId()."/import-adresse-num-".$import->getId()."/split_files/";
                                        $filesystem->mkdir($folderPath);
                                        $outputFilePath = $folderPath . $outputFileName;

                                        if($order == 1){
                                            $outputCsvData = implode("\n", array_map('implode', $chunks[$i]));
                                        }else{
                                            // Convert header and data rows to strings
                                            $headerString = implode(",", $header);
                                            $dataStrings = array_map(function ($row) {
                                                return implode(",", $row);
                                            }, $chunks[$i]);
                                            // Combine header and data rows as CSV lines
                                            $outputCsvLines = array_merge(array($headerString), $dataStrings);
                                            // Convert the CSV lines to a single CSV string
                                            $outputCsvData = implode("\n", $outputCsvLines);
                                        }
                                        try {
                                            // Move the file to the public/split_files directory
                                            file_put_contents($outputFilePath, $outputCsvData);
                                            $detail = new DetailsImport();
                                            $filesystem = new Filesystem();
                                            $detail->setUrl($outputFilePath);
                                            $detail->setIdImport($import);
                                            $detail->setOrdre($order);
                                            $detail->setEtat(0);
                                            $this->em->persist($detail);
                                            $this->em->flush();
                                            $order++;
                                            $codeStatut="OK";
                                        } catch (\Exception $e) {
                                            $codeStatut="ERROR";
                                        }
                                    }
                                }else{
                                    $codeStatut="EMPTY_FILE";
                                }
                            }else{
                                $codeStatut=$fileCheckAdresse["codeStatut"] ;
                            }
                        }else{
                            $codeStatut="EMPTY_FILE";
                        }
                    }
                }

                if($codeStatut=="OK"){
                    if($request->get("email_in_step") == "1"){
                        if(!empty($_FILES['email_file']['name'])){
                            $requiredHeaders=array();
                            $file = $_FILES['email_file'];
                            $fileCheckEmail=$this->FileService->checkFile($file);
                            if($fileCheckEmail["codeStatut"] == "OK" ){
                                $ptf_ = $donneurRepo->getOnePtf($ptf);
                                //Check donneurordre
                                $nom = $fileCheckEmail["nom"];
                                $extension_upload=$fileCheckEmail["extension_upload"];
                                $fileTmpLoc=$fileCheckEmail["fileTmpLoc"];
                                $details_model_email  = json_decode($request->get("details_model_email"), true);
                                $model_import = $integrationRepo->createModel($details_model_email , "email");
                                if($model_import){
                                    //Create import debiteur
                                    $import = new Import();
                                    $import->setDateCreation(new \DateTime());
                                    $import->setEtat(0);
                                    $import->setIdModel($model_import);
                                    $import->setIdIntegration($integration);
                                    $import->setOrderImport(1);
                                    $import->setUrl("");
                                    $import->setType("email");
                                    $this->em->persist($import);
                                    $this->em->flush();
                                    //Import CR
                                    $filesystem = new Filesystem();
                                    $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/fichiers/import/';
                                    $folderPath = $publicDirectory . 'integration-num-'.$integration->getId()."/import-email-num-".$import->getId();
                                    $fileStore = "fichiers/import/integration-num-".$integration->getId() ."/import-email-num-".$import->getId()."/". $nom . '.' . $extension_upload;
                                    //----Create file if n'existe pas
                                    $filesystem->mkdir($folderPath);
                                    move_uploaded_file($fileTmpLoc, $fileStore);
                                    $import->setUrl($fileStore);
                                    $this->em->flush();
                                    $csvData = file_get_contents($import->getUrl());
                                    $rows = array_map('str_getcsv', explode("\n", $csvData));
                                    $numberOfFiles = 3;
                                    $rowsPerFile = ceil(count($rows) / $numberOfFiles);
                                    $chunks = array_chunk($rows, $rowsPerFile);
                                    $header = array_shift($rows);
                                    $import->setNbrLignes(count($rows) - 2);
                                    $this->em->flush();

                                    if (!is_dir($folderPath)) {
                                        mkdir($folderPath);
                                    }
                                    
                                }else{
                                    $codeStatut="EMPTY_FILE";
                                }
                            }else{
                                $codeStatut=$fileCheckAdresse["codeStatut"] ;
                            }
                        }else{
                            $codeStatut="EMPTY_FILE";
                        }
                    }
                }
                if($codeStatut=="OK"){
                    if($request->get("emploi_in_step") == "1"){
                        if(!empty($_FILES['emploi_file']['name'])){
                            $requiredHeaders=array();
                            $file = $_FILES['emploi_file'];
                            $fileCheckEmploi=$this->FileService->checkFile($file);
                            if($fileCheckEmploi["codeStatut"] == "OK" ){
                                // if($cin_debiteur == 1){
                                    $ptf_ = $donneurRepo->getOnePtf($ptf);
                                    //Check donneurordre
                                    $nom = $fileCheckEmploi["nom"];
                                    $extension_upload=$fileCheckEmploi["extension_upload"];
                                    $fileTmpLoc=$fileCheckEmploi["fileTmpLoc"];
                                    $details_model_emploi  = json_decode($request->get("details_model_emploi"), true);

                                    $model_import = $integrationRepo->createModel($details_model_emploi , "emploi");
                                    // $model_import =$this->em->getRepository(ModelImport::class)->findOneBy(["id"=>30]);
                                    if($model_import){
                                        //Create import debiteur
                                        $import = new Import();
                                        $import->setDateCreation(new \DateTime());
                                        $import->setEtat(0);
                                        $import->setIdModel($model_import);
                                        $import->setIdIntegration($integration);
                                        $import->setOrderImport(1);
                                        $import->setUrl("");
                                        $import->setType("emploi");
                                        $this->em->persist($import);
                                        $this->em->flush();
                                    
                                        //Import CR
                                        $filesystem = new Filesystem();
                                        $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/fichiers/import/';
                                        $folderPath = $publicDirectory . 'integration-num-'.$integration->getId()."/import-emploi-num-".$import->getId();
                                        $fileStore = "fichiers/import/integration-num-".$integration->getId() ."/import-emploi-num-".$import->getId()."/". $nom . '.' . $extension_upload;
                                        //----Create file if n'existe pas
                                        $filesystem->mkdir($folderPath);
                                        move_uploaded_file($fileTmpLoc, $fileStore);
                                        $import->setUrl($fileStore);
                                        $this->em->flush();
                                        $codeStatut="OK";
                                        $csvData = file_get_contents($import->getUrl());
                                        $rows = array_map('str_getcsv', explode("\n", $csvData));
                                        $numberOfFiles = 3;
                                        $rowsPerFile = ceil(count($rows) / $numberOfFiles);
                                        $chunks = array_chunk($rows, $rowsPerFile);
                                        $header = array_shift($rows);
                                        $import->setNbrLignes(count($rows) - 2);
                                        $this->em->flush();

                                        if (!is_dir($folderPath)) {
                                            mkdir($folderPath);
                                        }
                                        $order=1;
                                        for ($i = 0; $i < count($chunks); $i++) {
                                            $outputFileName = sprintf('output_file_%d.csv', $i + 1);
                                            $folderPath = "fichiers/import/integration-num-".$integration->getId()."/import-emploi-num-".$import->getId()."/split_files/";
                                            $filesystem->mkdir($folderPath);
                                            $outputFilePath = $folderPath . $outputFileName;

                                            if($order == 1){
                                                $outputCsvData = implode("\n", array_map('implode', $chunks[$i]));
                                            }else{
                                                // Convert header and data rows to strings
                                                $headerString = implode(",", $header);
                                                $dataStrings = array_map(function ($row) {
                                                    return implode(",", $row);
                                                }, $chunks[$i]);
                                                // Combine header and data rows as CSV lines
                                                $outputCsvLines = array_merge(array($headerString), $dataStrings);
                                                // Convert the CSV lines to a single CSV string
                                                $outputCsvData = implode("\n", $outputCsvLines);
                                            }
                                            try {
                                                // Move the file to the public/split_files directory
                                                file_put_contents($outputFilePath, $outputCsvData);
                                                $detail = new DetailsImport();
                                                $filesystem = new Filesystem();
                                                $detail->setUrl($outputFilePath);
                                                $detail->setIdImport($import);
                                                $detail->setEtat(0);
                                                $detail->setOrdre($order);
                                                $this->em->persist($detail);
                                                $this->em->flush();
                                                $order++;
                                                $codeStatut="OK";
                                            } catch (\Exception $e) {
                                                $codeStatut="ERROR";
                                            }
                                        }
                                        $codeStatut="OK";
                                    }else{
                                        $codeStatut="EMPTY_FILE";
                                    }
                                // }else{
                                //     $codeStatut="ID_DEBITEUR_IS_EMPTY";
                                // }
                            }else{
                                $codeStatut=$fileCheckDebiteur["codeStatut"] ;
                            }
                        }else{
                            $codeStatut="EMPTY_FILE";
                        }
                    }
                }
                if($codeStatut=="OK"){
                    if($request->get("employeur_in_step") == "1"){
                        if(!empty($_FILES['employeur_file']['name'])){
                            $requiredHeaders=array();
                            $file = $_FILES['employeur_file'];
                            $fileCheckEmployeur=$this->FileService->checkFile($file);
                            if($fileCheckEmployeur["codeStatut"] == "OK" ){
                                // if($num_creance == 1){
                                    $ptf_ = $donneurRepo->getOnePtf($ptf);
                                    //Check donneurordre
                                    $nom = $fileCheckEmployeur["nom"];
                                    $extension_upload=$fileCheckEmployeur["extension_upload"];
                                    $fileTmpLoc=$fileCheckEmployeur["fileTmpLoc"];
                                    $details_model_employeur  = json_decode($request->get("details_model_employeur"), true);

                                    $model_import = $integrationRepo->createModel($details_model_employeur , "employeur");
                                    // $model_import =$this->em->getRepository(ModelImport::class)->findOneBy(["id"=>30]);
                                    if($model_import){
                                        //Create import debiteur
                                        $import = new Import();
                                        $import->setDateCreation(new \DateTime());
                                        $import->setEtat(0);
                                        $import->setIdModel($model_import);
                                        $import->setIdIntegration($integration);
                                        $import->setOrderImport(1);
                                        $import->setUrl("");
                                        $import->setType("employeur");
                                        $this->em->persist($import);
                                        $this->em->flush();
                                    
                                        //Import CR
                                        $filesystem = new Filesystem();
                                        $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/fichiers/import/';
                                        $folderPath = $publicDirectory . 'integration-num-'.$integration->getId()."/import-employeur-num-".$import->getId();
                                        $fileStore = "fichiers/import/integration-num-".$integration->getId() ."/import-employeur-num-".$import->getId()."/". $nom . '.' . $extension_upload;
                                        //----Create file if n'existe pas
                                        $filesystem->mkdir($folderPath);
                                        move_uploaded_file($fileTmpLoc, $fileStore);
                                        $import->setUrl($fileStore);
                                        $this->em->flush();
                                        $codeStatut="OK";
                                        $csvData = file_get_contents($import->getUrl());
                                        $rows = array_map('str_getcsv', explode("\n", $csvData));
                                        $numberOfFiles = 3;
                                        $rowsPerFile = ceil(count($rows) / $numberOfFiles);
                                        $chunks = array_chunk($rows, $rowsPerFile);
                                        $header = array_shift($rows);
                                        $import->setNbrLignes(count($rows) - 2);
                                        $this->em->flush();

                                        if (!is_dir($folderPath)) {
                                            mkdir($folderPath);
                                        }
                                        $order=1;
                                        for ($i = 0; $i < count($chunks); $i++) {
                                            $outputFileName = sprintf('output_file_%d.csv', $i + 1);
                                            $folderPath = "fichiers/import/integration-num-".$integration->getId()."/import-employeur-num-".$import->getId()."/split_files/";
                                            $filesystem->mkdir($folderPath);
                                            $outputFilePath = $folderPath . $outputFileName;

                                            if($order == 1){
                                                $outputCsvData = implode("\n", array_map('implode', $chunks[$i]));
                                            }else{
                                                // Convert header and data rows to strings
                                                $headerString = implode(",", $header);
                                                $dataStrings = array_map(function ($row) {
                                                    return implode(",", $row);
                                                }, $chunks[$i]);
                                                // Combine header and data rows as CSV lines
                                                $outputCsvLines = array_merge(array($headerString), $dataStrings);
                                                // Convert the CSV lines to a single CSV string
                                                $outputCsvData = implode("\n", $outputCsvLines);
                                            }
                                            try {
                                                // Move the file to the public/split_files directory
                                                file_put_contents($outputFilePath, $outputCsvData);
                                                $detail = new DetailsImport();
                                                $filesystem = new Filesystem();
                                                $detail->setUrl($outputFilePath);
                                                $detail->setIdImport($import);
                                                $detail->setEtat(0);
                                                $detail->setOrdre($order);
                                                $this->em->persist($detail);
                                                $this->em->flush();
                                                $order++;
                                                $codeStatut="OK";
                                            } catch (\Exception $e) {
                                                $codeStatut="ERROR";
                                            }
                                        }
                                        $codeStatut="OK";
                                    }else{
                                        $codeStatut="EMPTY_FILE";
                                    }
                                // }else{
                                //     $codeStatut="ID_DEBITEUR_IS_EMPTY";
                                // }
                            }else{
                                $codeStatut=$fileCheckDebiteur["codeStatut"] ;
                            }
                        }else{
                            $codeStatut="EMPTY_FILE";
                        }
                    }
                }
                if($codeStatut == "OK"){
                    $codeStatut = $this->createTableDBI($integration->getId() , $isMaj);
                    $codeStatut="OK";
                    $respObjects["data"] = $integration->getId();
                }
            }   
            else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
            if($codeStatut != "OK"){
                $processIntegration = $this->em->getRepository(ProcessIntegration::class)->findOneBy(["id"=>15]);
                $integration->setStatus($processIntegration);
            }
            $this->em->flush();

        }catch(\Exception $e){
            $processIntegration = $this->em->getRepository(ProcessIntegration::class)->findOneBy(["id"=>15]);
            $integration->setStatus($processIntegration);
            $this->em->flush();
            $codeStatut="ERROR";
            $respObjects["errr"] = $e->getMessage();
        }
        
        $respObjects["codeStatut"]=$codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }
    function remove_accents($str) {
        $str = mb_strtolower($str, 'UTF-8');
        $str = str_replace(
            ['á','à','ã','â','ä','é','è','ê','ë','í','ì','î','ï','ó','ò','õ','ô','ö','ú','ù','û','ü','ç','ñ'],
            ['a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','c','n'],
            $str
        );
        $str = preg_replace('/[^a-z0-9\s_]/', '', $str);
        return $str;
    }
    // #[Route('/createTableDBI', methods : ["POST"])]
    public function createTableDBI($id , $isMaj)
    // public function createTableDBI()
    {
        // $codeStatut= "ERROR";
        // $id=239;
        $isMaj = 0;
        // try{
            $import = $this->integrationRepo->getAllImportByIntegration($id);
            $maj = '';
            if($isMaj == 1){
                $maj = "maj_";
            }
            
            for ($i=0; $i <count($import) ; $i++) { 
                $tableName = 'debt_force_integration.'.$maj.$import[$i]['type'].'_'.$id;
                $idModel = $import[$i]['id_model_id'];
                $correColu = $this->em->getRepository(CorresColu::class)->findBy(['id_model_import'=>$idModel]);
                $params = '';
                for ($j=0; $j < count($correColu); $j++) { 
                    $liaison = " , ";
                    if($j == (count($correColu) - 1)){
                        $liaison = " ";
                    }
                    $name = $this->remove_accents($correColu[$j]->getColumnName());
                    
                    if( $correColu[$j]->getOriginChamp() == 1){
                        $params .=  $name.'  ' .$correColu[$j]->getIdColParams()->getTypeParam();
                        //TODO:$correColu[$j]->getIdColParams()->getTypeParam() is t s 
                        
                        if(strpos($correColu[$j]->getIdColParams()->getTypeParam(), 'VARCHAR') !== false) {
                            $params .= '  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ';
                        }
                    }else{
                        $modelAff = $this->em->getRepository(DetailModelAffichage::class)->findOneBy(['id'=>$correColu[$j]->getColumnTable()]);
                        $params .=  $name.'  ' .$modelAff->getTypeChamp() . ' ('.$modelAff->getLength().')' ;
                        if(strpos($modelAff->getTypeChamp(), 'VARCHAR') !== false) {
                            $params .= '  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ';
                        }
                    }
                    $params .= $liaison;
                }
                $sql = '
                CREATE TABLE '.$tableName.' (
                    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    '.$params.' , vide_champ VARCHAR(50) , log_action VARCHAR(1000) 
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                    ';  
                $this->integrationRepo->executeSQL($sql);
                $codeStatut = "OK";
            }
        // }catch(\Exception $e){
        //     $codeStatut="ERROR";
        //     $respObjects["err"] = $e->getMessage();
        // }
        $respObjects["codeStatut"]=$codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $codeStatut;
        // return $this->json($respObjects);
    }

    // #[Route('/sauvguardeDataCSV', methods : ["POST"])]
    // public function sauvguardeDataCSV()
    public function sauvguardeDataCSV($id)
    {
        $codeStatut= "ERROR";
        // $id =252;
        // try{
            //TODO:Check the entite
            $import = $this->integrationRepo->getAllImportByIntegration($id);

            for ($i=0; $i < count($import) ; $i++) { 
                $url = $import[$i]['url'];
                
                $file = new File($url);
                $realPath = str_replace("\\", "/", $file->getRealPath());
                // Open the CSV file for reading
                $fileHandle = fopen($realPath, 'r');
                $url = 'C:/Users/HP/Desktop/dt_force_work/ws_debt_force/public/'.$url.''; 
                

                $header = fgetcsv($fileHandle, 0, ';');
                fclose($fileHandle);
                $entiteCheck = '';
                $setForEntete = '';

                $liaison = " , ";
                for ($j=0; $j < count($header); $j++) { 
                    $corre = $this->em->getRepository(CorresColu::class)->findOneBy(['id_model_import'=>$import[$i]['id_model_id'] ,'column_name'=> $header[$j]]);
                    // dump($header[$j],$corre);
                    // dump($header);
                    
                    $entiteCheck .= '@'.$header[$j] .$liaison;
                    if($corre)
                    {
                        if($corre->getIdColParams() && $corre->getIdColParams()->getTypeParam() == 'date' ){
                            $setForEntete .= $header[$j] .' = STR_TO_DATE(@'.$header[$j] .', "%d/%m/%Y") '.$liaison;
                        }elseif ($corre->getIdColParams() && $corre->getIdColParams()->getTypeParam() == 'datetime' ){
                            $setForEntete .= $header[$j] .' = STR_TO_DATE(@'.$header[$j] .', "%d/%m/%Y") '.$liaison;
                        }else{
                            $setForEntete .= $header[$j] .' = @'.$header[$j] .' '.$liaison;
                        }
                    }
                }

                $sql = "LOAD DATA INFILE '$realPath' INTO TABLE debt_force_integration.".$import[$i]['type']."_".$id." FIELDS TERMINATED BY ';' ENCLOSED BY '\"' LINES TERMINATED BY '\n' IGNORE 2 ROWS
                (".rtrim($entiteCheck,' , ').")
                SET  ".$setForEntete."  vide_champ = '' "; 
                // dump($sql);
                $codeStatut = $this->integrationRepo->sauvguardeData($sql); //Entete not selected
                $codeStatut="OK";
            }

        // }catch(\Exception $e){
        //     $codeStatut="ERROR";
        //     $respObjects["err"] = $e->getMessage();
        // }
        return $codeStatut;
        // $respObjects["codeStatut"]=$codeStatut;
        // $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        // return $this->json($respObjects);
    }

    #[Route('/importData', methods : ["POST"])]
    public function importData(integrationRepo $integrationRepo ,ManagerRegistry $doctrine ,  SerializerInterface $serializer , Request $request): JsonResponse
    {
        $codeStatut= "ERROR";
        try{
            
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["errr"] = $e->getMessage();
        }
        $respObjects["codeStatut"]=$codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }

   

    #[Route('/importIntegration1', methods : ["POST"])]
    public function importIntegration(integrationRepo $integrationRepo ,ManagerRegistry $doctrine ,  SerializerInterface $serializer , Request $request): JsonResponse
    {
        $codeStatut= "ERROR";
        try{
            $ordre = 1;
            $this->startIntegrationToDBI($integrationRepo, $doctrine, $serializer, $request , $ordre);
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["errr"] = $e->getMessage();
        }
        $respObjects["codeStatut"]=$codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }
   
    #[Route('/importIntegration2', methods : ["POST"])]
    public function importIntegration2(integrationRepo $integrationRepo ,ManagerRegistry $doctrine ,  SerializerInterface $serializer , Request $request): JsonResponse
    {
        $codeStatut= "ERROR";
        try{
            $ordre = 2;
            $this->startIntegrationToDBI($integrationRepo, $doctrine, $serializer, $request , $ordre);
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["errr"] = $e->getMessage();
        }
        $respObjects["codeStatut"]=$codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }
    #[Route('/importIntegration3', methods : ["POST"])]
    public function importIntegration3(integrationRepo $integrationRepo ,ManagerRegistry $doctrine ,  SerializerInterface $serializer , Request $request): JsonResponse
    {
        $codeStatut= "ERROR";
        try{
            $ordre =3;
            $this->startIntegrationToDBI($integrationRepo, $doctrine, $serializer, $request , $ordre);
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["errr"] = $e->getMessage();
        }
        $respObjects["codeStatut"]=$codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }
    #[Route('/importToDBI', methods : ["POST"])]
    public function importToDBI(integrationRepo $integrationRepo ,ManagerRegistry $doctrine ,  SerializerInterface $serializer , Request $request): JsonResponse
    {
        ini_set('memory_limit','-1');
        ini_set('memory_size','-1');
        ini_set('max_execution_time','-1');
        $respObjects =array();
        $codeStatut = "ERROR";
        $emDbi = $doctrine->getManager('customer');

        try{
            $IntegrationNonCommencer = $integrationRepo->getAllInegrationByStatus2();
            
            if($IntegrationNonCommencer){
                for ($t=0; $t <count($IntegrationNonCommencer);$t++) {
                    try {
                        $porte_feuille = $IntegrationNonCommencer[$t]->getIdPtf()->getId(); 
                        $integrationId =  $IntegrationNonCommencer[$t]->getId();

                        
                        if($IntegrationNonCommencer[$t]->getStatus()->getId() == 2){
                            $sql = 'CALL debt_force_integration.PROC_ROOLBACK_DBI('.$integrationId.');';
                            $stmt = $integrationRepo->executeSQL($sql);

                            $sql="INSERT INTO `logs_actions_integration`( `id_integration`, `logs`, `date_creation`,`etat`) VALUES (".$integrationId.",'Relancer l\'intégration',now(),1)";
                            $stmt = $integrationRepo->executeSQL($sql);
                        }else{
                            $sql="INSERT INTO `logs_actions_integration`( `id_integration`, `logs`, `date_creation`,`etat`) VALUES (".$integrationId.",'Import dans la base d\'intégration',now(),1)";
                            $stmt = $integrationRepo->executeSQL($sql);
                        }

                        $sql="UPDATE `integration` SET `status_id` = '2' WHERE `integration`.`id` = ".$integrationId.";";
                        $stmt = $integrationRepo->executeSQL($sql);
                        
                        $IntegrationNonCommencer[$t]->setDateExecution(new \DateTime()); 
                        $emDbi->flush();
                        $this->sauvguardeDataCSV($integrationId);
                        $importByType = $integrationRepo->getOneImportType($integrationId , "debiteur");
                        $idModelDeb = $importByType->getIdModel()->getId();
                        
                        $a=new actionsImportDbi();
                        $a->setEtat(0);
                        $a->setCodeAction("Ajo_deb");
                        $a->setDateDebut(new \DateTime());
                        $a->setIdImport($importByType->getId());
                        $a->setTitre("Ajout");
                        $emDbi->persist($a);
                        $emDbi->flush();

                        $sql = 'CALL debt_force_integration.PROC_INSERT_DEB_DBI('.$integrationId.','.$importByType->getId().','.$importByType->getIdModel()->getId().','.$porte_feuille.',1,'.$a->getId().');';
                        dump($sql);
                        $stmt = $integrationRepo->executeSQL($sql);
                        $importByType = $integrationRepo->getOneImportType($integrationId , "dossier");
                        $a=new actionsImportDbi();
                        $a->setEtat(0);
                        $a->setCodeAction("Ajo_doss");
                        $a->setDateDebut(new \DateTime());
                        $a->setIdImport($importByType->getId());
                        $a->setTitre("Ajout");
                        $emDbi->persist($a);
                        $emDbi->flush();
                        $sql = 'CALL debt_force_integration.PROC_INSERT_DOSSIERS_DBI('.$integrationId.','.$importByType->getId().','.$importByType->getIdModel()->getId().','.$porte_feuille.',1,'.$a->getId().' , '.$idModelDeb.') ;';
                        dump($sql);
                        $stmt = $integrationRepo->executeSQL($sql);
                        $importByType = $integrationRepo->getOneImportType($integrationId , "creance");
        
                        $a=new actionsImportDbi();
                        $a->setEtat(0);
                        $a->setCodeAction("Ajo_creance");
                        $a->setDateDebut(new \DateTime());
                        $a->setIdImport($importByType->getId());
                        $a->setTitre("Ajout");
                        $emDbi->persist($a);
                        $emDbi->flush();
        
                        $sql = 'CALL debt_force_integration.PROC_INSERT_CREANCE_DBI('.$integrationId.','.$importByType->getId().','.$importByType->getIdModel()->getId().','.$porte_feuille.',1,'.$a->getId().' , '.$idModelDeb.'); ';                            
                        dump($sql);
                        $stmt = $integrationRepo->executeSQL($sql);
                        //TODO:Dossier 

                        //Emploi
                        $importByType = $integrationRepo->getOneImportType($integrationId , "emploi");
                        if($importByType){
                            $a=new actionsImportDbi();
                            $a->setEtat(0);
                            $a->setCodeAction("Ajo_emploi");
                            $a->setDateDebut(new \DateTime());
                            $a->setIdImport($importByType->getId());
                            $a->setTitre("Ajout");
                            $emDbi->persist($a);
                            $emDbi->flush();
            
                            $sql = 'CALL debt_force_integration.PROC_INSERT_EMPLOI_DBI('.$integrationId.','.$importByType->getId().','.$importByType->getIdModel()->getId().','.$porte_feuille.',1,'.$a->getId().' , '.$idModelDeb.'); ';
                            $stmt = $integrationRepo->executeSQL($sql);
                        }
        
                        $importByType = $integrationRepo->getOneImportType($integrationId , "employeur");
                        if($importByType){
                            $a=new actionsImportDbi();
                            $a->setEtat(0);
                            $a->setCodeAction("Ajo_employeur");
                            $a->setDateDebut(new \DateTime());
                            $a->setIdImport($importByType->getId());
                            $a->setTitre("Ajout");
                            $emDbi->persist($a);
                            $emDbi->flush();
            
                            $sql = 'CALL debt_force_integration.PROC_INSERT_EMPLOYEUR_DBI('.$integrationId.','.$importByType->getId().','.$importByType->getIdModel()->getId().','.$porte_feuille.',1,'.$a->getId().' , '.$idModelDeb.'); ';
                            $stmt = $integrationRepo->executeSQL($sql);
                        }

                        $importByType = $integrationRepo->getOneImportType($integrationId , "proc");
                        if($importByType){
                            $a=new actionsImportDbi();
                            $a->setEtat(0);
                            $a->setCodeAction("Ajo_proc");
                            $a->setDateDebut(new \DateTime());
                            $a->setIdImport($importByType->getId());
                            $a->setTitre("Ajout");
                            $emDbi->persist($a);
                            $emDbi->flush();
            
                            $sql = 'CALL debt_force_integration.PROC_INSERT_PROC_JUDU_DBI('.$integrationId.','.$importByType->getId().','.$importByType->getIdModel()->getId().','.$porte_feuille.',1,'.$a->getId().' , '.$idModelDeb.'); ';
                            $stmt = $integrationRepo->executeSQL($sql);
                        }
        
                        $importByType = $integrationRepo->getOneImportType($integrationId , "garantie");
                        if($importByType){
                            $a=new actionsImportDbi();
                            $a->setEtat(0);
                            $a->setCodeAction("Ajo_garantie");
                            $a->setDateDebut(new \DateTime());
                            $a->setIdImport($importByType->getId());
                            $a->setTitre("Ajout");
                            $emDbi->persist($a);
                            $emDbi->flush();

                            $sql = 'CALL debt_force_integration.PROC_INSERT_GARANTIE_DBI('.$integrationId.','.$importByType->getId().','.$importByType->getIdModel()->getId().','.$porte_feuille.',1,'.$a->getId().' , '.$idModelDeb.'); ';
                            $stmt = $integrationRepo->executeSQL($sql);
                        }

                        $importByType = $integrationRepo->getOneImportType($integrationId , "telephone");
                        if($importByType){
                            $a=new actionsImportDbi();
                            $a->setEtat(0);
                            $a->setCodeAction("Ajo_telephone");
                            $a->setDateDebut(new \DateTime());
                            $a->setIdImport($importByType->getId());
                            $a->setTitre("Ajout");
                            $emDbi->persist($a);
                            $emDbi->flush();

                            $sql = 'CALL debt_force_integration.PROC_INSERT_TEL_DBI('.$integrationId.','.$importByType->getId().','.$importByType->getIdModel()->getId().','.$porte_feuille.',1,'.$a->getId().' , '.$idModelDeb.'); ';
                            dump($sql);
                            $stmt = $integrationRepo->executeSQL($sql);
                        }

                        $importByType = $integrationRepo->getOneImportType($integrationId , "adresse");
                        if($importByType){
                            $a=new actionsImportDbi();
                            $a->setEtat(0);
                            $a->setCodeAction("Ajo_adresse");
                            $a->setDateDebut(new \DateTime());
                            $a->setIdImport($importByType->getId());
                            $a->setTitre("Ajout");
                            $emDbi->persist($a);
                            $emDbi->flush();
            
                            $sql = 'CALL debt_force_integration.PROC_INSERT_ADRESSE_DBI('.$integrationId.','.$importByType->getId().','.$importByType->getIdModel()->getId().','.$porte_feuille.',1,'.$a->getId().' , '.$idModelDeb.'); ';
                            
                            $stmt = $integrationRepo->executeSQL($sql);
                        }
                        $importByType = $integrationRepo->getOneImportType($integrationId , "email");
                        if($importByType){
                            $a=new actionsImportDbi();
                            $a->setEtat(0);
                            $a->setCodeAction("Ajo_email");
                            $a->setDateDebut(new \DateTime());
                            $a->setIdImport($importByType->getId());
                            $a->setTitre("Ajout");
                            $emDbi->persist($a);
                            $emDbi->flush();
                            $sql = 'CALL debt_force_integration.PROC_INSERT_EMAIL_DBI('.$integrationId.','.$importByType->getId().','.$importByType->getIdModel()->getId().','.$porte_feuille.',1,'.$a->getId().' , '.$idModelDeb.'); ';
                            dump($sql);
                            $stmt = $integrationRepo->executeSQL($sql);
                        }

                        $sql="update integration set status_id = 4 , `date_fin_execution_1`=now() where id = ".$IntegrationNonCommencer[$t]->getId()."";
                        $stmt = $this->conn->prepare($sql)->executeQuery();
                        $sql="INSERT INTO `logs_actions_integration`( `id_integration`, `logs`, `date_creation`,`etat`) VALUES (".$integrationId.",'Intégration terminée',now(),1)";
                        $stmt = $integrationRepo->executeSQL($sql);
                        $codeStatut="OK";

                    } catch (\Exception $e) {
                        $sql="UPDATE `integration` SET `status_id` = '3' WHERE `integration`.`id` = ".$integrationId.";";
                        $stmt = $integrationRepo->executeSQL($sql);
                        $sql="INSERT INTO `logs_actions_integration`( `id_integration`, `logs`, `date_creation`,`etat`) VALUES (".$integrationId.",'".$e->getMessage()."',now(),0)";
                        $stmt = $integrationRepo->executeSQL($sql);
                    }
                }
            }
        }
        catch(\Exception $e){
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/roolbackImport', methods : ["POST"])]
    public function roolbackImport(integrationRepo $integrationRepo ,ManagerRegistry $doctrine ,  SerializerInterface $serializer , Request $request): JsonResponse
    {
        $codeStatut= "ERROR";
        try{
            $IntegrationNonCommencer = $integrationRepo->getAllInegrationByStatus(10);
            if($IntegrationNonCommencer){
                for ($t=0; $t <count($IntegrationNonCommencer);$t++) {
                    $integrationId =  $IntegrationNonCommencer[$t]->getId();
                    $sql = 'CALL debt_force_integration.PROC_ROOLBACK('.$integrationId.'); ';
                    $stmt = $integrationRepo->executeSQL($sql);
                    $sql="update integration set status_id = 14  where id = ".$integrationId."";
                    $stmt = $this->conn->prepare($sql)->executeQuery();
                    $codeStatut="OK";
                }
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["errr"] = $e->getMessage();
        }
        $respObjects["codeStatut"]=$codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }

    #[Route('/approvalImport', methods : ["POST"])]
    public function approvalImport(integrationRepo $integrationRepo ,ManagerRegistry $doctrine ,  SerializerInterface $serializer , Request $request): JsonResponse
    {
        $codeStatut= "ERROR";
        try{
            $IntegrationNonCommencer = $integrationRepo->getAllInegrationByStatus(9);
            if($IntegrationNonCommencer){
                for ($t=0; $t <count($IntegrationNonCommencer);$t++) {
                    $integrationId =  $IntegrationNonCommencer[$t]->getId();
                    $sql = 'CALL debt_force_integration.PROC_APPROVAL('.$integrationId.'); ';
                    $stmt = $integrationRepo->executeSQL($sql);
                    $sql="update integration set status_id = 17  where id = ".$integrationId."";
                    $stmt = $this->conn->prepare($sql)->executeQuery();
                    $codeStatut="OK";
                }
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["errr"] = $e->getMessage();
        }
        $respObjects["codeStatut"]=$codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }
    #[Route('/getListeImport/{id}')]
    public function getListeImport(integrationRepo $integrationRepo ,$id,ManagerRegistry $doctrine ,  SerializerInterface $serializer , Request $request): JsonResponse
    {
        $codeStatut= "ERROR";
        try{
            $IntegrationNonCommencer = $integrationRepo->getAllImportByIntegration($id);
            $respObjects["data"] = $IntegrationNonCommencer;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["errr"] = $e->getMessage();
        }
        $respObjects["codeStatut"]=$codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }

    #[Route('/exportLogImport/{id}')]
    public function exportLogImport(IntegrationRepo $integrationRepo, $id, ManagerRegistry $doctrine, SerializerInterface $serializer, Request $request)
    {
        // Create a temporary directory to store CSV files
        $tempDir = sys_get_temp_dir() . '/' . uniqid('export_', true);
        if (!mkdir($tempDir) && !is_dir($tempDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $tempDir));
        }
    
        // Initialize a zip archive
        $zipFile = tempnam(sys_get_temp_dir(), 'exports');
        $zip = new ZipArchive();
        $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    
        $imports = $integrationRepo->getAllImportByIntegration($id);

        foreach ($imports as $import) {
            $type = $import["type"];
            $tableName = 'debt_force_integration.'.$type.'_'.$id;
            $sql = "SELECT * FROM ".$tableName;
            
            // Prepare and execute SQL query
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchAllAssociative();
            
            // Filter out the 'vide_champ' column
            $dataWithoutVideChamp = array_map(function ($row) {
                return array_filter($row, function ($value, $key) {
                    return $key !== 'vide_champ';
                }, ARRAY_FILTER_USE_BOTH); // Use both key and value for array_filter
            }, $data);
        
            // Create a CSV file for this import
            $csvFileName = $type."_".$id.'.csv';
            $csvFilePath = $tempDir . '/' . $csvFileName;
            // $csvFile = fopen($csvFilePath, 'w');
            $csvFile = fopen($csvFilePath, 'w', false, stream_context_create(['ftp' => ['encoding' => 'UTF-8']]));
            
            fwrite($csvFile, "\xEF\xBB\xBF");

            // Write CSV headers
            fputcsv($csvFile, array_keys($dataWithoutVideChamp[0]), ';');
        
            // Write CSV data
            foreach ($dataWithoutVideChamp as $row) {
                fputcsv($csvFile, $row, ';');
            }
        
            fclose($csvFile);
        
            // Add the CSV file to the zip archive
            $zip->addFile($csvFilePath, $csvFileName);
        }
        
        // Close the zip archive
        $zip->close();
    
        // Remove temporary directory
        foreach (glob($tempDir . '/*') as $file) {
            unlink($file);
        }
        rmdir($tempDir);
    
        // Create a response containing the zip file
        $response = new StreamedResponse(function () use ($zipFile) {
            readfile($zipFile);
        });
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'integration_log_'.$id.'.zip'
        ));
    
        return $response;
    }
    

    #[Route('/test_cron', methods : ["POST"])]
    public function test_cron(integrationRepo $integrationRepo ,ManagerRegistry $doctrine ,  SerializerInterface $serializer , Request $request): JsonResponse
    {
        ini_set('memory_limit','-1');
        ini_set('memory_size','-1');
        ini_set('max_execution_time','-1');
        $respObjects =array();
        $codeStatut = "ERROR";
        $emDbi = $doctrine->getManager('customer');
        try{
            $sql="INSERT INTO `adresse` (`id`, `id_debiteur_id`, `id_type_adresse_id`, `adresse_complet`, `pays`, `ville`, `verifier`, `code_postal`, `province`, `source`, `region`, `origine`, `id_status_id`) VALUES (NULL, '159', '1', '', NULL, NULL, '0', '', '', NULL, NULL, '', '1');";
            $stmt = $integrationRepo->executeSQL($sql);
        }
        catch(\Exception $e){
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);

        return $this->json($respObjects);
    }
    // #[Route('/startIntegrationToDBI1', methods : ["POST"])]
    public function startIntegrationToDBI(integrationRepo $integrationRepo ,ManagerRegistry $doctrine ,  SerializerInterface $serializer , Request $request , $ordre): JsonResponse
    {
        ini_set('memory_limit','-1');
        ini_set('memory_size','-1');
        ini_set('max_execution_time','-1');
        
        $countv=0;
        $countnv=0;
        $cr = 0;
        $dispatch1=array();
        $tableDispatch=array();

        $emDbi = $doctrine->getManager('customer');
        $respObjects =array();
        $codeStatut = "ERROR";
        $IntegrationNonCommencer = $integrationRepo->getAllInegrationByStatus(1);

        if($IntegrationNonCommencer){
            for ($t=0; $t <count($IntegrationNonCommencer);$t++) { 
                // $this->em->flush();
                $porte_feuille = $IntegrationNonCommencer[$t]->getIdPtf(); 
                $integrationId =  $IntegrationNonCommencer[$t]->getId();
                $importByType = $integrationRepo->getOneImportType($integrationId , "debiteur");
                //Etat en cours
                $importByType->setEtat(2);
                $this->em->flush();
                $id_import = $importByType->getId();
                $detailsImp = $integrationRepo->getDetailsImprt($id_import , $ordre);
                //grouping les tables by table_bdd 
                $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where  t.id
                    in(select (c.id_col_params) from  App\Entity\CorresColu  c where  c.id_model_import=:idSchema and c.origin_champ = 1
                     and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type) )group by t.table_bdd');
                $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                $query->setParameter('type','debiteur');
                $tables = $query->getResult();
                $tables_debiteur = $tables;

                $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where t.origin_champ = 1 
                 and (t.id_model_import)=:idSchema and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type)');
                $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                $query->setParameter('type','debiteur');
                $colFile = $query->getResult();

                $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where t.origin_champ = 2 
                and (t.id_model_import)=:idSchema and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type)');
                $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                $query->setParameter('type','debiteur');
                $colFileC = $query->getResult();

                $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                from App\Entity\CorresColu c where (c.id_model_import)=:idSchema and c.origin_champ = 1  and  c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type))');
                $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                $query->setParameter('type','debiteur');
                $tableDb = $query->getResult();

                $query = $this->em->createQuery('SELECT t.id from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                from App\Entity\CorresColu c where (c.id_model_import)=:idSchema and c.origin_champ = 1  and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type))');
                $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                $query->setParameter('type','debiteur');
                $colDb = $query->getResult();

                $param=array();
                $val=array();

                $response="";
                
                $filePath = $detailsImp->getUrl();
                
                if (($handle = fopen($filePath, "r")) !== FALSE)
                {
                    while (($data = fgetcsv($handle, 1000000, ";")) !== FALSE)
                    {
                        break;
                    }
                    //Les entétes de fichier excel
                    $data=array_map("utf8_encode",$data);
                    $data=array_map('trim', $data);
                    $data1=array_map("utf8_encode",str_replace(" ","_",$data));
                    
                    for ($i=0; $i < count($colFile); $i++) { 
                        if(!in_array($colFile[$i]["column_name"],$data1))
                        {
                            $response="ENTETE_IDENTIQUE";
                            $codeStatut="ENTETE_IDENTIQUE";
                        }
                    }

                    if($response == ""){
                        $a=new actionsImportDbi();
                        $a->setEtat(0);
                        $a->setCodeAction("Ajo");
                        $a->setDateDebut(new \DateTime());
                        $a->setIdImport($importByType->getId());
                        $a->setTitre("Ajout");
                        $emDbi->persist($a);
                        $emDbi->flush();

                        $isCinDeb = $integrationRepo->isCinDeb($importByType->getIdModel()->getId());

                        // $a = $this->integrationRepo->createActionsImport

                        $importByType->setEtat(2);
                        $this->em->flush(); 

                        foreach($tables as $ta)
                        {
                            $val[$ta["table_bdd"]]="";
                            // if($ta["table_bdd"]=="debiteur")
                            if($ta["table_bdd"]=="debiteur")
                            {
                                $val[$ta["table_bdd"]].="cin_formate";
                            }
                            for ($i = 0; $i < count($colFile); $i++)
                            {   
                                if(($colFile[$i]["origine"] == 0)){//TODO:Test si séparer par la déclaration dans le fichier
                                    $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                    if ($colParam->getTableBdd() == $ta["table_bdd"] and $colParam->getTitreCol()!="cin_gestionnaire")
                                    {
                                        $val[$ta["table_bdd"]].=",".$colParam->getTitreCol();
                                    }
                                }
                            }
                            if($ta["table_bdd"]=="creance")
                            {
                                if(!$this->in_array_r(5,$colDb))
                                {
                                    $val[$ta["table_bdd"]].=",numero_creance";
                                }
                            }
                        }
                        
                        $data=$this->convert($filePath,";");
                        
                        $valCinF=array();
                        $valCin=array();
                        foreach ($tables as $ta){
                            if($data){
                                $countRow=0;
                                foreach ($data as $row)
                                {
                                    $valCin[$countRow]="";
                                    $valCinF[$countRow]="";
                                    $numCreance[$countRow]="";
                                    $principale[$countRow]="";
                                    $frais[$countRow]="";
                                    $interet[$countRow]="";
                                    $type_deb[$countRow]="";
                                    $nom_deb[$countRow]="";
                                    $prenom_deb[$countRow]= "";
                                    $rs_deb[$countRow]= "";
                                    $id_debiteur[$countRow]= "";
                                    $date_naissance[$countRow]= "";
                                    $numDossier[$countRow]="";
                                
                                    $valRaison[$countRow]="";
                                    $cinGest[$countRow]="";
                                    $param[$ta["table_bdd"]][$countRow]="";

                                    $champ[$countRow]="";
                                    for ($i=0; $i <count($colFileC) ; $i++) 
                                    { 
                                        $champ[$countRow."-".$i] = $row[$colFileC[$i]["column_name"]];
                                    }
                                    if(!$isCinDeb){
                                        $param[$ta["table_bdd"]][$countRow]=",''";
                                    }
                                    for ($i = 0; $i < count($colFile); $i++)
                                    {
                                        if($colDb[$i]["id"]==75 and $row[$colFile[$i]["nomCol"]]!="")
                                        {
                                            $gest = $this->em->getRepository(Utilisateurs::class)->findOneBy(["cin"=>$row[$colFile[$i]["nomCol"]]]);
                                            if($gest)
                                            {
                                                $cinGest[$countRow]=$gest->getId();
                                            }
                                        }
                                        if($colDb[$i]["id"]==23)
                                        {
                                            if(isset($row[$colFile[$i]["column_name"]])=="")
                                            {
                                                for ($r = 0; $r < count($colFile); $r++)
                                                {
                                                    if($colDb[$r]["id"]==144)
                                                    {
                                                        $row[$colFile[$i]["column_name"]]=$row[$colFile[$r]["column_name"]];
                                                    }
                                                }
                                            }
                                            $cin=$row[$colFile[$i]["column_name"]];
                                            $valCin[$countRow]=$cin;
                                            $valCinF[$countRow]=strtoupper(preg_replace('/[^A-Za-z0-9]+/',"", $cin));
                                            if(!$this->in_array_r(5,$colDb))
                                            {
                                                $numCreance[$countRow]="Sys".$valCinF[$countRow]."-".($countRow+1);
                                            }
                                        }
                                        if($colDb[$i]["id"]==179)
                                        {
                                            $type_deb[$countRow]=$row[$colFile[$i]["column_name"]];
                                        }
                                        if($colDb[$i]["id"]==24)
                                        {
                                            $nom_deb[$countRow]=$row[$colFile[$i]["column_name"]];
                                        }
                                        if($colDb[$i]["id"]==25)
                                        {
                                            $prenom_deb[$countRow]=$row[$colFile[$i]["column_name"]];
                                        }
                                        if($colDb[$i]["id"]==37)
                                        {
                                            $rs_deb[$countRow]=$row[$colFile[$i]["column_name"]];
                                        }
                                        if($colDb[$i]["id"]==185)
                                        {
                                            $id_debiteur[$countRow]=$row[$colFile[$i]["column_name"]];
                                        }
                                        if($colDb[$i]["id"]==15)
                                        {
                                            $row[$colFile[$i]["column_name"]]=str_replace(" ","",$row[$colFile[$i]["column_name"]]);
                                        }
                                        if($colDb[$i]["id"]==65)
                                        {
                                            $numDossier[$countRow]=$row[$colFile[$i]["column_name"]];
                                        }
                                        if($colDb[$i]["id"]==167)
                                        {
                                            $principale[$countRow]=$row[$colFile[$i]["column_name"]];
                                        }
                                        if($colDb[$i]["id"]==28)
                                        {
                                            $date = $row[$colFile[$i]["column_name"]];
                                            $date_naissance[$countRow] = strtoupper(preg_replace('/[^A-Za-z0-9]+/', "", $date));
                                        }
                                        if($colDb[$i]["id"]==168)
                                        {
                                            $frais[$countRow]=$row[$colFile[$i]["column_name"]];
                                        }
                                        if($colDb[$i]["id"]==169)
                                        {
                                            $interet[$countRow]=$row[$colFile[$i]["column_name"]];
                                        }
                                        $cle[$countRow] = $valCinF[$countRow]."".$nom_deb[$countRow]."".$prenom_deb[$countRow]."".$rs_deb[$countRow]."".$date_naissance[$countRow];
                                        
                                        $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                        if ($colParam->getTableBdd() == $ta["table_bdd"])
                                        {
                                            $isDate = $this->em->getRepository(ColumnsParams::class)->find($colDb[$i]["id"]);
                                            if ($isDate->getIsDate() == 1 and \DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])==true)
                                            {
                                                $valueDate=\DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])->format("d-m-Y");
                                                $param[$ta["table_bdd"]][$countRow].= ",'" . date_format(new \DateTime($valueDate), 'Y-m-d H:i:s') . "'";
                                            }
                                            else
                                            {
                                                if($colDb[$i]["id"]!=75 && $colDb[$i]["id"]!=179 && $colDb[$i]["id"]!=24 && $colDb[$i]["id"]!=185 )
                                                {
                                                    $param[$ta["table_bdd"]][$countRow].= ",'" . $row[$colFile[$i]["column_name"]] . "'";
                                                }
                                            }
                                            if($colFile[$i]["required"] == 1 and empty($row[$colFile[$i]["column_name"]]))
                                            {
                                                unset($param[$ta["table_bdd"]][$countRow]);
                                            }
                                        }
                                    }

                                    if($ta["table_bdd"]=="creance" and $this->in_array_r("creance",$tableDb))
                                    {
                                        if(!$this->in_array_r(5,$colDb))
                                        {
                                            $param[$ta["table_bdd"]][$countRow].= ",'Sys" . $valCinF[$countRow] . "-".($countRow+1)."'";
                                        }
                                    }
                                    $countRow++;
                                }
                            }
                        }
                        $model = $importByType->getIdModel()->getId();
                        $query = $this->em->createQuery('SELECT t from App\Entity\CorresColu t where t.id_model_import = :idSchema 
                        and t.id_col_params is null 
                        and t.id_model_import in (select m.id from App\Entity\ModelImport m where m.type = :type) ');
                        $query->setParameter('idSchema', $model);
                        $query->setParameter('type','debiteur');
                        $champPersonalise = $query->getResult();
                        if(count($val)>0 and count($param)>0)
                        {
                            foreach ($tables as $ta)
                            {
                                $nameTable="debiteur";
                                if($ta["table_bdd"]==$nameTable)
                                {
                                    $lastKey = key(array_slice($param[$ta["table_bdd"]], -1, 1, true));
                                    for($i=0;$i<($lastKey+1);$i++)
                                    {
                                        if(array_key_exists($i,$param[$ta["table_bdd"]]) )
                                        {   
                                            if($this->integrationRepo->findTypeDeb(($type_deb[$i])))
                                            {
                                                if(!empty($nom_deb[$i]) or !empty($prenom_deb[$i]) or !empty($rs_deb[$i]))
                                                {
                                                    if($this->integrationRepo->testDebiteurCle($cle[$i])["exist"]==false)
                                                    {
                                                        // try {
                                                            $type_presone = "Personne morale";
                                                            if($nom_deb[$i] != "" || $prenom_deb[$i] != "")
                                                            {
                                                                $type_presone = "Personne physique";
                                                            }
                                                            $sql="insert into debt_force_integration.debiteur_dbi(cle_identifiant,cin,nom,prenom,raison_social,type_personne,id_debiteur,id_import,id_integration) values('".$cle[$i]."','".$valCinF[$i]."','".$nom_deb[$i]."','".$prenom_deb[$i]."','".$rs_deb[$i]."','".$type_presone."','".$id_debiteur[$i]."',".$id_import.",".$integrationId.")";
                                                            $stmt = $this->conn->prepare($sql)->executeQuery();
                                                            if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Débiteur ajouté')==true)
                                                            {
                                                                $countv++;
                                                            }
                                                            $deb1 = $integrationRepo->testDebiteurIdDeb($id_debiteur[$i],$integrationId);
                                                            if($deb1["exist"]){
                                                                $sql="insert into `debt_force_integration`.`type_debiteur_dbi`(id_debiteur_id,id_creance_id,type,origin_deb,origin_creance,id_integration,numero_dossier,id_debiteur) values(".$deb1["deb"].",0,".$type_deb[$i].",".$deb1["place"].",0,".$integrationId.",'".$numDossier[$i]."',".$id_debiteur[$i].")";
                                                                $this->em->getConnection()->prepare($sql)->execute();
                                                            }
                                                            if($champPersonalise)
                                                            {
                                                                for ($c=0; $c < count($champPersonalise); $c++) { 
                                                                    $sql=" INSERT INTO `debt_force_integration`.`champs_dbi`(`id_details_model`, `colum_name`, `value`, `id_import`, `id_champ`) VALUES ('".$champPersonalise[$c]->getColumnTable()."','','".$champ[$i."-".$c]."',".$id_import.",'1')";
                                                                    $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                }
                                                            }
                                                        // }
                                                        // catch (\Exception $e) {
                                                        //     if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),$e->getMessage())==true)
                                                        //     {
                                                        //         $countnv++;
                                                        //     }
                                                        // }
                                                    }else{
                                                        // $debiteur = $this->em->getRepository(Debiteur::class)->findOneBy(["cin_formate"=>$valCinF[$i]]);
                                                        if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),"Le débiteur avec cin ".$valCinF[$i]." déja existe !!")==true)
                                                        {
                                                            $countnv++;
                                                        }
                                                    }
                                                }
                                                else
                                                {
                                                    if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),"Nom débiteur ou rc est oubliguatoire !!")==true)
                                                    {
                                                        $countnv++;
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),"Type débiteur ".$type_deb[$i]." n'existe pas !!")==true)
                                                {
                                                    $countnv++;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            foreach ($tables as $ta)
                            {
                                $nameTable="dossier";
                                if($ta["table_bdd"]==$nameTable)
                                {
                                    $lastKey = key(array_slice($param[$ta["table_bdd"]], -1, 1, true));
                                    for($i=0;$i<($lastKey+1);$i++)
                                    {
                                        if(array_key_exists($i,$param[$ta["table_bdd"]]) )
                                        {
                                            /*if($this->in_array_r(65,$colDb)==false)
                                            {
                                                try {
                                                    //Génerer le redDossier
                                                    $sql="SELECT count(d.id) FROM `debt_force_integration`.`dossier_dbi` d where d.id_import = :id";
                                                    $stmt = $this->conn->prepare($sql);
                                                    $stmt->bindValue(":id",$id_import);
                                                    $stmt = $stmt->executeQuery();
                                                    $countDossDbi = $stmt->fetchOne();

                                                    $query = $this->em->createQuery('SELECT max(t.id) from App\Entity\Dossier t');
                                                    $maxNum = $query->getSingleScalarResult();
                                                    if(!$maxNum)
                                                    {
                                                        $maxNum=0;
                                                    }
                                                    $maxNum = $countDossDbi + $maxNum;
                                                    
                                                    $refDossier=$porte_feuille->getNumeroPtf()."-".($maxNum+1);
                                                    $numDossier[$i]=$refDossier;
                                                    if($this->integrationRepo->testDossierDbi($refDossier,$porte_feuille->getId())==false){
                                                        if($cinGest[$i]!=""){
                                                            $sql="insert into `debt_force_integration`.`dossier_dbi`(date_creation,numero_dossier,id_users,id_ptf".$val[$ta["table_bdd"]].",id_import) values(now(),'".$refDossier."','".$cinGest[$i]."',".$porte_feuille->getId().$param[$ta["table_bdd"]][$i].",".$id_import.")";
                                                        }else{
                                                            $sql="insert into `debt_force_integration`.`dossier_dbi`(date_creation,numero_dossier,id_ptf".$val[$ta["table_bdd"]].",id_import) values(now(),'".$refDossier."',".$porte_feuille->getId().$param[$ta["table_bdd"]][$i].",".$id_import.")";
                                                        }dump($sql);
                                                        $stmt = $this->conn->prepare($sql)->executeQuery();                                                        
                                                        // if($cinGest[$i]!=""){
                                                        //     $query = $this->em->createQuery('SELECT max(t.id) from App\Entity\Dossier t');
                                                        //     $dossier = $query->getSingleScalarResult();
                                                        //     $tableDispatch[$cr]=$dossier;
                                                        //     $cr++;
                                                        // }
                                                        $idDeb = $this->integrationRepo->testDebiteurDbi1($valCinF[$i]);
                                                        if(!$idDeb["exist"])
                                                        {
                                                            if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Debiteur avec cin '.$valCinF[$i].' n\'existe pas !!')==true)
                                                            {
                                                                $countnv++;
                                                            }
                                                        }
                                                        else
                                                        {
                                                            $dossier = $this->integrationRepo->testDossDbi1($numDossier[$i],$porte_feuille->getId());
                                                            $typeDeb = $this->integrationRepo->testDebDoss($idDeb["deb"],$dossier["dossier"]);
                                                            if(!$typeDeb["exist"])
                                                            {
                                                                if(array_key_exists($i,$numDossier) and $numDossier[$i]!="")
                                                                {
                                                                    $qualite="1";
                                                                    $c=$dossier;
                                                                    if($c)
                                                                    {
                                                                        $c=",".$c["dossier"];
                                                                        $origin_dossier=$dossier["place"];
                                                                        $origin_deb=$idDeb["place"];
                                                                        $sql="insert into `debt_force_integration`.`debi_doss_dbi`(id_debiteur_id,id_dossier_id,origin_deb,origin_doss) values(".$idDeb["deb"].$c.",".$origin_deb.",".$origin_dossier.")";
                                                                    }
                                                                }
                                                                $this->em->getConnection()->prepare($sql)->execute();
                                                            }
                                                        }
                                                        if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Dossier ajouté')==true)
                                                        {
                                                            $countv++;
                                                        }
                                                    }else{
                                                        if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Dossier n° '.$refDossier.' existe déjà')==true)
                                                        {
                                                            $countnv++;
                                                        }
                                                    }
                                                }catch (\Exception $e) {
                                                    if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),$e->getMessage())==true)
                                                    {
                                                        $countnv++;
                                                    }
                                                }
                                            }else{*/
                                            try {
                                                if($this->integrationRepo->testDossierDbi($numDossier[$i],$porte_feuille->getId())==false){
                                                    if($cinGest[$i]!="")
                                                    {
                                                        $sql="insert into `debt_force_integration`.`dossier_dbi`(date_creation,id_users,id_ptf".$val[$ta["table_bdd"]].",numero_dossier,id_import,id_integration) values(now(),'".$cinGest[$i]."',".$porte_feuille->getId().$param[$ta["table_bdd"]][$i].",".$id_import.",".$integrationId.")";
                                                    }else{
                                                        $sql="insert into `debt_force_integration`.`dossier_dbi`(date_creation,id_ptf".$val[$ta["table_bdd"]].",numero_dossier,id_import,id_integration) values(now(),".$porte_feuille->getId().$param[$ta["table_bdd"]][$i].",".$id_import.",".$integrationId.")";
                                                    }
                                                    $stmt = $this->conn->prepare($sql)->executeQuery();
                                                    $idDeb = $this->integrationRepo->testDebiteurIdDebCle($id_debiteur[$i],$cle[$i],$integrationId);
                                                    if(!$idDeb["exist"])
                                                    {
                                                        if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Debiteur avec cle '.$cle[$i].' n\'existe pas !!')==true)
                                                        {
                                                            $countnv++;
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $dossier = $this->integrationRepo->testDossDbi1($numDossier[$i],$porte_feuille->getId());
                                                        $typeDeb = $this->integrationRepo->testDebDoss($idDeb["deb"],$dossier["dossier"]);
                                                        if(!$typeDeb["exist"])
                                                        {
                                                            if(array_key_exists($i,$numDossier) and $numDossier[$i]!="")
                                                            {
                                                                $qualite="1";
                                                                $c=$dossier;
                                                                if($c)
                                                                {
                                                                    $c=",".$c["dossier"];
                                                                    $origin_dossier=$dossier["place"];
                                                                    $origin_deb=$idDeb["place"];
                                                                    $sql="insert into `debt_force_integration`.`debi_doss_dbi`(id_debiteur_id,id_dossier_id,origin_deb,origin_doss) values(".$idDeb["deb"].$c.",".$origin_deb.",".$origin_dossier.")";
                                                                }
                                                            }
                                                            $this->em->getConnection()->prepare($sql)->execute();
                                                        }
                                                    }
                                                    
                                                    if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Dossier ajouté')==true)
                                                    {
                                                        $countv++;
                                                    }
                                                }else{
                                                    $numDoss = str_replace('"',' ',$numDossier[$i]);
                                                    if($this->integrationRepo->addToLogImportDbi(0, $a->getId(), 'Dossier n° ' . $numDoss . ' existe déjà')==true)
                                                    {
                                                        $countnv++;
                                                    }
                                                }
                                            }catch (\Exception $e) {
                                                if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),$e->getMessage())==true)
                                                {
                                                    $countnv++;
                                                }
                                            }
                                            // }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                //TODO :Import dossier
                // $IntegrationNonCommencer[$t]->setEtat(2);
                $tableDb_dossier = [];
                $importByType = $integrationRepo->getOneImportType($integrationId , "dossier");
                if($importByType){
                    //Etat en cours
                    $importByType->setEtat(2);
                    $this->em->flush();
                    $id_import = $importByType->getId();
                    $detailsImp = $integrationRepo->getDetailsImprt($id_import , $ordre);
    
                    //grouping les tables by table_bdd 
                    $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id
                        in(select (c.id_col_params) from  App\Entity\CorresColu  c where  c.id_model_import=:idSchema and c.origin_champ = 1 and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type) )group by t.table_bdd');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','dossier');
                    $tables = $query->getResult();
                    $tableDb_dossier = $tables;
                    $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where (t.id_model_import)=:idSchema  and t.origin_champ = 1 and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type)');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','dossier');
                    $colFile = $query->getResult();

                    $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where t.origin_champ = 2 
                    and (t.id_model_import)=:idSchema and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type)');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','dossier');
                    $colFileC = $query->getResult();
    
                    $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                    from App\Entity\CorresColu c where (c.id_model_import)=:idSchema  and c.origin_champ = 1 and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type))');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','dossier');
                    $tableDb = $query->getResult();
    
                    $query = $this->em->createQuery('SELECT t.id from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                    from App\Entity\CorresColu c where (c.id_model_import)=:idSchema   and c.origin_champ = 1 and  c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type))');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','dossier');
                    $colDb = $query->getResult();
                    
    
                    $param=array();
                    $val=array();   
    
                    $response="";
    
                    $filePath = $detailsImp->getUrl();
                    
                    if (($handle = fopen($filePath, "r")) !== FALSE)
                    {
                        while (($data = fgetcsv($handle, 1000000, ";")) !== FALSE)
                        {
                            break;
                        }
                        //Les entétes de fichier excel
                        $data=array_map("utf8_encode",$data);
                        $data=array_map('trim', $data);
                        $data1=array_map("utf8_encode",str_replace(" ","_",$data));
                        
                        for ($i=0; $i < count($colFile); $i++) { 
                            if(!in_array($colFile[$i]["column_name"],$data1))
                            {
                                $response="ENTETE_IDENTIQUE";
                                $codeStatut="ENTETE_IDENTIQUE";
                            }
                        }
                        
                        if($response == ""){
                            $a=new actionsImportDbi();
                            $a->setEtat(0);
                            $a->setCodeAction("Ajo");
                            $a->setDateDebut(new \DateTime());
                            $a->setIdImport($importByType->getId());
                            $a->setTitre("Ajout");
                            $emDbi->persist($a);
                            $emDbi->flush();
                            
    
                            $importByType->setEtat(2);
                            $this->em->flush(); 
    
                            foreach($tables as $ta){
                                $val[$ta["table_bdd"]]="";
                                if($ta["table_bdd"]=="debiteur")
                                {
                                    $val[$ta["table_bdd"]].="cin_formate";
                                }
                                for ($i = 0; $i < count($colFile); $i++)
                                {   
                                    if(($colFile[$i]["origine"] == 1)){
                                        $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                        
                                        if ($colParam->getTableBdd() == $ta["table_bdd"] and $colParam->getTitreCol()!="cin_gestionnaire")
                                        {
                                            $val[$ta["table_bdd"]].=",".$colParam->getTitreCol();
                                        }
                                    }
                                }
                                if($ta["table_bdd"]=="creance")
                                {
                                    if(!$this->in_array_r(5,$colDb))
                                    {
                                        $val[$ta["table_bdd"]].=",numero_creance";
                                    }
                                }
                            }
    
                            $data=$this->convert($filePath,";");
                            
                            foreach ($tables as $ta){
                                if($data){
                                    $countRow=0;
                                    foreach ($data as $row)
                                    {
                                        $valCin[$countRow]="";
                                        $valCinF[$countRow]="";
                                        $numCreance[$countRow]="";
                                        $principale[$countRow]="";
                                        $frais[$countRow]="";
                                        $interet[$countRow]="";
                                        $type_deb[$countRow]="";
                                        $id_debiteur[$countRow]= "";

                                        $valRaison[$countRow]="";
                                        $cinGest[$countRow]="";
                                        $param[$ta["table_bdd"]][$countRow]="";

                                        $champ[$countRow]="";

                                        for ($i=0; $i <count($colFileC) ; $i++) 
                                        { 
                                            $champ[$countRow."-".$i] = $row[$colFileC[$i]["column_name"]];
                                        }
                                        for ($i = 0; $i < count($colFile); $i++)
                                        {
                                            if($colDb[$i]["id"]==75 and $row[$colFile[$i]["nomCol"]]!="")
                                            {
                                                $gest = $this->em->getRepository(Utilisateurs::class)->findOneBy(["cin"=>$row[$colFile[$i]["nomCol"]]]);
                                                if($gest)
                                                {
                                                    $cinGest[$countRow]=$gest->getId();
                                                }
                                            }
                                            if($colDb[$i]["id"]==23)
                                            {
                                                if(isset($row[$colFile[$i]["column_name"]])=="")
                                                {
                                                    for ($r = 0; $r < count($colFile); $r++)
                                                    {
                                                        if($colDb[$r]["id"]==144)
                                                        {
                                                            $row[$colFile[$i]["column_name"]]=$row[$colFile[$r]["column_name"]];
                                                        }
                                                    }
                                                }
                                                $cin=$row[$colFile[$i]["column_name"]];
                                                $valCin[$countRow]=$cin;
                                                $valCinF[$countRow]=strtoupper(preg_replace('/[^A-Za-z0-9]+/',"", $cin));
                                                if(!$this->in_array_r(5,$colDb))
                                                {
                                                    $numCreance[$countRow]="Sys".$valCinF[$countRow]."-".($countRow+1);
                                                }
                                            }
                                            if($colDb[$i]["id"]==179)
                                            {
                                                $type_deb[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==5)
                                            {
                                                $numCreance[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==15)
                                            {
                                                $row[$colFile[$i]["column_name"]]=str_replace(" ","",$row[$colFile[$i]["column_name"]]);
                                            }
                                            if($colDb[$i]["id"]==65)
                                            {
                                                $numDossier[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==167)
                                            {
                                                $principale[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==168)
                                            {
                                                $frais[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==185)
                                            {
                                                $id_debiteur[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==169)
                                            {
                                                $interet[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                            if ($colParam->getTableBdd() == $ta["table_bdd"])
                                            {
                                            $isDate = $this->em->getRepository(ColumnsParams::class)->find($colDb[$i]["id"]);
                                                if($isDate->getIsDate() == 1 and \DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])==true)
                                                {
                                                    $valueDate=\DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])->format("d-m-Y");
                                                    $param[$ta["table_bdd"]][$countRow].= ",'" . date_format(new \DateTime($valueDate), 'Y-m-d H:i:s') . "'";
                                                }
                                                else
                                                {
                                                    if($colDb[$i]["id"]!=75)
                                                    {
                                                        $param[$ta["table_bdd"]][$countRow].= ",'" . $row[$colFile[$i]["column_name"]] . "'";
                                                    }
                                                }
                                                if($colFile[$i]["required"] == 1 and empty($row[$colFile[$i]["column_name"]]))
                                                {
                                                    unset($param[$ta["table_bdd"]][$countRow]);
                                                }
                                            }
                                        }
                                        
                                        if($ta["table_bdd"]=="creance" and $this->in_array_r("creance",$tableDb))
                                        {
                                            if(!$this->in_array_r(5,$colDb))
                                            {
                                                $param[$ta["table_bdd"]][$countRow].= ",'Sys" . $valCinF[$countRow] . "-".($countRow+1)."'";
                                            }
                                        }
                                        $countRow++;
                                    }
                                }
                            }
                            $model = $importByType->getIdModel()->getId();
                            $query = $this->em->createQuery('SELECT t from App\Entity\CorresColu t where t.id_model_import = :idSchema 
                            and t.id_col_params is null 
                            and t.id_model_import in (select m.id from App\Entity\ModelImport m where m.type = :type) ');
                            $query->setParameter('idSchema', $model);
                            $query->setParameter('type','dossier');
                            $champPersonalise = $query->getResult();
                            if(count($val)>0 and count($param)>0)
                            {
                                $nameTable="dossier";
                                if($ta["table_bdd"]==$nameTable)
                                {
                                    $lastKey = key(array_slice($param[$ta["table_bdd"]], -1, 1, true));
                                    for($i=0;$i<($lastKey+1);$i++)
                                    {
                                        if(array_key_exists($i,$param[$ta["table_bdd"]]) )
                                        {
                                            if($this->in_array_r(65,$colDb)==false)
                                            {
                                                try {
                                                    //Génerer le redDossier
                                                    $sql="SELECT count(d.id) FROM `debt_force_integration`.`dossier_dbi` d where d.id_import = :id";
                                                    $stmt = $this->conn->prepare($sql);
                                                    $stmt->bindValue(":id",$id_import);
                                                    $stmt = $stmt->executeQuery();
                                                    $countDossDbi = $stmt->fetchOne();

                                                    $query = $this->em->createQuery('SELECT max(t.id) from App\Entity\Dossier t');
                                                    $maxNum = $query->getSingleScalarResult();
                                                    if(!$maxNum)
                                                    {
                                                        $maxNum=0;
                                                    }
                                                    $maxNum = $countDossDbi + $maxNum;
                                                    
                                                    $refDossier=$porte_feuille->getNumeroPtf()."-".($maxNum+1);
                                                    $numDossier[$i]=$refDossier;
                                                    if($this->integrationRepo->testDossierDbi($refDossier,$porte_feuille->getId())==false){
                                                        if($cinGest[$i]!=""){
                                                            $sql="insert into `debt_force_integration`.`dossier_dbi`(date_creation,numero_dossier,id_users,id_ptf".$val[$ta["table_bdd"]].",id_import,id_integration) values(now(),'".$refDossier."','".$cinGest[$i]."',".$porte_feuille->getId().$param[$ta["table_bdd"]][$i].",".$id_import.",".$integrationId.")";
                                                        }else{
                                                            $sql="insert into `debt_force_integration`.`dossier_dbi`(date_creation,numero_dossier,id_ptf".$val[$ta["table_bdd"]].",id_import,id_integration) values(now(),'".$refDossier."',".$porte_feuille->getId().$param[$ta["table_bdd"]][$i].",".$id_import.",".$integrationId.")";
                                                        }
                                                        $stmt = $this->conn->prepare($sql)->executeQuery();                                                        
                                                        // if($cinGest[$i]!=""){
                                                        //     $query = $this->em->createQuery('SELECT max(t.id) from App\Entity\Dossier t');
                                                        //     $dossier = $query->getSingleScalarResult();
                                                        //     $tableDispatch[$cr]=$dossier;
                                                        //     $cr++;
                                                        // }
                                                        // $idDeb = $this->integrationRepo->testDebiteurDbi1($valCinF[$i]);

                                                        $idDeb = $this->integrationRepo->testDebiteurIdDebCle($id_debiteur[$i],$cle[$i],$integrationId);
                                                        
                                                        if(!$idDeb["exist"])
                                                        {
                                                            if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Debiteur avec cin '.$valCinF[$i].' n\'existe pas !!')==true)
                                                            {
                                                                $countnv++;
                                                            }
                                                        }
                                                        else
                                                        {
                                                            $dossier = $this->integrationRepo->testDossDbi1($numDossier[$i],$porte_feuille->getId());
                                                            $typeDeb = $this->integrationRepo->testDebDoss($idDeb["deb"],$dossier["dossier"]);
                                                            if(!$typeDeb["exist"])
                                                            {
                                                                if(array_key_exists($i,$numDossier) and $numDossier[$i]!="")
                                                                {
                                                                    $qualite="1";
                                                                    $c=$dossier;
                                                                    if($c)
                                                                    {
                                                                        $c=",".$c["dossier"];
                                                                        $origin_dossier=$dossier["place"];
                                                                        $origin_deb=$idDeb["place"];
                                                                        $sql="insert into `debt_force_integration`.`debi_doss_dbi`(id_debiteur_id,id_dossier_id,origin_deb,origin_doss) values(".$idDeb["deb"].$c.",".$origin_deb.",".$origin_dossier.")";
                                                                    }
                                                                }
                                                                $this->em->getConnection()->prepare($sql)->execute();
                                                            }
                                                        }
                                                        if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Dossier ajouté')==true)
                                                        {
                                                            $countv++;
                                                        }
                                                    }else{
                                                        if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Dossier n° '.$refDossier.' existe déjà')==true)
                                                        {
                                                            $countnv++;
                                                        }
                                                    }
                                                }catch (\Exception $e) {
                                                    if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),$e->getMessage())==true)
                                                    {
                                                        $countnv++;
                                                    }
                                                }
                                            }else{
                                                try {
                                                    if($this->integrationRepo->testDossierDbi($numDossier[$i],$porte_feuille->getId())==false){
                                                        if($cinGest[$i]!="")
                                                        {
                                                            $sql="insert into `debt_force_integration`.`dossier_dbi`(date_creation,id_users,id_ptf".$val[$ta["table_bdd"]].",id_import,id_integration) values(now(),'".$cinGest[$i]."',".$porte_feuille->getId().$param[$ta["table_bdd"]][$i].",".$id_import.",".$integrationId.")";
                                                        }else{
                                                            $sql="insert into `debt_force_integration`.`dossier_dbi`(date_creation,id_ptf".$val[$ta["table_bdd"]].",id_import,id_integration) values(now(),".$porte_feuille->getId().$param[$ta["table_bdd"]][$i].",".$id_import.",".$integrationId.")";
                                                        }
                                                        $stmt = $this->conn->prepare($sql)->executeQuery();
                                                        // $idDeb = $this->integrationRepo->testDebiteurDbi1($valCinF[$i]);
                                                        $idDeb = $this->integrationRepo->testDebiteurIdDebCle($id_debiteur[$i],$cle[$i],$integrationId);
                                                        if(!$idDeb["exist"])
                                                        {
                                                            if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Debiteur avec cin '.$valCinF[$i].' n\'existe pas !!')==true)
                                                            {
                                                                $countnv++;
                                                            }
                                                        }
                                                        else
                                                        {
                                                            $dossier = $this->integrationRepo->testDossDbi1($numDossier[$i],$porte_feuille->getId());
                                                            $typeDeb = $this->integrationRepo->testDebDoss($idDeb["deb"],$dossier["dossier"]);
                                                            if(!$typeDeb["exist"])
                                                            {
                                                                if(array_key_exists($i,$numDossier) and $numDossier[$i]!="")
                                                                {
                                                                    $qualite="1";
                                                                    $c=$dossier;
                                                                    if($c)
                                                                    {
                                                                        $c=",".$c["dossier"];
                                                                        $origin_dossier=$dossier["place"];
                                                                        $origin_deb=$idDeb["place"];
                                                                        $sql="insert into `debt_force_integration`.`debi_doss_dbi`(id_debiteur_id,id_dossier_id,origin_deb,origin_doss) values(".$idDeb["deb"].$c.",".$origin_deb.",".$origin_dossier.")";
                                                                    }
                                                                }
                                                                $this->em->getConnection()->prepare($sql)->execute();
                                                            }
                                                        }
                                                        
                                                        if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Dossier ajouté')==true)
                                                        {
                                                            $countv++;
                                                        }
                                                    }else{
                                                        $numDoss = str_replace('"',' ',$numDossier[$i]);
                                                        if($this->integrationRepo->addToLogImportDbi(0, $a->getId(), 'Dossier n° ' . $numDoss . ' existe déjà')==true)
                                                        {
                                                            $countnv++;
                                                        }
                                                    }
                                                }catch (\Exception $e) {
                                                    if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),$e->getMessage())==true)
                                                    {
                                                        $countnv++;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                //TODO : Fin dossier
                
                //---------------
                //Start
                $importByType = $integrationRepo->getOneImportType($integrationId , "emploi");
    

                if($importByType){
                    //Etat en cours
                    $importByType->setEtat(2);
                    $this->em->flush();
    
                    $id_import = $importByType->getId();
    
                    $detailsImp = $integrationRepo->getDetailsImprt($id_import , $ordre);
    
                    //grouping les tables by table_bdd 
                    $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id
                        in(select (c.id_col_params) from  App\Entity\CorresColu  c where  c.id_model_import=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type) )group by t.table_bdd');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','emploi');
                    $tables = $query->getResult();
                    $tables_emploi = $tables;
    
                    $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where (t.id_model_import)=:idSchema and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type)');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','emploi');
                    $colFile = $query->getResult();

                    $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where t.origin_champ = 2 
                    and (t.id_model_import)=:idSchema and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type )');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','emploi');
                    $colFileC = $query->getResult();
    
                    $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                    from App\Entity\CorresColu c where (c.id_model_import)=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type))');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','emploi');
                    $tableDb = $query->getResult();
    
                    $query = $this->em->createQuery('SELECT t.id from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                    from App\Entity\CorresColu c where (c.id_model_import)=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type))');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','emploi');
                    $colDb = $query->getResult();

                    $param=array();
                    $val=array();
                    $response="";
    
                    $filePath = $detailsImp->getUrl();
                    if (($handle = fopen($filePath, "r")) !== FALSE)
                    {
                        while (($data = fgetcsv($handle, 1000000, ";")) !== FALSE)
                        {
                            break;
                        }
                        //Les entétes de fichier excel
                        $data=array_map("utf8_encode",$data);
                        $data=array_map('trim', $data);
                        $data1=array_map("utf8_encode",str_replace(" ","_",$data));
                        
                        for ($i=0; $i < count($colFile); $i++) { 
                            if(!in_array($colFile[$i]["column_name"],$data1))
                            {
                                $response="ENTETE_IDENTIQUE";
                                $codeStatut="ENTETE_IDENTIQUE";
                            }
                        }
                        if($response == ""){
                            $a=new actionsImportDbi();
                            $a->setEtat(0);
                            $a->setCodeAction("Ajo");
                            $a->setDateDebut(new \DateTime());
                            $a->setIdImport($importByType->getId());
                            $a->setTitre("Ajout");
                            $emDbi->persist($a);
                            $emDbi->flush();
                            // $a = $this->integrationRepo->createActionsImport
                            $importByType->setEtat(2);
                            $this->em->flush(); 
                            foreach($tables as $ta){
                                $val[$ta["table_bdd"]]="";
                                if($ta["table_bdd"]=="debiteur")
                                {
                                    $val[$ta["table_bdd"]].="cin_formate";
                                }
                                for ($i = 0; $i < count($colFile); $i++)
                                {   
                                    if(($colFile[$i]["origine"] == 1)){
                                        $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                        
                                        if ($colParam->getTableBdd() == $ta["table_bdd"] and $colParam->getTitreCol()!="cin_gestionnaire" and $colFile[$i]["id_col_params"]!=89)
                                        {
                                            $val[$ta["table_bdd"]].=",".$colParam->getTitreCol();
                                        }
                                    }
                                }
                                if($ta["table_bdd"]=="creance")
                                {
                                    if(!$this->in_array_r(5,$colDb))
                                    {
                                        $val[$ta["table_bdd"]].=",numero_creance";
                                    }
                                }
                            }
    
                            $data=$this->convert($filePath,";");
                            $valCinF=array();
                            $valCin=array();
                            foreach ($tables as $ta){
                                if($data){
                                    $countRow=0;
                                    foreach ($data as $row)
                                    {
                                        $valCin[$countRow]="";
                                        $valCinF[$countRow]="";
                                        $valRaison[$countRow]="";
                                        $cinGest[$countRow]="";
                                        $profession[$countRow]="";
                                        $id_debiteur[$countRow]="";

                                        $param[$ta["table_bdd"]][$countRow]="";

                                        $champ[$countRow]="";
                                        
                                        for ($i=0; $i <count($colFileC) ; $i++) 
                                        { 
                                            $champ[$countRow."-".$i] = $row[$colFileC[$i]["column_name"]];
                                        }

                                        for ($i = 0; $i < count($colFile); $i++)
                                        {
                                            if($colDb[$i]["id"]==75 and $row[$colFile[$i]["nomCol"]]!="")
                                            {
                                                $gest = $this->em->getRepository(Utilisateurs::class)->findOneBy(["cin"=>$row[$colFile[$i]["nomCol"]]]);
                                                if($gest)
                                                {
                                                    $cinGest[$countRow]=$gest->getId();
                                                }
                                            }
                                            if($colDb[$i]["id"]==23)
                                            {
                                                $cin=$row[$colFile[$i]["column_name"]];
                                                $valCin[$countRow]=$cin;
                                                $valCinF[$countRow]=strtoupper(preg_replace('/[^A-Za-z0-9]+/',"", $cin));
                                                if(!$this->in_array_r(5,$colDb))
                                                {
                                                    $numCreance[$countRow]="Sys".$valCinF[$countRow]."-".($countRow+1);
                                                }
                                            }
                                            if($colDb[$i]["id"]==5)
                                            {
                                                $numCreance[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==15)
                                            {
                                                $row[$colFile[$i]["column_name"]]=str_replace(" ","",$row[$colFile[$i]["column_name"]]);
                                            }
                                            if($colDb[$i]["id"]==65)
                                            {
                                                $numDossier[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==167)
                                            {
                                                $principale[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==168)
                                            {
                                                $frais[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==178)
                                            {
                                                $typeTelephone[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==185)
                                            {
                                                $id_debiteur[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==89)
                                            {
                                                $profession[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                            if ($colParam->getTableBdd() == $ta["table_bdd"])
                                            {
                                                $isDate = $this->em->getRepository(ColumnsParams::class)->find($colDb[$i]["id"]);
                                                    if ($isDate->getIsDate() == 1 and \DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])==true)
                                                    {
                                                        $valueDate=\DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])->format("d-m-Y");
                                                        $param[$ta["table_bdd"]][$countRow].= ",'" . date_format(new \DateTime($valueDate), 'Y-m-d H:i:s') . "'";
                                                    }
                                                    else
                                                    {
                                                        if($colDb[$i]["id"]!=75  && $colDb[$i]["id"]!=89)
                                                        {
                                                            $param[$ta["table_bdd"]][$countRow].= ",'" . $row[$colFile[$i]["column_name"]] . "'";
                                                        }
                                                    }
                                                    if($colFile[$i]["required"] == 1 and empty($row[$colFile[$i]["column_name"]]))
                                                    {
                                                        unset($param[$ta["table_bdd"]][$countRow]);
                                                    }
                                            }
                                        }
                                        
                                        if($ta["table_bdd"]=="creance" and $this->in_array_r("creance",$tableDb))
                                        {
                                            if(!$this->in_array_r(5,$colDb))
                                            {
                                                $param[$ta["table_bdd"]][$countRow].= ",'Sys" . $valCinF[$countRow] . "-".($countRow+1)."'";
                                            }
                                        }
                                        $countRow++;
                                    }
                                }
                            }
                            $model = $importByType->getIdModel()->getId();
                            $query = $this->em->createQuery('SELECT t from App\Entity\CorresColu t where t.id_model_import = :idSchema 
                            and t.id_col_params is null 
                            and t.id_model_import in (select m.id from App\Entity\ModelImport m where m.type = :type) ');
                            $query->setParameter('idSchema', $model);
                            $query->setParameter('type','emploi');
                            $champPersonalise = $query->getResult();
                            if(count($val)>0 and count($param)>0)
                            {
                                $nameTable="emploi";
                                if($ta["table_bdd"]==$nameTable)
                                {
                                    $lastKey = key(array_slice($param[$ta["table_bdd"]], -1, 1, true));
                                    for($i=0;$i<($lastKey+1);$i++)
                                    {
                                        // dump($param[$ta["table_bdd"]][$i]);
                                        if(array_key_exists($i,$param[$ta["table_bdd"]]) and $id_debiteur[$i]!="")
                                        {
                                            try {
                                                $debExist = $this->integrationRepo->testDebiteurIdDeb($id_debiteur[$i],$integrationId);
                                                if($debExist["exist"] == false)
                                                {
                                                    if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Debiteur n\'existe pas !!')==true)
                                                    {
                                                        $countnv++;
                                                    }
                                                }
                                                else
                                                {
                                                    $p = 0;
                                                    if($profession[$i] != ''){
                                                        $p = $this->integrationRepo->findProfession(trim($profession[$i]));
                                                    }
                                                    $sql="insert into debt_force_integration.emploi_dbi(id_debiteur".$val[$ta["table_bdd"]].",origin_deb , id_import , profession_id) values(".$debExist["deb"].$param[$ta["table_bdd"]][$i].",".$debExist["place"].",".$id_import.",".$p.")";
                                                    
                                                    $stmt = $this->conn->prepare($sql)->executeQuery();
                                                    if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Emploie ajouté')==true)
                                                    {
                                                        $countv++;
                                                    }
                                                }
                                            }
                                            catch (\Exception $e) {
                                                if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),$e->getMessage())==true)
                                                {
                                                    $countnv++;
                                                }
                                            }   
                                        }
                                    }
                                }
                            }
                            
                            //Set l'état d'importation d'import
                            // $sql="SELECT l.* from logs_actions l where l.etat=0 and l.id_action_id in(select a.id from actions_import a where a.id_import_id = :id)";
                            // $param=(array("id"=>$importByType->getId() ));
                            // $error_import = $this->conn->fetchAllAssociative($sql , $param);
                            // if(count($error_import) == 0){
                            //     $importByType->setEtat(4);
                            // }else{
                            //     $importByType->setEtat(3);
                            // }
                            // $this->em->flush();
                            // $this->verifecationDoublent($a->getId());
                        }
                    }
                }

                //END

                //Start
                $importByType = $integrationRepo->getOneImportType($integrationId , "employeur");
    

                if($importByType){
                    //Etat en cours
                    $importByType->setEtat(2);
                    $this->em->flush();
    
                    $id_import = $importByType->getId();
    
                    $detailsImp = $integrationRepo->getDetailsImprt($id_import , $ordre);
    
                    //grouping les tables by table_bdd 
                    $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id
                        in(select (c.id_col_params) from  App\Entity\CorresColu  c where  c.id_model_import=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type) )group by t.table_bdd');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','employeur');
                    $tables = $query->getResult();
                    $tables_employeur = $tables;
    
                    $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where (t.id_model_import)=:idSchema and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type)');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','employeur');
                    $colFile = $query->getResult();

                    $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where t.origin_champ = 2 
                    and (t.id_model_import)=:idSchema and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type )');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','employeur');
                    $colFileC = $query->getResult();
    
                    $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                    from App\Entity\CorresColu c where (c.id_model_import)=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type))');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','employeur');
                    $tableDb = $query->getResult();
    
                    $query = $this->em->createQuery('SELECT t.id from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                    from App\Entity\CorresColu c where (c.id_model_import)=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type))');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','employeur');
                    $colDb = $query->getResult();

                    $param=array();
                    $val=array();
                    $response="";
    
                    $filePath = $detailsImp->getUrl();
                    if (($handle = fopen($filePath, "r")) !== FALSE)
                    {
                        while (($data = fgetcsv($handle, 1000000, ";")) !== FALSE)
                        {
                            break;
                        }
                        //Les entétes de fichier excel
                        $data=array_map("utf8_encode",$data);
                        $data=array_map('trim', $data);
                        $data1=array_map("utf8_encode",str_replace(" ","_",$data));
                        
                        for ($i=0; $i < count($colFile); $i++) { 
                            if(!in_array($colFile[$i]["column_name"],$data1))
                            {
                                $response="ENTETE_IDENTIQUE";
                                $codeStatut="ENTETE_IDENTIQUE";
                            }
                        }
                        if($response == ""){
                            $a=new actionsImportDbi();
                            $a->setEtat(0);
                            $a->setCodeAction("Ajo");
                            $a->setDateDebut(new \DateTime());
                            $a->setIdImport($importByType->getId());
                            $a->setTitre("Ajout");
                            $emDbi->persist($a);
                            $emDbi->flush();
                            // $a = $this->integrationRepo->createActionsImport
                            $importByType->setEtat(2);
                            $this->em->flush(); 
                            foreach($tables as $ta){
                                $val[$ta["table_bdd"]]="";
                                if($ta["table_bdd"]=="debiteur")
                                {
                                    $val[$ta["table_bdd"]].="cin_formate";
                                }
                                for ($i = 0; $i < count($colFile); $i++)
                                {   
                                    if(($colFile[$i]["origine"] == 1)){
                                        $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                        
                                        if ($colParam->getTableBdd() == $ta["table_bdd"] and $colParam->getTitreCol()!="cin_gestionnaire")
                                        {
                                            $val[$ta["table_bdd"]].=",".$colParam->getTitreCol();
                                        }
                                    }
                                }
                                if($ta["table_bdd"]=="creance")
                                {
                                    if(!$this->in_array_r(5,$colDb))
                                    {
                                        $val[$ta["table_bdd"]].=",numero_creance";
                                    }
                                }
                            }
    
                            $data=$this->convert($filePath,";");
                            $valCinF=array();
                            $valCin=array();
                            foreach ($tables as $ta){
                                if($data){
                                    $countRow=0;
                                    foreach ($data as $row)
                                    {
                                        $valCin[$countRow]="";
                                        $valCinF[$countRow]="";
                                        $valRaison[$countRow]="";
                                        $cinGest[$countRow]="";
                                        $id_debiteur[$countRow]="";
                                        $param[$ta["table_bdd"]][$countRow]="";

                                        $champ[$countRow]="";
                                        
                                        for ($i=0; $i <count($colFileC) ; $i++) 
                                        { 
                                            $champ[$countRow."-".$i] = $row[$colFileC[$i]["column_name"]];
                                        }

                                        for ($i = 0; $i < count($colFile); $i++)
                                        {
                                            if($colDb[$i]["id"]==75 and $row[$colFile[$i]["nomCol"]]!="")
                                            {
                                                $gest = $this->em->getRepository(Utilisateurs::class)->findOneBy(["cin"=>$row[$colFile[$i]["nomCol"]]]);
                                                if($gest)
                                                {
                                                    $cinGest[$countRow]=$gest->getId();
                                                }
                                            }
                                            if($colDb[$i]["id"]==23)
                                            {
                                                $cin=$row[$colFile[$i]["column_name"]];
                                                $valCin[$countRow]=$cin;
                                                $valCinF[$countRow]=strtoupper(preg_replace('/[^A-Za-z0-9]+/',"", $cin));
                                                if(!$this->in_array_r(5,$colDb))
                                                {
                                                    $numCreance[$countRow]="Sys".$valCinF[$countRow]."-".($countRow+1);
                                                }
                                            }
                                            if($colDb[$i]["id"]==5)
                                            {
                                                $numCreance[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==15)
                                            {
                                                $row[$colFile[$i]["column_name"]]=str_replace(" ","",$row[$colFile[$i]["column_name"]]);
                                            }
                                            if($colDb[$i]["id"]==65)
                                            {
                                                $numDossier[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==167)
                                            {
                                                $principale[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==168)
                                            {
                                                $frais[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==178)
                                            {
                                                $typeTelephone[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==185)
                                            {
                                                $id_debiteur[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                            if ($colParam->getTableBdd() == $ta["table_bdd"])
                                            {
                                                $isDate = $this->em->getRepository(ColumnsParams::class)->find($colDb[$i]["id"]);
                                                if ($isDate->getIsDate() == 1 and \DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])==true)
                                                {
                                                    $valueDate=\DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])->format("d-m-Y");
                                                    $param[$ta["table_bdd"]][$countRow].= ",'" . date_format(new \DateTime($valueDate), 'Y-m-d H:i:s') . "'";
                                                }
                                                else
                                                {
                                                    if($colDb[$i]["id"]!=75)
                                                    {
                                                        $param[$ta["table_bdd"]][$countRow].= ",'" . $row[$colFile[$i]["column_name"]] . "'";
                                                    }
                                                }
                                                if($colFile[$i]["required"] == 1 and empty($row[$colFile[$i]["column_name"]]))
                                                {
                                                    unset($param[$ta["table_bdd"]][$countRow]);
                                                }
                                            }
                                        }
                                        
                                        if($ta["table_bdd"]=="creance" and $this->in_array_r("creance",$tableDb))
                                        {
                                            if(!$this->in_array_r(5,$colDb))
                                            {
                                                $param[$ta["table_bdd"]][$countRow].= ",'Sys" . $valCinF[$countRow] . "-".($countRow+1)."'";
                                            }
                                        }
                                        $countRow++;
                                    }
                                }
                            }
                            $model = $importByType->getIdModel()->getId();
                            $query = $this->em->createQuery('SELECT t from App\Entity\CorresColu t where t.id_model_import = :idSchema 
                            and t.id_col_params is null 
                            and t.id_model_import in (select m.id from App\Entity\ModelImport m where m.type = :type) ');
                            $query->setParameter('idSchema', $model);
                            $query->setParameter('type','employeur');
                            $champPersonalise = $query->getResult();
                            if(count($val)>0 and count($param)>0)
                            {
                                $nameTable="employeur";
                                if($ta["table_bdd"]==$nameTable)
                                {
                                    $lastKey = key(array_slice($param[$ta["table_bdd"]], -1, 1, true));
                                    for($i=0;$i<($lastKey+1);$i++)
                                    {
                                        // dump($param[$ta["table_bdd"]][$i]);
                                        if(array_key_exists($i,$param[$ta["table_bdd"]]) and $id_debiteur[$i]!="")
                                        {
                                            try {
                                                $debExist = $this->integrationRepo->testDebiteurIdDeb($id_debiteur[$i],$integrationId);
                                                if($debExist["exist"] == false)
                                                {
                                                    if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Debiteur n\'existe pas !!')==true)
                                                    {
                                                        $countnv++;
                                                    }
                                                }
                                                else
                                                {
                                                    // $idDeb=$idDeb->getId();
                                                    $sql="insert into debt_force_integration.employeur_dbi(id_debiteur".$val[$ta["table_bdd"]].",origin_deb , id_import) values(".$debExist["deb"].$param[$ta["table_bdd"]][$i].",".$debExist["place"].",".$id_import.")";
                                                    $stmt = $this->conn->prepare($sql)->executeQuery();
                                                    if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Employeur ajouté')==true)
                                                    {
                                                        $countv++;
                                                    }
                                                }
                                            }
                                            catch (\Exception $e) {
                                                if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),$e->getMessage())==true)
                                                {
                                                    $countnv++;
                                                }
                                            }   
                                        }
                                    }
                                }
                            }
                            
                            //Set l'état d'importation d'import
                            // $sql="SELECT l.* from logs_actions l where l.etat=0 and l.id_action_id in(select a.id from actions_import a where a.id_import_id = :id)";
                            // $param=(array("id"=>$importByType->getId() ));
                            // $error_import = $this->conn->fetchAllAssociative($sql , $param);
                            // if(count($error_import) == 0){
                            //     $importByType->setEtat(4);
                            // }else{
                            //     $importByType->setEtat(3);
                            // }
                            // $this->em->flush();
                            // $this->verifecationDoublent($a->getId());
                        }
                    }
                }

                //END

                //---------------
                
                //Start creance
                $importByType = $integrationRepo->getOneImportType($integrationId , "creance");
                if($importByType){
                    // dump($importByType);
                    //Etat en cours
                    $importByType->setEtat(2);
                    $this->em->flush();
    
                    $id_import = $importByType->getId();
    
                    $detailsImp = $integrationRepo->getDetailsImprt($id_import,$ordre);
    
                    //grouping les tables by table_bdd 
                    $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id
                        in(select (c.id_col_params) from  App\Entity\CorresColu  c where  c.id_model_import=:idSchema and c.origin_champ = 1  and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type or m.type = :type1) )group by t.table_bdd');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','creance');
                    $query->setParameter('type1','detail_creance');
                    $tables = $query->getResult();
    
                    $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where (t.id_model_import)=:idSchema and t.origin_champ = 1  and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type or m.type = :type1 )');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','creance');
                    $query->setParameter('type1','detail_creance');
                    $colFile = $query->getResult();

                    $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where t.origin_champ = 2 
                    and (t.id_model_import)=:idSchema and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type or m.type = :type1)');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','creance');
                    $query->setParameter('type1','detail_creance');
                    $colFileC = $query->getResult();

                    $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                    from App\Entity\CorresColu c where (c.id_model_import)=:idSchema and c.origin_champ = 1  and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type or m.type = :type1))');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','creance');
                    $query->setParameter('type1','detail_creance');
                    $tableDb = $query->getResult();
    
                    $query = $this->em->createQuery('SELECT t.id from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                    from App\Entity\CorresColu c where (c.id_model_import)=:idSchema and c.origin_champ = 1  and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type or m.type = :type1))');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','creance');
                    $query->setParameter('type1','detail_creance');
                    $colDb = $query->getResult();

                    $param=array();
                    $val=array();
    
                    $response="";
    
                    $filePath = $detailsImp->getUrl();
    
                    if (($handle = fopen($filePath, "r")) !== FALSE)
                    {
                        while (($data = fgetcsv($handle, 1000000, ";")) !== FALSE)
                        {
                            break;
                        }
                        //Les entétes de fichier excel
                        $data=array_map("utf8_encode",$data);
                        $data=array_map('trim', $data);
                        $data1=array_map("utf8_encode",str_replace(" ","_",$data));
                        
                        for ($i=0; $i < count($colFile); $i++) { 
                            if(!in_array($colFile[$i]["column_name"],$data1))
                            {
                                $response="ENTETE_IDENTIQUE";
                                $codeStatut="ENTETE_IDENTIQUE";
                            }
                        }

                        if($response == ""){
                            $a=new actionsImportDbi();
                            $a->setEtat(0);
                            $a->setCodeAction("Ajo");
                            $a->setDateDebut(new \DateTime());
                            $a->setIdImport($importByType->getId());
                            $a->setTitre("Ajout");
                            $emDbi->persist($a);
                            $emDbi->flush();
    
                            $importByType->setEtat(2);
                            $this->em->flush(); 
    
                            foreach($tables as $ta){
                                $val[$ta["table_bdd"]]="";
                                if($ta["table_bdd"]=="debiteur")
                                {
                                    $val[$ta["table_bdd"]].="cin_formate";
                                }
                                for ($i = 0; $i < count($colFile); $i++)
                                {   
                                    if(($colFile[$i]["origine"] == 1) && $colFile[$i]["id_col_params"]){
                                        $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                        
                                        if ($colParam->getTableBdd() == $ta["table_bdd"] and $colParam->getTitreCol()!="cin_gestionnaire" and $colFile[$i]["id_col_params"] != 3)
                                        {
                                            $val[$ta["table_bdd"]].=",".$colParam->getTitreCol();
                                        }
                                    }
                                }
                                if($ta["table_bdd"]=="creance")
                                {
                                    if(!$this->in_array_r(5,$colDb))
                                    {
                                        $val[$ta["table_bdd"]].=",numero_creance";
                                    }
                                }
                            }

                            foreach($tables_debiteur as $ta){
                                $val[$ta["table_bdd"]]="";
                                if($ta["table_bdd"]=="debiteur")
                                {
                                    $val[$ta["table_bdd"]].="cin_formate";
                                }
                            }
    
                            $data=$this->convert($filePath,";");
                            $valCinF=array();
                            $valCin=array();
                            foreach ($tables as $ta){
                                if($data){
                                    $countRow=0;
                                    foreach ($data as $row)
                                    {
                                        $valCin[$countRow]="";
                                        $valCinF[$countRow]="";
                                        $numCreance[$countRow]="";
                                        $principale[$countRow]="";
                                        $frais[$countRow]="";
                                        $interet[$countRow]="";
                                        $valRaison[$countRow]="";
                                        $cinGest[$countRow]="";
                                        $param[$ta["table_bdd"]][$countRow]="";
                                        $type_deb[$countRow]="";
                                        $type_creance[$countRow]="";
                                        $id_debiteur[$countRow]="";
                                        $numDossier[$countRow]="";

                                        $champ[$countRow]="";
                                        
                                        for ($i=0; $i <count($colFileC) ; $i++) 
                                        { 
                                            $champ[$countRow."-".$i] = $row[$colFileC[$i]["column_name"]];
                                        }

                                        for ($i = 0; $i < count($colFile); $i++)
                                        {
                                            if($colDb[$i]["id"]==75  and  $row[$colFile[$i]["nomCol"]]!="")
                                            {
                                                $gest = $this->em->getRepository(Utilisateurs::class)->findOneBy(["cin"=>$row[$colFile[$i]["nomCol"]]]);
                                                if($gest)
                                                {
                                                    $cinGest[$countRow]=$gest->getId();
                                                }
                                            }
                                            if($colDb[$i]["id"]==23)
                                            {
                                                if(isset($row[$colFile[$i]["column_name"]])=="")
                                                {
                                                    for ($r = 0; $r < count($colFile); $r++)
                                                    {
                                                        if($colDb[$r]["id"]==144)
                                                        {
                                                            $row[$colFile[$i]["column_name"]]=$row[$colFile[$r]["column_name"]];
                                                        }
                                                    }
                                                }
                                                $cin=$row[$colFile[$i]["column_name"]];
                                                $valCin[$countRow]=$cin;
                                                $valCinF[$countRow]=strtoupper(preg_replace('/[^A-Za-z0-9]+/',"", $cin));
                                                if(!$this->in_array_r(5,$colDb))
                                                {
                                                    $numCreance[$countRow]="Sys".$valCinF[$countRow]."-".($countRow+1);
                                                }
                                            }
                                            if($colDb[$i]["id"]==5)
                                            {
                                                $numCreance[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==15)
                                            {
                                                $row[$colFile[$i]["column_name"]]=str_replace(" ","",$row[$colFile[$i]["column_name"]]);
                                            }
                                            if($colDb[$i]["id"]==65)
                                            {
                                                $numDossier[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==179)
                                            {
                                                $type_deb[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==3)
                                            {
                                                $type_creance[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==167)
                                            {
                                                $principale[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==168)
                                            {
                                                $frais[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==185)
                                            {
                                                $id_debiteur[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            
                                            if($colDb[$i]["id"]==169)
                                            {
                                                $interet[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                            if ($colParam->getTableBdd() == $ta["table_bdd"])
                                            {
                                            $isDate = $this->em->getRepository(ColumnsParams::class)->find($colDb[$i]["id"]);
                                                if ($isDate->getIsDate() == 1 and \DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])==true)
                                                {
                                                    $valueDate=\DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])->format("d-m-Y");
                                                    $param[$ta["table_bdd"]][$countRow].= ",'" . date_format(new \DateTime($valueDate), 'Y-m-d H:i:s') . "'";
                                                }
                                                else
                                                {
                                                    if($colDb[$i]["id"]!=75 && $colDb[$i]["id"]!=3)
                                                    {
                                                        $param[$ta["table_bdd"]][$countRow].= ",'" . $row[$colFile[$i]["column_name"]] . "'";
                                                    }
                                                }
                                                if($colFile[$i]["required"] == 1 and empty($row[$colFile[$i]["column_name"]]))
                                                {
                                                    unset($param[$ta["table_bdd"]][$countRow]);
                                                }
                                            }
                                        }
                                        
                                        if($ta["table_bdd"]=="creance" and $this->in_array_r("creance",$tableDb))
                                        {
                                            if(!$this->in_array_r(5,$colDb))
                                            {
                                                $param[$ta["table_bdd"]][$countRow].= ",'Sys" . $valCinF[$countRow] . "-".($countRow+1)."'";
                                            }
                                        }
                                        $countRow++;
                                    }
                                }
                            }
                            
                            $model = $importByType->getIdModel()->getId();
                            $query = $this->em->createQuery('SELECT t from App\Entity\CorresColu t where t.id_model_import = :idSchema 
                            and t.id_col_params is null 
                            and t.id_model_import in (select m.id from App\Entity\ModelImport m where m.type = :type) ');
                            $query->setParameter('idSchema', $model);
                            $query->setParameter('type','creance');
                            $champPersonalise = $query->getResult();

                            if(count($val)>0 and count($param)>0)
                            {
                                foreach($tables as $ta)
                                {
                                    $nameTable="creance";
                                    if($ta["table_bdd"]==$nameTable)
                                    {
                                        $lastKey = key(array_slice($param[$ta["table_bdd"]], -1, 1, true));
                                        for($i=0;$i<($lastKey+1);$i++)
                                        {
                                            if(array_key_exists($i,$param[$ta["table_bdd"]]) and $id_debiteur[$i]!="")
                                            {
                                                // if(!$this->in_array_r("dossier",$tableDb_dossier))
                                                // {
                                                //     try{
                                                //         $sql="SELECT count(d.id) FROM `debt_force_integration`.`dossier_dbi` d where d.id_import = :id";
                                                //         $stmt = $this->conn->prepare($sql);
                                                //         $stmt->bindValue(":id",$id_import);
                                                //         $countDossDbi = $stmt->executeQuery()->fetchOne();

                                                //         $query = $this->em->createQuery('SELECT max(t.id) from App\Entity\Dossier t');
                                                //         $maxNum = $query->getSingleScalarResult();
                                                //         if(!$maxNum)
                                                //         {
                                                //             $maxNum=0;
                                                //         }
                                                //         $maxNum = $countDossDbi + $maxNum;
                                                //         $refDossier=$porte_feuille->getNumeroPtf()."-".($maxNum+1);
                                                //         $numDossier[$i]=$refDossier;
                                                //         if(!$this->in_array_r(5,$colDb))
                                                //         {
                                                //             $param[$ta->getTableBdd()][$i].= ",'Sys".$valCinF[$i]."-".($i+1) ."'";
                                                //             $numCreance[$i]="Sys".$valCinF[$i]."-".($i+1);
                                                //         }
                                                //         if($this->integrationRepo->testCreanceDbi($numCreance[$i],$porte_feuille->getId())==false)
                                                //         {
                                                //             if($this->integrationRepo->testDossierDbi($refDossier,$porte_feuille->getId())==false){
                                                //                 if($cinGest[$i]!=""){
                                                //                     $sql="insert into `debt_force_integration`.`dossier_dbi`(date_creation,numero_dossier,id_users,id_ptf,id_import) values(now(),'".$refDossier."','".$cinGest[$i]."',".$porte_feuille->getId().",".$id_import.")";
                                                //                 }else{
                                                //                     $sql="insert into `debt_force_integration`.`dossier_dbi`(date_creation,numero_dossier,id_ptf,id_import) values(now(),'".$refDossier."',".$porte_feuille->getId().",".$id_import.")";
                                                //                 }
                                                //                 $stmt = $this->conn->prepare($sql)->executeQuery();                                                        
                                                //                 // if($cinGest[$i]!=""){
                                                //                 //     $query = $this->em->createQuery('SELECT max(t.id) from App\Entity\Dossier t');
                                                //                 //     $dossier = $query->getSingleScalarResult();
                                                //                 //     $tableDispatch[$cr]=$dossier;
                                                //                 //     $cr++;
                                                //                 // }

                                                //                 if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Dossier ajouté')==true)
                                                //                 {
                                                //                     $countv++;
                                                //                 }
                                                //             }else{
                                                //                 if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Dossier n° '.$refDossier.' existe déjà')==true)
                                                //                 {
                                                //                     $countnv++;
                                                //                 }
                                                //             }
                                                            
                                                //         }
                                                //     }
                                                //     catch (\Exception $e) {
                                                //         if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),$e->getMessage())==true)
                                                //         {
                                                //             $countnv++;
                                                //         }
                                                //     }
                                                // }
                                                if($this->integrationRepo->testCreanceDbi($numCreance[$i],$porte_feuille->getId())==false)
                                                {
                                                    try {
                                                        // if(array_key_exists($i,$numDossier) and $numDossier[$i]!="")
                                                        // {
                                                            // $d = $this->integrationRepo->testDossierDbi1($numDossier[$i],$porte_feuille->getId());
                                                            // if($d["exist"])
                                                            // {
                                                                // if(!array_key_exists($d["doss"],$dispatch1))
                                                                // {
                                                                //     $dispatch1[$d]=$d["doss"];
                                                                // }
                                                                // $dossierId=",".$d["doss"];
                                                                // $sql="insert into `debt_force_integration`.`creance_dbi`(etat,id_ptf_id,id_dossier".$val[$ta["table_bdd"]].",origine_doss,id_import) values(6,".$porte_feuille->getId().$dossierId.$param[$ta["table_bdd"]][$i].",".$d["place"].",".$id_import.")";
                                                                // $this->em->getConnection()->prepare($sql)->execute();
                                                            // }
                                                            // else
                                                            // {
                                                                // $sql="insert into `debt_force_integration`.`creance_dbi`(etat,id_ptf_id".$val[$ta["table_bdd"]].",id_import) values(1,".$porte_feuille->getId().$param[$ta["table_bdd"]][$i].",".$id_import.")";
                                                                // $this->em->getConnection()->prepare($sql)->execute();
                                                            // }
                                                        // }
                                                        // else
                                                        // {
                                                        //     $sql="insert into `debt_force_integration`.`creance_dbi`(etat,id_ptf_id".$val[$ta["table_bdd"]].",id_import) values(1,".$porte_feuille->getId().$param[$ta["table_bdd"]][$i].",".$id_import.")";
                                                        //     $this->em->getConnection()->prepare($sql)->execute();
                                                        // }
                                                        
                                                        $idDeb = $this->integrationRepo->testDebiteurIdDeb($id_debiteur[$i],$integrationId);
                                                        if(!$idDeb["exist"])
                                                        {
                                                            if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Debiteur avec cin '.$valCinF[$i].' n\'existe pas !!')==true)
                                                            {
                                                                $countnv++;
                                                            }
                                                        }
                                                        else
                                                        {      
                                                            $typeDetails = $this->integrationRepo->findTypeDeb($type_deb[$i]);
                                                            $typeCreance = $this->integrationRepo->findTypeCreance($type_creance[$i]);
                                                            // if($typeDetails)
                                                            // {
                                                                if($typeCreance){
                                                                    $sql="insert into `debt_force_integration`.`creance_dbi`(etat,id_ptf_id".$val[$ta["table_bdd"]].",id_import, type_creance,id_integration) values(1,".$porte_feuille->getId().$param[$ta["table_bdd"]][$i].",".$id_import.",".$typeCreance.",".$integrationId.")";
                                                                    $this->em->getConnection()->prepare($sql)->execute();
                                                                    $creance = $this->integrationRepo->testCreanceDbi1($numCreance[$i],$porte_feuille->getId());
                                                                    // $typeDeb = $this->integrationRepo->testTypeDebiteur1($idDeb["deb"],$creance["creance"]);

                                                                    $typeDeb2 = $this->integrationRepo->testTypeDebiteur2($idDeb["deb"],$id_debiteur[$i],$numDossier[$i]);
                                                                    
                                                                    if($typeDeb2["exist"])
                                                                    {
                                                                        if($typeDeb2["type_d"]["id_creance_id"] == 0){
                                                                            $sql="SELECT max(t.id) FROM   `debt_force_integration`.`creance_dbi` t ";
                                                                            $stmt = $this->conn->prepare($sql);
                                                                            $stmt = $stmt->executeQuery();
                                                                            $creanceMax = $stmt->fetchOne();

                                                                            $sql = "UPDATE `debt_force_integration`.`type_debiteur_dbi` SET `id_creance_id`=".$creanceMax." WHERE id = ".$typeDeb2["type_d"]["id"]."";
                                                                            $this->em->getConnection()->prepare($sql)->execute();
                                                                        }
                                                                    //     $typeDeb1 = $this->integrationRepo->testTypeDebiteur2($idDeb["deb"],$creance["creance"]);
                                                                    //     $c=",".$creance["creance"];
                                                                    //     $origin_creance=$creance["place"];
                                                                    //     $origin_deb=$idDeb["place"];
                                                                    //     $sql="insert into `debt_force_integration`.`type_debiteur_dbi`(id_debiteur_id,id_creance_id,type,origin_deb,origin_creance,id_integration) values(".$idDeb["deb"].$c.",".$typeDetails.",".$origin_deb.",".$origin_creance.",".$integrationId.")";
                                                                    //     $this->em->getConnection()->prepare($sql)->execute();
                                                                    }


                                                                    if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Créance ajouté')==true)
                                                                    {
                                                                        $countv++;
                                                                    }
                                                                }else{
                                                                    if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),"Type creance ".$type_creance[$i]." n'existe pas !!")==true)
                                                                    {
                                                                        $countnv++;
                                                                    }
                                                                }
                                                            // }
                                                            // else
                                                            // {
                                                            //     if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),"Type débiteur ".$type_deb[$i]." n'existe pas !!")==true)
                                                            //     {
                                                            //         $countnv++;
                                                            //     }
                                                            // }
                                                        }
                                                        // if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Créance ajouté')==true)
                                                        // {
                                                        //     $countv++;
                                                        // }
                                                        // if($this->integrationRepo->addToLogImportTypeDeb(1,$a->getId(),'Type débiteur ajouté')==true)
                                                        // {
                                                        //     $countv++;
                                                        // }
                                                    } catch (\Exception $e) {
                                                        if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),$e->getMessage())==true)
                                                        {
                                                            $countnv++;
                                                        }
                                                    }
                                                }
                                                else
                                                {
                                                    if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Le numéro de créance '.$numCreance[$i].' existe déjà dans le portefeuille !!')==true)
                                                    {
                                                        $countnv++;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                $model = $importByType->getIdModel()->getId();
                                $query = $this->em->createQuery('SELECT t from App\Entity\CorresColu t where t.id_model_import = :idSchema 
                                and t.id_col_params is null 
                                and t.id_model_import in (select m.id from App\Entity\ModelImport m where m.type = :type) ');
                                $query->setParameter('idSchema', $model);
                                $query->setParameter('type','detail_creance');
                                $champPersonalise = $query->getResult();
    
                                foreach ($tables as $ta)
                                {
                                    $nameTable="detail_creance";
                                    if($ta["table_bdd"]==$nameTable)
                                    {
                                        $lastKey = key(array_slice($param[$ta["table_bdd"]], -1, 1, true));
                                        for($i=0;$i<($lastKey+1);$i++)
                                        {
                                            if(array_key_exists($i,$param[$ta["table_bdd"]]) and $id_debiteur[$i]!="")
                                            {
                                                try {
                                                    $creanceData =$this->integrationRepo->testCreanceDbi2($numCreance[$i], $porte_feuille->getId());
                                                    if($creanceData["exist"] == false)
                                                    {
                                                        if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Créance n\'existe pas !!')==true)
                                                        {
                                                            $countnv++;
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $total_crenace = 0;
                                                        if($principale[$i] != ""){
                                                            $total_crenace += $principale[$i];
                                                        }
                                                        if($frais[$i] != ""){
                                                            $total_crenace += $frais[$i];
                                                        }
                                                        if($interet[$i] != ""){
                                                            $total_crenace += $interet[$i];
                                                        }
                                                        if($creanceData["place"] == 2)
                                                        {
                                                            $total_crenace += $creanceData["creance"]["total_creance"];
                                                            $sql="UPDATE `debt_force_integration`.`creance_dbi` SET `total_creance`= ".$total_crenace."  WHERE id = ".$creanceData["creance"]["id"]."";
                                                            $stmt = $this->conn->prepare($sql)->executeQuery();
                                                        }
                                                        $sql="insert into `debt_force_integration`.`detail_creance_dbi`(id_creance , frais,interet,principale,origin_creance) values(".$creanceData["creance"]["id"].", ".$frais[$i]." , ".$interet[$i]." ,".$principale[$i].",".$creanceData["place"].")";
                                                        $stmt = $this->conn->prepare($sql)->executeQuery();

                                                        if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Détail créance ajouté')==true)
                                                        {
                                                            $countv++;
                                                        }
                                                    }
                                                }
                                                catch (\Exception $e) {
                                                    if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),$e->getMessage())==true)
                                                    {
                                                        $countnv++;
                                                    }
                                                }   
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                //TODO:END creance 


                //Start garantie
                $importByType = $integrationRepo->getOneImportType($integrationId , "garantie");

                if($importByType){
                    //Etat en cours
                    $importByType->setEtat(2);
                    $this->em->flush();
    
                    $id_import = $importByType->getId();
    
                    $detailsImp = $integrationRepo->getDetailsImprt($id_import , $ordre);
    
                    //grouping les tables by table_bdd 
                    $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id
                        in(select (c.id_col_params) from  App\Entity\CorresColu  c where  c.id_model_import=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type) )group by t.table_bdd');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','garantie');
                    $tables = $query->getResult();
                    $tables_garantie = $tables;
    
                    $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where (t.id_model_import)=:idSchema and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type)');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','garantie');
                    $colFile = $query->getResult();
    
                    $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                    from App\Entity\CorresColu c where (c.id_model_import)=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type))');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','garantie');
                    $tableDb = $query->getResult();

                    $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where t.origin_champ = 2 
                    and (t.id_model_import)=:idSchema and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type )');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','garantie');
                    $colFileC = $query->getResult();
    
                    $query = $this->em->createQuery('SELECT t.id from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                    from App\Entity\CorresColu c where (c.id_model_import)=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type))');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','garantie');
                    $colDb = $query->getResult();
    
                    $param=array();
                    $val=array();
    
                    $response="";
    
                    $filePath = $detailsImp->getUrl();
                    if (($handle = fopen($filePath, "r")) !== FALSE)
                    {
                        while (($data = fgetcsv($handle, 1000000, ";")) !== FALSE)
                        {
                            break;
                        }
                        //Les entétes de fichier excel
                        $data=array_map("utf8_encode",$data);
                        $data=array_map('trim', $data);
                        $data1=array_map("utf8_encode",str_replace(" ","_",$data));
                        
                        for ($i=0; $i < count($colFile); $i++) { 
                            if(!in_array($colFile[$i]["column_name"],$data1))
                            {
                                $response="ENTETE_IDENTIQUE";
                                $codeStatut="ENTETE_IDENTIQUE";
                            }
                        }
                        
                        if($response == ""){
                            $a=new actionsImportDbi();
                            $a->setEtat(0);
                            $a->setCodeAction("Ajo");
                            $a->setDateDebut(new \DateTime());
                            $a->setIdImport($importByType->getId());
                            $a->setTitre("Ajout");
                            $emDbi->persist($a);
                            $emDbi->flush();
    
                            // $a = $this->integrationRepo->createActionsImport
    
                            $importByType->setEtat(2);
                            $this->em->flush(); 
    
                            foreach($tables as $ta){
                                $val[$ta["table_bdd"]]="";
                                if($ta["table_bdd"]=="debiteur")
                                {
                                    $val[$ta["table_bdd"]].="cin_formate";
                                }
                                for ($i = 0; $i < count($colFile); $i++)
                                {   
                                    if(($colFile[$i]["origine"] == 1)){
                                        $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                        
                                        if ($colParam->getTableBdd() == $ta["table_bdd"] and $colParam->getTitreCol()!="cin_gestionnaire")
                                        {
                                            $val[$ta["table_bdd"]].=",".$colParam->getTitreCol();
                                        }
                                    }
                                }
                                if($ta["table_bdd"]=="creance")
                                {
                                    if(!$this->in_array_r(5,$colDb))
                                    {
                                        $val[$ta["table_bdd"]].=",numero_creance";
                                    }
                                }
                            }
    
                            $data=$this->convert($filePath,";");
                            $valCinF=array();
                            $valCin=array();
                            foreach ($tables as $ta){
                                if($data){
                                    $countRow=0;
                                    foreach ($data as $row)
                                    {
                                        $valCin[$countRow]="";
                                        $valCinF[$countRow]="";
                                        $principale[$countRow]="";
                                        $frais[$countRow]="";
                                        $interet[$countRow]="";
    
                                        $valRaison[$countRow]="";
                                        $cinGest[$countRow]="";
                                        $param[$ta["table_bdd"]][$countRow]="";

                                        $champ[$countRow]="";
                                        
                                        for ($i=0; $i <count($colFileC) ; $i++) 
                                        { 
                                            $champ[$countRow."-".$i] = $row[$colFileC[$i]["column_name"]];
                                        }
                                
                                        for ($i = 0; $i < count($colFile); $i++)
                                        {
                                            if($colDb[$i]["id"]==75 and $row[$colFile[$i]["nomCol"]]!="")
                                            {
                                                $gest = $this->em->getRepository(Utilisateurs::class)->findOneBy(["cin"=>$row[$colFile[$i]["nomCol"]]]);
                                                if($gest)
                                                {
                                                    $cinGest[$countRow]=$gest->getId();
                                                }
                                            }
                                            if($colDb[$i]["id"]==23)
                                            {
                                                $cin=$row[$colFile[$i]["column_name"]];
                                                $valCin[$countRow]=$cin;
                                                $valCinF[$countRow]=strtoupper(preg_replace('/[^A-Za-z0-9]+/',"", $cin));
                                                if(!$this->in_array_r(5,$colDb))
                                                {
                                                    $numCreance[$countRow]="Sys".$valCinF[$countRow]."-".($countRow+1);
                                                }
                                            }
                                            if($colDb[$i]["id"]==5)
                                            {
                                                $numCreance[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==15)
                                            {
                                                $row[$colFile[$i]["column_name"]]=str_replace(" ","",$row[$colFile[$i]["column_name"]]);
                                            }
                                            if($colDb[$i]["id"]==65)
                                            {
                                                $numDossier[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==167)
                                            {
                                                $principale[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==168)
                                            {
                                                $frais[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==169)
                                            {
                                                $interet[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                            if ($colParam->getTableBdd() == $ta["table_bdd"])
                                            {
                                            $isDate = $this->em->getRepository(ColumnsParams::class)->find($colDb[$i]["id"]);
                                                if ($isDate->getIsDate() == 1 and \DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])==true)
                                                {
                                                    $valueDate=\DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])->format("d-m-Y");
                                                    $param[$ta["table_bdd"]][$countRow].= ",'" . date_format(new \DateTime($valueDate), 'Y-m-d H:i:s') . "'";
                                                }
                                                else
                                                {
                                                    if($colDb[$i]["id"]!=75)
                                                    {
                                                        $param[$ta["table_bdd"]][$countRow].= ",'" . $row[$colFile[$i]["column_name"]] . "'";
                                                    }
                                                }
                                                if($colFile[$i]["required"] == 1 and empty($row[$colFile[$i]["column_name"]]))
                                                {
                                                    unset($param[$ta["table_bdd"]][$countRow]);
                                                }
                                            }
                                        }
                                        
                                        if($ta["table_bdd"]=="creance" and $this->in_array_r("creance",$tableDb))
                                        {
                                            if(!$this->in_array_r(5,$colDb))
                                            {
                                                $param[$ta["table_bdd"]][$countRow].= ",'Sys" . $valCinF[$countRow] . "-".($countRow+1)."'";
                                            }
                                        }
                                        $countRow++;
                                    }
                                }
                            }
                            
                            if(count($val)>0 and count($param)>0)
                            {
    
                                // /*
                                //     ETAT Si creance d"ja exite 3
                                //     ETAT Si exeption 2
                                //     ETAT Si debiteur n'existe pas 4
                                //     L'état debiteur si l'opération éffectue 
                                // */
                                $model = $importByType->getIdModel()->getId();
                                $query = $this->em->createQuery('SELECT t from App\Entity\CorresColu t where t.id_model_import = :idSchema 
                                and t.id_col_params is null 
                                and t.id_model_import in (select m.id from App\Entity\ModelImport m where m.type = :type) ');
                                $query->setParameter('idSchema', $model);
                                $query->setParameter('type','garantie');
                                $champPersonalise = $query->getResult();
    
                                foreach ($tables as $ta)
                                {
                                    $nameTable="garantie";
                                    if($ta["table_bdd"]==$nameTable)
                                    {
                                        $lastKey = key(array_slice($param[$ta["table_bdd"]], -1, 1, true));
                                        for($i=0;$i<($lastKey+1);$i++)
                                        {
                                            if(array_key_exists($i,$param[$ta["table_bdd"]]) and $valCinF[$i]!="")
                                            {
                                                try {
                                                    $debExist = $this->integrationRepo->testDebiteurDbi1($valCinF[$i]);
                                                    if($debExist["exist"] == false)
                                                    {
                                                        if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Debiteur avec cin '.$valCinF[$i].' n\'existe pas !!')==true)
                                                        {
                                                            $countnv++;
                                                        }
                                                    }
                                                    else
                                                    {
                                                        // $idDeb=$idDeb->getId();
                                                        $sql="insert into `debt_force_integration`.`garantie_dbi`(id_import".$val[$ta["table_bdd"]].") values(".$id_import.$param[$ta["table_bdd"]][$i].")";
                                                        $stmt = $this->conn->prepare($sql)->executeQuery();
    
                                                        // $query = $this->em->createQuery('SELECT max(t.id) from App\Entity\GarantieDbi t');
                                                        // $garantieMax = $query->getSingleScalarResult();
    
                                                        $sql="SELECT max(t.id) FROM   `debt_force_integration`.`garantie_dbi` t ";
                                                        $stmt = $this->conn->prepare($sql);
                                                        $stmt = $stmt->executeQuery();
                                                        $garantieMax = $stmt->fetchOne();
    
                                                        
                                                        if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Garantie ajouté')==true)
                                                        {
                                                            $countv++;
                                                        }
                                                        $sql="insert into debt_force_integration.garantie_debiteur_dbi( `id_garantie`, `id_debiteur`, `origin_deb`,`id_import`) values(".$garantieMax.",".$debExist["deb"].",".$debExist["place"].",".$id_import.")";
                                                        $stmt = $this->conn->prepare($sql)->executeQuery();
                                                        // if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Débiteur garantie ajouté')==true)
                                                        // {
                                                        //     $countv++;
                                                        // }
    
                                                        if($numCreance[$i] != ""){
                                                            $creanceExist = $this->integrationRepo->testCreanceDbi1($numCreance[$i], $porte_feuille->getId());
                                                            // $query= $this->em->createQuery("select c from App\Entity\Creance c where c.numero_creance=:num and identity(c.id_ptf) =:idPtf");
                                                            // $query->setParameter('num', $numCreance[$i]);
                                                            // $query->setParameter('idPtf', $porte_feuille->getId());
                                                            // $creance=$query->getSingleResult();
                                                            if($creanceExist["exist"] == true)
                                                            {
                                                                $sql="insert into debt_force_integration.creance_garantie_dbi( `id_garantie`, `id_creance`, `origin_creance`,`id_import`) values(".$garantieMax.",".$creanceExist["creance"].",".$creanceExist["place"].",".$id_import.")";
                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                            }
                                                        }
                                                    }
                                                }
                                                catch (\Exception $e) {
                                                    if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),$e->getMessage())==true)
                                                    {
                                                        $countnv++;
                                                    }
                                                }   
                                            }
                                        }
                                    }
                                }
                            }
                            //Set l'état d'importation d'import
                            // $sql="SELECT l.* from logs_actions l where l.etat=0 and l.id_action_id in(select a.id from actions_import a where a.id_import_id = :id)";
                            // $param=(array("id"=>$importByType->getId() ));
                            // $error_import = $this->conn->fetchAllAssociative($sql , $param);
                            // if(count($error_import) == 0){
                            //     $importByType->setEtat(4);
                            // }else{
                            //     $importByType->setEtat(3);
                            // }
                            // $this->em->flush();
                            // $this->verifecationDoublent($a->getId());
                        }
                    }
                }

                //Start
                $importByType = $integrationRepo->getOneImportType($integrationId , "proc");

                if($importByType){
                    //Etat en cours
                    $importByType->setEtat(2);
                    $this->em->flush();
    
                    $id_import = $importByType->getId();
    
                    $detailsImp = $integrationRepo->getDetailsImprt($id_import , $ordre);
    
                    //grouping les tables by table_bdd 
                    $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id
                        in(select (c.id_col_params) from  App\Entity\CorresColu  c where  c.id_model_import=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type) )group by t.table_bdd');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','proc');
                    $tables = $query->getResult();
                    $tables_proc = $tables;
    
                    $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where (t.id_model_import)=:idSchema and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type)');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','proc');
                    $colFile = $query->getResult();

                    $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where t.origin_champ = 2 
                    and (t.id_model_import)=:idSchema and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type )');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','proc');
                    $colFileC = $query->getResult();
    
                    $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                    from App\Entity\CorresColu c where (c.id_model_import)=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type))');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','proc');
                    $tableDb = $query->getResult();
    
                    $query = $this->em->createQuery('SELECT t.id from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                    from App\Entity\CorresColu c where (c.id_model_import)=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type))');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','proc');
                    $colDb = $query->getResult();
    
                    $param=array();
                    $val=array();
                    $response="";
    
                    $filePath = $detailsImp->getUrl();
                    if (($handle = fopen($filePath, "r")) !== FALSE)
                    {
                        while (($data = fgetcsv($handle, 1000000, ";")) !== FALSE)
                        {
                            break;
                        }
                        //Les entétes de fichier excel
                        $data=array_map("utf8_encode",$data);
                        $data=array_map('trim', $data);
                        $data1=array_map("utf8_encode",str_replace(" ","_",$data));
                        
                        for ($i=0; $i < count($colFile); $i++) { 
                            if(!in_array($colFile[$i]["column_name"],$data1))
                            {
                                $response="ENTETE_IDENTIQUE";
                                $codeStatut="ENTETE_IDENTIQUE";
                            }
                        }

                        if($response == ""){
                            $a=new actionsImportDbi();
                            $a->setEtat(0);
                            $a->setCodeAction("Ajo");
                            $a->setDateDebut(new \DateTime());
                            $a->setIdImport($importByType->getId());
                            $a->setTitre("Ajout");
                            $emDbi->persist($a);
                            $emDbi->flush();
    
                            // $a = $this->integrationRepo->createActionsImport
    
                            $importByType->setEtat(2);
                            $this->em->flush(); 
    
                            foreach($tables as $ta){
                                $val[$ta["table_bdd"]]="";
                                if($ta["table_bdd"]=="debiteur")
                                {
                                    $val[$ta["table_bdd"]].="cin_formate";
                                }
                                for ($i = 0; $i < count($colFile); $i++)
                                {   
                                    if(($colFile[$i]["origine"] == 1)){
                                        $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                        
                                        if ($colParam->getTableBdd() == $ta["table_bdd"] and $colParam->getTitreCol()!="cin_gestionnaire")
                                        {
                                            $val[$ta["table_bdd"]].=",".$colParam->getTitreCol();
                                        }
                                    }
                                }
                                if($ta["table_bdd"]=="creance")
                                {
                                    if(!$this->in_array_r(5,$colDb))
                                    {
                                        $val[$ta["table_bdd"]].=",numero_creance";
                                    }
                                }
                            }
    
                            $data=$this->convert($filePath,";");
                            $valCinF=array();
                            $valCin=array();

                            foreach ($tables as $ta){
                                if($data){
                                    $countRow=0;
                                    foreach ($data as $row)
                                    {
                                        $valCin[$countRow]="";
                                        $valCinF[$countRow]="";
                                        $principale[$countRow]="";
                                        $frais[$countRow]="";
                                        $interet[$countRow]="";
    
                                        $valRaison[$countRow]="";
                                        $cinGest[$countRow]="";
                                        $param[$ta["table_bdd"]][$countRow]="";

                                        $champ[$countRow]="";
                                        
                                        for ($i=0; $i <count($colFileC) ; $i++) 
                                        { 
                                            $champ[$countRow."-".$i] = $row[$colFileC[$i]["column_name"]];
                                        }
                                
                                        for ($i = 0; $i < count($colFile); $i++)
                                        {
                                            if($colDb[$i]["id"]==75 and $row[$colFile[$i]["nomCol"]]!="")
                                            {
                                                $gest = $this->em->getRepository(Utilisateurs::class)->findOneBy(["cin"=>$row[$colFile[$i]["nomCol"]]]);
                                                if($gest)
                                                {
                                                    $cinGest[$countRow]=$gest->getId();
                                                }
                                            }
                                            if($colDb[$i]["id"]==23)
                                            {
                                                $cin=$row[$colFile[$i]["column_name"]];
                                                $valCin[$countRow]=$cin;
                                                $valCinF[$countRow]=strtoupper(preg_replace('/[^A-Za-z0-9]+/',"", $cin));
                                                if(!$this->in_array_r(5,$colDb))
                                                {
                                                    $numCreance[$countRow]="Sys".$valCinF[$countRow]."-".($countRow+1);
                                                }
                                            }
                                            if($colDb[$i]["id"]==5)
                                            {
                                                $numCreance[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==15)
                                            {
                                                $row[$colFile[$i]["column_name"]]=str_replace(" ","",$row[$colFile[$i]["column_name"]]);
                                            }
                                            if($colDb[$i]["id"]==65)
                                            {
                                                $numDossier[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==167)
                                            {
                                                $principale[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==168)
                                            {
                                                $frais[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==169)
                                            {
                                                $interet[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                            if ($colParam->getTableBdd() == $ta["table_bdd"])
                                            {
                                            $isDate = $this->em->getRepository(ColumnsParams::class)->find($colDb[$i]["id"]);
                                                if ($isDate->getIsDate() == 1 and \DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])==true)
                                                {
                                                    $valueDate=\DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])->format("d-m-Y");
                                                    $param[$ta["table_bdd"]][$countRow].= ",'" . date_format(new \DateTime($valueDate), 'Y-m-d H:i:s') . "'";
                                                }
                                                else
                                                {
                                                    if($colDb[$i]["id"]!=75)
                                                    {
                                                        $param[$ta["table_bdd"]][$countRow].= ",'" . $row[$colFile[$i]["column_name"]] . "'";
                                                    }
                                                }
                                                if($colFile[$i]["required"] == 1 and empty($row[$colFile[$i]["column_name"]]))
                                                {
                                                    unset($param[$ta["table_bdd"]][$countRow]);
                                                }
                                            }
                                        }
                                        
                                        if($ta["table_bdd"]=="creance" and $this->in_array_r("creance",$tableDb))
                                        {
                                            if(!$this->in_array_r(5,$colDb))
                                            {
                                                $param[$ta["table_bdd"]][$countRow].= ",'Sys" . $valCinF[$countRow] . "-".($countRow+1)."'";
                                            }
                                        }
                                        $countRow++;
                                    }
                                }
                            }
                            
                            if(count($val)>0 and count($param)>0)
                            {
    
                                // /*
                                //     ETAT Si creance d"ja exite 3
                                //     ETAT Si exeption 2
                                //     ETAT Si debiteur n'existe pas 4
                                //     L'état debiteur si l'opération éffectue 
                                // */
                                $model = $importByType->getIdModel()->getId();
                                $query = $this->em->createQuery('SELECT t from App\Entity\CorresColu t where t.id_model_import = :idSchema 
                                and t.id_col_params is null 
                                and t.id_model_import in (select m.id from App\Entity\ModelImport m where m.type = :type) ');
                                $query->setParameter('idSchema', $model);
                                $query->setParameter('type','proc');
                                $champPersonalise = $query->getResult();
                                
                                foreach ($tables as $ta)
                                {
                                    $nameTable="proc_judicaire";
                                    if($ta["table_bdd"]==$nameTable)
                                    {
                                        $lastKey = key(array_slice($param[$ta["table_bdd"]], -1, 1, true));
                                        for($i=0;$i<($lastKey+1);$i++)
                                        {
                                            if(array_key_exists($i,$param[$ta["table_bdd"]]) and $valCinF[$i]!="")
                                            {
                                                try {
                                                    $debExist = $this->integrationRepo->testDebiteurDbi1($valCinF[$i]);
                                                    if($debExist["exist"] == false)
                                                    {
                                                        if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Debiteur avec cin '.$valCinF[$i].' n\'existe pas !!')==true)
                                                        {
                                                            $countnv++;
                                                        }
                                                    }
                                                    else
                                                    {
                                                        // $idDeb=$idDeb->getId();
                                                        $sql="insert into `debt_force_integration`.`proc_dbi`(id_import".$val[$ta["table_bdd"]].") values(".$id_import.$param[$ta["table_bdd"]][$i].")";
                                                        $stmt = $this->conn->prepare($sql)->executeQuery();
    
                                                        // $query = $this->em->createQuery('SELECT max(t.id) from App\Entity\GarantieDbi t');
                                                        // $garantieMax = $query->getSingleScalarResult();
    
                                                        $sql="SELECT max(t.id) FROM   `debt_force_integration`.`proc_dbi` t ";
                                                        $stmt = $this->conn->prepare($sql);
                                                        $stmt = $stmt->executeQuery();
                                                        $garantieMax = $stmt->fetchOne();
    
                                                        
                                                        if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Garantie ajouté')==true)
                                                        {
                                                            $countv++;
                                                        }
                                                        $sql="insert into debt_force_integration.proc_debiteur_dbi( `id_proc`, `id_debiteur`, `origin_deb`,`id_import`) values(".$garantieMax.",".$debExist["deb"].",".$debExist["place"].",".$id_import.")";
                                                        $stmt = $this->conn->prepare($sql)->executeQuery();
                                                        // if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Débiteur garantie ajouté')==true)
                                                        // {
                                                        //     $countv++;
                                                        // }
    
                                                        if($numCreance[$i] != ""){
                                                            $creanceExist = $this->integrationRepo->testCreanceDbi1($numCreance[$i], $porte_feuille->getId());
                                                            // $query= $this->em->createQuery("select c from App\Entity\Creance c where c.numero_creance=:num and identity(c.id_ptf) =:idPtf");
                                                            // $query->setParameter('num', $numCreance[$i]);
                                                            // $query->setParameter('idPtf', $porte_feuille->getId());
                                                            // $creance=$query->getSingleResult();
                                                            if($creanceExist["exist"] == true){
                                                                $sql="insert into debt_force_integration.proc_creance_dbi( `id_proc`, `id_creance`, `origin_creance`,`id_import`) values(".$garantieMax.",".$creanceExist["creance"].",".$creanceExist["place"].",".$id_import.")";
                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                            }
                                                        }
                                                    }
                                                }
                                                catch (\Exception $e) {
                                                    if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),$e->getMessage())==true)
                                                    {
                                                        $countnv++;
                                                    }
                                                }   
                                            }
                                        }
                                    }
                                }
                            }
                            //Set l'état d'importation d'import
                            // $sql="SELECT l.* from logs_actions l where l.etat=0 and l.id_action_id in(select a.id from actions_import a where a.id_import_id = :id)";
                            // $param=(array("id"=>$importByType->getId() ));
                            // $error_import = $this->conn->fetchAllAssociative($sql , $param);
                            // if(count($error_import) == 0){
                            //     $importByType->setEtat(4);
                            // }else{
                            //     $importByType->setEtat(3);
                            // }
                            // $this->em->flush();
                            // $this->verifecationDoublent($a->getId());
                        }
                    }
                }

                //Start 
                $importByType = $integrationRepo->getOneImportType($integrationId , "adresse");
                if($importByType){
                    //Etat en cours
                    $importByType->setEtat(2);
                    $this->em->flush();
                    $id_import = $importByType->getId();
                    $detailsImp = $integrationRepo->getDetailsImprt($id_import , $ordre);
    
                    //grouping les tables by table_bdd 
                    $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id
                        in(select (c.id_col_params) from  App\Entity\CorresColu  c where  c.id_model_import=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type) )group by t.table_bdd');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','adresse');
                    $tables = $query->getResult();
                    $tables_adresse = $tables;
    
                    $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where (t.id_model_import)=:idSchema and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type)');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','adresse');

                    $colFile = $query->getResult();
    
                    $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where t.origin_champ = 2 
                    and (t.id_model_import)=:idSchema and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type )');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','proc');
                    $colFileC = $query->getResult();
                    
                    $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                    from App\Entity\CorresColu c where (c.id_model_import)=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type))');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','adresse');
                    $tableDb = $query->getResult();
    
                    $query = $this->em->createQuery('SELECT t.id from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                    from App\Entity\CorresColu c where (c.id_model_import)=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type))');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','adresse');
                    $colDb = $query->getResult();
    
                    $param=array();
                    $val=array();
                    $response="";
                    $filePath = $detailsImp->getUrl();
                    if (($handle = fopen($filePath, "r")) !== FALSE)
                    {
                        while (($data = fgetcsv($handle, 1000000, ";")) !== FALSE)
                        {
                            break;
                        }
                        //Les entétes de fichier excel
                        $data=array_map("utf8_encode",$data);
                        $data=array_map('trim', $data);
                        $data1=array_map("utf8_encode",str_replace(" ","_",$data));
                        
                        for ($i=0; $i < count($colFile); $i++) { 
                            if(!in_array($colFile[$i]["column_name"],$data1))
                            {
                                $response="ENTETE_IDENTIQUE";
                                $codeStatut="ENTETE_IDENTIQUE";
                            }
                        }
                        
                        if($response == ""){
                            $a=new actionsImportDbi();
                            $a->setEtat(0);
                            $a->setCodeAction("Ajo");
                            $a->setDateDebut(new \DateTime());
                            $a->setIdImport($importByType->getId());
                            $a->setTitre("Ajout");
                            $emDbi->persist($a);
                            $emDbi->flush();
    
                            // $a = $this->integrationRepo->createActionsImport
    
                            $importByType->setEtat(2);
                            $this->em->flush(); 
                            foreach($tables as $ta){
                                $val[$ta["table_bdd"]]="";
                                if($ta["table_bdd"]=="debiteur")
                                {
                                    $val[$ta["table_bdd"]].="cin_formate";
                                }
                                for ($i = 0; $i < count($colFile); $i++)
                                {   
                                    if(($colFile[$i]["origine"] == 1)){
                                        $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                        
                                        if ($colParam->getTableBdd() == $ta["table_bdd"] and $colParam->getTitreCol()!="cin_gestionnaire")
                                        {
                                            $val[$ta["table_bdd"]].=",".$colParam->getTitreCol();
                                        }
                                    }
                                }
                                if($ta["table_bdd"]=="creance")
                                {
                                    if(!$this->in_array_r(5,$colDb))
                                    {
                                        $val[$ta["table_bdd"]].=",numero_creance";
                                    }
                                }
                            }
    
                            $data=$this->convert($filePath,";");
                            $valCinF=array();
                            $valCin=array();
                            foreach ($tables as $ta){
                                if($data){
                                    $countRow=0;
                                    foreach ($data as $row)
                                    {
                                        $valCin[$countRow]="";
                                        $valCinF[$countRow]="";
                                        $valRaison[$countRow]="";
                                        $cinGest[$countRow]="";
                                        $id_debiteur[$countRow]="";
                                        $param[$ta["table_bdd"]][$countRow]="";

                                        $champ[$countRow]="";
                                        
                                        for ($i=0; $i <count($colFileC) ; $i++) 
                                        { 
                                            $champ[$countRow."-".$i] = $row[$colFileC[$i]["column_name"]];
                                        }
                                        for ($i = 0; $i < count($colFile); $i++)
                                        {
                                            if($colDb[$i]["id"]==75 and $row[$colFile[$i]["nomCol"]]!="")
                                            {
                                                $gest = $this->em->getRepository(Utilisateurs::class)->findOneBy(["cin"=>$row[$colFile[$i]["nomCol"]]]);
                                                if($gest)
                                                {
                                                    $cinGest[$countRow]=$gest->getId();
                                                }
                                            }
                                            if($colDb[$i]["id"]==23)
                                            {
                                                $cin=$row[$colFile[$i]["column_name"]];
                                                $valCin[$countRow]=$cin;
                                                $valCinF[$countRow]=strtoupper(preg_replace('/[^A-Za-z0-9]+/',"", $cin));
                                                if(!$this->in_array_r(5,$colDb))
                                                {
                                                    $numCreance[$countRow]="Sys".$valCinF[$countRow]."-".($countRow+1);
                                                }
                                            }
                                            if($colDb[$i]["id"]==5)
                                            {
                                                $numCreance[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==15)
                                            {
                                                $row[$colFile[$i]["column_name"]]=str_replace(" ","",$row[$colFile[$i]["column_name"]]);
                                            }
                                            if($colDb[$i]["id"]==65)
                                            {
                                                $numDossier[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==167)
                                            {
                                                $principale[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==168)
                                            {
                                                $frais[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==185)
                                            {
                                                $id_debiteur[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==178)
                                            {
                                                $typeAdresse[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                            if ($colParam->getTableBdd() == $ta["table_bdd"])
                                            {
                                                $isDate = $this->em->getRepository(ColumnsParams::class)->find($colDb[$i]["id"]);
                                                    if ($isDate->getIsDate() == 1 and \DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])==true)
                                                    {
                                                        $valueDate=\DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])->format("d-m-Y");
                                                        $param[$ta["table_bdd"]][$countRow].= ",'" . date_format(new \DateTime($valueDate), 'Y-m-d H:i:s') . "'";
                                                    }
                                                    else
                                                    {
                                                        if($colDb[$i]["id"]!=75)
                                                        {
                                                            $param[$ta["table_bdd"]][$countRow].= ",'" . $row[$colFile[$i]["column_name"]] . "'";
                                                        }
                                                    }
                                                    if($colFile[$i]["required"] == 1 and empty($row[$colFile[$i]["column_name"]]))
                                                    {
                                                        unset($param[$ta["table_bdd"]][$countRow]);
                                                    }
                                            }
                                        }
                                        
                                        if($ta["table_bdd"]=="creance" and $this->in_array_r("creance",$tableDb))
                                        {
                                            if(!$this->in_array_r(5,$colDb))
                                            {
                                                $param[$ta["table_bdd"]][$countRow].= ",'Sys" . $valCinF[$countRow] . "-".($countRow+1)."'";
                                            }
                                        }
                                        $countRow++;
                                    }
                                }
                            }
                            $model = $importByType->getIdModel()->getId();
                            $query = $this->em->createQuery('SELECT t from App\Entity\CorresColu t where t.id_model_import = :idSchema 
                            and t.id_col_params is null 
                            and t.id_model_import in (select m.id from App\Entity\ModelImport m where m.type = :type) ');
                            $query->setParameter('idSchema', $model);
                            $query->setParameter('type','adresse');
                            $champPersonalise = $query->getResult();

                            if(count($val)>0 and count($param)>0)
                            {
                                $nameTable="adresse";
                                if($ta["table_bdd"]==$nameTable)
                                {
                                    $lastKey = key(array_slice($param[$ta["table_bdd"]], -1, 1, true));
                                    for($i=0;$i<($lastKey+1);$i++)
                                    {
                                        
                                        // dump($param[$ta["table_bdd"]][$i]);
                                        if(array_key_exists($i,$param[$ta["table_bdd"]]) and $id_debiteur[$i]!="")
                                        {
                                            try {
                                                $debExist = $this->integrationRepo->testDebiteurIdDeb($id_debiteur[$i],$integrationId);
                                                if($debExist["exist"] == false)
                                                {
                                                    if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Debiteur n\'existe pas !!')==true)
                                                    {
                                                        $countnv++;
                                                    }
                                                }
                                                else
                                                {
                                                    $typeAd = 0;
                                                    if($typeAdresse[$i] != ""){
                                                        $type_adresse = $integrationRepo->findTypeAdresse($typeAdresse[$i]);
                                                        if($type_adresse){
                                                            $typeAd = $type_adresse->getId();
                                                            $sql="insert into debt_force_integration.adresse_dbi(id_type_adresse,id_debiteur".$val[$ta["table_bdd"]].",origin_deb , id_import) values(".$typeAd.",".$debExist["deb"].$param[$ta["table_bdd"]][$i].",".$debExist["place"].",".$id_import.")";

                                                            $stmt = $this->conn->prepare($sql)->executeQuery();
                                                            if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Adresse ajouté')==true)
                                                            {
                                                                $countv++;
                                                            }
                                                        }
                                                    }else{
                                                        if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Type adresse '.$typeAdresse[$i].' n\'existe pas !!')==true)
                                                        {
                                                            $countnv++;
                                                        }
                                                    }
                                                }
                                            }
                                            catch (\Exception $e) {
                                                if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),$e->getMessage())==true)
                                                {
                                                    $countnv++;
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            
                            if(1)
                            {
    
                                // /*
                                //     ETAT Si creance d"ja exite 3
                                //     ETAT Si exeption 2
                                //     ETAT Si debiteur n'existe pas 4
                                //     L'état debiteur si l'opération éffectue 
                                // */

                                $idSchema = $importByType->getIdModel()->getId();
                                $colDb=$this->em
                                    ->createQuery("select t.champs from App\Entity\ImportType t where t.id_model=:idSchema")
                                    ->setParameter("idSchema",$idSchema)
                                    ->getResult();
                                $colFile=$this->em
                                    ->createQuery("select t.nom_col,t.tableBdd from App\Entity\ImportType t where t.id_model=:idSchema")
                                    ->setParameter("idSchema",$idSchema)
                                    ->getResult();
                                $ville=array();
                                $pays=array();
                                $status=array();
                                $region=array();
                                $cp=array();
                                $codePostal=array();
                                $province=array();
                                $source=array();
                                $note=array();
                                $note2=array();
                                $note3=array();
                                $numero2=array();
                                $numero3=array();
                                $status2=array();
                                $status3=array();

                            }
                        }
                    }
                }

                //END

                //Start
                $importByType = $integrationRepo->getOneImportType($integrationId , "telephone");
    

                if($importByType){
                    //Etat en cours
                    $importByType->setEtat(2);
                    $this->em->flush();
    
                    $id_import = $importByType->getId();
    
                    $detailsImp = $integrationRepo->getDetailsImprt($id_import , $ordre);
    
                    //grouping les tables by table_bdd 
                    $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id
                        in(select (c.id_col_params) from  App\Entity\CorresColu  c where  c.id_model_import=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type) )group by t.table_bdd');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','telephone');
                    $tables = $query->getResult();
                    $tables_telephone = $tables;
    
                    $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where (t.id_model_import)=:idSchema and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type)');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','telephone');
                    $colFile = $query->getResult();

                    $query = $this->em->createQuery('SELECT t.column_name,(t.id_col_params) as id_col_params,t.required  , t.origine from App\Entity\CorresColu t where t.origin_champ = 2 
                    and (t.id_model_import)=:idSchema and t.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type )');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','telephone');
                    $colFileC = $query->getResult();
    
                    $query = $this->em->createQuery('SELECT t.table_bdd from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                    from App\Entity\CorresColu c where (c.id_model_import)=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type))');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','telephone');
                    $tableDb = $query->getResult();
    
                    $query = $this->em->createQuery('SELECT t.id from App\Entity\ColumnsParams t where t.id in(select (c.id_col_params) 
                    from App\Entity\CorresColu c where (c.id_model_import)=:idSchema and c.id_model_import in(select m.id from App\Entity\ModelImport m where m.type = :type))');
                    $query->setParameter('idSchema', $importByType->getIdModel()->getId());
                    $query->setParameter('type','telephone');
                    $colDb = $query->getResult();

                    $param=array();
                    $val=array();
                    $response="";
    
                    $filePath = $detailsImp->getUrl();
                    if (($handle = fopen($filePath, "r")) !== FALSE)
                    {
                        while (($data = fgetcsv($handle, 1000000, ";")) !== FALSE)
                        {
                            break;
                        }
                        //Les entétes de fichier excel
                        $data=array_map("utf8_encode",$data);
                        $data=array_map('trim', $data);
                        $data1=array_map("utf8_encode",str_replace(" ","_",$data));
                        
                        for ($i=0; $i < count($colFile); $i++) { 
                            if(!in_array($colFile[$i]["column_name"],$data1))
                            {
                                $response="ENTETE_IDENTIQUE";
                                $codeStatut="ENTETE_IDENTIQUE";
                            }
                        }
                        if($response == ""){
                            $a=new actionsImportDbi();
                            $a->setEtat(0);
                            $a->setCodeAction("Ajo");
                            $a->setDateDebut(new \DateTime());
                            $a->setIdImport($importByType->getId());
                            $a->setTitre("Ajout");
                            $emDbi->persist($a);
                            $emDbi->flush();
                            // $a = $this->integrationRepo->createActionsImport
                            $importByType->setEtat(2);
                            $this->em->flush(); 
                            foreach($tables as $ta){
                                $val[$ta["table_bdd"]]="";
                                if($ta["table_bdd"]=="debiteur")
                                {
                                    $val[$ta["table_bdd"]].="cin_formate";
                                }
                                for ($i = 0; $i < count($colFile); $i++)
                                {   
                                    if(($colFile[$i]["origine"] == 1)){
                                        $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                        
                                        if ($colParam->getTableBdd() == $ta["table_bdd"] and $colParam->getTitreCol()!="cin_gestionnaire")
                                        {
                                            $val[$ta["table_bdd"]].=",".$colParam->getTitreCol();
                                        }
                                    }
                                }
                                if($ta["table_bdd"]=="creance")
                                {
                                    if(!$this->in_array_r(5,$colDb))
                                    {
                                        $val[$ta["table_bdd"]].=",numero_creance";
                                    }
                                }
                            }
    
                            $data=$this->convert($filePath,";");
                            $valCinF=array();
                            $valCin=array();
                            foreach ($tables as $ta){
                                if($data){
                                    $countRow=0;
                                    foreach ($data as $row)
                                    {
                                        $valCin[$countRow]="";
                                        $valCinF[$countRow]="";
                                        $valRaison[$countRow]="";
                                        $cinGest[$countRow]="";
                                        $id_debiteur[$countRow]="";
                                        $param[$ta["table_bdd"]][$countRow]="";

                                        $champ[$countRow]="";
                                        
                                        for ($i=0; $i <count($colFileC) ; $i++) 
                                        { 
                                            $champ[$countRow."-".$i] = $row[$colFileC[$i]["column_name"]];
                                        }

                                        for ($i = 0; $i < count($colFile); $i++)
                                        {
                                            if($colDb[$i]["id"]==75 and $row[$colFile[$i]["nomCol"]]!="")
                                            {
                                                $gest = $this->em->getRepository(Utilisateurs::class)->findOneBy(["cin"=>$row[$colFile[$i]["nomCol"]]]);
                                                if($gest)
                                                {
                                                    $cinGest[$countRow]=$gest->getId();
                                                }
                                            }
                                            if($colDb[$i]["id"]==23)
                                            {
                                                $cin=$row[$colFile[$i]["column_name"]];
                                                $valCin[$countRow]=$cin;
                                                $valCinF[$countRow]=strtoupper(preg_replace('/[^A-Za-z0-9]+/',"", $cin));
                                                if(!$this->in_array_r(5,$colDb))
                                                {
                                                    $numCreance[$countRow]="Sys".$valCinF[$countRow]."-".($countRow+1);
                                                }
                                            }
                                            if($colDb[$i]["id"]==5)
                                            {
                                                $numCreance[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==15)
                                            {
                                                $row[$colFile[$i]["column_name"]]=str_replace(" ","",$row[$colFile[$i]["column_name"]]);
                                            }
                                            if($colDb[$i]["id"]==65)
                                            {
                                                $numDossier[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==167)
                                            {
                                                $principale[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==168)
                                            {
                                                $frais[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==178)
                                            {
                                                $typeTelephone[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            if($colDb[$i]["id"]==185)
                                            {
                                                $id_debiteur[$countRow]=$row[$colFile[$i]["column_name"]];
                                            }
                                            $colParam = $this->em->getRepository(ColumnsParams::class)->find($colFile[$i]["id_col_params"]);
                                            if ($colParam->getTableBdd() == $ta["table_bdd"])
                                            {
                                                $isDate = $this->em->getRepository(ColumnsParams::class)->find($colDb[$i]["id"]);
                                                    if ($isDate->getIsDate() == 1 and \DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])==true)
                                                    {
                                                        $valueDate=\DateTime::createFromFormat('d/m/Y', $row[$colFile[$i]["column_name"]])->format("d-m-Y");
                                                        $param[$ta["table_bdd"]][$countRow].= ",'" . date_format(new \DateTime($valueDate), 'Y-m-d H:i:s') . "'";
                                                    }
                                                    else
                                                    {
                                                        if($colDb[$i]["id"]!=75)
                                                        {
                                                            $param[$ta["table_bdd"]][$countRow].= ",'" . $row[$colFile[$i]["column_name"]] . "'";
                                                        }
                                                    }
                                                    if($colFile[$i]["required"] == 1 and empty($row[$colFile[$i]["column_name"]]))
                                                    {
                                                        unset($param[$ta["table_bdd"]][$countRow]);
                                                    }
                                            }
                                        }
                                        
                                        if($ta["table_bdd"]=="creance" and $this->in_array_r("creance",$tableDb))
                                        {
                                            if(!$this->in_array_r(5,$colDb))
                                            {
                                                $param[$ta["table_bdd"]][$countRow].= ",'Sys" . $valCinF[$countRow] . "-".($countRow+1)."'";
                                            }
                                        }
                                        $countRow++;
                                    }
                                }
                            }
                            $model = $importByType->getIdModel()->getId();
                            $query = $this->em->createQuery('SELECT t from App\Entity\CorresColu t where t.id_model_import = :idSchema 
                            and t.id_col_params is null 
                            and t.id_model_import in (select m.id from App\Entity\ModelImport m where m.type = :type) ');
                            $query->setParameter('idSchema', $model);
                            $query->setParameter('type','telephone');
                            $champPersonalise = $query->getResult();
                            if(count($val)>0 and count($param)>0)
                            {
                                $nameTable="telephone";
                                if($ta["table_bdd"]==$nameTable)
                                {
                                    $lastKey = key(array_slice($param[$ta["table_bdd"]], -1, 1, true));
                                    for($i=0;$i<($lastKey+1);$i++)
                                    {
                                        // dump($param[$ta["table_bdd"]][$i]);
                                        if(array_key_exists($i,$param[$ta["table_bdd"]]) and $valCinF[$i]!="")
                                        {
                                            try {
                                                $debExist = $this->integrationRepo->testDebiteurIdDeb($id_debiteur[$i],$integrationId);
                                                if($debExist["exist"] == false)
                                                {
                                                    if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Debiteur n\'existe pas !!')==true)
                                                    {
                                                        $countnv++;
                                                    }
                                                }
                                                else
                                                {
                                                    if($typeTelephone[$i] != ""){
                                                        $type_telephone = $integrationRepo->findtypeTelephone($typeTelephone[$i]);
                                                        if($type_telephone){
                                                            $typeTel = $type_telephone->getId();
                                                            $sql="insert into debt_force_integration.telephone_dbi(id_type_telephone,id_debiteur".$val[$ta["table_bdd"]].",origin_deb , id_import) values(".$typeTel.",".$debExist["deb"].$param[$ta["table_bdd"]][$i].",".$debExist["place"].",".$id_import.")";
                                                            $stmt = $this->conn->prepare($sql)->executeQuery();
                                                            if($this->integrationRepo->addToLogImportDbi(1,$a->getId(),'Téléphone ajouté')==true)
                                                            {
                                                                $countv++;
                                                            }
                                                        }
                                                    }else{
                                                        if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),'Type adresse '.$typeAdresse[$i].' n\'existe pas !!')==true)
                                                        {
                                                            $countnv++;
                                                        }
                                                    }
                                                }
                                            }
                                            catch (\Exception $e) {
                                                if($this->integrationRepo->addToLogImportDbi(0,$a->getId(),$e->getMessage())==true)
                                                {
                                                    $countnv++;
                                                }
                                            }   
                                        }
                                    }
                                }
                            }
                            
                            //Set l'état d'importation d'import
                            // $sql="SELECT l.* from logs_actions l where l.etat=0 and l.id_action_id in(select a.id from actions_import a where a.id_import_id = :id)";
                            // $param=(array("id"=>$importByType->getId() ));
                            // $error_import = $this->conn->fetchAllAssociative($sql , $param);
                            // if(count($error_import) == 0){
                            //     $importByType->setEtat(4);
                            // }else{
                            //     $importByType->setEtat(3);
                            // }
                            // $this->em->flush();
                            // $this->verifecationDoublent($a->getId());
                        }
                    }
                }

                //END
                //END

                

                

                // Set l'état d'integration d'import
                // $sql = "SELECT l.* from import l where l.id_integration_id  = :id AND l.etat = 3 or l.etat = 4 ";
                // $param=(array("id"=>$integrationId ));
                // $imports = $this->conn->fetchAllAssociative($sql , $param);
                // if(count($imports) == 3){
                //     $sql="SELECT l.* from logs_actions l where l.etat=0 and l.id_action_id in(select a.id from actions_import a where a.id_import_id in (select i.id FROM import i where i.id_integration_id  = :id ))";
                //     $param=(array("id"=>$integrationId ));
                //     $error = $this->conn->fetchAllAssociative($sql , $param);
                    
                //     if(count($error) == 0){
                //         $IntegrationNonCommencer[$t]->setEtat(4);
                //     }else{
                //         $IntegrationNonCommencer[$t]->setEtat(3);
                //     }
                //     $this->em->flush();
                // }
                // $IntegrationNonCommencer[$t]->setEtat(4);
                // $this->em->flush();

                //todo
                $sql="update integration set status_id = 4 , `date_fin_execution_".$ordre."`=now() where id = ".$IntegrationNonCommencer[$t]->getId()."";
                $stmt = $this->conn->prepare($sql)->executeQuery();
            }
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/importToPROD', methods : ["POST"])]
    public function startIntegrationToDBPROD(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request ): JsonResponse
    {
        ini_set('memory_limit','-1');
        ini_set('memory_size','-1');
        ini_set('max_execution_time','-1');
        $respObjects =array();
        $codeStatut = "ERROR";
        $IntegrationNonCommencer = $integrationRepo->getAllInegrationByStatus6();

        if($IntegrationNonCommencer){
            for ($t=0; $t <count($IntegrationNonCommencer) ; $t++) { 
                $integrationId =  $IntegrationNonCommencer[$t]->getId();

                if($IntegrationNonCommencer[$t]->getStatus()->getId() == 6){
                    $sql = 'CALL debt_force_integration.PROC_ROOLBACK('.$integrationId.');';
                    $stmt = $integrationRepo->executeSQL($sql);

                    $sql="INSERT INTO `logs_actions_integration`( `id_integration`, `logs`, `date_creation`,`etat`) VALUES (".$integrationId.",'Relancer l\'intégration',now(),1)";
                    $stmt = $integrationRepo->executeSQL($sql);
                }else{
                    $sql="INSERT INTO `logs_actions_integration`( `id_integration`, `logs`, `date_creation`,`etat`) VALUES (".$integrationId.",'Import dans la base production',now(),1)";
                    $stmt = $integrationRepo->executeSQL($sql);
                }

                $sql="UPDATE `integration` SET `status_id` = '6' WHERE `integration`.`id` = ".$integrationId.";";
                $stmt = $integrationRepo->executeSQL($sql);
                

                $importByType = $integrationRepo->getOneImportType($integrationId , "debiteur");
                $a=new ActionsImport();
                $a->setEtat(0);
                $a->setCodeAction("Ajo");
                $a->setDateDebut(new \DateTime());
                $a->setIdImport($importByType);
                $a->setTitre("Ajout");
                $this->em->persist($a);
                $this->em->flush();

                $integrationRepo->insertDebFromDbiToProd($integrationId,$importByType->getId(),$a->getId());

                $importByType = $integrationRepo->getOneImportType($integrationId , "dossier");
                $a=new ActionsImport();
                $a->setEtat(0);
                $a->setCodeAction("Ajo");
                $a->setDateDebut(new \DateTime());
                $a->setIdImport($importByType);
                $a->setTitre("Ajout");
                $this->em->persist($a);
                $this->em->flush();

                $integrationRepo->insertDossierFromDbiToProd($integrationId ,$importByType->getId() , $a->getId());

                $importByType = $integrationRepo->getOneImportType($integrationId , "creance");
                $a=new ActionsImport();
                $a->setEtat(0);
                $a->setCodeAction("Ajo");
                $a->setDateDebut(new \DateTime());
                $a->setIdImport($importByType);
                $a->setTitre("Ajout");
                $this->em->persist($a);
                $this->em->flush();

                $integrationRepo->insertCreanceFromDbiToProd($integrationId ,$importByType->getId() , $a->getId());
                
                $importByType = $integrationRepo->getOneImportType($integrationId , "emploi");
                if($importByType){
                    $a=new ActionsImport();
                    $a->setEtat(0);
                    $a->setCodeAction("Ajo");
                    $a->setDateDebut(new \DateTime());
                    $a->setIdImport($importByType);
                    $a->setTitre("Ajout");
                    $this->em->persist($a);
                    $this->em->flush();
    
                    $integrationRepo->insertEmploiFromDbiToProd($integrationId ,$importByType->getId() , $a->getId());
                }

                $importByType = $integrationRepo->getOneImportType($integrationId , "employeur");
                if($importByType){
                    $a=new ActionsImport();
                    $a->setEtat(0);
                    $a->setCodeAction("Ajo");
                    $a->setDateDebut(new \DateTime());
                    $a->setIdImport($importByType);
                    $a->setTitre("Ajout");
                    $this->em->persist($a);
                    $this->em->flush();
                    
                    $integrationRepo->insertEmployeurFromDbiToProd($integrationId ,$importByType->getId() , $a->getId());
                }

                $importByType = $integrationRepo->getOneImportType($integrationId , "telephone");
                if($importByType){
                    $a=new ActionsImport();
                    $a->setEtat(0);
                    $a->setCodeAction("Ajo");
                    $a->setDateDebut(new \DateTime());
                    $a->setIdImport($importByType);
                    $a->setTitre("Ajout");
                    $this->em->persist($a);
                    $this->em->flush();
                    
                    $integrationRepo->insertTelephoneFromDbiToProd($integrationId ,$importByType->getId() , $a->getId());
                }
                $importByType = $integrationRepo->getOneImportType($integrationId , "adresse");
                if($importByType){
                    $a=new ActionsImport();
                    $a->setEtat(0);
                    $a->setCodeAction("Ajo");
                    $a->setDateDebut(new \DateTime());
                    $a->setIdImport($importByType);
                    $a->setTitre("Ajout");
                    $this->em->persist($a);
                    $this->em->flush();

                    $integrationRepo->insertAdresseFromDbiToProd($integrationId ,$importByType->getId() , $a->getId());
                }

                /*$importByType = $integrationRepo->getOneImportType($integrationId , "emploi");
                $integrationRepo->insertEmploiFromDbiToProd($importByType->getId());

                $importByType = $integrationRepo->getOneImportType($integrationId , "dossier");
                $integrationRepo->insertDossierFromDbiToProd($importByType->getId());

                $importByType = $integrationRepo->getOneImportType($integrationId , "creance");
                $integrationRepo->insertCreanceFromDbiToProd($importByType->getId());

                $importByType = $integrationRepo->getOneImportType($integrationId , "garantie");
                $integrationRepo->insertGarantieDebiteurFromDbiToProd($importByType->getId());
                $integrationRepo->insertGarantieFromDbiToProd($importByType->getId());

                $integrationRepo->insertGarantieCreanceFromDbiToProd($importByType->getId());
                $importByType = $integrationRepo->getOneImportType($integrationId , "proc");
                $integrationRepo->insertProcFromDbiToProd($importByType->getId());
                $integrationRepo->insertProcDebiteurFromDbiToProd($importByType->getId());
                $integrationRepo->insertProcCreanceFromDbiToProd($importByType->getId());*/
                //todo
                $sql="update integration set status_id = 8 , `date_fin_execution_2`=now() where id = ".$IntegrationNonCommencer[$t]->getId()."";
                $stmt = $this->conn->prepare($sql)->executeQuery();

                $sql="INSERT INTO `logs_actions_integration`( `id_integration`, `logs`, `date_creation`,`etat`) VALUES (".$integrationId.",'Intégration production terminée',now(),1)";
                $stmt = $integrationRepo->executeSQL($sql);

                $codeStatut='OK';
            }
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/checkIfTitreExist')]
    public function checkIfTitreExist(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";

        try{
            $this->AuthService->checkAuth(0,$request);
            $titre = $request->get("titre");
            $findIntegrationByTitre = $this->integrationRepo->findIntegrationByTitre($titre);
            if($findIntegrationByTitre)
            {
                $codeStatut = "ELEMENT_DEJE_EXIST";
            }else{
                $codeStatut="OK";
            }
        }catch (\Exception $e) {
            $codeStatut = "ERROR";
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/findIntegrationValide')]
    public function findIntegrationValide(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";

        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $findIntegration = $this->integrationRepo->findIntegrationValide($id);
            
            if($findIntegration){
                $respObjects["data"] = $findIntegration;
                $respObjects["import"] = $this->integrationRepo->getImport($id);;
                $codeStatut = "OK";
            }else{
                $codeStatut = "NOT_EXIST";
            }
        }catch (\Exception $e) {
            $codeStatut = "ERROR";
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    #[Route('/getColumnCreance')]
    public function getColumnCreance(integrationRepo $integrationRepo , Request $request , SerializerInterface $serializer): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);
        $respObjects = array();
        try{
            $dataList = $integrationRepo->getColumnCreance();
            $respObjects["message"] = "Opération effectué avec success";
            $respObjects["codeStatut"] = "OK";
            $respObjects["data"] = $dataList;
        }catch(\Exception $e){
            $result = "Une erreur s'est produite ".$e->getMessage();
            $respObjects["message"] = $result;
        }
        return $this->json($respObjects );
    }

    #[Route('/getColumnByTable')]
    public function getColumnByTable(integrationRepo $integrationRepo , Request $request,SerializerInterface $serializer): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);
        $codeStatut = "ERROR";
        $respObjects =array(); 
        try{
            $table = $request->get("table");
            $dataList = $integrationRepo->getColumnByTable($table);
            $codeStatut="OK";
            $respObjects["data"] = $dataList;
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        $respObjects["ee"]=$e->getMessage();
        }
        $respObjects["codeStatut"]=$codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getAllColumnsParams')]
    public function getAllColumnsParams(integrationRepo $integrationRepo ,affichageRepo $affichageRepo, Request $request): JsonResponse
    {
        $codeStatut = "ERROR";
        $respObjects =array(); 
        try{
            $this->AuthService->checkAuth(0,$request);
            $table = $request->get("table");
            $id = $request->get("id");
            $dataList = $integrationRepo->getAllColumnsParams();
            $query = $this->em->createQuery('SELECT t.table_bdd  from App\Entity\ColumnsParams t group by t.table_bdd');
            $tables = $query->getResult();
            $respObjects["groupe_tables"] = $tables;
            $codeStatut="OK";
            $respObjects["data"] = $dataList;

            if( $id != "undefined"){
                $dataModel = $affichageRepo->listDetailsModels($id);
                $respObjects["detail_model"] = $dataModel;
                $dataModel = $affichageRepo->groupingListDetailsModels($id);
                $respObjects["groupe_table_detail"] = $dataModel;
            }
            $table=array();
            $tableObj=array();
            $j=0;

            $typeAdresse = $integrationRepo->getTypeAdresse();
            $typeTel = $integrationRepo->getTypeTel();

            for ($a = 0 ; $a < count($typeAdresse) ; $a++)
            {
                $tableObj["table"][$j]="Adresse";
                $tableObj["obj"][$j]=$typeAdresse[$a]->getType();
                $j++;
            }

            for ($s = 0 ; $s < count($typeTel) ; $s++)
            {
                $tableObj["table"][$j]="Tel";
                $tableObj["obj"][$j]=$typeTel[$s]->getType();
                $j++;
            }

            $tableObj["table"][$j]="Banque";
            $tableObj["obj"][$j]="Banque";$j++;
            $tableObj["table"][$j]="Titre foncier";
            $tableObj["obj"][$j]="Titre foncier";$j++;
            $tableObj["table"][$j]="Cnss";
            $tableObj["obj"][$j]="Cnss";

            $table[0]="Adresse";
            $table[1]="Tel";

            $respObjects["columnsCadrage"] = $table;
            $respObjects["col2"]= $tableObj;

        }catch(\Exception $e){
            $codeStatut = "ERROR";
            $respObjects["ee"]=$e->getMessage();
        }
        $respObjects["codeStatut"]=$codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getAllColumnsParams2')]
    public function getAllColumnsParamsCreance(integrationRepo $integrationRepo ,affichageRepo $affichageRepo, Request $request): JsonResponse
    {
        $codeStatut = "ERROR";
        $respObjects =array(); 
        try{
            $this->AuthService->checkAuth(0,$request);
            $table = $request->get("table");
            $type = $request->get("type");
            $id = $request->get("id");
            if($type == "creance"){
                $dataList = $integrationRepo->getAllColumnsParams($type);
                $query = $this->em->createQuery('SELECT t.table_bdd  from App\Entity\ColumnsParams t   where (t.table_bdd = :tb1 or t.table_bdd = :tb2 or t.table_bdd = :tb3 or t.table_bdd = :tb4)   group by t.table_bdd ')
                ->setParameters([
                    'tb1' => 'creance',
                    'tb2' => 'detail_creance',
                    'tb3' => 'debiteur',
                    'tb4' => 'dossier',
                ]);
                $tables = $query->getResult();
                $respObjects["groupe_tables"] = $tables;
                $dataModel = $affichageRepo->listDetailsModels($id,$type);
                $respObjects["detail_model"] = $dataModel;
                $dataModel = $affichageRepo->groupingListDetailsModels($id,$type);
                $respObjects["groupe_table_detail"] = $dataModel;
                $codeStatut="OK";
                $respObjects["data"] = $dataList;
            }else if($type == "debiteur"){
                $dataList = $integrationRepo->getAllColumnsParams($type);
                $query = $this->em->createQuery('SELECT t.table_bdd  from App\Entity\ColumnsParams t   where (t.table_bdd = :tb1 OR t.table_bdd = :tb2)   group by t.table_bdd ')
                ->setParameters([
                    'tb1' => 'debiteur',
                    'tb2' => 'dossier',
                    // 'tb2' => 'emploi',   
                    // 'tb3' => 'debiteur'
                ]);
                $tables = $query->getResult();
                $respObjects["groupe_tables"] = $tables;
                $dataModel = $affichageRepo->listDetailsModels($id,$type);
                $respObjects["detail_model"] = $dataModel;
                $dataModel = $affichageRepo->groupingListDetailsModels($id,$type);
                $respObjects["groupe_table_detail"] = $dataModel;
                $codeStatut="OK";
                $respObjects["data"] = $dataList;
            }
            else if($type == "dossier"){
                $dataList = $integrationRepo->getAllColumnsParams($type);
                $query = $this->em->createQuery('SELECT t.table_bdd  from App\Entity\ColumnsParams t where t.table_bdd = :tb1 or  t.table_bdd = :tb2 group by t.table_bdd ')
                ->setParameters([
                    'tb1' => 'dossier',
                    'tb2' => 'debiteur'
                ]);
                $tables = $query->getResult();
                $respObjects["groupe_tables"] = $tables;
                $dataModel = $affichageRepo->listDetailsModels($id,$type);
                $respObjects["detail_model"] = $dataModel;
                $dataModel = $affichageRepo->groupingListDetailsModels($id,$type);
                $respObjects["groupe_table_detail"] = $dataModel;
                $codeStatut="OK";
                $respObjects["data"] = $dataList;
            }
            else if($type == "garantie"){
                $dataList = $integrationRepo->getAllColumnsParams($type);
                $query = $this->em->createQuery('SELECT t.table_bdd  from App\Entity\ColumnsParams t where t.table_bdd = :tb1 or t.table_bdd = :tb2 or t.table_bdd = :tb3 group by t.table_bdd ')
                ->setParameters([
                    'tb1' => 'garantie',
                    'tb2' => 'creance',
                    'tb3' => 'debiteur'
                ]);
                $tables = $query->getResult();
                $respObjects["groupe_tables"] = $tables;

                $dataModel = $affichageRepo->listDetailsModels($id,$type);
                $respObjects["detail_model"] = $dataModel;
                $dataModel = $affichageRepo->groupingListDetailsModels($id,$type);
                $respObjects["groupe_table_detail"] = $dataModel;
                $codeStatut="OK";
                $respObjects["data"] = $dataList;
            }
            else if($type == "procedure_judicaire"){
                $dataList = $integrationRepo->getAllColumnsParams($type);
                $query = $this->em->createQuery('SELECT t.table_bdd  from App\Entity\ColumnsParams t where t.table_bdd = :tb1 or t.table_bdd = :tb2 or t.table_bdd = :tb3 group by t.table_bdd ')
                ->setParameters([
                    'tb1' => 'proc_judicaire',
                    'tb2' => 'creance',
                    'tb3' => 'debiteur'
                ]);
                $tables = $query->getResult();
                $respObjects["groupe_tables"] = $tables;

                $dataModel = $affichageRepo->listDetailsModels($id,$type);
                $respObjects["detail_model"] = $dataModel;
                $dataModel = $affichageRepo->groupingListDetailsModels($id,$type);
                $respObjects["groupe_table_detail"] = $dataModel;
                $codeStatut="OK";
                $respObjects["data"] = $dataList;
            }
            else if($type == "telephone"){
                $dataList = $integrationRepo->getAllColumnsParams($type);
                $query = $this->em->createQuery('SELECT t.table_bdd  from App\Entity\ColumnsParams t where t.table_bdd = :tb1 or t.table_bdd = :tb2 group by t.table_bdd ')
                ->setParameters([
                    'tb1' => 'telephone',
                    'tb2' => 'debiteur'
                ]);
                $tables = $query->getResult();
                $respObjects["groupe_tables"] = $tables;

                $dataModel = $affichageRepo->listDetailsModels($id,$type);
                $respObjects["detail_model"] = $dataModel;
                $dataModel = $affichageRepo->groupingListDetailsModels($id,$type);
                $respObjects["groupe_table_detail"] = $dataModel;
                $codeStatut="OK";
                $respObjects["data"] = $dataList;
            }
            else if($type == "adresse"){
                $dataList = $integrationRepo->getAllColumnsParams($type);
                $query = $this->em->createQuery('SELECT t.table_bdd  from App\Entity\ColumnsParams t where t.table_bdd = :tb1 or t.table_bdd = :tb2 group by t.table_bdd ')
                ->setParameters([
                    'tb1' => 'adresse',
                    'tb2' => 'debiteur'
                ]);
                $tables = $query->getResult();
                $respObjects["groupe_tables"] = $tables;
                $dataModel = $affichageRepo->listDetailsModels($id,$type);
                $respObjects["detail_model"] = $dataModel;
                $dataModel = $affichageRepo->groupingListDetailsModels($id,$type);
                $respObjects["groupe_table_detail"] = $dataModel;
                $codeStatut="OK";
                $respObjects["data"] = $dataList;
            }
            else if($type == "email"){
                $dataList = $integrationRepo->getAllColumnsParams($type);
                $query = $this->em->createQuery('SELECT t.table_bdd  from App\Entity\ColumnsParams t where t.table_bdd = :tb1 or t.table_bdd = :tb2 group by t.table_bdd ')
                ->setParameters([
                    'tb1' => 'email',
                    'tb2' => 'debiteur'
                ]);
                $tables = $query->getResult();
                $respObjects["groupe_tables"] = $tables;
                $dataModel = $affichageRepo->listDetailsModels($id,$type);
                $respObjects["detail_model"] = $dataModel;
                $dataModel = $affichageRepo->groupingListDetailsModels($id,$type);
                $respObjects["groupe_table_detail"] = $dataModel;
                $codeStatut="OK";
                $respObjects["data"] = $dataList;
            }
            else if($type == "emploi"){
                $dataList = $integrationRepo->getAllColumnsParams($type);
                $query = $this->em->createQuery('SELECT t.table_bdd  from App\Entity\ColumnsParams t where t.table_bdd = :tb1 or t.table_bdd = :tb2 group by t.table_bdd ')
                ->setParameters([
                    'tb1' => 'emploi',
                    'tb2' => 'debiteur'
                ]);
                $tables = $query->getResult();
                $respObjects["groupe_tables"] = $tables;
                $dataModel = $affichageRepo->listDetailsModels($id,$type);
                $respObjects["detail_model"] = $dataModel;
                $dataModel = $affichageRepo->groupingListDetailsModels($id,$type);
                $respObjects["groupe_table_detail"] = $dataModel;
                $codeStatut="OK";
                $respObjects["data"] = $dataList;
            }else if($type == "employeur"){
                $dataList = $integrationRepo->getAllColumnsParams($type);
                $query = $this->em->createQuery('SELECT t.table_bdd  from App\Entity\ColumnsParams t where t.table_bdd = :tb1 or t.table_bdd = :tb2 group by t.table_bdd ')
                ->setParameters([
                    'tb1' => 'employeur',
                    'tb2' => 'debiteur'
                ]);
                $tables = $query->getResult();
                $respObjects["groupe_tables"] = $tables;
                $dataModel = $affichageRepo->listDetailsModels($id,$type);
                $respObjects["detail_model"] = $dataModel;
                $dataModel = $affichageRepo->groupingListDetailsModels($id,$type);
                $respObjects["groupe_table_detail"] = $dataModel;
                $codeStatut="OK";
                $respObjects["data"] = $dataList;
            }
            else{
                $codeStatut="ERROR";
            }
            
            // $table=array();
            // $tableObj=array();
            // $j=0;

            // $typeAdresse = $integrationRepo->getTypeAdresse();
            // $typeTel = $integrationRepo->getTypeTel();

            // for ($a = 0 ; $a < count($typeAdresse) ; $a++)
            // {
            //     $tableObj["table"][$j]="Adresse";
            //     $tableObj["obj"][$j]=$typeAdresse[$a]->getType();
            //     $j++;
            // }

            // for ($s = 0 ; $s < count($typeTel) ; $s++)
            // {
            //     $tableObj["table"][$j]="Tel";
            //     $tableObj["obj"][$j]=$typeTel[$s]->getType();
            //     $j++;
            // }

            // $tableObj["table"][$j]="Banque";
            // $tableObj["obj"][$j]="Banque";$j++;
            // $tableObj["table"][$j]="Titre foncier";
            // $tableObj["obj"][$j]="Titre foncier";$j++;
            // $tableObj["table"][$j]="Cnss";
            // $tableObj["obj"][$j]="Cnss";

            // $table[0]="Adresse";
            // $table[1]="Tel";

            // $respObjects["columnsCadrage"] = $table;
            // $respObjects["col2"]= $tableObj;

            // $query = $this->em->createQuery('SELECT t  from App\Entity\DetailModelAffichage where t.id_model_affichage = '.$id.'');
            // $liste_detail = $query->getResult();
            // dump($liste_detail);

        }catch(\Exception $e){
            $codeStatut = "ERROR";
            $respObjects["ee"]=$e->getMessage();
        }
        $respObjects["codeStatut"]=$codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getColumn', methods: ['POST'])]
    public function getColumn(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            //code...
            if(!empty($_FILES['fichier']['name']))
            {
                $fileSize=$_FILES['fichier']['size'];
                $extensions_valides = array('csv');
                $fileName = $_FILES['fichier']['name'];
                $extension_upload = strtolower(substr(strrchr($fileName, '.'), 1));
                $fileError = $_FILES['fichier']['error'];
                $fileTmpLoc = $_FILES['fichier']['tmp_name'];

                if ($fileError > 0)
                {
                    $codeStatut="ERROR-FILE";
                }else{
                    if (in_array($extension_upload, $extensions_valides))
                    {
                        if (($handle = fopen($fileTmpLoc, "r")) !== FALSE) {
                            while (($data = fgetcsv($handle, 1000000, ";")) !== FALSE)
                            {
                                break;
                            }
                            $data[0] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $data[0]);
                            for($i=0; $i < count($data); $i++) { 
                                $data[$i] = mb_convert_encoding($data[$i], 'UTF-8','Windows-1252');
                            }
                            fclose($handle);
                            $respObjects["data"] = $data;
                            $codeStatut = "OK";
                        }
                    }
                    else
                    {
                        $codeStatut="ERROR_FILE_EXTENSION";
                    }
                }
    
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        } catch (\Exception $e) {
            //throw $th;
            $respObjects["dd"] = $e->getMessage();
            $codeStatut="ERROR";
        }
      
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }
    #[Route('/changeStatus', methods: ['POST'])]
    public function changeStatus(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            $status = $request->get("status");
            $id = $request->get("id");
            if($status == "14"){
                $codeStatut = $this->integrationRepo->updateStatus($status, $id,[1,3], $integrationRepo);
            }
            if($status == "5"){
                $codeStatut = $this->integrationRepo->updateStatus($status, $id,[4], $integrationRepo);
            }
            if($status == "9"){
                $codeStatut = $this->integrationRepo->updateStatus($status, $id,[8], $integrationRepo);
            }if($status == "10"){
                $codeStatut = $this->integrationRepo->updateStatus($status, $id,[9 , 7], $integrationRepo);
            }if($status == "12"){
                $codeStatut = $this->integrationRepo->updateStatus($status, $id,[4,5], $integrationRepo);
            }
        }catch (\Exception $e) {
            $respObjects["msg"] = $e->getMessage();
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }
    #[Route('/getOneModel')]
    public function getOneModel(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $id = $request->get("id");
            $model = $integrationRepo->findModel($id);
            if($model){
                $codeStatut = "OK";
                $corres = $integrationRepo->getDataCorres($id);
                $respObjects["data"]=$model;
                $respObjects["data_model"]=$corres;
            }else{
                $codeStatut="NOT_EXIST_M";
            }
        }catch(\Exception $e){
           $codeStatut="ERROR";
           $respObjects["mssgError"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/createModel', methods: ['POST'])]
    public function createModel(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);
        $respObjects =array();
        $codeStatut = "ERROR";
        // try{
            $data_list = json_decode($request->getContent(), true);
            $titre = $request->get("titre");
            $table = $request->get("table");
            if($data_list){
                $is_exist = false;
                for ($j=0; $j < count($data_list); $j++) { 
                    if(isset($data_list[$j]["col_param"])){

                        if("23" == $data_list[$j]["col_param"]){
                            $is_exist = true;
                        }
                    }
                }

                if($is_exist){
                    if(empty($titre) or !count($data_list)>0 )
                    {
                        $codeStatut="EMPTY-DATA";
                    }
                    else
                    {
                        $findModel = $integrationRepo->findModelByTitle($titre);
                        if($findModel){
                            $codeStatut="TITRE_DEJE_EXIST";
                        }else{
                            $m = new ModelImport();
                            $m->setTitre($titre);                  
                            $m->setDateCreation(new \DateTime()); 
                            $m->setType("");      
                            $this->em->persist($m);
                            if($m){
                                for($i=0 ;$i < count($data_list);$i++){
                                    if($data_list[$i]["required"])
                                    {
                                        $check=1;
                                    }
                                    else
                                    {
                                        $check=0;
                                    }

                                    $colTbale = "";
                                    if(isset($data_list[$i]["column_db"])){
                                        $colTbale = $data_list[$i]["column_db"];
                                    }

                                    $colParam = null;
                                    if(isset($data_list[$i]["col_param"])){
                                        $colParam = $this->em->getRepository(ColumnsParams::class)->findOneBy(["id"=>$data_list[$i]["col_param"]]); 
                                        $colonne = new CorresColu();
                                        $colonne->setIdModelImport($m);
                                        $colonne->setColumnName(trim($data_list[$i]["column_file"]));
                                        $colonne->setCode("code");
                                        $colonne->setTableName($data_list[$i]["table_name"]);
                                        $colonne->setColumnTable($colTbale);
                                        $colonne->setRequired($check);
                                        $colonne->setIdColParams($colParam);
                                        $this->em->persist($colonne);
                                    }
                                    else
                                    {
                                        $typesAdresses=$this->em->getRepository(TypeAdresse::class)->findAll();
                                        for ($a = 0 ; $a < count($typesAdresses) ; $a++)
                                        {
                                            if($data_list[$i]["column_db_name"]==$typesAdresses[$a]->getType()."--Adresse" or $data_list[$i]["column_db_name"]==$typesAdresses[$a]->getType()."--Adresse--status"
                                                or $data_list[$i]["column_db_name"]==$typesAdresses[$a]->getType()."--Adresse--ville" or $data_list[$i]["column_db_name"]==$typesAdresses[$a]->getType()."--Adresse--pays"
                                                or $data_list[$i]["column_db_name"]==$typesAdresses[$a]->getType()."--Adresse--region" or $data_list[$i]["column_db_name"]==$typesAdresses[$a]->getType()."--Adresse--cp"
                                                or $data_list[$i]["column_db_name"]==$typesAdresses[$a]->getType()."--Adresse--code_postal" or $data_list[$i]["column_db_name"]==$typesAdresses[$a]->getType()."--Adresse--volet2"
                                                or $data_list[$i]["column_db_name"]==$typesAdresses[$a]->getType()."--Adresse--volet3" or $data_list[$i]["column_db_name"]==$typesAdresses[$a]->getType()."--Adresse--volet4"
                                                or $data_list[$i]["column_db_name"]==$typesAdresses[$a]->getType()."--Adresse--province" or $data_list[$i]["column_db_name"]==$typesAdresses[$a]->getType()."--Adresse--source")
                                            {
                                                $t=new ImportType();
                                                $t->setTableBdd("type_adresse");
                                                $t->setChamps($data_list[$i]["column_db_name"]);
                                                $t->setIdModel($m);
                                                $t->setNomCol($data_list[$i]["column_file"]);
                                                $this->em->persist($t);
                                            }
                                        }
                                        $typesTel=$this->em->getRepository(TypeTel::class)->findAll();
                                        for ($a = 0 ; $a < count($typesTel) ; $a++)
                                        {
                                            if($data_list[$i]["column_db_name"]==$typesTel[$a]->getType()."--Tel--status" or $data_list[$i]["column_db_name"]==$typesTel[$a]->getType()."--Tel"
                                                or $data_list[$i]["column_db_name"]==$typesTel[$a]->getType()."--Tel--status2" or $data_list[$i]["column_db_name"]==$typesTel[$a]->getType()."--Tel--status3"
                                                or $data_list[$i]["column_db_name"]==$typesTel[$a]->getType()."--Tel--note1" or $data_list[$i]["column_db_name"]==$typesTel[$a]->getType()."--Tel--note2"
                                                or $data_list[$i]["column_db_name"]==$typesTel[$a]->getType()."--Tel--note3" or $data_list[$i]["column_db_name"]==$typesTel[$a]->getType()."--Tel--numero2"
                                                or $data_list[$i]["column_db_name"]==$typesTel[$a]->getType()."--Tel--numero3")
                                            {
                                                $t=new ImportType();
                                                $t->setTableBdd("type_telephone");
                                                $t->setChamps($data_list[$i]["column_db_name"]);
                                                $t->setIdModel($m);
                                                $t->setNomCol($data_list[$i]["column_file"]);
                                                $this->em->persist($t);
                                            }
                                        }
                                    }
                                    
                                    $this->em->flush();
                                    $codeStatut="OK";
                                }
                            }
                            $codeStatut="OK";  
                        }
                    }
                }else{
                    $codeStatut="CIN_OUBLIGATOIRE";
                }
            }else{
                $codeStatut="EMPTY-DATA";
            }
        // }catch(\Exception $e){
        //    $codeStatut="ERROR";
        //    $respObjects["mssgError"] = $e->getMessage();
        // }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
   
    #[Route('/updateModel', methods: ['POST'])]
    public function updateModel(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);

        $codeStatut="ERROR";
        $respObjects =array();
        try{
            $id = $request->get("id");
            $data_list = json_decode($request->getContent(), true);
            $titre = $request->get("titre");
            
                if($data_list){
                    // if(empty($titre) or !count($data_list)>0 or empty($table))
                    if(empty($titre) or !count($data_list)>0 )
                    {
                        $codeStatut="EMPTY-DATA";
                    }else{
                        
                            $m = $integrationRepo->findModel($id);
                            $m->setTitre($titre);                  
                            $m->setDateCreation(new \DateTime()); 
                            $this->em->persist($m);
                            $this->em->flush();
    
                            $corres=$this->em->getRepository(CorresColu::class)->findBy(array("id_model_import"=>$id));
                            foreach ($corres as $c)
                            {
                                $this->em->remove($c);
                            }
    
                            if($m){
                                for($i=0 ;$i < count($data_list);$i++){
                                    if($data_list[$i]["required"])
                                    {
                                        $check=1;
                                    }
                                    else
                                    {
                                        $check=0;
                                    }
                                    $colParam = $this->em->getRepository(ColumnsParams::class)->findOneBy(["id"=>$data_list[$i]["col_param"]]); 
                                    $colonne = new CorresColu();
                                    $colonne->setIdModelImport($m);
                                    $colonne->setColumnName($data_list[$i]["column_file"]);
                                    $colonne->setCode("code");
                                    $colonne->setTableName($data_list[$i]["table_name"]);
                                    $colonne->setColumnTable($data_list[$i]["column_db"]);
                                    $colonne->setRequired($check);
                                    $colonne->setIdColParams($colParam);
                                    $this->em->persist($colonne);
                                    $this->em->flush();
                                    $codeStatut="OK";
                                }
                            }
                            $codeStatut="OK";  
                        }
                }else{
                    $codeStatut="EMPTY-DATA";
                }
            
        }catch(\Exception $e){
            $result = "Une erreur s'est produite ".$e->getMessage();
            $respObjects["err"] = $result;
            $codeStatut= "ERREUR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getAllModel')]
    public function getAllModel(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);

        $respObjects =array();
        try{
            $models = $integrationRepo->getAllModels();
            $respObjects["message"] = "Opération effectue avec succes";
            $respObjects["codeStatut"] = "OK";
            $respObjects["data"] = $models;
        }catch(\Exception $e){
            $result = "Une erreur s'est produite ".$e->getMessage();
            $respObjects["message"] = $result;
            $respObjects["codeStatut"] = "ERREUR";
        }
        return $this->json($respObjects );
    }

    #[Route('/cancelIntegration')]
    public function cancelIntegration(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);

        $respObjects =array();
        try{
            $id=$request->get("id");
            $integration = $integrationRepo->findIntegration($id);
            if($integration){
                $imports = $this->em->getRepository(Import::class)->findBy(["id_integration"=>$id]);
                foreach ($imports as $m) {
                    $table_inetgration = $this->em->getRepository(ImportDonneurOrdreBack::class)->findBy(["id_import"=>$m->getId()]);
                    foreach ($table_inetgration as $table) {
                        if($table->getEtatExist() == 0){
                            //Update etat d'affichage 
                            // $sql = "update creance set etat_affichge =  :etat where id = :id";
                            // $stmt = $this->conn->prepare($sql);
                            // $stmt->bindValue('etat', -1);
                            // $stmt->bindValue('id',$table->getIdColumn() );
                            // $stmt->execute();
                        }else{
                            //Il reste detectee la lign : => il faut régler 
                            // $entite = json_decode($table->getEntete(), true);
                            // $sql = "update creance set montant = montant - :monatntAjouter  where id = :id;";
                            // $stmt = $this->conn->prepare($sql);
                            // $stmt->bindValue('monatntAjouter',$entite[4] );
                            // $stmt->bindValue('id',$table->getIdColumn() );
                            // $stmt->execute();
                        }
                    }
                }
                $integration->setEtat(4);
                $this->em->flush();
                $respObjects["codeStatut"] = "OK";
                $respObjects["message"] = "Opération effectue avec success";
            }else{
                $respObjects["message"] = "L'integration n'existe pas !";
                $respObjects["codeStatut"] = "ERREUR";
            }
        }catch(\Exception $e){
            $result = "Une erreur s'est produite ".$e->getMessage();
            $respObjects["message"] = $result;
            $respObjects["codeStatut"] = "ERREUR";
        }
        return $this->json($respObjects );
    }
    #[Route('/deleteIntegration')]
    public function deleteIntegration(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);

        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $id=$request->get("id");
            $integration = $integrationRepo->findIntegration($id);
            if($integration){
                $integration =  $this->em->getRepository(Integration::class)->findOneBy(["id"=>$id]);
                $models = $this->em->getRepository(Import::class)->findBy(["id_integration"=>$id]);
                foreach ($models as $m) {
                    $this->em->remove($m );
                }
                $this->em->remove($integration);
                $this->em->flush();
                $codeStatut="OK";
            }else{
                $codeStatut="NOT_EXIST_ELEMENT";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/deleteImport')]
    public function deleteImport(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);

        $respObjects =array();
        try{
            $id=$request->get("id");
            $model = $this->em->getRepository(Import::class)->findOneBy(["id"=>$id]);
            if($model){
                $this->em->remove($model);
                $this->em->flush();
                $respObjects["codeStatut"] = "OK";
                $respObjects["message"] = "Opération effectue avec success";
            }else{
                $respObjects["message"] = "L'import n'existe pas !";
                $respObjects["codeStatut"] = "ERREUR";
            }
        }catch(\Exception $e){
            $result = "Une erreur s'est produite ".$e->getMessage();
            $respObjects["message"] = $result;
            $respObjects["codeStatut"] = "ERREUR";
        }
        return $this->json($respObjects );
    }

    #[Route('/getModelByType/')]
    public function get_shema_by_type(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);

        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $codeStatut = "ERROR";
            $type = $request->get("type");
            if(!empty($type)){
                $data = $integrationRepo->getModelsByType($type);
                if($data){
                    $respObjects["data"] = $data;
                    $codeStatut="OK";
                }
            }else{
                $codeStatut = "EMPTY-DATA";
            }
        }
        catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    

    #[Route('/detailsIntegration')]
    public function detailsIntegration(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        $id = $request->get("id");
        try {
            $integration = $integrationRepo->getOneIntegration($id);
            if(!$integration){
                $codeStatut="INTEGRATION_NOT_EXIST";
            }else{
                //code...
                $sql="SELECT t.* from actions_import t where t.id_import_id in (SELECT i.id FROM import i where i.id_integration_id = :id);";
                $param=(array("id"=>$id  ));
                $actions = $this->conn->fetchAssociative($sql , $param);
    
                $sql="SELECT l.* from logs_actions l where l.etat=1 and l.id_action_id in(select a.id from actions_import a where a.id_import_id in (select i.id FROM import i where i.id_integration_id  = :id ))";
                $param=(array("id"=>$id  ));
                $success = $this->conn->fetchAllAssociative($sql , $param);
    
                $sql="SELECT l.* from logs_actions l where l.etat=2 and l.id_action_id in(select a.id from actions_import a where a.id_import_id in (select i.id FROM import i where i.id_integration_id  = :id ))";
                $param=(array("id"=>$id  ));
                $warning = $this->conn->fetchAllAssociative($sql , $param);
                
                $sql="SELECT l.* from logs_actions l where l.etat=0 and l.id_action_id in(select a.id from actions_import a where a.id_import_id in (select i.id FROM import i where i.id_integration_id  = :id ))";
                $param=(array("id"=>$id  ));
                $error = $this->conn->fetchAllAssociative($sql , $param);
                
                $sql="SELECT distinct l.rapport as rapport, count(l.rapport) as nombrefrom from logs_actions l where l.etat=1 and l.id_action_id in(select a.id from actions_import a where a.id_import_id in (select i.id FROM import i where i.id_integration_id  = :id )) group by l.rapport";
                $param=(array("id"=>$id  ));
                $rapport = $this->conn->fetchAllAssociative($sql , $param);
                
                $respObjects["data"]["actions"]=$actions;
                $respObjects["data"]["suceess"]=$success;
                $respObjects["data"]["warning"]=$warning;
                $respObjects["data"]["error"]=$error;
                $respObjects["data"]["rapport"]=$rapport;
                $respObjects["data"]["integration"]=$integration;
                $codeStatut="OK";
            }

        } catch (\Exception $e) {
            $codeStatut="ERROR";
            $respObjects["err"]=$e->getMessage();
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/detailsIntegration2')]
    public function detailsIntegration2(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        $id = $request->get("id");
        try {
            $integration = $integrationRepo->getOneIntegration($id);
            if(!$integration){
                $codeStatut="INTEGRATION_NOT_EXIST";
            }else{
                //code...
                $sql="SELECT t.* from debt_force_integration.actions_import_dbi t where t.id_import in (SELECT i.id FROM import i where i.id_integration_id = :id);";
                $param=(array("id"=>$id  ));
                $actions = $this->conn->fetchAssociative($sql , $param);
                
                $sql="SELECT l.* from debt_force_integration.logs_actions_dbi l where l.etat=1 and l.id_action_id in(select a.id from debt_force_integration.actions_import_dbi a where a.id_import in (select i.id FROM import i where i.id_integration_id  = :id ))";
                $param=(array("id"=>$id  ));
                $success = $this->conn->fetchAllAssociative($sql , $param);
    
                $sql="SELECT l.* from debt_force_integration.logs_actions_dbi l where l.etat=2 and l.id_action_id in(select a.id from debt_force_integration.actions_import_dbi a where a.id_import in (select i.id FROM import i where i.id_integration_id  = :id ))";
                $param=(array("id"=>$id  ));
                $warning = $this->conn->fetchAllAssociative($sql , $param);
                
                $sql="SELECT l.* from debt_force_integration.logs_actions_dbi l where l.etat=0 and l.id_action_id in(select a.id from actions_import a where a.id_import_id in (select i.id FROM import i where i.id_integration_id  = :id ))";
                $param=(array("id"=>$id  ));
                $error = $this->conn->fetchAllAssociative($sql , $param);
                
                $sql="SELECT distinct l.rapport as rapport, count(l.rapport) as nombrefrom from logs_actions l where l.etat=1 and l.id_action_id in(select a.id from actions_import a where a.id_import_id in (select i.id FROM import i where i.id_integration_id  = :id )) group by l.rapport";
                $param=(array("id"=>$id  ));
                $rapport = $this->conn->fetchAllAssociative($sql , $param);

                $sql="SELECT * from import  where id_integration_id = :id";
                $param=(array("id"=>$id  ));
                $details_import = $this->conn->fetchAllAssociative($sql , $param);

                $array_details = array();

                for ($i=0; $i < count($details_import); $i++) { 
                    $array_details[$i]["import"] = $details_import[$i];
                    $sql="SELECT * FROM debt_force_integration.`actions_import_dbi` WHERE  id_import = :id and date_debut = (SELECT MIN(date_debut) FROM debt_force_integration.`actions_import_dbi` WHERE id_import = :id);";
                    $param=(array("id"=>$details_import[$i]["id"]));
                    $actions = $this->conn->fetchAssociative($sql , $param);
                    $array_details[$i]["actions"] = $actions;

                    $sql="SELECT l.* from debt_force_integration.logs_actions_dbi l where l.etat=1 and l.id_action_id in(select a.id from debt_force_integration.actions_import_dbi a where a.id_import in (select i.id FROM import i where i.id  = :id ))";
                    $param=(array("id"=>$details_import[$i]["id"]  ));
                    $success = $this->conn->fetchAllAssociative($sql , $param);

                    $sql="SELECT l.* from debt_force_integration.logs_actions_dbi l where l.etat=0 and l.id_action_id in(select a.id from debt_force_integration.actions_import_dbi a where a.id_import in (select i.id FROM import i where i.id  = :id ))";
                    $param=(array("id"=>$details_import[$i]["id"]  ));
                    $error = $this->conn->fetchAllAssociative($sql , $param);

                    $sql="SELECT distinct l.rapport as rapport, count(l.rapport) as nombrefrom from debt_force_integration.logs_actions_dbi l where l.etat=1 and l.id_action_id in(select a.id from debt_force_integration.actions_import_dbi a where a.id_import = :id  ) group by l.rapport";
                    $param=(array("id"=>$details_import[$i]["id"] ));
                    $rapport = $this->conn->fetchAllAssociative($sql , $param);
                    // dump($success);
                    $array_details[$i]["success"] = $success;
                    $array_details[$i]["rapport"] = $rapport;
                    $array_details[$i]["error"] = $error;
                }

                $array_details_prod = array();
                for ($i=0; $i < count($details_import); $i++) { 
                    $array_details_prod[$i]["import"] = $details_import[$i];
                    $sql="SELECT * FROM `actions_import` WHERE  id_import_id = :id and date_debut = (SELECT MIN(date_debut) FROM `actions_import` WHERE id_import_id = :id);";
                    $param=(array("id"=>$details_import[$i]["id"]));
                    $actions = $this->conn->fetchAssociative($sql , $param);
                    $array_details_prod[$i]["actions"] = $actions;

                    $sql="SELECT l.* from logs_actions l where l.etat=1 and l.id_action_id in(select a.id from actions_import a where a.id_import_id in (select i.id FROM import i where i.id  = :id ))";
                    $param=(array("id"=>$details_import[$i]["id"]  ));
                    $success = $this->conn->fetchAllAssociative($sql , $param);

                    $sql="SELECT l.* from logs_actions l where l.etat=0 and l.id_action_id in(select a.id from actions_import a where a.id_import_id in (select i.id FROM import i where i.id  = :id ))";
                    $param=(array("id"=>$details_import[$i]["id"]  ));
                    $error = $this->conn->fetchAllAssociative($sql , $param);

                    $sql="SELECT distinct l.rapport as rapport, count(l.rapport) as nombrefrom from logs_actions l where l.etat=1 and l.id_action_id in(select a.id from actions_import a where a.id_import_id = :id  ) group by l.rapport";
                    $sql="SELECT distinct l.rapport as rapport, count(l.rapport) as nombrefrom from logs_actions l where l.etat=1 and l.id_action_id in(select a.id from actions_import a where a.id_import_id = :id  ) group by l.rapport";

                    $param=(array("id"=>$details_import[$i]["id"] ));
                    $rapport = $this->conn->fetchAllAssociative($sql , $param);
                    
                    $array_details_prod[$i]["success"] = $success;
                    $array_details_prod[$i]["rapport"] = $rapport;
                    $array_details_prod[$i]["error"] = $error;
                }
                
                // $respObjects["data"]["actions"]=$actions;
                // $respObjects["data"]["suceess"]=$success;
                // $respObjects["data"]["warning"]=$warning;
                // $respObjects["data"]["error"]=$error;
                // $respObjects["data"]["rapport"]=$rapport;
                $respObjects["data"]["integration"]=$integration;
                $respObjects["data"]["details_import"]=$details_import;
                $respObjects["data"]["dt"]=$array_details;
                $respObjects["data"]["dt_prod"]=$array_details_prod;
                $codeStatut="OK";
            }

        } catch (\Exception $e) {
            $codeStatut="ERROR";
            $respObjects["err"]=$e->getMessage();
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getImportByIntegration')]
    public function getImportByIntegration(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        $id = $request->get("id");
        try {
            $this->AuthService->checkAuth(0,$request);
            $integration = $integrationRepo->getOneIntegration($id);
            if(!$integration){
                $codeStatut="INTEGRATION_NOT_EXIST";
            }else{
                //code...
                $details = $integrationRepo->getAllImportByIntegration($id);
                $respObjects["details"]=$details;

                $codeStatut="OK";
            }

        } catch (\Exception $e) {
            $codeStatut="ERROR";
            $respObjects["err"]=$e->getMessage();
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    
    #[Route('/testRedoublent')]
    public function testRedoublent(integrationRepo $integrationRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $this->verifecationDoublent(140);
            // $respObjects["token"] = $jwt;
            $codeStatut = "OK";
            }catch(\Exception $e){
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    
    #[Route('/getAllIntegration')]
    public function listeGroupe(Request $request , integrationRepo $integrationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = $integrationRepo->getAllInegration();
            $codeStatut = "OK";
            $respObjects["data"] = $data;
            
        }catch(\Exception $e){
            $respObjects["err"] = $e->getMessage();
            $codeStatut = "ERREUR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getAllPTF')]
    public function listePorteFeuille(integrationRepo $integrationRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $data = $integrationRepo->getListePtf();
            $codeStatut = "OK";
            $respObjects["data"] = $data;
        }catch(\Exception $e){
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    function in_array_r($needle, $haystack) {
        foreach ($haystack as $item) {
            if (in_array($needle,$item)) {
                return true;
            }
        } 
        return false;
    }
    public function convert($filename, $delimiter = ';')
    {
        if(!file_exists($filename) || !is_readable($filename)) {
            return FALSE;
        }
        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {
                if(!$header) {
                    $header = $row;
                    $header = array_map('trim',$header);
                    $header = array_map("utf8_encode", str_replace(" ","_",$header));
                } else {
                    $row = array_map("utf8_encode", $row);
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }
    public function testDebiteur($id)
    {
        $response=false;
        $debiteur = $this->em->getRepository(Debiteur::class)->findOneBy(["cin_formate"=>$id]);
        if($debiteur)
        {
            $response=true;
        }
        return $response;
    }
    public function testDebiteurIntg($id , $id_import)
    {
        $response=false;
        $debiteur = $this->em->getRepository(Debiteur::class)->findOneBy(["cin_formate"=>$id]);
        if($debiteur)
        {
            $response=true;
        }else{
            $debiteurIn = $this->em->getRepository(IntegDebiteur::class)->findOneBy(["cin_formate"=>$id , "id_import"=>$id_import]);
            if($debiteurIn){
                $response=true;
            }else{
                $response = false;
            }
        }
        return $response;
    }
    public function testDossierDbi1($num,$idPtf)
    {
        $response=false;
        $query = $this->em->createQuery('SELECT t from App\Entity\Dossier t where t.numero_dossier =:num and t.id_ptf =:idPtf');
        $query->setParameter('num', $num);
        $query->setParameter('idPtf', $idPtf);
        $dossier = $query->getResult();
        if($dossier)
        {
            $response=true;
        }
        return $response;
    }
    public function testDossier($num,$idPtf)
    {
        $response=false;
        $query = $this->em->createQuery('SELECT t from App\Entity\Dossier t where t.numero_dossier =:num and t.id_ptf =:idPtf');
        $query->setParameter('num', $num);
        $query->setParameter('idPtf', $idPtf);
        $dossier = $query->getResult();
        if($dossier)
        {
            $response=true;
        }
        return $response;
    }
    public function testCreance($num,$idPtf)
    {
        $response=false;
        $query= $this->em->createQuery("select c from App\Entity\Creance c where c.numero_creance=:num and identity(c.id_ptf) =:idPtf");
        $query->setParameter('num', $num);
        $query->setParameter('idPtf', $idPtf);
        $creance=$query->getResult();
        if($creance)
        {
            $response=true;
        }
        return $response;
    }
    public function testDossierInInteg($num,$idPtf)
    {
        $response=false;
        $query = $this->em->createQuery('SELECT t from App\Entity\Dossier t where t.numero_dossier =:num and t.id_ptf =:idPtf');
        $query->setParameter('num', $num);
        $query->setParameter('idPtf', $idPtf);
        $dossier = $query->getResult();
        if($dossier)
        {
            $response=true;
        }else{
            $debiteurIn = $this->em->getRepository(IntegDossier::class)->findOneBy(["value_num_dossier"=>$num , "value_ptf"=>$idPtf]);
            if($debiteurIn){
                $response=true;
            }else{
                $response = false;
            }
        }
        return $response;
    }
    function formatPhoneNumber($phoneNumber) {
        $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);
        $areCode="212";
    
        $number = substr($phoneNumber,-9);
        if(strlen($phoneNumber) >= 10) {
            $phoneNumber = $areCode . $number;
        }else if(strlen($phoneNumber) == 9){
            $startNumber = substr($phoneNumber,0,1);
            if($startNumber == 5 || $startNumber== 6 || $startNumber== 7 ){
                $phoneNumber = $areCode . $number;
            }
        }
        return $phoneNumber;
    }

    function verifecationDoublent($a){
        $sql="select t.* from telephone t WHERE t.id in (select a.id_telephone_id from logs_actions a where a.id_telephone_id and a.id_action_id = :id );";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $a);
        $stmt = $stmt->executeQuery();
        $tele_table = $stmt->fetchAll();
        
        for($i=0 ; $i < count($tele_table) ; $i++){
            $id_deb =  $tele_table[$i]["id_debiteur_id"];
            $tele =  $tele_table[$i]["numero"];
            $id_tel = $tele_table[$i]["id"];

            $sql="update `logs_actions` set id_telephone_id = null where id_telephone_id=:id_tel";
            $stmt = $this->conn->prepare($sql );
            $stmt->bindParam('id_tel', $id_tel);
            $stmt = $stmt->executeQuery();

            $sql="SELECT count(*) nbr , id from telephone where id_debiteur_id=".$id_deb." and numero=:numero and active='0'  ORDER BY `telephone`.`id` DESC";           
            $param=array("numero"=>$tele);
            $teleRedoubleT=$this->conn->fetchAssociative($sql,$param);

            if($teleRedoubleT["nbr"] > 1){
                $sql="DELETE FROM telephone WHERE id = ".$teleRedoubleT["id"]."";
                $stmt = $this->conn->prepare($sql );
                $stmt->execute();
            }else {
                $sql="SELECT count(*) nbr , id from telephone where id_debiteur_id=".$id_deb." and numero=:numero ORDER BY `telephone`.`id` DESC";
                $param=array("numero"=>$tele);
                $teleRedoubleActif=$this->conn->fetchAssociative($sql,$param);
                    
                if($teleRedoubleActif["nbr"] > 1){
                    $sql="DELETE FROM telephone WHERE id = ".$teleRedoubleActif["id"]." and active ='0' ";
                    $stmt = $this->conn->prepare($sql );
                    $stmt->execute();
                }
            }

            $sql="SELECT count(*) nbr , id from telephone where id_debiteur_id=".$id_deb." and numero=:numero and active='0'  ORDER BY `telephone`.`id` DESC";           
            $param=array("numero"=>$tele);
            $teleRedoubleT=$this->conn->fetchAssociative($sql,$param);

            if( $teleRedoubleT['nbr'] == "1"){
                $sql="update telephone set active=1 where id_debiteur_id=".$teleRedoubleT["id"];
                $this->em->getConnection()->prepare($sql)->execute();
            }
        }
    }
}
