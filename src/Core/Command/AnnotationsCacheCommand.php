<?php

namespace App\Core\Command;

use App\Core\Cache\AnnotationsCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AnnotationsCacheCommand extends Command
{
    protected static $defaultName = 'app:cache:annotations';
    private AnnotationsCache $annotationsCache;

    public function __construct(AnnotationsCache $annotationsCache)
    {
        parent::__construct();
        $this->annotationsCache = $annotationsCache;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Warm up of annotations cache')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->annotationsCache->getAnnotations(true);
        $io->success('Annotations cache is created.');

        return Command::SUCCESS;
    }
}
