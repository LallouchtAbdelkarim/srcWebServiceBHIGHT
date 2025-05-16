<?php

namespace App\Repository;

use App\Entity\Dossier;
use App\Entity\NoteDossier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;

/**
 * @extends ServiceEntityRepository<Dossier>
 *
 * @method Dossier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dossier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dossier[]    findAll()
 * @method Dossier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DossierRepository extends ServiceEntityRepository
{

    private $conn;
    public function __construct(ManagerRegistry $registry,Connection $conn)
    {
        parent::__construct($registry, Dossier::class);
        $this->conn = $conn;
    }

    public function save(Dossier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Dossier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function createNoteDossier($id,$note): void
    {
        $dossier = $this->getEntityManager()->getRepository(Dossier::class)->findOneBy(['id' => $id]);
        $integration = new NoteDossier();
        $integration->setNote($note);
        $integration->setIdDossier($dossier);
        $this->em->persist($integration);
        $this->em->flush();
    }

    public function UpdateGestDossier($id, $timer, $from)
    {
        if ($from == 1) {
            $sqlUpdateDossier = "
                UPDATE dossier
                SET date_fin_prevesionnel = NOW()
                WHERE id = :id
            ";

            $stmtUpdate = $this->conn->prepare($sqlUpdateDossier);
            $stmtUpdate->bindParam(':id', $id);
            $stmtUpdate->execute();
            
        }
    
        // Insert into HistoriqueTimer table
        $sqlInsertHistorique = "
            INSERT INTO historique_timer (id_dossier_id , timer, date)
            VALUES (:id, :timer, NOW())
        ";
    
        $stmtInsert = $this->conn->prepare($sqlInsertHistorique);
        $stmtInsert->bindParam(':id', $id);
        $stmtInsert->bindParam(':timer', $timer);
        $stmtInsert->execute();
    }


    public function getNextDossier($userLogin)
    {
        $sql = "
            SELECT * 
            FROM dossier d
            WHERE d.date_fin IS NULL 
            AND d.id_user_assign_id = :userLogin 
            AND NOT EXISTS (
                SELECT 1 
                FROM historique_timer ht 
                WHERE ht.id_dossier_id = d.id 
                AND DATE(ht.date) = CURDATE()
            )
            ORDER BY d.date_fin_prevesionnel ASC 
            LIMIT 1
        ";        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':userLogin', $userLogin);
        $stmt = $stmt->executeQuery();
        $resultat = $stmt->fetchAll();
        return $resultat;
    }

    public function getQueueNextDossier()
    {
        $sql = "
            SELECT * 
            FROM dossier d
            WHERE d.date_fin IS NULL  
            AND d.id IN (
                SELECT sd.id_dossier 
                FROM debt_force_seg.seg_dossier sd 
                WHERE sd.id_seg IN (
                    SELECT q.id_segmentation_id 
                    FROM queue q 
                    WHERE q.id_type_id = 1
                )
            )
            AND NOT EXISTS (
                SELECT 1 
                FROM historique_timer ht 
                WHERE ht.id_dossier_id = d.id 
                AND DATE(ht.date) = CURDATE()
            )
            ORDER BY d.date_fin_prevesionnel ASC 
            LIMIT 1
        ";        
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resultat = $stmt->fetchAll();
        return $resultat;
    }
    
    //    /**
//     * @return Dossier[] Returns an array of Dossier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Dossier
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
