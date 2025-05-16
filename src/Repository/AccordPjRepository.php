<?php

namespace App\Repository;

use App\Entity\Accord;
use App\Entity\AccordPj;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;

/**
 * @extends ServiceEntityRepository<AccordPj>
 *
 * @method AccordPj|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccordPj|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccordPj[]    findAll()
 * @method AccordPj[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccordPjRepository extends ServiceEntityRepository
{
    private $conn;
    public function __construct(ManagerRegistry $registry,Connection $conn)
    {
        parent::__construct($registry, AccordPj::class);
        $this->conn = $conn;

    }

    public function saveFileBase64($id, $fileBase64, $name)
    {
        $entityManager = $this->getEntityManager();
    
        // Updated SQL to include the `nom` column
        $sql = "INSERT INTO `accord_pj` (`id_accord_id`, `url`, `nom`) VALUES (:id, :pj, :name);";
    
        // Prepare and execute the query
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->bindParam('pj', $fileBase64);
        $stmt->bindParam('name', $name);
        $stmt->executeQuery();
    }
    

    public function deleteAttachment($attachmentId)
    {
        $sql = "
            DELETE FROM accord_pj
            WHERE id = :id
        ";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $attachmentId, \PDO::PARAM_INT);
        return $stmt->execute();  // Executes the deletion query
    }
    

//    /**
//     * @return AccordPj[] Returns an array of AccordPj objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AccordPj
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
