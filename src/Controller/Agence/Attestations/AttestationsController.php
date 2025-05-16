<?php

namespace App\Controller\Agence\Attestations;
use App\Repository\Oracle\AbonnementsRepository;
use App\Repository\Oracle\AuthentificationRepository;
use App\Repository\Oracle\ImpayeesRepository;
use App\Entity\Attestations;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\AttestationsHelper;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class AttestationsController extends AbstractController
{

    
	public function __construct(
		private ManagerRegistry $doctrine,
		private JWTEncoderInterface $JWTManager,
		private AbonnementsRepository $AbonneRrepo,
		private AuthentificationRepository $authRepo,
		private AttestationsHelper $attestationsHelper,
		private ImpayeesRepository $ImpayRepo,


		) {}




    /*----------------------------------------------*/
    /*----------------------------------------------*/
    /* Fonctions pour la création des attestations  */
    /*----------------------------------------------*/
    /*----------------------------------------------*/


    #[Route('/ws/agonline/attestations/list/', methods: ['GET'])]
    public function listAttestationsAction(Request $request)
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("attestations"=>array(),"abonnements"=>array());

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

            	 $em = $this->doctrine->getManager();
				$attestations = $em->getRepository(Attestations::class)->findBy(array("refClient"=>$refClient),array("dateAttestation"=>"DESC"));
				
				$responseObjects["attestations"] = $attestations;
				
				/*$databaseService = new DataBaseService($conn);
				$abonnements = $databaseService->getAbonnementsByCin($cinClient);

				$responseObjects["abonnements"] = $abonnements;*/

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
	


    #[Route('/ws/agonline/attestations/add/', methods: ['POST'])]
    public function addAttestationsAction(Request $request)
    {
		
    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/
		
		ini_set('memory_limit', -1);
		ini_set('max_execution_time', 300);

    	$response = new Response();
        //$response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("doc"=>0,"rdoc"=>"");

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

				$codTypeAtt = $request->get("codeType");
				$police = $request->get("police");
				$jum = $request->get("jum");
				
				if(empty($police) || empty($codTypeAtt)){
					$codeStatut = "EMPTY-PARAMS";
				}
				else{
					if($codTypeAtt < 1 || $codTypeAtt > 4){ // a voir les code type des documents
						$codeStatut = "ERROR-COD-DOC";
					}
					else{
						//$databaseService = new DataBaseService($this->conn);
						$abonnement = $this->AbonneRrepo->getAbonnementForAttestation($police);
						if($abonnement){
							
							$client = $this->authRepo->getClientByCinPolice($cinClient,$police);
							if($client){
								
								$policeEau = "";
								$policeElec = "";
								
								$abonnement2 = null;
								
								if($abonnement["GERANCE"] == "ELEC"){
									$policeElec = $police;
									if($jum == 1){
										$abon2 = $this->AbonneRrepo->getAbonnementsJumlee($abonnement["ADRESSE"],$cinClient,"EAU");
										if($abon2){
											$policeEau = $abon2["POLICE"];
											
											$abonnement2 = $this->AbonneRrepo->getAbonnementForAttestation($abon2["POLICE"]);
										}
									}
								}
								else{
									$policeEau = $police;
									if($jum == 1){
										$abon2 = $this->AbonneRrepo->getAbonnementsJumlee($abonnement["ADRESSE"],$cinClient,"ELEC");
										if($abon2){
											$policeElec = $abon2["POLICE"];
											
											$abonnement2 = $this->AbonneRrepo->getAbonnementForAttestation($abon2["POLICE"]);
										}
									}
								}
								
								
								
								$ifDossiersValide = true;
								if($codTypeAtt == 1){
									if($abonnement["STATUT"] == "Actif"){
										if($jum ==1 && $abonnement2 != null){
											if($abonnement2["STATUT"] != "Actif"){
												$ifDossiersValide = false;
											}
										}
									}
									else{
										$ifDossiersValide = false;
									}
								}
								elseif($codTypeAtt == 2){
									if($abonnement["STATUT"] == "Actif"){
										if($jum ==1 && $abonnement2 != null){
											if($abonnement2["STATUT"] != "Actif"){
												$ifDossiersValide = false;
											}
										}
									}
									else{
										$ifDossiersValide = false;
									}
								}
								elseif($codTypeAtt == 3){
									if($abonnement["STATUT"] != "Actif"){
										if($jum ==1 && $abonnement2 != null){
											if($abonnement2["STATUT"] == "Actif"){
												$ifDossiersValide = false;
											}
										}
									}
									else{
										$ifDossiersValide = false;
									}
								}
								
								if($ifDossiersValide){
									$dateDebut = date("Y-m")."-01";
									$dateFin = date("Y-m-t", strtotime($dateDebut));
									
									$em = $this->doctrine->getManager();
									
									$repo = $em->getRepository(Attestations::class);
									$qb = $repo->createQueryBuilder('a');
									$qb->select('count(a.id)');
									if($jum == 0){
										$qb->where('a.police = :police and a.jum = :jum and a.dateAttestation between :date and :date2 and a.codtypAtt = :type');
									}
									else{
										$qb->where('(a.policeEau = :police or a.policeElec = :police) and a.jum = :jum and a.dateAttestation between :date and :date2 and a.codtypAtt = :type');
									}
									
									$qb->setParameter('police',$police);
									$qb->setParameter('jum',$jum);
									$qb->setParameter('date',$dateDebut." 00:00:00");
									$qb->setParameter('date2',$dateFin." 23:59:59");
									$qb->setParameter('type',$codTypeAtt);
									$c = $qb->getQuery()->getSingleScalarResult();
									
									if($c > 0){
										$codeStatut = "COUNT-ATT";
									}
									else{
										
										//$attestationsHelper = new AttestationsHelper();
										
										$doc = $this->attestationsHelper->createTextForAttestation($abonnement,$abonnement2,$client,$jum,$codTypeAtt);
										//$codeStatut = $doc;
										$typeAttestation = $doc["typeDoc"];
										$textForPdf = $doc["textDoc"];
										
									
										$attestation = new Attestations();
										$attestation->setDateAttestation(new \DateTime());
										$attestation->setCodtypAtt($codTypeAtt);
										$attestation->setTypeAttestation($typeAttestation);
										$attestation->setRefClient($refClient);
										$attestation->setRefDoc("/".date("Y")."/DC/EI");
										$attestation->setPolice($police);
										$attestation->setPoliceEau($policeEau);
										$attestation->setPoliceElec($policeElec);
										$attestation->setJum($jum);
										$attestation->setRefViewDoc("");
										$attestation->setTextHTML($textForPdf);
										
										$em->persist($attestation);
										$em->flush();
										
										//-----to update
										$rvd = "ATB-".$attestation->getId()."-".$police;
										$attestation->setRefViewDoc($rvd);
										
										$em->flush();
										
						
										$codeStatut = "OK";
										$responseObjects["doc"] = $attestation->getId();
										$responseObjects["rdoc"] = $rvd;
									}
								}
								else{
									$codeStatut = "ERROR-ABN-INV-".$codTypeAtt;
								}
								
								
							}
							else{
								$codeStatut = "ERROR-CLT";
							}
							
						}
						else{
							$codeStatut = "ERROR-ABN";
						}
					}
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


    #[Route('/ws/agonline/attestations/{id}/{refDoc}/', methods: ['GET'])]
    public function getAttestationAction(Request $request,$id,$refDoc)
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("attestation"=>array());

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

            	$em = $this->doctrine->getManager();
				$attestation = $em->getRepository(Attestations::class)->findOneBy(array("id"=>$id,"refViewDoc"=>$refDoc));

				$responseObjects["attestation"] = $attestation;

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


  

    #[Route('/ws/agonline/attestations/verify/', methods: ['GET'])]
    public function verifyAttestationAction(Request $request)
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("attestation"=>array(),"client"=>array());

    	//--------- Verifier la validité du JWT Token -----------//

    	try{

    		// $decoded = JWT::decode($jwt, $this->key, array('HS256'));
			//$jwt = substr($jwt, 7);
			//$decoded = $this->JWTManager->decode($jwt);


            //$publicKey = $decoded["publicKey"];
			
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
				$refDoc = $codeArr[0];
				$cin = $codeArr[1];
				//$refClient = $cin;
				$refArr = explode("-", $refDoc);
				 $id = 0;
				 if (count($refArr) > 2) {
					 $id = $refArr[1];
				 }
				if(!empty($id) && !empty($refDoc)){

					$em = $this->doctrine->getManager();
					$attestation = $em->getRepository(Attestations::class)->findOneBy(array("id"=>$id,"refViewDoc"=>$refDoc));
					
					$responseObjects["attestation"] = $attestation;
					
					if($attestation){
						//$databaseService = new DataBaseService($this->conn);
						//$client = $this->authRepo->getClientByRef($attestation->getRefClient());
						
						$client = $this->authRepo->getClientByPolice($attestation->getPolice());
						
						$responseObjects["client"] = $client;
					}

					$codeStatut = "OK";
				}
				else{
					$codeStatut = "ERROR-PUBLIC-KEY";
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

	// gener etat des impayees
	#[Route("/ws/agonline/attestations/getImpayees/", methods:["Post"])]
	public function generImpayees(Request $request){
		
    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("listeImpayees"=>array());
		$police="";
		$gerance="";
		$message = "";
		try{

			$jwt = substr($jwt, 7);
			$decoded = $this->JWTManager->decode($jwt);
            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];
			if(empty($refClient) || empty($cinClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{
				$police = trim($request->get("police"));
				$gerance = trim($request->get("gerance"));
				$dateDebut = trim($request->get("dateDebut"));
				$dateFin = trim($request->get("dateFin"));
				if(empty($dateDebut) or empty($dateFin)){
					$codeStatut = "ERR-EMPTY";
					$message = "La date debut est de fin sont obligatoires ! ";
				}else{
					$policeOK = true;
					if(!empty($police)){
						$verifyClient = $this->authRepo->verifyPolice($cinClient, $police);
						if(!$verifyClient){
							$policeOK = false;
						}
					}
					
					if($policeOK){
						$listeImpayees = $this->ImpayRepo->getImpayeeByDate($police,$gerance,$dateDebut,$dateFin,$cinClient);
						if($listeImpayees){
							$codeStatut = "OK";
							$responseObjects["listeImpayees"]= $listeImpayees;
						}
						else{
							$codeStatut = "NO-DATA";
						}
					}
				}

			}

		}catch(\Exception $e){
			$codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

            if($e->getMessage() == "Expired token"){
                $codeStatut = "ERROR-TOKEN";
            }

		}

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