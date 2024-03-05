<?php

namespace App\Repository\Customer;

use App\Entity\Customer\debiDossDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<debiDossDbi>
 *
 * @method debiDossDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method debiDossDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method debiDossDbi[]    findAll()
 * @method debiDossDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class debiDossDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, debiDossDbi::class);
    }

    public function save(debiDossDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(debiDossDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return debiDossDbi[] Returns an array of debiDossDbi objects
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

//    public function findOneBySomeField($value): ?debiDossDbi
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
