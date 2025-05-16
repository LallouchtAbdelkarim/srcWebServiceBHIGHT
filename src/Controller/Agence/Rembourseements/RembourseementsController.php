<?php
namespace App\Controller\Agence\Rembourseements;


use App\Repository\Oracle\AuthentificationRepository;
use App\Repository\Oracle\RembourssementsRepository;
use App\Repository\Oracle\ProvisionsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;



class RembourseementsController extends AbstractController{
   


    // public function __construct(private ManagerRegistry $doctrine) {}
    public function __construct(
		private ManagerRegistry $doctrine,
		private JWTEncoderInterface $JWTManager,
        private AuthentificationRepository $authRepo,
        private RembourssementsRepository $RembRepo,
        private ProvisionsRepository $ProvRepo,
		) {}


    /*----------------------------------------------------------------*/
    /*----------------------------------------------------------------*/
    /* Fonction pour récupération de l'historique des rembourseements */
    /*----------------------------------------------------------------*/
    /*----------------------------------------------------------------*/
  

    #[Route('/ws/agonline/remboursements/historique/{police}/', methods: ['GET'])]
    public function historiqueRembourssementsAction(Request $request, $police)
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("remboursements"=>array());

    	//--------- Verifier la validité du JWT Token -----------//

    	try{

    		// $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            // $refClient = $decoded->data->REF;
            // $cinClient = $decoded->data->CIN;
            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];

            if(empty($refClient) || empty($cinClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{

            	//$databaseService = new DataBaseService($this->conn);
				
				$verifyClient = $this->authRepo->verifyPolice($cinClient, $police);
				if($verifyClient){
					$histoProvisions = $this->RembRepo->getRembourssements($police);

					$responseObjects["remboursements"] = $histoProvisions;

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
	
	/*----------------------------------------------------------------*/
    /*----------------------------------------------------------------*/
    /* Fonction pour récupération de l'historique des rembourseements */
    /*----------------------------------------------------------------*/
    /*----------------------------------------------------------------*/


    #[Route('/ws/agonline/provisions/historique/{police}/', methods: ['GET'])]
    public function historiqueProvisionsAction(Request $request, $police)
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("provisions"=>array());

    	//--------- Verifier la validité du JWT Token -----------//

    	try{

    		// $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            // $refClient = $decoded->data->REF;
            // $cinClient = $decoded->data->CIN;
            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];

            if(empty($refClient) || empty($cinClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{

            	//$databaseService = new DataBaseService($this->conn);
				
				$verifyClient = $this->authRepo->verifyPolice($cinClient, $police);
				if($verifyClient){
					$histoProvisions = $this->ProvRepo->getProvisions($police);

					$responseObjects["provisions"] = $histoProvisions;

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

}