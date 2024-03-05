<?php


namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class typeService
{
    
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }

    public function getTypeById($id, $type)
    {
        switch ($type) {
            case 'revenu':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\TypeRevenu r where r.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return $resultList;
                }
                break;
            case 'debiteur':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\TypeDebiteur r where r.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return $resultList;
                }
                break;
            case 'relation':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\Relation r where r.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return $resultList;
                }
                break;
            case 'tel':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\TypeTel r where r.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return $resultList;
                }
                break;

            case 'adresse':
                // Check if $id matches 'adresse' type
                $query = $this->em->createQuery('SELECT a FROM App\Entity\TypeAdresse a where a.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return $resultList;
                }
                break;

            case 'charge':
                // Check if $id matches 'charge' type
                $query = $this->em->createQuery('SELECT c FROM App\Entity\TypeCharge c where c.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();dump($resultList);
                if ($resultList) {
                    return $resultList;
                }
                break;

            case 'email':
                // Check if $id matches 'email' type
                $query = $this->em->createQuery('SELECT e FROM App\Entity\TypeEmail e where e.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return $resultList;
                }
                break;

            case 'paiement':
                // Check if $id matches 'paiement' type
                $query = $this->em->createQuery('SELECT p FROM App\Entity\TypePaiement p where p.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return $resultList;
                }
                break;

            default:
                // If $type doesn't match any expected types, return false
                return null;
        }

        // If none of the cases matched, return false
        return null;
    }    
    public function checkType($id, $type)
    {
        switch ($type) {
            case 'revenu':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\TypeRevenu r where r.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return true;
                }
                break;
            case 'debiteur':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\TypeDebiteur r where r.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return $resultList;
                }
                break;
            case 'relation':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\Relation r where r.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return true;
                }
                break;
            case 'tel':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\TypeTel r where r.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return true;
                }
                break;

            case 'adresse':
                // Check if $id matches 'adresse' type
                $query = $this->em->createQuery('SELECT a FROM App\Entity\TypeAdresse a where a.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return true;
                }
                break;

            case 'charge':
                // Check if $id matches 'charge' type
                $query = $this->em->createQuery('SELECT c FROM App\Entity\TypeCharge c where c.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();dump($resultList);
                if ($resultList) {
                    return true;
                }
                break;

            case 'email':
                // Check if $id matches 'email' type
                $query = $this->em->createQuery('SELECT e FROM App\Entity\TypeEmail e where e.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return true;
                }
                break;

            case 'paiement':
                // Check if $id matches 'paiement' type
                $query = $this->em->createQuery('SELECT p FROM App\Entity\TypePaiement p where p.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return true;
                }
                break;

            default:
                // If $type doesn't match any expected types, return false
                return false;
        }

        // If none of the cases matched, return false
        return false;
    }    
    public function getListeType($type)
    {
        switch ($type) {
            case 'telephone':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\TypeTel r ');
                $resultList = $query->getResult();
                if ($resultList) {
                    return $resultList;
                }
                break;

            case 'adresse':
                $query = $this->em->createQuery('SELECT r FROM App\Entity\TypeAdresse r ');
                $resultList = $query->getResult();
                if ($resultList) {
                    return $resultList;
                }
                break;

            case 'email':
                $query = $this->em->createQuery('SELECT r FROM App\Entity\TypeAdresse r ');
                $resultList = $query->getResult();
                if ($resultList) {
                    return $resultList;
                }
                break;

            case 'paiement':
                $query = $this->em->createQuery('SELECT r FROM App\Entity\TypePaiement r ');
                $resultList = $query->getResult();
                if ($resultList) {
                    return $resultList;
                }
                break;

            default:
                // If $type doesn't match any expected types, return false
                return false;
        }

        // If none of the cases matched, return false
        return false;
    }    
    public function getListeStatus($type)
    {
        switch ($type) {
            case 'telephone':
                $query = $this->em->createQuery('SELECT r FROM App\Entity\StatusTelephone r ');
                $resultList = $query->getResult();
                if ($resultList) {
                    return $resultList;
                }
                break;
            case 'adresse':
                $query = $this->em->createQuery('SELECT r FROM App\Entity\StatusAdresse r ');
                $resultList = $query->getResult();
                if ($resultList) {
                    return $resultList;
                }
                break;

            
            default:
                // If $type doesn't match any expected types, return false
                return false;
        }

        // If none of the cases matched, return false
        return false;
    }    
    public function getOneStatus($type , $id)
    {
        switch ($type) {
            case 'telephone':
                $query = $this->em->createQuery('SELECT r FROM App\Entity\StatusTelephone r where r.id = '.$id.'');
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return $resultList;
                }
                break;
            case 'adresse':
                $query = $this->em->createQuery('SELECT r FROM App\Entity\StatusAdresse r where r.id = '.$id.'');
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return $resultList;
                }
                break;

            
            default:
                // If $type doesn't match any expected types, return false
                return false;
        }

        // If none of the cases matched, return false
        return false;
    }    
    public function checkElement($id, $type)
    {
        switch ($type) {
            case 'revenu':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\Revenu r where r.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return true;
                }
                break;
            case 'foncier':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\Foncier r where r.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return true;
                }
                break;
            case 'historique_emploi':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\HistoriqueEmploi r where r.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return true;
                }
                break;
            case 'emploi':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\Emploi r where r.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return true;
                }
                break;
            case 'compte_bancaire':
                // Check if $id matches 'revenu' type
                $query = $this->em->createQuery('SELECT r FROM App\Entity\CompteBancaire r where r.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return true;
                }
                break;
    
            case 'employeur':
            // Check if $id matches 'revenu' type
            $query = $this->em->createQuery('SELECT r FROM App\Entity\Employeur r where r.id = :id')
                ->setParameters([
                    'id' => $id
                ])
                ->setMaxResults(1);
            $resultList = $query->getOneOrNullResult();
            if ($resultList) {
                return true;
            }
            break;

            case 'adresse':
                // Check if $id matches 'adresse' type
                $query = $this->em->createQuery('SELECT a FROM App\Entity\Adresse a where a.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return true;
                }
                break;
            case 'telephone':
                // Check if $id matches 'adresse' type
                $query = $this->em->createQuery('SELECT a FROM App\Entity\Telephone a where a.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return true;
                }
                break;

            case 'charge':
                // Check if $id matches 'charge' type
                $query = $this->em->createQuery('SELECT c FROM App\Entity\Charge c where c.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return true;
                }
                break;

            case 'email':
                // Check if $id matches 'email' type
                $query = $this->em->createQuery('SELECT e FROM App\Entity\Email e where e.id = :id')
                    ->setParameters([
                        'id' => $id
                    ])
                    ->setMaxResults(1);
                $resultList = $query->getOneOrNullResult();
                if ($resultList) {
                    return true;
                }
                break;

            default:
                // If $type doesn't match any expected types, return false
                return false;
        }

        // If none of the cases matched, return false
        return false;
    }    
}