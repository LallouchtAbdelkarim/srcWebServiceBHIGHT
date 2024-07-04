<?php

namespace App\Repository;

use App\Entity\ModelCourier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModelCourier>
 *
 * @method ModelCourier|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModelCourier|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModelCourier[]    findAll()
 * @method ModelCourier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModelCourierRepository extends ServiceEntityRepository
{
    private $conn;
    public $em;
    public function __construct(ManagerRegistry $registry , Connection $conn , EntityManagerInterface $em)
    {
        parent::__construct($registry, ModelCourier::class);
        $this->conn = $conn;
        $this->em = $em;
    }
    public function save(ModelCourier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ModelCourier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ModelCourier[] Returns an array of ModelCourier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ModelCourier
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getListeInfos(){
        $sql="SELECT * FROM `detail_model_affichage` ;";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }

}
