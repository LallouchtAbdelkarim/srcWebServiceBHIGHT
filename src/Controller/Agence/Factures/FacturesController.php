<?php

namespace App\Controller\Agence\Factures;
use App\Repository\Oracle\FacturesRepository;
use App\Repository\Oracle\ImpayeesRepository;
use App\Repository\Oracle\AbonnementsRepository;
use App\Repository\Oracle\AuthentificationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;


class FacturesController extends AbstractController
{




    

	public function __construct(
		private ManagerRegistry $doctrine,
		private JWTEncoderInterface $JWTManager,
		private FacturesRepository $factureRepo,
		private ImpayeesRepository $ImpayRepo,
		private AuthentificationRepository $authRepo,
		private AbonnementsRepository $AbonneRepo,
		) {}

	

	
		
	

    /*---------------------------------------------------------*/
    /*---------------------------------------------------------*/
    /* Fonction pour récupération de l'historique des factures */
    /*---------------------------------------------------------*/
    /*---------------------------------------------------------*/

   
    #[Route('/ws/agonline/factures/historique/{police}', methods: ['GET'])]
    public function historiqueFacturesAction(Request $request, $police)
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("factures"=>array());

    	//--------- Verifier la validité du JWT Token -----------//

    	try{

    		// $decoded = JWT::decode($jwt, $this->key, array('HS256'));
			$jwt = substr($jwt, 7);
			$decoded = $this->JWTManager->decode($jwt);


            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];

