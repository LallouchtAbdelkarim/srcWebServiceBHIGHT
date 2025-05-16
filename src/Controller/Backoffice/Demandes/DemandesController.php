<?php

namespace App\Controller\Backoffice\Demandes;
use App\Repository\Oracle\AuthentificationRepository;
use App\Repository\Oracle\AbonnementsRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\TokenPass;
use App\Entity\Notifications;
use App\Entity\DemandeAbonnement;
use App\Entity\DemandeBranchement;
use App\Entity\DemandeCreation;
use App\Entity\DemandeResiliation;
use App\Entity\DemandeRemboursement;
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

class DemandesController extends AbstractController{

    public function __construct(
		private ManagerRegistry $doctrine,
		private JWTEncoderInterface $JWTManager, 
        private AuthentificationRepository $AuthRepo,   
        private Verify $Verify,

		) {}

   
    #[Route("/ws/agonline/back/demande/abonnement/get/{id}/", methods:["GET"])]
    public function getDemandeAbonnementAction(Request $request,$id)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("demande"=>array());

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
				$code = "CONSDEMABN";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$demandeAbonnement = $em->getRepository(DemandeAbonnement::class)->findOneById($id);
					
					$responseObjects["demande"] = $demandeAbonnement;
					
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
	
	
    #[Route("/ws/agonline/back/demandes/abonnement/get/all/", methods:["GET"])]
    public function getAllDemandeAbonnementAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("demande"=>array());

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
				$code = "CONSDEMABN";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$demandeAbonnement = $em->getRepository(DemandeAbonnement::class)->findAll();
					
					$responseObjects["demande"] = $demandeAbonnement;
					
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

    #[Route("/ws/agonline/back/demandes/creation/get/all/", methods:["GET"])]
    public function getAllDemandecreationAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("demande"=>array());

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
				$code = "CONSDEMABN";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$demandeCreation = $em->getRepository(DemandeCreation::class)->findAll();
					
					$responseObjects["demande"] = $demandeCreation;
					
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

