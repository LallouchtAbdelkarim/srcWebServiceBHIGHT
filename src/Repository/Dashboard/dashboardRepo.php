<?php

namespace App\Repository\Dashboard;

use App\Entity\QueueEventUser;
use App\Entity\Utilisateurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
class dashboardRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    
    public function getNbrCreances(){
        $sql="SELECT count(*) AS nbrCreance FROM `creance` where etat != -1";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchOne();
        return $resulat;
    }

    public function getTotalCreance(){
        $sql="SELECT SUM(total_creance) FROM `creance` where etat != -1";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchOne();
        return $resulat;
    }
    public function getTotalRestant(){
        $sql="SELECT SUM(total_restant) FROM `creance` where etat != -1";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchOne();
        return $resulat;
    }

    public function getProcessOfUser($idUser){
        $resultList = $this->em->getRepository(QueueEventUser::class)->findBy(["id_user"=>$idUser , "id_status"=>2]);
        return $resultList;
    }
    public function getCountProcess($idUser){
        $sql="SELECT COUNT(id) FROM `queue_event_user` where id_user_id =  ".$idUser." and id_status_id = 2";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchOne();
        return $resulat;
    }
    public function getCountProcessByDepartement($idUser){
        $user = $this->getOneUser($idUser);
        $idDepa = $user->getIdDepartement()->getId();
        $sql="SELECT COUNT(id) FROM `queue_event_user` where  id_status_id = 2 and id_user_id in (select u.id from utilisateurs u where u.id_departement_id = ".$idDepa." )";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchOne();
        return $resulat;
    }
    public function getOneUser($idUser){
        $resultList = $this->em->getRepository(Utilisateurs::class)->find($idUser);
        return $resultList;
    }
}