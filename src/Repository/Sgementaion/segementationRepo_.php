<?php

namespace App\Repository\Sgementaion;
use App\Entity\CritereParentSeg;
use App\Entity\CritereSegment;
use App\Entity\DetailCritereSegment;
use App\Entity\DetailsSeg;
use App\Entity\GroupeCritere;
use App\Entity\IntermGroupeCritere;
use App\Entity\SegCritere;
use App\Entity\Segmentation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class segementationRepo_ extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }

    public function getListeGroupe(){
        $resultList = $this->em->getRepository(GroupeCritere::class)->findAll();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    
    public function createGroupe($titre){
        $model = new GroupeCritere();
        $model->setTitreGroupe($titre);
        $model->setDateCreation(new \DateTime);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }
    public function findGroupe($id){
        $groupe = $this->em->getRepository(GroupeCritere::class)->findOneBy(["id"=>$id]);
        if($groupe){
            return $groupe;
        }else{
            return $groupe;
        }
    }
    public function findParentCritere($id){
        $groupe = $this->em->getRepository(CritereParentSeg::class)->findOneBy(["id"=>$id]);
        if($groupe){
            return $groupe;
        }else{
            return $groupe;
        }
    }
    public function deleteGroupe($id){
        try{
            $groupe = $this->em->getRepository(GroupeCritere::class)->findOneBy(["id"=>$id]);
            $interm = $this->em->getRepository(IntermGroupeCritere::class)->findBy(["id_groupe"=>$id]);
            foreach ($interm as $item) {
                $this->em->remove($item);
            }
            $this->em->remove($groupe);
            $this->em->flush();
            $statut = "OK";
            return $statut;
        }catch(\Exception $e){
            $statut = "NO";
            return $statut;
        }
    }
    public function updateGroupe($titre  ,$id){
        $sql = "UPDATE `groupe_critere` SET `titre_groupe`=:titre WHERE `id`=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('titre', $titre); 
        $stmt->bindParam('id', $id); 
        $stmt = $stmt->executeQuery();
        if($stmt){
            return true;
        }else{
            return false;
        }
    }
    public function getListeCritere(){
        $resultList = $this->em->getRepository(CritereSegment::class)->findAll();
        if($resultList){
            return $resultList;
        }else{
            return $resultList;
        }
    }
    public function getListeParentCritere(){
        $resultList = $this->em->getRepository(CritereParentSeg::class)->findAll();
        if($resultList){
            return $resultList;
        }else{
            return $resultList;
        }
    }
    public function deleteCritere($id){
        try{
            $groupe = $this->em->getRepository(CritereSegment::class)->findOneBy(["id"=>$id]);
            $interm = $this->em->getRepository(IntermGroupeCritere::class)->findBy(["id_critere"=>$id]);
            foreach ($interm as $item) {
                $this->em->remove($item);
            }
            $this->em->remove($groupe);
            $this->em->flush();
            $statut = "OK";
            return $statut;
        }catch(\Exception $e){
            $statut = "NO";
            return $statut;
        }
    }
    public function findCritere($id){
        $groupe = $this->em->getRepository(CritereSegment::class)->findOneBy(["id"=>$id]);
        if($groupe){
            return $groupe;
        }else{
            return $groupe;
        }
    }
    public function findByParentCritere($id){
        $groupe = $this->em->getRepository(CritereSegment::class)->findBy(["id_parent"=>$id]);
        if($groupe){
            return $groupe;
        }else{
            return $groupe;
        }
    }
    public function findSegment($id){
        $groupe = $this->em->getRepository(Segmentation::class)->findOneBy(["id"=>$id]);
        if($groupe){
            return $groupe;
        }else{
            return $groupe;
        }
    }

    public function updateCritere($operator,$table,$column,$type,$valeur1,$valeur2,$action,$id){
        $sql = "UPDATE `critere_segment` SET `table_name`=:table,`column_name`=:column,`valeur1`= :valeur1,`valeur2`=:valeur2,`action`=:action,`type_column`=:type_column,`operator`=:operator WHERE  `id`=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('table', $table); 
        $stmt->bindParam('column', $column); 
        $stmt->bindParam('valeur1', $valeur1); 
        $stmt->bindParam('valeur2', $valeur2); 
        $stmt->bindParam('action', $action); 
        $stmt->bindParam('type_column', $type); 
        $stmt->bindParam('operator', $operator); 
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        if($stmt){
            return true;
        }else{
            return false;
        }
    }
    public function getListeSegment(){
        $resultList = $this->em->getRepository(Segmentation::class)->findAll();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function createSegment($nom){
        $model = new Segmentation();
        $model->setNomSegment($nom);
        $model->setDateCreation(new \DateTime);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }
    public function createSegment1($nom , $type){
        $model = new Segmentation();
        $model->setNomSegment($nom);
        $model->setType($type);
        $model->setDateCreation(new \DateTime);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }
    public function createDetailSegemnt($id_seg , $detail){
        $details = new DetailsSeg();
        $details ->setIdSegment($id_seg);
        $details ->setIdDeb($detail);
        $this->em->persist($details);
        $this->em->flush();
        return $details;
    }
    public function updateSegmentation($titre  ,$id){
        $sql = "UPDATE `segmentation` SET `nom_segment`=:titre WHERE `id`=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('titre', $titre); 
        $stmt->bindParam('id', $id); 
        $stmt = $stmt->executeQuery();
        if($stmt){
            return true;
        }else{
            return false;
        }
    }
    public function deleteSegment($id){
        try{
            $groupe = $this->em->getRepository(Segmentation::class)->findOneBy(["id"=>$id]);
            $this->em->remove($groupe);
            $this->em->flush();
            $statut = "OK";
            return $statut;
        }catch(\Exception $e){
            $statut = "NO";
            return $statut;
        }
    }
    public function createCritereSegment($segment,$groupe){
        $model = new SegCritere();
        $model->setIdSegment($segment);
        $model->setIdGroupe($groupe);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }
    public function createParentCritere($titre){
        $model = new CritereParentSeg();
        $model->setTitre($titre);
        $model->setDateCreation(new \DateTime);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }
    public function createCritere($table_critere,$column_critere,$action_critere,$valeur1_critere,$valeur2,$type_critere,$operator,$parentCritere){
        $critere = new CritereSegment();
        $critere->setTableName($table_critere);
        $critere->setColumnName($column_critere);
        $critere->setAction($action_critere);
        $critere->setValeur1($valeur1_critere);
        $critere->setTypeColumn($type_critere);
        $critere->setValeur2($valeur2);
        $critere->setOperator($operator);
        $critere->setIdParent($parentCritere);
        $this->em->persist($critere);
        $this->em->flush();
        return $critere;
    }
    public function createDetailCritere($table_critere,$column_critere,$action_critere,$valeur1_critere,$valeur2,$type_critere,$critere ,$type_detail,$operator_detail){
        $detailCritere = new DetailCritereSegment();
        $detailCritere->setTableName($table_critere);
        $detailCritere->setColumnName($column_critere);
        $detailCritere->setAction($action_critere);
        $detailCritere->setValeur1($valeur1_critere);
        $detailCritere->setTypeColumn($type_critere);
        $detailCritere->setValeur2($valeur2);
        $detailCritere->setIdCritereSegment($critere);
        $detailCritere->setTypeDetail($type_detail);
        $detailCritere->setOperator($operator_detail);
        $this->em->persist($detailCritere);
        $this->em->flush();
    }
    public function createIntermCritereGroupe($critere , $groupe){
        $model = new IntermGroupeCritere();
        $model->setIdCritere($critere);
        $model->setIdGroupe($groupe);
        $this->em->persist($model);
        $this->em->flush();
        return $model;
    }
    public function getCritereByGroupe($id){
        
        $query = $this->em->createQuery('SELECT t from App\Entity\CritereParentSeg t where t.id in (select (g.id_critere) from App\Entity\IntermGroupeCritere g where g.id_groupe = :id_groupe)');
        $query->setParameter('id_groupe', $id);
        $resultList = $query->getResult();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }

    public function getDataDossier($data){
        $codeStatut="ERROR";
        $queryEntities = "App\Entity\Dossier d,App\Entity\Debiteur deb,App\Entity\Creance c";
        $queryConditions = " ";
        $param = array();
        $liste_groupe = $data["liste_groupe"];
        for ($j=0; $j < count($data["liste_groupe"]); $j++) { 
            $groupe = $liste_groupe[$j];
            $groupe = $this->findGroupe($groupe);
            $critereP = $this->getCritereByGroupe($groupe->getId());
            for ($d=0; $d < count($critereP); $d++) { 
                $criteres = $this->findByParentCritere($critereP[$d]->getId());
                for ($i=0; $i < count($criteres); $i++) { 
                    if($i==0)
                    {
                        $operateur[$i]="";
                    }
                    else
                    {
                        $operateur[$i]=$criteres[$i]->getOperator();
                    }
                    //contact_donneur_ordre
                    // //Adress table
                    if($criteres[$i]->getTableName() == "debiteur"){
                        if($criteres[$i]->getColumnName() =="cin"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.cin LIKE :cin".$i.")";
                                $param['cin'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.cin NOT LIKE :cin".$i.")";
                                $param['cin'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="civilite"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                {
                                    $queryEntities .= ",App\Entity\DebiteurDetail t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.civilite LIKE :civilite".$i.")";
                                $param['civilite'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                {
                                    $queryEntities .= ",App\Entity\DebiteurDetail t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.civilite NOT LIKE :civilite".$i.")";
                                $param['civilite'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                {
                                    $queryEntities .= ",App\Entity\DebiteurDetail t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.civilite is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                {
                                    $queryEntities .= ",App\Entity\DebiteurDetail t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.civilite is not null)";
                            }
                        }
                        
                        if($criteres[$i]->getColumnName() =="nom"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.nom LIKE :nom".$i.")";
                                $param['nom'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.nom NOT LIKE :nom".$i.")";
                                $param['nom'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.nom is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.nom is not null)";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="prenom"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.prenom LIKE :prenom".$i.")";
                                $param['prenom'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.prenom NOT LIKE :prenom".$i.")";
                                $param['prenom'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.prenom is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.prenom is not null)";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="raison_social"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.raison_social LIKE :raison_social".$i.")";
                                $param['raison_social'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.raison_social NOT LIKE :raison_social".$i.")";
                                $param['raison_social'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.raison_social is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.raison_social is not null)";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="fax"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.fax LIKE :fax".$i.")";
                                $param['fax'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.fax NOT LIKE :fax".$i.")";
                                $param['fax'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.fax is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                {
                                    $queryEntities .= ",App\Entity\DebiteurDetail t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.fax is not null)";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="date_naissance"){
                            if($criteres[$i]->getAction() == "egal"){

                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());

                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.date_naissance BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){

                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.date_naissance > :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.date_naissance < :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                            }
                            if($criteres[$i]->getAction() == "entre"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.date_naissance BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                        }
                        if($criteres[$i]->getColumnName() =="lieu_naissance"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.lieu_naissance LIKE :lieu_naissance".$i.")";
                                $param['lieu_naissance'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.lieu_naissance NOT LIKE :lieu_naissance".$i.")";
                                $param['lieu_naissance'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.lieu_naissance is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.lieu_naissance is not null)";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="email"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.email LIKE :email".$i.")";
                                $param['email'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.email NOT LIKE :email".$i.")";
                                $param['email'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                    }
                    if($criteres[$i]->getTableName() == "adresse"){
                        if($criteres[$i]->getColumnName() =="adresse_complet"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id deb.id=identity(a.id_debiteur) and a.verifier=1 and a.adresse_complet LIKE :adresse".$i.")";
                                $param['adresse'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.adresse_complet NOT LIKE :adresse".$i.")";
                                $param['adresse'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="cp"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.cp LIKE :cp".$i.")";
                                $param['cp'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.cp NOT LIKE :cp".$i.")";
                                $param['cp'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.cp is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.cp is not null)";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="pays"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.pays LIKE :pays".$i.")";
                                $param['pays'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.pays NOT LIKE :pays".$i.")";
                                $param['pays'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.pays is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.pays is not null)";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="ville"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.ville LIKE :ville".$i.")";
                                $param['ville'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.ville NOT LIKE :ville".$i.")";
                                $param['ville'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.ville is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.ville is not null)";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="status"){
                            if($criteres[$i]->getAction() == "egal"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.status = :status".$i.")";
                                $param['status'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notegal"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.status  !=  :status".$i.")";
                                $param['status'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="verifier"){
                            if($criteres[$i]->getAction() == "egal"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.verifier = :verifier".$i.")";
                                $param['verifier'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notegal"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.verifier  !=  :verifier".$i.")";
                                $param['verifier'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                    }
                    if($criteres[$i]->getTableName() == "compte_bancaire"){
                        if($criteres[$i]->getColumnName() =="banque"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\CompteBancaire cb") == false)
                                {
                                    $queryEntities .= ",App\Entity\CompteBancaire cb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and cb.banque LIKE :banque".$i.")";
                                $param['banque'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="rib"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\CompteBancaire cb") == false)
                                {
                                    $queryEntities .= ",App\Entity\CompteBancaire cb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and cb.rib LIKE :rib".$i.")";
                                $param['banque'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                    }
                    if($criteres[$i]->getTableName() == "charge"){
                        if($criteres[$i]->getColumnName() =="charge"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Charge tc") == false)
                                {
                                    $queryEntities .= ",App\Entity\Charge tc";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and identity(tc.id_type_charge) LIKE :typeCaharge".$i.")";
                                $param['typeCaharge'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                {
                                    $queryEntities .= ",App\Entity\DebiteurDetail t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.adresse_complet NOT LIKE :adresse".$i.")";
                                $param['adresse'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                    }
                    if($criteres[$i]->getTableName() == "creance"){
                        if($criteres[$i]->getColumnName() =="numero_creance"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }

                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.numero_creance LIKE :numero_creance".$i.")";
                                $param['numero_creance'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.numero_creance NOT LIKE :numero_creance".$i.")";
                                $param['numero_creance'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="total_ttc_initial"){
                            if($criteres[$i]->getAction() == "egal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_ttc_initial = :total_ttc_initial".$i.")";
                                $param['total_ttc_initial'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_ttc_initial >= :total_ttc_initial".$i.")";
                                $param['total_ttc_initial'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_ttc_initial <= :total_ttc_initial".$i.")";
                                $param['total_ttc_initial'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() =="entre"){
                                if(strpos($queryEntities,",UtilisateursBundle:creance c") == false)
                                {
                                    $queryEntities .= ",UtilisateursBundle:creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.totalTtcInitial BETWEEN ".$criteres[$i]->getValeur1()." AND ".$criteres[$i]->getValeur2()." )";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="total_ttc_restant"){
                            if($criteres[$i]->getAction() == "egal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_ttc_restant = :total_ttc_restant".$i.")";
                                $param['total_ttc_restant'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_ttc_restant >= :total_ttc_restant".$i.")";
                                $param['total_ttc_restant'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_ttc_restant <= :total_ttc_restant".$i.")";
                                $param['total_ttc_restant'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() =="entre"){
                                if(strpos($queryEntities,",UtilisateursBundle:creance c") == false)
                                {
                                    $queryEntities .= ",UtilisateursBundle:creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_ttc_restant BETWEEN ".$criteres[$i]->getValeur1()." AND ".$criteres[$i]->getValeur2()." )";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="date_echeance"){
                            if($criteres[$i]->getAction() == "egal"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());

                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.date_echeance BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){

                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.date_echeance > :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){

                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.date_echeance < :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "entre"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.date_echeance BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                        }
                        if($criteres[$i]->getColumnName() =="total_creance"){
                            if($criteres[$i]->getAction() == "egal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_creance = :total_creance".$i.")";
                                $param['total_creance'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_creance >= :total_creance".$i.")";
                                $param['total_creance'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_creance <= :total_creance".$i.")";
                                $param['total_creance'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() =="entre"){
                                if(strpos($queryEntities,",UtilisateursBundle:creance c") == false)
                                {
                                    $queryEntities .= ",UtilisateursBundle:creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_creance BETWEEN ".$criteres[$i]->getValeur1()." AND ".$criteres[$i]->getValeur2()." )";
                                $param['total_creance'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                    }
                    
                    if($criteres[$i]->getTableName() == "dossier"){
                        if($criteres[$i]->getColumnName() =="numero_dossier"){
                            if($criteres[$i]->getAction() == "like"){
                                
                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.numero_dossier LIKE :numero_dossier".$i.")";
                                $param['numero_dossier'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.numero_dossier NOT LIKE :numero_dossier".$i.")";
                                $param['numero_dossier'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="date_overture"){
                            if($criteres[$i]->getAction() == "egal"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_overture BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and  d.date_overture > :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and  d.date_overture < :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "entre"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_overture BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "null"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_overture is null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_overture is not null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                        }
                        if($criteres[$i]->getColumnName() =="date_creation"){
                            if($criteres[$i]->getAction() == "egal"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_creation BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and  d.date_creation > :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and  d.date_creation < :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "entre"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_creation BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "null"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_creation is null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_creation is not null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                        }
                        if($criteres[$i]->getColumnName() =="date_fin_prevesionnel"){
                            if($criteres[$i]->getAction() == "egal"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_fin_prevesionnel BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and  d.date_fin_prevesionnel > :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and  d.date_fin_prevesionnel < :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "entre"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_fin_prevesionnel BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "null"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_fin_prevesionnel is null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_fin_prevesionnel is not null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                        }
                        if($criteres[$i]->getColumnName() =="date_fin"){
                            if($criteres[$i]->getAction() == "egal"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_fin BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and  d.date_fin > :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and  d.date_fin < :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "entre"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_fin BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "null"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_fin is null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_fin is not null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                        }
                    }
                    if($criteres[$i]->getTableName() == "portefeuille"){
                        if($criteres[$i]->getColumnName() =="numero_ptf"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.numeroPtf LIKE :numeroPtf".$i.")";
                                $param['numeroPtf'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){

                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and d.numeroPtf NOT LIKE :numeroPtf".$i.")";
                                $param['numeroPtf'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="titre"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.titre LIKE :titre".$i.")";
                                $param['titre'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){

                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and d.titre NOT LIKE :titre".$i.")";
                                $param['titre'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="type_creance"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.type_creance LIKE :type_creance".$i.")";
                                $param['type_creance'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){

                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and d.type_creance NOT LIKE :type_creance".$i.")";
                                $param['type_creance'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="date_debut_gestion"){
                            if($criteres[$i]->getAction() == "egal"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and  ptf.date_debut_gestion BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){

                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_debut_gestion > :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){

                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_debut_gestion < :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "entre"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_debut_gestion BETWEEN :dateFinStart".$i." and :dateFinEnd".$i." )";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "null"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_debut_gestion is null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_debut_gestion is not null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                        }
                        if($criteres[$i]->getColumnName() =="date_fin_gestion"){
                            if($criteres[$i]->getAction() == "egal"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_fin_gestion BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){

                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_fin_gestion > :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){

                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_fin_gestion < :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "entre"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_fin_gestion BETWEEN :dateFinStart".$i." and :dateFinEnd".$i." )";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "null"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_fin_gestion is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_fin_gestion is not null)";                   
                            }
                        }
                        if($criteres[$i]->getColumnName() =="duree_gestion"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.duree_gestion LIKE :duree_gestion".$i.")";
                                $param['duree_gestion'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){

                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and d.duree_gestion NOT LIKE :duree_gestion".$i.")";
                                $param['duree_gestion'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="actif"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.actif = :actif".$i.")";
                                $param['actif'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and d.actif != :actif".$i.")";
                                $param['actif'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="type_mission"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.type_mission LIKE :type_mission".$i.")";
                                $param['type_mission'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and d.type_mission NOT LIKE :type_mission".$i.")";
                                $param['type_mission'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                    }
                }
            }
        }
        // dump($queryEntities);
        if($queryConditions != " "){
            $sql="SELECT distinct  d.id , d.numero_dossier , d.date_creation   FROM  ". $queryEntities . " where " . $queryConditions;
            $query = $this->em->createQuery($sql);
            $query->setParameters($param);
            $result = $query->getResult(); 
            $dossier_data = $result;
            // $this->createSegmentcreateDetailSegemnt($createSegment , $dossier_data);
            $codeStatut="OK";
            $respObjects["dossier_data"] = $dossier_data;
            $respObjects["nombre_dossier"] = count($dossier_data);
        }
        return $respObjects;
    }

    public function getDataCreance($data){
        $codeStatut="ERROR";
        $queryEntities = "App\Entity\Dossier d,App\Entity\Debiteur deb,App\Entity\Creance c";
        $queryConditions = " ";
        $param = array();
        $liste_groupe = $data["liste_groupe"];
        for ($j=0; $j < count($data["liste_groupe"]); $j++) { 
            $groupe = $liste_groupe[$j];
            $groupe = $this->findGroupe($groupe);
            $critereP = $this->getCritereByGroupe($groupe->getId());
            for ($d=0; $d < count($critereP); $d++) { 
                $criteres = $this->findByParentCritere($critereP[$d]->getId());
                for ($i=0; $i < count($criteres); $i++) { 
                    if($i==0)
                    {
                        $operateur[$i]="";
                    }
                    else
                    {
                        $operateur[$i]=$criteres[$i]->getOperator();
                    }
                    //contact_donneur_ordre
                    // //Adress table
                    if($criteres[$i]->getTableName() == "debiteur"){
                        if($criteres[$i]->getColumnName() =="cin"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.cin LIKE :cin".$i.")";
                                $param['cin'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.cin NOT LIKE :cin".$i.")";
                                $param['cin'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="civilite"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                {
                                    $queryEntities .= ",App\Entity\DebiteurDetail t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.civilite LIKE :civilite".$i.")";
                                $param['civilite'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                {
                                    $queryEntities .= ",App\Entity\DebiteurDetail t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.civilite NOT LIKE :civilite".$i.")";
                                $param['civilite'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                {
                                    $queryEntities .= ",App\Entity\DebiteurDetail t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.civilite is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                {
                                    $queryEntities .= ",App\Entity\DebiteurDetail t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.civilite is not null)";
                            }
                        }
                        
                        if($criteres[$i]->getColumnName() =="nom"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.nom LIKE :nom".$i.")";
                                $param['nom'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.nom NOT LIKE :nom".$i.")";
                                $param['nom'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.nom is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.nom is not null)";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="prenom"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.prenom LIKE :prenom".$i.")";
                                $param['prenom'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.prenom NOT LIKE :prenom".$i.")";
                                $param['prenom'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.prenom is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.prenom is not null)";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="raison_social"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.raison_social LIKE :raison_social".$i.")";
                                $param['raison_social'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.raison_social NOT LIKE :raison_social".$i.")";
                                $param['raison_social'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.raison_social is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.raison_social is not null)";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="fax"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.fax LIKE :fax".$i.")";
                                $param['fax'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.fax NOT LIKE :fax".$i.")";
                                $param['fax'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.fax is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                {
                                    $queryEntities .= ",App\Entity\DebiteurDetail t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.fax is not null)";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="date_naissance"){
                            if($criteres[$i]->getAction() == "egal"){

                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());

                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.date_naissance BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){

                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.date_naissance > :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.date_naissance < :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                            }
                            if($criteres[$i]->getAction() == "entre"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.date_naissance BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                        }
                        if($criteres[$i]->getColumnName() =="lieu_naissance"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.lieu_naissance LIKE :lieu_naissance".$i.")";
                                $param['lieu_naissance'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.lieu_naissance NOT LIKE :lieu_naissance".$i.")";
                                $param['lieu_naissance'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.lieu_naissance is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.lieu_naissance is not null)";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="email"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.email LIKE :email".$i.")";
                                $param['email'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.email NOT LIKE :email".$i.")";
                                $param['email'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                    }
                    if($criteres[$i]->getTableName() == "adresse"){
                        if($criteres[$i]->getColumnName() =="adresse_complet"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id deb.id=identity(a.id_debiteur) and a.verifier=1 and a.adresse_complet LIKE :adresse".$i.")";
                                $param['adresse'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.adresse_complet NOT LIKE :adresse".$i.")";
                                $param['adresse'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="cp"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.cp LIKE :cp".$i.")";
                                $param['cp'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.cp NOT LIKE :cp".$i.")";
                                $param['cp'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.cp is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.cp is not null)";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="pays"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.pays LIKE :pays".$i.")";
                                $param['pays'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.pays NOT LIKE :pays".$i.")";
                                $param['pays'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.pays is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.pays is not null)";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="ville"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.ville LIKE :ville".$i.")";
                                $param['ville'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.ville NOT LIKE :ville".$i.")";
                                $param['ville'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "null"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.ville is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.ville is not null)";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="status"){
                            if($criteres[$i]->getAction() == "egal"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.status = :status".$i.")";
                                $param['status'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notegal"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.status  !=  :status".$i.")";
                                $param['status'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="verifier"){
                            if($criteres[$i]->getAction() == "egal"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.verifier = :verifier".$i.")";
                                $param['verifier'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notegal"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.verifier  !=  :verifier".$i.")";
                                $param['verifier'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                    }

                    if($criteres[$i]->getTableName() == "compte_bancaire"){
                        if($criteres[$i]->getColumnName() =="banque"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\CompteBancaire cb") == false)
                                {
                                    $queryEntities .= ",App\Entity\CompteBancaire cb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and cb.banque LIKE :banque".$i.")";
                                $param['banque'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="rib"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\CompteBancaire cb") == false)
                                {
                                    $queryEntities .= ",App\Entity\CompteBancaire cb";
                                }
                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and cb.rib LIKE :rib".$i.")";
                                $param['banque'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                    }
                    
                    if($criteres[$i]->getTableName() == "charge"){
                        if($criteres[$i]->getColumnName() =="charge"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\TypeDebiteur t") == false)
                                {
                                    $queryEntities .= ",App\Entity\TypeDebiteur t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Charge tc") == false)
                                {
                                    $queryEntities .= ",App\Entity\Charge tc";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and identity(tc.id_type_charge) LIKE :typeCaharge".$i.")";
                                $param['typeCaharge'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                {
                                    $queryEntities .= ",App\Entity\DebiteurDetail t";
                                }
                                if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                {
                                    $queryEntities .= ",App\Entity\Debiteur deb";
                                }
                                if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                {
                                    $queryEntities .= ",App\Entity\Adresse a";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.adresse_complet NOT LIKE :adresse".$i.")";
                                $param['adresse'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                    }
                    if($criteres[$i]->getTableName() == "creance"){
                        if($criteres[$i]->getColumnName() =="numero_creance"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }

                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.numero_creance LIKE :numero_creance".$i.")";
                                $param['numero_creance'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.numero_creance NOT LIKE :numero_creance".$i.")";
                                $param['numero_creance'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="total_ttc_initial"){
                            if($criteres[$i]->getAction() == "egal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_ttc_initial = :total_ttc_initial".$i.")";
                                $param['total_ttc_initial'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_ttc_initial >= :total_ttc_initial".$i.")";
                                $param['total_ttc_initial'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_ttc_initial <= :total_ttc_initial".$i.")";
                                $param['total_ttc_initial'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() =="entre"){
                                if(strpos($queryEntities,",UtilisateursBundle:creance c") == false)
                                {
                                    $queryEntities .= ",UtilisateursBundle:creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.totalTtcInitial BETWEEN ".$criteres[$i]->getValeur1()." AND ".$criteres[$i]->getValeur2()." )";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="total_ttc_restant"){
                            if($criteres[$i]->getAction() == "egal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_ttc_restant = :total_ttc_restant".$i.")";
                                $param['total_ttc_restant'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_ttc_restant >= :total_ttc_restant".$i.")";
                                $param['total_ttc_restant'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_ttc_restant <= :total_ttc_restant".$i.")";
                                $param['total_ttc_restant'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() =="entre"){
                                if(strpos($queryEntities,",UtilisateursBundle:creance c") == false)
                                {
                                    $queryEntities .= ",UtilisateursBundle:creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_ttc_restant BETWEEN ".$criteres[$i]->getValeur1()." AND ".$criteres[$i]->getValeur2()." )";
                            }
                        }
                        if($criteres[$i]->getColumnName() =="date_echeance"){
                            if($criteres[$i]->getAction() == "egal"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());

                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.date_echeance BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){

                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.date_echeance > :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){

                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.date_echeance < :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "entre"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.date_echeance BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                        }
                        if($criteres[$i]->getColumnName() =="total_creance"){
                            if($criteres[$i]->getAction() == "egal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_creance = :total_creance".$i.")";
                                $param['total_creance'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_creance >= :total_creance".$i.")";
                                $param['total_creance'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){
                                if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                {
                                    $queryEntities .= ",App\Entity\Creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_creance <= :total_creance".$i.")";
                                $param['total_creance'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() =="entre"){
                                if(strpos($queryEntities,",UtilisateursBundle:creance c") == false)
                                {
                                    $queryEntities .= ",UtilisateursBundle:creance c";
                                }
                                $queryConditions .= " ".$operateur[$i]."(c.id=identity(t.id_creance) and c.total_creance BETWEEN ".$criteres[$i]->getValeur1()." AND ".$criteres[$i]->getValeur2()." )";
                                $param['total_creance'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                    }
                    
                    if($criteres[$i]->getTableName() == "dossier"){
                        if($criteres[$i]->getColumnName() =="numero_dossier"){
                            if($criteres[$i]->getAction() == "like"){
                                
                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.numero_dossier LIKE :numero_dossier".$i.")";
                                $param['numero_dossier'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.numero_dossier NOT LIKE :numero_dossier".$i.")";
                                $param['numero_dossier'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="date_overture"){
                            if($criteres[$i]->getAction() == "egal"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_overture BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and  d.date_overture > :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and  d.date_overture < :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "entre"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_overture BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "null"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_overture is null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_overture is not null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                        }
                        if($criteres[$i]->getColumnName() =="date_creation"){
                            if($criteres[$i]->getAction() == "egal"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_creation BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and  d.date_creation > :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and  d.date_creation < :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "entre"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_creation BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "null"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_creation is null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_creation is not null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                        }
                        if($criteres[$i]->getColumnName() =="date_fin_prevesionnel"){
                            if($criteres[$i]->getAction() == "egal"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_fin_prevesionnel BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and  d.date_fin_prevesionnel > :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and  d.date_fin_prevesionnel < :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "entre"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_fin_prevesionnel BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "null"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_fin_prevesionnel is null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_fin_prevesionnel is not null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                        }
                        if($criteres[$i]->getColumnName() =="date_fin"){
                            if($criteres[$i]->getAction() == "egal"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_fin BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and  d.date_fin > :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and  d.date_fin < :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "entre"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_fin BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "null"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_fin is null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.date_fin is not null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                        }
                    }
                    if($criteres[$i]->getTableName() == "portefeuille"){
                        if($criteres[$i]->getColumnName() =="numero_ptf"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.numeroPtf LIKE :numeroPtf".$i.")";
                                $param['numeroPtf'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){

                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and d.numeroPtf NOT LIKE :numeroPtf".$i.")";
                                $param['numeroPtf'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="titre"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.titre LIKE :titre".$i.")";
                                $param['titre'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){

                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and d.titre NOT LIKE :titre".$i.")";
                                $param['titre'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="type_creance"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.type_creance LIKE :type_creance".$i.")";
                                $param['type_creance'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){

                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and d.type_creance NOT LIKE :type_creance".$i.")";
                                $param['type_creance'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="date_debut_gestion"){
                            if($criteres[$i]->getAction() == "egal"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and  ptf.date_debut_gestion BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){

                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_debut_gestion > :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){

                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_debut_gestion < :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "entre"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_debut_gestion BETWEEN :dateFinStart".$i." and :dateFinEnd".$i." )";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "null"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_debut_gestion is null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_debut_gestion is not null)";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                        }
                        if($criteres[$i]->getColumnName() =="date_fin_gestion"){
                            if($criteres[$i]->getAction() == "egal"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_fin_gestion BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "supOuEgal"){

                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_fin_gestion > :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "infOuEgal"){

                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_fin_gestion < :dateFinStart".$i.")";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "entre"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_fin_gestion BETWEEN :dateFinStart".$i." and :dateFinEnd".$i." )";
                                $param['dateFinStart'.$i] = $start;
                                $param['dateFinEnd'.$i] = $end;
                            }
                            if($criteres[$i]->getAction() == "null"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_fin_gestion is null)";
                            }
                            if($criteres[$i]->getAction() == "notnull"){
                                $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.date_fin_gestion is not null)";                   
                            }
                        }
                        if($criteres[$i]->getColumnName() =="duree_gestion"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.duree_gestion LIKE :duree_gestion".$i.")";
                                $param['duree_gestion'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){

                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and d.duree_gestion NOT LIKE :duree_gestion".$i.")";
                                $param['duree_gestion'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="actif"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.actif = :actif".$i.")";
                                $param['actif'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and d.actif != :actif".$i.")";
                                $param['actif'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                        if($criteres[$i]->getColumnName() =="type_mission"){
                            if($criteres[$i]->getAction() == "like"){
                                if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                {
                                    $queryEntities .= ",App\Entity\Portefeuille ptf";
                                }
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and ptf.type_mission LIKE :type_mission".$i.")";
                                $param['type_mission'.$i] = $criteres[$i]->getValeur1();
                            }
                            if($criteres[$i]->getAction() == "notlike"){
                                $queryConditions .= " ".$operateur[$i]."(  ptf.id=identity(c.id_ptf) and d.type_mission NOT LIKE :type_mission".$i.")";
                                $param['type_mission'.$i] = $criteres[$i]->getValeur1();
                            }
                        }
                    }
                }
            }
        }
        // dump($queryEntities);
        if($queryConditions != " "){
            $sql="SELECT distinct  c.id , c.numero_creance , c.total_ttc_initial , c.total_ttc_restant , c.total_creance  FROM  ". $queryEntities . " where " . $queryConditions;
            $query = $this->em->createQuery($sql);
            $query->setParameters($param);
            $result = $query->getResult(); 
            $creance_data = $result;
            // $this->createSegmentcreateDetailSegemnt($createSegment , $dossier_data);
            $codeStatut="OK";
            $respObjects["creance_data"] = $creance_data;
            $respObjects["nombre_creance"] = count($creance_data);
        }
        return $respObjects;
    }
}