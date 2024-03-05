<?php 
// src/EventListener/ExceptionListener.php
namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function onExecuteView(KernelEvent $event): void
    {
        $connection = $this->entityManager->getConnection();
        try {
            $createViewStatement = $connection->prepare('CREATE VIEW v_dossier AS
            SELECT d.* FROM dossier d INNER JOIN logs_actions l ON d.id = l.id_dossier_id
            INNER JOIN actions_import a ON l.id_action_id = a.id INNER JOIN import i ON a.id_import_id = i.id WHERE i.etat IN (3, 4);');
            $createViewStatement->execute();
        } catch (\Exception $th) {
            
        }
    }
}