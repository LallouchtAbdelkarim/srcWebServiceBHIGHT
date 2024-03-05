<?php

namespace App\Service;

use App\Entity\Champs;
use App\Entity\ContactDonneurOrdre;
use App\Entity\DetailModelAffichage;
use App\Entity\DonneurOrdre;
use App\Entity\ModelAffichage;
use App\Service\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;


class DonneurOrdreService
{

    private $connection;
    private $validator;

    public function __construct(Connection $connection,  ValidationService $validator)
    {
        $this->connection = $connection;
        $this->validator = $validator;
    }

    #[Route('/donneur-ordre/ajout')]
    public function addDonneurOrdre(EntityManagerInterface $entityManager, Request $request, ValidationService $validator): Response
    {
        $inputs = $entityManager->getRepository(DetailModelAffichage::class)->findBy(['table_name' => 'donneur_ordre']);
        $response = "";
        $nom = "";
        $metier = "";
        $rs = "";
        $num_rc = "";
        $c_postale = "";
        $compte_bancaire = "";
        $dateCreation = date("d/m/Y");
        $dateDebut = "";
        $dateFin = "";

        if ($request->getMethod() == "POST") {


            
                    $do = $entityManager->getRepository(DonneurOrdre::class)->findBy(array("nom" => $nom, "numero_rc" => $num_rc));
                    if ($do) {

                        return new JsonResponse([
                            'response' => "Donneur d'ordre déja existe !",
                        ], Response::HTTP_IM_USED);
                    } else {
                        $data = json_decode($request->getContent(), true);
                        if (
                            $data['input'] == null || $data['contact'] == null || $data['metier'] == null || $data['nom'] == null || $data['rs'] == null ||
                            $data['num_rc'] == null || $data['c_postal'] == null || $data['compte_bancaire'] == null || $data['dateDebut'] == null || $data['dateFin'] == null
                        ) {
                            return new JsonResponse([
                                'response' => "Un des champs est vide !",
                            ], Response::HTTP_BAD_REQUEST);
                        } else {
                            print_r($data);
                            // $donneur_ordre = new DonneurOrdre();

                            // $donneur_ordre->setNom($nom);
                            // $donneur_ordre->setMetier($metier);
                            // $donneur_ordre->setRaisonSociale($num_rc);
                            // $donneur_ordre->setNumeroRc($num_rc);
                            // $donneur_ordre->setCp($c_postale);
                            // $donneur_ordre->setCompteBancaire($compte_bancaire);
                            // $donneur_ordre->setDateCreation(new \DateTime());
                            // $donneur_ordre->setDateDebut(new \DateTime());
                            // $donneur_ordre->setDateFin(new \DateTime());
                            // $entityManager->persist($donneur_ordre);
                            // if ($inputs) {
                            //     if ($_POST['input']) {
                            //         foreach ($_POST['input'] as $key => $value) {
                            //             $input = $entityManager->getRepository(DetailModelAffichage::class)->findOneBy(['id' => $key]);

                            //             $champs  = new Champs();
                            //             $champs->setColumName($input->getChampName());
                            //             $champs->setValue($value);
                            //             $champs->setTableName("donneur_ordre");
                            //             $champs->setChampsId($input);
                            //             $champs->setForm($donneur_ordre->getId());
                            //             $entityManager->persist($champs);
                            //         }
                            //     }
                            // }
                            // if ($request->get("contact")) {
                            //     foreach ($_POST['contact'] as $contactData) {
                            //         $contact = new ContactDonneurOrdre();
                            //         $contact->setIdDonneurOrdre($donneur_ordre);
                            //         $contact->setNom($contactData['nom']);
                            //         $contact->setPrenom($contactData['prenom']);
                            //         $contact->setAdresse($contactData['adresse']);
                            //         $contact->setEmail($contactData['email']);
                            //         $contact->setPoste($contactData['poste']);
                            //         $contact->setTel($contactData['tel']);
                            //         $contact->setMobile($contactData['mobile']);
                            //         $entityManager->persist($contact);
                            //     }
                            // }
                            // $entityManager->flush();
                            // $response = "OK";
                        }

                        // dd($_REQUEST);


                        // return $this->redirect('/donneur-ordre/ajout-contact/'.$donneur_ordre->getId());


                    }
                }

        
        return new JsonResponse($response);
    }
}
