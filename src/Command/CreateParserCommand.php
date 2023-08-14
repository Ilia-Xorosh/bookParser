<?php

namespace App\Command;

use App\Controller\ParserController;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;


// the name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'app:create-parser',
    description: 'Start parser.',
    hidden: false,
)]
class CreateParserCommand extends Command
{
    public EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to get a list of books from a json file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $parser = new ParserController;
        $count = $parser->getFile($this->entityManager);
        $output->writeln([
            'The parser worked',
            '============',
            $count . ' added',
        ]);

        return Command::SUCCESS;
    }
}
