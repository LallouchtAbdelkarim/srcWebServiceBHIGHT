<?php

namespace App\Controller\DonneurAndPTFController\PorteFeuille;

use App\Entity\Champs;
use App\Entity\DetailModelAffichage;
use App\Entity\DetailsSecteurActivite;
use App\Entity\Portefeuille;
use App\Entity\SecteurActivite;
use App\Service\AuthService;
use App\Service\ValidationService;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\DonneurOrdre;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\DonneurOrdreAndPTF\donneurRepo;

#[Route('/API')]

class PorteFeuilleController extends AbstractController
{
    private $connection;
    private $MessageService;
    private $AuthService;

    private $donneurRepo;
    private $validator;
    public function __construct(Connection $connection, 
    MessageService $MessageService,
    ValidationService $validator,
    donneurRepo $donneurRepo,
    AuthService $AuthService,
    )
    {
        $this->AuthService = $AuthService;
        $this->MessageService = $MessageService;
        $this->connection = $connection;
        $this->validator = $validator;
        $this->donneurRepo = $donneurRepo;
    }

    #[Route('/porte-feuille/ajout', methods: ['POST'])]
    public function addDonneurOrdre(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, donneurRepo $donneurRepo): JsonResponse
    {
        $respObjects = array();
        $codeStatut = "ERROR";
        // try {
            $donneurOrdre = $entityManager->getRepository(DonneurOrdre::class)->findAll();
            $inputs = $entityManager->getRepository(DetailModelAffichage::class)->findBy(['table_name' => 'portefeuille']);
            $data = json_decode($request->getContent(), true);
            $donneurOrdre = $entityManager->getRepository(DonneurOrdre::class)->findOneBy(array("id" => (int)$data['dn']));
            if (!$donneurOrdre) {
                $codeStatut = "NOT_EXIST";
            } else {
                if ($data['dn'] == null || $data['titre'] == null || $data['numPtf'] == null || $data['dureeGestion'] == null  ||
                $data['dateDebutGestion'] == null || $data['dateFinGestion'] == null || $data['typeMission'] == null || $data['typeCreance'] == null
                ) {
                    $codeStatut="ERROR-EMPTY-PARAMS";
                } else {
                    if(is_numeric($data['dureeGestion'])){
                        if($data['dateFinGestion'] > $data['dateDebutGestion']){
                            $portefeuille = $donneurRepo->createPortefeuille($data);
                            if ($portefeuille) {
                                if($data['input']){
                                    $champs = $donneurRepo->AddChampsPortefeuille($data['input'], $portefeuille->getId());
                                }
                            } else {
                                $codeStatut = "ERROR";
                            }
                            $codeStatut="OK";
                            $entityManager->flush();
                        }else{
                            $codeStatut="ERROR_DATE";
                        }
                    }else{
                        $codeStatut = "ERROR_DURRE";
                    }
                }
            }
        // }catch(\Exception $e){
        //     $codeStatut = "ERROR";
        // $respObjects["err"] = $e->getMessage();
        // }
        
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/porte-feuille/modifier/{id}', methods: ['PUT'])]
    public function updateDonneurOrdre(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, $id, donneurRepo $donneurRepo): Response
    {
        $respObjects = [];

        $ptf = $entityManager->getRepository(PorteFeuille::class)->findOneBy(["id" => $id]);
        $donneurOrdre = $entityManager->getRepository(DonneurOrdre::class)->findAll();
        $do = $entityManager->getRepository(Champs::class)->findBy(["form" => $id]);
        $typee = $entityManager->getRepository(DetailModelAffichage::class)->findBy(['table_name' => "portefeuille"]);
        $type = $entityManager->getRepository(Champs::class)->findBy(['champs' => $typee]);

        $data = json_decode($request->getContent(), true);

            if (!$ptf) {
                $respObjects["message"] = "Une erreur s'est produite !";
                $respObjects["codeStatut"] = "NOT OK";
            } else {
                if (
                    $data['input'] == null || $data['dn'] == null || $data['titre'] == null || $data['numPtf'] == null || $data['dureeGestion'] == null ||
                    $data['dateDebutGestion'] == null || $data['dateFinGestion'] == null || $data['typeMission'] == null || $data['typeCreance'] == null
                ) {
                    $respObjects["message"] = "Un des champs est vide !";
                    $respObjects["codeStatut"] = "Not OK";
                } else {
                    $donneurOrdreExist = $entityManager->getRepository(DonneurOrdre::class)->findOneBy(["id" => (int)$data['dn']]);
                    if (!$donneurOrdreExist) {
                        $respObjects["message"] = "Erreur d'un Donneur d'ordre !";
                        $respObjects["codeStatut"] = "NOT OK";
                    } else {

                        $sql = "SELECT * from portefeuille s WHERE s.titre = '" . $data['titre'] . "' AND  s.numero_ptf = '" . $data['numPtf'] . "' AND s.id != '" . $id . "'";
                        $stmt = $this->connection->prepare($sql)->executeQuery();
                        $PorteFeuille = $stmt->fetchAllAssociative();
                        if ($PorteFeuille) {
                            $response = "Portefeuille déja existe !";
                        } else {
                            if (new \DateTime($data['dateDebutGestion']) > new \DateTime($data['dateFinGestion'])) {
                                $respObjects["message"] = "Erreur dans insert date !";
                                $respObjects["codeStatut"] = "NOT OK";
                            } else {

                                // $ptf->setTitre($data['titre']);
                                // $ptf->setActif(0);
                                // $ptf->setNumeroPtf($data['numPtf']);
                                // $ptf->setDureeGestion($data['dureeGestion']);
                                // $ptf->setDateDebutGestion(new \DateTime($data['dateDebutGestion']));
                                // $ptf->setDateFinGestion(new \DateTime($data['dateFinGestion']));
                                // $ptf->setTypeMission($data['typeMission']);
                                // $ptf->setTypeCreance($data['typeCreance']);
                                // $ptf->setIdDonneurOrdre($donneurOrdreExist);
                                // $entityManager->persist($ptf);
                                // foreach ($data['input'] as $key => $value) {
                                //     $input = $entityManager->getRepository(Champs::class)->findOneBy(["champs" => (int)$key, "form" => $id]);
                                //     $input->setValue($value);
                                //     $input->setForm($id);
                                //     $entityManager->persist($input);

                                // }
                                // $entityManager->flush();
                                // $respObjects["message"] = "Modifier avec success";
                                // $respObjects["codeStatut"] = "OK";
                                $portefeuille = $donneurRepo->UpdatePortefeuille($data, $id);
                                if ($portefeuille) {
                                    $champs = $donneurRepo->UpdateChampsPortefeuille($data['input'], $id);
                                    if (!$donneurRepo->UpdateChampsPortefeuille($data['input'], $id)) {
                                        $respObjects["message"] = "Modifier avec success";
                                        $respObjects["codeStatut"] = "OK";
                                    } else {
                                        $respObjects["message"] = "une erreur s'est produite !!!";
                                        $respObjects["codeStatut"] = "NOT OK";
                                    }
                                } else {
                                    $respObjects["message"] = "une erreur s'est produite !!";
                                    $respObjects["codeStatut"] = "NOT OK";
                                }

                            }
                        }
                    }
                }
            }
        

        return new JsonResponse($respObjects);
    }
    #[Route('/delete_porte_feuille/{id}', methods: ['DELETE'])]
    public function DeletePortefeuille(EntityManagerInterface $entityManager, Request $request, $id , donneurRepo $donneurRepo): Response
    {
        $respObjects = [];
        
        $donneur = $donneurRepo->DeletePortefeuille($id);
        if ($donneur) {
            $respObjects["message"] = "success";
            $respObjects["codeStatut"] = "OK";
        }else{
                $respObjects["message"] = "Une error s'est produite !";
            $respObjects["codeStatut"] = "NOT OK";
        }
        return new JsonResponse($respObjects);
    }

    #[Route('/getchampsP', methods: ['GET'])]
    public function GetChamps(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $response = [];
        $result = [];
        $champs = $entityManager->getRepository(DetailModelAffichage::class)->findBy(['table_name' => 'portefeuille']);

        foreach ($champs as $champ) {

            $response = [
                'id' => $champ->getId(),
                'champ_name' => $champ->getChampName(),
                'length' => $champ->getLength(),
                'type' => $champ->getTypeChamp(),
                'required' => $champ->isRequired()
            ];
            array_push($result, $response);
        }

        return new JsonResponse($result);
    }
    #[Route('/ListeSecteurActivity')]
    public function type_donneur(Request $request ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            // $this->AuthService->checkAuth(0,$request);
            $data = $entityManager->getRepository(DetailsSecteurActivite::class)->findAll();
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
    #[Route('/secteurActivity')]
    public function secteur_activity(Request $request ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            // $this->AuthService->checkAuth(0,$request);
            $data = $entityManager->getRepository(SecteurActivite::class)->findAll();
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
    #[Route('/porte-feuille/Activity')]
    public function activity(Request $request ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            // $this->AuthService->checkAuth(0,$request);
            $data = $entityManager->getRepository(DetailsSecteurActivite::class)->findBy(["id_secteur"=> $request->get("id")]);
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
    
    #[Route('/getptf', methods: ['GET'])]
    public function Getptf(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        
        $ptf = "SELECT * FROM `portefeuille` ";
        $stmt = $this->connection->prepare($ptf);
        $stmt = $stmt->executeQuery();
        $resulatPtf = $stmt->fetchAllAssociative();
           
        return new JsonResponse($resulatPtf);
    }
    #[Route('/get_liste_ptf', methods: ['GET'])]
    public function get_liste_ptf(donneurRepo $donneurRepo ,  EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $ptf = $donneurRepo->getListPtf();
            $respObjects["data"] = $ptf;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    #[Route('/getOneptf/{id}',methods: ['GET'],name: 'get_one_ptf')]
    public function GetOnePtf(EntityManagerInterface $entityManager, $id, Request $request,donneurRepo $donneurRepo): JsonResponse
    {
        $response = [];
        $result = [];

        $sqlptf = "SELECT * FROM `portefeuille` WHERE id =".$id;
        $stmtd = $this->connection->prepare($sqlptf);
        $stmtd = $stmtd->executeQuery();
        $donneur = $stmtd->fetchAllAssociative();

        $champs = "SELECT c.*, dc.*
        FROM champs c, detail_model_affichage dc
        WHERE dc.id = c.champs_id
        AND dc.table_name = 'portefeuille'
        AND c.form = ".$id;
        $stmt = $this->connection->prepare($champs);
        $stmt = $stmt->executeQuery();
        $resulatChamps = $stmt->fetchAllAssociative();

            $result = [
                'champs'=>$resulatChamps,
                'ptf'=>$donneur
            ];
           
        return new JsonResponse($result);
    }
    #[Route('/getAllSecteurActivite')]
    public function liste_dossiers(Request $request,donneurRepo $donneurRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            // $this->AuthService->checkAuth(0,$request);
            $data = $donneurRepo->getAllSecteurActivite();
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
}
