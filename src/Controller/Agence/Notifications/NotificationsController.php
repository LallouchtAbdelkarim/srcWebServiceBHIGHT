<?php
namespace App\Controller\Agence\Notifications;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Notifications;
use App\Entity\NotificationsLu;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use App\Service\MailerService;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class NotificationsController extends AbstractController
{




	public function __construct(
		private ManagerRegistry $doctrine,
		private Connection $conn,
		private MailerService $mailer,
		private JWTEncoderInterface $JWTManager
		) {}




	#[Route('/ws/agonline/contact/send/', methods: ['POST'])]
    public function sendEmailContactAction(Request $request,)
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
				
                $nom = $request->get("nom");
				$tel = $request->get("tel");
				$email = $request->get("email");
				$objet = $request->get("objet");
				$msg = $request->get("msg");
				
				if(empty($nom) || empty($tel) || empty($objet) || empty($email) || empty($msg)){
					$codeStatut = "ERR-EMPTY";
					$message = "Tous les champs sont obligatoires ! ";
				}
				else{
					// Create the Transport
					// $transport = (new \Swift_SmtpTransport($this->smtpEmail, 465, 'ssl'))
					//   ->setUsername($this->contactEmail)
					//   ->setPassword($this->passwordEmail)
					// ;
				 
					// // Create the Mailer using your created Transport
					// $mailer = new \Swift_Mailer($transport);
				 
					// // Create a message
					$body = 'Nom : '.$nom."<br/>Téléphone : ".$tel.'<br/>Email : '.$email.'<br/>Message : <br/>'.$msg;
				 
					// $messageMail = (new \Swift_Message($objet))
					//   ->setFrom(['agence.radeej.regie@gmail.com' => 'KHADAMAT RADEEJ'])
					//   ->setTo(['communication@radeej.ma'])
					//   ->setBody($body)
					//   ->setContentType('text/html')
					// ;
				 
					// // Send the message
					$mailer->sendEmail($body,$objet);
					// //---------------- send email ------------//
					$codeStatut = "OK";
					
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




   

	#[Route('/ws/agonline/notifications/get/', methods: ['GET'])]
    public function getNotificationsAction(Request $request)
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";
		$message = "";
    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("notifications"=>array());

    	//--------- Verifier la validité du JWT Token -----------//

    	try{
			
			// $decoded = JWT::decode($jwt, $this->key, array('HS256'));
			$jwt = substr($jwt, 7);
			$decoded = $this->JWTManager->decode($jwt);


            // $refClient = $decoded->data->REF;
            // $cinClient = $decoded->data->CIN;
            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];

            if(empty($refClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{
				$cn = $this->doctrine->getManager('customer')->getConnection();
				//$notifications = $em->getRepository(Notifications::class)->findAll();
				
				$req = "SELECT n.*, (SELECT nbrLecture FROM notificationsLu nl where nl.codeClient = :codeClient AND nl.idNotification = n.id) nbrLecture FROM notifications n WHERE codeClient = :codeClient or codeClient is null ORDER BY dateNotif DESC";
				$parms = array("codeClient"=>$refClient);
				$stmtReq = $cn->prepare($req);
				$stmtReq = $stmtReq->executeQuery($parms);
				$notifications =$stmtReq->fetchAllAssociative();

				// $notifications = array();
				// while ($row = $stmtReq->fetch()) {
				// 	$notifications[] = $row;
				// }
				
				$responseObjects["notifications"] = $notifications;
				$codeStatut = "OK";
			}
    		
    	}catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

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
	
	
	#[Route('/ws/agonline/notifications/set/lu/', methods: ['POST'])]
    public function setNotificationsLuAction(Request $request)
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
			
			// $decoded = JWT::decode($jwt, $this->key, array('HS256'));
			$jwt = substr($jwt, 7);
			$decoded = $this->JWTManager->decode($jwt);


            // $refClient = $decoded->data->REF;
            // $cinClient = $decoded->data->CIN;
            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];
			$idNotif = $request->get("idNotif");
            if(empty($refClient) || empty($idNotif)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{
				
				// $em = $this->getDoctrine()->getManager('customer');
				$em = $this->doctrine->getManager();
				$notificationsLu = $em->getRepository(NotificationsLu::class)->findOneBy(array("codeClient"=>$refClient,"idNotification"=>$idNotif));
				if($notificationsLu){
					$notificationsLu->setDateLecture(new \DateTime());
					$notificationsLu->setNbrLecture($notificationsLu->getNbrLecture()+1);
				}
				else{
					$notificationsLu = new NotificationsLu();
					$notificationsLu->setDateLecture(new \DateTime());
					$notificationsLu->setNbrLecture($notificationsLu->getNbrLecture()+1);
					$notificationsLu->setCodeClient($refClient);
					$notificationsLu->setIdNotification($idNotif);
					
					$em->persist($notificationsLu);
				}
				
				$em->flush();
				
				$codeStatut = "OK";
			}
    		
    	}catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

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
	


	#[Route('/ws/agonline/notifications/get/nonLu/', methods: ['GET'])]
    public function getNotificationsNonLuAction(Request $request)
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";
		$message = "";
    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("notifications"=>array());

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
				$cn = $this->doctrine->getManager('customer')->getConnection();
				//$notifications = $em->getRepository(Notifications::class)->findAll();
				
				$req = "SELECT COUNT(n.id) as NBR FROM notifications n WHERE (n.codeClient = :codeClient or n.codeClient is null) AND n.id not in (SELECT nl.idNotification FROM notificationsLu nl where nl.codeClient = :codeClient AND nl.idNotification = n.id) ";

				$parms = array("codeClient"=>$refClient);
				$stmt = $cn->prepare($req);
				$stmt = $stmt->executeQuery($parms);
				$nbrNotLu = $stmt->fetchAssociative();
				
				$responseObjects["nbr"] = $nbrNotLu["NBR"];
				$codeStatut = "OK";
			}
    		
    	}catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

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


	#[Route('/ws/agonline/testsms/', methods: ['GET'])]
    public function testsmsAction(Request $request,)
    {


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "ERROR";

        $responseObjects = array("abonnements"=>array());

        //--------- Verifier la validité du JWT Token -----------//

        // try{
			

			
		// 	$email = "hadmi.youssef@proxisoft.ma";
		// 	$message = (new \Swift_Message("Test"))
        //     ->setFrom(['khadamat@radeej.ma' => 'KHADAMAT RADEEJ'])
        //     ->setTo($email)
		// 	->setBody("Test de message");
			
		// 	$codeStatut = $mailer->send($message);
			
			
            
        // }catch (\Exception $e) {

        //     $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

        //     if($e->getMessage() == "Expired token"){
        //         $codeStatut = "ERROR-TOKEN";
        //     }
        // }

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