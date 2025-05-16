<?php

namespace App\Repository\Creances;

use App\Entity\ActiviteAssigned;
use App\Entity\Adresse;
use App\Entity\Bookmarks;
use App\Entity\Creance;
use App\Entity\CreanceAccord;
use App\Entity\CreanceActivite;
use App\Entity\Debiteur;
use App\Entity\DetailsAccord;
use App\Entity\Email;
use App\Entity\ModelCourier;
use App\Entity\ModelSMS;
use App\Entity\Paiement;
use App\Entity\PaiementAccord;
use App\Entity\ParamActivite;
use App\Entity\Personne;
use App\Entity\Portefeuille;
use App\Entity\Promise;
use App\Entity\RecentCreance;
use App\Entity\ReglePortefeuille;
use App\Entity\Task;
use App\Entity\TaskAssigned;
use App\Entity\Teams;
use App\Entity\Telephone;
use App\Entity\TypeDebiteur;
use App\Entity\TypePaiement;
use App\Entity\Utilisateurs;
use App\Repository\Encaissement\paiementRepo;
use App\Service\typeService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;
use PhpParser\Builder\Function_;

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
        INNER JOIN debiteur deb ON t.id_debiteur_id = deb.id ';

        if ($tel != "") {
            $query .= ' INNER JOIN telephone tel ON deb.id = tel.id_debiteur_id AND tel.numero = "'.$tel.'" ';
        }
        if ($addr != "") {
            $query .= ' INNER JOIN adresse ad ON deb.id = ad.id_debiteur_id 
            AND (
                ad.adresse_complet = "'.$addr.'" 
                OR ad.pays = "'.$addr.'" 
                OR ad.ville = "'.$addr.'" 
                OR ad.code_postal = "'.$addr.'" 
                OR ad.province = "'.$addr.'"
            ) ';
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

            $sql="select * from utilisateurs u where u.id = ".$resulat[$i]['id_users_id']." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $act = $stmt->fetchAssociative();
            $resulat[$i]["agent"] = $act;
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

            $sql="select * from param_activite ca where ca.id = ".$result[$i]['id_param_parent_id']." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $act = $stmt->fetchAssociative();
            $result[$i]["parent"] = $act;

            $sql="select * from utilisateurs u where u.id = ".$result[$i]['created_by_id']." ";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $act = $stmt->fetchAssociative();
            $result[$i]["agent"] = $act;
        }
        return $result;
    }
    
    public function getPaiement($id){
        $sql="SELECT * FROM `paiement` where id_creance_id = ".$id."";
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

                                // $nbrAccordsAgent++;
                                // $montantAccordsAgent+=$autreDetail["montant_restant"];

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
                                
                                $total=($creance->getTotalRestant()-$autreDetail["montant_restant"]);
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

                                // $nbrAccordsAgent++;
                                // $montantAccordsAgent+=$rest;

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
    


    public function getListeAccordByDossier($id)
    {
        $sql = "SELECT * FROM creance WHERE id_dossier_id = :id";
        $stmt = $this->conn->prepare($sql);
        $resultat = $stmt->executeQuery(['id' => $id])->fetchAll(); // Fetch all creances for the dossier
        
        $resultatArray = [];
        $existingAccordIds = []; // Array to keep track of the unique accord IDs

        foreach ($resultat as $creance) {
            $creanceId = $creance['id'];
    
            // Query for the accords, including the status and type of payment
            $sqlAccord = "
                SELECT 
                    a.*, 
                    sa.status AS status_statut, 
                    tp.type AS type_paiement 
                FROM accord a
                LEFT JOIN status_accord sa ON a.id_status_id = sa.id
                LEFT JOIN type_paiement tp ON a.id_type_paiement_id = tp.id
                WHERE a.id IN (
                    SELECT distinct id_accord_id FROM creance_accord WHERE id_creance_id = :creanceId
                )
            ";
            $stmtAccord = $this->conn->prepare($sqlAccord);
            $accords = $stmtAccord->executeQuery(['creanceId' => $creanceId])->fetchAll(); // Fetch accords with joins
    
            // Merge results into the final array
            foreach ($accords as $accord) {
                $accordId = $accord['id'];
    
                // Check if the accord has already been added to the result array
                if (!in_array($accordId, $existingAccordIds)) {
                    $resultatArray[] = $accord; // Add the unique accord to the result array
                    $existingAccordIds[] = $accordId; // Mark this accord ID as processed
                }
            }

        }
    
        return $resultatArray;
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

            // Fetch the detail_creance associated with the current creance
            $sql = "SELECT * FROM `detail_creance` WHERE id_creance_id = ".$resultat[$i]["id"]."";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $detailsCreance = $stmt->fetchAssociative();
            $resultatArray[$i]['detail_creance'] = $detailsCreance;

            $sql="SELECT * FROM `portefeuille` where id = ".$resultat[$i]['id_ptf_id']."";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $portefeuille = $stmt->fetchAssociative();
            $resultatArray[$i]['portefeuille'] = $portefeuille;

            $resultatArray[$i]['allDebiteur'] = $this->getListesDebiteurByDossier($resultat[$i]["id"]);

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
        // Convert date format for MySQL
        if (isset($data['date']) && $data['date']) {
            $dateObj = DateTime::createFromFormat('d/m/Y', $data['date']);
            if ($dateObj) {
                $data['date'] = $dateObj->format('Y-m-d H:i:s');
            }
        }
    
        // Convert date_creation to string format
        $data['date_creation'] = date('Y-m-d H:i:s');
    
        $sql = "INSERT INTO `promise` (";
        $columns = [];
        $placeholders = [];
        $values = [];
    
        foreach ($data as $key => $value) {
            // Skip null or empty values
            if ($value === null || $value === '') {
                continue;
            }
    
            $columns[] = "`$key`";
            $placeholders[] = "?";
            $values[] = $value;
        }
    
        $sql .= implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';
    
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->executeQuery($values);
    
            // Fetch and return the last inserted ID
            $resultStmt = $this->conn->prepare("SELECT LAST_INSERT_ID()");
            $result = $resultStmt->executeQuery();
            return $result->fetchOne();
        } catch (\Exception $e) {
            // Log the error
            error_log('Promise creation error: ' . $e->getMessage());
            return null;
        }
    }

    public function updatePromise($data, $id){
        // Convert date format for MySQL
        if (isset($data['date']) && $data['date']) {
            $dateObj = DateTime::createFromFormat('d/m/Y', $data['date']);
            if ($dateObj) {
                $data['date'] = $dateObj->format('Y-m-d H:i:s');
            }
        }


        $sql = "UPDATE `promise` SET ";
        $setClauses = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "`$key` = :$key";
        }
        $sql .= implode(', ', $setClauses);
        $sql .= " WHERE `id` = :id";
        
        $stmt = $this->conn->prepare($sql);
        
        // Use bindValue instead of bindParam
        foreach ($data as $key => $value) {
            // Handle special cases
            if ($value === 'now()') {
                $stmt->bindValue($key, date('Y-m-d H:i:s'), \PDO::PARAM_STR);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        $stmt->bindValue("id", $id);
        
        return $stmt->executeQuery();
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

    public function addTaskWithAssigned($creance, $activity, $dateEcheance, $time, $assigned , $user , $comment, $assignedUser, $assignedD, $dep)
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
        $task->setAssignedUser($assignedUser);
        $task->setEquipe($assignedD);
        $task->setAssignedDepartement($dep);
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

    public function getEquipes(){
        $teams  = $this->em->getRepository(Teams::class)->findAll();
        return $teams;
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
    public function addActivity($creance, $activity, $assigned , $user , $comment, $type, $debiteur, $personne, $email, $telephone, $adresse, $activite)
    {
        $user = $this->getUser($user);
        $task = new CreanceActivite();
        $task->setIdCreance($creance);
        $task->setIdParamActivite($activity);
        $task->setAssignedType($assigned);
        $task->setDateCreation(new \DateTime());
        $task->setCreatedBy($user);
        $task->setCommentaire($comment);
        $task->setIdParamParent($type);
        $task->setTypeActivite($activite);
        $task->setDebiteur($debiteur);
        $task->setPersonne($personne);
        $task->setEmail($email);
        $task->setTelephone($telephone);
        $task->setAdresse($adresse);

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
            $sql = "SELECT * FROM `details_type_deb` dt 
                    WHERE dt.id IN (
                        SELECT d.id_type_id 
                        FROM type_debiteur d 
                        WHERE d.id_creance_id = :id 
                        AND d.id_debiteur_id = :idDebiteur
                    )";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('idDebiteur', $resulat[$i]['id']);
            $stmt->bindParam('id', $id);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAssociative();
            $resulat[$i]["type"] = $type ? $type : null;  // Ensure it handles the case when no type is found
        }

        for ($i=0; $i <count($resulat) ; $i++) { 
            $sql = "SELECT * FROM `personne` dt where dt.id in (select d.id_personne_id from relation_debiteur d where d.id_debiteur_id =:id);";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $resulat[$i]['id']);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAll();
            $resulat[$i]["relation"] = $type;
        }

        for ($i=0; $i <count($resulat) ; $i++) { 
            $sql = "SELECT * FROM `telephone` t where  t.id_debiteur_id =:id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $resulat[$i]['id']);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAll();
            $resulat[$i]["telephones"] = $type;
        }

        for ($i=0; $i <count($resulat) ; $i++) { 
            $sql = "SELECT * FROM `adresse` a where  a.id_debiteur_id =:id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $resulat[$i]['id']);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAll();
            $resulat[$i]["adresses"] = $type;
        }

        for ($i=0; $i <count($resulat) ; $i++) { 
            $sql = "SELECT * FROM `email` e where  e.id_debiteur_id =:id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam('id', $resulat[$i]['id']);
            $stmt = $stmt->executeQuery();
            $type = $stmt->fetchAll();
            $resulat[$i]["emails"] = $type;
        }
        return $resulat;
    }


    public function deletePromise($id){
        $promise  = $this->em->getRepository(Promise::class)->find($id);
        $this->em->remove($promise);
        $this->em->flush();
    }


    public function deletePaiement($id){
        // Start a transaction
        $this->em->beginTransaction();
    
        try {
            // Find the Paiement entity
            $paiement = $this->em->getRepository(Paiement::class)->find($id);
            
            if (!$paiement) {
                return 'NOT_EXIST_PAIEMENT';
            }
    

            // Remove related PaiementAccord entries
            $paiementAccordEntries = $this->em->getRepository(PaiementAccord::class)
                ->findBy(['id_paiement' => $paiement]);
            
            foreach ($paiementAccordEntries as $paiementAccord) {
                $detailsAccord = $paiementAccord->getIdDetailsAccord();
                
                // Remove PaiementAccord
                $this->em->remove($paiementAccord);
    
                // Check if we can remove DetailsAccord
                if ($detailsAccord) {
                    $relatedPaiements = $this->em->getRepository(PaiementAccord::class)
                        ->findBy(['id_details_accord' => $detailsAccord]);
                    
                    // If no other paiements are linked to this DetailsAccord
                    if (count($relatedPaiements) <= 1) {
                        // Get the associated Accord
                        $accord = $detailsAccord->getIdAccord();
                        
                        // Remove DetailsAccord
                        $this->em->remove($detailsAccord);
    
                        // Check if we can remove Accord
                        if ($accord) {
                            $remainingDetailsAccords = $this->em->getRepository(DetailsAccord::class)
                                ->findBy(['id_accord' => $accord]);
                            
                            // If no other DetailsAccords exist for this Accord
                            if (count($remainingDetailsAccords) <= 1) {
                                // Remove associated CreanceAccord
                                $creanceAccords = $this->em->getRepository(CreanceAccord::class)
                                    ->findBy(['id_accord' => $accord]);
                                
                                foreach ($creanceAccords as $creanceAccord) {
                                    $this->em->remove($creanceAccord);
                                }
    
                                // Remove Accord
                                $this->em->remove($accord);
                            }
                        }
                    }
                }
            }

            $creance = $this->em->getRepository(Creance::class)->find($paiement->getIdCreance());
            if($creance)
            {
                $montant = $paiement->getMontant();
    
                $total=($creance->getTotalRestant()+$montant);
                $creance->setTotalRestant($total);
    
            }

            // Remove the Paiement
            $this->em->remove($paiement);
    
            // Flush all changes
            $this->em->flush();
    
            // Commit the transaction
            $this->em->commit();
            return "OK";

    
        } catch (\Exception $e) {
            // Rollback the transaction
            $this->em->rollback();
    
            // Re-throw the exception or handle it as needed
            throw $e;
        }
    }


    public function deleteTask($id)
    {
        // Start a transaction
        $this->em->beginTransaction();
    
        try {
            // Find the Task entity
            $task = $this->em->getRepository(Task::class)->find($id);
            
            if (!$task) {
                return 'NOT_EXIST_TASK';

            }
    
            // Remove related TaskAssigned entries
            $taskAssignedEntries = $this->em->getRepository(TaskAssigned::class)
                ->findBy(['id_task' => $task]);
            
            foreach ($taskAssignedEntries as $taskAssigned) {
                // Remove TaskAssigned
                $this->em->remove($taskAssigned);
            }
    
            // Remove the Task
            $this->em->remove($task);
    
            // Flush all changes
            $this->em->flush();
    
            // Commit the transaction
            $this->em->commit();
    
            return "OK";
    
        } catch (\Exception $e) {
            // Rollback the transaction
            $this->em->rollback();
    
            // Re-throw the exception or handle it as needed
            throw $e;
        }
    }

    public function getBookMarks($idUser)
    {
        $sql = '
            SELECT c.* 
            FROM creance c
            WHERE c.id IN (
                SELECT b.id_creance_id 
                FROM bookmarks b
                WHERE b.id_user_id = "'.$idUser.'"
            )';
    
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    
    

    public function addRecentCreance($creance, $user)
    {
        $user = $this->getUser($user);
        $creance = $this->getCreance($creance);
    
        // Check if the recentCreance entry already exists
        $recentCreance = $this->em->getRepository(RecentCreance::class)
            ->findOneBy(['user_id' => $user, 'creance_id' => $creance]);
    
        // If it exists, remove it
        if ($recentCreance) {
            $this->em->remove($recentCreance);
            $this->em->flush();
        }
    
        // Create a new RecentCreance entry
        $recent = new RecentCreance();
        $recent->setUserId($user);
        $recent->setDate(new \DateTime());
        $recent->setCreanceId($creance);
    
        // Persist and flush the new entry
        $this->em->persist($recent);
        $this->em->flush();
    
        return $recent;
    }

    public function getRecentsCreance($idUser)
    {
        $sql = '
        SELECT c.* 
        FROM creance c
        INNER JOIN recent_creance r ON c.id = r.creance_id_id
        WHERE r.user_id_id = '.$idUser.'
        ORDER BY r.date DESC';
    
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }

    public function getAccordWithDetails($id)
    {
        // Fetch the main accord
        $sql = "
            SELECT 
                a.*, 
                tp.type AS type_paiement,
                sa.status AS status_accord
            FROM 
                accord a
            LEFT JOIN 
                type_paiement tp ON a.id_type_paiement_id = tp.id
            LEFT JOIN 
                status_accord sa ON a.id_status_id = sa.id
            WHERE 
                a.id = :id;
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt = $stmt->executeQuery();
        $accord = $stmt->fetchAssociative();

        if ($accord) {
            // Fetch the details, attachments, and notes for this accord
            $accord['details'] = $this->getDetailsAccord($id);
            $accord['attachments'] = $this->getAccordAttachments($id);
            $accord['notes'] = $this->getAccordNotes($id);
            $accord['creances'] = $this->getAccordCreances($id);

        }
    
        return $accord;
    }

    public function getDetailsAccord($id)
    {
        $sql = "
            SELECT 
                da.*, 
                tp.type AS type_paiement,
                sa.status AS status_accord
            FROM 
                details_accord da
            INNER JOIN 
                accord a ON da.id_accord_id = a.id
            LEFT JOIN 
                type_paiement tp ON da.id_type_paiement_id = tp.id
            LEFT JOIN 
                status_details_accord sa ON da.id_status_id = sa.id
            WHERE 
                da.id_accord_id = :id;
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt = $stmt->executeQuery();

        // Fetch all associative results
        return $stmt->fetchAllAssociative();
    }   

    public function getAccordAttachments($id)
    {
        $sql = "
            SELECT 
                pj.* 
            FROM 
                accord_pj pj
            WHERE 
                pj.id_accord_id = :id;
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt = $stmt->executeQuery();

        return $stmt->fetchAllAssociative();
    }

    public function getAccordNotes($id)
    {
        $sql = "
            SELECT 
                n.* 
            FROM 
                accord_notes n
            WHERE 
                n.id_accord_id = :id
            ORDER BY 
                n.date_creation DESC;
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt = $stmt->executeQuery();

        return $stmt->fetchAllAssociative();
    }

    public function UpdateAccord($id, $dataAccord)
    {
        $sql = "
            UPDATE accord
            SET 
                date_premier_paiement = :date_premier_paiement,
                date_fin_paiement = :date_fin_paiement,
                nbr_echeanciers = :nbr_echeanciers
            WHERE id = :id;
        ";
    
        // Prepare the SQL query
        $stmt = $this->conn->prepare($sql);
    
        // Bind the parameters to the prepared statement
        $stmt->bindParam(':date_premier_paiement', $dataAccord['date_premier_paiement']);
        $stmt->bindParam(':date_fin_paiement', $dataAccord['date_fin_paiement']);
        $stmt->bindParam(':nbr_echeanciers', $dataAccord['nbr_echeanciers'], \PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
    
        // Execute the query and return the result
        return $stmt->execute();
    }
        
    public function deleteDetailsByAccordId($accordId)
    {
        $sql = "
            DELETE FROM details_accord
            WHERE id_accord_id = :id_accord_id;
        ";
    
        // Prepare and execute the delete query
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_accord_id', $accordId, \PDO::PARAM_INT);
        return $stmt->execute();
    }
  
    public function deleteNotesByAccordId($accordId)
    {
        $sql = "
            DELETE FROM accord_notes
            WHERE id_accord_id = :id_accord_id;
        ";

        // Prepare and execute the delete query
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_accord_id', $accordId, \PDO::PARAM_INT);
        return $stmt->execute();
    }


    public function getAccordCreances($id)
    {
        // Query to fetch creances and their type_creance
        $sql = "
            SELECT 
                c.*, 
                dtc.type AS type_creance
            FROM 
                creance c
            INNER JOIN 
                creance_accord ca ON c.id = ca.id_creance_id
            LEFT JOIN 
                details_type_creance dtc ON c.id_type_creance_id = dtc.id
            WHERE 
                ca.id_accord_id = :id;
        ";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt = $stmt->executeQuery();
    
        // Fetch all creances with their type_creance
        return $stmt->fetchAllAssociative();
    }
    
    public function updateCreance($data, $id){
        // Convert date format for MySQL
        if (isset($data['date']) && $data['date']) {
            $dateObj = DateTime::createFromFormat('d/m/Y', $data['date']);
            if ($dateObj) {
                $data['date'] = $dateObj->format('Y-m-d H:i:s');
            }
        }


        $sql = "UPDATE `creance` SET ";
        $setClauses = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "`$key` = :$key";
        }
        $sql .= implode(', ', $setClauses);
        $sql .= " WHERE `id` = :id";
        
        $stmt = $this->conn->prepare($sql);
        
        // Use bindValue instead of bindParam
        foreach ($data as $key => $value) {
            // Handle special cases
            if ($value === 'now()') {
                $stmt->bindValue($key, date('Y-m-d H:i:s'), \PDO::PARAM_STR);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        $stmt->bindValue("id", $id);
        
        return $stmt->executeQuery();
    }


    public function UpdateAccordEtat($id, $dataAccord)
    {
        $sql = "
            UPDATE accord
            SET 
                id_status_id = :id_status_id,
                motif = :motif
            WHERE id = :id;
        ";
    
        // Prepare the SQL query
        $stmt = $this->conn->prepare($sql);
    
        // Bind the parameters to the prepared statement
        $stmt->bindParam(':id_status_id', $dataAccord['id_status_id']);
        $stmt->bindParam(':motif', $dataAccord['motif']);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
    
        // Execute the query and return the result
        return $stmt->execute();
    }


    public function UpdateAccordSave($id, $dataAccord)
    {
        // Prepare the SQL query with the correct fields
        $sql = "
            UPDATE accord
            SET 
                date_premier_paiement = :date_premier_paiement,
                date_fin_paiement = :date_fin_paiement,
                nbr_echeanciers = :nbr_echeanciers,
                remise = :remise,
                montant_a_payer = :montant_a_payer,
                montant_de_base = :montant_de_base
            WHERE id = :id;
        ";

        // Prepare the SQL statement
        $stmt = $this->conn->prepare($sql);

        // Bind the parameters to the prepared statement
        $stmt->bindParam(':date_premier_paiement', $dataAccord['date_premier_paiement']);
        $stmt->bindParam(':date_fin_paiement', $dataAccord['date_fin_paiement']);
        $stmt->bindParam(':nbr_echeanciers', $dataAccord['nbr_echeanciers'], \PDO::PARAM_INT);
        $stmt->bindParam(':remise', $dataAccord['remise']);
        $stmt->bindParam(':montant_a_payer', $dataAccord['montant_a_payer']);
        $stmt->bindParam(':montant_de_base', $dataAccord['montant_de_base']);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

        // Execute the query and return the result
        return $stmt->execute();
    }


    public function getOneActivite($idActivite){

        return $this->em->getRepository(CreanceActivite::class)->findOneBy(["id" => $idActivite]);

    }

    public function getOneDebiteur($id){

        return $this->em->getRepository(Debiteur::class)->findOneBy(["id" => $id]);

    }

    public function getOnePersonne($id){

        return $this->em->getRepository(Personne::class)->findOneBy(["id" => $id]);

    }

    public function getOneTelephone($id){

        return $this->em->getRepository(Telephone::class)->findOneBy(["id" => $id]);

    }

    public function getOneEmail($id){

        return $this->em->getRepository(Email::class)->findOneBy(["id" => $id]);

    }

    public function getOneAdresse($id){

        return $this->em->getRepository(Adresse::class)->findOneBy(["id" => $id]);

    }


    public function getCalendrierTache($id)
    {
        // Query to fetch tasks assigned to the user
        $sql = "
            SELECT 
                t.* 
            FROM 
                task t
            WHERE 
                t.assigned_user_id = :id;
        ";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt = $stmt->executeQuery();
    
        // Fetch all tasks
        return $stmt->fetchAllAssociative();
    }

    public function getCalendrierTacheByTeam($id)
    {
        // Query to fetch tasks assigned to the team
        $sql = "
            SELECT 
                t.* 
            FROM 
                task t
            WHERE 
                t.equipe_id = :id;
        ";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt = $stmt->executeQuery();
    
        // Fetch all tasks
        return $stmt->fetchAllAssociative();
    }
    
    public function getCalendrierTacheByDepartement($id)
    {
        // Query to fetch tasks assigned to the department
        $sql = "
            SELECT 
                t.* 
            FROM 
                task t
            WHERE 
                t.assigned_departement_id = :id;
        ";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt = $stmt->executeQuery();
    
        // Fetch all tasks
        return $stmt->fetchAllAssociative();
    }


    /*public function getProductionByUser()
    {
        $sqlAccord = "
            SELECT 
                a.*, 
            FROM 
                accord a
            WHERE 
                a.id_users_id = :id;
        ";

        $stmtAcc = $this->conn->prepare($sqlAccord);
        $stmtAcc->bindParam(':id', $id);
        $stmtAcc = $stmtAcc->executeQuery();


        $sql = "
            SELECT 
                c.*, 
            FROM 
                creance_activite c
            WHERE 
                c.created_by_id = :id;
        ";
        

        // Fetch all creances with their type_creance
        return $stmtAcc->fetchAllAssociative();
    }*/


    public function getProductionByUser($id)
    {
        // Query for accord table
        $sqlAccord = "
            SELECT 
                a.* 
            FROM 
                accord a
            WHERE 
                a.id_users_id = :id;
        ";

        $stmtAcc = $this->conn->prepare($sqlAccord);
        $stmtAcc->bindParam(':id', $id);
        $stmtAcc = $stmtAcc->executeQuery();
        $accords = $stmtAcc->fetchAllAssociative();

        // Query for creance_activite table
        $sqlCreance = "
            SELECT 
                c.* 
            FROM 
                creance_activite c
            WHERE 
                c.created_by_id = :id;
        ";

        $stmtCreance = $this->conn->prepare($sqlCreance);
        $stmtCreance->bindParam(':id', $id);
        $stmtCreance = $stmtCreance->execute();
        $creances = $stmtCreance->fetchAllAssociative();


        // Query for creance_activite table
        $sqlPromesse = "
            SELECT 
                p.* 
            FROM 
                promise p
            WHERE 
                p.id_user_id = :id;
        ";

        $stmtPro = $this->conn->prepare($sqlPromesse);
        $stmtPro->bindParam(':id', $id);
        $stmtPro = $stmtPro->execute();
        $promisses = $stmtPro->fetchAllAssociative();
    

        // Combine results
        $result = [
            'accords' => $accords,
            'creances' => $creances,
            'promisses' => $promisses
        ];

        return $result;
    }

    public function getProductionByUsers(array $ids)
    {
        if (empty($ids)) {
            return [
                'accords' => [],
                'creances' => [],
                'promesses' => []
            ];
        }
    
        // Convert array of IDs to a comma-separated string
        $idsString = implode(',', array_map('intval', $ids));
    
        // Query for accord table
        $sqlAccord = "
            SELECT 
                a.* 
            FROM 
                accord a
            WHERE 
                a.id_users_id IN ($idsString);
        ";
    
        $stmtAcc = $this->conn->prepare($sqlAccord);
        $stmtAcc = $stmtAcc->execute();
        $accords = $stmtAcc->fetchAllAssociative();
    
        // Query for creance_activite table
        $sqlCreance = "
            SELECT 
                c.* 
            FROM 
                creance_activite c
            WHERE 
                c.created_by_id IN ($idsString);
        ";
    
        $stmtCreance = $this->conn->prepare($sqlCreance);
        $stmtCreance = $stmtCreance->execute();
        $creances = $stmtCreance->fetchAllAssociative();
    
        // Query for promesse table
        $sqlPromesse = "
            SELECT 
                p.* 
            FROM 
                promise p
            WHERE 
                p.id_user_id IN ($idsString);
        ";
    
        $stmtPromesse = $this->conn->prepare($sqlPromesse);
        $stmtPromesse = $stmtPromesse->execute();
        $promesses = $stmtPromesse->fetchAllAssociative();
    
        // Combine results
        return [
            'accords' => $accords,
            'creances' => $creances,
            'promesses' => $promesses
        ];
    }
    


    
}