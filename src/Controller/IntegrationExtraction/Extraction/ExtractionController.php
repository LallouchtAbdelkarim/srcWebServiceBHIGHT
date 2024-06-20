<?php

namespace App\Controller\IntegrationExtraction\Extraction;

use App\Entity\HistoriqueDemandeCadrage;
use App\Repository\IntegrationExtraction\Extraction\extractionRepo;
use App\Service\AuthService;
use App\Service\FileService;
use App\Service\GeneralService;
use App\Service\MessageService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Flex\Response;

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
    public function __construct(
        
        EntityManagerInterface $em,
        MessageService $MessageService,
        FileService $FileService,
        Connection $conn,
        AuthService $AuthService,
        GeneralService $generalService,
        extractionRepo $extractionRepo
        )
    {
        $this->conn = $conn;
        $this->AuthService = $AuthService;
        $this->em = $em;
        $this->MessageService = $MessageService;
        $this->FileService = $FileService;
        $this->generalService = $generalService;
        $this->extractionRepo = $extractionRepo;
    
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
        try{

            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
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
}
