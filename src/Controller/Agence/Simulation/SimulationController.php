<?php

namespace App\Controller\Agence\Simulation;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
class SimulationController extends AbstractController
{


	

	private $contactEmail = "agence.radeej.regie@gmail.com";//reclamation.agence.radeej
	private $passwordEmail = "etuwduyiklfnmgtx";
	private $smtpEmail = 'smtp.googlemail.com';



	// public function __construct(private ManagerRegistry $doctrine) {}
	public function __construct(
		private ManagerRegistry $doctrine,
		private JWTEncoderInterface $JWTManager
		) {}

    //------------------- simulation de la consommation -------------------//
	

    #[Route('/ws/agonline/simulation/consommation/', methods: ['POST'])]
    public function simulationConsoAction(Request $request)
    {

        /*header("Access-Control-Allow-Origin: *");
		 header("Access-Control-Allow-Headers: Authorization");*/


        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $codeStatut = "ERROR";
		$message = "";
        //$jwt = $request->headers->get('Authorization');
        $responseObjects = array("conso"=>array());

        //--------- Verifier la validité du JWT Token -----------//

        try{

            $gerance = $request->get("gerance");
				
			$index = $request->get("indexConso");
			$usage = $request->get("usage");
				
				
			if(empty($gerance) || empty($index)){
				$codeStatut = "ERR-EMPTY";
				$message = "Un des champs obligatoires est vide ! ";
			}
			else{
				if($gerance == "EAU"){
					$commune = $request->get("commune");
					
					if(empty($commune) || empty($usage)){
						$codeStatut = "ERR-EMPTY";
						$message = "Un des champs obligatoires est vide ! ";
					}
					else{
						$responseObjects['conso'] = $this->calcEau($index,$commune,$usage);
						$codeStatut = "OK";
					}
				}
				else{
					$responseObjects['conso'] = $this->calcElec($index,$usage);
					$codeStatut = "OK";
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
		$resp["message"] = $message;
        $resp["objects"] = $responseObjects;

        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
        JsonEncoder()));
        $responseSerialise = $serializer->serialize($resp, 'json');

        $response->setContent($responseSerialise);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;

    }

    function calcEau($index,$commune,$usage){

		$tranchesEau = array();

		// $arraEau['eau'] = null;

	   $totale = 0;

	   $prixEau = [
			[12.744,10.069,6.966,7.693,12.744,12.690,8.325,8.325,3.306],
			[12.401,9.855,7.265,7.982,12.401,12.337,7.511,7.511,2.643],
			[10.839,8.774,6.795,7.501,10.839,10.775,6.581,6.581,2.643],
			[11.545,8.967,6.078,6.645,11.545,11.481,7.116,7.116,2.696]];

		$prixEauAssaiRelatif = array(0.5885,1.5301,2.9211,3.2528);
		$prixEauAssaiConst = 42.8;

		$comm = trim($commune);



		if($comm == "EL JADIDA" || $comm == "MY ABDELLAH"  || $comm == "HAOUZIA"  || $comm == "OULED HCINE" || $comm == "SIDI ABED"){
		   $iCom=0;
		}
		elseif($comm == "AZEMMOUR" || $comm == "BENHAMDOUCHE" || $comm == "OULED RAHMOUNE" || $comm == "OULED FREJ" || $comm == "METTOUH"){
		   $iCom=1;
		}
		elseif($comm == "BIR JDID" || $comm == "CHTOUKA" || $comm == "LAGHDIRA" || $comm == "LAGHNADRA" || $comm == "EL MECHREK" || $comm == "MHARZA SAHEL" || $comm == "OULED SBAITA" || $comm == "SEBT SAISS" || 
			$comm == "SIDI BENNOUR" || $comm == "SIDI SMAIL" || $comm == "ZAOUIAT SAISS" || $comm == "ZEMAMRA"){
		   $iCom=2;
		}
		elseif($comm == "EL OUALIDIA" || $comm == "EL GHARBIA" || $comm == "OULED GHANEM"  ){

		   $iCom=3;
		}

		$tranchesEau = array();
		$totaleEau = 0;
		$conceEauStr = "Consommation de l'eau en m3 (Tranche ";
		$conceAssStr = "Assainissement Tranche ";

		if(strpos($usage, "domestique") !== false){

			if (0 <= $index && $index <= 6) {
				$totaleEau =  $index*$prixEau[$iCom][8];
				$totale +=  $totaleEau;

				$tranchesEau[0] = array($conceEauStr."1)",$index,$prixEau[$iCom][8],round($index*$prixEau[$iCom][8],2));
				$tranchesEau[1] = array("Sous Total","","",round($totaleEau,2));
			}
			elseif (7 <= $index && $index <= 12) {
				$totaleEau =  6*$prixEau[$iCom][8]+($index-6)*$prixEau[$iCom][7];
				$totale +=  $totaleEau;


				$tranchesEau[0] = array($conceEauStr."1)",6,$prixEau[$iCom][8],round(6*$prixEau[$iCom][8],2));
				$tranchesEau[1] = array($conceEauStr."2)",($index-6),$prixEau[$iCom][7],round(($index-6)*$prixEau[$iCom][7],2));
				$tranchesEau[2] = array("Sous Total","","",round($totaleEau,2));
			}
			elseif (13 <= $index && $index <= 20) {
				$totaleEau =  ($index)*$prixEau[$iCom][6];
				$totale +=  $totaleEau;


				$tranchesEau[0] = array($conceEauStr."3)",($index),$prixEau[$iCom][6],round(($index)*$prixEau[$iCom][6],2));
				$tranchesEau[1] = array("Sous Total","","",round($totaleEau,2));

			}
			elseif (21 <= $index && $index <= 35) {
				$totaleEau =  ($index)*$prixEau[$iCom][5];
				$totale +=  $totaleEau;


				$tranchesEau[0] = array($conceEauStr."4)",($index),$prixEau[$iCom][5],round(($index)*$prixEau[$iCom][5],2));
				$tranchesEau[1] = array("Sous Total","","",round($totaleEau,2));

			}
			elseif (35 < $index ) {

				$totaleEau =  ($index)*$prixEau[$iCom][4];
				$totale +=  $totaleEau;


				$tranchesEau[0] = array($conceEauStr."5)",($index),$prixEau[$iCom][4],round(($index)*$prixEau[$iCom][4],2));
				$tranchesEau[1] = array("Sous Total","","",round($totaleEau,2));


			}
		}
		elseif(strpos($usage, "preferentielle") !== false){
			$totale += $index*$prixEau[$iCom][3];
			$totale +=  $totaleEau;


			$tranchesEau[0] = array($conceEauStr."1)",$index,$prixEau[$iCom][3],round($index*$prixEau[$iCom][3],2));
			$tranchesEau[1] = array("Sous Total","","",round($totaleEau,2));
		}
		elseif(strpos($usage, "industrielle") !== false){
			$totale += $index*$prixEau[$iCom][2];
			$totale +=  $totaleEau;

			$tranchesEau[0] = array($conceEauStr."1)",$index,$prixEau[$iCom][2],round($index*$prixEau[$iCom][2],2));
			$tranchesEau[1] = array("Sous Total","","",round($totaleEau,2));

		}
		elseif(strpos($usage, "hoteliere") !== false){
			$totale += $index*$prixEau[$iCom][1];
			$totale +=  $totaleEau;


			$tranchesEau[0] = array($conceEauStr."1)",$index,$prixEau[$iCom][1],round($index*$prixEau[$iCom][1],2));
			$tranchesEau[1] = array("Sous Total","","",round($totaleEau,2));

		}
		elseif(strpos($usage, "administrative") !== false){
			$totale += $index*$prixEau[$iCom][0];
			$totale +=  $totaleEau;

			$tranchesEau[0] = array($conceEauStr."1)",$index,$prixEau[$iCom][0],round($index*$prixEau[$iCom][0],2));
			$tranchesEau[1] = array("Sous Total","","",round($totaleEau,2));
		}

		$arraEau['eau'] = $tranchesEau;


		$tranchesAssai = array();
		$tranchesFixe = array();
		$totaleAssai = 0;
		$totaleFixe = 0;
		//l'assainissenet liquide
		//relatif
		if(strpos($usage, "domestique") !== false){
			if (0 <= $index && $index <= 6) {
				$totaleAssai =  $index*$prixEauAssaiRelatif[0];
				$totale +=  $totaleAssai;


				$tranchesAssai[0] = array($conceAssStr."1",$index,$prixEauAssaiRelatif[0],round($index*$prixEauAssaiRelatif[0],2));
				$tranchesAssai[1] = array("Sous Total","","",round($totaleAssai,2));
			}
			elseif (7 <= $index && $index <= 20) {
				$totaleAssai = 6*$prixEauAssaiRelatif[0] +($index-6)*$prixEauAssaiRelatif[1];
				$totale +=  $totaleAssai;

				$tranchesAssai[0] = array($conceAssStr."1",6,$prixEauAssaiRelatif[0],round(6*$prixEauAssaiRelatif[0],2));
				$tranchesAssai[1] = array($conceAssStr."2",($index-6),$prixEauAssaiRelatif[1],round(($index-6)*$prixEauAssaiRelatif[1],2));
				$tranchesAssai[2] = array("Sous Total","","",round($totaleAssai,2));
			}
			elseif (20 < $index ) {
				$totaleAssai =  6*$prixEauAssaiRelatif[0]+14*$prixEauAssaiRelatif[1]+($index-6-14)*$prixEauAssaiRelatif[2];
				$totale +=  $totaleAssai;

				$tranchesAssai[0] = array($conceAssStr."1",6,$prixEauAssaiRelatif[0],round(6*$prixEauAssaiRelatif[0],2));
				$tranchesAssai[1] = array($conceAssStr."2",14,$prixEauAssaiRelatif[1],round(14*$prixEauAssaiRelatif[1],2));
				$tranchesAssai[2] = array($conceAssStr."3",($index-6-14),$prixEauAssaiRelatif[2],round(($index-6-14)*$prixEauAssaiRelatif[2],2));
				$tranchesAssai[3] = array("Sous Total","","",round($totaleAssai,2));
			}
		}
		else{
			$totaleAssai =  $index*$prixEauAssaiRelatif[3];
			$totale +=  $totaleAssai;


			$tranchesAssai[0] = array($conceAssStr.")",$index,$prixEauAssaiRelatif[3],round($index*$prixEauAssaiRelatif[3],2));
			$tranchesAssai[1] = array("Sous Total","","",round($totaleAssai,2));
		}

		$arraEau['asss'] = $tranchesAssai;

		//constant
		//$totaleFixe = ($prixEauAssaiConst/12);
		//$totale += $totaleFixe;

		//$tranchesFixe[0] = array("Redevance Fixes Assainissement","","",3.57);
		//$tranchesFixe[1] = array("Sous Total","","",round($totaleFixe,2));

		//$arraEau['Taxes'] = $tranchesFixe;


		$arraEau['TotalEau'] = round($totale,2);


		return $arraEau;

	}

    	//------------------------ Calcul eléctricité---------------------------
	function calcElec($index,$usage){

		$prixELec = [
				[0.9010,1.0732,1.0732,1.1676,1.3817,1.5958],
				[1.5146,1.7090]];
		$prixTPPAN = array(0.1,0.15,0.2);
		$tranchesElec = array();


		$totale = 0;
		$conceElecStr = "Consommation de l'electricité en Kwh (Tranche ";
		if(strpos($usage, "domestique") !== false){
			if (0 <= $index && $index <= 100) {

				$totale =  $index*$prixELec[0][0];

				$totale = round($totale,2);
				$tranchesElec[0] = array($conceElecStr."1)",$index,$prixELec[0][0],$totale);
				$tranchesElec[1] = array("Sous Total","","",round($totale,2));
			}
			elseif (101 <= $index && $index <= 150) {
				$totale =  100*$prixELec[0][0]+($index-100)*$prixELec[0][1];

				$totale = round($totale,2);
				$tranchesElec[0] = array($conceElecStr."1)",100,$prixELec[0][0],round(100*$prixELec[0][0],2));
				$tranchesElec[1] = array($conceElecStr."2)",($index-100),$prixELec[0][1],round(($index-100)*$prixELec[0][1],2));
				$tranchesElec[2] = array("Sous Total","","",round($totale,2));
			}
			elseif (151 <= $index && $index <= 210) {
				$totale =  ($index)*$prixELec[0][2];

				$totale = round($totale,2);
				$tranchesElec[0] = array($conceElecStr."3)",($index),$prixELec[0][2],round(($index)*$prixELec[0][2],2));
				$tranchesElec[1] = array("Sous Total","","",round($totale,2));
			}
			elseif (211 <= $index && $index <= 310) {
				$totale = ($index)*$prixELec[0][3];


				$totale = round($totale,2);
				$tranchesElec[0] = array($conceElecStr."4)",$index,$prixELec[0][3],round(($index)*$prixELec[0][3],2));
				$tranchesElec[1] = array("Sous Total","","",round($totale,2));
			}
			elseif (311 <= $index && $index <= 510) {
				$totale =  ($index)*$prixELec[0][4];

				$totale = round($totale,2);
				$tranchesElec[0] = array($conceElecStr."5)",$index,$prixELec[0][4],round(($index)*$prixELec[0][4],2));
				$tranchesElec[1] = array("Sous Total","","",round($totale,2));
			}
			elseif (510 < $index) {
				$totale =  ($index)*$prixELec[0][5];


				$totale = round($totale,2);
				$tranchesElec[0] = array($conceElecStr."6)",$index,$prixELec[0][5],round(($index)*$prixELec[0][5],2));
				$tranchesElec[1] = array("Sous Total","","",round($totale,2));
			}
		}
		else{
			if (0 <= $index && $index <= 150) {

				$totale =  $index*$prixELec[1][0];

				$totale = round($totale,2);
				$tranchesElec[0] = array($conceElecStr."1)",$index,$prixELec[1][0],$totale);
				$tranchesElec[1] = array("Sous Total","","",round($totale,2));
			}
			elseif ($index >= 151) {
				$totale =  150*$prixELec[1][0]+($index-150)*$prixELec[1][1];

				$totale = round($totale,2);
				$tranchesElec[0] = array($conceElecStr."1)",150,$prixELec[1][0],round(150*$prixELec[1][0],2));
				$tranchesElec[1] = array($conceElecStr."2)",($index-150),$prixELec[1][1],round(($index-150)*$prixELec[1][1],2));
				$tranchesElec[2] = array("Sous Total","","",round($totale,2));
			}
		}

		$arraEau['elec'] = $tranchesElec;
		
		//----------------- calcul montant de la taxe TPPAN ---------------------//
		

		$totalTPPAN = 0;
		if($index > 200){
			$t1 = 100*$prixTPPAN[0];
			$t2 = 100*$prixTPPAN[1];
			$reste = $index-200;
			
			$t3 = $reste*$prixTPPAN[2];
			
			$totalTPPAN = $t1+$t2+$t3;
			if($totalTPPAN > 100)
				$totalTPPAN = 100;
		}
		
		$arraEau['TPPAN'] = round($totalTPPAN,2);
		
		$arraEau['TotalElec'] = round($totale+$totalTPPAN,2);

		return  $arraEau;


	}



}
