<?php

namespace App\Repository\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\Utilisateurs;

class userRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    public function getOneUser($id){
        $sql="SELECT u.* FROM Utilisateurs u where u.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchOne();
        if($statut){
            return $statut;
        }else{
            return null;
        }
    }
    public function getUser($id){
        $sql="SELECT u.* FROM Utilisateurs u where u.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchAssociative();
        if($statut){
            return $statut;
        }else{
            return null;
        }
    }
    
}