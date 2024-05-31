<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Repository\DossiersRepository;
use GuzzleHttp\ClientInterface;


class CreateNoteDossierCommand extends Command
{
    private $dossiersRepo;
    protected $client;

    public function __construct(DossiersRepository $dossiersRepo)
    {
        parent::__construct();

        $this->dossiersRepo = $dossiersRepo;
    }

    protected function configure()
    {
        $this
            ->setName('app:create-note-dossier')
            ->setDescription('Create a note for a dossier.');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = $this->client->request('POST', 'https://localhost:8000/API/integration/test_cron');
        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {
            $output->writeln('API call successful');
        } else {
            $output->writeln('API call failed');
        }
        return Command::SUCCESS;
    }

}
