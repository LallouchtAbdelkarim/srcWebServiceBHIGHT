<?php

namespace App\Controller\IntegrationExtraction\Extraction;

use App\Entity\HistoriqueDemandeCadrage;
use App\Repository\IntegrationExtraction\Extraction\extractionRepo;
use App\Service\AuthService;
use App\Service\FileService;
use App\Service\GeneralService;
use App\Service\MessageService;
use App\Service\typeService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Flex\Response;
use ZipArchive;

#[Route('/API/extraction')]

class ExtractionController extends AbstractController
{
    private  $extractionRepo;
    private  $donneurRepo;
    private  $affichageRepo;
    public  $em;
    private $conn;
    public $AuthService;
    public $MessageService;
    public $typeService;
    public function __construct(
        
        EntityManagerInterface $em,
        MessageService $MessageService,
        FileService $FileService,
        Connection $conn,
        AuthService $AuthService,
        GeneralService $generalService,
        extractionRepo $extractionRepo,
        typeService $typeService,
        )
    {
        $this->conn = $conn;
        $this->AuthService = $AuthService;
        $this->em = $em;
        $this->MessageService = $MessageService;
        $this->FileService = $FileService;
        $this->generalService = $generalService;
        $this->extractionRepo = $extractionRepo;
        $this->typeService = $typeService;
    
    }
    #[Route('/exportCadrage/{typeCreance}/{typeCadrage}/{ptf}/{maxTotal}/{minTotal}/{maxRestant}/{minRestant}')]
    public function exportCadrage(Request $request , $typeCreance , $typeCadrage , $ptf , $maxTotal , $minTotal , $maxRestant , $minRestant)
    {
        $codeStatut= "ERROR";
        // try{

            $data_list = json_decode($request->getContent(), true);
            
            $data = $this->extractionRepo->getDataExtraction($typeCreance ,$ptf, $maxTotal , $minTotal ,$maxRestant , $minRestant );
            $typeCadrage = explode(",", $typeCadrage);
            foreach ($typeCadrage as $type)
            {
                $this->extractionRepo->addHistoDemandeCadrage(count($data) , $type ,$ptf );
            }
            $response = new StreamedResponse();
            $response->setCallback(function() use ($data) {
                $handle = fopen('php://output', 'w+');
                $tableTypes=array();
                $tableTypes[0]=utf8_decode('Numéro Pièce Identité');
                $tableTypes[1]=utf8_decode('Nom & prénom');

                fputcsv($handle, $tableTypes,";");
                foreach ($data as $debiteur)
                {
                    fputcsv(
                        $handle,
                        ["\t" . $debiteur['cin'] . "",utf8_decode($debiteur['nom'])." ".utf8_decode($debiteur['prenom'])],';'
                    );
                } 
                fclose($handle);
            }); 
            $filename = 'Demande_cadrage_'.date("d-m-y_H:i").'.csv' ;

            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
            $response->headers->set('Content-Disposition','attachment; filename="'.$filename.'"');

            return $response;
        // }catch(\Exception $e){
        //     $codeStatut="ERROR";
        //     $respObjects["err"] = $e->getMessage();
        // }
        // $respObjects["codeStatut"]=$codeStatut;
        // $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        // return $this->json($respObjects );
    }

