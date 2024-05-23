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
    public function yearStart($year)
    {
        $dateTime = new \DateTime("$year-01-01");
        $formattedDate = $dateTime->format('Y-01-01 H:i:s');

        return $formattedDate;
    }

    public function yearEnd($year)
    {
        $dateTime = new \DateTime("$year-12-31");
        $formattedDate = $dateTime->format('Y-12-31 H:i:s');

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
    public function getNextStep(array $workflowData, string $cleEvent)
    {
        $nextStep = null;
        $foundCurrent = false;

        foreach ($workflowData as $index => $component) {
            // Check if this is the current component
            if ($component['id'] === $cleEvent) {
                $foundCurrent = true;
                // Check if there is a next component in the sequence
                if (isset($workflowData[$index + 1])) {
                    $nextStep = $workflowData[$index + 1];
                }
                break;
            }

            // If the component is a switch, check its branches
            if (isset($component['branches'])) {
                foreach ($component['branches'] as $branchComponents) {
                    foreach ($branchComponents as $branchComponent) {
                        if ($branchComponent['id'] === $cleEvent) {
                            $foundCurrent = true;
                            // Check if there is a next component in the branch sequence
                            if (isset($branchComponents[$index + 1])) {
                                $nextStep = $branchComponents[$index + 1];
                            }
                            break 3; // Exit all loops
                        }
                    }
                }
            }
        }

        if (!$foundCurrent) {
            // Handle case where current component is not found (e.g., error or end of workflow)
            throw new \Exception("Current component with id $cleEvent not found in workflow data.");
        }
        return $nextStep;
    }


    public function getPreviousStep(array $workflowData, string $cleEvent)
    {
        $previousStep = null;
        $foundCurrent = false;

        foreach ($workflowData as $index => $component) {
            // Check if this is the current component
            if ($component['id'] === $cleEvent) {
                $foundCurrent = true;
                // Check if there is a previous component in the sequence
                if (isset($workflowData[$index - 1])) {
                    $previousStep = $workflowData[$index - 1];
                }
                break;
            }

            // If the component is a switch, check its branches
            if (isset($component['branches'])) {
                foreach ($component['branches'] as $branchComponents) {
                    foreach ($branchComponents as $branchIndex => $branchComponent) {
                        if ($branchComponent['id'] === $cleEvent) {
                            $foundCurrent = true;
                            // Check if there is a previous component in the branch sequence
                            if (isset($branchComponents[$branchIndex - 1])) {
                                $previousStep = $branchComponents[$branchIndex - 1];
                            }
                            break 3; // Exit all loops
                        }
                    }
                }
            }
        }

        if (!$foundCurrent) {
            // Handle case where current component is not found (e.g., error or end of workflow)
            throw new \Exception("Current component with id $cleEvent not found in workflow data.");
        }

        return $previousStep;
    }

}