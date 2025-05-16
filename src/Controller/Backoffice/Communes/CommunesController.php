<?php
namespace App\Controller\Backoffice\Communes;
use App\Repository\Oracle\AuthentificationRepository;
use App\Repository\Oracle\AbonnementsRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Communes;
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

class CommunesController extends AbstractController{
    //-------------- Communes ----------------//
    public function __construct(
		private ManagerRegistry $doctrine,
		private JWTEncoderInterface $JWTManager,    
        private Verify $Verify,

		) {}

	
    #[Route("/ws/agonline/back/communes/add/", methods:["POST"])]
    public function addCommunesAction(Request $request)
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
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            $idAdmin = $decoded["ID"];
            $trv = $decoded["TRV"];

            if(empty($idAdmin)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{
				$code = "ADDCOM";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$titre = $request->get("titre");
					$isEau = $request->get("isEau");
					$isElec = $request->get("isElec");
					
					if(empty($titre)){
						$codeStatut = "ERROR-PARAMS";
						$message = "Le titre est obligatoire !";
					}
					else{
						$em = $this->doctrine->getManager();
						
						
						$commune = new Communes();
						$commune->setTitre($titre);
						$commune->setIsEau($isEau);
						$commune->setIsElec($isElec);
						
						
						$em->persist($commune);
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
	
	
    #[Route("/ws/agonline/back/communes/get/all/", methods:["GET"])]
    public function getAllCommunesAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("communes"=>array());

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
				$code = "CONSCOM";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$communes = $em->getRepository(Communes::class)->findAll();
					
					$responseObjects["communes"] = $communes;
					
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
	
	
    #[Route("/ws/agonline/back/communes/delete/{id}/", methods:["GET"])]
    public function deleteCommunesAction(Request $request, $id)
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
				$code = "DELCOM";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$commune = $em->getRepository(Communes::class)->findOneById($id);
					
					if(!$commune){
						$codeStatut = "ERROR-USER";
						$message = "Une erreur s'est produite !";
					}
					else{
						
						$em->remove($commune);
						
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