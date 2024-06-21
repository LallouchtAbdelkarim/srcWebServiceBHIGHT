<?php

namespace App\Repository\IntegrationExtraction\Integration;

use App\Entity\CorresColu;
use App\Entity\DetailModelAffichage;
use App\Entity\ImportType;
use App\Entity\Integration;
use App\Entity\ModelExport;
use App\Entity\ModelImport;
use App\Entity\Import;

use App\Entity\TypeAdresse;
use App\Entity\TypeTel;
use App\Entity\ColumnsParams;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Proxies\__CG__\App\Entity\ProcessIntegration;

class integrationRepo extends ServiceEntityRepository
{
    private $conn;
    public $em;

    public function __construct(Connection $conn , EntityManagerInterface $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }
    public function getColumnCreance(){
        $sql="SHOW COLUMNS FROM creance WHERE `Key` != 'MUL'  and Field != 'id';"; 
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $list_columns = $stmt->fetchAllAssociative();
        $sql="select * from detail_model_affichage where table_name = 'creance'"; 
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $inputs = $stmt->fetchAllAssociative();

        $array_culomn = array();
        for ($i=0; $i < count($inputs); $i++) { 
            $array_culomn[$i]["Field"] = $inputs[$i]["champ_name"];
            if($inputs[$i]["type_champ"] == "Number"){
                $array_culomn[$i]["Type"] = "int(".$inputs[$i]["length"].")" ;
            }elseif ($inputs[$i]["type_champ"] == "Text") {
                $array_culomn[$i]["Type"] = "varchar(".$inputs[$i]["length"].")" ;
            }   
        }
        $mergedArray = array_merge($list_columns, $array_culomn);
        if($mergedArray){
            return $mergedArray;
        }else{
            return null;
        }
    }
    public function getColumnDonneurOrdre(){
        $sql="SHOW COLUMNS FROM donneur_ordre WHERE `Key` != 'MUL'  and Field != 'id';"; 
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $list_columns = $stmt->fetchAllAssociative();
        $sql="select * from detail_model_affichage where table_name = 'donneur_ordre'"; 
        $stmt = $this->conn->prepare($sql);
        $stmt = $stmt->executeQuery();
        $inputs = $stmt->fetchAllAssociative();

        $array_culomn = array();
        for ($i=0; $i < count($inputs); $i++) { 
            $array_culomn[$i]["Field"] = $inputs[$i]["champ_name"];
            if($inputs[$i]["type_champ"] == "Number"){
                $array_culomn[$i]["Type"] = "int(".$inputs[$i]["length"].")" ;
            }elseif ($inputs[$i]["type_champ"] == "Text") {
                $array_culomn[$i]["Type"] = "varchar(".$inputs[$i]["length"].")" ;
            }   
        }
        $mergedArray = array_merge($list_columns, $array_culomn);
        if($mergedArray){
            return $mergedArray;
        }else{
            return null;
        }
    }

    

