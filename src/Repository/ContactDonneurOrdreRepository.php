<?php

namespace App\Repository;

use App\Entity\ContactDonneurOrdre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactDonneurOrdre>
 *
 * @method ContactDonneurOrdre|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactDonneurOrdre|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactDonneurOrdre[]    findAll()
 * @method ContactDonneurOrdre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactDonneurOrdreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactDonneurOrdre::class);
    }

    public function save(ContactDonneurOrdre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContactDonneurOrdre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ContactDonneurOrdre[] Returns an array of ContactDonneurOrdre objects
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

//    public function findOneBySomeField($value): ?ContactDonneurOrdre
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
