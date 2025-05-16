<?php

namespace App\Controller\Agence\Demandes;

use App\Repository\Oracle\AuthentificationRepository;
use App\Repository\Oracle\ImpayeesRepository;
use App\Entity\DemandeAbonnement;
use App\Entity\DemandeBranchement;
use App\Entity\DemandeRemboursement;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\DemandeResiliation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
class DemandesController extends AbstractController
{


	

	


    /*----------------------------------------------------*/
    /*----------------------------------------------------*/
    /* Fonction pour enregistrer une demande d'abonnement */
    /*----------------------------------------------------*/
    /*----------------------------------------------------*/

	public function __construct(
		private ManagerRegistry $doctrine,
		private JWTEncoderInterface $JWTManager,
		private ImpayeesRepository $ImpayRepo,
		private AuthentificationRepository $authRepo,
		) {}

	


	

    #[Route('/ws/agonline/demande/abonnement/', methods: ['POST'])]
    public function demandeAbonnementAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		 header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "ERROR";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        //$responseObjects = array(""=>array());

        //--------- Verifier la validité du JWT Token -----------//
		$refClient ="";
        $cinClient = "";
		
		try{

			if(!empty($jwt) && $jwt != null){
				// $decoded = JWT::decode($jwt, $this->key, array('HS256'));
				$jwt = substr($jwt, 7);
				$decoded = $this->JWTManager->decode($jwt);


				$refClient = $decoded["REF"];
				$cinClient = $decoded["CIN"];
			}
		}catch (\Exception $e) {
			$refClient = "";
        }

