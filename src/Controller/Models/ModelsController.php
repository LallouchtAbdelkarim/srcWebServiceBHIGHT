<?php

namespace App\Controller\Models;

use App\Entity\BackgroundCourrier;
use App\Entity\DetailModelAffichage;
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

    #[Route("/previewPdf")]
    public function getContrat(Request $request): JsonResponse
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        try {
            $data = json_decode($request->getContent(), true);
            $objet = $data['objet'];
            $message = $data['message'];
            $background = $data['background'];
        
            
            // Header
            $header = '<style>
            .background{
                        width:100px;height:100px;
                    }
            </style><div style="text-align:center"><img src="profile_img/logoCourrier/header2.png"  /></div>';
            $html = "<div style='font-family:dejavusans'>";
            $html .= $header;
            $html .= "<h1 class='title'>Object :" . htmlspecialchars($objet, ENT_QUOTES, 'UTF-8') . "</h1>";
            $html .= '<div style="text-align:right"><img src="profile_img/barcode.gif"  /></div>';
            $html .= '
                <div style="margin-top: 20px; margin-bottom: 50px;">
                    <table style="width: 100%;">
                        <tr>
                        <td style="width: 70%;">
                             <div>
                            <b>Réference : 123456789</b>
                            </div>
                        </td>
                        <td style="width: 30%;">
                            <div>
                                <b>Prenom NOM</b><br><br>
                                <b>Adresse complet</b><br><br>
                                <b>Code postale Ville</b><br><br>
                                <b>Pays</b><br><br>
                            </div>
                        </td>
                        </tr>
                    </table>
                </div>
            ';
           
            
            if($background != ""){
                $background = $this->em->getRepository(BackgroundCourrier::class)->find($background);
                $background = $background->getUrl();
                $html .= "<div style='position:relative;width:100%;min-height:1000px'>
                    <div style='position:absolute; top:0; bottom:0; left:0; right:0; z-index:-1;'>
                    <img src='".$background."' style='width:100%; height:100%; object-fit:cover;'>
                    </div>
                    <p>" . html_entity_decode($message) . "</p>
                </div>";
                $html .="</div>";
            }else{
                $html .= "<div style='position:relative;width:100%;min-height:1000px'>
                    <p>" . html_entity_decode($message) . "</p>
                </div>";
                $html .="</div>";
            }


            $html2pdf = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', array(10, 10, 10, 10),false); 
            $html2pdf->pdf->SetFont('dejavusans', '', 12); 

            // Write HTML to PDF
            $html2pdf->writeHTML($html);
        
            // Output PDF as string
            $pdfContent = $html2pdf->output('', 'S');
        
            // Encode PDF content to base64
            $base64Content = base64_encode($pdfContent);
        
            // Prepare response data
            $file = [
                'content' => $base64Content,
                'filename' => 'previewPdf.pdf',
                'type' => 'application/pdf'
            ];
        
            // Serialize response to JSON
            $json = $this->serializer->serialize($file, 'json');
        
            // Return JsonResponse with PDF data
            return new JsonResponse($json, Response::HTTP_OK, [], true);
        
        } catch (\Exception $e) {
            // Handle exceptions
            $response = "Exception- " . $e->getMessage();
        
            // Return error response
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
    
}
