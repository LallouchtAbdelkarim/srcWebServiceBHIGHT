<?php

namespace App\Controller;


use App\Entity\CritereModelFacturation;
use App\Entity\RegleModelFacturation;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ModelFacturation;
use Doctrine\DBAL\Connection;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\MessageService;


#[Route('/API/general')]

class GeneralController extends AbstractController
{
    private $conn;
    public $em;
    private $MessageService;
    private $AuthService;

    public function __construct(private JWTEncoderInterface $JWTManager ,AuthService $AuthService    , Connection $conn,MessageService $MessageService, EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
        $this->MessageService = $MessageService;
        $this->AuthService = $AuthService;
    }
    #[Route('/')]
    public function index(Request $request): JsonResponse
    {
        $codeStatut = "ERROR";
        try{
            $codeStatut = "OK";
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/get_list_table', name: 'app_general')]
    public function getListTable(Request $request): JsonResponse
    {
        $codeStatut = "ERROR";
        $isConnected = false;
        $this->AuthService->checkAuth(0,$request);
        $data = [];
        try{
            $data = ['creance' ,"accord","dossier"]; 
            $codeStatut = "OK";
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["data"] = $data;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/get_column')]
    public function getListColumn(Request $request): JsonResponse
    {
        $codeStatut = "ERROR";
        $table = $request->get("table");
        $data = [];
        if($table != ""){
            $sql="SHOW COLUMNS FROM ".$table. " WHERE `Key` != 'MUL'  and Field != 'id' and Field != 'cin_formate' ";
            $sql .= "and Field != 'id_integration'  and Field != 'id_creance' and Field != 'etat' and Field != 'id_creance_dbi'";
            $sql .= "and Field != 'motif' ";
            $sql .= "and Field != 'id_dossier_dbi' ";
            $sql .= "and Field != 'ref' and Field != 'confirmed' and Field != 'commentaire' ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            // returns an array of arrays (i.e. a raw data set)
            $data = $stmt->fetchAllAssociative();
            $codeStatut="OK";
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["data"] = $data;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
}
