<?php

namespace App\Controller\Facturation;
use App\Entity\CritereModelFacturation;
use App\Entity\Facture;
use App\Entity\ModelFacturation;
use App\Repository\Facturation\facturationRepo;
use App\Service\MessageService;
use App\Service\GeneralService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\RegleModelFacturation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\AuthService;
use App\Service\typeService;

#[Route('/API/facturation')]

class FacturationController extends AbstractController
{
    private  $factureRepo;
    private $AuthService;
    private  $serializer;
    private $MessageService;
    private $GeneralService;
    private $conn;
    private $TypeService;


    public function __construct(
        AuthService $AuthService,
        facturationRepo $factureRepo,
        SerializerInterface $serializer,
        MessageService $MessageService,
        GeneralService $GeneralService,
        Connection $conn ,
        typeService $TypeService,
        EntityManagerInterface $em)
    {
        $this->AuthService = $AuthService;
        $this->factureRepo = $factureRepo;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->MessageService = $MessageService;
        $this->GeneralService = $GeneralService;
        $this->conn = $conn;
        $this->TypeService = $TypeService;
    }
    
    #[Route('/listeFacture')]
    public function listeFacture(Request $request,facturationRepo $factureRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $id = $request->get('id');
            $this->AuthService->checkAuth(0,$request);
            $data = $factureRepo->getListeModels($id);
            $respObjects["data"] = $data;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/addFacture', methods: ['POST'])]
    public function addFacture(facturationRepo $factureRepo , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $donneur = $data_list["donneur_ordre"];
            $IdModel = $data_list["model"];
            $type = $data_list["type"];
            if((!empty($donneur) && $donneur !="" ) && (!empty($type) && $type !="" )  ){
                $donneurOrdre = $factureRepo->getOneDonneurOrdre($donneur);
                if($donneurOrdre){
                    //Get num fact 
                    $date_echeance = $data_list["date_echeance"];
                    
                    $yearF = date_format(new \DateTime(),"Y");
                    $num = $factureRepo->getNumFact();
                    $numeroFact = $num[0]["numero"];
                    if($type == 1){
                        $queryEntities = "App\Entity\Dossier d,App\Entity\Debiteur deb";
                        $queryConditions = " ";
                        $param = array();

                        $regle = $data_list["regle"];

                        for ($j=0; $j < count($regle); $j++) { 

                            $criteres = $this->em->getRepository(CritereModelFacturation::class)->findBy(['idRegle'=>$regle[$j]]);
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
                                if($criteres[$i]->getTableName() == "adresse"){
                                    if($criteres[$i]->getColumnName() =="adresse_complet"){
                                        if($criteres[$i]->getAction() == "like"){
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.adresse_complet LIKE :adresse".$i.")";
                                            $param['adresse'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.adresse_complet NOT LIKE :adresse".$i.")";
                                            $param['adresse'.$i] = $criteres[$i]->getValeur1();
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="cp"){
                                        if($criteres[$i]->getAction() == "like"){
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.cp LIKE :cp".$i.")";
                                            $param['cp'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.cp NOT LIKE :cp".$i.")";
                                            $param['cp'.$i] = $criteres[$i]->getValeur1();
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
                                            if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Adresse a";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.cp is null)";
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
                                            if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Adresse a";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.cp is not null)";
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="pays"){
                                        if($criteres[$i]->getAction() == "like"){
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.pays LIKE :pays".$i.")";
                                            $param['pays'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.pays NOT LIKE :pays".$i.")";
                                            $param['pays'.$i] = $criteres[$i]->getValeur1();
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
                                            if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Adresse a";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.pays is null)";
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
                                            if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Adresse a";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.pays is not null)";
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="ville"){
                                        if($criteres[$i]->getAction() == "like"){
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.ville LIKE :ville".$i.")";
                                            $param['ville'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.ville NOT LIKE :ville".$i.")";
                                            $param['ville'.$i] = $criteres[$i]->getValeur1();
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
                                            if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Adresse a";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.ville is null)";
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
                                            if(strpos($queryEntities,",App\Entity\Adresse a") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Adresse a";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and  a.ville is not null)";
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="status"){
                                        if($criteres[$i]->getAction() == "egal"){
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.status = :status".$i.")";
                                            $param['status'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() == "notegal"){
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.status  !=  :status".$i.")";
                                            $param['status'.$i] = $criteres[$i]->getValeur1();
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="verifier"){
                                        if($criteres[$i]->getAction() == "egal"){
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.verifier = :verifier".$i.")";
                                            $param['verifier'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() == "notegal"){
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.verifier  !=  :verifier".$i.")";
                                            $param['verifier'.$i] = $criteres[$i]->getValeur1();
                                        }
                                    }
                                }

                                if($criteres[$i]->getTableName() == "compte_bancaire"){
                                    if($criteres[$i]->getColumnName() =="banque"){
                                        if($criteres[$i]->getAction() == "like"){
                                            if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                            {
                                                $queryEntities .= ",App\Entity\DebiteurDetail t";
                                            }
                                            if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Debiteur deb";
                                            }
                                            if(strpos($queryEntities,",App\Entity\CompteBancaire cb") == false)
                                            {
                                                $queryEntities .= ",App\Entity\CompteBancaire cb";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and cb.banque LIKE :banque".$i.")";
                                            $param['banque'.$i] = $criteres[$i]->getValeur1();
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="rib"){
                                        if($criteres[$i]->getAction() == "like"){
                                            if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                            {
                                                $queryEntities .= ",App\Entity\DebiteurDetail t";
                                            }
                                            if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Debiteur deb";
                                            }
                                            if(strpos($queryEntities,",App\Entity\CompteBancaire cb") == false)
                                            {
                                                $queryEntities .= ",App\Entity\CompteBancaire cb";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and cb.rib LIKE :rib".$i.")";
                                            $param['banque'.$i] = $criteres[$i]->getValeur1();
                                        }
                                    }
                                    
                                }
                                
                                if($criteres[$i]->getTableName() == "charge"){
                                    if($criteres[$i]->getColumnName() =="charge"){
                                        if($criteres[$i]->getAction() == "like"){
                                            if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                            {
                                                $queryEntities .= ",App\Entity\DebiteurDetail t";
                                            }
                                            if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Debiteur deb";
                                            }
                                            if(strpos($queryEntities,",App\Entity\Charge tc") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Charge tc";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and identity(tc.id_type_charge) LIKE :typeCaharge".$i.")";
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(a.id_debiteur) and a.verifier=1 and a.adresse_complet NOT LIKE :adresse".$i.")";
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
                                            
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.numero_creance LIKE :numero_creance".$i.")";
                                            $param['numero_creance'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() == "notlike"){
                                            if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Creance c";
                                            }
                                            
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.numero_creance NOT LIKE :numero_creance".$i.")";
                                            $param['numero_creance'.$i] = $criteres[$i]->getValeur1();
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="total_ttc_initial"){
                                        if($criteres[$i]->getAction() == "egal"){
                                            if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Creance c";
                                            }
                                            
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.total_ttc_initial = :total_ttc_initial".$i.")";
                                            $param['total_ttc_initial'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() == "supOuEgal"){
                                            if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Creance c";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.total_ttc_initial >= :total_ttc_initial".$i.")";
                                            $param['total_ttc_initial'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() == "infOuEgal"){
                                            if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Creance c";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.total_ttc_initial <= :total_ttc_initial".$i.")";
                                            $param['total_ttc_initial'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() =="entre"){
                                            if(strpos($queryEntities,",UtilisateursBundle:creance c") == false)
                                            {
                                                $queryEntities .= ",UtilisateursBundle:creance c";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.totalTtcInitial BETWEEN ".$criteres[$i]->getValeur1()." AND ".$criteres[$i]->getValeur2()." )";
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="total_ttc_restant"){
                                        if($criteres[$i]->getAction() == "egal"){
                                            if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Creance c";
                                            }
                                            
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.total_ttc_restant = :total_ttc_restant".$i.")";
                                            $param['total_ttc_restant'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() == "supOuEgal"){
                                            if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Creance c";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.total_ttc_restant >= :total_ttc_restant".$i.")";
                                            $param['total_ttc_restant'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() == "infOuEgal"){
                                            if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Creance c";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.total_ttc_restant <= :total_ttc_restant".$i.")";
                                            $param['total_ttc_restant'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() =="entre"){
                                            if(strpos($queryEntities,",UtilisateursBundle:creance c") == false)
                                            {
                                                $queryEntities .= ",UtilisateursBundle:creance c";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.total_ttc_restant BETWEEN ".$criteres[$i]->getValeur1()." AND ".$criteres[$i]->getValeur2()." )";
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.date_echeance BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                            $param['dateFinStart'.$i] = $start;
                                            $param['dateFinEnd'.$i] = $end;
                                        }
                                        if($criteres[$i]->getAction() == "supOuEgal"){
    
                                            if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Creance c";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.date_echeance > :dateFinStart".$i.")";
                                            $param['dateFinStart'.$i] = $start;
                                        }
                                        if($criteres[$i]->getAction() == "infOuEgal"){
    
                                            if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Creance c";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.date_echeance < :dateFinStart".$i.")";
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.date_echeance BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
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
                                            
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.total_creance = :total_creance".$i.")";
                                            $param['total_creance'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() == "supOuEgal"){
                                            if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Creance c";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.total_creance >= :total_creance".$i.")";
                                            $param['total_creance'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() == "infOuEgal"){
                                            if(strpos($queryEntities,",App\Entity\Creance c") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Creance c";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.total_creance <= :total_creance".$i.")";
                                            $param['total_creance'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() =="entre"){
                                            if(strpos($queryEntities,",UtilisateursBundle:creance c") == false)
                                            {
                                                $queryEntities .= ",UtilisateursBundle:creance c";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and c.total_creance BETWEEN ".$criteres[$i]->getValeur1()." AND ".$criteres[$i]->getValeur2()." )";
                                            $param['total_creance'.$i] = $criteres[$i]->getValeur1();
                                        }
                                    }
                                }
                                if($criteres[$i]->getTableName() == "debiteur"){
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.civilite LIKE :civilite".$i.")";
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.civilite NOT LIKE :civilite".$i.")";
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.civilite is null)";
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.civilite is not null)";
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="cin"){
                                        if($criteres[$i]->getAction() == "like"){
                                            if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                            {
                                                $queryEntities .= ",App\Entity\DebiteurDetail t";
                                            }
                                            if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Debiteur deb";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.cin LIKE :cin".$i.")";
                                            $param['cin'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.cin NOT LIKE :cin".$i.")";
                                            $param['cin'.$i] = $criteres[$i]->getValeur1();
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="nom"){
                                        if($criteres[$i]->getAction() == "like"){
                                            if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                            {
                                                $queryEntities .= ",App\Entity\DebiteurDetail t";
                                            }
                                            if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Debiteur deb";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(t.id_debiteur) and  deb.nom LIKE :nom".$i.")";
                                            $param['nom'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.nom NOT LIKE :nom".$i.")";
                                            $param['nom'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.nom is null)";
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.nom is not null)";
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="prenom"){
                                        if($criteres[$i]->getAction() == "like"){
                                            if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                            {
                                                $queryEntities .= ",App\Entity\DebiteurDetail t";
                                            }
                                            if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Debiteur deb";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.prenom LIKE :prenom".$i.")";
                                            $param['prenom'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.prenom NOT LIKE :prenom".$i.")";
                                            $param['prenom'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.prenom is null)";
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.prenom is not null)";
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="raison_social"){
                                        if($criteres[$i]->getAction() == "like"){
                                            if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                            {
                                                $queryEntities .= ",App\Entity\DebiteurDetail t";
                                            }
                                            if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Debiteur deb";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.raison_social LIKE :raison_social".$i.")";
                                            $param['raison_social'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.raison_social NOT LIKE :raison_social".$i.")";
                                            $param['raison_social'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.raison_social is null)";
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.raison_social is not null)";
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="fax"){
                                        if($criteres[$i]->getAction() == "like"){
                                            if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                            {
                                                $queryEntities .= ",App\Entity\DebiteurDetail t";
                                            }
                                            if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Debiteur deb";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.fax LIKE :fax".$i.")";
                                            $param['fax'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.fax NOT LIKE :fax".$i.")";
                                            $param['fax'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.fax is null)";
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.fax is not null)";
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="date_naissance"){
                                        if($criteres[$i]->getAction() == "egal"){

                                            $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                            $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur1());

                                            if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                            {
                                                $queryEntities .= ",App\Entity\DebiteurDetail t";
                                            }
                                            if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Debiteur deb";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.date_naissance BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                            $param['dateFinStart'.$i] = $start;
                                            $param['dateFinEnd'.$i] = $end;
                                        }
                                        if($criteres[$i]->getAction() == "supOuEgal"){

                                            $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                            $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());

                                            if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                            {
                                                $queryEntities .= ",App\Entity\DebiteurDetail t";
                                            }
                                            if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Debiteur deb";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.date_naissance > :dateFinStart".$i.")";
                                            $param['dateFinStart'.$i] = $start;
                                        }
                                        if($criteres[$i]->getAction() == "infOuEgal"){
                                            if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                            {
                                                $queryEntities .= ",App\Entity\DebiteurDetail t";
                                            }
                                            if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Debiteur deb";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.date_naissance < :dateFinStart".$i.")";
                                            $param['dateFinStart'.$i] = $start;
                                        }
                                        if($criteres[$i]->getAction() == "entre"){
                                            $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                            $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());
                                            if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                            {
                                                $queryEntities .= ",App\Entity\DebiteurDetail t";
                                            }
                                            if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Debiteur deb";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.date_naissance BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                            $param['dateFinStart'.$i] = $start;
                                            $param['dateFinEnd'.$i] = $end;
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="lieu_naissance"){
                                        if($criteres[$i]->getAction() == "like"){
                                            if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                            {
                                                $queryEntities .= ",App\Entity\DebiteurDetail t";
                                            }
                                            if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Debiteur deb";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.lieu_naissance LIKE :lieu_naissance".$i.")";
                                            $param['lieu_naissance'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.lieu_naissance NOT LIKE :lieu_naissance".$i.")";
                                            $param['lieu_naissance'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.lieu_naissance is null)";
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.lieu_naissance is not null)";
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="email"){
                                        if($criteres[$i]->getAction() == "like"){
                                            if(strpos($queryEntities,",App\Entity\DebiteurDetail t") == false)
                                            {
                                                $queryEntities .= ",App\Entity\DebiteurDetail t";
                                            }
                                            if(strpos($queryEntities,",App\Entity\Debiteur deb") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Debiteur deb";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.email LIKE :email".$i.")";
                                            $param['email'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(t.id_dossier) and identity(t.id_debiteur)=deb.id and deb.id=identity(tc.id_debiteur) and  deb.email NOT LIKE :email".$i.")";
                                            $param['email'.$i] = $criteres[$i]->getValeur1();
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
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.numeroPtf LIKE :numeroPtf".$i.")";
                                            $param['numeroPtf'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() == "notlike"){

                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.numeroPtf NOT LIKE :numeroPtf".$i.")";
                                            $param['numeroPtf'.$i] = $criteres[$i]->getValeur1();
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="titre"){
                                        if($criteres[$i]->getAction() == "like"){
                                            if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Portefeuille ptf";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.titre LIKE :titre".$i.")";
                                            $param['titre'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() == "notlike"){

                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.titre NOT LIKE :titre".$i.")";
                                            $param['titre'.$i] = $criteres[$i]->getValeur1();
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="type_creance"){
                                        if($criteres[$i]->getAction() == "like"){
                                            if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Portefeuille ptf";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.type_creance LIKE :type_creance".$i.")";
                                            $param['type_creance'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() == "notlike"){

                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.type_creance NOT LIKE :type_creance".$i.")";
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
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.date_debut_gestion BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                            $param['dateFinStart'.$i] = $start;
                                            $param['dateFinEnd'.$i] = $end;
                                        }
                                        if($criteres[$i]->getAction() == "supOuEgal"){
    
                                            if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Portefeuille ptf";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.date_debut_gestion > :dateFinStart".$i.")";
                                            $param['dateFinStart'.$i] = $start;
                                            $param['dateFinEnd'.$i] = $end;
                                        }
                                        if($criteres[$i]->getAction() == "infOuEgal"){
    
                                            if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Portefeuille ptf";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.date_debut_gestion < :dateFinStart".$i.")";
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
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.date_debut_gestion BETWEEN :dateFinStart".$i." and :dateFinEnd".$i." )";
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
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.date_debut_gestion is null)";
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
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.date_debut_gestion is not null)";
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
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.date_fin_gestion BETWEEN :dateFinStart".$i." and :dateFinEnd".$i.")";
                                            $param['dateFinStart'.$i] = $start;
                                            $param['dateFinEnd'.$i] = $end;
                                        }
                                        if($criteres[$i]->getAction() == "supOuEgal"){
    
                                            if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Portefeuille ptf";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.date_fin_gestion > :dateFinStart".$i.")";
                                            $param['dateFinStart'.$i] = $start;
                                            $param['dateFinEnd'.$i] = $end;
                                        }
                                        if($criteres[$i]->getAction() == "infOuEgal"){
    
                                            if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Portefeuille ptf";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.date_fin_gestion < :dateFinStart".$i.")";
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
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.date_fin_gestion BETWEEN :dateFinStart".$i." and :dateFinEnd".$i." )";
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
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.date_fin_gestion is null)";

                                        }
                                        if($criteres[$i]->getAction() == "notnull"){
                                            $start = $this->GeneralService->dateStart($criteres[$i]->getValeur1());
                                            $end = $this->GeneralService->dateEnd($criteres[$i]->getValeur2());
                                            if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Portefeuille ptf";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.date_fin_gestion is not null)";                   
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="duree_gestion"){
                                        if($criteres[$i]->getAction() == "like"){
                                            if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Portefeuille ptf";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.duree_gestion LIKE :duree_gestion".$i.")";
                                            $param['duree_gestion'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() == "notlike"){

                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.duree_gestion NOT LIKE :duree_gestion".$i.")";
                                            $param['duree_gestion'.$i] = $criteres[$i]->getValeur1();
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="actif"){
                                        if($criteres[$i]->getAction() == "like"){
                                            if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Portefeuille ptf";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.actif = :actif".$i.")";
                                            $param['actif'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() == "notlike"){
                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.actif != :actif".$i.")";
                                            $param['actif'.$i] = $criteres[$i]->getValeur1();
                                        }
                                    }
                                    if($criteres[$i]->getColumnName() =="type_mission"){
                                        if($criteres[$i]->getAction() == "like"){
                                            if(strpos($queryEntities,",App\Entity\Portefeuille ptf") == false)
                                            {
                                                $queryEntities .= ",App\Entity\Portefeuille ptf";
                                            }
                                            $queryConditions .= " ".$operateur[$i]."( ptf.id=identity(d.id_ptf) and ptf.type_mission LIKE :type_mission".$i.")";
                                            $param['type_mission'.$i] = $criteres[$i]->getValeur1();
                                        }
                                        if($criteres[$i]->getAction() == "notlike"){

                                            $queryConditions .= " ".$operateur[$i]."(d.id=identity(c.id_dossier) and d.type_mission NOT LIKE :type_mission".$i.")";
                                            $param['type_mission'.$i] = $criteres[$i]->getValeur1();
                                        }
                                    }
                                }
                                //Il reste type of paramétrages
                            }
                        }

                        if($queryConditions != " "){
                            $query = $this->em->createQuery("SELECT DISTINCT d.id  FROM  ". $queryEntities . " where " . $queryConditions );
                            $query->setParameters($param);
                            $result = $query->getResult(); 
                            if($result){

                                $total_creance = 0;
                                $total_ttc_initial = 0;
                                $total_ttc_restant = 0;

                                foreach ($result as $key => $value) {
                                    # code...
                                    $id_dossier = $value["id"];
                                    $sql = "SELECT c.id , c.total_ttc_initial , c.total_ttc_restant ,c.total_creance FROM `creance` c where c.id_dossier_id = :id;";
                                    $stmt = $this->conn->prepare($sql);
                                    $stmt->bindParam('id', $id_dossier);
                                    $stmt = $stmt->executeQuery();
                                    $list_creance = $stmt->fetchAll();
                                    $total_creance += $list_creance[0]["total_creance"];
                                    $total_ttc_initial += $list_creance[0]["total_ttc_initial"];
                                    $total_ttc_restant += $list_creance[0]["total_ttc_restant"];
                                    
                                }
                                // $respObjects["data"] = $result;  
                                $model =  $this->em->getRepository(ModelFacturation::class)->findOneBy(['id'=>$IdModel]);
                                $facture = $factureRepo->createFacture($donneurOrdre , $numeroFact , $yearF , $date_echeance,$type , $total_creance ,$total_ttc_initial ,$total_ttc_restant,$model);
                                for ($k=0; $k < count($regle) ; $k++) { 
                                    # code...
                                    $regle_entity = $this->em->getRepository(RegleModelFacturation::class)->findOneBy(['id'=>$regle[$k]]);
                                    $factureRepo->createDetailFacture($facture,$regle_entity);
                                }
                                $respObjects["data"]["facture"] = $facture;
                                $respObjects["data"]["type_facture"] = 1;  
                                $respObjects["data"]["total_creance"] = $total_creance;  
                                $respObjects["data"]["total_ttc_initial"] = $total_ttc_initial;  
                                $respObjects["data"]["total_ttc_restant"] = $total_ttc_restant; 

                                $codeStatut="OK";
                            }else{
                                $codeStatut="AUCUN_DOSSIER";
                            }
                        }else{
                            $codeStatut="EMPTY_REGLE";
                        }
                    }
                }else{
                    $codeStatut="NOT_EXIST_D";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
        $respObjects["ee"] = $e->getMessage();
        } 
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/listeProducts')]
    public function listeProducts(Request $request,facturationRepo $factureRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = $factureRepo->getListeProducts();
            $respObjects["data"] = $data;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getOneProduct')]
    public function getOneProduct(Request $request,facturationRepo $factureRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $data = $factureRepo->getOneProduct($id);
            $respObjects["data"] = $data;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/listeTypePaiement')]
    public function listeTypePaiement(Request $request,facturationRepo $factureRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get("id");
            $data = $this->TypeService->getListeType("paiement");
            $respObjects["data"] = $data;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    #[Route('/saveFacture', methods: ['POST'])]
    public function saveFacture(facturationRepo $factureRepo , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $donneur = $data_list["donneur"];
            $objet = $data_list["objet"];
            $model = $data_list["model"];
            $type_paiement = $data_list["type_paiement"];
            if((!empty($donneur) && $donneur !="" ) && (!empty($type_paiement) && $type_paiement !="" ) && (!empty($objet) && $objet !="" )  ){
                $donneurOrdre = $factureRepo->getOneDonneurOrdre($donneur);
                if($donneurOrdre){
                    $products = $data_list["products"] ;
                    if(count($products) >= 1)
                    {
                        $yearF = date_format(new \DateTime(),"Y");
                        $num = $factureRepo->getNumFact();
                        $numeroFact = $num[0]["numero"];
                        $totalTTC = $data_list["totalTTC"];
                        $donneurOrdre = $factureRepo->createFacture2($donneurOrdre->getId() ,$numeroFact , $yearF , $totalTTC , $type_paiement , $model);
                        $codeStatut="OK";

                    }else{
                        $codeStatut="ERROR-EMPTY-PARAMS";
                    }
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
}
