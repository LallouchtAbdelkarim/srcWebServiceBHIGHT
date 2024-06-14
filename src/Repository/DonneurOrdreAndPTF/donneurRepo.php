<?php

namespace App\Repository\DonneurOrdreAndPTF;

use App\Entity\Champs;
use App\Entity\ContactDonneurOrdre;
use App\Entity\ContactHistorique;
use App\Entity\DetailModelAffichage;
use App\Entity\DetailsTypeCreance;
use App\Entity\DonneurOrdre;
use App\Entity\ListesRoles;
use App\Entity\ModelFacturation;
use App\Entity\Portefeuille;
use App\Entity\PtfTypeCreanceD;
use App\Entity\TypeDonneur;
use App\Entity\DetailsSecteurActivite;
use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class donneurRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn, EntityManagerInterface $em, private ValidatorInterface $validator)
    {
        $this->conn = $conn;
        $this->em = $em;
    }

    public function createDonneurOrdre($data)
    {
        if ($data) {
            
            $modelFacturation = $this->em->getRepository(ModelFacturation::class)->findOneBy(['id' => $data["regleSelected"]]);
            $type_donneur = $this->em->getRepository(TypeDonneur::class)->findOneBy(['id' => $data["id_type"]]);

            $donneur_ordre = new DonneurOrdre();
            $donneur_ordre->setNom($data['nom']);
            $donneur_ordre->setMetier($data['metier']);
            $donneur_ordre->setRaisonSociale($data['rs']);
            $donneur_ordre->setNumeroRc($data['num_rc']);
            $donneur_ordre->setCp($data['c_postale']);
            $donneur_ordre->setCompteBancaire($data['compte_bancaire']);
            $donneur_ordre->setDateCreation(new \DateTime());
            $donneur_ordre->setDateDebut(new \DateTime());
            $donneur_ordre->setDateFin(new \DateTime());
            $donneur_ordre->setIdModeleRegle($modelFacturation);
            $donneur_ordre->setIdType($type_donneur);
            $this->em->persist($donneur_ordre);
            $this->em->flush();
            return $donneur_ordre;
        } else return false;
    }

    public function createHistoriques($idDonneur , $type , $note){
        $contact = new ContactHistorique();
        $contact->setIdDonneur($idDonneur);
        $contact->setType($type);
        $contact->setNote($note);
        $contact->setDateCreation(new \DateTime());
        $this->em->persist($contact);
        $this->em->flush();
    }

    public function getDetailsDonneur(){
        $donneur_details = array();
        $sql="select * from donneur_ordre ";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $dn = $stmt->fetchAll();
        
        for ($i=0; $i < count($dn); $i++) { 
            $donneur_details[$i]["dn"] =  $dn[$i];
            $activite =  $this->em->getRepository(ContactDonneurOrdre::class)->findBy(['id_donneurOrdre' => $dn[$i]["id"]]);
            $donneur_details[$i]["contacts"] =  $activite;
        }
        return $donneur_details;
    }
    public function getListPtf(){
        $ptf = "SELECT * FROM `portefeuille` ";
        $stmt = $this->conn->prepare($ptf);
        $stmt = $stmt->executeQuery();
        $resulatPtf = $stmt->fetchAllAssociative();
        return $resulatPtf;
    }

    public function AddChamps($data, $id)
    {
        if ($data) {
            foreach ($data as $key => $value) {

                $input = $this->em->getRepository(DetailModelAffichage::class)->findOneBy(['id' => $key]);

                $champs  = new Champs();
                $champs->setColumName($input->getChampName());
                $champs->setValue($value);
                $champs->setTableName("donneur_ordre");
                $champs->setChampsId($input);
                $champs->setForm($id);
                $this->em->persist($champs);
                $this->em->flush();
            }
            return true;
        } else {
            return false;
        }
    }
    public function AddContacts($data, $donneur_ordre)
    {
        if ($data) {
            foreach ($data as $contactData) {
                $contact = new ContactDonneurOrdre();
                $contact->setIdDonneurOrdre($donneur_ordre);
                $contact->setNom($contactData['nom']);
                $contact->setPrenom($contactData['prenom']);
                $contact->setAdresse($contactData['adresse']);
                $contact->setEmail($contactData['email']);
                $contact->setPoste($contactData['poste']);
                $contact->setTel($contactData['tel']);
                $contact->setMobile($contactData['mobile']);
                $this->em->persist($contact);
            }
            return true;
        } else return false;
    }

    public function UpdateDonneur($data, $id)
    {
        if ($data && $id) {
            $modelFacturation = $this->em->getRepository(ModelFacturation::class)->findOneBy(['id' => $data["regleSelected"]]);
            $type_donneur = $this->em->getRepository(TypeDonneur::class)->findOneBy(['id' => $data["id_type"]]);

            $donneur = $this->em->getRepository(DonneurOrdre::class)->findOneBy(["id" => $id]);
            $donneur->setNom($data['nom']);
            $donneur->setMetier($data['metier']);
            $donneur->setRaisonSociale($data['rs']);
            $donneur->setNumeroRc($data['num_rc']);
            $donneur->setCp($data['c_postale']);
            $donneur->setCompteBancaire($data['compte_bancaire']);
            $donneur->setDateCreation(new \DateTime());
            $donneur->setDateDebut(new \DateTime());
            $donneur->setDateFin(new \DateTime());
            $donneur->setIdType($type_donneur);
            $donneur->setIdModeleRegle($modelFacturation);
            $this->em->persist($donneur);
            $this->em->flush();
            return true;
        } else return false;
    }

    public function UpdateChamps($data, $id)
    {
        foreach ($data as $key => $value) {
            $input = $this->em->getRepository(Champs::class)->findOneBy(["champs" => (int)$key, "form" => (int)$id]);
            $input->setValue($value);
            $this->em->persist($input);
        }
    }

    public function UpdateContact($id, $nom, $prenom, $poste, $email, $tel, $mobile, $adresse)
    {
        if ($id && $nom && $prenom && $poste && $email && $tel && $mobile && $adresse) {

            $contact = $this->em->getRepository(ContactDonneurOrdre::class)->findOneBy(["id" => (int)$id]);

            $contact->setNom($nom);
            $contact->setPrenom($prenom);
            $contact->setposte($poste);
            $contact->setemail($email);
            $contact->setMobile($tel);
            $contact->settel($mobile);
            $contact->setAdresse($adresse);

            $this->em->persist($contact);
            $this->em->flush();
            return true;
        } else return false;
    }

    public function DeleteDonneurOrdre($id)
    {
        $donneur = $this->em->getRepository(DonneurOrdre::class)->findOneBy(["id" => $id]);
        if (!$donneur) {

            return false;
        } else {
            $contacts = $this->em->getRepository(ContactHistorique::class)->findBy(['idDonneur' => $id]);
            foreach ($contacts as $contact) {
                $this->em->remove($contact);
            }
            $contacts = $this->em->getRepository(ContactDonneurOrdre::class)->findBy(['id_donneurOrdre' => $id]);
            foreach ($contacts as $contact) {
                $this->em->remove($contact);
            }
            $champs = $this->em->getRepository(Champs::class)->findBy(['form' => $id]);
            foreach ($champs as $champ) {
                $this->em->remove($champ);
            }
            $this->em->remove($donneur);
            $this->em->flush();

            return true;
        }
        return false;
    }
    public function DeleteContact($id)
    {
        $contact = $this->em->getRepository(ContactDonneurOrdre::class)->findOneBy(["id" => $id]);

        if (!$contact) {

            return false;
        } else {

            $this->em->remove($contact);
            $this->em->flush();

            return true;
        }
    }

    //  Portefeuille

    public function createPortefeuille($data)
    {
        if ($data) {
            $donneurOrdre = $this->em->getRepository(DonneurOrdre::class)->findOneBy(array("id" => (int)$data['dn']));
            // $id_activity =  $this->em->getRepository(DetailsSecteurActivite::class)->findOneBy(array("id" =>$data['id_activity']));
            $type_creance =  $this->em->getRepository(DetailsTypeCreance::class)->findOneBy(array("id" =>$data['typeCreance']));
            $ptf = new Portefeuille();
            $ptf->setTitre($data['titre']);
            $ptf->setActif(0);
            $ptf->setNumeroPtf($data['numPtf']);
            $ptf->setDureeGestion($data['dureeGestion']);
            $ptf->setDateDebutGestion(new \DateTime($data['dateDebutGestion']));
            $ptf->setDateFinGestion(new \DateTime($data['dateFinGestion']));
            $ptf->setTypeMission($data['typeMission']);
            $ptf->setTypeCreance(json_encode($data['typeCreance']));
            // $ptf->setIdDetailSecteurActivite($id_activity);
            $ptf->setIdDonneurOrdre($donneurOrdre);
            $this->em->persist($ptf);
            $this->em->flush();
            return $ptf;
        } else 
        return false;
    }
    public function majDetails($idPtf){
        $sql="DELETE FROM ptf_type_creance_d WHERE id_ptf_id = ".$idPtf->getId().";
        DELETE FROM regle_portefeuille WHERE id_ptf_id = ".$idPtf->getId().";
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
    }
    public function createPortefeuilleType($idPtf , $type){
        //Delete if already ptf_type
        $sql="DELETE FROM ptf_type_creance_d WHERE id = ".$idPtf->getId()."";
        $stmt = $this->conn->prepare($sql );
        $stmt->execute();
        $ptf = new PtfTypeCreanceD();
        $ptf->setIdPtf($idPtf);
        $ptf->setIdType($type);
        $this->em->persist($ptf);
        $this->em->flush();
    }

    public function AddChampsPortefeuille($data, $id)
    {
        if ($data) {
            foreach ($data as $key => $value) {

                $input = $this->em->getRepository(DetailModelAffichage::class)->findOneBy(['id' => $key]);

                $champs  = new Champs();
                $champs->setColumName($input->getChampName());
                $champs->setValue($value);
                $champs->setTableName("portefeuille");
                $champs->setChampsId($input);
                $champs->setForm($id);
                $this->em->persist($champs);
            }
            return true;
        } else {
            return false;
        }
    }

    public function UpdatePortefeuille($data, $id)
    {
        if ($data && $id) {
            $donneurOrdreExist = $this->em->getRepository(DonneurOrdre::class)->findOneBy(["id" => (int)$data['dn']]);
            $ptf = $this->em->getRepository(Portefeuille::class)->findOneBy(["id" => (int)$id]);
            $ptf->setTitre($data['titre']);
            $ptf->setActif(1);
            $ptf->setNumeroPtf($data['numPtf']);
            $ptf->setDureeGestion($data['dureeGestion']);
            $ptf->setDateDebutGestion(new \DateTime($data['dateDebutGestion']));
            $ptf->setDateFinGestion(new \DateTime($data['dateFinGestion']));
            $ptf->setTypeMission($data['typeMission']);
            $ptf->setTypeCreance(json_encode($data['typeCreance']));
            $ptf->setIdDonneurOrdre($donneurOrdreExist);
            $this->em->persist($ptf);
            $this->em->flush();
            return true;
        } else return false;
    }
    public function UpdateChampsPortefeuille($data, $id)
    {
        foreach ($data as $key => $value) {
            $input = $this->em->getRepository(Champs::class)->findOneBy(["champs" => (int)$key, "form" => (int)$id]);
            $input->setValue($value);
            $this->em->persist($input);
            $this->em->flush();
        }
    }

    public function DeletePortefeuille($id)
    {
        $portefeuille = $this->em->getRepository(Portefeuille::class)->findOneBy(["id" => $id]);
        if (!$portefeuille) {

            return false;
        } else {

            $champs = $this->em->getRepository(Champs::class)->findBy(['form' => $id]);
            foreach ($champs as $champ) {
                $this->em->remove($champ);
            }
            $this->em->remove($portefeuille);
            $this->em->flush();

            return true;
        }
        return false;
    }


   

    public function ValidateChamps($id, $message): bool | string
    {
        $input = $this->em->getRepository(DetailModelAffichage::class)->findOneBy(['id' => $id]);

        if (!$input) {
            return false;
        } else {
            

            if ($input->getTypeChamp() === "text") {
                if (strlen($message) > $input->getLength()) {
                    return false;
                } else return true;
            }

            if ($input->getTypeChamp() === "number") {
                if (is_numeric($message)) {
                    return true;
                } else return false;
            }
            //return $input->getTypeChamp() . "-" . $input->getId();

            if ($input->getTypeChamp() === "date") {

                $date = DateTime::createFromFormat('d-m-Y', $message);
                if ($date !== false) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    public function getListesRoles(){
        $param =  $this->em->getRepository(ListesRoles::class)->findAll();
        
        if($param){
            
            return $param;
        }else{
            return null;
        }
    }
    public function getOnePtf($id){
        $ptf =  $this->em->getRepository(Portefeuille::class)->findOneBy(["id"=>$id]);
        if($ptf){
            
            return $ptf;
        }else{
            return null;
        }
    }
    public function getAllSecteurActivite(){
        $sql="SELECT * from secteur_activite";
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $resulat = $stmt->fetchAll();
        return $resulat;
    }
    
}
