<?php

namespace App\Controller\Models;

use App\Entity\ModelCourier;
use App\Entity\ModelEmail;
use App\Entity\ModelSMS;
use App\Service\AuthService;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ValidationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/API')]

class ModelsController extends AbstractController
{
    private $MessageService;

    public function __construct(
        AuthService $AuthService,
        EntityManagerInterface $em,
        MessageService $MessageService,
    )
    {
        $this->em = $em;
        $this->AuthService = $AuthService;
        $this->MessageService = $MessageService;
    }

    #[Route('/models/SMS', name: 'app_models')]
    public function index(EntityManagerInterface $entityManager, Request $request, ValidationService $validator,SerializerInterface $serializer): Response
    {
        $sms =  $entityManager->getRepository(ModelSMS::class)->findAll();
        $jsonContent = $serializer->serialize($sms,  'json');

        return new JsonResponse($jsonContent);
    }
    #[Route('/AddModelSMS', name: 'ajout_model_sms')]
    public function addModelSMS(EntityManagerInterface $entityManager, Request $request, ValidationService $validator): JsonResponse
    {
        $message = '';
        $titre = '';
        $codeStatut = '';

        $message = $request->get("message");
        $titre = $request->get("titre");
        if ($message == "" or $titre == "") {
            $codeStatut = "EMPTY-PARAMS";
        } else {
            $sms = new ModelSMS();
            $sms->setMessage($message);
            $sms->setTitre($titre);
            $sms->setDateCreation(new \DateTime());
            $entityManager->persist($sms);
            $entityManager->flush();
            $codeStatut = "OK";
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/modifie_model/{id}', name: 'modifie_model_sms')]
    public function modifie_model(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, $id): JsonResponse
    {
        $sms =  $entityManager->getRepository(ModelSMS::class)->findOneBy(array('id' => $id));
        $message = '';
        $titre = '';
        $codeStatut = '';

        $message = $request->get("message");
        $titre = $request->get("titre");
        $contraintes = array(
            "message" => array("val" => $message, "length" => 80, "type" => "string"),
            "titre" => array("val" => $titre, "length" => 80, "type" => "string"),
        );
        $valideForm = $validator->validateur($contraintes);
        if (!$valideForm) {
            $codeStatut = "EMPTY-PARAMS";
        } else {
            if ($message == "" or $titre == "") {
                $codeStatut = "EMPTY-PARAMS";
            } else {
                $sms->setMessage($message);
                $sms->setTitre($titre);
                $entityManager->persist($sms);
                $entityManager->flush();
                $codeStatut = "OK";
            }
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/delete_model/{id}', name: 'delete_model_sms')]
    public function deleteModelSMS(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, $id): JsonResponse
    {
        $sms = $entityManager->getRepository(ModelSMS::class)->findOneBy(array('id' => $id));
        $response = "";
        $codeStatut="";
        if (!$sms) {
            $response = "Ce Model n'existe pas !";
            $codeStatut = "NOT_EXIST";
        } else {
            $entityManager->remove($sms);
            $entityManager->flush();
            $codeStatut = "OK";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
     /////////////////////////////////////////////////////////////////////////////////////
     #[Route('/getmodelsms/{id}', name: 'sms')]
     public function getModelSMS(EntityManagerInterface $entityManager,$id, Request $request, ValidationService $validator,SerializerInterface $serializer): Response
     {
         $sms =  $entityManager->getRepository(ModelSMS::class)->findOneBy(["id"=>$id]);
         $jsonContent = $serializer->serialize($sms,  'json');
         return new JsonResponse($jsonContent);
     }

    #[Route('/models/courier', name: 'app_models_courier')]
    public function indexCourier(EntityManagerInterface $entityManager, Request $request, ValidationService $validator,SerializerInterface $serializer): Response
    {
        $courier =  $entityManager->getRepository(ModelCourier::class)->findAll();
        $jsonContent = $serializer->serialize($courier,  'json');
        return new JsonResponse($jsonContent);
        // return $this->render('modelsMsg/create-model-courier.html.twig', [
        //     'controller_name' => 'ModelsController', 'courier' => $courier
        // ]);
    }

    #[Route('/Add_Model_Courier', name: 'ajout_model_courier')]
    public function addModelCourier(EntityManagerInterface $entityManager, Request $request, ValidationService $validator): JsonResponse
    {
        $message = '';
        $titre = '';
        $objet = '';
        $codeStatut = "";

        $message = $request->get("message");
        $titre = $request->get("titre");
        $objet = $request->get("objet");

        $contraintes = array(
            "message" => array("val" => $message, "length" => 80, "type" => "string"),
            "titre" => array("val" => $titre, "length" => 80, "type" => "string"),
            "objet" => array("val" => $objet, "length" => 80, "type" => "string"),
        );
        $valideForm = $validator->validateur($contraintes);
        if (!$valideForm) {
            $codeStatut = "EMPTY-PARAMS";
        } else {
            if ($message == "" or $titre == "" or $objet == "") {
                $codeStatut = "EMPTY-PARAMS";
            } else {
                $courier = new ModelCourier();
                $courier->setMessage($message);
                $courier->setTitre($titre);
                $courier->setObjet($objet);
                $courier->setDateCreation(new \DateTime());
                $entityManager->persist($courier);
                $entityManager->flush();
                $codeStatut = "OK";
            }
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/modifie_model_courier/{id}', name: 'modifie_model_courier')]
    public function modifie_model_courier(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, $id): Response
    {
        $courier =  $entityManager->getRepository(ModelCourier::class)->findOneBy(array('id' => $id));
        //dump($courier);

        $message = '';
        $titre = '';
        $objet = '';
        $codeStatut = "";
        if ($request->getMethod() == "POST") {

            $message = $request->get("message");
            $titre = $request->get("titre");
            $objet = $request->get("objet");

            $contraintes = array(
                "message" => array("val" => $message, "length" => 80, "type" => "string"),
                "titre" => array("val" => $titre, "length" => 80, "type" => "string"),
                "objet" => array("val" => $objet, "length" => 80, "type" => "string"),
            );
            $valideForm = $validator->validateur($contraintes);
            if (!$valideForm) {
                $codeStatut = "EMPTY-PARAMS";
            } else {
                if ($message == "" or $titre == "" or $objet == "") {
                    $codeStatut = "EMPTY-PARAMS";
                } else {
                    $courier->setMessage($message);
                    $courier->setTitre($titre);
                    $courier->setObjet($objet);
                    $entityManager->persist($courier);
                    $entityManager->flush();
                    $codeStatut="OK";
                }
            }
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/delete_model_courier/{id}', name: 'delete_model_courier')]
    public function deleteModelCourier(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, $id): JsonResponse
    {

        $courier = $entityManager->getRepository(ModelCourier::class)->findOneBy(array('id' => $id));
        $response = "";
        $codeStatut = "";
        if (!$courier) {
            $codeStatut = "NOT_EXIST";
        } else {
            $entityManager->remove($courier);
            $entityManager->flush();
            $response = "OK";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getmodelcourier/{id}', name: 'courier')]
    public function getModelCourier(EntityManagerInterface $entityManager,$id, Request $request, ValidationService $validator,SerializerInterface $serializer): Response
    {
        $courier =  $entityManager->getRepository(ModelCourier::class)->findOneBy(["id"=>$id]);
        $jsonContent = $serializer->serialize($courier,  'json');
        return new JsonResponse($jsonContent);
    }

    //TODO:Email
    #[Route('/models/email', name: 'app_models_email')]
    public function indexEmail(EntityManagerInterface $entityManager, Request $request, ValidationService $validator,SerializerInterface $serializer): Response
    {
        $email =  $entityManager->getRepository(ModelEmail::class)->findAll();
        $jsonContent = $serializer->serialize($email,  'json');
        return new JsonResponse($jsonContent);
    }
    #[Route('/Add_Model_Email', name: 'ajout_model_email')]
    public function addModelEmail(EntityManagerInterface $entityManager, Request $request, ValidationService $validator): JsonResponse
    {
        $message = '';
        $titre = '';
        $objet = '';
        $codeStatut = "";

        $message = $request->get("message");
        $titre = $request->get("titre");
        $objet = $request->get("objet");

        $contraintes = array(
            "message" => array("val" => $message, "length" => 80, "type" => "string"),
            "titre" => array("val" => $titre, "length" => 80, "type" => "string"),
            "objet" => array("val" => $objet, "length" => 80, "type" => "string"),
        );
        $valideForm = $validator->validateur($contraintes);
        if (!$valideForm) {
            $codeStatut = "EMPTY-PARAMS";
        } else {
            if ($message == "" or $titre == "" or $objet == "") {
                $codeStatut = "EMPTY-PARAMS";
            } else {
                $email = new ModelEmail();
                $email->setMessage($message);
                $email->setTitre($titre);
                $email->setObjet($objet);
                $email->setDateCreation(new \DateTime());
                $entityManager->persist($email);
                $entityManager->flush();
                $codeStatut = "OK";
            }
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/modifie_model_email/{id}', name: 'modifie_model_email')]
    public function modifie_model_email(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, $id): Response
    {
        $email =  $entityManager->getRepository(ModelEmail::class)->findOneBy(array('id' => $id));
        //dump($email);
        $message = '';
        $titre = '';
        $objet = '';
        $codeStatut = "";
        if ($request->getMethod() == "POST") {

            $message = $request->get("message");
            $titre = $request->get("titre");
            $objet = $request->get("objet");

            $contraintes = array(
                "message" => array("val" => $message, "length" => 80, "type" => "string"),
                "titre" => array("val" => $titre, "length" => 80, "type" => "string"),
                "objet" => array("val" => $objet, "length" => 80, "type" => "string"),
            );
            $valideForm = $validator->validateur($contraintes);
            if (!$valideForm) {
                $codeStatut = "EMPTY-PARAMS";
            } else {
                if ($message == "" or $titre == "" or $objet == "") {
                    $codeStatut = "EMPTY-PARAMS";
                } else {
                    $email->setMessage($message);
                    $email->setTitre($titre);
                    $email->setObjet($objet);
                    $entityManager->persist($email);
                    $entityManager->flush();
                    $codeStatut="OK";
                }
            }
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/delete_model_email/{id}', name: 'delete_model_email')]
    public function deleteModelEmail(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, $id): JsonResponse
    {

        $email = $entityManager->getRepository(ModelEmail::class)->findOneBy(array('id' => $id));
        $response = "";
        $codeStatut = "";
        if (!$email) {
            $codeStatut = "NOT_EXIST";
        } else {
            $entityManager->remove($email);
            $entityManager->flush();
            $response = "OK";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

   
    #[Route('/getmodelemail/{id}', name: 'email')]
    public function getModelEmail(EntityManagerInterface $entityManager,$id, Request $request, ValidationService $validator,SerializerInterface $serializer): Response
    {
        $email =  $entityManager->getRepository(ModelEmail::class)->findOneBy(["id"=>$id]);
        $jsonContent = $serializer->serialize($email,  'json');
        return new JsonResponse($jsonContent);
    }
}
