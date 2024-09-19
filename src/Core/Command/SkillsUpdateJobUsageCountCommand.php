<?php

namespace App\Core\Command;

use App\Core\Entity\Skill;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SkillsUpdateJobUsageCountCommand extends Command
{
    protected static $defaultName = 'app:core:skills:update-job-usage-count';
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Update jobUsageCount of each skill')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('App - Core - Update jobUsageCount of each skill');

        $data = $this->em->getRepository(Skill::class)->countJobPostingsGroupBySkill();
        $dataCount = \count($data);

        $skillsUpdatedCount = 0;

        $io->progressStart($dataCount);

        foreach ($data as $d) {
            /** @var Skill $skill */
            $skill = $d['skill'];
            $count = $d['count'];

            $skill->setJobUsageCount($count);
            ++$skillsUpdatedCount;

            $io->progressAdvance();
        }

        $this->em->flush();
        $io->progressFinish();

        $io->success("$skillsUpdatedCount skills updated");

        return Command::SUCCESS;
    }
}
