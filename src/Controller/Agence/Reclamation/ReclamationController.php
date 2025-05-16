<?php

namespace App\Controller\Agence\Reclamation;
use App\Repository\Oracle\AuthentificationRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Reclamation;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

use App\Service\Mailer;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;


class ReclamationController extends AbstractController
{
    

	



    // public function __construct(private ManagerRegistry $doctrine, private Mailer $mailer) {}
    public function __construct(
		private ManagerRegistry $doctrine,
		private JWTEncoderInterface $JWTManager,
        private AuthentificationRepository $AuthRepo,
        private MailerService $mailer,
		) {}

 
        
   #[Route('/ws/agonline/reclamation/get/', methods: ['GET'])]
   public function getReclamationsAction(Request $request)
   {

       /*header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Authorization");*/


       $response = new Response();
       $response->headers->set('Content-Type', 'application/json');

       $codeStatut = "ERROR";
       $message = "";
       $jwt = $request->headers->get('Authorization');
       $responseObjects = array("reclamations"=>array());

       //--------- Verifier la validité du JWT Token -----------//

       try{

        //$decoded = JWT::decode($jwt, $this->key, array('HS256'));
        $jwt = substr($jwt, 7);
        $decoded = $this->JWTManager->decode($jwt);


            //$refClient = $decoded->data->REF;
            //$cinClient = $decoded->data->CIN;
           $refClient = $decoded["REF"];
           $cinClient = $decoded["CIN"];

           if(empty($refClient)){
               $codeStatut = "ERROR-EMPTY-PARAMS";
           }
           else{
            //    $em = $this->getDoctrine()->getManager('customer');
            $em = $this->doctrine->getManager();
               $reclamation = $em->getRepository(Reclamation::class)->findByRefClient($refClient);
               //---------------------------------
               $responseObjects["reclamations"] = $reclamation;
               
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
       $resp["message"] = $message;
       $resp["objects"] = $responseObjects;

       $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
       JsonEncoder()));
       $responseSerialise = $serializer->serialize($resp, 'json');

       $response->setContent($responseSerialise);
       $response->setStatusCode(Response::HTTP_OK);

       return $response;

   }
   


   #[Route('/ws/agonline/reclamation/send/', methods: ['POST'])]
   public function sendEmailReclamationAction(Request $request)
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
            //    $decoded = JWT::decode($jwt, $this->key, array('HS256'));
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
           $tel = $request->get("tel");
           $email = $request->get("email");
           $objet = $request->get("objet");
           $msg = $request->get("msg");
           $police = $request->get("police");
               