    //-----------!!!!!--
    #[Route("/ws/agonline/back/demande/creation/active/", methods:["POST"])]
    public function demandecreationActiveAction(Request $request){


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array();
        $data=$request->get("data");
        
        try{
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            $idAdmin = $decoded["ID"];
            $trv = $decoded["TRV"];
            if(empty($idAdmin)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{
                $code = "CONSDEMBRA";
				if($this->Verify->verifyRole($code,$idAdmin)){
                    $em = $this->doctrine->getManager();
                    $data = json_decode(stripslashes($data));
                    foreach($data as $d){
                        $client = $em->getRepository(DemandeCreation::class)->findOneBy(array("id"=>$d));
                        $pass= $this->Verify->randomPassword();
                        $client->setPassword($pass);
                        $client->setDateactivation(new \DateTime());
                        $client->setEtat(1);
                        $em->flush();
                        //$mdp=sha1($pass);
						$mdp=$pass;
                        //$client the full obj
                        //$clents =$this->AuthRepo->getClientByCin($client["CIN"]);
                        $clents =$this->AuthRepo->getClientByCin($client->getCin());
                        $this->AuthRepo->createClient($clents,$email="",$tel="",$mdp,$token="",$tokenDevice="",$deviceName="",1);
                    }
                    $codeStatut = "OK";
                    	
				}
				else{
					$codeStatut = "ERROR-ROLE";
					$message = "Vous n'êtes pas autorisé à effectuer cette opération !";
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
    //--------------------------------


	
    #[Route("/ws/agonline/back/demande/branchement/get/{id}/", methods:["GET"])]
    public function getDemandeBranchementAction(Request $request, $id)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("demande"=>array());

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
				$code = "CONSDEMBRA";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$demandeBranchement = $em->getRepository(DemandeBranchement::class)->findOneById($id);
					
					$responseObjects["demande"] = $demandeBranchement;
					
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
	
	
    #[Route("/ws/agonline/back/demandes/branchement/get/all/", methods:["GET"])]
    public function getAllDemandeBranchementAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("demande"=>array());

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
				$code = "CONSDEMBRA";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$demandeBranchement = $em->getRepository(DemandeBranchement::class)->findAll();
					
					$responseObjects["demande"] = $demandeBranchement;
					
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

    #[Route("/ws/agonline/back/demande/resiliation/get/{id}/", methods:["GET"])]
    public function getDemandeResiliationAction(Request $request,$id)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("demande"=>array());

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
				$code = "CONSDEMRES";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$demandeResiliation = $em->getRepository(DemandeResiliation::class)->findOneById($id);
					
					$responseObjects["demande"] = $demandeResiliation;
					
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
	
	
    #[Route("/ws/agonline/back/demandes/resiliation/get/all/", methods:["GET"])]
    public function getAllDemandeResiliationAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("demande"=>array());

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
				$code = "CONSDEMRES";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$demandeResiliation = $em->getRepository(DemandeResiliation::class)->findAll();
					
					$responseObjects["demande"] = $demandeResiliation;
					
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


    
    #[Route("/ws/agonline/back/demande/remboursement/get/{id}/", methods:["GET"])]
    public function getDemandeRemboursementAction(Request $request,$id)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("demande"=>array());

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
				$code = "CONSDEMREM";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$demandeResiliation = $em->getRepository(DemandeRemboursement::class)->findOneById($id);
					
					$responseObjects["demande"] = $demandeResiliation;
					
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
	
	
    #[Route("/ws/agonline/back/demandes/remboursement/get/all/", methods:["GET"])]
    public function getAllDemandeRemboursementAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("demande"=>array());

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
				$code = "CONSDEMREM";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$demandeResiliation = $em->getRepository(DemandeRemboursement::class)->findAll();
					
					$responseObjects["demande"] = $demandeResiliation;
					
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


   
    #[Route("/ws/agonline/back/demandes/update/statut/{id}/{type}/", methods:["POST"])]
    public function updateStatutDemandeAction(Request $request,$id,$type)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/

		ini_set('max_execution_time', 0);
		
		define( 'API_ACCESS_KEY', 'AAAA_Y1823I:APA91bHKjclZavblqGhevxlyc5OOv1BnH0cZHXZHu4tJbvKAQ9wBzpG0HbxCzN9qVceSQ-3BgvYwdA6bfVu3rYRt-6eGQZh_Mp7vxsQrkIqylaD6psvxV9CAjmky5wcE04q-6wED4h2V' );


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("releve"=>array());

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
				$code = "TRAIDEM";
				if($this->Verify->verifyRole($code,$idAdmin)){
					if($type < 1 || $type > 4){
						$codeStatut = "ERROR-TYPE";
						$message = "Une erreur s'est produite !";
					}
					else{
						$cSt = $request->get("statut");
						$commentaire = $request->get("commentaire");
						if($cSt < 0 || $cSt > 2){
							$codeStatut = "ERROR-TYPE";
							$message = "Une erreur s'est produite !";
						}
						else{
							$demande = null;
							
							$em = $this->doctrine->getManager();
							if($type == 1)
								$demande = $em->getRepository(DemandeAbonnement::class)->findOneById($id);
							
							if($type == 2)
								$demande = $em->getRepository(DemandeBranchement::class)->findOneById($id);
							
							if($type == 3)
								$demande = $em->getRepository(DemandeResiliation::class)->findOneById($id);
							
							if($type == 4)
								$demande = $em->getRepository(DemandeRemboursement::class)->findOneById($id);
							
							if(!$demande){
								$codeStatut = "ERROR-TYPE";
								$message = "Une erreur s'est produite !";
							}
							else{
								$statut = "";
								if($cSt == 0)
									$statut = "En cours";
								if($cSt == 1)
									$statut = "Validée";
								if($cSt == 2)
									$statut = "Rejetée";
								
								$demande->setCodeStatut($cSt);
								$demande->setStatut($statut);
								$demande->setCommentaire($commentaire);
								
								if(!empty($commentaire)){
									
									$notif = new Notifications();
									$notif->setDateNotif(new \DateTime());
									$notif->setText($commentaire);
									$notif->setCodeClient($demande->getRefClient());
									
									
									$em->persist($notif);
					
									//---------------- send -------------//
									
									$url = 'https://fcm.googleapis.com/fcm/send';
									
									
									$clients = $this->AuthRepo->getClientByRef($demande->getRefClient());
								
									
									$user_data = array();
									if($clients != null && !empty($clients["INT_INFO4"])){
										$user_data = $clients["INT_INFO4"];
										$registrationIds = array($user_data);

										$data["notification"] = array(
											"title" => "Notification SRM",
											"body"=>$commentaire,
											"sound"=>"default",
											"click_action"=>"FCM_PLUGIN_ACTIVITY",
											"icon"=>"fcm_push_icon"
										);
										$data["data"] = array(
											"title" => "Notification SRM",
											"body"=>$commentaire,
										);
										$data["to"] = $user_data;
										$data["priority"] = "high";
										$data["restricted_package_name"] = "";


										$headers = array
										(
											'Authorization: key=' . API_ACCESS_KEY,
											'Content-Type: application/json'
										);

										$ch = curl_init();
										curl_setopt( $ch,CURLOPT_URL, $url );
										curl_setopt( $ch,CURLOPT_POST, true );
										curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
										curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
										curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
										curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $data ) );
										$result = curl_exec($ch );
										curl_close( $ch );
											
										}
								}
								
								$em->flush();
								
								
								
								$codeStatut = "OK";
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

}