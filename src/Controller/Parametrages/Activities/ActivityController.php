<?php

namespace App\Controller\Parametrages\Activities;

use App\Entity\Activite;
use App\Entity\ActiviteParent;
use App\Entity\Etap;
use App\Entity\EtapActivite;
use App\Entity\EventAction;
use App\Entity\IntermResultatActivite;
use App\Entity\ParamActivite;
use App\Entity\ResultatActivite;
use App\Entity\SousEtap;
use App\Entity\TypeParametrage;
use App\Service\AuthService;
use App\Service\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Parametrages\Activities\activityRepo;
use App\Repository\Creances\creancesRepo;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

#[Route('/API')]

class ActivityController extends AbstractController
{
    
    private  $activityRepo;
    private  $serializer;
    public $em;
    private $MessageService;
    private $AuthService;

    public function __construct(
        JWTEncoderInterface $JWTManager,
        activityRepo $activityRepo,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        MessageService $MessageService,
        AuthService $AuthService
        )
    {
        $this->activityRepo = $activityRepo;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->MessageService = $MessageService;
        $this->AuthService = $AuthService;
    }
    
    #[Route('/activities/createParentActivity', methods: ['POST'])]
    public function createParentActivity(activityRepo $activityRepo , Request $request): JsonResponse
    {
        $respObjects = array();
        $codeStatut = "ERROR";
        $titre = $request->get("titre");
        $note = $request->get("note");
        $this->AuthService->checkAuth(0,$request);

        $data = json_decode($request->getContent(), true);
        if(isset($data["resultArray"][0]['result_0'])){
            if( count($data["resultArray"][0]['result_0']) < 2){
                $codeStatut = "EMPTY-DATA-ACTIVITY";
            }else{
                if(trim($titre) != ""){
                    $findActivity = $activityRepo->getOneParentActivityByTitre($titre);
                    if($findActivity){
                        $codeStatut="ELEMENT_DEJE_EXIST";
                    }
                    else{
                        $activityRepo = $activityRepo->createParentActivity($titre , $note );
                        if($activityRepo){
                            $codeStatut = "OK";
                            $respObjects["data"] = $activityRepo;
                        }else{
                            $codeStatut = "ERREUR";
                        }
                    }
                }else{
                    $codeStatut = "EMPTY-DATA";
                }
            }
        }else{
            $codeStatut = "EMPTY-DATA-ACTIVITY";
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/activities/updateParentActivity')]
    public function updateParentActivity(activityRepo $activityRepo , Request $request): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);

        $respObjects = array();
        $codeStatut = "ERROR";
        $titre = $request->get("titre");
        $note = $request->get("note");
        $id = $request->get('id');
        $etat = 0;
        if(trim($titre) != ""){
            $findParent = $activityRepo->findParentActivity($id);
            if($findParent){
                $activityRepo = $activityRepo->updateParentActivity($id,$titre , $note );
                if($activityRepo){
                    $codeStatut = "OK";
                }else{
                    $codeStatut = "ERREUR";
                }
            }else{
                $codeStatut = "NOT_EXIST_M";
            }
        }else{
            $codeStatut = "EMPTY-DATA";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/activities/deleteParentActivity')]
    public function deleteParentActivity(activityRepo $activityRepo , Request $request): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);

        $respObjects = array();
        $codeStatut = "ERROR";
        $id = $request->get('id');
        $findParent = $activityRepo->findParentActivity($id);
        if($findParent){
            $deleteParent = $activityRepo->deleteParentActivity($id);
            $codeStatut = "OK";
        }else{
            $codeStatut = "NOT_EXIST_M";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

   
    #[Route('/activities/getAllParentActivity')]
    public function getAllParentActivity(activityRepo $activityRepo, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $respObjects = array();
        $codeStatut = "ERROR";
        try {
            $this->AuthService->checkAuth(0, $request);
            $parentActivities = $activityRepo->getAllParentActivity();

            $codeStatut = "OK";
            $respObjects["data"] = $parentActivities;
        } catch (\Exception $e) {
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/activities/getAllParentActivity2')]
    public function getAllParentActivity2(activityRepo $activityRepo, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $respObjects = array();
        $codeStatut = "ERROR";
        try {
            $this->AuthService->checkAuth(0, $request);
            $parentActivities = $activityRepo->getAllParentActivity2();

            $codeStatut = "OK";
            $respObjects["data"] = $parentActivities;
        } catch (\Exception $e) {
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    #[Route('/activities/getAllTypeOfParametrages')]
    public function getAllTypeOfParametrages(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $parentActivities = $activityRepo->getTypesOfSParametrages();
            $paramsActivity = $activityRepo->getParamsActivity();


            // Transform `paramsActivity` to avoid circular references
            $transformedParamsActivity = array_map(function ($activity) {
                return [
                    'id' => $activity->getId(),
                    'type' => $activity->getType(),
                    'code_type' => $activity->getCodeType(),
                    'id_branche' => $activity->getIdBranche()?->getId(),
                    'typeActivite' => $activity->getTypeActivite(),
                    'activite_p' => $activity->getActiviteP(),
                    // Exclude relations like `qualificationParams` and `creanceActivites`
                ];
            }, $paramsActivity);


            $codeStatut = "OK";
            $respObjects["data"] = $parentActivities;


            $respObjects["paramsActivity"] = $transformedParamsActivity;


            }catch(\Exception $e){
                $result = "Une erreur s'est produite".$e->getMessage();
                $respObjects["message"] = $result;
                $respObjects["err"] = $e->getMessage();
            }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    
    #[Route('/activities/getAllParamsActivity')]
    public function getAllParamsActivity(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $parentActivities = $activityRepo->getParamsActivity();
            $codeStatut = "OK";
            $respObjects["data"] = $parentActivities;
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
     #[Route('/activities/getAllParamsActivityByType')]
    public function getAllParamsActivityByType(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        $id = $request->get("id");
        try{
            $this->AuthService->checkAuth(0,$request);
            $parentActivities = $activityRepo->getParamsActivityByType($id);
            $codeStatut = "OK";
            $respObjects["data"] = $parentActivities;
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/activities/getListResultatByAct')]
    public function getListResultatByAct(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        $id = $request->get("id");
        try{
            $this->AuthService->checkAuth(0,$request);
            $parentActivities = $activityRepo->getListResultatByAct($id);
            $codeStatut = "OK";
            $respObjects["data"] = $parentActivities;
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/activities/getDetailsOfActivitie')]
    public function getDetailsOfActivities(Request $request ,activityRepo $activityRepo , SerializerInterface $serializer): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $parentActivities = $activityRepo->getDetailsOfActivitie();
            $codeStatut = "OK";
            $respObjects["data"] = $parentActivities;
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/activities/getResultats/{id}')]
    public function getResultats(Request $request,$id ,activityRepo $activityRepo , SerializerInterface $serializer): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $parentActivities = $activityRepo->getResultats($id);
            $codeStatut = "OK";
            $respObjects["data"] = $parentActivities;
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/activities/getDetailsOfTypeParametrages' )]
    public function getDetailsOfTypeParametrages(Request $request,activityRepo $activityRepo , SerializerInterface $serializer): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $parentActivities = $activityRepo->getDetailsOfTypeParametrages();
            $respObjects["message"] = "Opération effectué avec success";
            $respObjects["codeStatut"] = "OK";

            $respObjects["data"] = $parentActivities;
            // $Content = $serializer->serialize($respObjects, 'json');
        }catch(\Exception $e){
            $result = "Une erreur s'est produite ".$e->getMessage();
            $respObjects["message"] = $result;
        }
        return $this->json($respObjects );
    }
    #[Route('/activities/getOneParam' )]
    public function getOneParam(Request $request,activityRepo $activityRepo , SerializerInterface $serializer): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get('id');
            $respObjects["message"] = "Opération effectué avec success";
            $respObjects["codeStatut"] = "OK";
            $respObjects["data"] = $activityRepo->getOneParam($id);;
        }catch(\Exception $e){
            $result = "Une erreur s'est produite ".$e->getMessage();
            $respObjects["message"] = $result;
        }
        return $this->json($respObjects );
    }


    #[Route('/activities/saveTreeDecision', methods: ['POST'])]
    public function saveTreeDecision(activityRepo $activityRepo , Request $request ): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        $nbr_act = $request->get('nbr_act');
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $parentActReq = $request->get("parentAct");
            $activityArray = $data["activityArray"];
            
                for ($i=0; $i < count($activityArray); $i++) {
                    
                    $id_param = $activityArray[$i]["activite_".$i][0]["value"];
                    $param =  $activityRepo->getOneParam($id_param);
                    
                    //----Traitement
                    $parentActivity = $activityRepo->findParentActivity($parentActReq);
                    if($parentActivity && $param){
                        $activite_ = $activityRepo->createActivity($parentActivity , $param , $i);
                        if(isset($data["resultArray"][0]["result_".$i])){
                            $resultArray = $data["resultArray"][0]["result_".$i];
                            if($resultArray){
                                foreach ($resultArray as $key => $value) {
                                    
                                    $param = $activityRepo->getOneParam($value);
                                    $skip=false;
                                    // if($data["res_sauter"] == $param->getId()){
                                    //     $skip = true;
                                    // }
                                    $activityRepo->createResult($activite_ , $key ,$param ,$i , $skip);
                                }
                            }
                        }
                        if (isset($activityArray[$i]["activite_".$i][1]["etap"]) && is_array($activityArray[$i]["activite_".$i][1]["etap"])) {
                            $liste_etap = $activityArray[$i]["activite_".$i][1]["etap"];
                            for ($k=0; $k < count($liste_etap); $k++) { 
                                $id_param = $liste_etap[$k];
                                // $param =  $activityRepo->getOneParam($id_param);
                                $etap =  $activityRepo->getOneEtap($id_param);
                                $activityRepo->createEtap($activite_,$etap);
                            }
                        } 
                    }
                }
                if(isset($data["resLinkArray"][0])){
                    for($i = 0 ; $i<  count($data["resLinkArray"][0]) ;$i++){
                        $listRsLink = $data["resLinkArray"][0]["act_res_link_".$i];
                        if($listRsLink){
                            foreach ($listRsLink as $key => $values) {
                                $query = $this->em->createQuery('SELECT r FROM App\Entity\ResultatActivite r WHERE r.ordre = '.$key.' and r.id_activite in(SELECT a.id from App\Entity\Activite a WHERE a.id_parent_activite = :id_parent ) ')->setParameter('id_parent', $parentActReq)->setMaxResults(1);;
                                $resultatLink = $query->getOneOrNullResult();
                                if($resultatLink){
                                    foreach ($values as $act_num) {
                                        $query = $this->em->createQuery('SELECT a from App\Entity\Activite a WHERE a.num_link = '.$act_num.' and a.id_parent_activite = :id_parent  ')->setParameter('id_parent', $parentActReq)->setMaxResults(1);
                                        $act_link = $query->getOneOrNullResult();
                                        if($act_link){
                                            $activityRepo->createIntermResult($resultatLink , $act_link);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $codeStatut="OK";
        }catch(\Exception $e){
            $$codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }

    #[Route('/activities/getTreeDesicion' )]
    public function getTreeDecision(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
        $this->AuthService->checkAuth(0,$request);
        $id = $request->get('id');
            $parentActivity = $activityRepo->findParentActivity($id);
            if(!$parentActivity){
                $respObjects["codeStatut"] = "NOT_EXIST_ELEMENT";
            }else{
                $activite =  $this->em->getRepository(Activite::class)->findOneBy(["id_parent_activite"=>$id],["id"=>"ASC"]);

                $data = $this->generateDecisionTree($id);
                $respObjects["data"] = $data;
                $respObjects["activity"] = $parentActivity;
                $respObjects["type"] = $activite->getIdParam()->getIdBranche();
                $codeStatut = "OK";
            }
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }
    #[Route('/activities/saveActivity' )]
    public function saveActivity(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get('parentAct');
            $data = json_decode($request->getContent(), true);
            $parentActivity = $activityRepo->findParentActivity($id);
            if(!$parentActivity){
                $respObjects["codeStatut"] = "NOT_EXIST_ELEMENT";
            }else{
                if($data["titre"] != ""){
                    $parentActivity->setTitre($data["titre"]);
                    $parentActivity->setNote($data["note"]);
                    $this->em->flush();
                    $codeStatut="OK";
                }else{
                    $codeStatut = "EMPTY-DATA";
                }
            }
        }catch(\Exception $e){
            $codeStatut = "ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }

    
    
    

    #[Route('/activities/getTreeDesicionWorkFlow' )]
    public function getTreeDesicionWorkFlow(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get('id');
            $list_decision_tree = [];
            
            $list_parentActivities = $activityRepo->getAllParentActivity();

            for ($i=0; $i < count($list_parentActivities); $i++) { 
                $data = $this->generateDecisionTreeWorkflow($list_parentActivities[$i][0]->getId());
                $list_decision_tree[$i] = $data;
            }

            $codeStatut = "OK";
            $respObjects["data"] = $list_decision_tree;
        }catch(\Exception $e){
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }
    public function generateDecisionTreeWorkflow( $id)
    {
        $activite =  $this->em->getRepository(Activite::class)->findOneBy(["id_parent_activite"=>$id],["id"=>"ASC"]);
        $tree = $this->buildActiviteTree($activite  , $id);
        return $tree;
    }
    
    public function generateDecisionTree( $id)
    {
        $activite =  $this->em->getRepository(Activite::class)->findOneBy(["id_parent_activite"=>$id],["id"=>"ASC"]);
        $tree[] = $this->buildActiviteTree($activite  , $id);
        return $tree;
    }
    private function buildActiviteTree($activite  ,$id)
    {
        $activiteNode = [
            'parent' => $activite,
            'children' => [],
        ];

        if($activite){
            $interm = $this->em->getRepository(ResultatActivite::class)->findBy(["id_activite"=>$activite->getId()]);
            
            foreach ($interm as $intermResultatActivite) {
                $resultat = $intermResultatActivite;
                $activites =  $this->em->getRepository(IntermResultatActivite::class)->findBy(['id_resultat'=>$intermResultatActivite->getId()]);
                $resultatNode = [
                    'parent' => $resultat,
                    'children' => []
                ];
                //Check id activite
                for ($i=0; $i < count($activites); $i++) { 
                    $resultatNode['children'][] = $this->buildActiviteTree($activites[$i]->getIdActivite() ,$id);
                }
                $activiteNode['children'][] = $resultatNode;
            }   
        }   
        return $activiteNode;
    }
    private function buildActiviteTreeOld($activite  ,$id)
    {
            $activiteNode = [
                'activite' => $activite,
                'resultats' => [],
            ];

            if($activite){
                $interm = $this->em->getRepository(ResultatActivite::class)->findBy(["id_activite"=>$activite->getId()]);
                
                foreach ($interm as $intermResultatActivite) {
                    $resultat = $intermResultatActivite;
                    $activites =  $this->em->getRepository(IntermResultatActivite::class)->findBy(['id_resultat'=>$intermResultatActivite->getId()]);
                    $resultatNode = [
                        'resultat' => $resultat,
                        'activites_child' => []
                    ];
                    //Check id activite
                    for ($i=0; $i < count($activites); $i++) { 
                        $resultatNode['activites_child'][] = $this->buildActiviteTree($activites[$i]->getIdActivite() ,$id);
                    }
                    $activiteNode['resultats'][] = $resultatNode;
                }   
            }   
            return $activiteNode;
    }
    #[Route('/activities/deleteActivity' , methods: ['POST'])]
    public function deleteActivity(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get('id');
            $Activity = $activityRepo->findActivity($id);
            if(!$Activity){
                $respObjects["message"] = "Activité n'existe pas";
                $respObjects["codeStatut"] = "ERREUR";
            }else{
                 //Delete interm param
                 $interm =  $this->em->getRepository(IntermResultatActivite::class)->findBy(['id_activite'=>$Activity->getId()]);
                 foreach ($interm as $i) {
                     $this->em->remove($i);
                 }
                // $this->em->flush();

                //Delete resultat
                $resultat =  $this->em->getRepository(ResultatActivite::class)->findBy(['id_activite'=>$id]);
                foreach ($resultat as $res) {
                    $interm =  $this->em->getRepository(IntermResultatActivite::class)->findBy(['id_resultat'=>$res->getId()]);
                    foreach ($interm as $i) {
                        $this->em->remove($i);
                    }
                    $this->em->remove($res);
                }
                // $this->em->flush();

                // //Delete etap
                $etap =  $this->em->getRepository(EtapActivite::class)->findBy(['id_activite'=>$Activity->getId()]);
                foreach ($etap as $e) {
                    $this->em->remove($e);
                }
                // $this->em->flush();

               
                $this->em->remove($Activity);
                $this->em->flush();

                $respObjects["message"] = "Opération effectué avec success";
                $respObjects["codeStatut"] = "OK";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $result = "Une erreur s'est produite ".$e->getMessage();
            $respObjects["message"] = $result;
        }
        return $this->json($respObjects );
    }
    #[Route('/activities/deleteResult' )]
    public function deleteResult(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get('id');
            $result =  $this->em->getRepository(ResultatActivite::class)->findOneBy(['id'=>$id]);

            if(!$result){
                $respObjects["message"] = "Resultat n'existe pas";
                $respObjects["codeStatut"] = "ERREUR";
            }else{
                //Delete resultat
                $interm =  $this->em->getRepository(IntermResultatActivite::class)->findBy(['id_resultat'=>$result->getId()]);
                foreach ($interm as $i) {
                    $this->em->remove($i);
                }
                
                $this->em->remove($result);
                $this->em->flush();

                $respObjects["message"] = "Opération effectué avec success";
                $respObjects["codeStatut"] = "OK";
            }
        }catch(\Exception $e){
            $result = "Une erreur s'est produite ".$e->getMessage();
            $respObjects["message"] = $result;
        }
        return $this->json($respObjects);
    }
    #[Route('/activities/getEtapActivite')]
    public function getEtapActivite(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get('id');
            $result =  $this->em->getRepository(Activite::class)->findOneBy(['id'=>$id]);
            if(!$result){
                $codeStatut="NOT_EXIST_ELEMENT";
            }else{
                $result =  $this->em->getRepository(EtapActivite::class)->findBy(['id_activite'=>$id]);
                $respObjects["data"] = $result;
                $codeStatut="OK";
            }
        }catch(\Exception $e){
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/activities/saveEtap' , methods: ['POST'])]
    public function saveEtap(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            if(isset($data['titre'])  && isset($data['arrayEtapApproval']) && isset($data['arrayEtapSuivi']))
            {
                if(!empty($data['titre']) != "" && count($data['arrayEtapApproval']) == 1 && count($data['arrayEtapSuivi']) >= 1 ){
                    $familles = $data['familles'];
                    $etap = $activityRepo->createEtapParam($data['titre'],$familles);
                    $arrayEtapApproval = $data['arrayEtapApproval'];
                    $arrayEtapSuivi = $data['arrayEtapSuivi'];
                    for ($i=0; $i < count($arrayEtapApproval); $i++) { 
                        $param = $activityRepo->getOneParam($arrayEtapApproval[$i]['id']);
                        $activityRepo->createSousEtap($param , $etap ,1 , $i + 1);
                    }

                    for ($i=0; $i < count($arrayEtapSuivi); $i++) { 
                        $param = $activityRepo->getOneParam($arrayEtapSuivi[$i]['id']);
                        $activityRepo->createSousEtap($param , $etap ,2 , $i + 1);
                    }
                    $codeStatut="OK";
                
                }else{
                    $codeStatut="EMPTY-DATA";
                }
            }else{
                $codeStatut="EMPTY-DATA";
            }
            
        }catch(\Exception $e){
        $respObjects["error"] = $e->getMessage();
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    
    #[Route('/activities/getListeEtap')]
    public function getListeEtap(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $result =  $this->em->getRepository(Etap::class)->findAll();
            $respObjects["data"] = $result;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    #[Route('/activities/getListeEtapByFamille')]
    public function getListeEtapByFamille(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $famille = $request->get('id');
            $this->AuthService->checkAuth(0,$request);
            $result =  $this->em->getRepository(Etap::class)->findBy(['id_famille'=>$famille]);
            $respObjects["data"] = $result;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    #[Route('/activities/getOneEtap')]
    public function getOneEtap(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get('id');
            $result =  $this->em->getRepository(Etap::class)->find($id);
            $sousEtap =  $this->em->getRepository(SousEtap::class)->findBy(['id_etap'=>$result->getId()]);
            $data = [
                "titre"=>$result->getTitre(),
                "familles"=>$result->getIdFamille(),
                "arrayEtap"=>$sousEtap,
            ];
            $respObjects["data"] = $data;
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/activities/updateEtap' , methods: ['POST'])]
    public function updateEtap(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $id = $data['id'];
            $etapSelected =  $this->em->getRepository(Etap::class)->find($id);
            if($etapSelected)
            {
                if(isset($data['titre'])  && isset($data['arrayEtapApproval']) && isset($data['arrayEtapSuivi']))
                {
                    if(!empty($data['titre']) != "" && count($data['arrayEtapApproval']) == 1 && count($data['arrayEtapSuivi']) >= 1 ){
                        
                        $etapSelected->setTitre($data['titre']);
                        $etapSelected->setIdFamille($data['familles']);
                        $sousEtap =  $this->em->getRepository(SousEtap::class)->findBy(['id_etap'=>$etapSelected->getId()]);

                        foreach ($sousEtap as $sous) {
                            $this->em->remove($sous);
                        }
                        $this->em->flush();
                        $arrayEtapApproval = $data['arrayEtapApproval'];
                        $arrayEtapSuivi = $data['arrayEtapSuivi'];
                        for ($i=0; $i < count($arrayEtapApproval); $i++) { 
                            $param = $activityRepo->getOneParam($arrayEtapApproval[$i]['id']);
                            $activityRepo->createSousEtap($param , $etapSelected ,1 , $i + 1);
                        }
    
                        for ($i=0; $i < count($arrayEtapSuivi); $i++) { 
                            $param = $activityRepo->getOneParam($arrayEtapSuivi[$i]['id']);
                            $activityRepo->createSousEtap($param , $etapSelected ,2 , $i + 1);
                        }
                        $codeStatut="OK";
                    
                    }else{
                        $codeStatut="EMPTY-DATA";
                    }
                }else{
                    $codeStatut="EMPTY-DATA";
                }
            }
            
        }catch(\Exception $e){
        $respObjects["error"] = $e->getMessage();
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/activities/deleteEtap' , methods: ['POST'])]
    public function deleteEtap(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $id = $request->get('id');

            $etpAct =  $this->em->getRepository(EtapActivite::class)->findOneBy(array("idEtap" => $id));

            if($etpAct)
            {
                $codeStatut = "ETAP-LIAISON";

            }
            else
            {
                $etapSelected =  $this->em->getRepository(Etap::class)->find($id);

                if($etapSelected)
                {
                    $sousEtap =  $this->em->getRepository(SousEtap::class)->findBy(['id_etap'=>$etapSelected->getId()]);
    
                    foreach ($sousEtap as $sous) {
                        $this->em->remove($sous);
                    }
                    $this->em->remove($etapSelected);
                    $this->em->flush();
                    $codeStatut='OK';
                }
    
            }
            
        }catch(\Exception $e){
        $respObjects["error"] = $e->getMessage();
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/activities/getParametreByFamilles' )]
    public function getParametreByFamilles(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get('id');
            $params =  $this->em->getRepository(ParamActivite::class)->findBy(['id_branche'=>$id , 'typeActivite'=>1]);
            $array = array();
            foreach ($params as $param) {
                $array[] = [
                    "id"=>$param->getId(),
                    "type"=>$param->getType(),
                    "codeType"=>$param->getCodeType(),
                    "typeText"=>$param->getTypeActivite(),
                    "activiteP"=>$param->getActiviteP(),
                    "type_select"=>1,
                ];
            }
            $respObjects['data'] = $array;
            $codeStatut = "OK";            
        }catch(\Exception $e){
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }
    #[Route('/activities/saveParametre' ,methods: ['POST'])]
    public function saveParametre(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);

            $famille =  $this->em->getRepository(TypeParametrage::class)->find($data['famille']);
            $typeText = $data["typeText"];
            $codeType = $data["codeType"];
            $type_select = $data["type_select"];
            $parametrageSelected = $data['parametrageSelected'];
            if($famille && $typeText != ""){
                if($type_select == 1){
                   
                    $activityRepo->createParam(null , $codeType , $typeText , $famille , $type_select);
                    $codeStatut='OK';
                }else{
                    $activite_p = $this->em->getRepository(ParamActivite::class)->find($parametrageSelected);
                    if($activite_p){
                        $activityRepo->createParam($activite_p->getId() , $codeType , $typeText , $famille , $type_select);
                        $codeStatut='OK';
                    }else{
                        $codeStatut='EMPTY-DATA';
                    }
                }
            }else{
                $codeStatut='EMPTY-DATA';
            }
        }catch(\Exception $e){
            $codeStatut = "ERROR";
            $respObjects["err"] = $e->getMessage();
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects );
    }

    #[Route('/activities/geltListeParametrages')]
    public function getListeParametre(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get('id');
            $id = 1;
            $result =  $this->em->getRepository(ParamActivite::class)->findAll();
            $respObjects["data"] = array_map(function (ParamActivite $param) {
                return [
                    'id' => $param->getId(),
                    'type' => $param->getType(),
                    'code_type' => $param->getCodeType(),
                    'typeActivite' => $param->getTypeActivite(),
                    'activite_p' => $param->getActiviteP(),
                ];
            }, $result);

            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    
    #[Route('/activities/deleteParametre' , methods: ['POST'])]
    public function deleteParametre(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get('id');

            $se =  $this->em->getRepository(SousEtap::class)->findOneBy(array("id_param"=>$id));
            $act =  $this->em->getRepository(Activite::class)->findOneBy(array("id_param"=>$id));
            $resAct =  $this->em->getRepository(ResultatActivite::class)->findOneBy(array("id_param"=>$id));

            if($se || $act || $resAct)
            {
                $codeStatut="PARAM-LIAISON";
            }
            else
            {
                $result =  $this->em->getRepository(ParamActivite::class)->find($id);
                $this->em->remove($result);
                $this->em->flush();
                $codeStatut="OK";    
            }
        }catch(\Exception $e){
            $codeStatut=$e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/activities/updateParametre' , methods: ['POST'])]
    public function updateParametre(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        // try{
            $this->AuthService->checkAuth(0,$request);
            $data = json_decode($request->getContent(), true);
            $id = $data["id"];
            $result =  $this->em->getRepository(ParamActivite::class)->find($id);
            if($result){
                $codeStatut="OK";
                $famille =  $this->em->getRepository(TypeParametrage::class)->find($data['famille']);
                $typeText = $data["typeText"];
                $codeType = $data["codeType"];
                $type_select = $data["type_select"];
                $parametrageSelected = $data['parametrageSelected'];
                if($famille && $typeText != ""){
                    if($type_select == 2){
                        $activite_p = $this->em->getRepository(ParamActivite::class)->find($parametrageSelected);
                        if($activite_p){
                            $activityRepo->updateParam($id , $activite_p->getId() , $codeType , $typeText , $famille , $type_select);
                            $codeStatut='OK';
                        }else{
                            $codeStatut='EMPTY-DATA';
                        }
                    }else{
                        $activityRepo->updateParam($id,null , $codeType , $typeText , $famille , $type_select);
                        $codeStatut='OK';
                    }
                }else{
                    $codeStatut='EMPTY-DATA';
                }
            }else{
                $codeStatut="NOT_EXIST_ELEMENT";
            }
        // }catch(\Exception $e){
        //     $codeStatut="ERROR";
        // $respObjects["err"] = $e->getMessage();
        // }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/activities/getOneParametre' )]
    public function getOneParametre(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get('id');
            $result =  $this->em->getRepository(ParamActivite::class)->find($id);
            $respObjects['data'] = $result;
            
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }
    #[Route('/activities/getQualificationByType' )]
    public function getQualificationByType(activityRepo $activityRepo , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{
            $this->AuthService->checkAuth(0,$request);
            $id = $request->get('id');
            $result = $activityRepo->getQualificationByType($id);
            $respObjects['data'] = $result;
            
            $codeStatut="OK";
        }catch(\Exception $e){
            $codeStatut="ERROR";
            $respObjects['error'] = $e->getMessage();
        }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }

    #[Route('/activities/replaceActivity')]
    public function replaceActivity(activityRepo $activityRepo , Request $request): JsonResponse
    {
        $this->AuthService->checkAuth(0,$request);

        $respObjects = array();
        $codeStatut = "ERROR";
        $data = json_decode($request->getContent(), true);
    
        $id = $data['id'] ?? null; // Use null coalescing operator to avoid undefined index errors
        $idRemplacement = $data['idRemplacement'] ?? null;
        if (!$idRemplacement) {
            $activites = $this->em->getRepository(ActiviteParent::class)->createQueryBuilder('a')
                ->where('a.id != :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getResult();
        
            // Ensure there's at least one activity available before accessing the first element
            if (!empty($activites)) {
                $idRemplacement = $activites[0]->getId();
            }
        }
        
        $events =  $this->em->getRepository(EventAction::class)->findBy(array("id_activity_p" => $id));
        if($events)
        {
            foreach ($events as $event) {

                $event->setIdActivityP($idRemplacement);

            }

            $findParent = $activityRepo->findParentActivity($id);
            if($findParent){
                $deleteParent = $activityRepo->deleteParentActivity($id);
                $codeStatut = "OK";
            }else{
                $codeStatut = "NOT_EXIST_M";
            }
    
        }
    
        
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }


    #[Route('/activities/getListTypeParamsCommunication')]
    public function getListTypeParamsCommunication(activityRepo $activityRepo , SerializerInterface $serializer , Request $request,creancesRepo $creancesRepo): JsonResponse
    {
        $respObjects =array();
        $codeStatut = "ERROR";
        try{

            $id = $request->get('id');
    
            $this->AuthService->checkAuth(0,$request);
            $parentActivities = $activityRepo->getTypesOfSParametragesC();
            $paramsActivity = $activityRepo->getParamsActivityC(2);


            // Transform `paramsActivity` to avoid circular references
            $transformedParamsActivity = array_map(function ($activity) {
                return [
                    'id' => $activity->getId(),
                    'type' => $activity->getType(),
                    'code_type' => $activity->getCodeType(),
                    'id_branche' => $activity->getIdBranche()?->getId(),
                    'typeActivite' => $activity->getTypeActivite(),
                    'activite_p' => $activity->getActiviteP(),
                    // Exclude relations like `qualificationParams` and `creanceActivites`
                ];
            }, $paramsActivity);


            $codeStatut = "OK";
            $respObjects["data"] = $parentActivities;
            $respObjects["allDebiteur"] =$creancesRepo->getListesDebiteurByDossier($id);


            $respObjects["paramsActivity"] = $transformedParamsActivity;


            }catch(\Exception $e){
                $result = "Une erreur s'est produite".$e->getMessage();
                $respObjects["message"] = $result;
                $respObjects["err"] = $e->getMessage();
            }
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);

        
        return $this->json($respObjects);
    }
    
}
