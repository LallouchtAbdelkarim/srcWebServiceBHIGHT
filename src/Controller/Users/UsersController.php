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
use App\Entity\Profil;
use App\Entity\Roles;
use App\Entity\Utilisateurs;
use App\Entity\Workflow;
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
    public function __construct(Connection $connection,  
    MessageService $MessageService,
    AuthService $AuthService,
    JWTEncoderInterface $JWTManager)
    {
         $this->connection = $connection;
         $this->JWTManager = $JWTManager;
         $this->MessageService = $MessageService;
         $this->AuthService = $AuthService;
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
    
                foreach ($cp as $competence) {

                    $compObject = $entityManager->getRepository(CompetenceProfil::class)->findOneBy([
                        'id_profil' => $profil->getId(),
                        'id_competence' => $competence->getId(),
                    ]);
    
                    if (in_array($competence->getId(), $compTenc) && $data['comp'][$competence->getId()] === "on") {
                        $compObject->setStatus(1);
                    } else {
                        $compObject->setStatus(0);
                    }
                    $entityManager->persist($compObject);
                }
    
                $entityManager->flush();
                $codeStatut="OK";
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
            //code...
            $profile = $entityManager->getRepository(Profil::class)->findOneBy(array('id' => $id));
            $response = "";

        if (!$profile) {
            $codeStatut = "NOT_EXIST_ELEMENT";
        } else {
            $roles = $entityManager->getRepository(Roles::class)->findBy(array('id_profil' => $id));
            foreach ($roles as $role) {
                $entityManager->remove($role);
            }
            $competences = $entityManager->getRepository(CompetenceProfil::class)->findBy(array('id_profil' => $id));
            foreach ($competences as $competence) {
                $entityManager->remove($competence);
            }
            $gp = $entityManager->getRepository(GroupProfil::class)->findBy(array('id' => $id));
            foreach ($gp as $item) {
                $entityManager->remove($item);
            }

            $entityManager->remove($profile);
            $entityManager->flush();
            $codeStatut = "OK";
        }
        } catch (\Exception $th) {
            //throw $th;
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
                $group = new Groupe();
                $group->setTitre($data['titre']);
                // $group->setStatus($data['status']);
                $group->setDateCreation(new \DateTime());
                $entityManager->persist($group);
                $entityManager->flush();
                // print_r($data['ary']);
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
            }
        } catch (\Exception $th) {
            $codeStatut="ERROR";
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

            if ($data['titre'] == null || $data['status'] == null || $data['ary'] == null) {
                $response = "Un des champs est vide";
            } else {
                $groupe->setTitre($data['titre']);
                $groupe->setStatus($data['status']);
                $groupe->setDateCreation(new \DateTime());
                $entityManager->persist($groupe);
                $entityManager->flush();
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
                $response = "Modified successfully";
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
            $supp = $request->request->get('supp');
            $imei = $request->request->get('imei');
            $rayon = $request->request->get('rayon');
            $pass = $request->request->get('pass');
            $pass1 = $request->request->get('pass1');
            $status = $request->request->get('status');
            $email = $request->request->get('email');
            $type = $request->request->get('type');
            $competence = $request->request->get('competence');
            $responsable = $request->request->get('responsable');
            
    
            // Get the image file from the request
            $img = $request->files->get('img');
            if(empty(trim($nom)) || empty(trim($prenom)) ||  empty(trim($this->formatPhoneNumber($tel))) || empty(trim($pass)) ){
                $codeStatut = "EMPTY-DATA";
            }else{
                if ($pass !== $pass1) {
                    $codeStatut="PASSWORD_MATCH";
                } else {
                    $existUser = $entityManager->getRepository(Utilisateurs::class)->findOneBy(['nom' => $nom ,'prenom' => $prenom  ]);

                    if($existUser){
                        $codeStatut="ELEMENT_DEJE_EXIST";
                    }else{
                        $group = $entityManager->getRepository(Groupe::class)->findOneBy(['id' => $grpr]);
                        $typeUser = $entityManager->getRepository(TypeUtilisateur::class)->findOneBy(['id' => $type]);
                        $competenceEntity = $entityManager->getRepository(Competence::class)->findOneBy(['id' => $competence]);
                        $responsableEntity = $entityManager->getRepository(TypeUtilisateur::class)->findOneBy(['id' => $responsable]);
                        if($typeUser->getOrdre() < $responsableEntity->getOrdre()){
                            $codeStatut="RESPONSABILITY_ERROR";
                        }else{
                            if($group != null && $typeUser != null){
                                // Create a new user entity
                                $user = new Utilisateurs();
                                $hashedPassword = md5($pass);
                                $user->setNom($nom);
                                $user->setPrenom($prenom);
                                $user->setPassword($hashedPassword);
                                // $user->setStatus($status);
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
                            }else{
                                $codeStatut="EMPTY-DATA";
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
            // Get the image file from the request
            $img = $request->files->get('img');
            if(empty(trim($nom)) || empty(trim($prenom)) ||  empty(trim($this->formatPhoneNumber($tel)))  ){
                $codeStatut = "EMPTY-DATA";
            }else{
                if ($pass !== $pass1) {
                    $codeStatut="PASSWORD_MATCH";
                } else {
                    if(empty(trim($nom)) or empty(trim($nom))){
                        $codeStatut = "EMPTY-DATA";
                    }else{
                        $group = $entityManager->getRepository(Groupe::class)->findOneBy(['id' => $grpr]);
                        $typeUser = $entityManager->getRepository(TypeUtilisateur::class)->findOneBy(['id' => $type]);
                        $competenceEntity = $entityManager->getRepository(Competence::class)->findOneBy(['id' => $competence]);
                        $responsableEntity = $entityManager->getRepository(TypeUtilisateur::class)->findOneBy(['id' => $responsable]);
                        if($typeUser->getOrdre() < $responsableEntity->getOrdre()){
                            $codeStatut="RESPONSABILITY_ERROR";
                        }else{
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
                            $user->setIdCompetence($competenceEntity);
                            $user->setResponsable($responsableEntity);
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
            // Retrieve form data
            
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
            if($workflow){
                $response="Cet utilisateur est affecté à un workflow";
            }else{
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
}
