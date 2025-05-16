<?php

namespace App\Repository\Users;
use App\Entity\Departement;
use App\Entity\Teams;
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
        $sql="SELECT u.* FROM utilisateurs u where u.id = :id";
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
        $sql="SELECT u.* FROM utilisateurs u where u.id = :id";
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
    public function saveDepartment($titre, ?Departement $department) {
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

    public function getOneDepatement($id){
        $sql="SELECT d.* FROM departement d where d.id = :id";
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


    public function getListDepartment(){
        return $this->em->getRepository(Departement::class)->findAll();
    }


    public function getUtilisateursLibres(){
        $sql="SELECT u.* FROM utilisateurs u where teams_id is null";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchAllAssociative();
        if($statut){
            return $statut;
        }else{
            return null;
        }
    }



    public function getEquipeByNameAndDepartement($nom,$departement){
        return $this->em->getRepository(Teams::class)->findOneBy(['team'=>$nom,'id_departement'=>$departement]);
    }

    public function saveEquipe($nom, $departement, $users, $id = null) {
        if (!$id) {
            $team = new Teams();
            $team->setDateCreation(new \DateTime());
        } else {
            $team = $this->em->getRepository(Teams::class)->findOneBy(['id' => $id]);
    
            // Clear existing users if needed
            $existingUsers = $team->getUsers();
            foreach ($existingUsers as $existingUser) {
                $team->removeUser($existingUser); // Adjust to your Teams entity's method
            }
        }
    
        // Set the department
        $dep = $this->em->getRepository(Departement::class)->findOneBy(['id' => $departement]);
        $team->setTeam($nom);
        $team->setIdDepartement($dep);
    
        // Add users to the team
        foreach ($users as $userId) {
            $user = $this->em->getRepository(Utilisateurs::class)->findOneBy(['id' => $userId]);
            
            if ($user) {
                // Add user to the team if it exists
                $team->addUser($user);
            } 
        }
    
        // Save the team
        $this->em->persist($team);
        $this->em->flush();
    }
    

    public function getAllEquipes(){
        return $this->em->getRepository(Teams::class)->findAll();
    }

    public function getOneEquipe($id){
        return $this->em->getRepository(Teams::class)->find($id);
    }

    public function getTeam($id){
        $sql="SELECT t.* FROM teams t where t.id = :id";
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


    public function getUtilisateursLibresAndSelected($id){
        $sql="SELECT u.* FROM utilisateurs u where teams_id is null or teams_id = :id";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchAllAssociative();
        if($statut){
            return $statut;
        }else{
            return null;
        }
    }

    public function getUsersTeam($id){
        $sql="SELECT u.* FROM utilisateurs u where u.teams_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchAllAssociative();
        if($statut){
            return $statut;
        }else{
            return null;
        }
    }


    public function getTeamsDepartement($id){
        $sql="SELECT t.* FROM teams t where t.id_departement_id  = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $statut = $stmt->fetchAllAssociative();
        if($statut){
            return $statut;
        }else{
            return null;
        }
    }

          
}