    #[Route('/getHistoriqueCadrage')]
    public function getHistoriqueCadrage(Request $request )
    {
        $codeStatut= "ERROR";
        $respObjects =array();
        try{

            $this->AuthService->checkAuth(0,$request);
            $data = $this->extractionRepo->getHistoriqueCadrage();
            $codeStatut="OK";
            $respObjects["data"] = $data;

        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"]=$codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }

    #[Route('/getDataForImport')]
    public function importCadrage(Request $request )
    {
        $codeStatut= "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $table=array();
            $tableObj=array();
            $j=0;
            
            $demandesCadrages=$this->extractionRepo->getHistoriqueCadrage();

            $typesAdresses=$this->typeService->getListeType("adresse");
            for ($a = 0 ; $a < count($typesAdresses) ; $a++)
            {
                $tableObj["table"][$j]="Adresse";
                $tableObj["obj"][$j]=$typesAdresses[$a]->getType();
                $j++;
            }

            $typesTel=$this->typeService->getListeType("telephone");
            for ($a = 0 ; $a < count($typesTel) ; $a++)
            {
                $tableObj["table"][$j]="Tel";
                $tableObj["obj"][$j]=$typesTel[$a]->getType();
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
            $table[2]="Banque";
            $table[3]="Cnss";
            $table[4]="Titre foncier";

            $respObjects['columns'] = $table;
            $respObjects['col2'] = $tableObj;
            $respObjects['demandesCadrages'] = $demandesCadrages;

        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"]=$codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }
    #[Route('/getAllModelExport')]
    public function getAllModelExport(Request $request , extractionRepo $extractionRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            
            $this->AuthService->checkAuth(0,$request);
            $type = $request->get('type');
            $model_export = $extractionRepo->getAllModelExport($type);
            $objet['model_export'] = $model_export;
            $respObjects["data"] = $objet;
            $codeStatut="OK";

        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/addModelExport' , methods:['POST'])]
    public function addModelExport(Request $request , extractionRepo $extractionRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $titre = $data['titre'] ;
            $entities = $data['data'] ;
            $selectedColumns = $data['selectedColumns'];
            if(!empty($titre) && count($entities) > 0){
                $model = $extractionRepo->saveModelExport($titre , $entities);
                $extractionRepo->saveColumnEntity($entities  , $selectedColumns , $model);
                $codeStatut="OK";
            }else{
                $codeStatut="EMPTY-DATA";
            }

        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getModelExport/{id}')]
    public function getModelExport($id , Request $request , extractionRepo $extractionRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $respObjects['data'] = $extractionRepo->getModelExport($id);
            $respObjects['columns'] = $extractionRepo->getColumnModelExport($id);
        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/updateModelExport/{id}')]
    public function updateModelExport($id , Request $request , extractionRepo $extractionRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $titre = $data['titre'] ;
            $entities = $data['data'] ;
            $selectedColumns = $data['selectedColumns'];
            if(!empty($titre) && count($entities) > 0){
                $model = $extractionRepo->getModelExport($id);
                if($model){
                    $extractionRepo->saveModelExport($titre , $entities , $model);

                    $columns = $extractionRepo->getColumnModelExport($id);
                    foreach ($columns as $value) {
                        $extractionRepo->deleteColumnModelExport($value);   
                    }
                    $extractionRepo->saveColumnEntity($entities  , $selectedColumns , $model);

                    $codeStatut="OK";
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="EMPTY-DATA";
            }

        }catch(\Exception $e){
            $codeStatut = "ERREUR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/deleteModelExport/{id}')]
    public function deleteModelExport($id , Request $request , extractionRepo $extractionRepo ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $model = $extractionRepo->getModelExport($id);
            if($model){
                $columns = $extractionRepo->getColumnModelExport($id);
                foreach ($columns as $value) {
                    $extractionRepo->deleteColumnModelExport($value);   
                }
                $extractionRepo->deleteModelExport($model);
                $codeStatut="OK";
            }else{
                $codeStatut="NOT_EXIST_ELEMENT";
            }
        }catch(\Exception $e){
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    
    #[Route('/exportCadrageModel/{idSegmentation}/{id}')]
    public function exportLogImport(extractionRepo $extractionRepo, $idSegmentation, $id, ManagerRegistry $doctrine, SerializerInterface $serializer, Request $request)
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

        $model = $extractionRepo->getModelExport($id);
        $entities = $model->getEntities();

        foreach ($entities as $entity) {
            $type = $entity;
            $tableName = strtolower($entity);

            $data = $extractionRepo->getDataBySegment($tableName, $id, $idSegmentation);

            // Filter out the 'vide_champ' column
            $dataWithoutVideChamp = $data;

            // Create a CSV file for this import
            $csvFileName = $type . "_" . $id . '.csv';
            $csvFilePath = $tempDir . '/' . $csvFileName;
            $csvFile = fopen($csvFilePath, 'w', false, stream_context_create(['ftp' => ['encoding' => 'UTF-8']]));

            fwrite($csvFile, "\xEF\xBB\xBF"); // UTF-8 BOM for proper encoding

            if (!empty($dataWithoutVideChamp)) {
                // Write CSV headers if data is available
                fputcsv($csvFile, array_keys($dataWithoutVideChamp[0]), ';');

                // Write CSV data
                foreach ($dataWithoutVideChamp as $row) {
                    fputcsv($csvFile, $row, ';');
                }
            } else {
                // If no data, write only headers
                $columns = $extractionRepo->getColumnModel($id, ucfirst($tableName));
                $arrayHeader = array_map(function ($column) {
                    return $column->getColumnName();
                }, $columns);

                fputcsv($csvFile, $arrayHeader, ';');
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
            'extraction_log_' . $id . '_' . $idSegmentation . '.zip'
        ));

        return $response;
    }

}