    public function getColumnByTable($table){
        if(!empty($table)){
            // $table = "creance";
            $sql = "SHOW COLUMNS FROM ".$table." WHERE `Key` != 'MUL' and Field != 'id';";
            $stmt = $this->conn->prepare($sql);
            $stmt = $stmt->executeQuery();
            $list_columns = $stmt->fetchAllAssociative();
            $sql = "SELECT * FROM detail_model_affichage WHERE table_name = :table";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":table", $table);
            $stmt = $stmt->executeQuery();
            $inputs = $stmt->fetchAllAssociative();
            
            $array_culomn = array();
            for ($i=0; $i < count($inputs); $i++) { 
                $array_culomn[$i]["Field"] = $inputs[$i]["champ_name"];
                if($inputs[$i]["type_champ"] == "Number"){
                    $array_culomn[$i]["Type"] = "int(".$inputs[$i]["length"].")" ;
                }elseif ($inputs[$i]["type_champ"] == "Text") {
                    $array_culomn[$i]["Type"] = "varchar(".$inputs[$i]["length"].")" ;
                }   
            }
            $mergedArray = array_merge($list_columns, $array_culomn);

            if($mergedArray){
                return $mergedArray;
            }else{
                return null;
            }
        }else{
            return null;
        }
    }
    public function getDataCorres($id){
        $data_corres = array();
        $sql = "SELECT * FROM `corres_colu` WHERE id_model_import_id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt = $stmt->executeQuery();
        $inputs = $stmt->fetchAllAssociative();
        
        for ($i=0; $i < count($inputs) ; $i++) { 
            # code...
            $data_corres[$i]["corres"] = $inputs[$i];
            $sql = "SELECT * FROM `columns_params` WHERE `id` = :id_param";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id_param", $inputs[$i]["id_col_params_id"]);
            $stmt = $stmt->executeQuery();
            $data_params = $stmt->fetchAllAssociative();

            $data_corres[$i]["corres"]["corres_param"] = $data_params;
        }
        return $data_corres;
    }

    public function findModel($id){
        $model = $this->em->getRepository(ModelImport::class)->findOneBy(["id"=>$id]);
        if($model){
            return $model;
        }else{
            return null;
        }
    }
    public function getStautsId($id){
        $model = $this->em->getRepository(ProcessIntegration::class)->findOneBy(["id"=>$id]);
        if($model){
            return $model;
        }else{
            return null;
        }
    }
    public function resetImport($id){
        $sql = 'UPDATE `import` SET id_integration_id = null where id_integration_id = :id';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->execute();
    }
    public function findModelByTitle($titre){
        $model = $this->em->getRepository(ModelImport::class)->findOneBy(["titre"=>$titre]);
        if($model){
            return $model;
        }else{
            return null;
        }
    }
    public function getAllColumnsParams($type){
        switch ($type) {
            case 'debiteur':
                $query = $this->em->createQuery('SELECT t.table_bdd, t.id, t.titre_col FROM App\Entity\ColumnsParams t WHERE t.table_bdd = :tb1 OR ( t.table_bdd = :tb3 and t.titre_col = :numero_dossier) ')
                    ->setParameters([
                        'tb1' => 'debiteur',
                        'numero_dossier' => 'numero_dossier',
                        'tb3' => 'dossier',
                    ]);
                $tables = $query->getResult();
                return $tables ?: null;
            case 'dossier':
                $query = $this->em->createQuery('SELECT t.table_bdd, t.id, t.titre_col FROM App\Entity\ColumnsParams t WHERE t.table_bdd = :tb1  OR ( t.table_bdd = :tb3 and t.titre_col = :id_debiteur)  ')
                    ->setParameters([
                        'tb1' => 'dossier',
                        'tb3' => 'debiteur',
                        'id_debiteur' => 'id_debiteur'
                    ]);
                $tables = $query->getResult();
                return $tables ?: null;
            case 'creance':
                $query = $this->em->createQuery('SELECT t.table_bdd, t.id, t.titre_col FROM App\Entity\ColumnsParams t WHERE t.table_bdd = :tb1 OR t.table_bdd = :tb2 OR ( t.table_bdd = :tb3 and (t.titre_col = :cin)) OR  (t.table_bdd = :tb4 and (t.titre_col = :numero_dossier))')
                    ->setParameters([
                        'tb1' => 'creance',
                        'tb2' => 'detail_creance',
                        'tb3' => 'debiteur',
                        'tb4'=>'dossier',
                        'cin' => 'id_debiteur',
                        'numero_dossier'=>'numero_dossier',
                        // 'type'=>'type_debiteur'
                    ]);
                $tables = $query->getResult();
                return $tables ?: null;
            case 'garantie':
                $query = $this->em->createQuery('SELECT t.table_bdd, t.id, t.titre_col FROM App\Entity\ColumnsParams t WHERE t.table_bdd = :tb1 OR ( t.table_bdd = :tb3 and t.titre_col = :cin) OR ( t.table_bdd = :tb4 and t.titre_col = :numero_creance)')
                    ->setParameters([
                        'tb1' => 'garantie',
                        'tb3' => 'debiteur',
                        'cin' => 'id_debiteur',
                        'tb4' => 'creance',
                        'numero_creance'=>'numero_creance'
                    ]);
                $tables = $query->getResult();
                return $tables ?: null;
            case 'procedure_judicaire':
                $query = $this->em->createQuery('SELECT t.table_bdd, t.id, t.titre_col FROM App\Entity\ColumnsParams t WHERE t.table_bdd = :tb1 OR ( t.table_bdd = :tb3 and t.titre_col = :cin) OR ( t.table_bdd = :tb4 and t.titre_col = :numero_creance)')
                    ->setParameters([
                        'tb1' => 'proc_judicaire',
                        'tb3' => 'debiteur',
                        'cin' => 'id_debiteur',
                        'tb4' => 'creance',
                        'numero_creance'=>'numero_creance'
                    ]);
                $tables = $query->getResult();
                return $tables ?: null;
            case 'adresse':
                $query = $this->em->createQuery('SELECT t.table_bdd, t.id, t.titre_col FROM App\Entity\ColumnsParams t WHERE t.table_bdd = :tb1 OR ( t.table_bdd = :tb3 and t.titre_col = :cin)')
                    ->setParameters([
                        'tb1' => 'adresse',
                        'tb3' => 'debiteur',
                        'cin' => 'id_debiteur',
                    ]);
                $tables = $query->getResult();
                return $tables ?: null;
            case 'email':
                $query = $this->em->createQuery('SELECT t.table_bdd, t.id, t.titre_col FROM App\Entity\ColumnsParams t WHERE t.table_bdd = :tb1 OR ( t.table_bdd = :tb3 and t.titre_col = :cin)')
                    ->setParameters([
                        'tb1' => 'email',
                        'tb3' => 'debiteur',
                        'cin' => 'id_debiteur',
                    ]);
                $tables = $query->getResult();
                return $tables ?: null;
            case 'telephone':
                $query = $this->em->createQuery('SELECT t.table_bdd, t.id, t.titre_col FROM App\Entity\ColumnsParams t WHERE t.table_bdd = :tb1 OR ( t.table_bdd = :tb3 and t.titre_col = :cin)')
                    ->setParameters([
                        'tb1' => 'telephone',
                        'tb3' => 'debiteur',
                        'cin' => 'id_debiteur',
                    ]);
                $tables = $query->getResult();
                return $tables ?: null;
            case 'emploi':
                $query = $this->em->createQuery('SELECT t.table_bdd, t.id, t.titre_col FROM App\Entity\ColumnsParams t WHERE t.table_bdd = :tb1 OR ( t.table_bdd = :tb3 and t.titre_col = :cin)')
                    ->setParameters([
                        'tb1' => 'emploi',
                        'tb3' => 'debiteur',
                        'cin' => 'id_debiteur',
                    ]);
                $tables = $query->getResult();
                return $tables ?: null;
            case 'employeur':
                $query = $this->em->createQuery('SELECT t.table_bdd, t.id, t.titre_col FROM App\Entity\ColumnsParams t WHERE t.table_bdd = :tb1 OR ( t.table_bdd = :tb3 and t.titre_col = :cin)')
                    ->setParameters([
                        'tb1' => 'employeur',
                        'tb3' => 'debiteur',
                        'cin' => 'id_debiteur',
                    ]);
                $tables = $query->getResult();
                return $tables ?: null;
            default:
                return null;
        }    
    }

    public function getAllColumnsParams2($type){
        switch ($type) {
            case 'adresse':
                $query = $this->em->createQuery('SELECT t.table_bdd, t.id, t.titre_col FROM App\Entity\ColumnsParams t WHERE t.table_bdd = :tb1 OR ( t.table_bdd = :tb3 and t.titre_col = :cin)')
                    ->setParameters([
                        'tb1' => 'adresse',
                        'tb3' => 'debiteur',
                        'cin' => 'cin',
                    ]);
                $tables = $query->getResult();
                return $tables ?: null;
            case 'telephone':
                $query = $this->em->createQuery('SELECT t.table_bdd, t.id, t.titre_col FROM App\Entity\ColumnsParams t WHERE t.table_bdd = :tb1 OR ( t.table_bdd = :tb3 and t.titre_col = :cin)')
                    ->setParameters([
                        'tb1' => 'telephone',
                        'tb3' => 'debiteur',
                        'cin' => 'cin',
                    ]);
                $tables = $query->getResult();
                return $tables ?: null;
            default:
                return null;
        }    
    }
    public function getAllModels(){
        $resultList = $this->em->getRepository(ModelImport::class)->findBy([],['id' => 'DESC'] );
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }

    
    function testDebiteurCle($cle){
        $array = array();
        $sql="SELECT d.id FROM `debt_force_integration`.`debiteur_dbi` d where d.cle_identifiant = :cle";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":cle",$cle);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            $array["exist"]=true;
            $array["place"]=2;
            $array["deb"]=$data;
        }else{
            $sql="SELECT d.id FROM `debiteur` d where d.cle_identifiant = :cle";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":cle",$cle);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchOne();
            if($data){
                $array["exist"]=true;
                $array["place"]=1;
                $array["deb"]=$data;
            }else{
                $array["exist"]=false;
            }
        }
        return $array;
    }
    function testDebiteurIdDebCle($id_deb,$cle,$id_integration){
        $array = array();
        $sql="SELECT d.id FROM `debt_force_integration`.`debiteur_dbi` d where d.id_debiteur = :id_deb and d.id_integration = :id_integration";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_deb",$id_deb);
        $stmt->bindValue(":id_integration",$id_integration);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            $array["exist"]=true;
            $array["place"]=2;
            $array["deb"]=$data;
        }else{
            $sql="SELECT d.id FROM `debiteur` d where d.cle_identifiant = :cle";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":cle",$cle);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchOne();
            if($data){
                $array["exist"]=true;
                $array["place"]=1;
                $array["deb"]=$data;
            }else{
                $array["exist"]=false;
            }
        }
        return $array;
    }
    function testDebiteurIdDeb($id_deb,$id_integration){
        $array = array();
        $sql="SELECT d.id FROM `debt_force_integration`.`debiteur_dbi` d where d.id_debiteur = :id_deb and d.id_integration = :id_integration";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_deb",$id_deb);
        $stmt->bindValue(":id_integration",$id_integration);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            $array["exist"]=true;
            $array["place"]=2;
            $array["deb"]=$data;
        }else{
            $array["exist"]=false;
        }
        return $array;
    }
    public function isCinDeb($id){
        $sql="SELECT t.id from corres_colu t WHERE t.id_model_import_id = :id and id_col_params_id = 23";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        return $data;
    }
    public function getAllInegrationByEtat($etat){
        $resultList = $this->em->getRepository(Integration::class)->findBy(["etat"=>$etat]);
        if($resultList){
            return $resultList; 
        }else{
            return null;
        }
    }
    public function getAllInegrationByStatus($etat){
        $resultList = $this->em->getRepository(Integration::class)->findBy(["status"=>$etat]);
        if($resultList){
            return $resultList; 
        }else{
            return null;
        }
    }
    public function getAllInegrationByStatus2(){
        $resultList = $this->em->getRepository(Integration::class)->findBy(["status" => [1, 2] , "isMaj"=>0 ,"type"=>1]);
        if($resultList){
            return $resultList; 
        }else{
            return null;
        }
    }
    public function getAllInegrationByStatus6(){
        $resultList = $this->em->getRepository(Integration::class)->findBy(["status" => [5, 6]]);
        if($resultList){
            return $resultList; 
        }else{
            return null;
        }
    }
    
    public function getAllInegration(){
        // $resultList = $this->em->getRepository(Integration::class)->findAll();
        $sql="SELECT t.* from integration t WHERE t.status_id != 15 ORDER BY t.id DESC";
        $resultList = $this->conn->fetchAllAssociative($sql);
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getListePtf(){
        $query = $this->em->createQuery('SELECT t from App\Entity\Portefeuille t');
        $tables = $query->getResult();

        if($tables){
            return $tables;
        }else{
            return null;
        }
    }
    
    public function getModelsByType($type){
        $resultList = $this->em->getRepository(ModelImport::class)->findBy(["type"=>$type]);
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function findIntegration($id){
        $resultList = $this->em->getRepository(Integration::class)->findOneBy(["id"=>$id]);
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }

    public function getOneImportByType($id , $type){
        // $resultList = $this->em->getRepository(ModelImport::class)->findOneBy(["id_integration"=>$Id , "type"=> $type]);
        $query = $this->em->createQuery('SELECT r FROM App\Entity\Import r WHERE r.id_integration = :id AND r.id_model IN (SELECT m.id FROM App\Entity\ModelImport m WHERE m.type = :type)')
        ->setParameters([
            'id' => $id,
            'type' => $type // Replace with the actual value for m.type you want to use in the subquery.
        ])
        ->setMaxResults(1);
        $resultList = $query->getOneOrNullResult();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getOneIntegration($id ){
        // $resultList = $this->em->getRepository(ModelImport::class)->findOneBy(["id_integration"=>$Id , "type"=> $type]);
        $query = $this->em->createQuery('SELECT r FROM App\Entity\Integration r where r.id = :id')
        ->setParameters([
            'id' => $id
        ])
        ->setMaxResults(1);
        $resultList = $query->getOneOrNullResult();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getAllImportByIntegration($id ){
        $sql="SELECT *  FROM `import`  where id_integration_id = :id_integration";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_integration",$id);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchAll();
        if($data){
            return $data;
        }else{
            return null;
        }
    }
    public function getOneImportByOrder($id , $order){
        // $resultList = $this->em->getRepository(ModelImport::class)->findOneBy(["id_integration"=>$Id , "type"=> $type]);
        $query = $this->em->createQuery('SELECT r FROM App\Entity\Import r WHERE r.id_integration = :id and r.order_import =:order')
        ->setParameters([
            'id' => $id,
            'order' => $order // Replace with the actual value for m.type you want to use in the subquery.
        ])
        ->setMaxResults(1);
        $resultList = $query->getOneOrNullResult();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    
    public function getOneImportType($id , $type){
        // $resultList = $this->em->getRepository(ModelImport::class)->findOneBy(["id_integration"=>$Id , "type"=> $type]);
        $query = $this->em->createQuery('SELECT r FROM App\Entity\Import r WHERE r.id_integration = :id and r.type =:type')
        ->setParameters([
            'id' => $id,
            'type' => $type // Replace with the actual value for m.type you want to use in the subquery.
        ])
        ->setMaxResults(1);
        $resultList = $query->getOneOrNullResult();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    
    public function findTypeAdresse($id){
        // $resultList = $this->em->getRepository(ModelImport::class)->findOneBy(["id_integration"=>$Id , "type"=> $type]);
        $query = $this->em->createQuery('SELECT r FROM App\Entity\TypeAdresse r WHERE r.id = :id')
        ->setParameters([
            'id' => $id // Replace with the actual value for m.type you want to use in the subquery.
        ])
        ->setMaxResults(1);
        $resultList = $query->getOneOrNullResult();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }
    public function getDetailsImprt($id , $ordre){
        // $resultList = $this->em->getRepository(ModelImport::class)->findOneBy(["id_integration"=>$Id , "type"=> $type]);
        $query = $this->em->createQuery('SELECT r FROM App\Entity\DetailsImport r WHERE r.id_import = :id and r.ordre =:ordre')
        ->setParameters([
            'id' => $id,
            'ordre' => $ordre // Replace with the actual value for m.type you want to use in the subquery.
        ])
        ->setMaxResults(1);
        $resultList = $query->getOneOrNullResult();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }

    public function getOneImportByOrder2($id , $order){
        // $resultList = $this->em->getRepository(ModelImport::class)->findOneBy(["id_integration"=>$Id , "type"=> $type]);
        $query = $this->em->createQuery('SELECT r FROM App\Entity\Import r WHERE r.id_integration = :id and r.order_import =:order and r.id in (select t.id from App\Entity\Integration t where t.etat= 2 )')
        ->setParameters([
            'id' => $id,
            'order' => $order // Replace with the actual value for m.type you want to use in the subquery.
        ])
        ->setMaxResults(1);
        $resultList = $query->getOneOrNullResult();
        if($resultList){
            return $resultList;
        }else{
            return null;
        }
    }

    public function getTypeAdresse(){
        $model = $this->em->getRepository(TypeAdresse::class)->findAll();
        
            return $model;
    }
    public function getTypeTel(){
        $model = $this->em->getRepository(TypeTel::class)->findAll();
        if($model){
        }
        return $model;
    }
    function updateStatus($status, $id, $expectedStatus, $integrationRepo)
    {
        $integration = $this->getOneIntegration($id);
        if ($integration) {
            // Check if the current status is in the expectedStatus array
            if (in_array($integration->getStatus()->getId(), $expectedStatus)) {
                $this->anuulerIntegration($id, $status);
                return "OK";
            } else {
                return "ERROR";
            }
        } else {
            return "NOT_EXIST_ELEMENT";
        }
    }
    function anuulerIntegration($id , $status){
        $sql="UPDATE `integration` SET `status_id`=:status WHERE id = :id";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(":id",$id);
        $stmt->bindValue(":status",$status);
        $stmt = $stmt->executeQuery();
    }
    function addToLogImportDeb($etat,$idAction,$report,$deb)
    {
        $sql='insert into logs_actions(id_action_id,etat,rapport,id_debiteur_id) values('.$idAction.','.$etat.',"'.$report.'",'.$deb.')';
        $stmt = $this->conn->prepare($sql); 
        $stmt = $stmt->executeQuery();
    }
    function addToLogImportDbi($etat,$idAction,$report)
    {
        $sql='insert into debt_force_integration.logs_actions_dbi(id_action_id,etat,rapport) values('.$idAction.','.$etat.',"'.$report.'")';
        $stmt = $this->conn->prepare($sql); 
        $stmt = $stmt->executeQuery();
    }
    function addToLogImport($etat,$idAction,$report)
    {
        $sql='insert into logs_actions(id_action_id,etat,rapport) values('.$idAction.','.$etat.',"'.$report.'")';
        $stmt = $this->conn->prepare($sql); 
        $stmt = $stmt->executeQuery();
    }
    function testCreanceDbi($numero_creance , $id_ptf){
        $sql="SELECT d.id FROM `creance` d where d.numero_creance = :numero_creance and d.id_ptf_id = :id_ptf";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":numero_creance",$numero_creance);
        $stmt->bindValue(":id_ptf",$id_ptf);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            return true;
        }else{
            $sql="SELECT d.id FROM `debt_force_integration`.`creance_dbi` d where d.numero_creance = :numero_creance and d.id_ptf_id = :id_ptf";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":numero_creance",$numero_creance);
            $stmt->bindValue(":id_ptf",$id_ptf);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchOne();
            if($data){
                return true;
            }else{
                return false;
            }
        }
    }
    function testCreanceDbi1($numero_creance , $id_ptf){
        $sql="SELECT d.id FROM `creance` d where d.numero_creance = :numero_creance and d.id_ptf_id = :id_ptf";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":numero_creance",$numero_creance);
        $stmt->bindValue(":id_ptf",$id_ptf);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            
            $array["exist"]=true;
            $array["place"]=1;
            $array["creance"]=$data;
        }else{
            $sql="SELECT d.id FROM `debt_force_integration`.`creance_dbi` d where d.numero_creance = :numero_creance and d.id_ptf_id = :id_ptf";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":numero_creance",$numero_creance);
            $stmt->bindValue(":id_ptf",$id_ptf);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchOne();
            if($data){
                $array["exist"]=true;
                $array["place"]=2;
                $array["creance"]=$data;
            }else{
                $array["exist"]=false;
            }
        }
        return $array;
    }
    function testDossDbi1($numero_dossier , $id_ptf){
        $sql="SELECT d.id FROM `dossier` d where d.numero_dossier = :numero_dossier and d.id_ptf_id = :id_ptf";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":numero_dossier",$numero_dossier);
        $stmt->bindValue(":id_ptf",$id_ptf);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            $array["exist"]=true;
            $array["place"]=1;
            $array["dossier"]=$data;
        }else{
            $sql="SELECT d.id FROM `debt_force_integration`.`dossier_dbi` d where d.numero_dossier = :numero_dossier and d.id_ptf = :id_ptf";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":numero_dossier",$numero_dossier);
            $stmt->bindValue(":id_ptf",$id_ptf);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchOne();
            if($data){
                $array["exist"]=true;
                $array["place"]=2;
                $array["dossier"]=$data;
            }else{
                $array["exist"]=false;
            }
        }
        return $array;
    }
    function testCreanceDbi2($numero_creance , $id_ptf){
        $sql="SELECT d.id , d.total_creance FROM `creance` d where d.numero_creance = :numero_creance and d.id_ptf_id = :id_ptf";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":numero_creance",$numero_creance);
        $stmt->bindValue(":id_ptf",$id_ptf);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchAssociative();
        if($data){
            
            $array["exist"]=true;
            $array["place"]=1;
            $array["creance"]=$data;
        }else{
            $sql="SELECT d.id , d.total_creance FROM `debt_force_integration`.`creance_dbi` d where d.numero_creance = :numero_creance and d.id_ptf_id = :id_ptf";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":numero_creance",$numero_creance);
            $stmt->bindValue(":id_ptf",$id_ptf);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchAssociative();
            if($data){
                $array["exist"]=true;
                $array["place"]=2;
                $array["creance"]=$data;
            }else{
                $array["exist"]=false;
            }
        }
        return $array;
    }
    function testTypeDebiteur1($id_deb , $id_creance){
        $sql="SELECT d.id FROM `type_debiteur` d where d.id_debiteur_id = :id_deb and d.id_creance_id = :id_creance";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_deb",$id_deb);
        $stmt->bindValue(":id_creance",$id_creance);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            $array["exist"]=true;
            $array["place"]=1;
            $array["type_d"]=$data;
        }else{
            $sql="SELECT d.id FROM `debt_force_integration`.`type_debiteur_dbi` d where d.id_debiteur_id = :id_deb and d.id_creance_id = :id_creance";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id_deb",$id_deb);
            $stmt->bindValue(":id_creance",$id_creance);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchOne();
            if($data){
                $array["exist"]=true;
                $array["place"]=2;
                $array["type_d"]=$data;
            }else{
                $array["exist"]=false;
            }
        }
        return $array;
    }
    function testTypeDebiteur2($id_deb ,$id_deb_integ,$numero_dossier){
        // $sql="SELECT d FROM `type_debiteur` d where d.id_debiteur_id = :id_deb and d.id_creance_id = :id_creance";
        // $stmt = $this->conn->prepare($sql);
        // $stmt->bindValue(":id_deb",$id_deb);
        // $stmt->bindValue(":id_creance",$id_creance);
        // $stmt = $stmt->executeQuery();
        // $data = $stmt->fetchOne();
        // if($data){
        //     $array["exist"]=true;
        //     $array["place"]=1;
        //     $array["type_d"]=$data;
        // }else{
            $sql="SELECT d.id , d.id_creance_id  FROM `debt_force_integration`.`type_debiteur_dbi` d where d.id_debiteur_id = :id_deb and d.id_debiteur = :id_deb_integ and d.numero_dossier = :num
            and (d.id_creance_id != 0 or 1=1)
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id_deb",$id_deb);
            $stmt->bindValue(":id_deb_integ",$id_deb_integ);
            $stmt->bindValue(":num",$numero_dossier);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchAssociative();

            if($data){
                $array["exist"]=true;
                $array["place"]=2;
                $array["type_d"]=$data;
            }else{
                $array["exist"]=false;
            }
        // }
        return $array;
    }

    
    function testDebDoss($id_deb , $id_dossier){
        $sql="SELECT d.id FROM `debi_doss` d where d.id_debiteur_id = :id_deb and d.id_dossier_id = :id_dossier";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_deb",$id_deb);
        $stmt->bindValue(":id_dossier",$id_dossier);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            $array["exist"]=true;
            $array["place"]=1;
            $array["type_d"]=$data;
        }else{
            $sql="SELECT d.id FROM `debt_force_integration`.`debi_doss_dbi` d where d.id_debiteur_id = :id_deb and d.id_dossier_id = :id_dossier";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id_deb",$id_deb);
            $stmt->bindValue(":id_dossier",$id_dossier);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchOne();
            if($data){
                $array["exist"]=true;
                $array["place"]=2;
                $array["type_d"]=$data;
            }else{
                $array["exist"]=false;
            }
        }
        return $array;
    }
    function testAutreDebiteur($id_creance ){
        $sql="SELECT d.id FROM `type_debiteur` d where d.id_creance_id = :id_creance";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_creance",$id_creance);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            $array["exist"]=true;
            $array["place"]=1;
            $array["type_deb"]=$data;
        }else{
            $sql="SELECT d.id FROM `debt_force_integration`.`type_debiteur_dbi` d where d.id_creance_id = :id_creance";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id_creance",$id_creance);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchOne();
            if($data){
                $array["exist"]=true;
                $array["place"]=1;
                $array["doss"]=$data;
            }else{
                $array["exist"]=false;
            }
        }
        return $array;
    }
    
    function testDossierDbi($numero_dossier , $id_ptf){
        $sql="SELECT d.id FROM `dossier` d where d.numero_dossier = :numero_dossier and d.id_ptf_id = :id_ptf";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":numero_dossier",$numero_dossier);
        $stmt->bindValue(":id_ptf",$id_ptf);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            return true;
        }else{
            $sql="SELECT d.id FROM `debt_force_integration`.`dossier_dbi` d where d.numero_dossier = :numero_dossier and d.id_ptf = :id_ptf";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":numero_dossier",$numero_dossier);
            $stmt->bindValue(":id_ptf",$id_ptf);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchOne();
            if($data){
                return true;
            }else{
                return false;
            }
        }
    }
    //Pour vérifier l'existant de dossier
    function testDossierDbi1($numero_dossier , $id_ptf){
        $sql="SELECT d.id FROM `dossier` d where d.numero_dossier = :numero_dossier and d.id_ptf_id = :id_ptf";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":numero_dossier",$numero_dossier);
        $stmt->bindValue(":id_ptf",$id_ptf);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            $array["exist"]=true;
            $array["place"]=1;
            $array["doss"]=$data;
        }else{
            $sql="SELECT d.id FROM `debt_force_integration`.`dossier_dbi` d where d.numero_dossier = :numero_dossier and d.id_ptf = :id_ptf";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":numero_dossier",$numero_dossier);
            $stmt->bindValue(":id_ptf",$id_ptf);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchOne();
            if($data){
                $array["exist"]=true;
                $array["place"]=1;
                $array["doss"]=$data;
            }else{
                $array["exist"]=false;
            }
        }
        return $array;
    }
    function testDebiteurDbi($cin){
        $sql="SELECT d.id FROM `debt_force_integration`.`debiteur_dbi` d where cin = :cin";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":cin",$cin);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            return true;
        }else{
            $sql="SELECT d.id FROM `debiteur` d where cin = :cin";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":cin",$cin);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchOne();
            if($data){
                return true;
            }else{

                return false;
            }
        }
    }
    function findTypeDeb($id){ 
        $sql="SELECT d.id FROM `details_type_deb` d where id = :id";
        $stmt = $this->conn->prepare($sql);
        // $stmt->bindValue(":type",$type);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            return $data;
        }else{
            return false;
        }
    }
    function findTypeCreance($id){ 
        $sql="SELECT d.id FROM `details_type_creance` d where id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            return $data;
        }else{
            return false;
        }
    }
    
    
    function findProfession($profession){ 
        $sql="SELECT d.id FROM `profession` d where profession like :profession";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":profession",$profession);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            return $data;
        }else{
            return false;
        }
    }
    
    function getListeDebiteurDbi($id){
        $sql="SELECT * FROM `debt_force_integration`.`debiteur_dbi` d where id_import = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchAll();
        return $data;
    }
    function insertDebFromDbiToProd($idIntegration,$id_import,$idAction){
        $sql1 = "CALL debt_force_integration.PROC_INSERT_DEB_PROD(".$idIntegration." , ".$id_import." , ".$idAction.")";
        // dump($sql1);
        $sql = "CALL debt_force_integration.PROC_INSERT_DEB_PROD(:idIntegration , :idImport , :idAction)";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(":idImport",$id_import);
        $stmt->bindValue(":idIntegration",$idIntegration);
        $stmt->bindValue(":idAction",$idAction);
        $stmt = $stmt->executeQuery();
    }
    function insertDossierFromDbiToProd($idIntegration ,$id_import , $id_action){
        $sql1 = "CALL debt_force_integration.PROC_INSERT_DOSSIERS_PROD(".$idIntegration." , ".$id_import." , ".$id_action.")";
        // dump($sql1);
        $sql = "CALL debt_force_integration.PROC_INSERT_DOSSIERS_PROD(:idIntegration , :idImport , :idAction)";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(":idImport",$id_import);   
        $stmt->bindValue(":idIntegration",$idIntegration);  
        $stmt->bindValue(":idAction",$id_action);
        $stmt = $stmt->executeQuery();
    }
    function insertEmploiFromDbiToProd($idIntegration ,$id_import , $id_action){	
        $sql = "CALL debt_force_integration.PROC_INSERT_EMPLOI_PROD(".$idIntegration." , ".$id_import." , ".$id_action.")";
        $stmt = $this->conn->prepare($sql); 
        // $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
    }
    function insertEmployeurFromDbiToProd($idIntegration ,$id_import , $id_action){	
        $sql = "CALL debt_force_integration.PROC_INSERT_EMPLOYEUR_PROD(".$idIntegration." , ".$id_import." , ".$id_action.")";
        $stmt = $this->conn->prepare($sql); 
        $stmt = $stmt->executeQuery();
    }
    function insertTelephoneFromDbiToProd($idIntegration ,$id_import , $id_action){
        $sql = "CALL debt_force_integration.PROC_INSERT_TEL_PROD(".$idIntegration." , ".$id_import." , ".$id_action.")";dump($sql);
        $stmt = $this->conn->prepare($sql); 
        $stmt = $stmt->executeQuery();
    }
    function insertAdresseFromDbiToProd($idIntegration ,$id_import , $id_action){
        $sql = "CALL debt_force_integration.PROC_INSERT_ADRESSE_PROD(".$idIntegration." , ".$id_import." , ".$id_action.")";
        $stmt = $this->conn->prepare($sql); 
        $stmt = $stmt->executeQuery();
    }
    function findIntegrationValide($id){
        $model = $this->em->getRepository(Integration::class)->findOneBy(["id"=>$id , "status"=>9]);
        if($model){
            return $model;
        }else{
            return null;
        }
    }
    function getImport($id){
        $model = $this->em->getRepository(Import::class)->findBy(["id_integration"=>$id]);
        if($model){
            return $model;
        }else{
            return null;
        }
    }

    function insertCreanceFromDbiToProd($idIntegration ,$id_import , $id_action){
        /*$sql="INSERT INTO `debt_force`.`creance`(`id_dossier_id`,`id_ptf_id`, `numero_creance`, `date_echeance`, `total_creance`, `etat`, `type_creance`, `total_restant`, `date_creation`)
        SELECT 
            CASE 
                WHEN dbi.origine_doss = 2 THEN (SELECT d.id FROM `debt_force`.`dossier` d WHERE d.id = dbi.id_dossier) 
                WHEN dbi.origine_doss = 1 
        THEN (SELECT d.id FROM `debt_force`.`dossier` d WHERE d.id_dossier_dbi = dbi.id) 
        ELSE NULL END,
         dbi.`id_ptf_id`, dbi.`numero_creance`, dbi.`date_echeance`, dbi.`total_creance`, dbi.`etat`, dbi.`type_creance`, dbi.`total_restant`, dbi.`date_creation` FROM `debt_force_integration`.`creance_dbi` dbi WHERE `id_import` = :id";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();*/
        $sql = "CALL debt_force_integration.PROC_INSERT_CREANCE_PROD(".$idIntegration." , ".$id_import." , ".$id_action.")";
        // dump($sql);
        $stmt = $this->conn->prepare($sql); 
        // $stmt->bindValue(":idImport",$id_import);   
        // $stmt->bindValue(":idIntegration",$idIntegration);  
        // $stmt->bindValue(":idAction",$id_action);
        $stmt = $stmt->executeQuery();
    }
    function insertGarantieFromDbiToProd($id){
        $sql="INSERT INTO `garantie`(`id_garantie_dbi`,`type_garantie`, `description`, `taux`, `etat`) 
        SELECT `id`,`type_garantie`, `description`, `taux`, `etat` FROM `debt_force_integration`.`garantie_dbi` WHERE id_import=:id";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
    }
    function insertGarantieDebiteurFromDbiToProd($id){
        $sql ="INSERT INTO `garantie_debiteur`(`id_debiteur_id`, `id_garantie_id`)
        SELECT 
            CASE
                WHEN dbi.origin_deb = 2 THEN (SELECT d.id FROM `debt_force`.`debiteur` d WHERE d.id = dbi.id_debiteur)
                WHEN dbi.origin_deb = 1 THEN (SELECT d.id FROM `debt_force`.`debiteur` d WHERE d.id_debiteur_dbi = dbi.id)
                ELSE NULL
            END,
            g.id
        FROM `debt_force_integration`.`garantie_debiteur_dbi` dbi
        JOIN `garantie` g ON g.id_garantie_dbi = dbi.id_garantie
        WHERE dbi.`id_import` = :id";
             $stmt = $this->conn->prepare($sql); 
             $stmt->bindValue(":id",$id);
             $stmt = $stmt->executeQuery();
    }
    
    function insertGarantieCreanceFromDbiToProd($id){
        $sql ="INSERT INTO `garantie_creance`(`id_creance_id`, `garantie_id`) SELECT CASE WHEN dbi.origin_creance = 2 THEN (SELECT d.id FROM `debt_force`.`creance` d WHERE d.id = dbi.id_creance) WHEN dbi.origin_creance = 1 THEN (SELECT d.id FROM `debt_force`.`creance` d WHERE d.id_creance_dbi = dbi.id) ELSE NULL END, 
        g.id FROM debt_force_integration.creance_garantie_dbi dbi JOIN `creance` g ON g.id_creance_dbi = dbi.id_creance WHERE dbi.`id_import` = :id;";
             $stmt = $this->conn->prepare($sql); 
             $stmt->bindValue(":id",$id);
             $stmt = $stmt->executeQuery();
    }
    function insertProcFromDbiToProd($id){
        $sql="INSERT INTO `proc_judicaire`(`id_proc_dbi`,`date_depot`, `numero_judicaire`, `description`, `type_proc_judicaire`) 
        SELECT `id`,`date_depot`, `numero_judicaire`, `description`, `type_proc_judicaire` FROM `debt_force_integration`.`proc_dbi` WHERE id_import=:id";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
    }
    function insertProcDebiteurFromDbiToProd($id){
        $sql="INSERT INTO `proc_debiteur`(`id_debiteur_id`, `id_proc_judicaire_id`) SELECT CASE WHEN dbi.origin_deb = 2 THEN (SELECT d.id FROM `debt_force`.`debiteur` d WHERE d.id = dbi.id_debiteur) WHEN dbi.origin_deb = 1 THEN (SELECT d.id FROM `debt_force`.`debiteur` d WHERE d.id_debiteur_dbi = dbi.id) ELSE NULL END,
         g.id FROM `debt_force_integration`.`proc_debiteur_dbi` dbi JOIN `proc_judicaire` g ON g.id_proc_dbi = dbi.id_proc WHERE dbi.`id_import` = :id;";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
    }
    function insertProcCreanceFromDbiToProd($id){
        $sql="INSERT INTO `proc_creance`(`id_creance_id`, `id_proc_id`) SELECT CASE WHEN dbi.origin_creance = 2 THEN 
        (SELECT d.id FROM `debt_force`.`creance` d WHERE d.id = dbi.id_creance) WHEN dbi.origin_creance = 1 THEN (SELECT d.id FROM `debt_force`.`creance` d WHERE d.id_creance_dbi = dbi.id) ELSE NULL END, g.id FROM `debt_force_integration`.`proc_creance_dbi` dbi JOIN `creance` g ON g.id_creance_dbi = dbi.id_creance WHERE dbi.`id_import` =:id";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(":id",$id);
        $stmt = $stmt->executeQuery();
    }
    function setStatusIntegration($id , $status){
        $sql= "UPDATE `integration` SET `status_id` = :status WHERE `integration`.`id` = :id;";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(":id",$id);
        $stmt->bindValue(":status",$status);
        $stmt = $stmt->executeQuery();
    }
    function findIntegrationByTitre($titre){
        $sql="SELECT d.id FROM `integration` d where d.titre = :titre and status_id != 15 and status_id != 9";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":titre",$titre);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            return true;
        }
        return false;
    }

    function testDebiteurDbi1($cin){
        $array = array();
        $sql="SELECT d.id FROM `debt_force_integration`.`debiteur_dbi` d where d.cin_formate = :cin";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":cin",$cin);
        $stmt = $stmt->executeQuery();
        $data = $stmt->fetchOne();
        if($data){
            $array["exist"]=true;
            $array["place"]=2;
            $array["deb"]=$data;
        }else{
            $sql="SELECT d.id FROM `debiteur` d where d.cin_formate = :cin";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":cin",$cin);
            $stmt = $stmt->executeQuery();
            $data = $stmt->fetchOne();
            if($data){
                $array["exist"]=true;
                $array["place"]=1;
                $array["deb"]=$data;
            }else{
                $array["exist"]=false;
            }
        }
        return $array;
    }

    public function createModel($data_list , $type){
        $codeStatut="ERROR";
        $array = array();
        try {
            //code...
            $m = null;
            $is_exist = false;
            if($data_list){
                if($type != "debiteur"){
                    $is_exist = true;
                }else{
                    for ($j=0; $j < count($data_list); $j++) {
                        if(isset($data_list[$j]["col_param"])){
                            if($data_list[$j]["col_param"] == 185){
                                $is_exist = true;
                            }
                        }
                    }
                }
                
                if($is_exist ){
                    if(!count($data_list)>0 )
                    {
                        $codeStatut="EMPTY-DATA";
                    }
                    else
                    {
                        $m = new ModelImport();
                        $m->setTitre("");
                        $m->setDateCreation(new \DateTime()); 
                        $m->setType($type);
                        $this->em->persist($m);
                        $this->em->flush();
                        if($m){
                            for($i=0 ;$i < count($data_list);$i++){
                                if($data_list[$i]["required"])
                                {
                                    $check=1;
                                }
                                else
                                {
                                    $check=0;
                                }
                                $colTbale = "";
                                if(isset($data_list[$i]["column_db"])){
                                    $colTbale = $data_list[$i]["column_db"];
                                }
                               
                                $colParam = null;
                                if(isset($data_list[$i]["col_param"])&& $data_list[$i]["origine_champ"] == 1){
                                    $colParam = $this->em->getRepository(ColumnsParams::class)->findOneBy(["id"=>$data_list[$i]["col_param"]]); 
                                }
                                $tableName = "";
                                if($data_list[$i]["origine_champ"] == 1){
                                    $tableName = $data_list[$i]["table_name"];
                                }else{
                                    $champ = $this->em->getRepository(DetailModelAffichage::class)->findOneBy(["id"=>$data_list[$i]["col_param"]]); 
                                    $tableName = $champ->getTableName();
                                }
                                $colonne = new CorresColu();
                                $colonne->setIdModelImport($m);
                                $colonne->setColumnName(str_replace(" ","_",trim($data_list[$i]["column_file"])));
                                $colonne->setCode("code");
                                $colonne->setTableName($tableName);
                                $colonne->setColumnTable($colTbale);
                                $colonne->setRequired($check);
                                $colonne->setIdColParams($colParam);
                                $colonne->setOrigine(0);
                                $colonne->setOriginChamp($data_list[$i]["origine_champ"]);
                                $this->em->persist($colonne);
                            }
                        }
                        $this->em->flush();
                        $codeStatut="OK";
                    }
                }else{
                    $codeStatut="CIN_OUBLIGATOIRE";
                }
            }else{
                $codeStatut="EMPTY-DATA";
            }
        } catch (\Exception $e) {
            $codeStatut="ERROR";
        }
        // return $codeStatut;
        return $m;
    }

    public function executeSQL($sql){
        $stmt = $this->conn->prepare($sql); 
        $stmt = $stmt->executeQuery();
    }
    public function sauvguardeData($sql){
        $stmt = $this->conn->prepare($sql); 
        $stmt = $stmt->executeQuery();
    }
    public function getListeModelExport($type){
        $entity = $this->em->getRepository(ModelExport::class)->findBy(['type'=>$type]);
        return $entity;
    }
    public function getListePtfForMaj(){
        $query = $this->em->createQuery('SELECT t from App\Entity\Portefeuille t where t.id in (SELECT identity(i.id_ptf) from App\Entity\Integration i )');
        $tables = $query->getResult();

        if($tables){
            return $tables;
        }else{
            return null;
        }
    }
    public function getAllIntegrationMAJ(){
        $resultList = $this->em->getRepository(Integration::class)->findBy(["status" => [1, 2] , "isMaj"=>1 , "type"=>1]);
        if($resultList){
            return $resultList; 
        }else{
            return null;
        }
    }

    function insertCreanceMAJFromDbiToProd($idIntegration ,$id_import , $id_action){
        $sql = "CALL debt_force_integration.PROC_MAJ_INSERT_CREANCE_PROD(".$idIntegration." , ".$id_import." , ".$id_action.")";
        $stmt = $this->conn->prepare($sql); 
        $stmt = $stmt->executeQuery();
    }
    public function getAllInegrationCadrageByStatus2(){
        $resultList = $this->em->getRepository(Integration::class)->findBy(["status" => [1, 2] , "isMaj"=>0 ,"type"=>2]);
        if($resultList){
            return $resultList; 
        }else{
            return null;
        }
    }
}