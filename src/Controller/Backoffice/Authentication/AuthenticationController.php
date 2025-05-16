<?php

namespace App\Controller\Backoffice\Authentication;
use App\Repository\Oracle\AuthentificationRepository;
use App\Repository\Oracle\AbonnementsRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\TokenPass;
use App\Entity\DemandeCreation;
use App\Entity\Administrateur;
use App\Entity\ListRoleAdmin;
use App\Entity\RoleAdmin;
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

class AuthenticationController extends AbstractController{

	public function __construct(
		private ManagerRegistry $doctrine,
		private JWTEncoderInterface $JWTManager,    
        private Verify $Verify,

		) {}

    
    #[Route("/ws/agonline/back/authentication/", methods: ['POST'])]
    public function authenticationAction(Request $request,)
    {


    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/

    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$tokenJWT = "";
    	$codeStatut = "ERROR";
        $message = "";

		try{
			$login = $request->get("login");
			$password = $request->get("password");

			$admin = null;
			if(empty($login) || empty($password)){
				$codeStatut = "ERROR-EMPTY-PARAMS";
				$message = "Le login et le mot de passe sont obligatoires !";
			}
			else{
				
                $em = $this->doctrine->getManager();
				$admin = $em->getRepository(Administrateur::class)->findOneBy(array("login"=>$login,"password"=>sha1($password)));
				
				if($admin){
					
					$trv = sha1(date_format(new \DateTime(),"Y-m-d H:i:s")."-".$admin->getId());
					
					$admin->setTrv($trv);
					
					$em->flush();
					
					$now = date_format(new \DateTime(), "Y-m-d h:i:s");
					$timestampNow = strtotime($now);

					//$this->iat = $timestampNow;

					//-------

					$dt = date_format(new \DateTime('now +1 day'), "Y-m-d h:i:s");
					$timestampTomorrow = strtotime($dt);

					

                    $data = array(
                        "ID" => $admin->getId(),
                        "TRV" => $trv
                    );

					// $jwt = JWT::encode($token, $this->key);
					// $tokenJWT = $jwt;
                    $jwt = $this->JWTManager->encode($data);
                    $tokenJWT = $jwt;

					$codeStatut = "OK";
				}
				else{
					$codeStatut = "ERROR-WRONG-PARAMS";
					$message = "Login ou mot de passe incorrect !";
				}
			}
		}catch (\Exception $e) {

            $codeStatut = "ERROR";
			$message =$e->getMessage();
        }
    	

        $resp["codeStatut"] = $codeStatut;
        $resp["message"] = $message;
        $resp["token"] = $tokenJWT;
		$resp["admin"] = $admin;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }

    
    #[Route("/ws/agonline/back/admin/get/roles/", methods: ['GET'])]
    public function getRolesAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "ERROR";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("roles"=>array());

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
				$code = "ADDUSR";
				if($this->Verify->verifyRole($code,$idAdmin)){
					
                    $em = $this->doctrine->getManager();
					$listRole = $em->getRepository(ListRoleAdmin::class)->findAll();
					
					$responseObjects["roles"] = $listRole;
					
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

    
    #[Route("/ws/agonline/back/admin/add/", methods: ['POST'])]
    public function addAdminAction(Request $request)
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
				$code = "ADDUSR";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$nom = $request->get("nom");
					$prenom = $request->get("prenom");
					$login = $request->get("login");
					$pass1 = $request->get("pass1");
					$pass2 = $request->get("pass2");
					
				
                    $em = $this->doctrine->getManager(); 
					$listRole = $em->getRepository(ListRoleAdmin::class)->findAll();
					
					if(empty($nom) || empty($login) || empty($pass1)){
						$codeStatut = "ERROR-EMPTY";
						$message = "Un des champs obligatoires est vide !";
					}
					else{
						$em = $this->doctrine->getManager();
						$admin = $em->getRepository(Administrateur::class)->findOneBy(array("login"=>$login));
						
						if($admin){
							$codeStatut = "ERROR-ADMIN-EXIST";
							$message = "Le nom d'utilisateur existe dans la base de données";
						}
						else{
							if(strlen($pass1) < 8){
								$codeStatut = "ERROR-PASS";
								$message = "Le mot de passe doit comprendre 8 caractères au minimum";
							}
							else{
								if($pass1 != $pass2){
									$codeStatut = "ERROR-PASS-2";
									$message = "Les deux mot de passe ne sont pas identiques";
								}
								else{
									$admin = new Administrateur();
									$admin->setNom($nom);
									$admin->setPrenom($prenom);
									$admin->setLogin($login);
									$admin->setPassword(sha1($pass1));
									$admin->setTrv("");
									
									$em->persist($admin);
									$em->flush();
									
									//---------------------
									
									for($i=0;$i<count($listRole);$i++){
										$etat = 0;
										if(isset($_POST[$listRole[$i]->getCode()]))
										{
											$etat = 1;
										}
										
										$role = new RoleAdmin();
										$role->setCode($listRole[$i]->getCode());
										$role->setIdRole($listRole[$i]->getId());
										$role->setIdAdmin($admin->getId());
										$role->setEtat($etat);
										
										$em->persist($role);
									}
									
									$em->flush();
									
									$codeStatut = "OK";
								}
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

   
    #[Route("/ws/agonline/back/admin/update/{id}/", methods: ['POST'])]
    public function updateAdminAction(Request $request,$id)
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
				$code = "UPPUSER";
				if($this->Verify->verifyRole($code,$idAdmin)){
					
                    $em = $this->doctrine->getManager(); 
					$admin = $em->getRepository(Administrateur::class)->findOneById($id);
					
					if(!$admin){
						$codeStatut = "ERROR-USER";
						$message = "Une erreur s'est produite !";
					}
					else{
						$nom = $request->get("nom");
						$prenom = $request->get("prenom");
						$login = $request->get("login");
						$pass1 = $request->get("pass1");
						$pass2 = $request->get("pass2");
						
						$em = $this->doctrine->getManager(); 
						$listRole = $em->getRepository(ListRoleAdmin::class)->findAll();
						
						if(empty($nom) || empty($login)){
							$codeStatut = "ERROR-EMPTY";
							$message = "Un des champs obligatoires est vide !";
						}
						else{
							$em = $this->doctrine->getManager(); 
							$admin1 = $em->getRepository(Administrateur::class)->findOneBy(array("login"=>$login));
							
							if($admin1 && $admin1->getId() != $admin->getId()){
								$codeStatut = "ERROR-ADMIN-EXIST";
								$message = "Le nom d'utilisateur existe dans la base de données";
							}
							else{
								
								$admin->setNom($nom);
								$admin->setPrenom($prenom);
								$admin->setLogin($login);
										
								$em->flush();
										
										
								if(!empty($pass1)){
									if(strlen($pass1) < 8){
										$codeStatut = "ERROR-PASS";
										$message = "Le mot de passe doit comprendre 8 caractères au minimum. (Les autres informations ont étés modifiées)";
									}
									else{
										if($pass1 != $pass2){
											$codeStatut = "ERROR-PASS-2";
											$message = "Les deux mot de passe ne sont pas identiques. (Les autres informations ont étés modifiées)";
										}
										else{
											$admin->setPassword(sha1($pass1));
										}
									}
								}
								
								//---------------------
										
								for($i=0;$i<count($listRole);$i++){
									$etat = 0;
									if(isset($_POST[$listRole[$i]->getCode()]))
									{
										$etat = 1;
									}
									
									$em = $this->doctrine->getManager(); 
									$role = $em->getRepository(RoleAdmin::class)->findOneBy(array("idAdmin"=>$admin->getId(),"idRole"=>$listRole[$i]->getId()));
									if($role){
										$role->setEtat($etat);
									}
									else{
										$role = new RoleAdmin();
										$role->setCode($listRole[$i]->getCode());
										$role->setIdRole($listRole[$i]->getId());
										$role->setIdAdmin($admin->getId());
										$role->setEtat($etat);
												
										$em->persist($role);
									}
								}
										
								$em->flush();
									
								if($codeStatut == "")
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

    
    #[Route("/ws/agonline/back/profil/update/", methods: ['POST'])]
    public function updateProfilAction(Request $request)
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
				
				$em = $this->doctrine->getManager(); 
				$admin = $em->getRepository(Administrateur::class)->findOneById($idAdmin);
				
				if(!$admin){
					$codeStatut = "ERROR-USER";
					$message = "Une erreur s'est produite !";
				}
				else{
					$nom = $request->get("nom");
					$prenom = $request->get("prenom");
					$pass1 = $request->get("pass1");
					$pass2 = $request->get("pass2");
					
					$em = $this->doctrine->getManager(); 
					$listRole = $em->getRepository(ListRoleAdmin::class)->findAll();
					
					if(empty($nom)){
						$codeStatut = "ERROR-EMPTY";
						$message = "Un des champs obligatoires est vide !";
					}
					else{
						$admin->setNom($nom);
						$admin->setPrenom($prenom);
									
						$em->flush();
						
						if(!empty($pass1)){
							if(strlen($pass1) < 8){
								$codeStatut = "ERROR-PASS";
								$message = "Le mot de passe doit comprendre 8 caractères au minimum. (Les autres informations ont étés modifiées)";
							}
							else{
								if($pass1 != $pass2){
									$codeStatut = "ERROR-PASS-2";
									$message = "Les deux mot de passe ne sont pas identiques. (Les autres informations ont étés modifiées)";
								}
								else{
									$admin->setPassword(sha1($pass1));
								}
							}
						}							
								
						if($codeStatut == "")
							$codeStatut = "OK";
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

    
    #[Route("/ws/agonline/back/admin/get/{id}/", methods: ['GET'])]
    public function getAdminAction(Request $request,$id)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("admin"=>array(),"rolesAdmin"=>array(),"roles"=>array());

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
				$code = "CONSUSR";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager(); 
					$admin = $em->getRepository(Administrateur::class)->findOneById($id);
					
					$responseObjects["admin"] = $admin;
					
					$rolesAdmin = $em->getRepository(RoleAdmin::class)->findByIdAdmin($id);
					
					$responseObjects["rolesAdmin"] = $rolesAdmin;
					
					$listRole = $em->getRepository(ListRoleAdmin::class)->findAll();
					
					$responseObjects["roles"] = $listRole;
					
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


    

    #[Route("/ws/agonline/back/admins/get/all/", methods: ['GET'])]
    public function getAllAdminAction(Request $request,)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("admin"=>array());

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
				$code = "CONSUSR";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager(); 
					$admin = $em->getRepository(Administrateur::class)->findAll();
					
					$responseObjects["admin"] = $admin;
					
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

    #[Route("/ws/agonline/back/admin/delete/{id}/", methods: ['GET'])]
    public function deleteAdminAction(Request $request,$id)
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
				$code = "SUPUSER";
				if($this->Verify->verifyRole($code,$idAdmin)){
					$em = $this->doctrine->getManager(); 
					$admin = $em->getRepository(Administrateur::class)->findOneById($id);
					
					if(!$admin){
						$codeStatut = "ERROR-USER";
						$message = "Une erreur s'est produite !";
					}
					else{
						if($admin->getId() == $idAdmin){
							$codeStatut = "ERROR-USER-DEL";
							$message = "Impossible de supprimer le compte !";
						}
						else{
							$em = $this->doctrine->getManager(); 
							$roles = $em->getRepository(RoleAdmin::class)->findByIdAdmin($id);
							
							for($i=0;$i<count($roles);$i++){
								$em->remove($roles[$i]);
							}
							
							$em->remove($admin);
							
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
}
