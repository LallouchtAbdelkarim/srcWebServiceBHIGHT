<?php

namespace App\Controller\Encaissement;

use App\Entity\Creance;
use App\Entity\DetailsImportPaiement;
use App\Entity\Portefeuille;
use App\Entity\Dossier;
use App\Entity\StatusDetailsAccord;
use App\Entity\StatusDetImpP;
use App\Entity\TypeDebiteur;
use App\Entity\TypePaiement;
use App\Entity\Customer\ActionImportPaiementDbi;
use App\Entity\ActionsImportPaiement;
use Proxies\__CG__\App\Entity\ImportPaiement;
use Proxies\__CG__\App\Entity\Utilisateurs;
use Proxies\__CG__\App\Entity\StatusImportPaiement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\AuthService;
use App\Service\MessageService;
use App\Repository\Encaissement\paiementRepo;
use App\Service\FileService;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/API/encaissement')]

class paiementController extends AbstractController
{
    private  $paiementRepo;
    private  $donneurRepo;
    private  $affichageRepo;
    private  $serializer;
    public $em;
    private $conn;
    private $AuthService;


    public function __construct(
        paiementRepo $paiementRepo,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        MessageService $MessageService,
        Connection $conn,
        FileService $FileService,
        AuthService $AuthService
        )
    {
        $this->conn = $conn;
        $this->paiementRepo = $paiementRepo;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->MessageService = $MessageService;
        $this->FileService = $FileService;
        $this->AuthService = $AuthService;
    }
    #[Route('/setImport', methods:"POST")]
    public function setImport(Request $request,paiementRepo $paiementRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $titre = $request->get("titre");
            $colFile = $request->get("colFile");
            $colDb = $request->get("colDb");
            if($titre != "")
            {
                if(!empty($_FILES['file']['name'])){
                    $file = $_FILES['file'];
                    $fileCheck=$this->FileService->checkFile($file);
                    if($fileCheck["codeStatut"] == "OK" ){
                        if(!in_array("dossier",$colDb) or !in_array("creance",$colDb) or !in_array("typePaiement",$colDb) or !in_array("datePaiement",$colDb) or !in_array("montant",$colDb) or !in_array("porte_feuille",$colDb))
                        {
                            $codeStatut="EMPTY-DATA";
                        }
                        else
                        {
                            $nom = $fileCheck["nom"];
                            $extension_upload=$fileCheck["extension_upload"];
                            $fileTmpLoc=$fileCheck["fileTmpLoc"];
                            $id_users = $this->AuthService->returnUserId($request);
                            $user =$this->em->getRepository(Utilisateurs::class)->findOneBy(["id"=>$id_users]);
                            $status =$this->em->getRepository(StatusImportPaiement::class)->findOneBy(["id"=>1]);
                            $import = new ImportPaiement();
                            $import->setDateCreation(new \DateTime());
                            $import->setIdUsers($user);
                            $import->setStatus($status);
                            $import->setUrlFile("");
                            $import->setTitre($titre);
                            $this->em->persist($import);
                            $this->em->flush();
        
                            $filesystem = new Filesystem();
                            $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/fichiers/import_paiement/';
                            $folderPath = $publicDirectory . 'import-num-'.$import->getId();
                            $fileStore = "fichiers/import_paiement/import-num-".$import->getId() ."/". $nom . '.' . $extension_upload;
                            //----Create file if n'existe pas
                            $filesystem->mkdir($folderPath);
                            move_uploaded_file($fileTmpLoc, $fileStore);
                            $import->setUrlFile($fileStore);
                            $this->em->flush();
        
                            //SetChunks
                            $csvData = file_get_contents($import->getUrlFile());
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
                                $folderPath = "fichiers/import_paiement/import-num-".$import->getId()."/split_files/";
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
                                    $status_d =$this->em->getRepository(StatusDetImpP::class)->findOneBy(["id"=>1]);
                                // try {
                                    // Move the file to the public/split_files directory
                                    file_put_contents($outputFilePath, $outputCsvData);
                                    $detail = new DetailsImportPaiement();
                                    $filesystem = new Filesystem();
                                    $detail->setUrl($outputFilePath);
                                    $detail->setIdImport($import);
                                    $detail->setOrdre($order);
                                    $detail->setStatus($status_d);
                                    $this->em->persist($detail);
                                    $this->em->flush();
                                    $order++;
                                    $codeStatut="OK";
                                // } catch (\Exception $e) {
                                //     $codeStatut="ERROR";
                                // }
                            }

                        }
                        
    
                    }else{
                        $codeStatut=$fileCheck["codeStatut"] ;
                    }
                }
            }
            else{
                $codeStatut='EMPTY-DATA';
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/startImportToDBI', methods:"POST")]
    public function startImport(Request $request,ManagerRegistry $doctrine ,paiementRepo $paiementRepo): JsonResponse
    {
        ini_set('memory_limit','-1');
        ini_set('memory_size','-1');
        ini_set('max_execution_time','-1');
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $emDbi = $doctrine->getManager('customer');
           
            $ImportNonCommencer = $paiementRepo->getAllImportByStatus(1);
            $data_list = json_decode($request->getContent(), true);
            $colFile = $data_list["colFile"];
            $colDb = $data_list["colDb"];

            if($ImportNonCommencer){
                for ($t=0; $t < count($ImportNonCommencer);$t++) { 
                    $id_import =  $ImportNonCommencer[$t]->getId();
                    $id_user =  $ImportNonCommencer[$t]->getIdUsers()->getId();
                    $nbrAccordsSys=0;
                    $nbrAccordsAgent=0;
                    $nbrConfirme=0;
                    $montantConfirme=0;
                    $montantAccordsSys=0;
                    $montantAccordsAgent=0;
                    $detailsImp = $paiementRepo->getDetailsImprt($id_import , 1);
                    $filePath = $detailsImp->getUrl();
                    $action=new ActionImportPaiementDbi();
                    $action->setIdImport($id_import);
                    $emDbi->persist($action);
                    $emDbi->flush();
                    $id_action = $action->getId();
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
                        $data=$this->FileService->convert($filePath,";");

                        if(count($data)>0){
                            if($data)
                            {
                                $numDossier="";
                                $numCreance="";
                                $ty="";
                                $datePaiement="";
                                $montant="";
                                $commentaire="";
                                $numPtf="";

                                foreach ($data as $row)
                                {
                                    $motif="";
                                    for ($i = 0; $i < count($colFile); $i++)
                                    {
                                        if($colDb[$i]=="dossier")
                                        {
                                            $numDossier=$row[$colFile[$i]];
                                        }
                                        if($colDb[$i]=="creance")
                                        {
                                            $numCreance=$row[$colFile[$i]];
                                        }
                                        if($colDb[$i]=="typePaiement")
                                        {
                                            $ty=trim($row[$colFile[$i]]);
                                            // $ty=str_replace("'","",$ty);
                                        }
                                        if($colDb[$i]=="datePaiement")
                                        {
                                            $datePaiement=$row[$colFile[$i]];
                                        }
                                        if($colDb[$i]=="montant")
                                        {
                                            $montant=$row[$colFile[$i]];
                                        }
                                        if($colDb[$i]=="commentaire")
                                        {
                                            $commentaire=$row[$colFile[$i]];
                                        }
                                        if($colDb[$i]=="ptf")
                                        {
                                            $numPtf=$row[$colFile[$i]];
                                        }
                                    }
                                    if($numDossier=="" or $numCreance=="" or $ty=="" or $datePaiement=="" or $montant=="" or $numPtf=="")
                                    {
                                        $motif="Un des champs obligatoire est vide";
                                        goto insertError;
                                    }
                                    else
                                    {
                                        $ptf = $this->em->getRepository(Portefeuille::class)->findOneBy(["numeroPtf"=>$numPtf]);
                                        if(!$ptf)
                                        {
                                            $motif="Portefeuille avec n° ".$numPtf." non trouvé";
                                            goto insertError;
                                        }
                                        else
                                        {
                                            //TODO:hna ziid l etat dialo
                                            $creance=$this->em->getRepository(Creance::class)->findOneBy(array("numero_creance"=>$numCreance));
                                            if(!$creance)
                                            {
                                                $motif="Creance avec n° ".$numCreance." archivée ou non trouvée";
                                                goto insertError;
                                            }
                                            else
                                            {
                                                if(!is_numeric($montant) or $montant<=0)
                                                {
                                                    $motif="Montant de paiement erronné";
                                                    goto insertError;
                                                }
                                                else
                                                {
                                                    $typePaiement=$this->em->getRepository(TypePaiement::class)->findOneBy(["type"=>$ty]);
                                                    if(!$typePaiement)
                                                    {
                                                        $motif="Type de paiement erronée";
                                                        goto insertError;
                                                    }
                                                    else
                                                    {
                                                        if (\DateTime::createFromFormat('d/m/Y', $datePaiement)==true)
                                                        {
                                                            $valueDate=\DateTime::createFromFormat('d/m/Y', $datePaiement)->format("d-m-Y");
                                                            $datePaiement= date_format(new \DateTime($valueDate), 'Y-m-d H:i:s');
                                                            $accord = $paiementRepo->getDetailsAccord($creance->getId());
                                                            $paiement=$paiementRepo->getPaiement($creance->getId(),$montant);
                                                            $id_creance = $creance->getId();
                                                            if(!$accord and !$paiement){
                                                                //TODO:Validé
                                                                //Restant dialo
                                                                $solde = $paiementRepo->getRestantCreance($creance->getId());
                                                                if($solde > $montant)
                                                                {
                                                                    $sql="insert into debt_force_integration.accord_dbi (`id_type_paiement`, `date_premier_paiement`, `montant`, `frequence`, `nbr_echeanciers`, `etat`, `date_creation`, `date_fin_paiement`) values(".$typePaiement->getId().",'".$datePaiement."',".$montant.",1,1,1,sysdate(),'".$datePaiement."')";
                                                                    $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                    $sql = "SELECT MAX(id) FROM debt_force_integration.accord_dbi";
                                                                    $maxAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                    $sql="insert into debt_force_integration.creance_accord_dbi (`id_creance`, `id_accord`) values(".$creance->getId().",".$maxAccord.")";
                                                                    $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                    $sql = "SELECT MAX(id) FROM debt_force_integration.creance_accord_dbi";
                                                                    $maxCreanceAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                    $sql="insert into debt_force_integration.details_accord_dbi (`id_accord`, `montant`, `status`, `id_type_paiement`, `montant_paiement`, `montant_restant`, `date_paiement`) values(".$maxAccord.",".$montant.",1,".$typePaiement->getId().",".$montant.",0,'".$datePaiement."')";
                                                                    $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                    $nbrAccordsSys++;
                                                                    $montantAccordsSys+=$montant;
                                                                }
                                                                else
                                                                {
                                                                    $sql="insert into debt_force_integration.accord_dbi (`id_type_paiement`, `date_premier_paiement`, `montant`, `frequence`, `nbr_echeanciers`, `etat`, `date_creation`, `date_fin_paiement`) values(".$typePaiement->getId().",'".$datePaiement."',".$solde.",1,1,1,sysdate(),'".$datePaiement."')";
                                                                    $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                    $sql = "SELECT MAX(id) FROM debt_force_integration.accord_dbi";
                                                                    $maxAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                    $sql="insert into debt_force_integration.creance_accord_dbi (`id_creance`, `id_accord`) values(".$creance->getId().",".$maxAccord.")";
                                                                    $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                    $sql = "SELECT MAX(id) FROM debt_force_integration.creance_accord_dbi";
                                                                    $maxCreanceAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                    $sql="insert into debt_force_integration.details_accord_dbi (`id_accord`, `montant`, `status`, `id_type_paiement`, `montant_paiement`, `montant_restant`, `date_paiement`) values(".$maxAccord.",".$solde.",1,".$typePaiement->getId().",".$montant.",0,'".$datePaiement."')";
                                                                    $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                    $nbrAccordsSys++;
                                                                    $montantAccordsSys+=$solde;
                                                                }
                                                                $sql = "SELECT MAX(id) FROM debt_force_integration.details_accord_dbi";
                                                                $maxDetailAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                $date=new \DateTime();
                                                                $dmy = $date->format('dmYHis');
                                                                $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $creance->getId(), "id_type" => 3]);
                                                                if (!$typeDeb) {
                                                                    $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $creance->getId()]);
                                                                }
                                                                $idDeb =1;
                                                                $ref = "PA" . $dmy . ($typeDeb ? $typeDeb->getId() : "");
                                                                $sql="insert into debt_force_integration.paiement_dbi (`id_creance`, `id_type_paiement`, `ref`, `montant`, `date_creation`, `date_paiement`, `etat`, `id_users`, `id_debiteur`, `id_ptf`, `id_details_accord`, `commentaire`, `id_import`, `confirmed`) values(".$creance->getId().",".$typePaiement->getId().",'".$ref."',".$montant.",sysdate(),'".$datePaiement."',0,".$id_user.",".$idDeb.",".$creance->getIdPtf()->getId().",".$maxDetailAccord.",'".$commentaire."',".$detailsImp->getId().",1)";
                                                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                
                                                                $sql = "SELECT MAX(id) FROM debt_force_integration.paiement_dbi";
                                                                $maxPaiement = $this->conn->executeQuery($sql)->fetchOne();
                                                                $sql="insert into debt_force_integration.paiement_accord_dbi (`id_paiement`,`id_details_accord`) values(".$maxPaiement.",".$maxDetailAccord.")";
                                                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                $sql="SELECT * FROM debt_force_integration.`creance_paiement_dbi` c where c.id_creance = :id_creance and c.id_action = :id_action ORDER BY c.id DESC";
                                                                $stmt = $this->conn->prepare($sql);
                                                                $stmt->bindValue(":id_creance",$id_creance);
                                                                $stmt->bindValue(":id_action",$id_action);
                                                                $stmt = $stmt->executeQuery();
                                                                $checkIfAlreadyExist = $stmt->fetchAssociative();
                                                                // dump($checkIfAlreadyExist);
                                                                if(!$checkIfAlreadyExist){
                                                                    $newTotalRestant = $creance->getTotalRestant() - $montant;
                                                                    $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                                                                    (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                                                                    VALUES (" . $creance->getId() . ", " . $creance->getTotalRestant() . ", " . $newTotalRestant . ", " . $montant . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                                                                }else{
                                                                    $newTotalRestant = $checkIfAlreadyExist["new_total_restant"] - $montant;
                                                                    $TotalRestant = $checkIfAlreadyExist["new_total_restant"] ;
                                                                    $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                                                                    (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                                                                    VALUES (" . $creance->getId() . ", " . $TotalRestant . ", " . $newTotalRestant . ", " . $montant . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                                                                }
                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                $sql="insert into debt_force_integration.accord_import_dbi (`type`, `id_accord`, `id_details_accord`, `id_import`, `id_creance_accord`) values(1,".$maxAccord.",".$maxDetailAccord.",".$id_import.",".$maxCreanceAccord.")";
                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                $sql="insert into debt_force_integration.paiement_import_dbi ( `id_import`, `id_creance`, `id_paiement`) values(".$id_import.",".$creance->getId().",".$maxPaiement.")";
                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                            }
                                                            elseif($paiement)
                                                            {
                                                                //TODO:Non Validé
                                                                $sql="insert into debt_force_integration.paiement_dbi (`id_creance`, `id_type_paiement`, `ref`, `montant`, `date_creation`, `date_paiement`, `etat`, `id_users`, `id_debiteur`, `id_ptf`, `id_details_accord`, `commentaire`, `id_import`, `confirmed`,`is_update`) values(".$creance->getId().",".$typePaiement->getId().",'".$ref."',".$montant.",sysdate(),'".$datePaiement."',0,".$id_user.",".$idDeb.",".$creance->getIdPtf()->getId().",".$maxDetailAccord.",'".$commentaire."',".$detailsImp->getId().",1,".true.")";
                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                $nbrConfirme++;
                                                                $montantConfirme+=$paiement[0]->getMontant();
                                                                $sql="insert into debt_force_integration.paiement_import_dbi ( `id_import`, `id_creance`, `id_paiement`) values(".$id_import.",".$creance->getId().",".$paiement.")";
                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                            }
                                                            else
                                                            {
                                                                $rest = $montant;
                                                                while($rest > 0)
                                                                {
                                                                    $autreDetails = $paiementRepo->getDetailsAccord2($numCreance , $id_action);
                                                                    
                                                                    if($autreDetails)
                                                                    {
                                                                        foreach ($autreDetails as $autreDetail)
                                                                        {
                                                                            if($rest>=$autreDetail["montant_restant"])
                                                                            {//TODO: Validé
                                                                                $sql = "INSERT INTO `debt_force_integration`.`details_accord_paiement` 
                                                                                (`id_detsils_accord`, `id_import`,`id_action`, `old_montant_paiement`, `new_montant_paiement`, `new_montant_restant`, `old_montant_restant`, `type`)
                                                                                VALUES (" . $autreDetail['id'] . ", " . $id_import . ", " . $id_action . ", " . $autreDetail["montant_paiement"] . ", " . $autreDetail["montant_restant"] . ", 0, " . $autreDetail["montant_restant"] . ",update)";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                                $nbrAccordsAgent++;
                                                                                $montantAccordsAgent+=$autreDetail["montant_restant"];

                                                                                $date=new \DateTime();
                                                                                $dmy = $date->format('dmYHis');
                                                                                $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $creance->getId(), "id_type" => 3]);
                                                                                if (!$typeDeb) {
                                                                                    $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $creance->getId()]);
                                                                                }
                                                                                $idDeb =1;
                                                                                $ref = "PA" . $dmy . ($typeDeb ? $typeDeb->getId() : "");

                                                                                $sql="insert into debt_force_integration.paiement_dbi (`id_creance`, `id_type_paiement`, `ref`, `montant`, `date_creation`, `date_paiement`, `etat`, `id_users`, `id_debiteur`, `id_ptf`, `id_details_accord`, `commentaire`, `id_import`, `confirmed`) values(".$creance->getId().",".$typePaiement->getId().",'".$ref."',".$autreDetail["montant_restant"].",sysdate(),'".$datePaiement."',0,".$id_user.",".$idDeb.",".$creance->getIdPtf()->getId().",".$autreDetail["id"].",'".$commentaire."',".$id_import.",1)";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                                $sql = "SELECT MAX(id) FROM debt_force_integration.paiement_dbi";
                                                                                $maxPaiement = $this->conn->executeQuery($sql)->fetchOne();
                                                                                $sql="insert into debt_force_integration.paiement_accord_dbi (`id_paiement`,`id_details_accord`) values(".$maxPaiement.",".$autreDetail["id"].")";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                                $sql="insert into debt_force_integration.accord_import_dbi (`type`, `id_details_accord`, `id_import`) values(1,".$autreDetail["id"].",".$id_import.")";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                                
                                                                                //TODO: Modifier le rest de creance
                                                                                $sql="insert into debt_force_integration.paiement_import_dbi ( `id_import`, `id_creance`, `id_paiement`) values(".$id_import.",".$creance->getId().",".$maxPaiement.")";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                                $rest = ($rest - $autreDetail['montant_restant']);
                                                                                if($rest <= 0)
                                                                                {
                                                                                    break;
                                                                                }
                                                                            }
                                                                            else
                                                                            {
                                                                                $sql = "INSERT INTO `debt_force_integration`.`details_accord_paiement` 
                                                                                (`id_detsils_accord`, `id_import`,`id_action`, `old_montant_paiement`, `old_montant_restant`, `new_montant_paiement`, `new_montant_restant`,  `type`)
                                                                                VALUES (" . $autreDetail['id'] . ", " . $id_import . ", " . $id_action . ", " . $autreDetail["montant_paiement"] . ", " . $autreDetail["montant_restant"] . "," . $autreDetail["montant_restant"]. ", ".($autreDetail["montant_restant"] - $rest).", 'update')";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                                $nbrAccordsAgent++;
                                                                                $montantAccordsAgent+=$rest;

                                                                                $date=new \DateTime();
                                                                                $dmy = $date->format('dmYHis');
                                                                                $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $creance->getId(), "id_type" => 3]);
                                                                                if (!$typeDeb) {
                                                                                    $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $creance->getId()]);
                                                                                }
                                                                                $idDeb =1;
                                                                                $ref = "PA" . $dmy . ($typeDeb ? $typeDeb->getId() : "");

                                                                                $sql="insert into debt_force_integration.paiement_dbi (`id_creance`, `id_type_paiement`, `ref`, `montant`, `date_creation`, `date_paiement`, `etat`, `id_users`, `id_debiteur`, `id_ptf`, `id_details_accord`, `commentaire`, `id_import`, `confirmed`) values
                                                                                (".$creance->getId().",".$typePaiement->getId().",'".$ref."',".$autreDetail["montant_restant"].",sysdate(),'".$datePaiement."',0,".$id_user.",".$idDeb.",".$creance->getIdPtf()->getId().",".$autreDetail["id"].",'".$commentaire."',".$id_import.",1)";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                                $sql = "SELECT MAX(id) FROM debt_force_integration.paiement_dbi";
                                                                                $maxPaiement = $this->conn->executeQuery($sql)->fetchOne();
                                                                                $sql="insert into debt_force_integration.paiement_accord_dbi (`id_paiement`,`id_details_accord`) values(".$maxPaiement.",".$autreDetail["id"].")";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                                $checkIfAlreadyExist2 = $paiementRepo->checkIfUpdatedCreance($id_creance , $id_action);
                                                                                if(!$checkIfAlreadyExist2){
                                                                                    $newTotalRestant = $creance->getTotalRestant() - $rest;
                                                                                    $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                                                                                    (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                                                                                    VALUES (" . $creance->getId() . ", " . $creance->getTotalRestant() . ", " . $newTotalRestant . ", " . $rest . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                                                                                }else{
                                                                                    $newTotalRestant = $checkIfAlreadyExist2["new_total_restant"] - $rest;
                                                                                    $TotalRestant = $checkIfAlreadyExist2["new_total_restant"] ;
                                                                                    $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                                                                                    (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                                                                                    VALUES (" . $creance->getId() . ", " . $TotalRestant . ", " . $newTotalRestant . ", " . $rest . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                                                                                }
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                                $sql="insert into debt_force_integration.accord_import_dbi (`type`, `id_details_accord`, `id_import`) values(1,".$autreDetail["id"].",".$id_import.")";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                                $sql="insert into debt_force_integration.paiement_import_dbi ( `id_import`, `id_creance`, `id_paiement`) values(".$id_import.",".$creance->getId().",".$maxPaiement.")";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                                $rest=0;
                                                                            }
                                                                        }
                                                                    }
                                                                    else
                                                                    {//TODO:Non Validé
                                                                        $solde = $paiementRepo->getRestantCreance($creance->getId());
                                                                        if($solde > $rest)
                                                                        { 
                                                                            $sql="insert into debt_force_integration.accord_dbi (`id_type_paiement`, `date_premier_paiement`, `montant`, `frequence`, `nbr_echeanciers`, `etat`, `date_creation`, `date_fin_paiement`) values(".$typePaiement->getId().",'".$datePaiement."',".$rest.",1,1,1,sysdate(),'".$datePaiement."')";
                                                                            $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                            $sql = "SELECT MAX(id) FROM debt_force_integration.accord_dbi";
                                                                            $maxAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                            $sql="insert into debt_force_integration.creance_accord_dbi (`id_creance`, `id_accord`) values(".$creance->getId().",".$maxAccord.")";
                                                                            $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                            $sql = "SELECT MAX(id) FROM debt_force_integration.creance_accord_dbi";
                                                                            $maxCreanceAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                            $sql="insert into debt_force_integration.details_accord_dbi (`id_accord`, `montant`, `status`, `id_type_paiement`, `montant_paiement`, `montant_restant`, `date_paiement`) values(".$maxAccord.",".$rest.",1,".$typePaiement->getId().",".$rest.",0,'".$datePaiement."')";
                                                                            $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                            $nbrAccordsSys++;
                                                                            $montantAccordsSys+=$rest;
                                                                        }
                                                                        else
                                                                        {
                                                                            $sql="insert into debt_force_integration.accord_dbi (`id_type_paiement`, `date_premier_paiement`, `montant`, `frequence`, `nbr_echeanciers`, `etat`, `date_creation`, `date_fin_paiement`) values(".$typePaiement->getId().",'".$datePaiement."',".$solde.",1,1,1,sysdate(),'".$datePaiement."')";
                                                                            $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                            $sql = "SELECT MAX(id) FROM debt_force_integration.accord_dbi";
                                                                            $maxAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                            $sql="insert into debt_force_integration.creance_accord_dbi (`id_creance`, `id_accord`) values(".$creance->getId().",".$maxAccord.")";
                                                                            $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                            $sql = "SELECT MAX(id) FROM debt_force_integration.creance_accord_dbi";
                                                                            $maxCreanceAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                            $sql="insert into debt_force_integration.details_accord_dbi (`id_accord`, `montant`, `status`, `id_type_paiement`, `montant_paiement`, `montant_restant`, `date_paiement`) values(".$maxAccord.",".$solde.",1,".$typePaiement->getId().",".$rest.",0,'".$datePaiement."')";
                                                                            $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                            $nbrAccordsSys++;
                                                                            $montantAccordsSys+=$solde;
                                                                        }
                                                                        $sql = "SELECT MAX(id) FROM debt_force_integration.details_accord_dbi";
                                                                        $maxDetailAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                        $date=new \DateTime();
                                                                        $dmy = $date->format('dmYHis');
                                                                        $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $creance->getId(), "id_type" => 3]);
                                                                        if (!$typeDeb) {
                                                                            $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $creance->getId()]);
                                                                        }
                                                                        $idDeb =1;
                                                                        $ref = "PA" . $dmy . ($typeDeb ? $typeDeb->getId() : "");
                                                                        $sql="insert into debt_force_integration.paiement_dbi (`id_creance`, `id_type_paiement`, `ref`, `montant`, `date_creation`, `date_paiement`, `etat`, `id_users`, `id_debiteur`, `id_ptf`, `id_details_accord`, `commentaire`, `id_import`, `confirmed`) values(".$creance->getId().",".$typePaiement->getId().",'".$ref."',".$montant.",sysdate(),'".$datePaiement."',0,".$id_user.",".$idDeb.",".$creance->getIdPtf()->getId().",".$maxDetailAccord.",'".$commentaire."',".$detailsImp->getId().",1)";
                                                                        $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                        
                                                                        $sql = "SELECT MAX(id) FROM debt_force_integration.paiement_dbi";
                                                                        $maxPaiement = $this->conn->executeQuery($sql)->fetchOne();
                                                                        $sql="insert into debt_force_integration.paiement_accord_dbi (`id_paiement`,`id_details_accord`) values(".$maxPaiement.",".$maxDetailAccord.")";
                                                                        $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                        $checkIfAlreadyExist = $paiementRepo->checkIfUpdatedCreance($id_creance , $id_action);
                                                                        if(!$checkIfAlreadyExist){
                                                                            $newTotalRestant = $creance->getTotalRestant() - $montant;
                                                                            $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                                                                            (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                                                                            VALUES (" . $creance->getId() . ", " . $creance->getTotalRestant() . ", " . $newTotalRestant . ", " . $montant . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                                                                        }else{
                                                                            $newTotalRestant = $checkIfAlreadyExist["new_total_restant"] - $montant;
                                                                            $TotalRestant = $checkIfAlreadyExist["new_total_restant"] ;
                                                                            $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                                                                            (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                                                                            VALUES (" . $creance->getId() . ", " . $TotalRestant . ", " . $newTotalRestant . ", " . $montant . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                                                                        }
                                                                        $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                        
                                                                        $sql="insert into debt_force_integration.accord_import_dbi (`type`, `id_accord`, `id_details_accord`, `id_import`, `id_creance_accord`) values(1,".$maxAccord.",".$maxDetailAccord.",".$id_import.",".$maxCreanceAccord.")";
                                                                        $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                        $sql="insert into debt_force_integration.paiement_import_dbi ( `id_import`, `id_creance`, `id_paiement`) values(".$id_import.",".$creance->getId().",".$maxPaiement.")";
                                                                        $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                        $rest=0;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                            
                                    }
                                    insertError:
                                    if($motif!="")
                                    {
                                        
                                    }
                                }
                            }
                        }
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
    #[Route('/startImportToPROD', methods:"POST")]
    public function startImportPro(Request $request,ManagerRegistry $doctrine ,paiementRepo $paiementRepo): JsonResponse
    {
        ini_set('memory_limit','-1');
        ini_set('memory_size','-1');
        ini_set('max_execution_time','-1');
        $respObjects =array();
        $codeStatut="ERROR";
        // try{
            $emDbi = $doctrine->getManager('customer');
           
            $ImportNonCommencer = $paiementRepo->getAllImportByStatus(5);
            $data_list = json_decode($request->getContent(), true);
            $colFile = $data_list["colFile"];
            $colDb = $data_list["colDb"];

            if($ImportNonCommencer){
                for ($t=0; $t < count($ImportNonCommencer);$t++) { 
                    $id_import =  $ImportNonCommencer[$t]->getId();
                    $id_user =  $ImportNonCommencer[$t]->getIdUsers()->getId();
                    $nbrAccordsSys=0;
                    $nbrAccordsAgent=0;
                    $nbrConfirme=0;
                    $montantConfirme=0;
                    $montantAccordsSys=0;
                    $montantAccordsAgent=0;
                    $detailsImp = $paiementRepo->getDetailsImprt($id_import , 1);
                    $filePath = $detailsImp->getUrl();
                    $action=new ActionsImportPaiement();
                    $action->setIdImport($ImportNonCommencer[$t]);
                    $this->em->persist($action);
                    $this->em->flush();
                    $id_action = $action->getId();
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
                        $data=$this->FileService->convert($filePath,";");

                        if(count($data)>0){
                            if($data)
                            {
                                $numDossier="";
                                $numCreance="";
                                $ty="";
                                $datePaiement="";
                                $montant="";
                                $commentaire="";
                                $numPtf="";

                                foreach ($data as $row)
                                {
                                    $motif="";
                                    for ($i = 0; $i < count($colFile); $i++)
                                    {
                                        if($colDb[$i]=="dossier")
                                        {
                                            $numDossier=$row[$colFile[$i]];
                                        }
                                        if($colDb[$i]=="creance")
                                        {
                                            $numCreance=$row[$colFile[$i]];
                                        }
                                        if($colDb[$i]=="typePaiement")
                                        {
                                            $ty=trim($row[$colFile[$i]]);
                                            // $ty=str_replace("'","",$ty);
                                        }
                                        if($colDb[$i]=="datePaiement")
                                        {
                                            $datePaiement=$row[$colFile[$i]];
                                        }
                                        if($colDb[$i]=="montant")
                                        {
                                            $montant=$row[$colFile[$i]];
                                        }
                                        if($colDb[$i]=="commentaire")
                                        {
                                            $commentaire=$row[$colFile[$i]];
                                        }
                                        if($colDb[$i]=="ptf")
                                        {
                                            $numPtf=$row[$colFile[$i]];
                                        }
                                    }
                                    if($numDossier=="" or $numCreance=="" or $ty=="" or $datePaiement=="" or $montant=="" or $numPtf=="")
                                    {
                                        $motif="Un des champs obligatoire est vide";
                                        goto insertError;
                                    }
                                    else
                                    {
                                        $ptf = $this->em->getRepository(Portefeuille::class)->findOneBy(["numeroPtf"=>$numPtf]);
                                        if(!$ptf)
                                        {
                                            $motif="Portefeuille avec n° ".$numPtf." non trouvé";
                                            goto insertError;
                                        }
                                        else
                                        {
                                            //TODO:hna ziid l etat dialo
                                            $creance=$this->em->getRepository(Creance::class)->findOneBy(array("numero_creance"=>$numCreance));
                                            if(!$creance)
                                            {
                                                $motif="Creance avec n° ".$numCreance." archivée ou non trouvée";
                                                goto insertError;
                                            }
                                            else
                                            {
                                                if(!is_numeric($montant) or $montant<=0)
                                                {
                                                    $motif="Montant de paiement erronné";
                                                    goto insertError;
                                                }
                                                else
                                                {
                                                    $typePaiement=$this->em->getRepository(TypePaiement::class)->findOneBy(["type"=>$ty]);
                                                    if(!$typePaiement)
                                                    {
                                                        $motif="Type de paiement erronée";
                                                        goto insertError;
                                                    }
                                                    else
                                                    {
                                                        if (\DateTime::createFromFormat('d/m/Y', $datePaiement)==true)
                                                        {
                                                            $valueDate=\DateTime::createFromFormat('d/m/Y', $datePaiement)->format("d-m-Y");
                                                            $datePaiement= date_format(new \DateTime($valueDate), 'Y-m-d H:i:s');
                                                            $accord = $paiementRepo->getDetailsAccord($creance->getId());
                                                            $paiement=$paiementRepo->getPaiement($creance->getId(),$montant);
                                                            $id_creance = $creance->getId();
                                                            
                                                            if(!$accord and !$paiement){
                                                                //TODO:Validé
                                                                //Restant dialo
                                                                
                                                                $solde = $paiementRepo->getRestantCreance($creance->getId());
                                                                if($solde > $montant)
                                                                {
                                                                    $sql="insert into accord (`id_type_paiement_id`, `date_premier_paiement`, `montant`, `frequence`, `nbr_echeanciers`, `etat`, `date_creation`, `date_fin_paiement`,`id_users_id`,`id_status_id`) values(".$typePaiement->getId().",'".$datePaiement."',".$montant.",1,1,1,sysdate(),'".$datePaiement."','".$id_user."',1)";
                                                                    $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                    $sql = "SELECT MAX(id) FROM accord";
                                                                    $maxAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                    $sql="insert into creance_accord (`id_creance_id`, `id_accord_id`) values(".$creance->getId().",".$maxAccord.")";
                                                                    $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                    $sql = "SELECT MAX(id) FROM creance_accord";
                                                                    $maxCreanceAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                    $sql="insert into details_accord (`id_accord_id`, `montant`, `id_status_id`, `id_type_paiement_id`, `montant_paiement`, `montant_restant`, `date_paiement`,`id_user_id`) values(".$maxAccord.",".$montant.",1,".$typePaiement->getId().",".$montant.",0,'".$datePaiement."','".$id_user."')";
                                                                    $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                    $nbrAccordsSys++;
                                                                    $montantAccordsSys+=$montant;
                                                                }
                                                                else
                                                                {
                                                                    $sql="insert into accord (`id_type_paiement_id`, `date_premier_paiement`, `montant`, `frequence`, `nbr_echeanciers`, `etat`, `date_creation`, `date_fin_paiement`,`id_users_id`,`id_status_id`) values(".$typePaiement->getId().",'".$datePaiement."',".$solde.",1,1,1,sysdate(),'".$datePaiement."','".$id_user."',1)";
                                                                    $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                    $sql = "SELECT MAX(id) FROM accord";
                                                                    $maxAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                    $sql="insert into creance_accord (`id_creance_id`, `id_accord_id`) values('".$creance->getId()."','".$maxAccord."')";
                                                                    $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                    $sql = "SELECT MAX(id) FROM creance_accord";
                                                                    $maxCreanceAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                    $sql="insert into details_accord (`id_accord_id`, `montant`, `id_status_id`, `id_type_paiement_id`, `montant_paiement`, `montant_restant`, `date_paiement`,`id_user_id`) values(".$maxAccord.",".$solde.",1,".$typePaiement->getId().",".$montant.",0,'".$datePaiement."','".$id_user."')";
                                                                    $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                    $nbrAccordsSys++;
                                                                    $montantAccordsSys+=$solde;
                                                                }
                                                                $sql = "SELECT MAX(id) FROM details_accord";
                                                                $maxDetailAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                $date=new \DateTime();
                                                                $dmy = $date->format('dmYHis');
                                                                $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $creance->getId(), "id_type" => 3]);
                                                                if (!$typeDeb) {
                                                                    $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $creance->getId()]);
                                                                }
                                                                $idDeb =$typeDeb->getIdDebiteur()->getId();
                                                                $ref = "PA" . $dmy . ($typeDeb ? $typeDeb->getId() : "");//Etat de paiement pour l'etat l"annulaion si = 0 non annulé //si=1 est annulé
                                                                $sql="insert into paiement (`id_creance_id`, `id_type_paiement_id`, `ref`, `montant`, `date_creation`, `date_paiement`, `etat`, `id_users_id`, `id_debiteur_id`, `id_ptf_id`, `id_details_accord_id`, `commentaire`, `id_import_id`, `confirmed`)
                                                                 values(".$creance->getId().",".$typePaiement->getId().",'".$ref."',".$montant.",sysdate(),'".$datePaiement."',0,".$id_user.",".$idDeb.",".$creance->getIdPtf()->getId().",".$maxDetailAccord.",'".$commentaire."',".$id_import.",1)";
                                                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                
                                                                $sql = "SELECT MAX(id) FROM paiement";
                                                                $maxPaiement = $this->conn->executeQuery($sql)->fetchOne();
                                                                $sql="insert into paiement_accord (`id_paiement_id`,`id_details_accord_id`) values(".$maxPaiement.",".$maxDetailAccord.")";
                                                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                /*$sql="SELECT * FROM `creance_paiement_dbi` c where c.id_creance = :id_creance and c.id_action = :id_action ORDER BY c.id DESC";
                                                                $stmt = $this->conn->prepare($sql);
                                                                $stmt->bindValue(":id_creance",$id_creance);
                                                                $stmt->bindValue(":id_action",$id_action);
                                                                $stmt = $stmt->executeQuery();
                                                                $checkIfAlreadyExist = $stmt->fetchAssociative();
                                                                if(!$checkIfAlreadyExist){
                                                                    $newTotalRestant = $creance->getTotalRestant() - $montant;
                                                                    $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                                                                    (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                                                                    VALUES (" . $creance->getId() . ", " . $creance->getTotalRestant() . ", " . $newTotalRestant . ", " . $montant . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                                                                }else{
                                                                    $newTotalRestant = $checkIfAlreadyExist["new_total_restant"] - $montant;
                                                                    $TotalRestant = $checkIfAlreadyExist["new_total_restant"] ;
                                                                    $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                                                                    (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                                                                    VALUES (" . $creance->getId() . ", " . $TotalRestant . ", " . $newTotalRestant . ", " . $montant . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                                                                }*/
                                                                $total=($creance->getTotalRestant()-$montant);
                                                                $creance->setTotalRestant($total);
                                                                $this->em->flush();
                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                $sql="insert into accord_import (`id_accord_id`, `id_details_accord_id`, `id_import_id`, `id_creance_accord_id`) values(".$maxAccord.",".$maxDetailAccord.",".$id_import.",".$maxCreanceAccord.")";
                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                $sql="insert into paiement_import ( `id_import`, `id_creance`, `id_paiement`) values(".$id_import.",".$creance->getId().",".$maxPaiement.")";
                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                            }
                                                            elseif($paiement)
                                                            {
                                                                //TODO:Non Validé
                                                                $sql="update paiement set `confirmed`=1, `id_import_id`=".$id_import." where id=".$paiement->getId();
                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                $nbrConfirme++;
                                                                $montantConfirme+=$paiement[0]->getMontant();
                                                                $sql="insert into paiement_import ( `id_import`, `id_creance`, `id_paiement`) values(".$id_import.",".$creance->getId().",".$paiement.")";
                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                            }
                                                            else
                                                            {
                                                                $rest = $montant;
                                                                while($rest > 0)
                                                                {
                                                                    $autreDetails = $paiementRepo->getDetailsAccord3($numCreance);
                                                                    
                                                                    if($autreDetails)
                                                                    {
                                                                        foreach ($autreDetails as $autreDetail)
                                                                        {
                                                                            if($rest>=$autreDetail["montant_restant"])
                                                                            {//TODO: Validé
                                                                                $sql="update details_accord set `id_status_id`=1,`id_type_paiement_id`=".$typePaiement->getId().", `montant_paiement`=".$autreDetail["montant_restant"].", `montant_restant`=0, `date_paiement`=sysdate() where id=".$autreDetail["id"];
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                                $nbrAccordsAgent++;
                                                                                $montantAccordsAgent+=$autreDetail["montant_restant"];

                                                                                $date=new \DateTime();
                                                                                $dmy = $date->format('dmYHis');
                                                                                $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $creance->getId(), "id_type" => 3]);
                                                                                if (!$typeDeb) {
                                                                                    $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $creance->getId()]);
                                                                                }
                                                                                $idDeb =$typeDeb->getIdDebiteur()->getId();
                                                                                $ref = "PA" . $dmy . ($typeDeb ? $typeDeb->getId() : "");

                                                                                $sql="insert into paiement (`id_creance_id`, `id_type_paiement_id`, `ref`, `montant`, `date_creation`, `date_paiement`, `etat`, `id_users_id`, `id_debiteur_id`, `id_ptf_id`, `id_details_accord_id`, `commentaire`, `id_import_id`, `confirmed`) values(".$creance->getId().",".$typePaiement->getId().",'".$ref."',".$autreDetail["montant_restant"].",sysdate(),'".$datePaiement."',0,".$id_user.",".$idDeb.",".$creance->getIdPtf()->getId().",".$autreDetail["id"].",'".$commentaire."',".$id_import.",1)";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                                $sql = "SELECT MAX(id) FROM paiement";
                                                                                $maxPaiement = $this->conn->executeQuery($sql)->fetchOne();
                                                                                $sql="insert into paiement_accord (`id_paiement_id`,`id_details_accord_id`) values(".$maxPaiement.",".$autreDetail["id"].")";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                                
                                                                                $total=($creance->getTotalRestant()-$autreDetail->getMontantRestant());
                                                                                $creance->setTotalRestant($total);
                                                                                $this->em->flush();
                                                                                $sql="insert into accord_import (`type`, `id_details_accord_id`, `id_import_id`) values(1,".$autreDetail["id"].",".$id_import.")";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                                $sql="insert into paiement_import ( `id_import`, `id_creance`, `id_paiement`) values(".$id_import.",".$creance->getId().",".$maxPaiement.")";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                                $rest = ($rest - $autreDetail['montant_restant']);
                                                                                if($rest <= 0)
                                                                                {
                                                                                    break;
                                                                                }
                                                                            }
                                                                            else
                                                                            {
                                                                                
                                                                                $sql="update details_accord set `id_status_id`=2,`id_type_paiement_id`=".$typePaiement->getId().", `montant_paiement`=".$rest.", `montant_restant`=".($autreDetail["montant_restant"]-$rest).", `date_paiement`=sysdate() where id=".$autreDetail["id"];
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                                $nbrAccordsAgent++;
                                                                                $montantAccordsAgent+=$rest;

                                                                                $date=new \DateTime();
                                                                                $dmy = $date->format('dmYHis');
                                                                                $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $creance->getId(), "id_type" => 3]);
                                                                                if (!$typeDeb) {
                                                                                    $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $creance->getId()]);
                                                                                }
                                                                                $idDeb =$typeDeb->getIdDebiteur()->getId();
                                                                                $ref = "PA" . $dmy . ($typeDeb ? $typeDeb->getId() : "");

                                                                                $sql="insert into paiement (`id_creance_id`, `id_type_paiement_id`, `ref`, `montant`, `date_creation`, `date_paiement`, `etat`, `id_users_id`, `id_debiteur_id`, `id_ptf_id`, `id_details_accord_id`, `commentaire`, `id_import_id`, `confirmed`) values
                                                                                (".$creance->getId().",".$typePaiement->getId().",'".$ref."',".$autreDetail["montant_restant"].",sysdate(),'".$datePaiement."',0,".$id_user.",".$idDeb.",".$creance->getIdPtf()->getId().",".$autreDetail["id"].",'".$commentaire."',".$id_import.",1)";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                                $sql = "SELECT MAX(id) FROM paiement";
                                                                                $maxPaiement = $this->conn->executeQuery($sql)->fetchOne();
                                                                                $sql="insert into paiement_accord (`id_paiement_id`,`id_details_accord_id`) values(".$maxPaiement.",".$autreDetail["id"].")";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                                // $checkIfAlreadyExist2 = $paiementRepo->checkIfUpdatedCreance($id_creance , $id_action);dump($checkIfAlreadyExist2);
                                                                                // if(!$checkIfAlreadyExist2){
                                                                                //     $newTotalRestant = $creance->getTotalRestant() - $rest;
                                                                                //     $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                                                                                //     (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                                                                                //     VALUES (" . $creance->getId() . ", " . $creance->getTotalRestant() . ", " . $newTotalRestant . ", " . $rest . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                                                                                // }else{
                                                                                //     $newTotalRestant = $checkIfAlreadyExist2["new_total_restant"] - $rest;
                                                                                //     $TotalRestant = $checkIfAlreadyExist2["new_total_restant"] ;
                                                                                //     $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                                                                                //     (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                                                                                //     VALUES (" . $creance->getId() . ", " . $TotalRestant . ", " . $newTotalRestant . ", " . $rest . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                                                                                // }
                                                                                $total=($creance->getTotalRestant()-$rest);
                                                                                $creance->setTotalRestant($total);
                                                                                $this->em->flush();
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                                $sql="insert into accord_import (`type`, `id_details_accord_id`, `id_import_id`) values(1,".$autreDetail["id"].",".$id_import.")";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                                $sql="insert into debt_force_integration.paiement_import_dbi ( `id_import`, `id_creance`, `id_paiement`) values(".$id_import.",".$creance->getId().",".$maxPaiement.")";
                                                                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                                $rest=0;
                                                                            }
                                                                        }
                                                                    }
                                                                    else
                                                                    {//TODO:Non Validé
                                                                        $solde = $paiementRepo->getRestantCreance($creance->getId());
                                                                        if($solde > $rest)
                                                                        { 
                                                                            $sql="insert into accord (`id_type_paiement_id`, `date_premier_paiement`, `montant`, `frequence`, `nbr_echeanciers`, `etat`, `date_creation`, `date_fin_paiement`,`id_users_id`) values(".$typePaiement->getId().",'".$datePaiement."',".$rest.",1,1,1,sysdate(),'".$datePaiement."','".$id_user."')";
                                                                            $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                            $sql = "SELECT MAX(id) FROM accord";
                                                                            $maxAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                            $sql="insert into creance_accord (`id_creance_id`, `id_accord_id`) values(".$creance->getId().",".$maxAccord.")";
                                                                            $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                            $sql = "SELECT MAX(id) FROM creance_accord";
                                                                            $maxCreanceAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                            $sql="insert into details_accord (`id_accord_id`, `montant`, `status`, `id_type_paiement_id`, `montant_paiement`, `montant_restant`, `date_paiement`) values(".$maxAccord.",".$rest.",1,".$typePaiement->getId().",".$rest.",0,'".$datePaiement."')";
                                                                            $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                            $nbrAccordsSys++;
                                                                            $montantAccordsSys+=$rest;
                                                                        }
                                                                        else
                                                                        {
                                                                            $sql="insert into accord (`id_type_paiement_id`, `date_premier_paiement`, `montant`, `frequence`, `nbr_echeanciers`, `etat`, `date_creation`, `date_fin_paiement`) values(".$typePaiement->getId().",'".$datePaiement."',".$solde.",1,1,1,sysdate(),'".$datePaiement."')";
                                                                            $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                            $sql = "SELECT MAX(id) FROM debt_force_integration.accord_dbi";
                                                                            $maxAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                            $sql="insert into creance_accord (`id_creance_id`, `id_accord_id`) values(".$creance->getId().",".$maxAccord.")";
                                                                            $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                            $sql = "SELECT MAX(id) FROM creance_accord";
                                                                            $maxCreanceAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                            $sql="insert into details_accord (`id_accord_id`, `montant`, `status`, `id_type_paiement_id`, `montant_paiement`, `montant_restant`, `date_paiement`) values(".$maxAccord.",".$solde.",1,".$typePaiement->getId().",".$rest.",0,'".$datePaiement."')";
                                                                            $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                            $nbrAccordsSys++;
                                                                            $montantAccordsSys+=$solde;
                                                                        }
                                                                        $sql = "SELECT MAX(id) FROM details_accord";
                                                                        $maxDetailAccord = $this->conn->executeQuery($sql)->fetchOne();
                                                                        $date=new \DateTime();
                                                                        $dmy = $date->format('dmYHis');
                                                                        $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $creance->getId(), "id_type" => 3]);
                                                                        if (!$typeDeb) {
                                                                            $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $creance->getId()]);
                                                                        }
                                                                        $idDeb =$typeDeb->getIdDebiteur()->getId();
                                                                        $ref = "PA" . $dmy . ($typeDeb ? $typeDeb->getId() : "");
                                                                        $sql="insert into paiement (`id_creance_id`, `id_type_paiement_id`, `ref`, `montant`, `date_creation`, `date_paiement`, `etat`, `id_users_id`, `id_debiteur_id`, `id_ptf_id`, `id_details_accord_id`, `commentaire`, `id_import_id`, `confirmed`) values(".$creance->getId().",".$typePaiement->getId().",'".$ref."',".$montant.",sysdate(),'".$datePaiement."',0,".$id_user.",".$idDeb.",".$creance->getIdPtf()->getId().",".$maxDetailAccord.",'".$commentaire."',".$detailsImp->getId().",1)";
                                                                        $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                        
                                                                        $sql = "SELECT MAX(id) FROM paiement";
                                                                        $maxPaiement = $this->conn->executeQuery($sql)->fetchOne();
                                                                        $sql="insert into paiement_accord (`id_paiement_id`,`id_details_accord_id`) values(".$maxPaiement.",".$maxDetailAccord.")";
                                                                        $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                        // $checkIfAlreadyExist = $paiementRepo->checkIfUpdatedCreance($id_creance , $id_action);
                                                                        // if(!$checkIfAlreadyExist){
                                                                        //     $newTotalRestant = $creance->getTotalRestant() - $montant;
                                                                        //     $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                                                                        //     (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                                                                        //     VALUES (" . $creance->getId() . ", " . $creance->getTotalRestant() . ", " . $newTotalRestant . ", " . $montant . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                                                                        // }else{
                                                                        //     $newTotalRestant = $checkIfAlreadyExist["new_total_restant"] - $montant;
                                                                        //     $TotalRestant = $checkIfAlreadyExist["new_total_restant"] ;
                                                                        //     $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                                                                        //     (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                                                                        //     VALUES (" . $creance->getId() . ", " . $TotalRestant . ", " . $newTotalRestant . ", " . $montant . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                                                                        // }
                                                                        // $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                        $total=($creance->getTotalRestant()-$rest);
                                                                        $creance->setTotalRestant($total);
                                                                        $this->em->flush();
                                                                        $sql="insert into accord_import (`type`, `id_accord_id`, `id_details_accord_id`, `id_import_id`, `id_creance_accord_id`) values(1,".$maxAccord.",".$maxDetailAccord.",".$id_import.",".$maxCreanceAccord.")";
                                                                        $stmt = $this->conn->prepare($sql)->executeQuery();

                                                                        $sql="insert into paiement_import ( `id_import`, `id_creance`, `id_paiement`) values(".$id_import.",".$creance->getId().",".$maxPaiement.")";
                                                                        $stmt = $this->conn->prepare($sql)->executeQuery();
                                                                        $rest=0;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                            
                                    }
                                    insertError:
                                    if($motif!="")
                                    {
                                        
                                    }
                                }
                            }
                        }
                    }

                }
            }
    
        
        // }catch(\Exception $e){
        //     $codeStatut="ERROR";
        //     $respObjects["err"] = $e->getMessage();
        // }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/findIntgeration')]
    public function getIntegration(paiementRepo $paiementRepo ,Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $id = $request->get("id");
            $data = $paiementRepo->getOneIntegration($id);
            $codeStatut = "OK";
            $respObjects["data"]["integration"] = $data;
        }catch(\Exception $e){
            $respObjects["err"] = $e->getMessage();
            $codeStatut = "ERREUR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getAllIntegration')]
    public function getAllIntegration(paiementRepo $paiementRepo ,Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $id = $request->get("id");
            $data = $paiementRepo->getAllInegration();
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
    #[Route('/annulerIntgeration', methods: ['POST'])]
    public function changeStatus(paiementRepo $paiementRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $status = $request->get("status");
            $id = $request->get("id");
            if($status == "14"){
                $codeStatut = $this->paiementRepo->updateStatus($status, $id,[1,3], $paiementRepo);
            }
            if($status == "5"){
                $codeStatut = $this->paiementRepo->updateStatus($status, $id,[4], $paiementRepo);
            }
            if($status == "9"){
                $codeStatut = $this->paiementRepo->updateStatus($status, $id,[8], $paiementRepo);
            }if($status == "10"){
                $codeStatut = $this->paiementRepo->updateStatus($status, $id,[9 , 7], $paiementRepo);
            }if($status == "12"){
                $codeStatut = $this->paiementRepo->updateStatus($status, $id,[4,5], $paiementRepo);
            }
        } catch (\Exception $e) {
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }
}
