<?php

namespace App\Controller\Agence\Dashboard;
use App\Repository\Oracle\AuthentificationRepository;
use App\Repository\Oracle\DevisRepository;
use App\Repository\Oracle\AbonnementsRepository;
use App\Repository\Oracle\FacturesRepository;
use App\Repository\Oracle\ConsommationRepository;
use App\Repository\Oracle\ImpayeesRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Actualites;
use App\Entity\Faq;
use App\Entity\Communes;
use App\Entity\Agences;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use App\Service\MailerService;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
class DashboardController extends AbstractController
{





    public function __construct(
		private ManagerRegistry $doctrine,
        private MailerService $mailer,
        private AuthentificationRepository $AuthRepo,
        private DevisRepository $DeviRepo,
        private AbonnementsRepository $AbonneRepo,
        private ConsommationRepository $ConsoRepo,
        private ImpayeesRepository $ImpayRepo,
        private FacturesRepository $factorRepo,
		private JWTEncoderInterface $JWTManager
		) {}



    

    #[Route('/ws/agonline/dashboard/info/{police}/', methods:['GET'])]
    public function dashboardAction(Request $request,$police = "0")
    {


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "ERROR";

        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("abonnements"=>array(),"consoElec"=>array(),"totalImp"=>0,"consoEau"=>array(),"consoMt"=>array(),"derniereFacture"=>"","gerance"=>"","adresse"=>"");

        //--------- Verifier la validité du JWT Token -----------//

        try{

            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];

            if(empty($refClient) || empty($cinClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{

                //$databaseService = new DataBaseService($this->conn);
				$adresse = "";
				$gerance = "";
				$gerancePol = "";

				//------ préparation des données
				/*if($police == "0"){
					$abonnements = $databaseService->getAbonnementsByCin($cinClient);

					$responseObjects["abonnements"] = $abonnements;

					//------- get liste actualité --------//

					if(count($abonnements) > 0){
						$police = $abonnements[0]["POLICE"];
						$gerancePol = $abonnements[0]["GERANCE"];
						if($abonnements[0]["GERANCE"] == "ELEC")
							$gerance = "EAU";
						else
							$gerance = "ELEC";

						$adresse = $abonnements[0]["ADRESSE"];

					}
					else{
						$police = null;
					}
				}
				else{
					$verifyClient = $databaseService->verifyPolice($cinClient, $police);
					if($verifyClient){
						$abonnement = $databaseService->getOneAbonnement($police);
						if($abonnement){
							$gerancePol = $abonnement["GERANCE"];
							if($abonnement["GERANCE"] == "ELEC")
								$gerance = "EAU";
							else
								$gerance = "ELEC";

							$adresse = $abonnement["ADRESSE"];
						}
						else{
							$police = null;
						}
					}
					else{
						$police = null;
					}
				}*/

                $verifyClient = $this->AuthRepo->verifyPolice($cinClient, $police);
                if($verifyClient){
                    $abonnement = $this->AbonneRepo->getOneAbonnement($police);
                    if($abonnement){
                        $gerancePol = $abonnement["GERANCE"];
                        if($abonnement["GERANCE"] == "ELEC")
                            $gerance = "EAU";
                        else
                            $gerance = "ELEC";

                        $adresse = $abonnement["ADRESSE"];
                    }
                    else{
                        $police = null;
                    }
                }
                else{
                    $police = null;
                }

				$responseObjects["gerance"] = $gerancePol;
				$responseObjects["adresse"] = $adresse;


                //-------- preparer l'historique 1ere police (Active) -------//

                $consoElec = array();
                $consoEau = array();
                $montantPolice = array();
                //$montants = array();
                $totalImp = 0;
				$moisExist = array();
				$in = 0;
				$derniereFacture = null;

				if($police){
					$historique = $this->factorRepo->getConso($police);
                    $totalImp = $this->ImpayRepo->getSumImpaye($police);

                    for($i=0;$i<count($historique);$i++){
                        $mo = $historique[$i]["MOIS"];
						$ann = $historique[$i]["ANNEE"];
                        $mois = "";

                        switch ($mo) {
                            case 1:
                                $mois = "Janvier";
                                break;
                            case 2:
                                $mois = "Fevrier";
                                break;
                            case 3:
                                $mois = "Mars";
                                break;
                            case 4:
                                $mois = "Avril";
                                break;
                            case 5:
                                $mois = "Mai";
                                break;
                            case 6:
                                $mois = "Juin";
                                break;
                            case 7:
                                $mois = "Juillet";
                                break;
                            case 8:
                                $mois = "Aout";
                                break;
                            case 9:
                                $mois = "Septembre";
                                break;
                            case 10:
                                $mois = "Octobre";
                                break;
                            case 11:
                                $mois = "Novembre";
                                break;
                            case 12:
                                $mois = "Decembre";
                                break;
                        }

                        if(!in_array($mois.$ann, $moisExist)){
                            $moisExist[$in] = $mois.$ann;
                            $montantPolice[$in]["mois"] = $mois." ".$historique[$i]['ANNEE'];
                            $montantPolice[$in]["total"] = str_replace(",",".",$historique[$i]['MONTANT_TTC']);
                            if($gerancePol == "ELEC"){
                                $consoElec[$in]["mois"] = $mois." ".$historique[$i]['ANNEE'];
                                $consoElec[$in]["conso"] = $historique[$i]["CONSO"];

                            }
                            else{
                                $consoEau[$in]["mois"] = $mois." ".$historique[$i]['ANNEE'];
                                $consoEau[$in]["conso"] = $historique[$i]['CONSO'];

                            }

                            $in++;
                        }

						if($derniereFacture == null) $derniereFacture = $historique[$i]["DATE_FACTURE"];
						if($i == 12){
							break;
						}
                    }

                    //----- Jumelage des polices eau et elec ------//


					/*
                    $abon2 = $this->AbonneRepo->getAbonnementsJumlee($adresse,$cinClient,$gerance);
					if($abon2){
						$police2 = $abon2["POLICE"];
						$historique2 = $this->factorRepo->getConso($police2);


						$moisExist = array();
						$in = 0;
						for($i=0;$i<count($historique2);$i++){
							$mo = $historique2[$i]["MOIS"];
							$ann = $historique2[$i]["ANNEE"];
							$mois = "";

							switch ($mo) {
								case 1:
									$mois = "Janvier";
									break;
								case 2:
									$mois = "Fevrier";
									break;
								case 3:
									$mois = "Mars";
									break;
								case 4:
									$mois = "Avril";
									break;
								case 5:
									$mois = "Mai";
									break;
								case 6:
									$mois = "Juin";
									break;
								case 7:
									$mois = "Juillet";
									break;
								case 8:
									$mois = "Aout";
									break;
								case 9:
									$mois = "Septembre";
									break;
								case 10:
									$mois = "Octobre";
									break;
								case 11:
									$mois = "Novembre";
									break;
								case 12:
									$mois = "Decembre";
									break;
							}

							if(!in_array($mois.$ann, $moisExist)){
								$moisExist[$in] = $mois.$ann;
								if($gerance == "EAU"){

									$consoEau[$in]["mois"] = $mois." ".$historique2[$i]['ANNEE'];
									$consoEau[$in]["conso"] = $historique2[$i]['CONSO'];
								}
								else{

									$consoElec[$in]["mois"] = $mois." ".$historique2[$i]['ANNEE'];
									$consoElec[$in]["conso"] = $historique2[$i]['CONSO'];
								}

								$in++;
							}

							if($i == 12) break;
						}
					}*/

					$responseObjects["consoElec"] = $consoElec;
					$responseObjects["consoEau"] = $consoEau;
					$responseObjects["consoMt"] = $montantPolice;
					$responseObjects["totalImp"] = $totalImp;
					$responseObjects["derniereFacture"] = $derniereFacture;

					// $em = $this->getDoctrine()->getManager('customer');
                    $em = $this->doctrine->getManager();
					$actualites = $em->getRepository(Actualites::class)->findBy(array(),array("dateActu"=>"DESC"));

					$responseObjects["actualites"] = $actualites;


					$codeStatut = "OK";
				}
				else{
					$codeStatut = "ERROR-POLICE";
				}


            }
        }catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

            if($e->getMessage() == "Expired token"){
                $codeStatut = "ERROR-TOKEN";
            }
        }

        //-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }



    #[Route('/ws/agonline/faq/get/all/', methods:['GET'])]
    public function getAllFaqAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		 header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "ERROR";
		$message = "";
        //$jwt = $request->headers->get('Authorization');
        $responseObjects = array("faq"=>array());

        //--------- Verifier la validité du JWT Token -----------//

        try{

			// $em = $this->getDoctrine()->getManager('customer');
            $em = $this->doctrine->getManager();
			$faq = $em->getRepository(Faq::class)->findAll();

			$responseObjects["faq"] = $faq;

			$codeStatut = "OK";


        }catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

            if($e->getMessage() == "Expired token"){
                $codeStatut = "ERROR-TOKEN";
            }
        }

