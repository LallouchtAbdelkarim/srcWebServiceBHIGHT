<?php

namespace App\Controller\Dashboard;

use App\Repository\Dashboard\dashboardRepo;
use App\Service\AuthService;
use App\Service\MessageService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/API/dashboardUser')]

class UserDashboardController extends AbstractController
{
    private  $dashboardRepo;
    private $AuthService;
    private $MessageService;
    public function __construct(dashboardRepo $dashboardRepo,AuthService $AuthService , MessageService $MessageService)
    {
        $this->dashboardRepo = $dashboardRepo;
        $this->AuthService = $AuthService;
        $this->MessageService = $MessageService;
    }

    #[Route('/getData')]
    public function index(Request $request): JsonResponse
    {
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);

            $idUser = $this->AuthService->returnUserId($request);
    
            $nbrCreance = $this->dashboardRepo->getNbrCreances();
            $totalCreance = $this->dashboardRepo->getTotalCreance();
            $totalRestant = $this->dashboardRepo->getTotalRestant();

            $processOfUser = $this->dashboardRepo->getProcessOfUser($idUser);

            $nbrProcess = $this->dashboardRepo->getCountProcess($idUser);
            $nbrProcessByDepartement = $this->dashboardRepo->getCountProcessByDepartement($idUser);

            $data['nbrCreance']=$nbrCreance;
            $data['totalCreance']=$totalCreance;
            $data['totalRestant']=$totalRestant;
            $data['processOfUser']=$processOfUser;
            $data['nbrProcess']=$nbrProcess;
            $data['nbrProcessByDepartement']=$nbrProcessByDepartement;

            $respObjects["data"] = $data;
            $codeStatut="OK";

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        $respObjects["err"] = $e->getMessage();
        }


        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
}
