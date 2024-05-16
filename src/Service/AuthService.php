<?php


namespace App\Service;

use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Proxies\__CG__\App\Entity\ListesRoles;
use Proxies\__CG__\App\Entity\Roles;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Doctrine\DBAL\Connection;

class AuthService
{
    public $em;
    private  $serializer;
    private $conn;


    public function __construct(EntityManagerInterface $em,Connection $conn,  private JWTEncoderInterface $JWTManager  ){
        $this->em = $em;
        $this->conn = $conn;
    }

    public function checkAuth($codeFunction,$request)
    {
        $jwt = $request->headers->get('Authorization');        
        $isConnected = false;
        $jwt = substr($jwt,7);
        $data = $this->JWTManager->decode($jwt);
        $dataAgent = $data["id"];
        if ($dataAgent) {
            $isConnected = true;
            if($codeFunction != 0){
                return $this->Role($codeFunction,$request);
            }
        }
        return $isConnected;
    }
    
    public function returnUserId($request)
    {
        $jwt = $request->headers->get('Authorization');        
        $isConnected = false;
        if($jwt){
            $jwt = substr($jwt,7);
            $data = $this->JWTManager->decode($jwt);
            $dataAgent = $data["id"];
                if ($dataAgent) {
                    $isConnected = true;
                }
                return $dataAgent;
        }else{
            return false;
        }
    }
    function Role($codeFunction,$request){
        $id=$this->returnUserId($request);
        $userStatut = $this->em->getRepository(Utilisateurs::class)->findBy(["id"=>$id , "superAdmin"=>false]);
        if($userStatut){
            return true;
        }else{
            $sql="SELECT l.*
                FROM listes_roles l
                JOIN roles r ON l.id = r.id
                JOIN group_profil gp ON r.id_profil_id = gp.id_profil_id
                JOIN groupe g ON gp.id_group_id = g.id
                JOIN utilisateurs u ON g.id = u.id_group_id
                WHERE l.code = :code
                AND r.STATUS = 1
                AND u.id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":code", $codeFunction);
            $stmt->bindValue(":id",$id);
            $stmt = $stmt->executeQuery();
            $statut = $stmt->fetchOne();
            if ($statut){
                return true;
            }else
            {
              return throw new \Exception("Access Denied");
            }
        }
    }
}