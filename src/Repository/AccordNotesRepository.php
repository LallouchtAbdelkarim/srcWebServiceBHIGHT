<?php

namespace App\Repository;

use App\Entity\Accord;
use App\Entity\AccordNotes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccordNotes>
 *
 * @method AccordNotes|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccordNotes|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccordNotes[]    findAll()
 * @method AccordNotes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccordNotesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccordNotes::class);
    }

    public function createAccordNote(array $data): AccordNotes
    {
        $entityManager = $this->getEntityManager();

        $acc = $entityManager->getRepository(Accord::class)->findOneBy(["id" => $data['idAccord']]);
        if($acc)
        {
            $accordNote = new AccordNotes();
            $accordNote->setIdAccord($acc);
            $accordNote->setNote($data['note']);
            $accordNote->setDateNote($data['dateNote'] ?? null);
            $accordNote->setDateCreation($data['dateCreation']);
    
            $entityManager->persist($accordNote);
            $entityManager->flush();
    
            return $accordNote;
    
        }
    }

    
         

//    /**
//     * @return AccordNotes[] Returns an array of AccordNotes objects
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

//    public function findOneBySomeField($value): ?AccordNotes
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
