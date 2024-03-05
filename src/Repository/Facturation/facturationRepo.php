<?php

namespace App\Repository\Facturation;
use App\Entity\DetailFacturation;
use App\Entity\DonneurOrdre;
use App\Entity\Facture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class facturationRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    
    public function getListeProducts(){
        $sql="SELECT * FROM `produit`  ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAll();
        return $result;
    }
    public function getOneProduct($id){
        $sql="SELECT * FROM `produit` where id=".$id."  ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAssociative();
        return $result;
    }
    

    public function getListeModels(){
        $resultList = $this->em->getRepository(Facture::class)->findAll();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getListeDonneurOrdre(){
        $resultList = $this->em->getRepository(DonneurOrdre::class)->findAll();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getOneDonneurOrdre($id){
        $resultList = $this->em->getRepository(DonneurOrdre::class)->findOneBy(["id"=>$id]);
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getNumFact(){
        $yearFact = 2022;
        $query = $this->em->createQuery('SELECT a from App\Entity\Facture a WHERE a.yearFact =:yearFact ORDER BY a.id ASC ')->setParameter('yearFact', $yearFact)->setMaxResults(1);
        $numero = "1/".date_format(new \DateTime(),"y");
        
        $n = 1;
        $res = $query->getResult();
        $data=array();
        if($res){
            $n = $res[0]->getNumeroFact() +  1;
            $numero = $n."/".date_format(new \DateTime(),"y");
            $data = array("num"=>$n , "numero"=>$numero);
            return $numero ;
        }else{
            $data = array(["num"=>$n , "numero"=>$numero]);
            return $data;
        }
    }
    public function createFacture($donneur ,$numeroFact , $yearFact , $date_echeance,$type,$total_creance ,$total_ttc_initial ,$total_ttc_restant){
        $fact = new Facture();
        $fact->setIdDonneurOrdreId($donneur);
        $fact->setNumeroFact($numeroFact);
        $fact->setYearFact($yearFact);
        $fact->setDateCreation(new \DateTime("now"));
        $fact->setDateEcheance(new \DateTime($date_echeance));
        $fact->setTotalCreance($total_creance);
        $fact->setTotalTtcInitialCreance($total_ttc_initial);
        $fact->setTotalTtcRestantCreance($total_ttc_restant);
        $fact->setType($type);
        $this->em->persist($fact);
        $this->em->flush();
        return $fact;
    }
    public function createFacture2($donneur ,$numeroFact , $yearFact , $totalTTC,$id_type_paiemnt){
        $sql="INSERT INTO `facture`(`id_donneur_ordre_id_id`, `numero_fact`, `year_fact`, `date_creation`, `total_ttc`, `id_type_paiement_id` , `id_status_id`) VALUES
         (:donneur,:numeroFact,:yearFact,now(),:totalTTC,:id_type_paiemnt,1)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('donneur', $donneur);
        $stmt->bindParam('numeroFact', $numeroFact);
        $stmt->bindParam('yearFact', $yearFact);
        $stmt->bindParam('totalTTC', $totalTTC);
        $stmt->bindParam('id_type_paiemnt', $id_type_paiemnt);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();
        return $resulat;
    }
    public function createDetailFacture($id_facture , $id_regle){
        $fact = new DetailFacturation();
        $fact->setIdFacture($id_facture);
        $fact->setIdRegle($id_regle);
        $this->em->persist($fact);
        $this->em->flush();
        return $fact;
    }
}