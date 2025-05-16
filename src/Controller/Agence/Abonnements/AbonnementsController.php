<?php

namespace App\Controller\Agence\Abonnements;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\ReleveIndex;
use App\Entity\Params;
use App\Repository\Oracle\AbonnementsRepository;
use App\Repository\Oracle\ProvisionsRepository;
use App\Repository\Oracle\AuthentificationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\JsonResponse;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;


class AbonnementsController extends AbstractController
{
     

    /*--------------------------------------------------------*/
    /*--------------------------------------------------------*/
    /* Fonction pour récupération de la liste des abonnements */
    /*--------------------------------------------------------*/
    /*--------------------------------------------------------*/
    
    public function __construct(
        private ManagerRegistry $doctrine,
        private JWTEncoderInterface $JWTManager,
        private AbonnementsRepository $abonnementRepo, 
        private ProvisionsRepository $provisionRepo, 
        private AuthentificationRepository $authRepo,
        ) {
    
    }


  

    #[Route('/ws/agonline/abonnements/list/', methods: ['GET'])]
    public function abonnementsAction(Request $request, )
    {

        /*header("Access-Control-Allow-Origin: *");
	    header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "ERROR";

        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("abonnements"=>array());

        //--------- Verifier la validité du JWT Token -----------//

        try{

            // $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];

            if(empty($refClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{

                //$databaseService = new DataBaseService($this->conn);
                //$abonnements = $databaseService->getAbonnementsByCin($cinClient);
                $abonnements = $this->abonnementRepo->getAbonnementsByCin($cinClient);

                $responseObjects["abonnements"] = $abonnements;

                $codeStatut = "OK";

            }
        }catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

            if($e->getMessage() == "Expired token"){
                $codeStatut = "ERROR-TOKEN";
            }
        }

        //-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }
	

    #[Route('/ws/agonline/abonnements/get/{police}/', methods: ['GET'])]
    public function getOneAbonnementAction(Request $request,$police)
    {

        /*header("Access-Control-Allow-Origin: *");
	    header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "ERROR";

        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("abonnement"=>array());

        //--------- Verifier la validité du JWT Token -----------//

        try{

            // $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];

            if(empty($refClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{

                // $databaseService = new DataBaseService($this->conn);
                // $abonnements = $databaseService->getOneAbonnement($police);
                $abonnements = $this->abonnementRepo->getOneAbonnement($police);

                $responseObjects["abonnement"] = $abonnements;

                $codeStatut = "OK";

            }
        }catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

            if($e->getMessage() == "Expired token"){
                $codeStatut = "ERROR-TOKEN";
            }
        }

        //-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }



    #[Route('/ws/agonline/abonnements/operations/{police}', methods: ['GET'])]
    public function historiqueOperationAbonnementAction(Request $request, $police)
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/
    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("operations"=>array());

    	//--------- Verifier la validité du JWT Token -----------//

    	try{

    		// $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            $jwt = substr($jwt, 7);
            $decoded =$this->JWTManager->decode($jwt);


            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];

            if(empty($refClient) || empty($cinClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{

            	//$databaseService = new DataBaseService($this->conn);
				
				$verifyClient = $this->authRepo->verifyPolice($cinClient, $police);
				if($verifyClient){
					//$histoOperation = $databaseService->getOpAbonnement($police);
					$histoOperation = $this->provisionRepo->getOpAbonnement($police);

					$responseObjects["operations"] = $histoOperation;

					$codeStatut = "OK";
				}
				else{
					$codeStatut = "ERROR-POLICE";
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
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }



    #[Route('/ws/agonline/abonnements/list/releve/', methods: ['GET'])]
    public function abonnementsForReleveAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		 header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "ERROR";

        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("abonnements"=>array());

        //--------- Verifier la validité du JWT Token -----------//

        try{

            // $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];

            if(empty($refClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{
				
				$isAutoReleve = true;
				// $em = $this->getDoctrine()->getManager('customer');
                $em = $this->doctrine->getManager();
				
				$params = $em->getRepository(Params::class)->findOneById(1);
				if($params){
					if($params->getStatut() == 0){
						$isAutoReleve = false;
					}
				}
				
				if($isAutoReleve){

					//$databaseService = new DataBaseService($conn);
					//$abonnements = $databaseService->getAbonnementsByCin($cinClient);
					
					/*$list = array();
					for($i=0;$i<count($abonnements);$i++){
						$list[$i]["abn"] = $abonnement[$i];
						$list[$i]["index"] = $databaseService->getLastIndex($abonnement[$i]["POLICE"]);
					}*/

					//$responseObjects["abonnements"] = $abonnements;

					$codeStatut = "OK";
				}
				else{
					$codeStatut = "ERROR-SERVICE";
					$message = "Ce service est actuellement en arrêt !";
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
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }



    #[Route('/ws/agonline/abonnements/releve/index/', methods: ['POST'])]
    public function releveIndexAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "ERROR";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("abonnements"=>array());

        //--------- Verifier la validité du JWT Token -----------//

        try{

            // $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];

            if(empty($refClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{
				$isAutoReleve = true;
				// $em = $this->getDoctrine()->getManager('customer');
                $em = $this->doctrine->getManager();
				
				$params = $em->getRepository(Params::class)->findOneById(1);
				if($params){
					if($params->getStatut() == 0){
						$isAutoReleve = false;
					}
				}
				
				if($isAutoReleve){
					$police = $request->get("police");
					$index = $request->get("index");
					
					if(empty($police) || empty($index)){
						$codeStatut = "ERR-EMPTY";
						$message = "Tous les champs sont obligatoires ! ";
					}
					else{
						$verifyClient = $this->authRepo->verifyPolice($cinClient, $police);
						if($verifyClient){
							$extension_valide = array('jpeg','jpg','png','gif','pdf');
							$storeFolder = "./files/releves/";
							//-----------------------------
							$tmpFile = $_FILES['cpt']['tmp_name'];
							$extension = pathinfo($_FILES['cpt']['name'],PATHINFO_EXTENSION);
							$nomImage = date_format(new \DateTime(),"YmdHis").$police.".".$extension;
							
							$targetFile = $storeFolder.$nomImage;
							if(move_uploaded_file($tmpFile,$targetFile)){
								$targetFile1 = "/files/releves/".$nomImage;
								
								
								$releve = new ReleveIndex();
								$releve->setDateReleve(new \DateTime());
								$releve->setPolice($police);
								$releve->setReleveIndex($index);
								$releve->setUrlFile($targetFile1);
								
								$em->persist($releve);
								$em->flush();
								
								$codeStatut = "OK";
							}
							else{
								$codeStatut = "ERR-IMG";
								$message = "Erreur lors de l'upload de l'image ! ";
							}	
						}
						else{
							$codeStatut = "ERROR-POLICE";
							$message = "N° de police incorrect !";
						}
						
					}
				}
				else{
					$codeStatut = "ERROR-SERVICE";
					$message = "Ce service est actuellement en arrêt !";
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