           if(empty($nom) || empty($tel) || empty($objet) || empty($email) || empty($msg)){
               $codeStatut = "ERR-EMPTY";
               $message = "Tous les champs sont obligatoires ! ";
           }
           else{
               //--------------- verifier si c'est un client ---------------//
       
               if($refClient == ""){
                   //$databaseService = new DataBaseService($this->conn);
                   
                   $client = $this->AuthRepo->getClientByEmail($email);
                   if($client){
                       $refClient = $client["REF_CLIENT"];
                   }
               }
               
               //-----------------------------------------------------------//
               //----------- add reclamation to DATA BASE ------//
               
            //    $em = $this->getDoctrine()->getManager('customer');
               $em = $this->doctrine->getManager();
               
               $reclamation = new Reclamation();
               $reclamation->setNom($nom);
               $reclamation->setTel($tel);
               $reclamation->setObjet($objet);
               $reclamation->setMsgClient($msg);
               $reclamation->setDateReclamation(new \DateTime());
               $reclamation->setRefClient($refClient);
               $reclamation->setStatus("Crée");
               $reclamation->setCodeStatus(0);
               $reclamation->setPolice($police);
               
               $em->persist($reclamation);
               $em->flush();
               
               $reclamationId = $reclamation->getId();
               
               //------------- test piece jointe ----------//
               // Create the Transport
               //$transport = (new \Swift_SmtpTransport($this->smtpEmail, 465, 'ssl'))
                   //->setUsername($this->contactEmail)
                   //->setPassword($this->passwordEmail);
                
               // Create the Mailer using your created Transport
               //$mailer = new \Swift_Mailer($transport);
                
                   // Create a message
               //$body = 'Nom : '.$nom."<br/>Téléphone : ".$tel.'<br/>Email : '.$email.'<br/>Police : '.$police.'<br/>Message : <br/>'.$msg;
                
               //$messageMail = (new \Swift_Message($objet." [#".$reclamation->getId()."#]"))
                   //->setFrom(['agence.radeej.regie@gmail.com' => $nom])
                   //->setTo(['service.client@radeej.ma'])
                   //->setBody($body)
                   //->setContentType('text/html');
               $FilePath = null;
               $FileName = null;
               if(isset($_FILES['file']) && !empty($_FILES['file']['name'])){
                   //-----------------------------
                   $FilePath = $_FILES['file']['tmp_name'];
                   $FileName = $_FILES['file']['name'];
                   //$messageMail->attach(\Swift_Attachment::fromPath($_FILES['file']['tmp_name'])->setFilename($_FILES['file']['name']));
               }
                
               // Send the message
               //$mailer->send($messageMail);
               //---------------- send email ------------//
               $this->mailer->EmailReclamation($nom,$tel,$email,$police,$reclamationId,$objet,$FilePath,$FileName,$msg);
               
               $codeStatut = "OK";
                   
           }
       }catch (\Exception $e) {

           $codeStatut = "ERROR-EXCEPETION---".$e->getMessage()." ".$e->getLine()." ".$e->getFile();

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

  
    #[Route('/ws/agonline/reclamation/mails/synch/2c3aaa713577d1908accb94310b975aa73f989e4/', methods: ['GET'])]
    public function synchroReclamationAction(Request $request)
    {



        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "ERROR";
		$message = "";
        //$jwt = $request->headers->get('Authorization');
        $responseObjects = array("conso"=>array());

        //--------- Verifier la validité du JWT Token -----------//

        try{
			$imap = imap_open("{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX", $this->contactEmail, $this->passwordEmail);
			$message_count = imap_num_msg($imap);

			for ($i = 1; $i <= $message_count; ++$i) {
				$header = imap_header($imap, $i);
				$body = trim(imap_body($imap, $i));
				$prettydate = date("Y-m-d", $header->udate);
				
				/*$structure = imap_fetchstructure($imap, $i);

				if(isset($structure->parts) && is_array($structure->parts) && isset($structure->parts[1])) {
					$part = $structure->parts[1];
					$body = imap_fetchbody($imap,$i,2);

					if($part->encoding == 3) {
						$body = imap_base64($body);
					} else if($part->encoding == 1) {
						$body = imap_8bit($body);
					} else {
						$body = imap_qprint($body);
					}
				}*/
				
				$body = $this->ReplaceImap($body);
				
				$now = date("Y-m-d");

				$email = $header->from[0]->mailbox."@".$header->from[0]->host;
				//
				if($email == "crc@radeej.ma" && $now == $prettydate){
					$date2 = date("Y-m-d H:i:s", $header->udate);
					
					
					$overview = imap_fetch_overview($imap, $i, 0);
					$objet = utf8_decode(imap_utf8($overview[0]->subject));
					$ticket = $this->getStringBetween($objet,'[',']');
					$idRecla = $this->getStringBetween($objet,'[#','#]');
					
					//echo "-------".nl2br($body)."-------";
					
					$numTi = explode(" ", $ticket);
					$numTicket = "";
					if(count($numTi) > 1){
						$numTicket = $numTi[1];
					}
					
					if(!empty($numTicket)){
						// $em = $this->getDoctrine()->getManager('customer');
                        $em = $this->doctrine->getManager();
						$reclamation = $em->getRepository(Reclamation::class)->findOneById($idRecla);
						
						if($reclamation){
							$body = nl2br($body);
							$reclamation->setLastRep($body);
							$reclamation->setDateLastRep(new \DateTime($date2));
							$reclamation->setNumTicket($numTicket);
							$reclamation->setStatus("En cours");
							$reclamation->setCodeStatus(2);
							
							$em->flush();
						}
					}
				}
			}

			imap_close($imap);

           $codeStatut = "OK";
        }catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

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

    private function getStringBetween($str,$from,$to)
	{
		$sub = substr($str, strpos($str,$from)+strlen($from),strlen($str));
		return substr($sub,0,strpos($sub,$to));
	}
    
    private function ReplaceImap($txt) {
		$carimap = array("=C3=A9", "=C3=A8", "=C3=AA", "=C3=AB", "=C3=A7", "=C3=A0", "=20", "=C3=80", "=C3=89");
		$carhtml = array("é", "è", "ê", "ë", "ç", "à", "&nbsp;", "À", "É");
		$txt = str_replace($carimap, $carhtml, $txt);

		return $txt;
	}

}