            if(empty($refClient) || empty($cinClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{

            	//$databaseService = new DataBaseService($this->conn);
				
				$verifyClient = $this->authRepo->verifyPolice($cinClient, $police);
				if($verifyClient){
					$histoFacture = $this->factureRepo->getHistoriqueFacture($police);

					$responseObjects["factures"] = $histoFacture;
					
					
					$totalImp = $this->ImpayRepo->getSumImpaye($police);
					$responseObjects["impayee"] = $totalImp;
					
					$totalImpFact = $this->ImpayRepo->getSumImpayeFact($police);
					$responseObjects["impayeeFact"] = $totalImpFact;
					
					$countImp = $this->ImpayRepo->getCountImpaye($police);
					$responseObjects["countImp"] = $countImp;

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
	
	/*--------------------------------------------------*/
    /*--------------------------------------------------*/
    /* Fonction pour récupération des factures iampyées */
    /*--------------------------------------------------*/
    /*--------------------------------------------------*/


    #[Route('/ws/agonline/factures/impayee/{police}', methods: ['GET'])]
    public function facturesImpayeeAction(Request $request, $police)
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("factures"=>array());

    	//--------- Verifier la validité du JWT Token -----------//

    	try{

    		// $decoded = JWT::decode($jwt, $this->key, array('HS256'));
			$jwt = substr($jwt, 7);
			$decoded = $this->JWTManager->decode($jwt);

            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];

            if(empty($refClient) || empty($cinClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{

            	//$databaseService = new DataBaseService($this->conn);
				
				$verifyClient = $this->authRepo->verifyPolice($cinClient, $police);
				if($verifyClient){
					$facture = $this->ImpayRepo->getFactureImpayee($police);

					$responseObjects["factures"] = $facture;
					
					$totalImp = $this->ImpayRepo->getSumImpaye($police);
					$responseObjects["impayee"] = $totalImp;
					
					$totalImpFact = $this->ImpayRepo->getSumImpayeFact($police);
					$responseObjects["impayeeFact"] = $totalImpFact;
					
					$countImp = $this->ImpayRepo->getCountImpaye($police);
					$responseObjects["countImp"] = $countImp;

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
	
	/*----------------------------------------------*/
    /*----------------------------------------------*/
    /* Fonction pour récupération du détail facture */
    /*----------------------------------------------*/
    /*----------------------------------------------*/



    #[Route('/ws/agonline/facture/{ref}/', methods: ['GET'])]
    public function factureAction(Request $request, $ref)
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("facture"=>array(),"details"=>array(),"indexes"=>array());

    	//--------- Verifier la validité du JWT Token -----------//

    	try{

    		// $decoded = JWT::decode($jwt, $this->key, array('HS256'));
			$jwt = substr($jwt, 7);
			$decoded = $this->JWTManager->decode($jwt);


            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];

            if(empty($refClient) || empty($cinClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{

            	//$databaseService = new DataBaseService($this->conn);
            	$facture = $this->factureRepo->getFacture($ref);
				
				if($facture){
					$police = $facture["POLICE"];
					$verifyClient = $this->authRepo->verifyPolice($cinClient, $police);
					if($verifyClient){
						
						if($facture["GERANCE"] != "EAU"){
							$detailIndex = $this->factureRepo->getIndexFacture($ref);
							
							$responseObjects["indexes"] = $detailIndex;
						}
						
						$abonnement = $this->AbonneRepo->getOneAbonnement($police);
						
						$responseObjects["facture"] = $facture;
						
						$details = $this->factureRepo->getDetailsFacture($ref);
						
						$responseObjects["details"] = $details;

						$codeStatut = "OK";
					}
					else{
						$codeStatut = "ERROR-POLICE";
					}
				}
				else{
					$codeStatut = "NOT-FOUND";
				}
            	

            }
    	}catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage()." ".$e->getLine();

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

    /*---------------------------------------------------------*/
    /* Fonction pour récupération de l'historique des factures */
    /*---------------------------------------------------------*/
    /*---------------------------------------------------------*/


    #[Route('/ws/agonline/service/e-facture/', methods: ['POST'])]
    public function eFactureAction(Request $request)
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$responseObjects = array("factures"=>array());

    	//--------- Verifier la validité du JWT Token -----------//
		$message = "";
    	try{
			
			$police = $request->get("police");
			$cin = $request->get("cin");

    		//$databaseService = new DataBaseService($this->conn);
				
			$verifyClient = $this->authRepo->verifyPolice($cin, $police);
			if($verifyClient){
				$histoFacture = $this->factureRepo->getHistoriqueFacture($police);

				$responseObjects["factures"] = $histoFacture;
					

				$codeStatut = "OK";
			}
			else{
				$codeStatut = "ERROR-POLICE";
				$message = "N° de CIN ou police incorrect !";
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
		$resp["message"] = $message;
		
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }
	

    #[Route('/ws/agonline/facture/verify/docs/', methods: ['GET'])]
    public function verifyFactureAction(Request $request)
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	//$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("facture"=>array(),"details"=>array(),"client"=>array(),"indexes"=>array());

    	//--------- Verifier la validité du JWT Token -----------//

    	try{

    		// $decoded = JWT::decode($jwt, $this->key, array('HS256'));
			//$jwt = substr($jwt, 7);
			//$decoded = $this->JWTManager->decode($jwt);


            //$publicKey = $decoded["publicKey"];
			//$refClient = $decoded["refClient"];
			
			$reference = $request->get("reference");
			
			$refArr = explode(":", $reference);
			$refFacture = "";
			if(count($refArr) > 1){
				$refFacture = $refArr[1];
			}
		
			//========== Decode reference =========//
			
			 $encryption_key = "PROXISOFT@RADEEJ-ONLI/21";
			 $ciphering = "AES-256-CTR";
			 $encryption_iv = '1234567891011121';
			 $options = 0;
			 
			  $key = hash('sha256', $encryption_key);
			 $iv = substr(hash('sha256', $encryption_iv), 0, 16);

			 $refFactureDecryption=openssl_decrypt(base64_decode($refFacture), $ciphering, $key, $options, $iv);

			 $codeArr = explode(":", $refFactureDecryption);
			
			//=====================================//
            
            if(count($codeArr) < 2){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{
				$ref = $codeArr[0];
				$cin = $codeArr[1];
				$refClient = $cin;
				if(!empty($ref) && !empty($cin)){
					//$databaseService = new DataBaseService($this->conn);
					$facture = $this->factureRepo->getFacture($ref);
					
					if($facture){
						
						if($facture["GERANCE"] != "EAU"){
							$detailIndex = $this->factureRepo->getIndexFacture($ref);
							
							$responseObjects["indexes"] = $detailIndex;
						}
						
						$police = $facture["POLICE"];
						$abonnement = $this->AbonneRepo->getOneAbonnement($police);
							
						$responseObjects["facture"] = $facture;
							
						$details = $this->factureRepo->getDetailsFacture($ref);
							
						$responseObjects["details"] = $details;
						
						$client = $this->authRepo->getClientByPolice($police);
						
						$responseObjects["client"] = $client;
						
						if(strtoupper($refClient) == strtoupper($client["CIN"]) || strtoupper($refClient) == strtoupper($client["CIN_PAYEUR"])){ // CIN au lieu de la référence (Cas de plusieurs polices)
							$codeStatut = "OK";
						}
						else{
							$responseObjects["facture"] = array();
							$responseObjects["details"] = array();
							$responseObjects["client"] = array();
							
							$codeStatut = "ERROR-CL".$client["CIN_PAYEUR"]. "-".$refClient;
						}
					}
					else{
						
						$codeStatut = "NOT-FOUND1";
						
					}
				}
				else{
					$codeStatut = "ERROR-EMPTY-PARAMS";
				}
            }
    	}catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION 22---".$e->getMessage();

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