<?php

namespace App\Repository\Customer;

use App\Entity\Customer\PaiementAccordDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaiementAccordDbi>
 *
 * @method PaiementAccordDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaiementAccordDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaiementAccordDbi[]    findAll()
 * @method PaiementAccordDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaiementAccordDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaiementAccordDbi::class);
    }

    public function save(PaiementAccordDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PaiementAccordDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PaiementAccordDbi[] Returns an array of PaiementAccordDbi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PaiementAccordDbi
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
