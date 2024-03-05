<?php

namespace App\Repository\Customer;

use App\Entity\Customer\logsActionsDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<logsActionsDbi>
 *
 * @method logsActionsDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method logsActionsDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method logsActionsDbi[]    findAll()
 * @method logsActionsDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class logsActionsDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, logsActionsDbi::class);
    }

    public function save(logsActionsDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(logsActionsDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return logsActionsDbi[] Returns an array of logsActionsDbi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?logsActionsDbi
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
