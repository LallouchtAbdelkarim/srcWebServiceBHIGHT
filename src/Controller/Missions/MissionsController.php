<?php

namespace App\Controller\Missions;

use Proxies\__CG__\App\Entity\FileMissions;
use Proxies\__CG__\App\Entity\TypeMissions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Proxies\__CG__\App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\AuthService;
use App\Service\MessageService;
use App\Service\typeService;
use App\Repository\Missions\missionsRepo;
use App\Repository\Users\userRepo;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FileService;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/API/missions')]
class MissionsController extends AbstractController
{
    private  $integrationRepo;
    private  $donneurRepo;
    private  $affichageRepo;
    private  $serializer;
    public $em;
    private $conn;
    private $AuthService;
    private $TypeService;

    public function __construct(
        missionsRepo $missionsRepo,
        userRepo $userRepo,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        MessageService $MessageService,
        Connection $conn,
        AuthService $AuthService,
        TypeService $TypeService,
        FileService $FileService,
        )
    {
        $this->conn = $conn;
        $this->missionsRepo = $missionsRepo;
        $this->userRepo = $userRepo;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->FileService = $FileService;
        $this->MessageService = $MessageService;
        $this->AuthService = $AuthService;
        $this->TypeService = $TypeService;
    }
    #[Route('/setImport', methods:"POST")]
    public function setImport(missionsRepo $missionsRepo ,Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $titre = $request->get("titre");
            $colFile = $request->get("colFile");
            $colDb = $request->get("colDb");
            $type = $request->get("type");
            if($titre != "" || $type != "" || empty($titre))
            {
                if(!empty($_FILES['file']['name'])){
                    $file = $_FILES['file'];
                    $fileCheck=$this->FileService->checkFile($file);
                    if($fileCheck["codeStatut"] == "OK" ){
                        if(!in_array("dossier",$colDb) or !in_array("adresse",$colDb) )
                        {
                            $codeStatut="EMPTY-DATA";
                        }
                        else
                        {
                            $nom = $fileCheck["nom"];
                            $extension_upload=$fileCheck["extension_upload"];
                            $fileTmpLoc=$fileCheck["fileTmpLoc"];
                            $id_users = $this->AuthService->returnUserId($request);
                            $user =$this->em->getRepository(Utilisateurs::class)->findOneBy(["id"=>$id_users]);
                            $typeSelect = $this->em->getRepository(TypeMissions::class)->findOneBy(["id"=>$type]);
                            
                            // $status =$this->em->getRepository(Stat::class)->findOneBy(["id"=>1]);
                            $import = new FileMissions();
                            $import->setDateCreation(new \DateTime());
                            $import->setIdUsers($user);
                            $import->setUrl("");
                            $import->setTitre($titre);
                            $import->setIdTypeMissions($typeSelect);
                            $this->em->persist($import);
                            $this->em->flush();
        
                            $filesystem = new Filesystem();
                            $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/fichiers/import_missions/';
                            $folderPath = $publicDirectory . 'import-num-'.$import->getId();
                            $fileStore = "fichiers/import_missions/import-num-".$import->getId() ."/". $nom . '.' . $extension_upload;
                            //----Create file if n'existe pas
                            $filesystem->mkdir($folderPath);
                            move_uploaded_file($fileTmpLoc, $fileStore);
                            $import->setUrl($fileStore);
                            $this->em->flush();
                            if (($handle = fopen($fileStore, "r")) !== FALSE)
                            {
                                while (($data = fgetcsv($handle, 1000000, ";")) !== FALSE)
                                {
                                    break;
                                }
                                //Les entétes de fichier excel
                                $data=array_map("utf8_encode",$data);
                                $data=array_map('trim', $data);
                                $data1=array_map("utf8_encode",str_replace(" ","_",$data));
                                $data=$this->FileService->convert($fileStore,";");

                                if(count($data)>0){
                                    if($data)
                                    {
                                        $numeroDossier="";
                                        $adresse="";
                                        $dd = 0;

                                        foreach ($data as $row)
                                        {
                                            $motif="";
                                            for ($i = 0; $i < count($colFile); $i++)
                                            {
                                                if($colDb[$i]=="dossier")
                                                {
                                                    $numeroDossier=$row[$colFile[$i]];
                                                }
                                                if($colDb[$i]=="adresse")
                                                {
                                                    $adresse=$row[$colFile[$i]];
                                                }
                                            }
                                            $sql = "INSERT INTO details_file (`id_file_missions_id`, `numero_dossier`, `adresse`, `is_in_missions`) VALUES (" . $import->getId() . ", '" . $numeroDossier . "', '" . $adresse . "', 0)";
                                            $respObjects["data1"]=$sql;
                                            $stmt = $this->conn->prepare($sql)->executeQuery();

                                        }
                                    }
                                }
                            }
        


                            $codeStatut="OK";
                            $respObjects["data"] = $missionsRepo->getOneFile($import->getId());
                        }
                    }else{
                        $codeStatut=$fileCheck["codeStatut"] ;
                    }
                }
            }
            else{
                $codeStatut='EMPTY-DATA';
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getTypeMissions')]
    public function getTypeMissions(Request $request,missionsRepo $missionsRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data_list = json_decode($request->getContent(), true);
            $data = $missionsRepo->getTypeMissions();
            $respObjects["data"] =$data;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getAllIFilleMissions')]
    public function getAllIFilleMissions(missionsRepo $missionsRepo ,Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $id = $request->get("id");
            $data = $missionsRepo->getAllIFilleMissions();
            $codeStatut = "OK";
            $respObjects["data"] = $data;
        }catch(\Exception $e){
            $respObjects["err"] = $e->getMessage();
            $codeStatut = "ERREUR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/getOneFile')]
    public function getOneFile(missionsRepo $missionsRepo ,Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $id = $request->get("id");
            $data = $missionsRepo->getOneFile($id);
            $details = $missionsRepo->getDetailsFile($id);
            $codeStatut = "OK";
            $respObjects["data"] = $data;
            $respObjects["details"] = $details;
        }catch(\Exception $e){
            $respObjects["err"] = $e->getMessage();
            $codeStatut = "ERREUR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
}
