<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

class SetupCommand extends Command {

    private $pdoSessionHandler;
    private $em;

    public function __construct(EntityManagerInterface $em, PdoSessionHandler $pdoSessionHandler, string $name = null) {
        parent::__construct($name);

        $this->em = $em;
        $this->pdoSessionHandler = $pdoSessionHandler;
    }

    public function configure() {
        parent::configure();

        $this
            ->setName('app:setup')
            ->setDescription('Sets up the application.');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $style = new SymfonyStyle($input, $output);

        $this->setupSessions($style);

        return 0;
    }

    private function setupSessions(SymfonyStyle $style) {
        $sql = "SHOW TABLES LIKE 'sessions';";
        $row = $this->em->getConnection()->executeQuery($sql);

        if($row->fetch() === false) {
            $this->pdoSessionHandler->createTable();
        }

        $style->success('Sessions table ready.');
    }
}