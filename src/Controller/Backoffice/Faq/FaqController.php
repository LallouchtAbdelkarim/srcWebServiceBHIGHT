<?php

namespace App\Controller\Backoffice\Faq;
use App\Repository\Oracle\AuthentificationRepository;
use App\Repository\Oracle\AbonnementsRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Faq;
use App\Entity\DemandeCreation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use App\Service\Verify;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class FaqController extends AbstractController{


    public function __construct(
		private ManagerRegistry $doctrine,
		private JWTEncoderInterface $JWTManager,
		private AbonnementsRepository $AbonneRrepo,
        private AuthentificationRepository $AuthRepo,
        private Verify $Verify,

		) {}

    //--------------- F.A.Q ---------------//
	
	
    #[Route("/ws/agonline/back/faq/add/", methods:["POST"])]
    public function addFaqAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "ERROR";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array();

        //--------- Verifier la validité du JWT Token -----------//

        try{
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            $idAdmin = $decoded["ID"];
            $trv = $decoded["TRV"];

            if(empty($idAdmin)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{
				$code = "ADDFAQ";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$question = $request->get("question");
					$rep = $request->get("reponse");
					
					if(empty($question) || empty($rep)){
						$codeStatut = "ERROR-PARAMS";
						$message = "Tous les champs sont obligatoires !";
					}
					else{
						$em = $this->doctrine->getManager();
						
						//-----------------------------
						
						
						$faq = new Faq();
						$faq->setDateFaq(new \DateTime());
						$faq->setQuestion($question);
						$faq->setReponse($rep);
					
						$em->persist($faq);
						$em->flush();
						
						$codeStatut = "OK";
						
					}
				}
				else{
					$codeStatut = "ERROR-ROLE";
					$message = "Vous n'êtes pas autorisé à effectuer cette opération !";
				}

                
            }
        }catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

            if($e->getMessage() == "Expired token"){
                $codeStatut = "ERROR-TOKEN";
            }
        }

        //-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
		$resp["message"] = $message;
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }
	
	
    #[Route("/ws/agonline/back/faq/update/{id}/", methods:["POST"])]
    public function updateFaqAction(Request $request,$id)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array();

        //--------- Verifier la validité du JWT Token -----------//

        try{
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            $idAdmin = $decoded["ID"];
            $trv = $decoded["TRV"];

            if(empty($idAdmin)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{
				$code = "UPPFAQ";
				if($this->Verify->verifyRole($code,$idAdmin)){
					
					$em = $this->doctrine->getManager();
					$faq = $em->getRepository(Faq::class)->findOneById($id);
					
					if(!$faq){
						$codeStatut = "ERROR-ACTU";
						$message = "Une erreur s'est produite !";
					}
					else{
						$question = $request->get("question");
						$rep = $request->get("reponse");
						
						if(empty($question) || empty($rep)){
							$codeStatut = "ERROR-PARAMS";
							$message = "Tous les champs sont obligatoires !";
						}
						else{
							
							
							//$actualite->setDateActu(new \DateTime());
							$faq->setQuestion($question);
							$faq->setReponse($rep);
							
							$em->flush();
							
							$codeStatut = "OK";
							
						}
					}
					
				}
				else{
					$codeStatut = "ERROR-ROLE";
					$message = "Vous n'êtes pas autorisé à effectuer cette opération !";
				}
				
				

                
            }
        }catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

            if($e->getMessage() == "Expired token"){
                $codeStatut = "ERROR-TOKEN";
            }
        }

        //-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
		$resp["message"] = $message;
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }
	
	
    #[Route("/ws/agonline/back/faq/get/{id}/", methods:["GET"])]
    public function getFaqAction(Request $request,$id)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("faq"=>array());

        //--------- Verifier la validité du JWT Token -----------//

        try{
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            $idAdmin = $decoded["ID"];
            $trv = $decoded["TRV"];

            if(empty($idAdmin)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{
				$code = "CONSFAQ";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$faq = $em->getRepository(Faq::class)->findOneById($id);
					
					$responseObjects["faq"] = $faq;
					
					$codeStatut = "OK";
				}
				else{
					$codeStatut = "ERROR-ROLE";
					$message = "Vous n'êtes pas autorisé à effectuer cette opération !";
				}
				
				
               
            }
        }catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

            if($e->getMessage() == "Expired token"){
                $codeStatut = "ERROR-TOKEN";
            }
        }

        //-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
		$resp["message"] = $message;
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }
	
	
    #[Route("/ws/agonline/back/faqs/get/all/", methods:["GET"])]
    public function getAllFaqAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("faq"=>array());

        //--------- Verifier la validité du JWT Token -----------//

        try{
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            $idAdmin = $decoded["ID"];
            $trv = $decoded["TRV"];

            if(empty($idAdmin)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{
				$code = "CONSFAQ"; //$this->Verify->verifyRole($code,$idAdmin)
				if($code){
					$em = $this->doctrine->getManager();
					$faq = $em->getRepository(Faq::class)->findAll();
					
					$responseObjects["faq"] = $faq;
					
					$codeStatut = "OK";
				}
				else{
					$codeStatut = "ERROR-ROLE";
					$message = "Vous n'êtes pas autorisé à effectuer cette opération !";
				}
				
				
               
            }
        }catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

            if($e->getMessage() == "Expired token"){
                $codeStatut = "ERROR-TOKEN";
            }
        }

        //-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
		$resp["message"] = $message;
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }
	
	
    #[Route("/ws/agonline/back/faq/delete/{id}/", methods:["GET"])]
    public function deleteFaqAction(Request $request, $id)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array();

        //--------- Verifier la validité du JWT Token -----------//

        try{
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            $idAdmin = $decoded["ID"];
            $trv = $decoded["TRV"];

            if(empty($idAdmin)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{
				$code = "DELFAQ";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$faq = $em->getRepository(Faq::class)->findOneById($id);
					
					if(!$faq){
						$codeStatut = "ERROR-USER";
						$message = "Une erreur s'est produite !";
					}
					else{

						$em->remove($faq);
						
						$em->flush();
						
						$codeStatut = "OK";

					}
				}
				else{
					$codeStatut = "ERROR-ROLE";
					$message = "Vous n'êtes pas autorisé à effectuer cette opération !";
				}
            }
        }catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

            if($e->getMessage() == "Expired token"){
                $codeStatut = "ERROR-TOKEN";
            }
        }

        //-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
		$resp["message"] = $message;
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }
}