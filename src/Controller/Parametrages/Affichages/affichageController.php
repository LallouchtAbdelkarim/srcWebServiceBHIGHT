<?php

namespace App\Controller\Parametrages\Affichages;

use App\Entity\DetailModelAffichage;
use App\Service\MessageService;
use App\Entity\ModelAffichage;
use App\Repository\Parametrages\affichages\affichageRepo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/API')]

class affichageController extends AbstractController
{
    private $MessageService;

    public function __construct(
        affichageRepo $affichageRepo,
        SerializerInterface $serializer,
        MessageService $MessageService,
        EntityManagerInterface $em)
    {
    $this->affichageRepo = $affichageRepo;
    $this->serializer = $serializer;
    $this->em = $em;
    $this->MessageService = $MessageService;
    }
    
    #[Route('/affichages/listModels')]
    public function index(affichageRepo $affichageRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut="ERROR";
        try{
            $data = $affichageRepo->getListModels();
            $respObjects["data"] = $data;
            $codeStatut = "OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
        $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/affichages/createModel', methods: ['POST'])]
    public function createModel(affichageRepo $affichageRepo , Request $request): JsonResponse
    {
        $respObjects = array();
        $codeStatut="ERROR";
        try {
            //code...
            $data = json_decode($request->getContent(), true);
            $titre = $data["title"];
            $objet = $data["objet"];
            $ptf = $data["ptf"];
            if(trim($titre) != "" || !($ptf)){
                $activityRepo = $affichageRepo->createModel($titre , $objet , $ptf );
                if($activityRepo){
                    $respObjects["data"] = $activityRepo;
                    $codeStatut = "OK";
                }else{
                    $codeStatut="ERROR";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        } catch (\Exception $e) {
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/affichages/updateModel', methods: ['POST'])]
    public function updateModel(affichageRepo $affichageRepo , Request $request): JsonResponse
    {
        $respObjects = array();
        $codeStatut="ERROR";
        try {
            //code...  
        $data = json_decode($request->getContent(), true);
        $titre = $data["titre"];
        $objet = $data["objet"];
        $id = $data["id"];
        $findModel = $affichageRepo->findModel($id);
        if($findModel){
            if(trim($titre) != "" && trim($objet) ){
                $activityRepo = $affichageRepo->updateModel($titre , $objet , $id);
                if($activityRepo){
                    $codeStatut = "OK";
                }else{
                    $codeStatut="ERROR";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
        }else{
            $codeStatut="NOT_EXIST_M";
        }
        } catch (\Exception $th) {
            $codeStatut="ERROR";
        $respObjects["err"] = $th->getMessage();

        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/affichages/getModel')]
    public function getOneModel(affichageRepo $affichageRepo , Request $request): JsonResponse
    {
        $respObjects = array();
        $codeStatut="ERROR";
        try{
            $id = $request->get("id");
            $data = $affichageRepo->findModel($id);
            if($data){
                $codeStatut = "OK";
                $respObjects["data"] = $data;
            }else{
                $codeStatut="NOT_EXIST_M";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }  

    #[Route('/affichages/deleteModel')]
    public function deleteModel(affichageRepo $affichageRepo , Request $request): JsonResponse
    {
        $respObjects = array();
        $codeStatut="ERROR";
        try{
            $id = $request->get("id");
            $data = $affichageRepo->findModel($id);
            if($data){
                $codeStatut = "OK";
                $affichageRepo->deleteModel($id);
            }else{
                $codeStatut="NOT_EXIST_M";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }  
    #[Route('/affichages/createDetailModel/{id}', methods: ['POST'])]
    public function createDetailModel(affichageRepo $affichageRepo ,$id , Request $request): JsonResponse
    {        
        $respObjects = array();
        $codeStatut="ERROR";
        try{
            $data = json_decode($request->getContent(), true);

            $table = $data["table_name"];
            $champ_name = $data["champ_name"];
            $etat = $data["etat"];
            $length = $data["length"];
            $type_creance = $data["type_creance"];
            $type_champ = $data["type_champ"];
            $required = $data['required'];        
            $findModel = $affichageRepo->findModel($id);
            if($findModel){
            if($table != "" or $champ_name != "" or $length != "" or $etat != "" or $required != ""){
                $activityRepo = $affichageRepo->createDetailModel($id,$table ,$champ_name ,$length ,$etat , $type_creance,$type_champ,$required);
                if($activityRepo){
                    $codeStatut = "OK";
                }else{
                    $codeStatut="ERROR";
                }
            }else{
                $codeStatut="ERROR-EMPTY-PARAMS";
            }
            }else{
                $codeStatut="NOT_EXIST_M";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects["ERR"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/affichages/listDetailsModels')]
    public function listDetailsModels(affichageRepo $affichageRepo , Request $request): JsonResponse
    {
        $respObjects = array();
        $codeStatut="ERROR";
        try{
            $id = $request->get("id");
            $findM = $affichageRepo->findModel($id);
            if($findM){
                $data = $affichageRepo->listDetailsModels($id);
                $codeStatut = "OK";
                $respObjects["data"] = $data;
            }else{
                $codeStatut="NOT_EXIST_M";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/affichages/updateDetailModel', methods: ['POST'])]
    public function updateDetailModel(affichageRepo $affichageRepo , Request $request): JsonResponse
    {
        $respObjects = array();
        $codeStatut="ERROR";
        $data = json_decode($request->getContent(), true);

        try {
            $id_detail_model = $data["id"];
            $table = $data["table_name"];
            $champ_name = $data["champ_name"];
            $etat = $data["etat"];
            $length = $data["length"];
            $type_creance = $data["type_creance"];
            $type_champ = $data["type_champ"];
            $required = $data['required'];        
            $data = $affichageRepo->findDetailModel($id_detail_model);
            if($data){
                if($table != "" or $champ_name != "" or $length != "" or $etat != "" or $required != ""){
                    $activityRepo = $affichageRepo->updateDetailModel($id_detail_model,$table ,$champ_name ,$length ,$etat , $type_creance,$type_champ,$required);
                    if($activityRepo){
                        $codeStatut = "OK";
                    }else{
                        $codeStatut="ERROR";
                    }
                }else{
                    $codeStatut="NOT_EXIST_ELEMENT";
                }
            }else{
                $codeStatut="NOT_EXIST_ELEMENT";
            }
        } catch (\Exception $th) {
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/affichages/deleteDetailModel')]
    public function deleteDetailModel(affichageRepo $affichageRepo , Request $request): JsonResponse
    {
        $respObjects = array();
        $codeStatut="ERROR";

        try{
            $id = $request->get("id");
            $data = $affichageRepo->findDetailModel($id);
            if($data){
                $affichageRepo->deleteDetailModel($id);
                $codeStatut = "OK";
            }else{
                $codeStatut="NOT_EXIST_ELEMENT";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }  
    
    #[Route('/add_detail_affichage', name: 'add_detail_affichage', methods: ['POST'])]
    public function add_model_affichage(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $response = "";
        $titre = $data['title'];
        $objet = $data['objet'];


        if ($titre == "" && $objet == "") {
            $response = "Un des champs est vide !";
        } else {
            if (!$data) {
                $response = "Veuillez ajouter des champs !";
            } else {
                $model_titre = $entityManager->getRepository(ModelAffichage::class)->findOneBy(['Titre' => $titre]);
                if ($model_titre) {
                    $response = "Model déja exist";
                } else {
                    $model = new ModelAffichage();
                    $model->setTitre($titre);
                    $model->setObjet($objet);
                    $model->setDateCreation(new \DateTime());
                    $entityManager->persist($model);
                    $entityManager->flush();
                    $response = "OK";

                    foreach ($data['array'] as $d) {
                        $detail_model = $entityManager->getRepository(DetailModelAffichage::class)->findOneBy(['champ_name' => $d['champ_name'], 'table_name' => $d['table_name'], 'id_model_affichage' => $model]);
                        if ($detail_model) {
                            $response = "cette champ déja exist";
                        } else {
                            $detail_model = new DetailModelAffichage();
                            $detail_model->setTableName($d['table_name']);
                            $detail_model->setChampName($d['champ_name']);
                            $detail_model->setTypeChamp($d['type_champ'] == 'text' ? 'VARCHAR' : $d['type_champ']);
                            $detail_model->setLength($d['length']);
                            $detail_model->setIdModelAffichage($model);
                            $detail_model->setEtat($d['etat']);
                            $detail_model->setTypeCreance("type_creance");
                            $detail_model->setRequired($d['required']);
                            $entityManager->persist($detail_model);
                            $entityManager->flush();
                            $response = "OK";
                        }
                    }
                    $entityManager->flush();
                }
            }
        }
        return $this->json($response);
}
    
    #[Route('/add_model_affichage', name: 'add_detail', methods: ['POST'])]
    public function add_model(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $response = "";
        $titre = $data['title'];
        $objet = $data['objet'];
        if ($titre == "" && $objet == "") {
            $response = "Un des champs est vide !";
        } else {
            if (!$data) {
                $response = "Veuillez ajouter des champs !";
            } else {
                $model_titre = $entityManager->getRepository(ModelAffichage::class)->findOneBy(['Titre' => $titre]);
                if ($model_titre) {
                    $response = "Model déja exist";
                } else {
                    $model = new ModelAffichage();
                    $model->setTitre($titre);
                    $model->setObjet($objet);
                    $model->setDateCreation(new \DateTime());
                    $entityManager->persist($model);
                    $entityManager->flush();
                    $response = "OK";
                }
            }
        }

        return $this->json($response);
    }
    
    #[Route('/delete_model_affichage/{id}', name: 'delete_detail_affichage1')]
    public function delete_model_affichage(EntityManagerInterface $entityManager, $id, Request $request): JsonResponse
    {
        $response = "";

        $model = $entityManager->getRepository(ModelAffichage::class)->findOneBy(['id' => $id]);

        if (!$model) {
            $response = "Model n'existe pas !";
        } else {
            $detail_model = $entityManager->getRepository(DetailModelAffichage::class)->findBy(['id_model_affichage' => $id]);

            foreach ($detail_model as $detail) {
                $entityManager->remove($detail);
            }
            $entityManager->remove($model);
            $entityManager->flush();
            $response = "OK";
        }
        return new JsonResponse($response);
    }

    #[Route('/Add_detail_model_affichage/{id}', name: 'add_detail_model_affichage')]
    public function Add_detail_model_affichage(EntityManagerInterface $entityManager, $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $response = "";
        $model = $entityManager->getRepository(ModelAffichage::class)->findOneBy(['id' => $id]);

        $detail_model = new DetailModelAffichage();
            $detail_model->setTableName($data['table_name']);
            $detail_model->setChampName($data['champ_name']);
            $detail_model->setTypeChamp($data['type_champ']);
            $detail_model->setTypeChamp($data['type_champ'] == 'text' ? 'VARCHAR' : $data['type_champ']);
            $detail_model->setLength($data['length']);
            $detail_model->setIdModelAffichage($model);
            $detail_model->setEtat($data['etat']);
            $detail_model->setTypeCreance($data["type_creance"]);
            $detail_model->setRequired($data['required']);
            $entityManager->persist($detail_model);
        $entityManager->flush();
        $response = "OK";

        return $this->json($response);
    }

    
    #[Route('/update_model_affichage/{id}', name: 'modifier_model')]
    public function update_modele_action(EntityManagerInterface $entityManager, $id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);


        $model = $entityManager->getRepository(ModelAffichage::class)->findOneBy(['id' => $id]);
        if (!$model) {
            $response = "Ce model n'existe pas !";
        } else {
            $model_titre = $entityManager->getRepository(ModelAffichage::class)->findOneBy(['Titre' => $data['titre']]);
            $model->setTitre($data['titre']);
            $model->setObjet($data['objet']);
            $entityManager->flush();
            $response = "OK";
            // }
        }
        return new JsonResponse($response);
    }

    #[Route('/delete_detail_affichage/{id}', name: 'delete_detail_affichage')]
    public function delete_detail_affichage(EntityManagerInterface $entityManager, $id, Request $request): Response
    {
        $response = "";
        $detail_model = $entityManager->getRepository(DetailModelAffichage::class)->findOneBy(['id' => $id]);

        if (!$detail_model) {
            $response = "Model n'existe pas !";
        } else {

            $entityManager->remove($detail_model);

            $entityManager->flush();
            $response = "OK";
        }

        return new JsonResponse($response);
    }
    #[Route('/update_detail_affichage')]
    public function update_detail_action(EntityManagerInterface $entityManager, Request $request): Response
    {
        $response = "";
        $data = json_decode($request->getContent(), true);

        $detail_model = $entityManager->getRepository(DetailModelAffichage::class)->findOneBy(['id' => $data['id']]);
        if (!$detail_model) {
            $response = "Ce champ n'existe pas !";
        } else {
            $detail_model->setTableName($data['table_name']);
            $detail_model->setChampName($data['champ_name']);
            $detail_model->setTypeChamp($data['type_champ']);
            $detail_model->setLength($data['length']);
            $detail_model->setEtat($data['etat']);
            $detail_model->setTypeCreance($data["type_creance"]);
            $detail_model->setRequired($data['required']);
            $entityManager->persist($detail_model);
            $entityManager->flush();
            $response = "OK";
        }
        return new JsonResponse($response);
    }


    #[Route('/get_detail_affichage', name: 'modifier_detail')]
    public function get_detail_action(EntityManagerInterface $entityManager, Request $request, SerializerInterface $serializer): Response
    {
        $response = "";

        $id = $request->get('id');

        $detail_model = $entityManager->getRepository(DetailModelAffichage::class)->findOneBy(['id' => $id]);
        $jsonContent = $serializer->serialize($detail_model, 'json');
        return new JsonResponse($jsonContent);
    }
    #[Route('/get_model')]
    public function get_model(EntityManagerInterface $entityManager, Request $request, SerializerInterface $serializer): Response
    {
        $response = "";

        $id = $request->get('id');

        $data = $entityManager->getRepository(ModelAffichage::class)->findOneBy(['id' => $id]);
        $jsonContent = $serializer->serialize($data, 'json');
        return new JsonResponse($jsonContent);
    }
    #[Route('/getModels')]
    public function getmodels(EntityManagerInterface $entityManager, Request $request, SerializerInterface $serializer): Response
    {
        $data = $entityManager->getRepository(ModelAffichage::class)->findAll();
        $jsonContent = $serializer->serialize($data,  'json');
        return new JsonResponse($jsonContent);
    }
    #[Route('/getModel/{id}')]
    public function getmodel(EntityManagerInterface $entityManager, Request $request, $id, SerializerInterface $serializer): Response
    {
        $sqlptf = "SELECT * FROM `model_affichage` WHERE id =" . $id;
        $stmtd = $this->connection->prepare($sqlptf);
        $stmtd = $stmtd->executeQuery();
        $model = $stmtd->fetchAllAssociative();

        $champs = "SELECT *
        FROM detail_model_affichage
        WHERE id_model_affichage_id = " . $id;
        $stmt = $this->connection->prepare($champs);
        $stmt = $stmt->executeQuery();
        $resulatChamps = $stmt->fetchAllAssociative();

        $result = [
            'champs' => $resulatChamps,
            'model' => $model
        ];
        return new JsonResponse($result);
    }

}