        //-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
		$resp["message"] = $message;
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }


    /*-----------------------------------------------*/
    /*-----------------------------------------------*/
    /* Fonctions pour la récupération des actualiés  */
    /*-----------------------------------------------*/
    /*-----------------------------------------------*/


    #[Route('/ws/agonline/actualites/list/', methods:['GET'])]
    public function listActualitesAction(Request $request)
    {

    	/*header("Access-Control-Allow-Origin: *");
		 header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("actualites"=>array());

    	//--------- Verifier la validité du JWT Token -----------//

    	try{

    		// $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];

            if(empty($refClient) || empty($cinClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{

            	// $em = $this->getDoctrine()->getManager('customer');
                $em = $this->doctrine->getManager();
				$actualites = $em->getRepository(Actualites::class)->findBy(array(),array("dateActu"=>"DESC"));

				$responseObjects["actualites"] = $actualites;


				$codeStatut = "OK";



            }
    	}catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

            if($e->getMessage() == "Expired token"){
                $codeStatut = "ERROR-TOKEN";
            }
        }

    	//-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }


    #[Route('/ws/agonline/actualites/get/{id}/', methods:['GET'])]
    public function getActualiteAction(Request $request,$id)
    {

    	/*header("Access-Control-Allow-Origin: *");
		 header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("actualite"=>array());

    	//--------- Verifier la validité du JWT Token -----------//

    	try{

    		// $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];

            if(empty($refClient) || empty($cinClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{

            	//$em = $this->getDoctrine()->getManager('customer');
                $em = $this->doctrine->getManager();
				$actualites = $em->getRepository(Actualites::class)->findOneById($id);

				$responseObjects["actualite"] = $actualites;


				$codeStatut = "OK";



            }
    	}catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

            if($e->getMessage() == "Expired token"){
                $codeStatut = "ERROR-TOKEN";
            }
        }

    	//-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }



    #[Route('/ws/agonline/agences/get/', methods:['GET'])]
    public function getAllAgencesAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("agences"=>array());

        //--------- Verifier la validité du JWT Token -----------//

        try{

            //$em = $this->getDoctrine()->getManager('customer');
            $em = $this->doctrine->getManager();
			$agences = $em->getRepository(Agences::class)->findAll();


			$responseObjects["agences"] = $agences;

			$codeStatut = "OK";


        }catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

        }

        //-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
		$resp["message"] = $message;
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }

    	/*--------------------------------------------------------------*/
    /*--------------------------------------------------------------*/
    /* Fonction pour récupération de l'historique des devis travaux */
    /*--------------------------------------------------------------*/
    /*--------------------------------------------------------------*/


    #[Route('/ws/agonline/devis/historiques/{police}/', methods:['GET'])]
    public function historiqueDevisAction(Request $request,$police)
    {

    	/*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


    	$response = new Response();
        $response->headers->set('Content-Type', 'application/json');

    	$codeStatut = "ERROR";

    	$jwt = $request->headers->get('Authorization');
    	$responseObjects = array("devis"=>array());

    	//--------- Verifier la validité du JWT Token -----------//

    	try{

    		// $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            $jwt = substr($jwt, 7);
            $decoded = $this->JWTManager->decode($jwt);


            $refClient = $decoded["REF"];
            $cinClient = $decoded["CIN"];

            if(empty($refClient) || empty($cinClient)){
                $codeStatut = "ERROR-EMPTY-PARAMS";
            }
            else{

            	//$databaseService = new DataBaseService($this->conn);

				$verifyClient = $this->AuthRepo->verifyPolice($cinClient, $police);
				if($verifyClient){
					$historique = $this->DeviRepo->getFacDevTravaux($police);
					$sum = $this->DeviRepo->getSumFacDevTravaux($police);
					$responseObjects["devis"] = $historique;
					$responseObjects["total"] = $sum["TOTAL"];
					$responseObjects["reste"] = $sum["RESTE"];

					$codeStatut = "OK";
				}
				else{
					$codeStatut = "ERROR-POLICE";
				}

            }
    	}catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

            if($e->getMessage() == "Expired token"){
                $codeStatut = "ERROR-TOKEN";
            }
        }

    	//-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }



    #[Route('/ws/agonline/communes/get/{gerance}/', methods:['GET'])]
    public function getAllCommunesAction(Request $request,$gerance)
    {

        /*header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "";
		$message = "";
        $jwt = $request->headers->get('Authorization');
        $responseObjects = array("communes"=>array());

        //--------- Verifier la validité du JWT Token -----------//

        try{

			$communes = array();

            // $em = $this->getDoctrine()->getManager('customer');
            $em = $this->doctrine->getManager();

			if($gerance == 1){
				$communes = $em->getRepository(Communes::class)->findByIsEau(1);
			}
			elseif($gerance == 2){
				$communes = $em->getRepository(Communes::class)->findByIsElec(1);
			}

			$responseObjects["communes"] = $communes;

			$codeStatut = "OK";


        }catch (\Exception $e) {

            $codeStatut = "ERROR-EXCEPETION---".$e->getMessage();

        }

        //-------------------------------------------------------//

        $resp["codeStatut"] = $codeStatut;
		$resp["message"] = $message;
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }
}