        try{
			$nom = $request->get("nom");
			$ice = $request->get("ice");
			$adresse = $request->get("adresseCorr");
			$tel = $request->get("tel");
			$email = $request->get("email");
			$cin = $request->get("cin");
			
			$gerance = $request->get("gerance");
			$adresseLivraison = $request->get("adresseLivraison");
			$commune = $request->get("commune");
			$raisonSocial = $request->get("raisonSocial");
			$representant = $request->get("representant");
			$personnalite = $request->get("personnalite");
			$identifiantF = $request->get("identifiantF");
			$usage = $request->get("usage");

			if(empty($gerance) || empty($adresseLivraison) || empty($commune) || ($personnalite == "PHYSIQUE" && empty($nom)) || 
			($personnalite == "MORALE" && (empty($raisonSocial) || empty($ice) || empty($representant))) || empty($cin) || empty($adresse) || empty($tel)){
				$codeStatut = "EMPTY-PARAMS";
				$message = "Un des champs obligatoires est vide !";
			}
			else{
				$extension_valide = array('jpeg','jpg','png','gif','pdf');
				$storeFolder = "./files/demandes/abonnement/";
				//-----------------------------
				$tmpFile = $_FILES['cinF']['tmp_name'];
				$extension = pathinfo($_FILES['cinF']['name'],PATHINFO_EXTENSION);
				$nomImage = date_format(new \DateTime(),"YmdHis").$cin."-cin.".$extension;
						
				$targetFile = $storeFolder.$nomImage;
				//-----------------------------
				$tmpFileBail = $_FILES['bail']['tmp_name'];
				$extensionBail = pathinfo($_FILES['bail']['name'],PATHINFO_EXTENSION);
				$nomImageBail = date_format(new \DateTime(),"YmdHis").$cin."-bail.".$extensionBail;
						
				$targetFileBail = $storeFolder.$nomImageBail;
				
				if(move_uploaded_file($tmpFile,$targetFile) && move_uploaded_file($tmpFileBail,$targetFileBail)){
					$targetFile1 = "/files/demandes/abonnement/".$nomImage;
					$targetFile2 = "/files/demandes/abonnement/".$nomImageBail;
					
					//$em = $this->getDoctrine()->getManager('customer');
					$em = $this->doctrine->getManager();
							
					$demande = new DemandeAbonnement();
					$demande->setDateDemande(new \DateTime());
					$demande->setGerance($gerance);
					$demande->setAdresseLivraison($adresseLivraison);
					$demande->setCommune($commune);
					$demande->setPersonnalite($personnalite);
					$demande->setNom($nom);
					$demande->setRaisonSocial($raisonSocial);
					$demande->setRepresentant($representant);
					$demande->setIdentifiantF($identifiantF);
					$demande->setIce($ice);
					$demande->setCin($cin);
					$demande->setUsageAbn($usage);
					$demande->setAdresseCorr($adresse);
					$demande->setTel($tel);
					$demande->setEmail($email);
					$demande->setCinFile($targetFile1);
					$demande->setBailFile($targetFile2);
					$demande->setCodeStatut(0);
					$demande->setStatut("En cours");
					$demande->setCommentaire("");
					$demande->setRefClient($refClient);
			
					$em->persist($demande);
					$em->flush();
					
					if(isset($_FILES['permis']) && !empty($_FILES['permis']['name'])){
						//-----------------------------
						$tmpFilePermis = $_FILES['permis']['tmp_name'];
						$extensionPermis = pathinfo($_FILES['permis']['name'],PATHINFO_EXTENSION);
						$nomImagePermis = date_format(new \DateTime(),"YmdHis").$cin."-permis.".$extensionPermis;
								
						$targetFilePermis = $storeFolder.$nomImagePermis;
						if(move_uploaded_file($tmpFilePermis,$targetFilePermis)){
							$targetFile3 = "/files/demandes/abonnement/".$nomImagePermis;
							$demande->setPermisFile($targetFile3);
						}
					}
					
					if(isset($_FILES['statut']) && !empty($_FILES['statut']['name'])){
						//-----------------------------
						$tmpFileStatut = $_FILES['statut']['tmp_name'];
						$extensionStatut = pathinfo($_FILES['statut']['name'],PATHINFO_EXTENSION);
						$nomImageStatut = date_format(new \DateTime(),"YmdHis").$cin."-statut.".$extensionStatut;
								
						$targetFileStatut = $storeFolder.$nomImageStatut;
						if(move_uploaded_file($tmpFileStatut,$targetFileStatut)){
							$targetFile4 = "/files/demandes/abonnement/".$nomImageStatut;
							$demande->setStatutFile($targetFile4);
						}
					}
					
					$em->flush();
							
					$codeStatut = "OK";
				}
				else{
					$codeStatut = "ERR-IMG";
					$message = "Erreur lors de l'upload de l'image ! ";
				}
			}
						
        }catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

            if($e->getMessage() == "Expired token"){
                $codeStatut = "ERROR-TOKEN";
            }
			
