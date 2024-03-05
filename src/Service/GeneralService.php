<?php


namespace App\Service;

use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Repository\IntegrationExtraction\Integration\integrationRepo;

class GeneralService
{
    private  $integrationRepo;
    public function __construct(integrationRepo $integrationRepo,)
    {
        $this->integrationRepo = $integrationRepo;
    }
    public function dateStart($date)
    {
        $dateTime = new \DateTime($date);                        
        $formattedDate = $dateTime->format('Y-m-d')." 00:00:0";

        return $formattedDate;
    }
    public function dateEnd($date)
    {
        $dateTime = new \DateTime($date);                        
        $formattedDate = $dateTime->format('Y-m-d')." 23:59:59";
        
        return $formattedDate;
    }
    public function checkDateDebutDatefin($date_debut , $date_fin)
    {
        if($date_debut <= $date_fin){
            return true;
        }
        return false;
    }
    function updateStatus($status, $id, $expectedStatus, $integrationRepo)
    {
        $integration = $integrationRepo->findIntegration($id);

        if ($integration) {
            if ($integration->getStatus()->getId() == $expectedStatus) {
                $integrationRepo->updateStatus($id, $status);
                return "OK";
            } else {
                return "ERROR";
            }
        } else {
            return "NOT_EXIST_ELEMENT";
        }
    }

    
}