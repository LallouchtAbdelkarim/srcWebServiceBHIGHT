<?php

namespace App\Repository\Users;
use App\Entity\Departement;
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
    public function saveDepartment($titre, ?Departement $department = null) {
        if (!$department) {
            $department = new Departement();
            $department->setDateCreation(new \DateTime());
        }
        $department->setNom($titre);
        $this->em->persist($department);
        $this->em->flush();
    }
    
    public function getDepartmentByName($titre){
        return $this->em->getRepository(Departement::class)->findOneBy(['nom'=>$titre]);
    }
    public function getDepartment($id){
        return $this->em->getRepository(Departement::class)->findOneBy(['id'=>$id]);
    }
    public function getListDepartment(){
        return $this->em->getRepository(Departement::class)->findAll();
    }
          
}