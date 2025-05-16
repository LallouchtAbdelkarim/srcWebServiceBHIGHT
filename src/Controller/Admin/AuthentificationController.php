<?php

namespace App\Controller\Admin;

use App\Entity\Token;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MessageService;
use App\Service\AuthService;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

#[Route('/API'  ) ]

class AuthentificationController extends AbstractController
{
    // private $auth_service;
    private $AuthService;
    private $JWTManager;
    private $MessageService;

    public function __construct(
        AuthService $AuthService,
        MessageService $MessageService , 
        JWTEncoderInterface $JWTManager
        )
    {
        $this->MessageService = $MessageService;
        $this->JWTManager = $JWTManager;
        $this->AuthService = $AuthService;
    }
    
    #[Route('/checkWs' , methods:["GET"]) ]
    public function home(Request $request , EntityManagerInterface $em): JsonResponse
    {
        $codeStatut = "ERROR";
        try {
            //code...
            $this->AuthService->checkAuth(0,$request);
            $codeStatut="OK";
        } catch (\Exception $e) {
            if($e->getMessage() == "Invalid JWT Token"){
                $codeStatut=$e->getMessage();
            }else if($e->getMessage() == "Expired JWT Token"){
                $codeStatut=$e->getMessage();
            }else{
                $codeStatut="ERROR";
            }
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/login' , methods:["POST"]) ]
    public function index(Request $request , EntityManagerInterface $em): JsonResponse
    {
        $codeStatut="ERROR";
        try {
            $login = $request->get("login");
            $password = $request->get("password");
            $respObjects=array();
            if (!empty($login) && !empty($password)) {
                $user = $em->getRepository(Utilisateurs::class)->findOneBy(["email"=>$login , "password"=>md5($password)]);
                if($user  ){
                    $data = array("email"=>$login , "id"=>$user->getId());
                    $tokenJWT = $this->JWTManager->encode($data);
                    //Persist token
                    $token = $em->getRepository(token::class)->findOneBy(["userIdent"=>$user->getId()]);
                    if ($token) {
                        $token->setToken($tokenJWT);
                    } else {
                        $token = new Token();
                        $token->setToken($tokenJWT);
                        $token->setUserIdent($user);
                        $em->persist($token);
                    }
                    $em->flush();
                    $codeStatut = "OK";
                    $respObjects["id"] = $user->getId();
                    $respObjects["nom"] = $user->getNom();
                    $respObjects["prenom"] = $user->getPrenom();
                    $respObjects["token"] = $tokenJWT;
                    $respObjects["services"] = $user->getServices();

                }else{
                    $codeStatut=$login;
                }
            }else{
                $codeStatut="EMPTY-DATA";
            }
        } catch (\Exception $e) {
            $respObjects["err"] = $e->getMessage();
        }
        
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        
        return $this->json($respObjects);
    }

    #[Route('/logout' , methods:["POST"]) ]
    public function logout(Request $request , EntityManagerInterface $em): JsonResponse
    {
        $codeStatut="ERROR";
        $respObjects =array();
        try {
        $request->headers->set('Authorization',null);   
        $codeStatut="OK";
        } catch (\Exception $e) {
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/check_auth' )]
    public function check_auth(Request $request , EntityManagerInterface $em): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try {
            $this->AuthService->checkAuth(0,$request);
            $codeStatut="OK";
        } catch (\Exception $e) {
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
}
