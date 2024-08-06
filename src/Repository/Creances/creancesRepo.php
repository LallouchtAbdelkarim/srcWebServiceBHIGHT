<?php

namespace App\Repository\Creances;

use App\Entity\ActiviteAssigned;
use App\Entity\Adresse;
use App\Entity\Bookmarks;
use App\Entity\Creance;
use App\Entity\CreanceActivite;
use App\Entity\ModelCourier;
use App\Entity\ModelSMS;
use App\Entity\Paiement;
use App\Entity\ParamActivite;
use App\Entity\Portefeuille;
use App\Entity\Promise;
use App\Entity\ReglePortefeuille;
use App\Entity\Task;
use App\Entity\TaskAssigned;
use App\Entity\TypeDebiteur;
use App\Entity\TypePaiement;
use App\Entity\Utilisateurs;
use App\Repository\Encaissement\paiementRepo;
use App\Service\typeService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
class creancesRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;
    public $TypeService;

    public function __construct(Connection $conn , EntityManagerInterface $em , typeService $TypeService )
    {
        $this->conn = $conn;
        $this->em = $em;
        $this->TypeService = $TypeService;
    }
    public function getListesCreancesByFiltrages($data){
        $numero_creance = $data["numero_creance"];
        $date_echeance = $data["date_echeance"];
        $agent = $data["agent"];
        $ptf = $data["ptf"];

        $cin = $data["cin"];
        $raison_social = $data["raison_social"];
        $date_naissance = $data["date_naissance"];

        $tel = $data["tel"];
        $addr = $data["addr"];

        $query = 'SELECT DISTINCT c.*
        FROM creance c
        INNER JOIN type_debiteur t ON c.id = t.id_creance_id
        INNER JOIN debiteur deb ON t.id_debiteur_id = deb.id';

        if ($tel != "") {
            $query .= ' INNER JOIN telephone tel ON deb.id = tel.id_debiteur_id';
        }
        if ($addr != "") {
            $query .= ' INNER JOIN adresse ad ON deb.id = ad.id_debiteur_id';
        }

        if ($ptf != "") {
            $query .= ' AND c.id_ptf_id = "'.$ptf.'"';
        }

         if ($numero_creance != "") {
            $query .= ' WHERE c.numero_creance = "'.$numero_creance.'" ';
        }
        if ($agent != "") {
            $query .= ' AND c.id_users_id = "'.$agent.'"';
        }
        if ($cin != "") {
            $query .= ' AND deb.cin like "'.$cin.'" ';
        }
        if($raison_social != ""){
            $query .= ' AND deb.raison_social like "'.$raison_social.'" '; 
        }
        if ($date_echeance != "") {
            $dateDebut = $date_echeance . " 00:00:00";
            $dateFin = $date_echeance . " 23:59:59";
            $query .= ' AND c.date_echeance BETWEEN "'.$dateDebut.'" AND "'.$dateFin.'"';
        }
        if($date_naissance != ""){
            $dateDebut = $date_naissance. " 00:00:00";
            $dateFin = $date_naissance. " 23:59:59";
            $query .= ' AND deb.date_naissance BETWEEN "'.$dateDebut.'" AND "'.$dateFin.'"';
        }
        $stmt = $this->conn->prepare($query);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getDonneurByPtf($id_ptf){
        $sql="SELECT * FROM `donneur_ordre` where id in (select p.id_donneur_ordre_id from portefeuille p where p.id = ".$id_ptf.");";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAssociative();
        return $resulat;
    }
    public function getDebiteurByCreance($id_creance){
        $sql="SELECT * FROM `debiteur` where id in (select p.id_debiteur_id from type_debiteur p where p.id_creance_id = ".$id_creance."  and (p.id_type_id = 6 or 1=1));";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAssociative();

        return $resulat;
    }

    public function getTypeDebiteur($id_creance , $id_debiteur){
        $sql="SELECT * FROM `details_type_deb` where id in (select p.id_type_id from type_debiteur p where p.id_creance_id = ".$id_creance." and id_debiteur_id = ".$id_debiteur.")";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAssociative();
        return $resulat;
    }
    public function getOneCreance($id_creance){
        $sql="SELECT * FROM `creance` where id = ".$id_creance."";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAssociative();
        if($resulat){
            $sql="SELECT * FROM `details_type_creance` where id = ".$resulat['id_type_creance_id']."";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $resulat['type_creance'] = $type;
        }

        return $resulat;
    }

    public function getNbrDeb($id_creance){
        $sql="SELECT count(distinct(id_debiteur_id)) as nbr_deb FROM `type_debiteur` where id_creance_id = ".$id_creance.";";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $nbr_deb = $stmt->fetchAssociative();

        $sql="select count(p.id) as nbr_relation from personne p where p.id in (select r.id_personne_id from relation_debiteur r where r.id_debiteur_id in (SELECT t.id_debiteur_id FROM `type_debiteur` t where t.id_creance_id = ".$id_creance."));";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $nbr_relation = $stmt->fetchAssociative();

        $resulat[] = $nbr_deb;
        $resulat[] = $nbr_relation;
        return $resulat;
    }

    public function getQueueCreance($id_creance){
        $sql="SELECT * FROM `queue` where id in (select qc.id_queue from debt_force_seg.queue_creance qc where qc.id_creance = ".$id_creance.")  
        ORDER BY `queue`.`id` DESC;";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $nbr_relation = $stmt->fetchAssociative();

        return $nbr_relation;
    }
    public function getAccords($id){
        $sql="SELECT distinct a.* FROM accord a INNER JOIN creance_accord ca ON a.id = ca.id_accord_id
        INNER JOIN creance c ON ca.id_creance_id = :id;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAllAssociative();

        for ($i=0; $i < count($resulat); $i++) { 
            $sql="select * from type_paiement ca where ca.id = ".$resulat[$i]['id_type_paiement_id']." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $act = $stmt->fetchAssociative();
            $resulat[$i]["type_paiement"] = $act;

            $sql="select * from status_accord ca where ca.id = ".$resulat[$i]['id_status_id']." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $act = $stmt->fetchAssociative();
            $resulat[$i]["status_accord"] = $act;
        }
        return $resulat;
    }

    public function getActiviteCreance($id_creance){
        $sql="select * from creance_activite ca where ca.id_creance_id = ".$id_creance." ORDER BY `id` desc";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $result = $stmt->fetchAll();

        for ($i=0; $i < count($result); $i++) { 
            $sql="select * from param_activite ca where ca.id = ".$result[$i]['id_param_activite_id']." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $act = $stmt->fetchAssociative();
            $result[$i]["qualification"] = $act;

            $sql="select * from param_activite ca where ca.id = ".$act['type_activite']." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $result[$i]["type"] = $type;

            $sql="select * from type_parametrage ca where ca.id = ".$act['id_branche_id']." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $result[$i]["famille"] = $type;
        }
        return $result;
    }
    
    public function getPaiement($id){
        $sql="SELECT * FROM `paiement` where id = ".$id."";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        for ($i=0; $i < count($resulat); $i++) { 
            $sql="SELECT * FROM `type_paiement` where id = ".$resulat[$i]['id_type_paiement_id']."";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $t = $stmt->fetchAssociative();
            $resulat[$i]['typePaiement'] = $t;
        }
        return $resulat;
    }
    
    
    public function getStatusPaiement(){
        $sql="SELECT * FROM `status_paiement`";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getStatusAccord(){
        $sql="SELECT * FROM `status_accord`";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function createAccord($data){
        $sql = "INSERT INTO `accord` (";
        $values = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $values[] = "`$key`";
            
        }
        $sql .= implode(', ', $values) . ')';
        
        $sql .= ' VALUES (';  

        $params = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            if($key != "date_creation"){
                $params[] = '"'.$value.'"';
            }else{
                $params[] = 'now()';
            }
        }
        $sql .= implode(', ', $params) . ')';
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        
        if($stmt){
            $sql="SELECT max(id) FROM `accord`";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $resulat = $stmt->fetchOne();
            return $resulat;
        }else{
            return null;
        }
    }
    public function createDetailsAccord($data){
        $sql = "INSERT INTO `details_accord` (";
        $values = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $values[] = "`$key`";
        }
        $sql .= implode(', ', $values) . ')';
        
        $sql .= ' VALUES (';  

        $params = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $params[] = '"'.$value.'"';
        }
        $sql .= implode(', ', $params) . ')';
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
    }
    public function getTypeDonneurOrdre(){
        $sql="SELECT * FROM `type_donneur`";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getTypeCreance(){
        $sql="SELECT * FROM `type_creance`";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getTypeCreanceByTypeDn($id){
        $sql="SELECT * FROM `type_creance` where id_type_donneur_id = ".$id."";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    public function getTypeDetailsCreance($id){
        $sql="SELECT * FROM `details_type_creance` where id_type_creance_id = ".$id."";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }

    public function getTypeDetailsCreanceMultiple($data){
        $sql="SELECT * FROM `details_type_creance` ";
        for ($i=0; $i <count($data) ; $i++) { 
            if($i == 0){
                $sql .= "WHERE id_type_creance_id =".$data[$i]." ";
            }else{
                $sql .= "OR id_type_creance_id =".$data[$i]." ";
            }
        }
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }

    public function createActivity($id_creance , $id_param , $id_dossier){
        $sql="INSERT INTO `creance_activite`( `id_creance_id`, `id_param_activite_id`) VALUES (:id_creance_id,:id_param_activite_id);";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id_creance_id', $id_creance);
        $stmt->bindParam('id_param_activite_id', $id_param);
        $stmt = $stmt->executeQuery();

        $sql="INSERT INTO `dossier_activite`( `id_dossier_id`, `id_activite_id`) VALUES (:id_dossier,:id_activite_id);";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id_dossier', $id_dossier);
        $stmt->bindParam('id_activite_id', $id_param);
        $stmt = $stmt->executeQuery();
    }

    public function addPaiement($paiementRepo,$id_creance , $valueDate,$montant,  $idTypePaiement,$id_user , $commentaire){
        try {
            $creance = $this->em->getRepository(Creance::class)->findOneBy(["id" => $id_creance]);
            $nbrAccordsSys=0;
            $montantAccordsSys=0;
            $datePaiement= date_format(new \DateTime($valueDate), 'Y-m-d H:i:s');
            $accord = $paiementRepo->getDetailsAccord($id_creance);
            $paiement=$paiementRepo->getPaiement($id_creance,$montant);
            $typePaiement = $this->em->getRepository(TypePaiement::class)->findOneBy(["id" => $idTypePaiement]);
            $nbrConfirme=0;
            $numCreance = $creance->getNumeroCreance();
            $montantConfirme=0;
            if(!$accord and !$paiement){
                //TODO:Validé
                //Restant dialo
                
                $solde = $paiementRepo->getRestantCreance($id_creance);
                if($solde > $montant)
                {
                    $sql="insert into accord (`id_type_paiement_id`, `date_premier_paiement`, `montant`, `frequence`, `nbr_echeanciers`, `etat`, `date_creation`, `date_fin_paiement`,`id_users_id`,`id_status_id`) values(".$typePaiement->getId().",'".$datePaiement."',".$montant.",1,1,1,sysdate(),'".$datePaiement."','".$id_user."',1)";
                    $stmt = $this->conn->prepare($sql)->executeQuery();
                    $sql = "SELECT MAX(id) FROM accord";
                    $maxAccord = $this->conn->executeQuery($sql)->fetchOne();
                    $sql="insert into creance_accord (`id_creance_id`, `id_accord_id`) values(".$id_creance.",".$maxAccord.")";
                    $stmt = $this->conn->prepare($sql)->executeQuery();
                    $sql = "SELECT MAX(id) FROM creance_accord";
                    $maxCreanceAccord = $this->conn->executeQuery($sql)->fetchOne();
                    $sql="insert into details_accord (`id_accord_id`, `montant`, `id_status_id`, `id_type_paiement_id`, `montant_paiement`, `montant_restant`, `date_paiement`,`id_user_id`) values(".$maxAccord.",".$montant.",1,".$typePaiement->getId().",".$montant.",0,'".$datePaiement."','".$id_user."')";
                    $stmt = $this->conn->prepare($sql)->executeQuery();
                    $nbrAccordsSys++;
                    $montantAccordsSys+=$montant;
                }
                else
                {
                    $sql="insert into accord (`id_type_paiement_id`, `date_premier_paiement`, `montant`, `frequence`, `nbr_echeanciers`, `etat`, `date_creation`, `date_fin_paiement`,`id_users_id`,`id_status_id`) values(".$typePaiement->getId().",'".$datePaiement."',".$solde.",1,1,1,sysdate(),'".$datePaiement."','".$id_user."',1)";
                    $stmt = $this->conn->prepare($sql)->executeQuery();
                    $sql = "SELECT MAX(id) FROM accord";
                    $maxAccord = $this->conn->executeQuery($sql)->fetchOne();
                    $sql="insert into creance_accord (`id_creance_id`, `id_accord_id`) values('".$id_creance."','".$maxAccord."')";
                    $stmt = $this->conn->prepare($sql)->executeQuery();
                    $sql = "SELECT MAX(id) FROM creance_accord";
                    $maxCreanceAccord = $this->conn->executeQuery($sql)->fetchOne();
                    $sql="insert into details_accord (`id_accord_id`, `montant`, `id_status_id`, `id_type_paiement_id`, `montant_paiement`, `montant_restant`, `date_paiement`,`id_user_id`) values(".$maxAccord.",".$solde.",1,".$typePaiement->getId().",".$montant.",0,'".$datePaiement."','".$id_user."')";
                    $stmt = $this->conn->prepare($sql)->executeQuery();
                    $nbrAccordsSys++;
                    $montantAccordsSys+=$solde;
                }
                $sql = "SELECT MAX(id) FROM details_accord";
                $maxDetailAccord = $this->conn->executeQuery($sql)->fetchOne();
                $date=new \DateTime();
                $dmy = $date->format('dmYHis');
                $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $id_creance, "id_type" => 3]);
                if (!$typeDeb) {
                    $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $id_creance]);
                }
                $idDeb =$typeDeb->getIdDebiteur()->getId();
                $ref = "PA" . $dmy . ($typeDeb ? $typeDeb->getId() : "");//Etat de paiement pour l'etat l"annulaion si = 0 non annulé //si=1 est annulé
                $sql="insert into paiement (`id_creance_id`, `id_type_paiement_id`, `ref`, `montant`, `date_creation`, `date_paiement`, `etat`, `id_users_id`, `id_debiteur_id`, `id_ptf_id`, `id_details_accord_id`, `commentaire`, `confirmed`)
                values(".$id_creance.",".$typePaiement->getId().",'".$ref."',".$montant.",sysdate(),'".$datePaiement."',0,".$id_user.",".$idDeb.",".$creance->getIdPtf()->getId().",".$maxDetailAccord.",'".$commentaire."',1)";
                $stmt = $this->conn->prepare($sql)->executeQuery();

                
                $sql = "SELECT MAX(id) FROM paiement";
                $maxPaiement = $this->conn->executeQuery($sql)->fetchOne();
                $sql="insert into paiement_accord (`id_paiement_id`,`id_details_accord_id`) values(".$maxPaiement.",".$maxDetailAccord.")";
                $stmt = $this->conn->prepare($sql)->executeQuery();

                /*$sql="SELECT * FROM `creance_paiement_dbi` c where c.id_creance = :id_creance and c.id_action = :id_action ORDER BY c.id DESC";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(":id_creance",$id_creance);
                $stmt->bindValue(":id_action",$id_action);
                $stmt = $stmt->executeQuery();
                $checkIfAlreadyExist = $stmt->fetchAssociative();
                if(!$checkIfAlreadyExist){
                    $newTotalRestant = $creance->getTotalRestant() - $montant;
                    $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                    (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                    VALUES (" . $id_creance . ", " . $creance->getTotalRestant() . ", " . $newTotalRestant . ", " . $montant . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                }else{
                    $newTotalRestant = $checkIfAlreadyExist["new_total_restant"] - $montant;
                    $TotalRestant = $checkIfAlreadyExist["new_total_restant"] ;
                    $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                    (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                    VALUES (" . $id_creance . ", " . $TotalRestant . ", " . $newTotalRestant . ", " . $montant . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                }*/
                $total=($creance->getTotalRestant()-$montant);
                $creance->setTotalRestant($total);
                $this->em->flush();
                $stmt = $this->conn->prepare($sql)->executeQuery();
            }
            elseif($paiement)
            {
                //TODO:Non Validé
                $sql="update paiement set `confirmed`=1  where id=".$paiement->getId();
                $stmt = $this->conn->prepare($sql)->executeQuery();
                $nbrConfirme++;
                $montantConfirme+=$paiement[0]->getMontant();
            }
            else
            {
                $rest = $montant;
                while($rest > 0)
                {
                    $autreDetails = $paiementRepo->getDetailsAccord3($numCreance);
                    
                    if($autreDetails)
                    {
                        foreach ($autreDetails as $autreDetail)
                        {
                            if($rest>=$autreDetail["montant_restant"])
                            {//TODO: Validé
                                $sql="update details_accord set `id_status_id`=1,`id_type_paiement_id`=".$typePaiement->getId().", `montant_paiement`=".$autreDetail["montant_restant"].", `montant_restant`=0, `date_paiement`=sysdate() where id=".$autreDetail["id"];
                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                $nbrAccordsAgent++;
                                $montantAccordsAgent+=$autreDetail["montant_restant"];

                                $date=new \DateTime();
                                $dmy = $date->format('dmYHis');
                                $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $id_creance, "id_type" => 3]);
                                if (!$typeDeb) {
                                    $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $id_creance]);
                                }
                                $idDeb =$typeDeb->getIdDebiteur()->getId();
                                $ref = "PA" . $dmy . ($typeDeb ? $typeDeb->getId() : "");

                                $sql="insert into paiement (`id_creance_id`, `id_type_paiement_id`, `ref`, `montant`, `date_creation`, `date_paiement`, `etat`, `id_users_id`, `id_debiteur_id`, `id_ptf_id`, `id_details_accord_id`, `commentaire`,  `confirmed`) values(".$id_creance.",".$typePaiement->getId().",'".$ref."',".$autreDetail["montant_restant"].",sysdate(),'".$datePaiement."',0,".$id_user.",".$idDeb.",".$creance->getIdPtf()->getId().",".$autreDetail["id"].",'".$commentaire."',1)";
                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                $sql = "SELECT MAX(id) FROM paiement";
                                $maxPaiement = $this->conn->executeQuery($sql)->fetchOne();
                                $sql="insert into paiement_accord (`id_paiement_id`,`id_details_accord_id`) values(".$maxPaiement.",".$autreDetail["id"].")";
                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                
                                $total=($creance->getTotalRestant()-$autreDetail->getMontantRestant());
                                $creance->setTotalRestant($total);
                                $this->em->flush();

                                $rest = ($rest - $autreDetail['montant_restant']);
                                if($rest <= 0)
                                {
                                    break;
                                }
                            }
                            else
                            {
                                
                                $sql="update details_accord set `id_status_id`=2,`id_type_paiement_id`=".$typePaiement->getId().", `montant_paiement`=".$rest.", `montant_restant`=".($autreDetail["montant_restant"]-$rest).", `date_paiement`=sysdate() where id=".$autreDetail["id"];
                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                $nbrAccordsAgent++;
                                $montantAccordsAgent+=$rest;

                                $date=new \DateTime();
                                $dmy = $date->format('dmYHis');
                                $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $id_creance, "id_type" => 3]);
                                if (!$typeDeb) {
                                    $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $id_creance]);
                                }
                                $idDeb =$typeDeb->getIdDebiteur()->getId();
                                $ref = "PA" . $dmy . ($typeDeb ? $typeDeb->getId() : "");

                                $sql="insert into paiement (`id_creance_id`, `id_type_paiement_id`, `ref`, `montant`, `date_creation`, `date_paiement`, `etat`, `id_users_id`, `id_debiteur_id`, `id_ptf_id`, `id_details_accord_id`, `commentaire`, `confirmed`) values
                                (".$id_creance.",".$typePaiement->getId().",'".$ref."',".$autreDetail["montant_restant"].",sysdate(),'".$datePaiement."',0,".$id_user.",".$idDeb.",".$creance->getIdPtf()->getId().",".$autreDetail["id"].",'".$commentaire."',1)";
                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                $sql = "SELECT MAX(id) FROM paiement";
                                $maxPaiement = $this->conn->executeQuery($sql)->fetchOne();
                                $sql="insert into paiement_accord (`id_paiement_id`,`id_details_accord_id`) values(".$maxPaiement.",".$autreDetail["id"].")";
                                $stmt = $this->conn->prepare($sql)->executeQuery();

                                // $checkIfAlreadyExist2 = $paiementRepo->checkIfUpdatedCreance($id_creance , $id_action);dump($checkIfAlreadyExist2);
                                // if(!$checkIfAlreadyExist2){
                                //     $newTotalRestant = $creance->getTotalRestant() - $rest;
                                //     $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                                //     (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                                //     VALUES (" . $id_creance . ", " . $creance->getTotalRestant() . ", " . $newTotalRestant . ", " . $rest . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                                // }else{
                                //     $newTotalRestant = $checkIfAlreadyExist2["new_total_restant"] - $rest;
                                //     $TotalRestant = $checkIfAlreadyExist2["new_total_restant"] ;
                                //     $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                                //     (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                                //     VALUES (" . $id_creance . ", " . $TotalRestant . ", " . $newTotalRestant . ", " . $rest . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                                // }
                                $total=($creance->getTotalRestant()-$rest);
                                $creance->setTotalRestant($total);
                                $this->em->flush();
                                $stmt = $this->conn->prepare($sql)->executeQuery();
                                $rest=0;
                            }
                        }
                    }
                    else
                    {//TODO:Non Validé
                        $solde = $paiementRepo->getRestantCreance($id_creance);
                        if($solde > $rest)
                        { 
                            $sql="insert into accord (`id_type_paiement_id`, `date_premier_paiement`, `montant`, `frequence`, `nbr_echeanciers`, `etat`, `date_creation`, `date_fin_paiement`,`id_users_id`) values(".$typePaiement->getId().",'".$datePaiement."',".$rest.",1,1,1,sysdate(),'".$datePaiement."','".$id_user."')";
                            $stmt = $this->conn->prepare($sql)->executeQuery();
                            $sql = "SELECT MAX(id) FROM accord";
                            $maxAccord = $this->conn->executeQuery($sql)->fetchOne();
                            $sql="insert into creance_accord (`id_creance_id`, `id_accord_id`) values(".$id_creance.",".$maxAccord.")";
                            $stmt = $this->conn->prepare($sql)->executeQuery();
                            $sql = "SELECT MAX(id) FROM creance_accord";
                            $maxCreanceAccord = $this->conn->executeQuery($sql)->fetchOne();
                            $sql="insert into details_accord (`id_accord_id`, `montant`, `status`, `id_type_paiement_id`, `montant_paiement`, `montant_restant`, `date_paiement`) values(".$maxAccord.",".$rest.",1,".$typePaiement->getId().",".$rest.",0,'".$datePaiement."')";
                            $stmt = $this->conn->prepare($sql)->executeQuery();
                            $nbrAccordsSys++;
                            $montantAccordsSys+=$rest;
                        }
                        else
                        {
                            $sql="insert into accord (`id_type_paiement_id`, `date_premier_paiement`, `montant`, `frequence`, `nbr_echeanciers`, `etat`, `date_creation`, `date_fin_paiement`) values(".$typePaiement->getId().",'".$datePaiement."',".$solde.",1,1,1,sysdate(),'".$datePaiement."')";
                            $stmt = $this->conn->prepare($sql)->executeQuery();
                            $sql = "SELECT MAX(id) FROM debt_force_integration.accord_dbi";
                            $maxAccord = $this->conn->executeQuery($sql)->fetchOne();
                            $sql="insert into creance_accord (`id_creance_id`, `id_accord_id`) values(".$id_creance.",".$maxAccord.")";
                            $stmt = $this->conn->prepare($sql)->executeQuery();
                            $sql = "SELECT MAX(id) FROM creance_accord";
                            $maxCreanceAccord = $this->conn->executeQuery($sql)->fetchOne();
                            $sql="insert into details_accord (`id_accord_id`, `montant`, `status`, `id_type_paiement_id`, `montant_paiement`, `montant_restant`, `date_paiement`) values(".$maxAccord.",".$solde.",1,".$typePaiement->getId().",".$rest.",0,'".$datePaiement."')";
                            $stmt = $this->conn->prepare($sql)->executeQuery();
                            $nbrAccordsSys++;
                            $montantAccordsSys+=$solde;
                        }
                        $sql = "SELECT MAX(id) FROM details_accord";
                        $maxDetailAccord = $this->conn->executeQuery($sql)->fetchOne();
                        $date=new \DateTime();
                        $dmy = $date->format('dmYHis');
                        $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $id_creance, "id_type" => 3]);
                        if (!$typeDeb) {
                            $typeDeb = $this->em->getRepository(TypeDebiteur::class)->findOneBy(["id_creance" => $id_creance]);
                        }
                        $idDeb =$typeDeb->getIdDebiteur()->getId();
                        $ref = "PA" . $dmy . ($typeDeb ? $typeDeb->getId() : "");
                        $sql="insert into paiement (`id_creance_id`, `id_type_paiement_id`, `ref`, `montant`, `date_creation`, `date_paiement`, `etat`, `id_users_id`, `id_debiteur_id`, `id_ptf_id`, `id_details_accord_id`, `commentaire`, `confirmed`) values(".$id_creance.",".$typePaiement->getId().",'".$ref."',".$montant.",sysdate(),'".$datePaiement."',0,".$id_user.",".$idDeb.",".$creance->getIdPtf()->getId().",".$maxDetailAccord.",'".$commentaire."',1)";
                        $stmt = $this->conn->prepare($sql)->executeQuery();

                        
                        $sql = "SELECT MAX(id) FROM paiement";
                        $maxPaiement = $this->conn->executeQuery($sql)->fetchOne();
                        $sql="insert into paiement_accord (`id_paiement_id`,`id_details_accord_id`) values(".$maxPaiement.",".$maxDetailAccord.")";
                        $stmt = $this->conn->prepare($sql)->executeQuery();

                        // $checkIfAlreadyExist = $paiementRepo->checkIfUpdatedCreance($id_creance , $id_action);
                        // if(!$checkIfAlreadyExist){
                        //     $newTotalRestant = $creance->getTotalRestant() - $montant;
                        //     $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                        //     (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                        //     VALUES (" . $id_creance . ", " . $creance->getTotalRestant() . ", " . $newTotalRestant . ", " . $montant . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                        // }else{
                        //     $newTotalRestant = $checkIfAlreadyExist["new_total_restant"] - $montant;
                        //     $TotalRestant = $checkIfAlreadyExist["new_total_restant"] ;
                        //     $sql = "INSERT INTO `debt_force_integration`.`creance_paiement_dbi` 
                        //     (`id_creance`, `old_total_restant`, `new_total_restant`, `montant_paiement`, `id_import`, `is_updated_in_this_action`, `id_action`)
                        //     VALUES (" . $id_creance . ", " . $TotalRestant . ", " . $newTotalRestant . ", " . $montant . ", " . $id_import . ", " . true . ", " . $id_action . ")";
                        // }
                        // $stmt = $this->conn->prepare($sql)->executeQuery();
                        $total=($creance->getTotalRestant()-$rest);
                        $creance->setTotalRestant($total);
                        $this->em->flush();
                        // $sql="insert into accord_import (`type`, `id_accord_id`, `id_details_accord_id`, `id_import_id`, `id_creance_accord_id`) values(1,".$maxAccord.",".$maxDetailAccord.",".$id_import.",".$maxCreanceAccord.")";
                        // $stmt = $this->conn->prepare($sql)->executeQuery();

                        // $sql="insert into paiement_import ( `id_import`, `id_creance`, `id_paiement`) values(".$id_import.",".$id_creance.",".$maxPaiement.")";
                        // $stmt = $this->conn->prepare($sql)->executeQuery();
                        $rest=0;
                    }
                }
            }
            return "OK";
        } catch (\Exception $e) {
            return "ERROR";
            //throw $th;
        }
        
    }
    public function getPtf($id){
        return $this->em->getRepository(Portefeuille::class)->find($id);
    }

    public function getListeOtherCreance($id){
        $sql="select * from creance where id_dossier_id = ".$id." and total_restant != '0'";
        $stmt = $this->conn->prepare($sql)->executeQuery();
        $resultat = $stmt->fetchAll();
        return $resultat;
    }

    public function createCreanceAccord($data){
        $sql = "INSERT INTO `creance_accord` (";
        $values = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $values[] = "`$key`";
        }
        $sql .= implode(', ', $values) . ')';
        
        $sql .= ' VALUES (';  

        $params = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $params[] = '"'.$value.'"';
        }
        $sql .= implode(', ', $params) . ')';
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        if($stmt){
            $sql="SELECT max(id) FROM `accord`";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $resulat = $stmt->fetchOne();
            return $resulat;
        }else{
            return null;
        }
    }
    
    public function getListeAccordByDossier($id){
        $query = $this->em->createQuery('SELECT a  from App\Entity\Accord a   where a.id in (SELECT identity(ca.id_accord) from App\Entity\CreanceAccord ca  where identity(ca.id_creance) in (select c.id from App\Entity\Creance c where identity(c.id_dossier) = :id) )')
        ->setParameters([
            'id' => $id
        ]);
        $result = $query->getResult();
        return $result;
    }
    public function getListeCreanceByDoss($id){
        $sql="select * from creance where id_dossier_id = ".$id." and total_restant != '0'";
        $stmt = $this->conn->prepare($sql)->executeQuery();
        $resultat = $stmt->fetchAll();

        $resultatArray = array();
        for ($i=0; $i <count($resultat) ; $i++) { 
            $resultatArray[$i] = $resultat[$i];
    
            $sql="SELECT * FROM `details_type_creance` where id = ".$resultat[$i]['id_type_creance_id']."";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $resultatArray[$i]['type_creance'] = $type;

        }

        return $resultatArray;
    }

    public function checkBookmark($id , $idUser){
        $checkBookMark  = $this->em->getRepository(Bookmarks::class)->findOneBy(['id_creance'=>$id , 'id_user'=>$idUser  ]);
        return $checkBookMark;
    }

    public function deleteBookMark($id){
        $bookMark  = $this->em->getRepository(Bookmarks::class)->find($id);
        $this->em->remove($bookMark);
        $this->em->flush();
    }
    public function getCreance($id){
        $entity  = $this->em->getRepository(Creance::class)->find($id);
        return $entity;
    }
    public function getUser($id){
        $entity  = $this->em->getRepository(Utilisateurs::class)->find($id);
        return $entity;
    }

    public function addBookmark($idCreance , $idUser){
        $creance = $this->getCreance($idCreance);
        $user = $this->getUser($idUser);
        $bookMark  = new Bookmarks();
        $bookMark->setIdCreance($creance);
        $bookMark->setIdUser($user);
        $this->em->persist($bookMark);
        $this->em->flush();
        return $bookMark;
    }

    public function getReglePtf($id){
        $entity  = $this->em->getRepository(ReglePortefeuille::class)->findBy(['idPtf'=>$id]);
        return $entity;
    }

    public function createPromise($data){
        $sql = "INSERT INTO `promise` (";
        $values = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            $values[] = "`$key`";
            
        }
        $sql .= implode(', ', $values) . ')';
        
        $sql .= ' VALUES (';  

        $params = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
            if($key != "date_creation"){
                $params[] = '"'.$value.'"';
            }else{
                $params[] = 'now()';
            }
        }
        $sql .= implode(', ', $params) . ')';
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        
        if($stmt){
            $sql="SELECT max(id) FROM `promise`";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $resulat = $stmt->fetchOne();
            return $resulat;
        }else{
            return null;
        }
    }

    public function updatePromise($data , $id){
        // $primaryKey = "id";
        $sql = "UPDATE `promise` SET ";
        $setClauses = [];
        foreach ($data as $key => $value) {
            // Enclose column names within backticks if needed
                $setClauses[] = "`$key` = :$key";
        }
        $sql .= implode(', ', $setClauses);
        // You need to specify which row to update, typically using the primary key
        $sql .= " WHERE `id` = :id";
        $stmt = $this->conn->prepare($sql);
        // Bind parameters for the SET clauses
        foreach ($data as $key => $value) {
            $stmt->bindParam($key, $value);  // Use $key directly as the parameter name
            // Bind the primary key parameter
        }
        $stmt->bindParam("id", $id);
        $stmt = $stmt->executeQuery();
    }

    
    public function getListPromise($id){
        $entity  = $this->em->getRepository(Promise::class)->findBy(['id_creance'=>$id]);
        return $entity;
    }
    public function getPromise($id){
        $entity  = $this->em->getRepository(Promise::class)->find(['id'=>$id]);
        return $entity;
    }

    public function getParamActivity($id){
        $entity  = $this->em->getRepository(ParamActivite::class)->find($id);
        return $entity;
    }

    public function addTask($creance, $activity, $dateEcheance, $time, $assigned , $user , $comment)
    {
        $user = $this->getUser($user);
        $task = new Task();
        $task->setIdCreance($creance);
        $task->setIdActivity($activity);
        $task->setDateEcheance($dateEcheance);
        $task->setTemps($time);
        $task->setAssignedType($assigned);
        $task->setDateCreation(new \DateTime());
        $task->setCreatedBy($user);
        $task->setCommentaire($comment);
        $this->em->persist($task);
        $this->em->flush();

        return $task;
    }

    public function addAssignedTask($task,  $user)
    {
        $user = $this->getUser($user);
        $taskAssigned = new TaskAssigned();
        $taskAssigned->setIdTask($task);
        $taskAssigned->setIdUser($user);
        $this->em->persist($taskAssigned);
        $this->em->flush();

        return $taskAssigned;
    }
    public function getUtilisateurs($id){
        $entity  = $this->em->getRepository(Utilisateurs::class)->findAll();
        return $entity;
    }

    public function getListTask($id){
        $entity  = $this->em->getRepository(Task::class)->findBy(['idCreance'=>$id]);
        return $entity;
    }
    public function getAssignedTask($id){
        $entity  = $this->em->getRepository(TaskAssigned::class)->findOneBy(['id_task'=>$id] ,["id"=>"DESC"]);
        return $entity ? $entity->getIdUser() : null;
    }
    
    public function getTask($id){
        $entity  = $this->em->getRepository(Task::class)->find(['id'=>$id]);
        return $entity;
    }
    
    // public function getEmailsByCr($id){
    //     $query = $this->em->createQuery('SELECT a  from App\Entity\Email a where identity(a.id_debiteur) in (SELECT identity(ca.id_debiteur) from App\Entity\Debiteur ca  where (ca.id) in (select identity(c.id_debiteur) from App\Entity\TypeDebiteur c where identity(c.id_creance) = :id) )')
    //     ->setParameters([
    //         'id' => $id
    //     ]);
    //     $result = $query->getResult();
    //     return $result;
    // }
    public function getEmailsByCr($id) {
        $array = [];
        $sql="SELECT e.*
             FROM Email e 
             WHERE (e.id_debiteur_id) IN (
                 SELECT ca.id
                 FROM Debiteur ca 
                 WHERE ca.id IN (
                     SELECT (c.id_debiteur_id) 
                     FROM Type_Debiteur c 
                     WHERE (c.id_creance_id) = ".$id."
                 )
             );";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        for ($i=0; $i <count($resulat) ; $i++) { 
            $array[$i] = $resulat[$i];
            $array[$i]['id_type_email'] = $this->TypeService->getTypeById($resulat[$i]['id_type_email_id'] , 'email');
        }
        return $array;
    }
    
    public function getAddressesCr($id) {
        $array = [];
        $sql="SELECT e.*
             FROM Adresse e 
             WHERE (e.id_debiteur_id) IN (
                 SELECT ca.id
                 FROM Debiteur ca 
                 WHERE ca.id IN (
                     SELECT (c.id_debiteur_id) 
                     FROM Type_Debiteur c 
                     WHERE (c.id_creance_id) = ".$id."
                 )
             );";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        for ($i=0; $i <count($resulat) ; $i++) { 
            $array[$i] = $resulat[$i];
            $array[$i]['id_type_adresse_id'] = $this->TypeService->getTypeById($resulat[$i]['id_type_adresse_id'] , 'adresse');
        }
        return $array;
    }

    public function getTelephonesCr($id) {
        $array = [];
        $sql="SELECT e.*
             FROM Telephone e 
             WHERE (e.id_debiteur_id) IN (
                 SELECT ca.id
                 FROM Debiteur ca 
                 WHERE ca.id IN (
                     SELECT (c.id_debiteur_id) 
                     FROM Type_Debiteur c 
                     WHERE (c.id_creance_id) = ".$id."
                 )
             );";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        for ($i=0; $i <count($resulat) ; $i++) { 
            $array[$i] = $resulat[$i];
            $array[$i]['id_type_tel_id'] = $this->TypeService->getTypeById($resulat[$i]['id_type_tel_id'] , 'tel');
        }
        return $array;
    }
    public function getModelCourrier($id){
        return $this->em->getRepository(ModelCourier::class)->find($id);
    }
    public function getAdresse($id){
        return $this->em->getRepository(Adresse::class)->find($id);
    }
    public function getModelSMS($id){
        return $this->em->getRepository(ModelSMS::class)->find($id);
    }
    public function addActivity($creance, $activity, $assigned , $user , $comment)
    {
        $user = $this->getUser($user);
        $task = new CreanceActivite();
        $task->setIdCreance($creance);
        $task->setIdParamActivite($activity);
        $task->setAssignedType($assigned);
        $task->setDateCreation(new \DateTime());
        $task->setCreatedBy($user);
        $task->setCommentaire($comment);
        $this->em->persist($task);
        $this->em->flush();

        return $task;
    }

    public function addAssignedActivity($activity,  $user)
    {
        $user = $this->getUser($user);
        $taskAssigned = new ActiviteAssigned();
        $taskAssigned->setIdCreanceActivite($activity);
        $taskAssigned->setIdUser($user);
        $this->em->persist($taskAssigned);
        $this->em->flush();

        return $taskAssigned;
    }

    public function getListesDebiteurByDossier($id){
        $sql="SELECT  deb.* FROM debiteur deb where deb.id in (select dd.id_debiteur_id from type_debiteur dd where dd.id_creance_id = :id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        // Fetch type information for each telephone
        
        for ($i=0; $i <count($resulat) ; $i++) { 
            $sql = "SELECT * FROM `details_type_deb` dt where dt.id in (select d.id_type_id from type_debiteur d where d.id_creance_id in (select c.id from creance c where c.id_dossier_id = :id));";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $resulat[$i]['id']);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $resulat[$i]["type"] = $type;
        }

        for ($i=0; $i <count($resulat) ; $i++) { 
            $sql = "SELECT * FROM `personne` dt where dt.id in (select d.id_personne_id from relation_debiteur d where d.id_debiteur_id =:id);";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $resulat[$i]['id']);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAll();
            $resulat[$i]["relation"] = $type;
        }
        return $resulat;
    }
}