<?php

namespace App\Core\Command;

use App\Core\Entity\Location;
use App\Core\Manager\LocationManager;
use App\Core\Util\Arrays;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GeocodeCommand extends Command
{
    protected static $defaultName = 'app:core:geocode';
    private LocationManager $lm;

    public const MODE_DEFAULT = 'default';
    public const MODE_API = 'api';
    public const MODE_DATABASE = 'database';

    public function __construct(LocationManager $lm)
    {
        parent::__construct();
        $this->lm = $lm;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('value', InputArgument::REQUIRED, 'The string to geocode')
            ->addOption(
                'mode',
                null,
                InputOption::VALUE_REQUIRED,
                'Which mode do you want?',
                self::MODE_DEFAULT
            )
            ->setDescription('Geocoding of a string')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('App - Core - Geocoding of a string');

        $value = $input->getArgument('value');
        $modes = [self::MODE_DEFAULT, self::MODE_API, self::MODE_DATABASE];
        $mode = $input->getOption('mode');

        if (!\in_array($mode, $modes, true)) {
            $io->error(sprintf('"%s" is not a valid mode. ["%s"]', $mode, implode('","', $modes)));

            return Command::FAILURE;
        }

        $io->info([
            "Value: '$value'",
            "Mode: $mode",
        ]);

        if (self::MODE_DEFAULT === $mode) {
            $location = $this->lm->searchInDatabase($value) ?? Arrays::first($this->lm->autocompleteMobilities($value));
        } elseif (self::MODE_API === $mode) {
            $location = Arrays::first($this->lm->autocompleteMobilities($value));
        } else {
            $location = $this->lm->searchInDatabase($value);
        }

        if ($location instanceof Location) {
            $label = $location->getLabel();
            $key = $location->getKey();
            $io->success([
                'Location geocoded',
                "Label: $label",
                "Key: $key",
            ]);
        } else {
            $io->error('Location not geocoded');
        }

        return Command::SUCCESS;
    }
}
