<?php
// src/Command/CheckEventStatusCommand.php
namespace App\Command;
use App\Event\EventCheckEvent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
#[AsCommand(
    name: 'app:check-event-status',
    description: 'Vérifie et met à jour le statut des événements en fonction de
leur date',
)]
class CheckEventStatusCommand extends Command
{
    private $eventDispatcher;
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct();
        $this->eventDispatcher = $eventDispatcher;
    }
    protected function execute(InputInterface $input, OutputInterface $output):
    int
    {
        $output->writeln('Vérification des statuts des événements...');
// Déclenche l'événement EventCheckEvent
        $this->eventDispatcher->dispatch(new EventCheckEvent(),
            EventCheckEvent::NAME);
        $output->writeln('Vérification terminée !');
        return Command::SUCCESS;
    }
}