<?php

namespace App\Controller\Agence\Authentication;
use App\Repository\Oracle\AuthentificationRepository;
use App\Repository\Oracle\AbonnementsRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\TokenPass;
use App\Entity\DemandeCreation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
//use App\Service\DataBaseService;
use App\Service\MailerService;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class AuthenticationController extends AbstractController
{


	// private $key = "RADEEJ@2021@PROXISOFT@AG_ONLINE";
    // private $iss = "https://www.radeej.ma";
    // private $aud = "https://www.radeej.ma";
    private $iat = 0;
    private $nbf = 0;
    private $exp = 0;
	
	private $contactEmail = "agence.radeej.regie@gmail.com";//reclamation.agence.radeej
	private $passwordEmail = "etuwduyiklfnmgtx";
	private $smtpEmail = 'smtp.googlemail.com';
    
    /*----------------------------------------------------*/
    /*----------------------------------------------------*/
    /* Fonction pour authentification à l'agence en ligne */
    /*----------------------------------------------------*/
    /*----------------------------------------------------*/

	public function __construct(
		private ManagerRegistry $doctrine,
		private MailerService $mailer,
		private AuthentificationRepository $AuthRepo,
		private AbonnementsRepository $AbonneRepo,
		private JWTEncoderInterface $JWTManager
		) {}


		

	
	


	#[Route('/ws/agonline/authentication/', methods: ['POST'])]
    public function authenticationAction(Request $request,  )
    {


        /*header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Authorization");*/

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $tokenJWT = "";
        $codeStatut = "ERROR";
        $message = "";

        $responseObjects = array();
        $cl = array();
        try{
            $cin = trim($request->get("cin"));
            $password = $request->get("password");
            $cin = str_replace(" ","",$cin);
            $cin = str_replace(".","",$cin);

            if(empty($cin) || empty($password)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
                $message = "Le Nom d'utilisateur et le mot de passe sont obligatoires !";
            }
            else{
                //$databaseService = new DataBaseService($this->conn);
                $client = $this->AuthRepo->authenticate($cin,$password);//sha1($password)
                if($client){
                    if($client["MAIL_AUTORISE"] == 1){

                        
                        $cl = array(
                            "REF_CLIENT"=>$client["REF_CLIENT"],
                            "NOM"=>$client["NOM_PRENOM"],
                            "CIN"=>$client["CIN"],
                            "MAIL"=>$client["MAIL"],
                            "ADRESSE"=>$client["ADRESSE"],
                            "DATECRE"=>$client["DATE_CREATION"],
                            "TEL"=>$client["TEL"],
                            "NATURE"=>$client["NATURE"],
                            "ICE"=>$client["ICE"]
                            );
                        
                       
                        
                        $data = array(
                                "REF" => $client["REF_CLIENT"],
                                "CIN" => $client["CIN"]);


                        // $jwt = JWT::encode($token, $this->key);
                        $jwt = $this->JWTManager->encode($data);

                        $tokenJWT = $jwt;
                        

                        //---------- Récupérer la liste des dossiers (Police) ------------//

                        $abonnements = $this->AbonneRepo->getAbonnementsByCin($client["CIN"]);

                        $responseObjects["abonnements"] = $abonnements;

                        //---------------------------------------------------------------//

                        $codeStatut = "OK";
                    }
                    else{
                        $codeStatut = "ERROR-VALID-ACCOUNT";
                        $message = "Le compte client n'est pas encore activé";
                    }
                }
                else{
                    $codeStatut = "ERROR-WRONG-PARAMS";
                    $message = "Nom d'utilisateur ou mot de passe incorrect !".$client;
                }
            }
        }catch (\Exception $e) {

            $codeStatut = "ERROR";
        }
        

        $resp["codeStatut"] = $codeStatut;
        $resp["message"] = $message;
        $resp["token"] = $tokenJWT;
        $resp["client"] = $cl;
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }


	#[Route('/ws/agonline/verifyCin/', methods: ['GET'])]
    public function verifyCinAction(Request $request)
    {

    	/*header("Access-Control-Allow-Origin: *");
		 header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$responseObjects = array();

    	//--------- Verifier la validité du JWT Token -----------//

    	try{
			$cin = trim($request->get("cin"));
			//$databaseService = new DataBaseService($this->conn);
			$client = $this->AuthRepo->getClientByCin($cin);
			
			if($client){
				$codeStatut = "CIN-OK";
			}
			else{
				$codeStatut = "CIN-NO";
			}
		
			
    		
    	}catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

        }

    	//-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }

	#----------------fonction pour domender de creation d'un account----------#
	#[Route('/ws/agonline/creation/account')]
	public function Demandcreation(Request $request){
		$response = new Response();
		$response->headers->set('Content-Type', 'application/json');
 
		$codeStatut = "ERROR";
		$message = "";
		$em = $this->doctrine->getManager();
		try{
			$cin = trim($request->get("cin"));
            $police = trim($request->get("police"));
			if(empty($police)){//empty($cin) || 
				$codeStatut = "ERR-EMPTY";
				$message = "Veuillez saisir une police ! ";
			}else{
				
				//$cinExist = $em->getRepository(DemandeCreation::class)->findByCin($cin);
				$policeExist = $em->getRepository(DemandeCreation::class)->findByPolice($police);
				if($policeExist){// && $policeExist 
					$codeStatut = "ERR-Operation-En-Cours";
					$message = "Vous avez déja une demande d'activation de compte en cours";
				}else{
					//------------------------------
					//$verifyClient = $this->AuthRepo->verifyPolice($cin, $police);
					$verifyClient = $this->AuthRepo->getClientByPolice($police);
					if($verifyClient){
						$demandcreation = new DemandeCreation();
						$demandcreation->setCin($verifyClient['CIN']);
						$demandcreation->setPolice($police);
						$demandcreation->setDatedemande(new \DateTime());
						$em->persist($demandcreation);
						$em->flush();
						$codeStatut = "OK";
					}
					else{
						$codeStatut = "BO-USER";
						$message = "Veuillez vérifier vos informations, police incorrecte !";
					}
					//
					
				}
				
			}
			
		}catch(\Exception $e){
			$codeStatut = "ERROR-EXCEPETION---".$e->getMessage();
			$message = "Une erreur s'est produite !";
		}
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

	


	#[Route('/ws/agonline/client/password/forget/', methods: ['POST'])]
    public function forgetPasswordAction(Request $request, )
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";
		$message = "";
    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("attestation"=>array(),"client"=>array());

    	//--------- Verifier la validité du JWT Token -----------//

    	try{

			$email = $request->get("email");
			$tel = $request->get("tel");
			
			if(empty($email)){
				$codeStatut = "ERROR-EMPTY-MAIL";
				$message = "Veuillez saisir une adresse mail ou un n° de téléphone";
			}
			else{
				
				$isTel = false;
				$isMail = false;
				
		
				if (filter_var($tel, FILTER_VALIDATE_EMAIL)) {
					$isMail = true; 
					
				}
				
				$telForSend = "";
				if (preg_match('#^0[0-9]([ .-]?[0-9]{2}){4}$#', $tel) == true) {
					$tel = str_replace(" ","",$tel);
					$tel = str_replace("-","",$tel);
					$isTel = true; 
					$telForSend = "+212".substr($tel, 1);
					  
				}
				else if (preg_match('#^(\+212)[ .-]?[0-9]([ .-]?[0-9]{2}){4}$#', $tel) == true) {
					$tel = str_replace(" ","",$tel);
					$tel = str_replace("-","",$tel);
					$isTel = true;
					$telForSend = $tel;
				}
				else if (preg_match('#^(00212)[ .-]?[0-9]([ .-]?[0-9]{2}){4}$#', $tel) == true) {
					$tel = str_replace(" ","",$tel);
					$tel = str_replace("-","",$tel);
					$isTel = true;
					$telForSend = "+".substr($tel, 2);
				}
				
				if($isTel || $isMail){
					//$databaseService = new DataBaseService($this->conn);
					//$email = "proxisoft.maroc@gmail.com";
					$client = $this->AuthRepo->getClientByEmailOrTel($email,$tel);
					//----------------------------------------------------------
					$ifAllOK = false;
					if(($client && $isMail) || ($client && $isTel)){
						$ifAllOK = true;
					}
					
					if($ifAllOK){
						if($isMail){
							/*$em = $this->getDoctrine()->getManager('customer');
							$tokenPass = $em->getRepository(TokenPass::class)->findOneBy(array("email"=>sha1($email)));
									
							if($tokenPass){
								$token = $tokenPass->getToken();
								$tokEmail = sha1($email);
							}
							else{
								
								$strTok = date_format(new \DateTime(),"YmdHis").$email;
								$token = sha1($strTok);
								$tokEmail = sha1($email);
								
								$tokenPass = new TokenPass();
								$tokenPass->setDateDem(new \DateTime());
								$tokenPass->setEmail($tokEmail);
								$tokenPass->setToken($token);
								$tokenPass->setRefClient($client["REF_CLIENT"]);
								
								$em->persist($tokenPass);
								$em->flush();

							}*/
							
							//---------------- send email --------------//
							
							$email = $client["MAIL"];
							$tel = $client["TEL"];
							
							$chars = '0123456789';
							$randomString = '';
							for ($i = 0; $i < 6; $i++)
							{
								$randomString .= $chars[rand(0, strlen($chars)-1)];
							}
							$pass1 = $randomString;
								
							$isC = $this->AuthRepo->createClient($client,$email,$tel,$pass1,sha1($client['POLICE']),$client["INT_INFO4"],$client["INT_INFO3"],1);
										
							if($isC){
								$telForSend = str_replace("+","",$telForSend);
								$msg = "Bonjour ".$client["NOM_PRENOM"].", Suite à votre requête, votre nouveau mot de passe est : ".$pass1.".";
								//----- send SMS
								$codeStatut = $mailer->sendEmailTwig($email,$client["NOM_PRENOM"],2,$pass1);
							}
							
							//$url = "http://agence.radeej.ma:92/password/reset/".$tokEmail.'/'.$token.'/';
							$url = "";
							
						
							//------------------------------------------//
							
							$message = "Un email vous a été envoyé avec votre nouveau mot de passe";
						}
						
						if($isTel){
							$email = $client["MAIL"];
							
							$chars = '0123456789';
							$randomString = '';
							for ($i = 0; $i < 6; $i++)
							{
								$randomString .= $chars[rand(0, strlen($chars)-1)];
							}
							$pass1 = $randomString;
								
							$isC = $this->AuthRepo->createClient($client,$email,$tel,$pass1,sha1($client['POLICE']),$client["INT_INFO4"],$client["INT_INFO3"],1);
										
							if($isC){
								$telForSend = str_replace("+","",$telForSend);
								$msg = "Bonjour ".$client["NOM_PRENOM"].", Suite à votre requête, votre nouveau mot de passe est : ".$pass1.".";
								//----- send SMS
								$codeStatut = $this->sendSMSToClient($telForSend,$msg);
							}
							
							$message = "Un SMS vous a été envoyé avec votre nouveau mot de passe";
						}

						$codeStatut = "OK";
					}
					else{
						$codeStatut = "ERROR-CL-MAIL-TEL";
						$message = "N° de téléphone ou email incorrect !";
					}
				}
				else{
					$codeStatut = "ERROR-MAIL-TEL";
					$message = "N° de téléphone ou email incorrect !";
				}
				
				
				
				
			}
    		
    	}catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

        }

    	//-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
        $resp["message"] = $message;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }
	

	#[Route('/ws/agonline/client/password/verify/token/{tokenMail}/{token}/', methods: ['GET'])]
    public function verifyTokenPasswordAction(Request $request, $tokenMail="",$token = "")
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";
		$message = "";
    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("attestation"=>array(),"client"=>array());

    	//--------- Verifier la validité du JWT Token -----------//

    	try{

			// $em = $this->getDoctrine()->getManager('customer');
			$em = $this->doctrine->getManager();
			$tokenPass = $em->getRepository(TokenPass::class)->findOneBy(array("email"=>$tokenMail,"token"=>$token));
							
			if($tokenPass){
				$codeStatut = "OK";
			}
    		
    	}catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

        }

    	//-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
        $resp["message"] = $message;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }


	#[Route('/ws/agonline/client/activate/{token}/{ref}/', methods: ['POST'])]
    public function activateAccountAction(Request $request, $token,$ref)
    {


        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "ERROR";
		$message = "";

        
        $responseObjects = array();

        //--------- Verifier la validité du JWT Token -----------//

        try{
			//$databaseService = new DataBaseService($this->conn);
            $client = $this->AuthRepo->getClientByRef($ref);
			if($client){
					
					
					if($token != $client["INT_INFO5"]){
						$codeStatut = "ERROR-TOKEN";
						$message = "Impossible d'activer le compte, veuillez réessayer !";
					}
					else{
						// token device and device name
						$isC = $this->AuthRepo->createClient($client,$client["MAIL"],$client["TEL"],$client["MOT_DE_PASSE"],sha1($client['POLICE']),$client["INT_INFO4"],$client["INT_INFO3"],1);
										
						if($isC){
							$codeStatut = "OK";
						}
						else{
							$codeStatut = "ERROR-PR";
							$message = "Une erreur s'est produite !";
						}
					}
				}
				else{
					$codeStatut = "ERROR-CLIENT";
					$message = "Une erreur s'est produite !";
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

	/*----------------------------------------------------*/
    /*----------------------------------------------------*/
    /* Fonction pour modifier le mot de passe d'un client */
    /*----------------------------------------------------*/
    /*----------------------------------------------------*/


	#[Route('/ws/agonline/client/update/', methods: ['POST'])]
    public function updateClientAction(Request $request)
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

            //$decoded = JWT::decode($jwt, $this->key, array('HS256'));
			$jwt = substr($jwt, 7);
			$decoded = $this->JWTManager->decode($jwt);


            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];

            if(empty($refClient) || empty($cinClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{

                //$databaseService = new DataBaseService($this->conn);
                $client = $this->AuthRepo->getClientByRef($refClient);
				if($client){
					//$email = $request->get("email");
					//$tel = $request->get("tel");
					$oldPass = $request->get("oldPass");
					$pass1 = $request->get("pass1");
					$pass2 = $request->get("pass2");
					
					/*$ifEmailValid = false;
					$ifTelValide = false;
					$ifPasswordValide = true;
					
					if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$ifEmailValid = false;
						$codeStatut = "ERROR-PASS";
						$message = "L'adresse email est invalide !";
					}
					else{
						$ifEmailValid = true;
					}
					
					if(!empty($tel)){
						$ifTelValide = true;
						//----- validation N° de téléphone
					}*/
					
					if(!empty($oldPass) && !empty($pass1)){
						if($oldPass != $client["MOT_DE_PASSE"]){ //sha1($oldPass)
							$codeStatut = "ERROR-PASS";
							$message = "L'ancien mot de passe est incorrect !";
							$ifPasswordValide = false;
						}
						else{
							//-- Verifier le nombre de caractère du mot de passe (Min 8)
							if(strlen($pass1) < 8){
								$codeStatut = "ERROR-INVALIDLEN-PASS";
								$message = "Le mot de passe doit comprendre 8 caractère au minimum";
								$ifPasswordValide = false;
							}
							else{
								//-------- Verifier si les deux mots de passe sont identique
								if($pass1 != $pass2){
									$codeStatut = "ERROR-INVALIDTW-PASS";
									$message = "Les deux mots de passe ne sont pas identiques";
									$ifPasswordValide = false;
								}
								else{
									$isC = $this->AuthRepo->createClient($client,$client["TEL"],$client["MAIL"],$pass1,sha1($client['POLICE']),$client["INT_INFO4"],$client["INT_INFO3"],1);
						
						
									if($isC){
										$codeStatut = "OK";
									}
									else{
										$codeStatut = "ERROR-PR";
										$message = "Une erreur s'est produite !";
									}
								}
							}
						}
					
					}
					
					/*if($ifEmailValid && $ifTelValide && $ifPasswordValide){
						if(empty($oldPass)){
							$isC = $this->AuthRepo->createClient($client,$email,$tel,$client["MOT_DE_PASSE"],sha1($client['POLICE']),$client["INT_INFO4"],$client["INT_INFO3"],1);
						}
						else{
							$isC = $this->AuthRepo->createClient($client,$email,$tel,$pass1,sha1($client['POLICE']),$client["INT_INFO4"],$client["INT_INFO3"],1);
						}
						
						if($isC){
							$codeStatut = "OK";
						}
						else{
							$codeStatut = "ERROR-PR";
							$message = "Une erreur s'est produite !";
						}
					}*/
				}
				else{
					$codeStatut = "ERROR-CLIENT";
					$message = "Une erreur s'est produite !";
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


	

	#[Route('/ws/agonline/client/password/update/{tokenMail}/{token}/', methods: ['POST'])]
    public function updatePasswordClientAction(Request $request,$tokenMail="",$token = "")
    {


        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "ERROR";
		$message = "";

        $responseObjects = array();

        //--------- Verifier la validité du JWT Token -----------//

        try{
			$pass1 = $request->get("pass1");
			$pass2 = $request->get("pass2");
					
			//-- Verifier le nombre de caractère du mot de passe (Min 8)
			if(strlen($pass1) < 8){
				$codeStatut = "ERROR-INVALIDLEN-PASS";
				$message = "Le mot de passe doit comprendre 8 caractère au minimum";
			}
			else{
				//-------- Verifier si les deux mots de passe sont identique
				if($pass1 != $pass2){
					$codeStatut = "ERROR-INVALIDTW-PASS";
					$message = "Les deux mots de passe ne sont pas identiques";
				}
				else{
					// $em = $this->getDoctrine()->getManager('customer');
					$em = $this->doctrine->getManager();
					$tokenPass = $em->getRepository(TokenPass::class)->findOneBy(array("email"=>$tokenMail,"token"=>$token));
									
					if($tokenPass){
						//$databaseService = new DataBaseService($this->conn);
						$client = $this->AuthRepo->getClientByRef($tokenPass->getRefClient());
						
						if(!$client){
							$codeStatut = "ERROR-CLT";
							$message = "Une erreur s'est produite !";
						}
						else{
							 $isC = $this->AuthRepo->createClient($client,$client["MAIL"],$client["TEL"],$pass1,sha1($client['POLICE']),$client["INT_INFO4"],$client["INT_INFO3"],1);
										
							if($isC){
							 	$codeStatut = "OK";
								
							 	$em->remove($tokenPass);
							 	$em->flush();
							}
							else{
								$codeStatut = "ERROR-PR";
								$message = "Une erreur s'est produite !";
							}

						 }
					}
					else{
						$codeStatut = "ERROR-TOKEN";
						$message = "Une erreur s'est produite !";
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


	
	private function sendSMSToClient($tel,$msg){
		
		
		try{
			
			/*$url = "https://10.128.147.141:9097/ws/sms";

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$headers = array(
			   "Content-Type: text/xml"
			);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

			$data = "  
					<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:sms=\"http://com/comviva/ngage/ws/sms\" xmlns:sms1=\"http://sms.soap.ngage.comviva.com\">
					<soapenv:Header>
						<sms:TransactionID></sms:TransactionID>
					</soapenv:Header>
					<soapenv:Body>
						<sms:SMSSubmitRequest>
							<sms:req>
								<sms1:CampaignName>KHADAMAT ligne</sms1:CampaignName>
								<sms1:CampaignDesc></sms1:CampaignDesc>
								<sms1:Sender>
									<sms1:Username>agenceradeej</sms1:Username>
									<sms1:Password>3wUcWtvjKGwJ@</sms1:Password>
									<sms1:Address>RADEEJ</sms1:Address>
								</sms1:Sender>
								<sms1:ProtocolId></sms1:ProtocolId>
								<sms1:CampaignCategory>4.6</sms1:CampaignCategory>
								<sms1:PromotionalCategory>information</sms1:PromotionalCategory>
								<sms1:ContentType>3.7</sms1:ContentType>
								<sms1:CallBackURL></sms1:CallBackURL>
								<sms1:ScheduledDeliveryDateTime></sms1:ScheduledDeliveryDateTime>
								<sms1:ValidityPeriodDateTime></sms1:ValidityPeriodDateTime>
								<sms1:DeliveryReport>true</sms1:DeliveryReport>
								<sms1:JobType>
									<sms1:Simple>
										<sms1:MsgDetails>
											<sms1:ShortMessage>".$msg."</sms1:ShortMessage>
										</sms1:MsgDetails>
										<sms1:Recipient>
											<sms1:Number>".$tel."</sms1:Number>
											<sms1:DistributionList></sms1:DistributionList>
											<sms1:SegmentList></sms1:SegmentList>
											<sms1:Contact></sms1:Contact>
											<sms1:ContactGroup></sms1:ContactGroup>
											<sms1:FileURL></sms1:FileURL>
										</sms1:Recipient>
									</sms1:Simple>
									<sms1:File>
										<sms1:DataFileURL></sms1:DataFileURL>
									</sms1:File>
									<sms1:Placeholders>
										<sms1:MsgDetails>
											<sms1:ShortMessage></sms1:ShortMessage>
										</sms1:MsgDetails>
										<sms1:Recipient>
											<sms1:DataFileURL></sms1:DataFileURL>
										</sms1:Recipient>
									</sms1:Placeholders>
								</sms1:JobType>
								<sms1:FixedTPS></sms1:FixedTPS>
							</sms:req>
						</sms:SMSSubmitRequest>
					</soapenv:Body></soapenv:Envelope>";

			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

			//for debug only!
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			$resp = curl_exec($curl);
			curl_close($curl);
			return $resp;*/
			return true;
            
        }catch (\Exception $e) {

            return null;
        }

		
    }

}









