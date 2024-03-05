<?php

namespace App\Repository\Missions;

use App\Entity\FileMissions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
class missionsRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    
    public function getTypeMissions(){
        $sql="SELECT * FROM `type_missions` ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getAllIFilleMissions()
    {
        $resultList = $this->em->getRepository(FileMissions::class)->findAll();
        if($resultList){
            return $resultList; 
        }else{
            return [];
        }
    }

    public function getOneFile($id){
        $sql="SELECT * FROM `file_missions` where id = ".$id." ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAssociative();

        if($result){
            $sql="SELECT * FROM `type_missions` where id = ".$result["id_type_missions_id"]." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $result["type_missions"] = $type;
        }
        return $result;
    }
    public function getDetailsFile($id){
        $sql="SELECT * FROM `details_file` where id_file_missions_id = ".$id." ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAll();
        return $result;
    }
    
    
}