			//$message = " ! :".$e->getMessage();
        }

        //-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
		$resp["message"] = $message;
        //$resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }


    /*------------------------------------------------------*/
    /*------------------------------------------------------*/
    /* Fonction pour enregistrer une demande de résiliation */
    /*------------------------------------------------------*/
    /*------------------------------------------------------*/

  

    #[Route('/ws/agonline/demande/resiliation/', methods: ['POST'])]
    public function demandeResiliationAction(Request $request)
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

                //$databaseService = new DataBaseService($this->conn);
				
                $police = $request->get("police");
				$motif = $request->get("motif");
				
				if(empty($police) || empty($motif)){
					$codeStatut = "ERR-EMPTY";
					$message = "Tous les champs sont obligatoires ! ";
				}
				else{
					$verifyClient = $this->authRepo->verifyPolice($cinClient, $police);
					if($verifyClient){
						$totalImp = $this->ImpayRepo->getSumImpaye($police);
						if($totalImp < 0){
							$codeStatut = "ERROR-SOLDE";
							$message = "Vous avez des impayées concernant cette police, veuillez procéder au paiement avant d'initier votre demande de résiliation";
						}
						else{
							$extension_valide = array('jpeg','jpg','png','gif','pdf');
							$storeFolder = "./files/demandes/resiliation/";
							//-----------------------------
							$tmpFile = $_FILES['file']['tmp_name'];
							$extension = pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
							$nomImage = date_format(new \DateTime(),"YmdHis").uniqid()."-justif.".$extension;
									
							$targetFile = $storeFolder.$nomImage;
							
							if(move_uploaded_file($tmpFile,$targetFile)){
								$targetFile1 = "/files/demandes/resiliation/".$nomImage;
								$em = $this->doctrine->getManager();
								$demande = new DemandeResiliation();
								$demande->setDateDemande(new \DateTime());
								$demande->setPolice($police);
								$demande->setMotifResiliation($motif);
								$demande->setCodeStatut(0);
								$demande->setStatut("En cours");
								$demande->setCommentaire("");
								$demande->setRefClient($refClient);
								$demande->setJustifFile($targetFile1);
								
								$em->persist($demande);
								$em->flush();
								
								$codeStatut = "OK";
							}
							else{
								$codeStatut = "ERR-IMG";
								$message = "Erreur lors de l'upload de la pièce jointe ! ";
							}
						}
					}
					else{
						$codeStatut = "ERROR-POLICE";
						$message = "N° de police incorrect !";
					}
					
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

    	/*--------------------------------------------------------*/
    /*--------------------------------------------------------*/
    /* Fonction pour enregistrer une demande de remboursement */
    /*--------------------------------------------------------*/
    /*--------------------------------------------------------*/

    

    #[Route('/ws/agonline/demande/remboursement/', methods: ['POST'])]
    public function demandeRemboursementAction(Request $request)
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

                //$databaseService = new DataBaseService($this->conn);
				
                $police = $request->get("police");
				$motif = $request->get("motif");
				$montant = $request->get("montant");
				
				if(empty($police) || empty($motif) || empty($montant)){
					$codeStatut = "ERR-EMPTY";
					$message = "Tous les champs sont obligatoires ! ";
				}
				else{
					$verifyClient = $this->authRepo->verifyPolice($cinClient, $police);
					if($verifyClient){
						$totalImp = $this->ImpayRepo->getSumImpaye($police);
						if($totalImp > 0){
							$codeStatut = "ERROR-SOLDE";
							$message = "Vous avez des impayées concernant cette police, impossible de demander le remboursement";
						}
						else if($totalImp == 0){
							$codeStatut = "ERROR-SOLDE";
							$message = "Impossible de demander le remboursement, votre solde est : 0 DH";
						}
						else{
							if($montant < 0){
								$codeStatut = "ERROR-MONTANT";
								$message = "Veuillez saisir un montant correct !";
							}
							else{
								$soldePos = 0-$totalImp;
								if($soldePos > $montant){
									$codeStatut = "ERROR-MONTANT";
									$message = "Veuillez saisir un montant correct, votre solde est : ".$soldePos." DH";
								}
								else{
									$em = $this->doctrine->getManager();
									$demande = new DemandeRemboursement();
									$demande->setDateDemande(new \DateTime());
									$demande->setPolice($police);
									$demande->setMotifRemboursement($motif);
									$demande->setCodeStatut(0);
									$demande->setStatut("En cours");
									$demande->setCommentaire("");
									$demande->setRefClient($refClient);
									$demande->setMontant($montant);
									
									$em->persist($demande);
									$em->flush();
									
									$codeStatut = "OK";
								}
								
							}
							
						}
					}
					else{
						$codeStatut = "ERROR-POLICE";
						$message = "N° de police incorrect !";
					}
					
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



    #[Route('/ws/agonline/demande/branchement/', methods: ['POST'])]
    public function demandeBranchementAction(Request $request)
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
		$refClient ="";
        $cinClient = "";
		
		try{

			if(!empty($jwt) && $jwt != null){
				// $decoded = JWT::decode($jwt, $this->key, array('HS256'));
				$jwt = substr($jwt, 7);
				$decoded = $this->JWTManager->decode($jwt);


				$refClient = $decoded["REF"];
				$cinClient = $decoded["CIN"];
			}
		}catch (\Exception $e) {
			$refClient = "";
        }

        try{
			$nom = $request->get("nom");
			$ice = $request->get("ice");
			$adresse = $request->get("adresseCorr");
			$tel = $request->get("tel");
			$email = $request->get("email");
			$cin = $request->get("cin");
			
			$type = $request->get("type");
			$gerance = $request->get("gerance");
			$adresseLivraison = $request->get("adresseLivraison");
			$commune = $request->get("commune");
			$raisonSocial = $request->get("raisonSocial");
			$representant = $request->get("representant");
			$personnalite = $request->get("personnalite");
			$identifiantF = $request->get("identifiantF");
			$usage = $request->get("usage");

			if(empty($type) || empty($gerance) || empty($adresseLivraison) || empty($commune) || ($personnalite == "PHYSIQUE" && empty($nom)) || 
			($personnalite == "MORALE" && (empty($raisonSocial) || empty($ice) || empty($representant))) || empty($cin) || empty($adresse) || empty($tel)){
				$codeStatut = "EMPTY-PARAMS";
				$message = "Un des champs obligatoires est vide !";
			}
			else{
				$extension_valide = array('jpeg','jpg','png','gif','pdf');
				$storeFolder = "./files/demandes/branchement/";
				//-----------------------------
				$tmpFile = $_FILES['cinF']['tmp_name'];
				$extension = pathinfo($_FILES['cinF']['name'],PATHINFO_EXTENSION);
				$nomImage = date_format(new \DateTime(),"YmdHis").$cin."-cin.".$extension;
						
				$targetFile = $storeFolder.$nomImage;
				//-----------------------------
				$tmpFileBail = $_FILES['bail']['tmp_name'];
				$extensionBail = pathinfo($_FILES['bail']['name'],PATHINFO_EXTENSION);
				$nomImageBail = date_format(new \DateTime(),"YmdHis").$cin."-bail.".$extensionBail;
						
				$targetFileBail = $storeFolder.$nomImageBail;
				
				if(move_uploaded_file($tmpFile,$targetFile) && move_uploaded_file($tmpFileBail,$targetFileBail)){
					$targetFile1 = "/files/demandes/branchement/".$nomImage;
					$targetFile2 = "/files/demandes/branchement/".$nomImageBail;
					
					// $em = $this->getDoctrine()->getManager('customer');
					$em = $this->doctrine->getManager();
							
					$demande = new DemandeBranchement();
					$demande->setDateDemande(new \DateTime());
					$demande->setTypeDemande($type);
					$demande->setGerance($gerance);
					$demande->setAdresseLivraison($adresseLivraison);
					$demande->setCommune($commune);
					$demande->setPersonnalite($personnalite);
					$demande->setNom($nom);
					$demande->setRaisonSocial($raisonSocial);
					$demande->setRepresentant($representant);
					$demande->setIdentifiantF($identifiantF);
					$demande->setIce($ice);
					$demande->setCin($cin);
					$demande->setUsageBra($usage);
					$demande->setAdresseCorr($adresse);
					$demande->setTel($tel);
					$demande->setEmail($email);
					$demande->setCinFile($targetFile1);
					$demande->setBailFile($targetFile2);
					$demande->setCodeStatut(0);
					$demande->setStatut("En cours");
					$demande->setCommentaire("");
					$demande->setRefClient($refClient);
			
					$em->persist($demande);
					$em->flush();
					
					if(isset($_FILES['permis']) && !empty($_FILES['permis']['name'])){
						//-----------------------------
						$tmpFilePermis = $_FILES['permis']['tmp_name'];
						$extensionPermis = pathinfo($_FILES['permis']['name'],PATHINFO_EXTENSION);
						$nomImagePermis = date_format(new \DateTime(),"YmdHis").$cin."-permis.".$extensionPermis;
								
						$targetFilePermis = $storeFolder.$nomImagePermis;
						if(move_uploaded_file($tmpFilePermis,$targetFilePermis)){
							$targetFile3 = "/files/demandes/branchement/".$nomImagePermis;
							$demande->setPermisFile($targetFile3);
						}
					}
					
					if(isset($_FILES['statut']) && !empty($_FILES['statut']['name'])){
						//-----------------------------
						$tmpFileStatut = $_FILES['statut']['tmp_name'];
						$extensionStatut = pathinfo($_FILES['statut']['name'],PATHINFO_EXTENSION);
						$nomImageStatut = date_format(new \DateTime(),"YmdHis").$cin."-statut.".$extensionStatut;
								
						$targetFileStatut = $storeFolder.$nomImageStatut;
						if(move_uploaded_file($tmpFileStatut,$targetFileStatut)){
							$targetFile4 = "/files/demandes/branchement/".$nomImageStatut;
							$demande->setStatutFile($targetFile4);
						}
					}
					
					if(isset($_FILES['plan']) && !empty($_FILES['plan']['name'])){
						//-----------------------------
						$tmpFilePlan = $_FILES['plan']['tmp_name'];
						$extensionPlan = pathinfo($_FILES['plan']['name'],PATHINFO_EXTENSION);
						$nomImagePlan = date_format(new \DateTime(),"YmdHis").$cin."-plan.".$extensionPlan;
								
						$targetFilePlan = $storeFolder.$nomImagePlan;
						if(move_uploaded_file($tmpFilePlan,$targetFilePlan)){
							$targetFile5 = "/files/demandes/branchement/".$nomImagePlan;
							$demande->setPlanFile($targetFile5);
						}
					}
					
					$em->flush();
							
					$codeStatut = "OK";
				}
				else{
					$codeStatut = "ERR-IMG";
					$message = "Erreur lors de l'upload de l'image ! ";
				}
			}
						
        }catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

            if($e->getMessage() == "Expired token"){
                $codeStatut = "ERROR-TOKEN";
            }
			
			//$message = " ! :".$e->getMessage();
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
	
	/*----------------------------------------------*/
    /*----------------------------------------------*/
    /* Fonctions pour la récupération des demandes  */
    /*----------------------------------------------*/
    /*----------------------------------------------*/

    
    #[Route('/ws/agonline/demande/list/', methods: ['GET'])]
    public function listDemandesAction(Request $request)
    {

    	/*header("Access-Control-Allow-Origin: *");
		 header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("demandeAbonnement"=>array(),"demandeBranchement"=>array(),"demandeResiliation"=>array(),"demandeRemboursement"=>array());

    	//--------- Verifier la validité du JWT Token -----------//

    	try{

    		// $decoded = JWT::decode($jwt, $this->key, array('HS256'));
			$jwt = substr($jwt, 7);
			$decoded = $this->JWTManager->decode($jwt);


            $refClient = $decoded ["REF"];
            $cinClient = $decoded ["CIN"];

            if(empty($refClient) || empty($cinClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{

            	// $em = $this->getDoctrine()->getManager('customer');
				$em = $this->doctrine->getManager();
				$demandeAbonnement = $em->getRepository(DemandeAbonnement::class)->findByRefClient($refClient);
				
				$responseObjects["demandeAbonnement"] = $demandeAbonnement;
				
				//$demandeBranchement = $em->getRepository(DemandeBranchement::class)->findByRefClient($refClient);
				
				//$responseObjects["demandeBranchement"] = $demandeBranchement;
				
				$demandeResiliation = $em->getRepository(DemandeResiliation::class)->findByRefClient($refClient);
				
				$responseObjects["demandeResiliation"] = $demandeResiliation;
				
				//$demandeRemboursement = $em->getRepository(DemandeRemboursement::class)->findByRefClient($refClient);
				
				//$responseObjects["demandeRemboursement"] = $demandeRemboursement;
				

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

}