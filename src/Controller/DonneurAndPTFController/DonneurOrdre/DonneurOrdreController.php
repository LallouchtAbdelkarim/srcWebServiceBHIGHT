<?php

namespace App\Controller\DonneurAndPTFController\DonneurOrdre;

use App\Entity\Champs;
use App\Entity\ContactDonneurOrdre;
use App\Entity\ContactHistorique;
use App\Entity\DetailModelAffichage;
use App\Entity\DonneurOrdre;
use App\Entity\ModelAffichage;
use App\Entity\TypeDonneur;
use App\Service\AuthService;

use App\Service\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use App\Service\MessageService;

use App\Repository\DonneurOrdreAndPTF\donneurRepo;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/API')]

class DonneurOrdreController extends AbstractController
{
    private $connection;
    private $validator;
    private $donneurRepo;
    private $MessageService;

    private $AuthService;

    public function __construct(Connection $connection, MessageService $MessageService,         AuthService $AuthService
    ,   ValidationService $validator, donneurRepo $donneurRepo)
    {
        $this->connection = $connection;
        $this->validator = $validator;
        $this->donneurRepo = $donneurRepo;
        $this->MessageService = $MessageService;
        $this->AuthService = $AuthService;
    }

    #[Route('/donneur-ordre/ajout', methods: ['POST'])]
    public function addDonneurOrdre(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, donneurRepo $donneurRepo): Response
    {
        $inputs = $entityManager->getRepository(DetailModelAffichage::class)->findBy(['table_name' => 'donneur_ordre']);
        $respObjects = array();
        $codeStatut = "ERROR";
        try {
            $data = json_decode($request->getContent(), true);
            if(isset($data['nom']) && isset($data['num_rc'])){
                if (
                    !isset($data['metier']) || empty($data['metier']) ||
                    !isset($data['nom']) || empty($data['nom']) ||
                    !isset($data['rs']) || empty($data['rs']) ||
                    !isset($data['num_rc']) || empty($data['num_rc']) ||
                    !isset($data['c_postale']) || empty($data['c_postale']) ||
                    !isset($data['compte_bancaire']) || empty($data['compte_bancaire']) ||
                    !isset($data['dateDebut']) || empty($data['dateDebut']) ||
                    !isset($data['dateFin']) || empty($data['dateFin']) ||
                    !isset($data['id_type']) || empty($data['id_type'])
                ){
                    $codeStatut="ERROR-EMPTY-PARAMS";
                }
                 else {

                    $do = $entityManager->getRepository(DonneurOrdre::class)->findBy(array("numero_rc" => $data['num_rc']));

                    if ($do) {
                        $codeStatut="DONNEUR_DEJA_EXIST";
                    } else {

                        $donneur = $donneurRepo->createDonneurOrdre($data);
                        $historique = $data['listeHistorique'];
                        if(count($historique)>0)
                        {
                            for ($i=0; $i < count($historique); $i++) { 
                                $donneurRepo->createHistoriques($donneur , $historique[$i]['type']['value'], $historique[$i]['text']);
                            }
                        }

                        if ($donneur) {
                            $champs = $donneurRepo->AddChamps($data['input'], $donneur->getId());
                            if (isset($data['contact'])) {
                                $contact = $donneurRepo->AddContacts($data['contact'], $donneur);
                            }
                            if ($champs && $contact) {
                                $codeStatut="ERROR-EMPTY-PARAMS";
                            } else {
                                $codeStatut = "ERROR";
                            }
                        } else {
                            $codeStatut = "ERROR";
                        }
                        $codeStatut = "OK";
                        $entityManager->flush();
                    }
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        } catch (\Exception $e) {
            $codeStatut = "ERROR";
        $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);

        return $this->json($respObjects);
    }
    
    #[Route('/donneur-ordre/getTypeDonneur')]
    public function type_donneur(Request $request ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            // $this->AuthService->checkAuth(0,$request);
            $data = $entityManager->getRepository(TypeDonneur::class)->findAll();
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

    #[Route('/donneur-ordre/addHistoriqueContact/{id}', methods: ['POST'])]
    public function addHistoriqueContact(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, $id): Response
    {
        $find_dn = $entityManager->getRepository(DonneurOrdre::class)->findOneBy(["id" => $id]);
        if (!$find_dn) {
            $codeStatut = "NOT_EXIST_ELEMENT";
        }
        $data = json_decode($request->getContent(), true);
       

        if ($request->getMethod() == "POST") {
            if ($data['type'] == "" or $data['note'] == "") {
                $codeStatut="EMPTY-DATA";
            } else {
                $contact = new ContactHistorique();
                $contact->setIdDonneur($find_dn);
                $contact->setType($data['type']);
                $contact->setDateCreation(new \DateTime("now"));
                $contact->setNote($data['note']);
                $entityManager->persist($contact);
                $entityManager->flush();
                $codeStatut = "OK";
            }
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/donneur-ordre/ajout-contact/{id}', methods: ['POST'])]
    public function addContactDonneurOrdre(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, $id): Response
    {
        $find_dn = $entityManager->getRepository(DonneurOrdre::class)->findOneBy(["id" => $id]);
        if (!$find_dn) {
            $codeStatut = "NOT_EXIST_ELEMENT";
        }
        $data = json_decode($request->getContent(), true);
        $response = "";
        $nom = "";
        $prenom = "";
        $poste = "";
        $email = "";
        $tel = "";
        $fix = "";
        $adresse = "";

        if ($request->getMethod() == "POST") {
            if ($data['nom'] == "" or $data['prenom'] == "" or $data['poste'] == "" or $data['mobile'] == "" or $data['email'] == "") {
                $response = "Un des champs est vide !";
                $codeStatut="EMPTY-DATA";
            } else {
                $contact = new ContactDonneurOrdre();
                $contact->setNom($data['nom']);
                $contact->setPrenom($data['prenom']);
                $contact->setposte($data['poste']);
                $contact->setemail($data['email']);
                $contact->setMobile($data['fix']);
                $contact->setTel($data['mobile']);
                $contact->setAdresse($data['adresse']);
                $contact->setIdDonneurOrdre($find_dn);
                $entityManager->persist($contact);
                $entityManager->flush();
                $codeStatut = "OK";
            }
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/donneur-ordre/modifier/{id}', methods: ['PUT'])]
    public function updateDonneurOrdre(EntityManagerInterface $entityManager, Connection $connection, Request $request, ValidationService $validator, $id, donneurRepo $donneurRepo): Response
    {
        $respObjects = array();
        $donneur = $entityManager->getRepository(DonneurOrdre::class)->findOneBy(["id" => $id]);
        if (!$donneur) {
            $respObjects["message"] = "Donneur d'ordre Introuvable";
            $respObjects["codeStatut"] = "Not OK";
        }
        $contact = $entityManager->getRepository(ContactDonneurOrdre::class)->findBy(['id_donneurOrdre' => $id]);

        $data = json_decode($request->getContent(), true);
        if (
             $data['metier'] == null || $data['nom'] == null || $data['rs'] == null ||
            $data['num_rc'] == null || $data['c_postale'] == null || $data['compte_bancaire'] == null || $data['dateDebut'] == null || $data['dateFin'] == null
        ) {
            $respObjects["message"] = "Un des champs est vide !";
            $respObjects["codeStatut"] = "Not OK";
        } else {

            $donneur1 = $donneurRepo->UpdateDonneur($data, $id);
            if (isset($data['contact'])) {
                $donneurRepo->AddContacts($data['contact'], $donneur);
            }

            $respObjects["message"] = "Opération effectuée";
        $respObjects["codeStatut"] = "OK";
        $entityManager->flush();
            
        }

        return new JsonResponse($respObjects);
    }

    #[Route('/donneur-ordre/update-contact/{id}', methods: ['POST'])]
    public function updateContact(EntityManagerInterface $entityManager, Connection $connection, Request $request, ValidationService $validator, $id, donneurRepo $donneurRepo): Response
    {
        $respObjects = [];
        $contact = $entityManager->getRepository(ContactDonneurOrdre::class)->findOneBy(["id" => $id]);
        if (!$contact) {
            $respObjects["message"] = "Contact n'existe pas";
            $respObjects["codeStatut"] = "NOT OK";
        }


        $data = json_decode($request->getContent(), true);

        $nom = $data['nom'];
        $prenom = $data['prenom'];
        $poste = $data['poste'];
        $email = $data['email'];
        $tel = $data['tel'];

        $mobile = $data['mobile'];
        $adresse = $data['adresse'];


        $contraintes = array(
            "nom" => array("val" => $nom, "length" => 80, "type" => "string"),
            "prenom" => array("val" => $prenom, "length" => 80, "type" => "string"),
            "poste" => array("val" => $poste, "length" => 100, "type" => "string"),
            "email" => array("val" => $email, "length" => 80, "type" => "string"),
            "tel" => array("val" => $tel, "length" => 80, "type" => "string"),
            "adresse" => array("val" => $adresse, "length" => 80, "type" => "string"),
            "mobile" => array("val" => $mobile, "length" => 80, "type" => "int")
        );
        $valideForm = $this->validator->validateur($contraintes);

        if (!$valideForm) {
            $response = "Une erreur s'est produite !";
            $respObjects["message"] = "Une erreur s'est produite !";
            $respObjects["codeStatut"] = "NOT OK";
        } else {
            if ($nom && $prenom && $poste && $email && $tel) {
                $contact = $donneurRepo->UpdateContact($id, $nom, $prenom, $poste, $email, $tel, $mobile, $adresse);
                if ($contact) {
                    $respObjects["message"] = "Modifier avec success";
                    $respObjects["codeStatut"] = "OK";
                } else {
                    $respObjects["message"] = "une erreur s'est produite !";
                    $respObjects["codeStatut"] = "NOT OK";
                }
            } else {
                $respObjects["message"] = "Un des champs est vide !";
                $respObjects["codeStatut"] = "NOT OK";
            }
        }


        return new JsonResponse($respObjects);
    }

    #[Route('/delete_donneur_ordre/{id}', methods: ['DELETE'])]
    public function deleteDonneurOrdre(EntityManagerInterface $entityManager, Request $request, $id, donneurRepo $donneurRepo): Response
    {

        $respObjects = [];

        $donneur = $donneurRepo->DeleteDonneurOrdre($id);
        if ($donneur == 'OK') {
            $respObjects["message"] = "success";
            $respObjects["codeStatut"] = "OK";
        } 
        else if ($donneur)
        {
            $respObjects["message"] = $donneur;
            $respObjects["codeStatut"] = "NOT OK";
        }
        else {
            $respObjects["message"] = "Une error s'est produite !";
            $respObjects["codeStatut"] = "NOT OK";
        }

        return new JsonResponse($respObjects);
    }

    #[Route('/delete_contact/{id}', methods: ['DELETE'])]
    public function deleteContactDonneurOrdre(EntityManagerInterface $entityManager, Request $request, $id, donneurRepo $donneurRepo): Response
    {
        $respObjects = [];

        $id = $request->get("id");

        $contact = $donneurRepo->DeleteContact($id);
        if ($contact) {
            $respObjects["message"] = "Supprimmer avec success";
            $respObjects["codeStatut"] = "OK";
        } else {
            $respObjects["message"] = "une erreur s'est produite !";
            $respObjects["codeStatut"] = "NOT OK";
        }
        return new JsonResponse($respObjects);
    }

    // #[Route('/testvalidateur', methods: ['POST'])]
    // public function TestValidation(EntityManagerInterface $entityManager, Request $request, donneurRepo $donneurRepo):Response
    // {
    //     $response = array();
    //     // if ($request->getMethod() == "POST") {

    //     //     $message = $request->get('message');
    //     //     $number = $request->get('number');
    //     //     $date = $request->get('date');

    //     //     // $validemsg = $donneurRepo->ValidateChamps(1, $message);
    //     //     // if ($validemsg != "OK") {
    //     //     //     $response[] = "Une erreur s'est produite pour le message : " . $validemsg;
    //     //     // } else {
    //     //     //     $response[] = "Message valide";
    //     //     // }
    //     //     $validenum = $donneurRepo->ValidateChamps(2, $number);
    //     //     if ($validenum !="OK") {
    //     //         $response[] = "Une erreur s'est produite pour le number : " . $validenum;
    //     //     } else {
    //     //         $response[] = "Number valide";
    //     //     }

    //     //     // $validedate = $donneurRepo->ValidateChamps(4, $date);
    //     //     // if ($validedate !="OK") {
    //     //     //     $response[] = "Une erreur s'est produite pour la date : " . $validedate;
    //     //     // } else {
    //     //     //     $response[] = "Date valide";
    //     //     // }
    //     // }
    //     if ($request->getMethod() == "POST") {
    //         $message = $request->get('message');
    //         $number = $request->get('number');
    //         $date = $request->get('date');

    //         $validemsg = $donneurRepo->ValidateChamps(1, $message);

    //         if ($validemsg !== true) {
    //             $response[] = $validemsg;
    //         } else {
    //             $response[] = "Inputs are valid";
    //         }
    //             // dd($_REQUEST);
    //         $validenum = $donneurRepo->ValidateChamps(3, $number);

    //         if ($validenum !== true) {
    //             $response[] = $validenum;
    //         } else {
    //             $response[] = "Number is valid";
    //         }

    //         $validedate = $donneurRepo->ValidateChamps(4, $date);

    //         if ($validedate !== true) {
    //             $response[] = $validedate;
    //         } else {
    //             $response[] = "Date is valid";
    //         }
    //     }


    //     return new JsonResponse($response);
    // }
    #[Route('/testvalidateur', methods: ['POST'])]
    public function TestValidation(EntityManagerInterface $entityManager, Request $request, donneurRepo $donneurRepo): JsonResponse
    {
        $response = array();

        $message = $request->get('message');
        $number = $request->get('number');
        $date = $request->get('date');

        $validemsg = $donneurRepo->ValidateChamps(1, $message);
        if ($validemsg !== true) {
            $response[] = "Une erreur s'est produite pour le message : " . $validemsg;
        } else {
            $response[] = "Message valide";
        }

        $validenum = $donneurRepo->ValidateChamps(2, $number);

        if ($validenum === true) {
            $response[] = "Number valide";
        } else {
            $response[] = "Une erreur s'est produite pour le number : " . $validenum;
        }


        $validedate = $donneurRepo->ValidateChamps(4, $date);

        if ($validedate === true) {
            $response[] = "Date valide";
        } else {
            $response[] = $validedate;
        }

        return new JsonResponse($response);
    }

    #[Route('/getchamps', methods: ['GET'])]
    public function GetChamps(EntityManagerInterface $entityManager, Request $request, donneurRepo $donneurRepo): JsonResponse
    {
        $response = [];
        $result = [];
        $champs = $entityManager->getRepository(DetailModelAffichage::class)->findBy(['table_name' => 'donneur_ordre']);

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

    #[Route('/get_detail_model', methods: ['GET'])]
    public function GetDetailModel(EntityManagerInterface $entityManager, Request $request, donneurRepo $donneurRepo, SerializerInterface $serializer): JsonResponse
    {
        $response = [];
        $result = [];
        $champs = $entityManager->getRepository(DetailModelAffichage::class)->findAll();

        foreach ($champs as $champ) {

            $response = [
                'id' => $champ->getId(),
                'champ_name' => $champ->getChampName(),
                'length' => $champ->getLength(),
                'type' => $champ->getTypeChamp(),
                'required' => $champ->isRequired(),
                'table_name'=>$champ->getTableName(),
                'type_creance'=>$champ->getTypeCreance(),
                'etat'=>$champ->getEtat(),
            ];
            array_push($result, $response);
        }
        
        return new JsonResponse($result);
    }
    #[Route('/get_detail_model/{id}', methods: ['GET'])]
    public function GetDetailModelById(EntityManagerInterface $entityManager, $id,Request $request, donneurRepo $donneurRepo, SerializerInterface $serializer): JsonResponse
    {
        $response = [];
        $result = [];
        $champs = $entityManager->getRepository(DetailModelAffichage::class)->findBy(array('id_model_affichage' => $id));

        foreach ($champs as $champ) {

            $response = [
                'id' => $champ->getId(),
                'champ_name' => $champ->getChampName(),
                'length' => $champ->getLength(),
                'type' => $champ->getTypeChamp(),
                'required' => $champ->isRequired(),
                'table_name'=>$champ->getTableName(),
                'type_creance'=>$champ->getTypeCreance(),
                'etat'=>$champ->getEtat(),
            ];
            array_push($result, $response);
        }
        
        return new JsonResponse($result);
    }
   

    #[Route('/getdonneur', methods: ['GET'])]
    public function Getdonneur(EntityManagerInterface $entityManager, Request $request, donneurRepo $donneurRepo): JsonResponse
    {

        $respObjects = array();
        $codeStatut = "ERROR";
        try{
            $data = $entityManager->getRepository(DonneurOrdre::class)->findBy([],["id"=>"DESC"]);
            if($data){
                $codeStatut = "OK";
                
                $respObjects["data"] = $donneurRepo->getDetailsDonneur();
            }else{
                $codeStatut = "ERREUR";
            }
        }catch(\Exception $e){
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);

    }

    #[Route('/getcontact', methods: ['GET'])]
    public function GetAllcontact(EntityManagerInterface $entityManager, Request $request, donneurRepo $donneurRepo): JsonResponse
    {
        $response = [];
        $result = [];
        $contacts = $entityManager->getRepository(ContactDonneurOrdre::class)->findAll();

        foreach ($contacts as $contact) {

            $response = [
                'id' => $contact->getId(),
                'nom' => $contact->getnom(),
                'prenom' => $contact->getPrenom(),
                'adresse' => $contact->getAdresse(),
                'email' => $contact->getEmail(),
                'poste' => $contact->getPoste(),
                'tel' => $contact->getTel(),
                'mobile' => $contact->getMobile(),

            ];
            array_push($result, $response);
        }

        return new JsonResponse($result);
    }

    #[Route('/getOneContact/{id}',methods: ['GET'],name: 'get_contact')]
    public function Getcontact(EntityManagerInterface $entityManager, $id, Request $request,donneurRepo $donneurRepo): JsonResponse
    {
        $comp = "SELECT * FROM `contact_donneur_ordre` WHERE id =".$id;
        $stmt = $this->connection->prepare($comp);
        $stmt = $stmt->executeQuery();
        $resulatContact = $stmt->fetchAllAssociative();
           
        return new JsonResponse($resulatContact);
    }
    #[Route('/getOneDonneur/{id}',methods: ['GET'],name: 'get_one_donneur')]
    public function GetOnedonneur(EntityManagerInterface $entityManager, $id, Request $request,donneurRepo $donneurRepo): JsonResponse
    {
        $result = [];
        $sqlDonneur = "SELECT * FROM `donneur_ordre` WHERE id =".$id;
        $stmtd = $this->connection->prepare($sqlDonneur);
        $stmtd = $stmtd->executeQuery();
        $donneur = $stmtd->fetchAllAssociative();

        $sqContact = "SELECT * FROM `contact_donneur_ordre` WHERE id_donneur_ordre_id =".$id;
        $stmtd = $this->connection->prepare($sqContact);
        $stmtd = $stmtd->executeQuery();
        $contact = $stmtd->fetchAllAssociative();

        $sqContact = "SELECT * FROM `contact_historique` WHERE id_donneur_id =".$id;
        $stmtd = $this->connection->prepare($sqContact);
        $stmtd = $stmtd->executeQuery();
        $histo_contact = $stmtd->fetchAllAssociative();

        $champs = "SELECT c.*, dc.*
        FROM champs c, detail_model_affichage dc
        WHERE dc.id = c.champs_id
        AND dc.table_name = 'donneur_ordre'
        AND c.form = ".$id;
        $stmt = $this->connection->prepare($champs);
        $stmt = $stmt->executeQuery();
        $resulatChamps = $stmt->fetchAllAssociative();

            $result = [
                'champs'=>$resulatChamps,
                'donneur'=>$donneur,
                'contacts'=>$contact,
                'histo_contact'=>$histo_contact
            ];
           
        return new JsonResponse($result);
    }
}
