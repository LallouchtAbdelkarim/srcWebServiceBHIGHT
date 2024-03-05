<?php

namespace App\Controller\Models;

use App\Entity\ModelCourier;
use App\Entity\ModelSMS;
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
    #[Route('/models/SMS', name: 'app_models')]
    public function index(EntityManagerInterface $entityManager, Request $request, ValidationService $validator,SerializerInterface $serializer): Response
    {
        $sms =  $entityManager->getRepository(ModelSMS::class)->findAll();
        $jsonContent = $serializer->serialize($sms,  'json');

        return new JsonResponse($jsonContent);
    }
    #[Route('/AddModelSMS', name: 'ajout_model_sms')]
    public function addModelSMS(EntityManagerInterface $entityManager, Request $request, ValidationService $validator): Response
    {
        $message = '';
        $titre = '';
        if ($request->getMethod() == "POST") {
            $message = $request->get("message");
            $titre = $request->get("titre");
            if ($message == "" or $titre == "") {
                $response = "Un des champs est vide !";
            } else {
                $sms = new ModelSMS();

                $sms->setMessage($message);
                $sms->setTitre($titre);
                $sms->setDateCreation(new \DateTime());
                $entityManager->persist($sms);
                $entityManager->flush();
                $response = "Model Added successfully";
            }
        }
        return new JsonResponse($response);
    }
    #[Route('/modifie_model/{id}', name: 'modifie_model_sms')]
    public function modifie_model(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, $id): Response
    {
        $sms =  $entityManager->getRepository(ModelSMS::class)->findOneBy(array('id' => $id));
        $message = '';
        $titre = '';
        if ($request->getMethod() == "POST") {
            $message = $request->get("message");
            $titre = $request->get("titre");
            $contraintes = array(
                "message" => array("val" => $message, "length" => 80, "type" => "string"),
                "titre" => array("val" => $titre, "length" => 80, "type" => "string"),
            );
            $valideForm = $validator->validateur($contraintes);
            if (!$valideForm) {
                $response = "Une erreur s'est produite !";
            } else {
                if ($message == "" or $titre == "") {
                    $response = "Un des champs est vide !";
                } else {
                    $sms->setMessage($message);
                    $sms->setTitre($titre);
                    $entityManager->persist($sms);
                    $entityManager->flush();
                    $response = "Modifier avec success";
                }
            }
        }

        return new JsonResponse($response);
    }

    #[Route('/delete_model/{id}', name: 'delete_model_sms')]
    public function deleteModelSMS(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, $id): Response
    {

        $sms = $entityManager->getRepository(ModelSMS::class)->findOneBy(array('id' => $id));
        $response = "";

        if (!$sms) {
            $response = "Ce Model n'existe pas !";
        } else {


            $entityManager->remove($sms);
            $entityManager->flush();
            $response = "OK";
        }
        return new JsonResponse($response);
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
    public function addModelCourier(EntityManagerInterface $entityManager, Request $request, ValidationService $validator): Response
    {

        $message = '';
        $titre = '';
        $objet = '';


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
                $response = "Une erreur s'est produite !";
            } else {
                if ($message == "" or $titre == "" or $objet == "") {
                    $response = "Un des champs est vide !";
                } else {
                    $courier = new ModelCourier();

                    $courier->setMessage($message);
                    $courier->setTitre($titre);
                    $courier->setObjet($objet);
                    $courier->setDateCreation(new \DateTime());
                    $entityManager->persist($courier);
                    $entityManager->flush();
                    $response = "Model Added successfully";
                }
            }
        }
        return new JsonResponse($response);
    }

    #[Route('/modifie_model_courier/{id}', name: 'modifie_model_courier')]
    public function modifie_model_courier(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, $id): Response
    {
        $courier =  $entityManager->getRepository(ModelCourier::class)->findOneBy(array('id' => $id));
        //dump($courier);

        $message = '';
        $titre = '';
        $objet = '';


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
                $response = "Une erreur s'est produite !";
            } else {
                if ($message == "" or $titre == "" or $objet == "") {
                    $response = "Un des champs est vide !";
                } else {


                    $courier->setMessage($message);
                    $courier->setTitre($titre);
                    $courier->setObjet($objet);
                    $entityManager->persist($courier);
                    $entityManager->flush();
                    $response = "Modifier avec success";
                }
            }
        }

        return new JsonResponse($response);
    }

    #[Route('/delete_model_courier/{id}', name: 'delete_model_courier')]
    public function deleteModelCourier(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, $id): Response
    {

        $courier = $entityManager->getRepository(ModelCourier::class)->findOneBy(array('id' => $id));
        $response = "";

        if (!$courier) {
            $response = "Ce profile n'existe pas !";
        } else {


            $entityManager->remove($courier);
            $entityManager->flush();
            $response = "OK";
        }
        return new JsonResponse($response);
    }

    /////////////////////////////////////////////////////////////////////////////////////
    #[Route('/getmodelsms/{id}', name: 'sms')]
    public function getModelSMS(EntityManagerInterface $entityManager,$id, Request $request, ValidationService $validator,SerializerInterface $serializer): Response
    {
        $sms =  $entityManager->getRepository(ModelSMS::class)->findOneBy(["id"=>$id]);
        $jsonContent = $serializer->serialize($sms,  'json');
        return new JsonResponse($jsonContent);
        // return $this->render('modelsMsg/create-model-courier.html.twig', [
        //     'controller_name' => 'ModelsController', 'courier' => $courier
        // ]);
    }
    #[Route('/getmodelcourier/{id}', name: 'courier')]
    public function getModelCourier(EntityManagerInterface $entityManager,$id, Request $request, ValidationService $validator,SerializerInterface $serializer): Response
    {
        $courier =  $entityManager->getRepository(ModelCourier::class)->findOneBy(["id"=>$id]);
        $jsonContent = $serializer->serialize($courier,  'json');
        return new JsonResponse($jsonContent);
        // return $this->render('modelsMsg/create-model-courier.html.twig', [
        //     'controller_name' => 'ModelsController', 'courier' => $courier
        // ]);
    }
}
