<?php

namespace App\Controller\Models;

use App\Entity\BackgroundCourrier;
use App\Entity\DetailModelAffichage;
use App\Entity\Footer;
use App\Entity\Header;
use App\Entity\ModelCourier;
use App\Entity\ModelEmail;
use App\Entity\ModelSMS;
use App\Repository\ModelCourierRepository;
use App\Service\AuthService;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ValidationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Spipu\Html2Pdf\Html2Pdf;
#[Route('/API')]

class ModelsController extends AbstractController
{
    private $MessageService;
    public $serializer;
    public $modelCourierRepo;
    private $AuthService;

    public $em;
    public function __construct(
        AuthService $AuthService,
        EntityManagerInterface $em,
        MessageService $MessageService,
        SerializerInterface $serializer,
        ModelCourierRepository $modelCourierRepository
    )
    {
        $this->em = $em;
        $this->AuthService = $AuthService;
        $this->MessageService = $MessageService;
        $this->serializer = $serializer;
        $this->modelCourierRepository = $modelCourierRepository;
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
        $background = $request->get("background");

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
                $backgroundImg = null;
                if($background != ""){
                    $backgroundImg =  $entityManager->getRepository(BackgroundCourrier::class)->find($background);
                }
                $courier = new ModelCourier();
                $courier->setMessage($message);
                $courier->setTitre($titre);
                $courier->setObjet($objet);
                $courier->setDateCreation(new \DateTime());
                $courier->setIdBackground($backgroundImg);
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

    #[Route("/previewPdf", methods: ["POST"])]
    public function previewPdf(Request $request): JsonResponse
    {
        // Fetch data from request
        $data = json_decode($request->getContent(), true);

        // Check if data was successfully decoded
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $objet = $data['objet'] ?? '';
        $message = $data['message'] ?? '';
        $background = $data['background'] ?? '';
        $headerSelected = $data['header'] ?? '';
        $footerSelected = $data['footer'] ?? '';

        // Fetch header and footer data from database
        $header = $this->em->getRepository(Header::class)->find($headerSelected);
        $footer = $this->em->getRepository(Footer::class)->find($footerSelected);
        $footerText = $footer ? $footer->getMesssage() : '';
        $positionHeader = $header ? $header->getPosition() : 1;
        $styleHeader = $positionHeader == 1 ? 'left' : ($positionHeader == 2 ? 'center' : 'right');
        $logo = $header ? $header->getUrl() : '';

        // Prepare variables for Twig
        $variables = [
            'objet' => $objet,
            'message' => $message,
            'background' => $background,
            'headerLogo' => $logo,
            'headerStyle' => $styleHeader,
            'footerText' => $footerText,
        ];

        // Render the Twig template
        $htmlContent = $this->renderView('models/previewPdf.html.twig', $variables);

        // Generate PDF from HTML content
        $html2pdf = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', [10, 10, 10, 10], false);
        $html2pdf->writeHTML($htmlContent);
        $pdfOutput = $html2pdf->output('', 'S');

        // Encode PDF content to base64
        $base64Content = base64_encode($pdfOutput);

        // Prepare response data
        $file = [
            'content' => $base64Content,
            'filename' => 'previewPdf.pdf',
            'type' => 'application/pdf'
        ];

        // Return JSON response with PDF data
        return new JsonResponse($file, Response::HTTP_OK);
    }


    #[Route('/addBackground')]
    public function addBackground(Request $request, EntityManagerInterface $entityManager): Response
    {
        $respObjects = array();
        $codeStatut ="ERROR";

        try {
            
            // Get the image file from the request
            $img = $request->files->get('img');
            if ($img) {
                $destination = $this->getParameter('kernel.project_dir') . '/public/profile_img';
                $newFilename = uniqid() . '.' . $img->guessExtension();
                $img->move($destination, $newFilename);

                $back = new BackgroundCourrier();
                $back->setUrl('profile_img/'.$newFilename);
                $this->em->persist($back);
                $this->em->flush();
                $codeStatut = "OK";
            }
            } catch (\Exception $e) {
                $respObjects["msg"] = $e->getMessage();
                $codeStatut="ERROR";
        }
        
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getListeBackground')]
    public function getListeBackground(Request $request, EntityManagerInterface $entityManager): Response
    {
        $respObjects = array();
        $codeStatut ="ERROR";

        try {
            $image = $entityManager->getRepository(BackgroundCourrier::class)->findAll();
            $respObjects['data']= $image;
        } catch (\Exception $e) {
            $respObjects["msg"] = $e->getMessage();
            $codeStatut="ERROR";
        }
        
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getInfosCompl')]
    public function getInfosCompl(Request $request, EntityManagerInterface $entityManager): Response
    {
        $respObjects = array();
        $codeStatut ="ERROR";

        try {
            $respObjects['data']= $this->modelCourierRepository->getListeInfos();
        } catch (\Exception $e) {
            $respObjects["msg"] = $e->getMessage();
            $codeStatut="ERROR";
        }
        
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/header' ,methods:['POST'])]
    public function AddHeader(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $data = json_decode($request->getContent(), true);
            $position = $request->get('position');
            $titre = $request->get('titre');
            
            $img = $request->files->get('logo');
            if ($img) {
                $destination = $this->getParameter('kernel.project_dir') . '/public/header_img';
                $newFilename = uniqid() . '.' . $img->guessExtension();
                $img->move($destination, $newFilename);
                $header = new Header();
                $header->setUrl('header_img/'.$newFilename);
                $header->setTitre($titre);
                $header->setPosition($position);
                $entityManager->persist($header);
                $entityManager->flush();
                $codeStatut="OK";
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        $respObjects["et"] = $e->getMessage();

        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/update-header/{id}' ,methods:['POST'])]
    public function updateDepartment(Request $request, $id ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $position = $request->get('position');
            $titre = $request->get('titre');
            
            $img = $request->files->get('logo');
            if($img) {
                $destination = $this->getParameter('kernel.project_dir') . '/public/header_img';
                $newFilename = uniqid() . '.' . $img->guessExtension();
                $img->move($destination, $newFilename);
                $header = $entityManager->getRepository(Header::class)->find($id);
                $header->setUrl('header_img/'.$newFilename);
                $header->setTitre($titre);
                $header->setPosition($position);
                $entityManager->flush();
                $codeStatut="OK";
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        $respObjects["et"] = $e->getMessage();

        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/header/{id}' ,methods:['DELETE'])]
    public function deleteHeader(Request $request, $id ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $cor = $entityManager->getRepository(ModelCourier::class)->findOneBy(['id_header'=>$id]);
            if ($cor) {
                $header = $entityManager->getRepository(Header::class)->find($id);
                $entityManager->remove($header);
                $entityManager->flush();
                $codeStatut="OK";
            }else{
                $codeStatut="ERROR-HEADER";
            }

        } catch (\Exception $e) {
            $codeStatut="ERROR";
            $respObjects["et"] = $e->getMessage();
        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/header' ,methods:['GET'])]
    public function listHedaer(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            $codeStatut='OK';
            $respObjects['data']  = $entityManager->getRepository(Header::class)->findAll();

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/header/{id}' ,methods:['GET'])]
    public function getHedaer(Request $request, $id ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $codeStatut='OK';
            $respObjects['data']  = $entityManager->getRepository(Header::class)->find($id);

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    #[Route('/footer' ,methods:['POST'])]
    public function AddFooter(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $data = json_decode($request->getContent(), true);
            $message = $request->get('message');
            $titre = $request->get('titre');
            
            if ($message != '') {

                $footer = new Footer();
                $footer->setMesssage($message);
                $footer->setTitre($titre);
                $entityManager->persist($footer);
                $entityManager->flush();
                $codeStatut="OK";
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        $respObjects["et"] = $e->getMessage();

        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/update-footer/{id}' ,methods:['POST'])]
    public function updateFooter(Request $request, $id ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $message = $request->get('message');
            $titre = $request->get('titre');
            
            if ($message != '') {

                $footer = $entityManager->getRepository(Footer::class)->find($id);
                $footer->setMesssage($message);
                $footer->setTitre($titre);
                $entityManager->persist($footer);
                $entityManager->flush();
                $codeStatut="OK";

            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        $respObjects["et"] = $e->getMessage();

        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/footer' ,methods:['GET'])]
    public function listFooter(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            $codeStatut='OK';
            $respObjects['data']  = $entityManager->getRepository(Footer::class)->findAll();

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/footer/{id}' ,methods:['GET'])]
    public function getFooter(Request $request, $id ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $codeStatut='OK';
            $respObjects['data']  = $entityManager->getRepository(Footer::class)->find($id);

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
}
