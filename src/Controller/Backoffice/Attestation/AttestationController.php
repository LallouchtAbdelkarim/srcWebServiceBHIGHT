<?php
namespace App\Controller\Backoffice\Attestation;
use App\Repository\Oracle\AuthentificationRepository;
use App\Repository\Oracle\AbonnementsRepository;
use App\Repository\Oracle\FacturesRepository;
use App\Repository\Oracle\ImpayeesRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Attestations;
use App\Entity\DemandeCreation;
use App\Entity\DemandeAbonnement;
use App\Entity\DemandeResiliation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use App\Service\AttestationsHelper;
use App\Service\Verify;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class AttestationController extends AbstractController{
   

    public function __construct(
		private ManagerRegistry $doctrine,
		private JWTEncoderInterface $JWTManager,
		private AbonnementsRepository $AbonneRrepo,
        private AuthentificationRepository $AuthRepo,
		private AttestationsHelper $attestationsHelper,
        private FacturesRepository $FacturesRepo,
        private ImpayeesRepository $ImpayeeRepo,
        private Verify $Verify,

		) {}
    
	//--------------- Attestation -------------//

   
    #[Route("/ws/agonline/back/attestations/get/all/", methods:["POST"])]
    public function getAllAttestationsAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/

		ini_set("memory_limit","256M");

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
        $message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("attestations"=>array());

        //--------- Verifier la validité du JWT Token -----------//
		$decoded = "";
        try{
            
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);

		

            $idAdmin = $decoded["ID"];
            $trv = $decoded["TRV"];

            if(empty($idAdmin)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{
                $code = "CONSATT";
                if($this->Verify->verifyRole($code,$idAdmin)){
                    $em = $this->doctrine->getManager();
					$type = $request->get("type");
					$ref = str_replace(" ","",$request->get("ref"));
					$police = str_replace(" ","",$request->get("police"));
					if(empty($ref) && empty($police)){
						$codeStatut = "ERROR-DATA";
						$message = "Veuillez saisir au moins la référence ou la police !".$ref." = ".$police;
					}
					else{
						$params = array();
						
						//$attestations = $em->getRepository(Attestations::class)->findAll();
						$pol = false;
						$ty = false;
						$re = false;
						$repo = $em->getRepository(Attestations::class);
                        $qb = $repo->createQueryBuilder('a');
                        $qb->select('a');
						if(!empty($ref) && !empty($police)){
							$query = '(a.policeEau = :police or a.policeElec = :police) and a.refViewDoc = :ref';
							if(!empty($type)){
								$query = $query." and a.codtypAtt = :type";
								$ty = true;
							}
							
							$qb->where($query);
							$pol = true;
							$re = true;
						}
						else{
							if(!empty($police)){
								$query = '(a.policeEau = :police or a.policeElec = :police)';
								if(!empty($type)){
									$query = $query." and a.codtypAtt = :type";
									$ty = true;
								}
								$qb->where($query);
								$pol = true;
							}
							
							if(!empty($ref)){
								$query = 'a.refViewDoc = :ref';
								if(!empty($type)){
									$query = $query." and a.codtypAtt = :type";
									$ty = true;
								}
								$qb->where($query);
								$re = true;
							}
						}

						if($pol)
							$qb->setParameter('police',$police);
						
						if($re)
							$qb->setParameter('ref',$ref);
                        
						if($ty)
							$qb->setParameter('type',$type);
						
						
                        $attestations = $qb->getQuery()->getResult();


						$responseObjects["attestations"] = $attestations;

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

    
    #[Route("/ws/agonline/back/attestations/delete/{id}/", methods:["GET"])]
    public function deleteAttestationsAction(Request $request,$id)
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
                $code = "DELATT";
                if($this->Verify->verifyRole($code,$idAdmin)){
                    $em = $this->doctrine->getManager();
                    $attestation = $em->getRepository(Attestations::class)->findOneById($id);
                    if($attestation){
                        $em->remove($attestation);
                        $em->flush();
                        $codeStatut = "OK";
                    }
                    else{
                        $codeStatut = "ERROR-IDENTIFIER";
                        $message = "Une erreur s'est produite !";
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

    
    #[Route("/ws/agonline/back/attestations/add/", methods:["POST"])]
    public function addAttestationsAction(Request $request)
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
                $code = "ADDATT";
                if($this->Verify->verifyRole($code,$idAdmin)){
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
                            
                            $abonnement = $this->AbonneRrepo->getAbonnementForAttestation($police);
                            if($abonnement){
                                $client = $this->AuthRepo->getClientByPolice($police);
                                if($client){
                                    $policeEau = "";
                                    $policeElec = "";

                                    $abonnement2 = null;

                                    if($abonnement["GERANCE"] == "ELEC"){
                                        $policeElec = $police;
                                        if($jum == 1){
                                            $abon2 = $this->AbonneRrepo->getAbonnementsJumlee($abonnement["ADRESSE"],$client["CIN"],"EAU");
                                            if($abon2){
                                                $policeEau = $abon2["POLICE"];

                                                $abonnement2 = $this->AbonneRrepo->getAbonnementForAttestation($abon2["POLICE"]);
                                            }
                                        }
                                    }
                                    else{
                                        $policeEau = $police;
                                        if($jum == 1){
                                            $abon2 = $this->AbonneRrepo->getAbonnementsJumlee($abonnement["ADRESSE"],$client["CIN"],"ELEC");
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
                                        $em = $this->doctrine->getManager();

                                        $repo = $em->getRepository(Attestations::class);
                                        $qb = $repo->createQueryBuilder('a');
                                        $qb->select('count(a.id)');
                                        if($jum == 0){
                                            $qb->where('a.police = :police and a.jum = :jum and a.dateAttestation LIKE :date and a.codtypAtt = :type');
                                        }
                                        else{
                                            $qb->where('(a.policeEau = :police or a.policeElec = :police) and a.jum = :jum and a.dateAttestation LIKE :date and a.codtypAtt = :type');
                                        }

                                        $qb->setParameter('police',$police);
                                        $qb->setParameter('jum',$jum);
                                        $qb->setParameter('date',date_format(new \DateTime(),"Y-m-d")."%");
                                        $qb->setParameter('type',$codTypeAtt);
                                        $c = $qb->getQuery()->getSingleScalarResult();

                                        if($c > 0){
                                            $codeStatut = "COUNT-ATT";
                                        }
                                        else{

                                            

                                            $doc = $this->attestationsHelper->createTextForAttestation($abonnement,$abonnement2,$client,$jum,$codTypeAtt);

                                            $typeAttestation = $doc["typeDoc"];
                                            $textForPdf = $doc["textDoc"];


                                            $attestation = new Attestations();
                                            $attestation->setDateAttestation(new \DateTime());
                                            $attestation->setCodtypAtt($codTypeAtt);
                                            $attestation->setTypeAttestation($typeAttestation);
                                            $attestation->setRefClient($client["REF_CLIENT"]);
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
											$responseObjects["refClient"] = $client["REF_CLIENT"];
                                        }
                                    }
                                    else{
                                        $codeStatut = "ERROR-ABN-INV-".$codTypeAtt;
                                    }
                                }
                            }
                            else{
                                $codeStatut = "ERROR-ABN";
                            }
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
	
	#[Route("/ws/agonline/back/attestations/verify/{id}/{refDoc}/", methods:["GET"])]
    public function verifyAttestationAction(Request $request,$id,$refDoc)
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

    		$jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            $idAdmin = $decoded["ID"];
            $trv = $decoded["TRV"];
            
            if(empty($trv)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{
				$em = $this->doctrine->getManager();
				$attestation = $em->getRepository(Attestations::class)->findOneBy(array("id"=>$id,"refViewDoc"=>$refDoc));
					
				$responseObjects["attestation"] = $attestation;
					
				if($attestation){
					//$databaseService = new DataBaseService($conn);
					//$client = $databaseService->getClientByRef($attestation->getRefClient());
						
					//$client = $databaseService->getClientByPolice($attestation->getPolice());
					$client = $this->AuthRepo->getClientByPolice($attestation->getPolice());
						
					$responseObjects["client"] = $client;
				}

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
	
	
    #[Route("/ws/agonline/back/service/e-facture/", methods:["POST"])]
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

			$histoFacture = $this->FacturesRepo->getHistoriqueFactureBack($police);

			$responseObjects["factures"] = $histoFacture;
			$codeStatut = "OK";
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
	
	#[Route("/ws/agonline/back/facture/verify/{ref}/", methods:["GET"])]
	public function verifyFactureAction(Request $request, $ref)
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("facture"=>array(),"details"=>array(),"client"=>array(),"indexes"=>array());

    	//--------- Verifier la validité du JWT Token -----------//

    	try{
			
			$jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);

            
            $facture = $this->FacturesRepo->getFacture($ref);
			$refClient = $request->get("cin");
			if($facture){
				
				if($facture["GERANCE"] != "EAU"){
					$detailIndex = $this->FacturesRepo->getIndexFacture($ref);
							
					$responseObjects["indexes"] = $detailIndex;
				}
						
				$police = $facture["POLICE"];
				$abonnement =  $this->AbonneRrepo->getOneAbonnement($police);
							
				$responseObjects["facture"] = $facture;
							
				$details = $this->FacturesRepo->getDetailsFacture($ref);
							
				$responseObjects["details"] = $details;
						
				$client = $this->AuthRepo->getClientByPolice($police);
						
				$responseObjects["client"] = $client;
						
				if($refClient == $client["CIN"] || $refClient == $client["CIN_PAYEUR"]){ // CIN au lieu de la référence (Cas de plusieurs polices)
					$codeStatut = "OK";
				}
				else{
					$responseObjects["facture"] = array();
					$responseObjects["details"] = array();
					$responseObjects["client"] = array();
							
					$codeStatut = "ERROR-CL".$client["CIN"]. "-".$refClient;
				}
			}
			else{
						
				$codeStatut = "NOT-FOUND1";
						
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



    #[Route("/ws/agonline/back/attestations/impayee/",methods:["POST"])]
    public function attestationImpayee (Request $request){
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$responseObjects = array("factures"=>array());

    	//--------- Verifier la validité du JWT Token -----------//
		$message = "";
        try{

			$police = $request->get("police");
            $dateDebut = $request->get("dateDebut");
            $dateFin = $request->get("dateFin");
			$payeur = $request->get("payeur");
			$gerance = $request->get("gerance");
			
			if(!empty($dateDebut) && !empty($dateFin)){
				$dateDebut = date_format(new \DateTime($dateDebut),"d/m/Y");
				$dateFin = date_format(new \DateTime($dateFin),"d/m/Y");
			}

			$clinet =$this->AuthRepo->getClientByCin($payeur);
			if($clinet){
				$ifAllOk = true;
				if(!empty($police)){
					$policeExist = $this->AuthRepo->getClientByPolice($police);
					if($policeExist){
						if(strtoupper($payeur) != strtoupper($policeExist["CIN_PAYEUR"])){
							$ifAllOk = false;
							$message = "Police incorrect !";
							$codeStatut = "ERROR-POLICE";
						}
					}
					else{
						$ifAllOk = false;
						$message = "Police incorrect !";
						$codeStatut = "ERROR-POLICE";
					}
				}
				
				if($ifAllOk){
					$Facture = $this->ImpayeeRepo->getImpayeeByDate($police,$gerance,$dateDebut,$dateFin,$payeur);
                    //$clinet =$this->AuthRepo->getClientByCin($policeExist["CIN_PAYEUR"]);
                    $responseObjects["factures"]=$Facture;
                    $responseObjects["clinet"]=$clinet["NOM_PRENOM"];//it was lower case

                    $codeStatut = "OK";
				}
			}
			else{
				$message = "Aucun client trouvé";
				$codeStatut = "NO-CL";
			}
			/*
			$policeExist = $this->AuthRepo->getClientByPolice($police);
            if($policeExist){
                if($policeExist["CIN"]==$policeExist["CIN_PAYEUR"]){
                    $Facture = $this->ImpayeeRepo->getImpayeeByDate($police,$gerance="",$dateDebut,$dateFin,$policeExist["CIN"]);
                    $clinet =$this->AuthRepo->getClientByCin($policeExist["CIN_PAYEUR"]);
                    $responseObjects["factures"]=$Facture;
                    $responseObjects["clinet"]=$clinet["NOM_PRENOM"];//it was lower case

                    $codeStatut = "OK";

                }else if($policeExist["CIN"]!=$policeExist["CIN_PAYEUR"]){

                    $clinet =$this->AuthRepo->getClientByCin($policeExist["CIN_PAYEUR"]);
                    if($clinet){
                        $Facture = $this->ImpayeeRepo->getImpayeeByDate($police,$gerance="",$dateDebut,$dateFin,$clinet["CIN"]);
                        $responseObjects["factures"]=$Facture;
                        $responseObjects["clinet"]=$clinet["NOM_PRENOM"];//it was lower case
                        $codeStatut = "OK";
                    }

                }else{
                    $message="Null";
                }
            }else{
                $message="police Not Exist";
            }*/

    	}catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage()." ".$e->getLine();

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

    //----------------------------------------

    #[Route("/ws/agonline/back/attestations/password/",methods:["POST"])]
    public function attestationPassword(Request $request){
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$responseObjects = array("listeClinetActive"=>array());

    	//--------- Verifier la validité du JWT Token -----------//
		$message = "";
        $pass ="";
        $em = $this->doctrine->getManager();
        try{
            $clinets = $em->getRepository(DemandeCreation::class)->findBy(array("etat"=>1));
            $responseObjects["listeClinetActive"]=$clinets;
			$codeStatut = "OK";
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

    //-------------------
    #[Route("/ws/agonline/back/attestations/password/pdf/{cin}/",methods:["POST"])]
    public function clinentAttestationPassword(Request $request,$cin){
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$responseObjects = array("clinet"=>array());

    	//--------- Verifier la validité du JWT Token -----------//
		$message = "";
        $em = $this->doctrine->getManager();
        try {
            $loginInfo = $em->getRepository(DemandeCreation::class)->findOneBy(array("id"=>$cin));//array("cin"=>$cin)
            if($loginInfo){
				$clinet =$this->AuthRepo->getClientByCin($loginInfo->getCin());
				$responseObjects["loginInfo"]= $loginInfo;
				$responseObjects["clinet"]= $clinet;
				$codeStatut = "Ok";  
			}
			else{
				$message = "Client introuvable";
			}	
        } catch (\Exception $e) {
            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

            if($e->getMessage() == "Expired token"){
                $codeStatut = "ERROR-TOKEN";
            }
            
        }

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


    //-----------situation impeeys---------
    #[Route("/ws/agonline/back/attestations/getImpayees/", methods:["Post"])]
	public function generImpayees(Request $request){
		
    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("listeImpayees"=>array());
		$police="";
		//$gerance="";
		$message = "";

		try{

			$jwt = substr($jwt, 7);
			$decoded = $this->JWTManager->decode($jwt);
			$police = trim($request->get("police"));
				//$gerance = trim($request->get("gerance"));
			$dateDebut = trim($request->get("dateDebut"));
			$dateFin = trim($request->get("dateFin"));
			$payeur = $request->get("payeur");
			
			if(!empty($dateDebut) && !empty($dateFin)){
				$dateDebut = date_format(new \DateTime($dateDebut),"d/m/Y");
				$dateFin = date_format(new \DateTime($dateFin),"d/m/Y");
			}

			$clinet =$this->AuthRepo->getClientByCin($payeur);
			if($clinet){
				$ifAllOk = true;
				if(!empty($police)){
					$policeExist = $this->AuthRepo->getClientByPolice($police);
					if($policeExist){
						if(strtoupper($payeur) != strtoupper($policeExist["CIN_PAYEUR"])){
							$ifAllOk = false;
							$message = "Police incorrect !";
							$codeStatut = "ERROR-POLICE";
						}
					}
					else{
						$ifAllOk = false;
						$message = "Police incorrect !";
						$codeStatut = "ERROR-POLICE";
					}
				}
				
				if($ifAllOk){
					$responseObjects["client"]=$clinet;
					$listeImpayees = $this->ImpayRepo->getImpayeeByDate($police,$gerance="",$dateDebut,$dateFin,$clinet["CIN"]); //,$clinet["cin"]
                    if($listeImpayees){
                        $codeStatut = "OK";
                        $responseObjects["listeImpayees"]= $listeImpayees;
                    }else{
                        $codeStatut = "ERROR";
                        $message = "impayée non trouver";
                    }
				}
				else{
					$codeStatut = "NO-POLICE";
					$message = "Aucune police trouvée ! ";
				}
			}
			else{
				$codeStatut = "NO-CL";
                $message = "Aucun client trouvé ! ";
			}
            /*if($police){
                $clinet = $this->AuthRepo->getClientByPolice($police);
                if($clinet){
                    $responseObjects["client"]=$clinet;
                    if(empty($dateDebut) or empty($dateFin)){
                        $codeStatut = "ERR-EMPTY";
                        $message = "La date debut est de fin sont obligatoires ! ";
                    }else{
                        $listeImpayees = $this->ImpayRepo->getImpayeeByDate($police,$gerance="",$dateDebut,$dateFin,$clinet["CIN"]); //,$clinet["cin"]
                        if($listeImpayees){
                            $codeStatut = "OK";
                            $responseObjects["listeImpayees"]= $listeImpayees;
                        }else{
                            $codeStatut = "ERROR";
                            $message = "impayée non trouver";
                        }
                    }
                }
            }*/
			
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
	
	#[Route("/ws/agonline/back/attestations/reglement/",methods:["POST"])]
    public function attestationReglement(Request $request){
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$responseObjects = array("factures"=>array());

    	//--------- Verifier la validité du JWT Token -----------//
		$message = "";
        try{

			$payeur = $request->get("payeur");

			$clinet =$this->AuthRepo->getClientByCin($payeur);
			if($clinet){
				$responseObjects["client"]=$clinet["NOM_PRENOM"];//it was lower case
				$responseObjects["adresse"]=$clinet["ADRESSE"];
                $codeStatut = "OK";
			}
			else{
				$message = "Aucun client trouvé";
				$codeStatut = "NO-CL";
			}
			

    	}catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage()." ".$e->getLine();

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
	
	//-------------------------------
	
	#[Route("/ws/agonline/back/dashboard/stat/",methods:["get"]) ]
    public function getStatDashboard(Request $request){
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
        $message = "";
        $responseObjects=[];
        $jwt = $request->headers->get('Authorization');
        try{
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);

            $idAdmin = $decoded["ID"];
            $trv = $decoded["TRV"];

            if(empty($idAdmin)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }else{
                // $qb = $this->
                // $em = $this->doctrine->getManager();
                // $qb = $em->createQueryBuilder();
                // $qb->select('u')
                // ->from('User', 'u')
                // ->where('u.id = ?1')
                // ->orderBy('u.name', 'ASC');
                // $attestations = $em->getRepository(Attestations::class)->findAll();
                // $responseObjects["attestations"] = $attestations;
                // $codeStatut = "OK";
				
				$em = $this->doctrine->getManager();
				
                $repo = $em->getRepository(Attestations::class);
                $qb = $repo->createQueryBuilder('a');
                $qb->select('count(a.codtypAtt)');
                $qb->where('a.codtypAtt=1');
                $countAb = $qb->getQuery()->getSingleScalarResult();
                //-------------
				$qb2 = $repo->createQueryBuilder('a');
                $qb2->select('count(a.codtypAtt)');
                $qb2->where('a.codtypAtt=2');
                $countCs = $qb2->getQuery()->getSingleScalarResult();
                //-------------
				$qb3 = $repo->createQueryBuilder('a');
                $qb3->select('count(a.codtypAtt)');
                $qb3->where('a.codtypAtt=3');
                $countRs = $qb3->getQuery()->getSingleScalarResult();

                $responseObjects["countAb"]=$countAb;
                $responseObjects["countCs"]=$countCs;
                $responseObjects["countRs"]=$countRs;
				
			    $repo = $em->getRepository(DemandeCreation::class);
                $qb = $repo->createQueryBuilder('a');
                $qb->select('count(a.id)');
                $qb->where('a.etat=1');
                $countDemVal = $qb->getQuery()->getSingleScalarResult();
                //-------------
				$qb2 = $repo->createQueryBuilder('a');
                $qb2->select('count(a.id)');
                $qb2->where('a.etat=0');
                $countDemEnc = $qb2->getQuery()->getSingleScalarResult();
				
				$responseObjects["clVal"]=$countDemVal;
                $responseObjects["clEnt"]=$countDemEnc;
                //-------------
				
				// demande abonnement
				
				$repo = $em->getRepository(DemandeAbonnement::class);
                $qb = $repo->createQueryBuilder('a');
                $qb->select('count(a.id)');
                $qb->where('a.codeStatut=0');
                $abEnAttente = $qb->getQuery()->getSingleScalarResult();
                //-------------
				$qb2 = $repo->createQueryBuilder('a');
                $qb2->select('count(a.id)');
                $qb2->where('a.codeStatut=1');
                $abValide = $qb2->getQuery()->getSingleScalarResult();
                //-------------
				$qb3 = $repo->createQueryBuilder('a');
                $qb3->select('count(a.id)');
                $qb3->where('a.codeStatut=2');
                $abRejete = $qb3->getQuery()->getSingleScalarResult();
				
				$responseObjects["abEnAttente"]=$abEnAttente;
                $responseObjects["abValide"]=$abValide;
                $responseObjects["abRejete"]=$abRejete;
				
				// demande résiliation
				
				$repo = $em->getRepository(DemandeResiliation::class);
                $qb = $repo->createQueryBuilder('a');
                $qb->select('count(a.id)');
                $qb->where('a.codeStatut=0');
                $resEnAttente = $qb->getQuery()->getSingleScalarResult();
                //-------------
				$qb2 = $repo->createQueryBuilder('a');
                $qb2->select('count(a.id)');
                $qb2->where('a.codeStatut=1');
                $resValide = $qb2->getQuery()->getSingleScalarResult();
                //-------------
				$qb3 = $repo->createQueryBuilder('a');
                $qb3->select('count(a.id)');
                $qb3->where('a.codeStatut=2');
                $resRejete = $qb3->getQuery()->getSingleScalarResult();
				
				$responseObjects["resEnAttente"]=$resEnAttente;
                $responseObjects["resValide"]=$resValide;
                $responseObjects["resRejete"]=$resRejete;
				
				
				
				//-----------------------------------------------------------
                $codeStatut="OK";

            }


        }catch(\Exception $e) {

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