<?php

namespace App\Controller\Users;

use App\Entity\Competence;
use App\Entity\CompetenceProfil;
use App\Entity\DetailModelAffichage;
use App\Entity\Token;
use App\Entity\TypeUtilisateur;
use App\Entity\Groupe;
use App\Entity\GroupProfil;
use App\Entity\ListesRoles;
use App\Entity\Missions;
use App\Entity\Profil;
use App\Entity\Roles;
use App\Entity\Utilisateurs;
use App\Entity\Workflow;
use App\Repository\Users\userRepo;
use App\Service\AuthService;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ValidationService;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\DBAL\Connection;

use Symfony\Component\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

#[Route('/API') ]

class UsersController extends AbstractController
{
    private $connection;
    private $MessageService;
    private $AuthService;

    private $JWTManager;
    private $userRepo;
    public function __construct(Connection $connection,  
    MessageService $MessageService,
    AuthService $AuthService,
    JWTEncoderInterface $JWTManager,
    userRepo $userRepo)
    {
         $this->connection = $connection;
         $this->JWTManager = $JWTManager;
         $this->MessageService = $MessageService;
         $this->AuthService = $AuthService;
         $this->userRepo = $userRepo;
    }

    #[Route('/add_profile', name: 'add_profile')]
    public function index(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, SerializerInterface $serializer): Response
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            $Liste_roles = $entityManager->getRepository(ListesRoles::class)->findAll();
            $competences = $entityManager->getRepository(Competence::class)->findAll();
            $data = json_decode($request->getContent(), true);
            
