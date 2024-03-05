<?php

namespace App\Repository\Customer;

use App\Entity\Customer\creanceAccordDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<creanceAccordDbi>
 *
 * @method creanceAccordDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method creanceAccordDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method creanceAccordDbi[]    findAll()
 * @method creanceAccordDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class creanceAccordDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, creanceAccordDbi::class);
    }

    public function save(creanceAccordDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(creanceAccordDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return creanceAccordDbi[] Returns an array of creanceAccordDbi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?creanceAccordDbi
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
