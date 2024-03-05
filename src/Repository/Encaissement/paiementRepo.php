<?php

namespace App\Repository\Encaissement;

use App\Entity\ImportPaiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
class paiementRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    public function createImport(){
        
    }
    public function getAllImportByStatus($etat){
        $resultList = $this->em->getRepository(ImportPaiement::class)->findBy(["status"=>$etat]);
        if($resultList){
            return $resultList; 
        }else{
            return null;
        }
    }
    
    public function getDetailsImprt($id , $ordre){
        // $resultList = $this->em->getRepository(ModelImport::class)->findOneBy(["id_integration"=>$Id , "type"=> $type]);
        $query = $this->em->createQuery('SELECT r FROM App\Entity\DetailsImportPaiement r WHERE r.id_import = :id and r.ordre =:ordre')
        ->setParameters([
            'id' => $id,
            'ordre' => $ordre 
        ])
        ->setMaxResults(1);
        $resultList = $query->getOneOrNullResult();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getDetailsAccord($id_creance){
        $query = $this->em->createQuery('SELECT d from App\Entity\DetailsAccord d where (d.id_status=4 or d.id_status=0) and identity(d.id_accord)
        in(select a.id from App\Entity\Accord a where a.id in(select identity(cr.id_accord) from App\Entity\CreanceAccord cr where identity(cr.id_creance)
        in(select c.id from App\Entity\Creance c where c.id=:id_creance)))')
        ->setParameters([
            'id_creance' => $id_creance,
        ])
        ->setMaxResults(1);
        $resultList = $query->getOneOrNullResult();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getPaiement($id_creance , $montant){
        $query = $this->em->createQuery('SELECT p from App\Entity\Paiement p where identity(p.id_details_accord) in(select d.id from App\Entity\DetailsAccord d where identity(d.id_accord)
        in(select a.id from App\Entity\Accord a where a.id in(select identity(cr.id_accord) from App\Entity\CreanceAccord cr where identity(cr.id_creance)
        in(select c.id from App\Entity\Creance c where c.id=:id_creance)))) and p.montant=:rest and p.confirmed=0')
        ->setParameters([
            'id_creance' => $id_creance,
            'rest' => $montant
        ])
        ->setMaxResults(1);
        $resultList = $query->getOneOrNullResult();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getRestantCreance($id_creance ){
        $query = $this->em->createQuery('SELECT c.totalRestant from App\Entity\Creance c where c.id = :id_creance')
        ->setParameters([
            'id_creance' => $id_creance,
        ])
        ->setMaxResults(1);
        $resultList = $query->getSingleScalarResult();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function testDetailsAccord($id_creance){
        $array = array();
        $query = $this->em->createQuery('SELECT d from App\Entity\DetailsAccord d where (d.id_status=4 or d.id_status=0) and identity(d.id_accord)
        in(select a.id from App\Entity\Accord a where a.id in(select identity(cr.id_accord) from App\Entity\CreanceAccord cr where identity(cr.id_creance)
        in(select c.id from App\Entity\Creance c where c.id=:id_creance)))')
        ->setParameters([
            'id_creance' => $id_creance,
        ])
        ->setMaxResults(1);
        $data = $query->getOneOrNullResult();

        if($data){
            $array["exist"]=true;
            $array["place"]=1;
            $array["accord"]=$data;
        }else{
            $query = $this->em->createQuery('SELECT d from App\Entity\DetailsAccord d where (d.id_status=4 or d.id_status=0) and identity(d.id_accord)
            in(select a.id from App\Entity\Accord a where a.id in(select identity(cr.id_accord) from App\Entity\CreanceAccord cr where identity(cr.id_creance)
            in(select c.id from App\Entity\Creance c where c.id=:id_creance)))')
            ->setParameters([
                'id_creance' => $id_creance,
            ])
            ->setMaxResults(1);
            $data = $query->getOneOrNullResult();
            if($data){
                $array["exist"]=true;
                $array["place"]=2;
                $array["accord"]=$data;
            }else{
                $array["exist"]=false;
            }
        }
        return $array;
    }
    public function checkIfUpdatedCreance($id_creance , $id_action){
        $sql="SELECT * FROM debt_force_integration.`creance_paiement_dbi` where id_creance = :id_creance  and id_action = :id_action ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_creance",$id_creance);
        $stmt->bindValue(":id_action",$id_action);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchAssociative();
        return $data;
    }

    public function getDetailsAccord2($numero_creance , $id_action ){
        $sql="SELECT * FROM details_accord d where (d.id_status_id = 4 or d.id_status_id = 0) and d.id_accord_id in (select a.id from accord a where a.id in(select (cr.id_accord_id) from creance_accord cr where (cr.id_creance_id)
        in(select c.id from creance c where c.numero_creance=:numero))) and d.id not in (SELECT dt.id from debt_force_integration.details_accord_paiement dt where dt.id_action = :id_action )";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":numero",$numero_creance);
        $stmt->bindValue(":id_action",$id_action);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchAll();
        return $data;
    }
    public function getDetailsAccord3($numero_creance){
        $sql="SELECT * FROM details_accord d where (d.id_status_id = 4 or d.id_status_id = 0) and d.id_accord_id in (select a.id from accord a where a.id in(select (cr.id_accord_id) from creance_accord cr where (cr.id_creance_id)
        in(select c.id from creance c where c.numero_creance=:numero))) ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":numero",$numero_creance);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchAll();
        return $data;
    }
    public function getAllInegration()
    {
        $resultList = $this->em->getRepository(ImportPaiement::class)->findAll();
        if($resultList){
            return $resultList; 
        }else{
            return null;
        }
    }
    public function getOneIntegration($id ){
        $query = $this->em->createQuery('SELECT r FROM App\Entity\ImportPaiement r where r.id = :id')
        ->setParameters([
            'id' => $id
        ])
        ->setMaxResults(1);
        $resultList = $query->getOneOrNullResult();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    function updateStatus($status, $id, $expectedStatus, $integrationRepo)
    {
        $integration = $this->getOneIntegration($id);
        if ($integration) {
            // Check if the current status is in the expectedStatus array
            if (in_array($integration->getStatus()->getId(), $expectedStatus)) {
                $this->anuulerIntegration($id, $status);
                return "OK";
            } else {
                return "ERROR";
            }
        } else {
            return "NOT_EXIST_ELEMENT";
        }
    }

    function anuulerIntegration($id , $status){
        $sql="UPDATE `import_paiement` SET `status_id`=:status WHERE id = :id";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(":id",$id);
        $stmt->bindValue(":status",$status);
        $stmt = $stmt->executeQuery();
    }
}

// $sql="
// SELECT d.*
// FROM debt_force_integration.details_accord_dbi d
// WHERE (d.status = 4 OR d.status = 0)
//     AND d.id_accord IN (
//         SELECT a.id 
//         FROM debt_force_integration.accord_dbi a 
//         WHERE a.id IN (
//             SELECT cr.id_accord 
//             FROM debt_force_integration.creance_accord_dbi cr 
//             WHERE cr.id_creance IN (
//                 SELECT c.id 
//                 FROM creance c 
//                 WHERE c.id = :id_creance
//             )
//         )
//     )
// ";
// $stmt = $this->conn->prepare($sql);
// $stmt->bindValue(":id_creance",$creance->getId());
// $stmt = $stmt->executeQuery();
// $data = $stmt->fetchOne();