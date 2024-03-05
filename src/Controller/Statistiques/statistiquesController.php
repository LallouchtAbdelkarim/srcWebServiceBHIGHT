<?php

namespace App\Controller\Statistiques;

use Proxies\__CG__\App\Entity\FileMissions;
use Proxies\__CG__\App\Entity\TypeMissions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Proxies\__CG__\App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\AuthService;
use App\Service\MessageService;
use App\Service\typeService;
use App\Repository\Statistiques\statistiquesRepo;
use App\Repository\Users\userRepo;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FileService;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/API/statistiques')]
class statistiquesController extends AbstractController
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
        statistiquesRepo $statistiquesRepo,
        userRepo $userRepo,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        MessageService $MessageService,
        Connection $conn,
        AuthService $AuthService,
        TypeService $TypeService,
        FileService $FileService,
        )
    {
        $this->conn = $conn;
        $this->statistiquesRepo = $statistiquesRepo;
        $this->userRepo = $userRepo;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->FileService = $FileService;
        $this->MessageService = $MessageService;
        $this->AuthService = $AuthService;
        $this->TypeService = $TypeService;
    }
    #[Route('/statistiquesMoinsUn')]
    public function statistiquesMoinsUn(statistiquesRepo $statistiquesRepo ,Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $total_creance = $this->statistiquesRepo->getTotalCreance();
            // $creance_non_success = $this->statistiquesRepo->getTotalCreanceNonSuccess();
            $liste_agent =$this->statistiquesRepo->getUsers();
            for ($i=0; $i <=$liste_agent ; $i++) { 
                $param = [
                    "creance_non_success"=>"",
                    "creance_success"=>"",
                    "nbr_process_groupe"=>"",
                    "nbr_process_by_user"=>"",
                    "nbr_paiement"=>"",
                    "nbr_actions"=>"",
                    "nbr_accord_terminee"=>"",
                    "nbr_accord_echeance"=>"",
                    "nbr_total_creance"=>$total_creance,
                    "id_users"=>$liste_agent[$i]["id"]
                ];
                $this->statistiquesRepo->saveStatistiques($param);
            }
            $codeStatut= "OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
}