                if ($data['titre'] == null ) {
                    $codeStatut="EMPTY-DATA";
                } else {
                    $pr = $entityManager->getRepository(Profil::class)->findOneBy(['titre' => $data['titre']]);
                    if($pr){
                        $codeStatut='TITRE_DEJE_EXIST';
                    }else{
                        if (empty($data['input']) || count($data['input']) === 0) {
                            $codeStatut='NO_ROLES_FOUND';
                        }
                        else
                        {
                            $profil = new Profil();
                            $profil->setTitre($data['titre']);
                            $profil->setDateCreation(new \DateTime());
                            $entityManager->persist($profil);
                            $entityManager->flush();
                
                            $inputRoles = array_keys($data['input']);
                            $compTenc = array_keys($data['comp']);
                
                            foreach ($Liste_roles as $roles) {
                                $role = new Roles();
                                $role->setIdProfil($profil);
                                $role->setIdRole($roles);
                
                                if (in_array($roles->getId(), $inputRoles) && $data['input'][$roles->getId()] === "on") {
                                    $role->setStatus(1);
                                } else {
                                    $role->setStatus(0);
                                }
                                $entityManager->persist($role);
                            }
                            foreach ($competences as $competence) {
                                $comp = new CompetenceProfil();
                                $comp->setIdProfil($profil);
                                $comp->setIdCompetence($competence);
                
                                if (in_array($competence->getId(), $compTenc) && $data['comp'][$competence->getId()] === "on") {
                                    $comp->setStatus(1);
                                } else {
                                    $comp->setStatus(0);
                                }
                                $entityManager->persist($comp);
                            }
                            $entityManager->flush();
                            $codeStatut = 'OK';
                        }
                        
                    }
                }
        } catch (\Exception $e) {
            $respObjects["err"] = $e->getMessage();
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/modifie_profile/{id}', name: 'modifie_profile')]
    public function UpdateProfile(
        EntityManagerInterface $entityManager,
        Request $request,
        ValidationService $validator,
        $id
    ): Response {
        
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);

            $Liste_roles = $entityManager->getRepository(ListesRoles::class)->findAll();
            $profil = $entityManager->getRepository(Profil::class)->findOneBy(['id' => $id]);
            $roles = $entityManager->getRepository(Roles::class)->findBy(['id_profil' => $id]);
            $competences = $entityManager->getRepository(CompetenceProfil::class)->findAll();
            $cp = $entityManager->getRepository(Competence::class)->findAll();
            $titre = '';
            $status = '';
            $response = '';


        $data = json_decode($request->getContent(), true);
        if ($data['titre'] == null || $data['status'] == null) {
            $codeStatut="EMPTY-DATA";
        } else {
            $pr = $entityManager->getRepository(Profil::class)->findOneBy(['titre' => $data['titre']]);
            if($pr && $pr->getId() != $id){
                $codeStatut='TITRE_DEJE_EXIST';
            }else{
                if (empty($data['input']) || count($data['input']) === 0) {
                    $codeStatut='NO_ROLES_FOUND';
                }
                else
                {
                    $profil->setTitre($data['titre']);
                    $profil->setDateCreation(new \DateTime());
                    $entityManager->persist($profil);
                    $entityManager->flush();
        
                    $inputRoles = array_keys($data['input']);
                    $compTenc = array_keys($data['comp']);
        
                    foreach ($Liste_roles as $role) {
                        $roleObject = $entityManager->getRepository(Roles::class)->findOneBy([
                            'id_profil' => $profil->getId(),
                            'id_role' => $role->getId(),
                        ]);
        
                        if (in_array($role->getId(), $inputRoles) && $data['input'][$role->getId()] === "on") {
                            $roleObject->setStatus(1);
                        } else {
                            $roleObject->setStatus(0);
                        }
                        $entityManager->persist($roleObject);
                    }
                    $entityManager->flush();
    
                    $codeStatut="OK";

                }
    
    
            }
        }
        } catch (\Exception $e) {
            $codeStatut="ERROR";            
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/delete_profile/{id}', name: 'delete_profile')]
    public function deleteProfile(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, $id): Response
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {

            $profile = $entityManager->getRepository(Profil::class)->findOneBy(array('id' => $id));
            $response = "";

            if (!$profile) {
                $codeStatut = "NOT_EXIST_ELEMENT";
            } else {
                $roles = $entityManager->getRepository(Roles::class)->findBy(array('id_profil' => $id));

                $competences = $entityManager->getRepository(CompetenceProfil::class)->findBy(array('id_profil' => $id));

                $gp = $entityManager->getRepository(GroupProfil::class)->findBy(array('id_profil' => $id));

                if($gp)
                {
                    $codeStatut="PROFIL-LIAISON";

                }
                else
                {
                    if ($roles) {
                        foreach ($roles as $role) {
                            // Remove each role
                            $entityManager->remove($role);
                        }
                    }

                    if ($competences) {
                        foreach ($competences as $c) {
                            // Remove each role
                            $entityManager->remove($c);
                        }
                    }
                    
                    
                    $entityManager->remove($profile);
                    $entityManager->flush();
                    $codeStatut = "OK";
    
                }
            }       
        } catch (\Exception $th) {
            $codeStatut="ERROR";
        }
        
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);    
    }
    
    #[Route('/add_groupe', methods: ['POST'])]
    public function groupe(EntityManagerInterface $entityManager, Request $request, ValidationService $validator): Response
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
        $profil = "";


            $data = json_decode($request->getContent(), true);

            if ($data['titre'] == null) {
                $codeStatut="EMPTY-DATA";
            } else {


                $grp = $entityManager->getRepository(Groupe::class)->findOneBy(['titre' => $data['titre']]);
                if($grp)
                {
                    $codeStatut="GROUPE-PROFIL-EXIST";
                }
                else
                {
                    $group = new Groupe();
                    $group->setTitre($data['titre']);
                    // $group->setStatus($data['status']);
                    $group->setDateCreation(new \DateTime());
                    $entityManager->persist($group);
                    // Check if $data['ary'] exists and is not empty
                    if (isset($data['ary']) && !empty($data['ary'])) {
                        foreach ($data['ary'] as $key => $value) {
                            $profil = $entityManager->getRepository(Profil::class)->findBy(['id' => $value]);
                            foreach ($profil as $item) {
                                $grp_profil = new GroupProfil();
                                $grp_profil->setIdGroup($group);
                                $grp_profil->setIdProfil($item);
                                $entityManager->persist($grp_profil);
                            }
                        }
                        $entityManager->flush();
                        $codeStatut = "OK";
                    } else {
                        // Handle the case where ary is empty or not set
                        $codeStatut = "NO_PROFILS_FOUND";
                    }
    
                }
            }
        } catch (\Exception $th) {
            $codeStatut=$th;
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);        
    }

    #[Route('/modifie_groupe/{id}', name: 'modifie_groupe')]
    public function UpdateGroupe(
        EntityManagerInterface $entityManager,
        Request $request,
        ValidationService $validator,
        $id
    ): Response {
        $groupe = $entityManager->getRepository(Groupe::class)->findOneBy(['id' => $id]);
        $profiles = $entityManager->getRepository(Profil::class)->findAll();
        $groupe_profils = $entityManager->getRepository(GroupProfil::class)->findBy(['id_group' => $id]);
        $response = '';

        if ($request->getMethod() === 'POST') {
            $data = json_decode($request->getContent(), true);

            if ($data['titre'] == null) {
                $response = "Un des champs est vide";
            } else {

                if (isset($data['ary']) && !empty($data['ary'])) {

                    $groupe->setTitre($data['titre']);
                    // $groupe->setStatus($data['status']);
                    $groupe->setDateCreation(new \DateTime());
                    $entityManager->persist($groupe);
                    // Remove existing GroupProfil records for the group
                    foreach ($groupe_profils as $groupe_profil) {
                        $entityManager->remove($groupe_profil);
                    }
                    $entityManager->flush();
                    foreach ($data['ary'] as $key => $value) {
                        $profil = $entityManager->getRepository(Profil::class)->findOneBy(['id' => $value]);
                        // Create a new instance of GroupProfil
                        $groupe_profil = new GroupProfil();
                        $groupe_profil->setIdGroup($groupe);
                        $groupe_profil->setIdProfil($profil);
                        $entityManager->persist($groupe_profil);
                    }

                    $entityManager->flush();
                    $response = "OK";

                }
                else
                {
                    $response = "Veuillez sélectionner au moins un profil !";

                }
            }
        }

        return new JsonResponse($response);
    }

    #[Route('/delete_groupe/{id}', name: 'delete_groupe')]
    public function deleteGroupe(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, $id): Response
    {
        $groupe = $entityManager->getRepository(Groupe::class)->findOneBy(array('id' => $id));
        $response = "";

        if (!$groupe) {
            $response = "Ce profile n'existe pas !";
        } else {
            $gp = $entityManager->getRepository(GroupProfil::class)->findBy(array('id_group' => $id));
            foreach ($gp as $item) {
                $entityManager->remove($item);
            }
            $entityManager->remove($groupe);
            $entityManager->flush();
            $response = "OK";
        }
        return new JsonResponse($response);
    }

  
    #[Route('/add_user', name: 'add_user')]
    public function utilisateurs(Request $request, EntityManagerInterface $entityManager): Response
    {
        $respObjects = array();
        $codeStatut ="ERROR";

        try {
            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $cin = $request->request->get('cin');
            $tel = $request->request->get('tel');
            $mobile = $request->request->get('mobile');
            $adresse = $request->request->get('adresse');
            $ville = $request->request->get('ville');
            $pays = $request->request->get('pays');
            $grpr = $request->request->get('grpr');
            $imei = $request->request->get('imei');
            $rayon = $request->request->get('rayon');
            $pass = $request->request->get('pass');
            $pass1 = $request->request->get('pass1');
            $email = $request->request->get('email');
            $type = $request->request->get('type');
            $competence = $request->request->get('competence');
            $responsable = $request->request->get('responsable');
            $servicesUser = $request->request->get('servicesUser');

            // Get the image file from the request
            $img = $request->files->get('img');

            // Initialize status code
            $codeStatut = "OK";

            // Check if mandatory fields are empty
            if (empty(trim($nom)) || empty(trim($prenom)) || empty(trim($pass))) {
                $codeStatut = "EMPTY-DATA";
            } else {
                // Validate CIN format (assuming it should be numeric and 8 digits)
                if (!empty(trim($cin)) && !preg_match('/^[A-Z]+[0-9]*[A-Z0-9]*$/', $cin)) {
                    $codeStatut = "INVALID_CIN_FORMAT";
                }
                elseif (!empty(trim($email)) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $codeStatut = "INVALID_EMAIL_FORMAT";
                }
                elseif (!empty(trim($tel)) && !preg_match("'^(([\+]([\d]{2,}))([0-9\.\-\/\s]{5,})|([0-9\.\-\/\s]{5,}))*$'", $tel)) {
                    $codeStatut = "INVALID_TEL_FORMAT";
                }
                elseif (empty($servicesUser)) {
                    $codeStatut = "SERVICES_EMPTY";
                }
                elseif ($pass !== $pass1) {
                    $codeStatut = "PASSWORD_MATCH";
                } else {
                    // Continue with user existence check and other validations
                    $existUser = $entityManager->getRepository(Utilisateurs::class)->findOneBy(['nom' => $nom, 'prenom' => $prenom]);

                    if ($existUser) {
                        $codeStatut = "ELEMENT_DEJE_EXIST";
                    } else {
                        $group = $entityManager->getRepository(Groupe::class)->findOneBy(['id' => $grpr]);
                        $typeUser = $entityManager->getRepository(TypeUtilisateur::class)->find($type);
                        $competenceEntity = $entityManager->getRepository(Competence::class)->findOneBy(['id' => $competence]);
                        $responsableEntity = $entityManager->getRepository(TypeUtilisateur::class)->findOneBy(['id' => $responsable]);

                        if (!$typeUser) {
                            $codeStatut = "EMPTY-DATA";
                        } else {
                            if ($typeUser->getOrdre() != 1 && ($responsableEntity && ($typeUser->getOrdre() < $responsableEntity->getOrdre()))) {
                                $codeStatut = "RESPONSABILITY_ERROR";
                            } else {
                                if ($group != null && $typeUser != null) {
                                    // Create a new user entity
                                    $user = new Utilisateurs();
                                    $hashedPassword = md5($pass);
                                    $user->setNom($nom);
                                    $user->setPrenom($prenom);
                                    $user->setPassword($hashedPassword);
                                    $user->setTel($tel);
                                    $user->setImei($imei);
                                    $user->setMobile($mobile);
                                    $user->setAdresse($adresse);
                                    $user->setRayon($rayon);
                                    $user->setCin($cin);
                                    $user->setVille($ville);
                                    $user->setPays($pays);
                                    $user->setIdGroup($group);
                                    $user->setIdTypeUser($typeUser);
                                    $user->setEmail($email);
                                    $user->setStatus(0);
                                    $user->setIdCompetence($competenceEntity);
                                    $user->setResponsable($responsableEntity);
                                    $user->setServices(json_encode($servicesUser));

                                    if ($img) {
                                        $destination = $this->getParameter('kernel.project_dir') . '/public/profile_img';
                                        $newFilename = uniqid() . '.' . $img->guessExtension();
                            
                                        try {
                                            $img->move($destination, $newFilename);
                                        } catch (FileException $e) {
                                            // Handle the exception if necessary
                                        }
                                        $user->setImg('/profile_img/' . $newFilename);
                                    }
                            
                                    // Persist the user entity
                                    $entityManager->persist($user);
                                    $entityManager->flush();
                                    $codeStatut = "OK";
                                } else {
                                    $codeStatut = "EMPTY-DATA";
                                }
                            }
                        }
                    }
                }
            }


        } catch (\Exception $e) {
        $respObjects["msg"] = $e->getMessage();
            //throw $th;
            $codeStatut="ERROR";
        }
        
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/modifie_user/{id}', name: 'modifie_user',methods:['POST'])]
    public function Updateutilisateurs(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $codeStatut="EROOR";

        try{
            $user = $entityManager->getRepository(Utilisateurs::class)->findOneBy(array('id' => $id));;
            // $gpu = $entityManager->getRepository(GroupProfil::class)->findBy(array('id_group' =>$groupe_user->getId() ));
            if(!$user){
                $codeStatut="NOT_EXIST_ELEMENT";
            }else{
                $nom = $request->request->get('nom');
                $prenom = $request->request->get('prenom');
                $cin = $request->request->get('cin');
                $tel = $request->request->get('tel');
                $mobile = $request->request->get('mobile');
                $adresse = $request->request->get('adresse');
                $ville = $request->request->get('ville');
                $pays = $request->request->get('pays');
                $grpr = $request->request->get('grpr');
                $supp = $request->request->get('supp');
                $imei = $request->request->get('imei');
                $rayon = $request->request->get('rayon');
                $pass = $request->request->get('pass');
                $pass1 = $request->request->get('pass1');
                $status = $request->request->get('status');
                $type = $request->request->get('type');
                $competence = $request->request->get('competence');
                $responsable = $request->request->get('responsable');
                $servicesUser = $request->request->get('servicesUser');
                $email = $request->request->get('email');

                // Get the image file from the request
                $img = $request->files->get('img');
                    if(empty(trim($nom)) || empty(trim($prenom)) ||  empty(trim($this->formatPhoneNumber($tel)))  ){
                        $codeStatut = "EMPTY-DATA";
                    }else{
                                        
                    if (!empty(trim($cin)) && !preg_match('/^[A-Z]+[0-9]*[A-Z0-9]*$/', $cin)) {
                        $codeStatut = "INVALID_CIN_FORMAT";
                    }
                    elseif (!empty(trim($email)) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $codeStatut = "INVALID_EMAIL_FORMAT";
                    }
                    elseif (!empty(trim($tel)) && !preg_match("'^(([\+]([\d]{2,}))([0-9\.\-\/\s]{5,})|([0-9\.\-\/\s]{5,}))*$'", $tel)) {
                        $codeStatut = "INVALID_TEL_FORMAT";
                    }
                    elseif (empty($servicesUser)) {
                        $codeStatut = "SERVICES_EMPTY";
                    }
                    elseif ($pass !== $pass1) {
                        $codeStatut="PASSWORD_MATCH";
                    } else {
                        if(empty(trim($nom)) or empty(trim($nom))){
                            $codeStatut = "EMPTY-DATA";
                        }else{
                            $group = $entityManager->getRepository(Groupe::class)->findOneBy(['id' => $grpr]);
                            $typeUser = $entityManager->getRepository(TypeUtilisateur::class)->findOneBy(['id' => $type]);
                            $competenceEntity = $entityManager->getRepository(Competence::class)->findOneBy(['id' => $competence]);
                            $responsableEntity = $entityManager->getRepository(TypeUtilisateur::class)->findOneBy(['id' => $responsable]);
                            if(($typeUser) == null){
                                $codeStatut="EMPTY-DATA";
                            }else{
                                if($typeUser->getOrdre() != 1 &&  ($responsableEntity && ($typeUser->getOrdre() < $responsableEntity->getOrdre()))){
                                    $codeStatut="RESPONSABILITY_ERROR";
                                }else{
                                    if (!empty($pass)) {
                                        // Hash and update password if it's not empty, null, or equal to an empty string
                                        $hashedPassword = md5($pass);
                                        $user->setPassword($hashedPassword);
                                    }
                                    $user->setNom($nom);
                                    $user->setPrenom($prenom);
                                    $user->setTel($tel);
                                    $user->setImei($imei);
                                    $user->setMobile($mobile);
                                    $user->setAdresse($adresse);
                                    $user->setRayon($rayon);
                                    $user->setCin($cin);
                                    $user->setVille($ville);
                                    $user->setPays($pays);
                                    $user->setIdGroup($group);
                                    $user->setIdTypeUser($typeUser);
                                    $user->setIdCompetence($competenceEntity);
                                    $user->setResponsable($responsableEntity);
                                    $user->setServices(json_encode($servicesUser));
                                    $user->setEmail($email);


                                    // Handle the image file upload
                                    if ($img) {
                                        // Remove the existing image file
                                        $existingImagePath = $this->getParameter('kernel.project_dir') . '/public' . $user->getImg();
                
                                        if ($existingImagePath !== null && file_exists($existingImagePath)) {
                                            unlink($existingImagePath);
                                        }
                                        // Upload the new image file
                                        $destination = $this->getParameter('kernel.project_dir') . '/public/profile_img';
                                        $newFilename = uniqid() . '.' . $img->guessExtension();
                
                                        try {
                                            $img->move($destination, $newFilename);
                                        } catch (FileException $e) {
                                            // Handle the exception if necessary
                                        }
                                        $user->setImg('/profile_img/' . $newFilename);
                                    }
                                    // Persist the user entity
                                    $entityManager->persist($user);
                                    $entityManager->flush();
                                    $codeStatut = "OK";
                                }
                            }
                        }
                    }
                }
                // Retrieve form data
                
            }
    
        }  catch (\Exception $e) {
        $respObjects["msg"] = $e->getMessage();
            //throw $th;
            $codeStatut="ERROR";
        }
        
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/delete_user/{id}', name: 'delete_user',methods:['DELETE'])]
    public function deleteUser(EntityManagerInterface $entityManager, Request $request, ValidationService $validator, $id): Response
    {

        $user = $entityManager->getRepository(Utilisateurs::class)->findOneBy(array('id' => $id));
        $response = "";

        if (!$user) {
            $response = "Ce profile n'existe pas !";
        } else {
            $workflow = $entityManager->getRepository(Workflow::class)->findOneBy(array('id_user' => $id));
            $mission = $entityManager->getRepository(Missions::class)->findOneBy(array('id_users' => $id));
            if($workflow){
                $response="Cet utilisateur est affecté à un workflow";
            }
            elseif($mission)
            {
                $response="Cet utilisateur est affecté à un mission";
            }
            else{
                $token = $entityManager->getRepository(Token::class)->findOneBy(array('userIdent' => $id));
                if($token)
                $entityManager->remove($token);
                // $existingImagePath = $this->getParameter('kernel.project_dir') . '/public' . $user->getImg();
    
                // if ($existingImagePath !== null && file_exists($existingImagePath)) {
                //     unlink($existingImagePath);
                // }
    
                $entityManager->remove($user);
                $entityManager->flush();
                $response = "OK";
            }
        }
        return new JsonResponse($response);
    }

    ////////////////////////////////////// Get data to Angular Views /////////////////////////////////////////////////////////////////////////////

    #[Route('/getListesRoles', name: 'get_role', methods: ['GET'])]
    public function getAllRoles(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $response = [];
        $result = [];
        $Roles = $entityManager->getRepository(ListesRoles::class)->findAll();

        foreach ($Roles as $role) {

            $response = [
                'id' => $role->getId(),
                'code'  => $role->getCode(),
                'titre' => $role->getTitre(),
                'groupe' => $role->getGroupe()
            ];
            array_push($result, $response);
        }
        return new JsonResponse($result);
    }
    
    #[Route('/getCompetences', name: 'get_competence', methods: ['GET'])]
    public function getAllCompetence(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $response = [];
        $result = [];
        $competences = $entityManager->getRepository(Competence::class)->findAll();

        foreach ($competences as $competence) {

            $response = [
                'id' => $competence->getId(),
                'titre' => $competence->getTitre()
            ];
            array_push($result, $response);
        }

        return new JsonResponse($result);
    }
    #[Route('/getProfils', name: 'get_Profils', methods: ['GET'])]
    public function getAllProfils(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $response = [];
        $result = [];
        $profils = $entityManager->getRepository(Profil::class)->findAll();

        foreach ($profils as $profil) {

            $response = [
                'id' => $profil->getId(),
                'titre' => $profil->getTitre(),
                'date_creation' => $profil->getDateCreation()
            ];
            array_push($result, $response);
        }

        return new JsonResponse($result);
    }
    #[Route('/getProfil/{id}', name: 'get_Profil', methods: ['GET'])]
    public function getProfil(Request $request, $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $response = [];
        $result = [];
        $profil = $entityManager->getRepository(Profil::class)->findOneBy(['id' => $id]);
        $competences = $entityManager->getRepository(CompetenceProfil::class)->findBy(['id_profil' => $id]);
        $roles = $entityManager->getRepository(Roles::class)->findBy(['id_profil' => $id]);

        $response = [
            'id' => $profil->getId(),
            'titre' => $profil->getTitre(),
            // 'status' => $profil->getStatus(),
            'date_creation' => $profil->getDateCreation(),
        ];

        $comp = "SELECT cp.*, c.* FROM competence_profil cp JOIN competence c ON c.id = cp.id_competence_id WHERE cp.id_profil_id = " . $id;
        $stmt = $this->connection->prepare($comp);
        $stmt = $stmt->executeQuery();
        $resulatListCompetence = $stmt->fetchAllAssociative();

        $role = "SELECT r.*, lr.* FROM roles r JOIN listes_roles lr ON r.id_role_id = lr.id WHERE r.id_profil_id = " . $id;
        $stmt = $this->connection->prepare($role);
        $stmt = $stmt->executeQuery();
        $resulatListRoles = $stmt->fetchAllAssociative();

        // array_push($result, $response,$resulatListCompetence,$resulatListRoles);
        $result = [
            'profil' => $response,
            'competence' => $resulatListCompetence,
            'role' => $resulatListRoles
        ];

        return new JsonResponse($result);
    }

    #[Route('/getGroupes', name: 'get_Groupes', methods: ['GET'])]
    public function getAllGroupes(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $response = [];
        $result = [];
        $groupes = $entityManager->getRepository(Groupe::class)->findAll();

        foreach ($groupes as $groupe) {

            $response = [
                'id' => $groupe->getId(),
                'titre' => $groupe->getTitre(),
                'status' => $groupe->getStatus(),
                'date_creation' => $groupe->getDateCreation()
            ];
            array_push($result, $response);
        }
        
        return new JsonResponse($result);
    }

    #[Route('/getGroupe/{id}', name: 'get_groupe', methods: ['GET'])]
    public function getGroupe(Request $request, $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $result = [];
        $comp = "SELECT gr.*, gp.id_profil_id FROM group_profil gp JOIN `groupe` AS gr ON gr.id = gp.id_group_id WHERE gr.id = " . $id;
        $stmt = $this->connection->prepare($comp);
        $stmt = $stmt->executeQuery();
        $resulatGroupe = $stmt->fetchAllAssociative();
        $result = [
            'groupe' => $resulatGroupe,
        ];

        return new JsonResponse($result);
    }

    #[Route('/getusers', name: 'get_users', methods: ['GET'])]
    public function getAllusers(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);

            $user = "SELECT u.* FROM utilisateurs u " ;
            $stmt = $this->connection->prepare($user);
            $stmt = $stmt->executeQuery();
            $resulatUser = $stmt->fetchAllAssociative();
            $respObjects["data"] = $resulatUser;
            $codeStatut="OK";
        } catch (\Exception $e) {
            $codeStatut="ERROR";
        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getTypeUsers')]
    public function getTypeUsers(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            // $this->AuthService->checkAuth(0,$request);

            $user = "SELECT u.* FROM type_utilisateur u " ;
            $stmt = $this->connection->prepare($user);
            $stmt = $stmt->executeQuery();
            $resulatUser = $stmt->fetchAllAssociative();
            $respObjects["data"] = $resulatUser;
            $codeStatut="OK";
        } catch (\Exception $e) {
            $codeStatut="ERROR";
        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getuser/{id}', name: 'get_user', methods: ['GET'])]
    public function getOneUser(Request $request, EntityManagerInterface $entityManager,$id,SerializerInterface $serializer): JsonResponse
    {
        // $user = "SELECT u.*, g.titre FROM utilisateurs u ,groupe g WHERE u.id_group_id = g.id AND u.id =" . $id;
        $user = "SELECT u.* FROM utilisateurs u where u.id =" . $id;
        $stmt = $this->connection->prepare($user);
        $stmt = $stmt->executeQuery();
        $resulatUser = $stmt->fetchAllAssociative();
        return new JsonResponse($resulatUser);
    }

    #[Route('/department' ,methods:['POST'])]
    public function AddDepartment(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $data = json_decode($request->getContent(), true);
            $department = $data['department'];
            
            if(!empty($department)){
                $exist = $this->userRepo->getDepartmentByName($department);
                if(!$exist){
                    $this->userRepo->saveDepartment($department,null);
                    $codeStatut = 'OK';
                }else{
                    $codeStatut="ELEMENT_DEJE_EXIST";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        $respObjects["et"] = $e->getMessage();

        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/department' ,methods:['PUT'])]
    public function updateDepartment(Request $request ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $data = json_decode($request->getContent(), true);
            $departmentId = $data['id'];
            $departmentName = $data['department'];

            if(!empty($departmentName)){
                $exist = $this->userRepo->getDepartmentByName($departmentName);
                $department = $this->userRepo->getDepartment($departmentId);
                if(!$exist || ( $exist && $exist->getId() == $departmentId)){
                    $this->userRepo->saveDepartment($departmentName , $department);
                    $codeStatut = 'OK';
                }else{
                    $codeStatut="ELEMENT_DEJE_EXIST";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        $respObjects["et"] = $e->getMessage();

        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/department' ,methods:['GET'])]
    public function listDepartment(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $respObjects['data'] = $this->userRepo->getListDepartment();

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/department/{id}' ,methods:['GET'])]
    public function getDepartment(Request $request, $id ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $respObjects['data'] = $this->userRepo->getDepartment($id);

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }



    #[Route('/getuserCompetence/{id}', name: 'get_user_comp', methods: ['GET'])]
    public function getUserCompetence(Request $request, EntityManagerInterface $entityManager,$id,SerializerInterface $serializer): JsonResponse
    {
        // $comp = "SELECT COUNT(*) FROM utilisateurs u ,group_profil gp , competence_profil c WHERE u.id_group_id = gp.id_group_id 
        // AND  c.id_profil_id = gp.id_profil_id And c.status = 1 And u.id =" . $id;
        $comp = "SELECT cp.titre ,c.status
        FROM utilisateurs u ,group_profil gp , competence_profil c ,competence cp
        WHERE u.id_group_id = gp.id_group_id
        AND  c.id_profil_id = gp.id_profil_id
        AND cp.id = c.id_competence_id
        
        And u.id = ".$id;
        $stmt = $this->connection->prepare($comp);
        $stmt = $stmt->executeQuery();
        $resulatUser = $stmt->fetchAllAssociative();
        return new JsonResponse($resulatUser);
    }
    function formatPhoneNumber($phoneNumber) {
        $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);
        $areCode="212";
    
        $number = substr($phoneNumber,-9);
        if(strlen($phoneNumber) >= 10) {
            $phoneNumber = $areCode . $number;
        }else if(strlen($phoneNumber) == 9){
            $startNumber = substr($phoneNumber,0,1);
            if($startNumber == 5 || $startNumber== 6 || $startNumber== 7 ){
                $phoneNumber = $areCode . $number;
            }
        }
        return $phoneNumber;
    }


    #[Route('/oneDepartment' ,methods:['GET'])]
    public function oneDepartment(Request $request ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";

        try {
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get('id');
            $respObjects['data'] = $this->userRepo->getDepartment($id);
            $codeStatut="OK";

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/department/{id}' ,methods:['DELETE'])]
    public function deleteDepartment(Request $request, $id ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $department = $this->userRepo->getDepartment($id);
            $entityManager->remove($department);
            $entityManager->flush();
            $codeStatut="OK";
        } catch (\Exception $e) {
            $codeStatut=$e->getMessage();
        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getUtilisateursLibres' ,methods:['GET'])]
    public function getUtilisateursLibres(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $respObjects['data'] = $this->userRepo->getUtilisateursLibres();
            $codeStatut="OK";

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/addEquipe' ,methods:['POST'])]
    public function AddEquipe(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $data = json_decode($request->getContent(), true);
            $equipe = $data['equipe'];
            $department = $data['selectedDepartment'];
            $users = $data['users'];

            if(!empty($equipe) && !empty($department)){
                $exist = $this->userRepo->getEquipeByNameAndDepartement($equipe,$department);
                if(!$exist){
                    $this->userRepo->saveEquipe($equipe,$department,$users,null);
                    $codeStatut = 'OK';
                }else{
                    $codeStatut="ELEMENT_DEJE_EXIST";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        $respObjects["et"] = $e->getMessage();

        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }


    #[Route('/getTeams' ,methods:['GET'])]
    public function getTeams(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $equipes = $this->userRepo->getAllEquipes();
           // Transform each equipe into an array
            foreach ($equipes as $equipe) {
                $result[] = [
                    'id' => $equipe->getId(),
                    'name' => $equipe->getTeam(), // Replace with the actual field names
                    'createdAt' => $equipe->getDateCreation(), // Replace with the actual field names
                    'department' => $equipe->getIdDepartement()->getNom(), // Replace with the actual field names
                   
                ];
            }
            $respObjects["data"] = $result;

            $codeStatut="OK";

        } catch (\Exception $e) {
            $codeStatut="ERROR";
            $respObjects["et"] = $e->getMessage();

        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/deleteTeam/{id}' ,methods:['DELETE'])]
    public function deleteTeam(Request $request, $id ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $equipe = $this->userRepo->getOneEquipe($id);
            $users = $equipe->getUsers();
            foreach ($users as $user) {
                $user->setTeams(null);
            };
            $entityManager->remove($equipe);
            $entityManager->flush();
            $codeStatut="OK";
        } catch (\Exception $e) {
            $codeStatut=$e->getMessage();
        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/getTeam/{id}' ,methods:['GET'])]
    public function getTeam(Request $request, $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $equipe = $this->userRepo->getOneEquipe($id);
            $result = [
                'id' => $equipe->getId(),
                'name' => $equipe->getTeam(), // Replace with the actual field names
                'createdAt' => $equipe->getDateCreation(), // Replace with the actual field names
                'department' => $equipe->getIdDepartement()->getId(), // Replace with the actual field names
               
            ];
            $respObjects["data"] = $result;

            $codeStatut="OK";

        } catch (\Exception $e) {
            $codeStatut="ERROR";
            $respObjects["et"] = $e->getMessage();

        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }


    #[Route('/getUtilisateursLibresAndSelected/{id}' ,methods:['GET'])]
    public function getUtilisateursLibresAndSelected(Request $request,$id ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            
            $this->AuthService->checkAuth(0,$request);

            $respObjects['data'] = $this->userRepo->getUtilisateursLibresAndSelected($id);
            $codeStatut="OK";

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }


    #[Route('/updateTeam' ,methods:['PUT'])]
    public function updateTeam(Request $request ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            
            $data = json_decode($request->getContent(), true);
            $equipeId = $data['id'];
            $equipe = $data['equipe'];
            $department = $data['selectedDepartment'];
            $users = $data['users'];

            if(!empty($equipe) && !empty($department)){
                $exist = $this->userRepo->getEquipeByNameAndDepartement($equipe,$department);
                if(!$exist || ($exist && $exist->getId() == $equipeId)){
                    $this->userRepo->saveEquipe($equipe,$department,$users,$equipeId);
                    $codeStatut = 'OK';
                }else{
                    $codeStatut="ELEMENT_DEJE_EXIST";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        
        } catch (\Exception $e) {
            $codeStatut="ERROR";
        $respObjects["et"] = $e->getMessage();

        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }


    #[Route('/getUserInfos',methods:['GET'])]
    public function getUserInfos(Request $request ,EntityManagerInterface $entityManager): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {


            $senderId = $this->AuthService->returnUserId($request);
            $user = $this->userRepo->getUser($senderId);
            $respObjects["user"] = $user;

            $equipe = null;
            if($user["teams_id"])
            {
                $equipe = $this->userRepo->getTeam($user["teams_id"]);
            }
            $respObjects["equipe"] = $equipe;   

            $department = null;
            if($equipe["id_departement_id"])
            {
                $department = $this->userRepo->getOneDepatement($equipe["id_departement_id"]); 
            }
            $respObjects["department"] = $department;

            $users = null;
            if($equipe)
            {
                $users = $this->userRepo->getUsersTeam($equipe["id"]); 
            }
            $respObjects["users"] = $users;

            $equipes = null;
            if($department)
            {
                $equipes = $this->userRepo->getTeamsDepartement($department["id"]); 
            }
            $respObjects["equipes"] = $equipes;



            $codeStatut="OK";

        } catch (\Exception $e) {
            $codeStatut="ERROR";
        $respObjects["et"] = $e->getMessage();

        }   
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }




}
