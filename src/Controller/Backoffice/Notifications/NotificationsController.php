<?php

namespace App\Controller\Backoffice\Notifications;
use App\Repository\Oracle\ConsommationRepository;
use App\Repository\Oracle\AbonnementsRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Notifications;
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

class NotificationsController extends AbstractController{



    public function __construct(
		private ManagerRegistry $doctrine,
		private JWTEncoderInterface $JWTManager,
		private AbonnementsRepository $AbonneRrepo,
        private ConsommationRepository $ConsoRepo,
        private Verify $Verify,

		) {}

    //-------------- Notifications ----------------//
	
	
    #[Route("/ws/agonline/back/notifications/add/", methods:["POST"])]
    public function addNotificationsAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/
		
		ini_set('max_execution_time', 0);
		
		define( 'API_ACCESS_KEY', 'AAAA_Y1823I:APA91bHKjclZavblqGhevxlyc5OOv1BnH0cZHXZHu4tJbvKAQ9wBzpG0HbxCzN9qVceSQ-3BgvYwdA6bfVu3rYRt-6eGQZh_Mp7vxsQrkIqylaD6psvxV9CAjmky5wcE04q-6wED4h2V' );


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
				$code = "ADDNOTIF";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$text = $request->get("text");
					
					if(empty($text)){
						$codeStatut = "ERROR-PARAMS";
						$message = "Le text est obligatoire !";
					}
					else{
						$em = $this->doctrine->getManager();
						
						
						$notif = new Notifications();
						$notif->setDateNotif(new \DateTime());
						$notif->setText($text);
						
						
						$em->persist($notif);
						$em->flush();
						
						
						
						//---------------- send -------------//
						
						$url = 'https://fcm.googleapis.com/fcm/send';
						
						
						$clients = $this->ConsoRepo->getMobileClient();
					
						
						$user_data = array();
						for($i=0;$i<count($clients);$i++){
							if(!empty($clients[$i]["INT_INFO4"])){
								$user_data = $clients[$i]["INT_INFO4"];
								$registrationIds = array($user_data);

								$data["notification"] = array(
									"title" => "Notification SRM",
									"body"=>$text,
									"sound"=>"default",
									"click_action"=>"FCM_PLUGIN_ACTIVITY",
									"icon"=>"fcm_push_icon"
								);
								$data["data"] = array(
									"title" => "Notification SRM",
									"body"=>$text,
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

                        
						
						//-----------------------------------//
						
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
	
	
    #[Route("/ws/agonline/back/notifications/get/all/", methods:["POST"])]
    public function getAllNotifsAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("notifications"=>array());

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
				$code = "CONSNOTIF";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$notifications = $em->getRepository(Notifications::class)->findAll();
					
					$responseObjects["notifications"] = $notifications;
					
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
	
	
    #[Route("/ws/agonline/back/notifications/delete/{id}/", methods:["POST"])]
    public function deleteNotificationsAction(Request $request, Connection $conn,$id)
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
				$code = "DELNOTIF";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$notifications = $em->getRepository(Notifications::class)->findOneById($id);
					
					if(!$notifications){
						$codeStatut = "ERROR-USER";
						$message = "Une erreur s'est produite !";
					}
					else{
						
						$em->remove($notifications);
						
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