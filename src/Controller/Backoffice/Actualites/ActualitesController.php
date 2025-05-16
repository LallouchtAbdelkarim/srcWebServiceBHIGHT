<?php

namespace App\Controller\Backoffice\Actualites;
use App\Repository\Oracle\AuthentificationRepository;
use App\Repository\Oracle\AbonnementsRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Actualites;
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

class ActualitesController extends AbstractController{


    public function __construct(
		private ManagerRegistry $doctrine,
		private JWTEncoderInterface $JWTManager,    
        private Verify $Verify,

		) {}


    //--------------- actualites ---------------//
	
    #[Route("/ws/agonline/back/actualites/add/",methods:["POST"])]
    public function addActualitesAction(Request $request, )
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
				$code = "ADDACT";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$titre = $request->get("titre");
					$text = $request->get("text");
					
					if(empty($titre)){
						$codeStatut = "ERROR-PARAMS";
						$message = "Le titre est obligatoire !";
					}
					else{
						$em = $this->doctrine->getManager();
						
						
						$extension_valide = array('jpeg','jpg','png','gif');
						$storeFolder = "./files/actualites/images/";
						//-----------------------------
						
						$actualite = new Actualites();
						$actualite->setDateActu(new \DateTime());
						$actualite->setTitre($titre);
						$actualite->setText($text);
						$actualite->setImg("");
						
						
						
						if(isset($_FILES['img']) && !empty($_FILES['img']['name'])){
							//-----------------------------
							$tmpFile = $_FILES['img']['tmp_name'];
							$extension = pathinfo($_FILES['img']['name'],PATHINFO_EXTENSION);
							$nomImage = sha1(date_format(new \DateTime(),"YmdHis")).".".$extension;
							
							$targetFile = $storeFolder.$nomImage;
							
							if(move_uploaded_file($tmpFile,$targetFile)){
								$targetFile3 = "/files/actualites/images/".$nomImage;
								$actualite->setImg($targetFile3);
							}
						}
						
						$em->persist($actualite);
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
	
	
    #[Route("/ws/agonline/back/actualites/update/{id}/",methods:["POST"])]
    public function updateActualitesAction(Request $request, $id)
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
				$code = "UPPACT";
				if($this->Verify->verifyRole($code,$idAdmin)){
					
					$em = $this->doctrine->getManager();
					$actualite = $em->getRepository(Actualites::class)->findOneById($id);
					
					if(!$actualite){
						$codeStatut = "ERROR-ACTU";
						$message = "Une erreur s'est produite !";
					}
					else{
						$titre = $request->get("titre");
						$text = $request->get("text");
						
						if(empty($titre)){
							$codeStatut = "ERROR-PARAMS";
							$message = "Le titre est obligatoire !";
						}
						else{
							$extension_valide = array('jpeg','jpg','png','gif');
							$storeFolder = "./files/actualites/images/";
							//-----------------------------
							
							//$actualite->setDateActu(new \DateTime());
							$actualite->setTitre($titre);
							$actualite->setText($text);
							
							
							
							
							if(isset($_FILES['img']) && !empty($_FILES['img']['name'])){
								
								
								//-----------------------------
								$tmpFile = $_FILES['img']['tmp_name'];
								$extension = pathinfo($_FILES['img']['name'],PATHINFO_EXTENSION);
								$nomImage = sha1(date_format(new \DateTime(),"YmdHis")).".".$extension;
								
								$targetFile = $storeFolder.$nomImage;
								
								if(move_uploaded_file($tmpFile,$targetFile)){
									if($actualite->getImg() != ""){
										if (file_exists(".".$actualite->getImg())) {
											unlink(".".$actualite->getImg());
										}
									}
									
									$targetFile3 = "/files/actualites/images/".$nomImage;
									$actualite->setImg($targetFile3);
								}
							}
							
							$em->flush();
							
							$codeStatut = "OK";
							
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
	
	
    #[Route("/ws/agonline/back/actualite/get/{id}/",methods:["GET"])]
    public function getActualiteAction(Request $request, $id)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("actualite"=>array());

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
				$code = "CONSACT";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$actualite = $em->getRepository(Actualites::class)->findOneById($id);
					
					$responseObjects["actualite"] = $actualite;
					
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
	
	
    #[Route("/ws/agonline/back/actualites/get/all/",methods:["GET"])]
    public function getAllActualiteAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("actualite"=>array());

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
				$code = "CONSACT";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager();
					$actualite = $em->getRepository(Actualites::class)->findAll();
					
					$responseObjects["actualite"] = $actualite;
					
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
	
	
    #[Route("/ws/agonline/back/actualites/delete/{id}/",methods:["GET"])]
    public function deleteActualiteAction(Request $request, $id)
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
				$code = "SUPPACT";
				if($this->Verify->verifyRole($code,$idAdmin)){
                    $em = $this->doctrine->getManager();
					$actualite = $em->getRepository(Actualites::class)->findOneById($id);
					
					if(!$actualite){
						$codeStatut = "ERROR-USER";
						$message = "Une erreur s'est produite !";
					}
					else{
						if($actualite->getImg() != ""){
							if (file_exists(".".$actualite->getImg())) {
								unlink(".".$actualite->getImg());
							}
						}
						
						$em->remove($actualite);
						
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