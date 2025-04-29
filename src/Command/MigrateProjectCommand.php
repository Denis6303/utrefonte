<?php

namespace App\Command;

use App\Migration\MigrationHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:migrate-project',
    description: 'Migre le projet Symfony 2 vers Symfony 7',
)]
class MigrateProjectCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('old-project-dir', InputArgument::REQUIRED, 'Chemin vers l\'ancien projet')
            ->addArgument('new-project-dir', InputArgument::REQUIRED, 'Chemin vers le nouveau projet');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $oldProjectDir = $input->getArgument('old-project-dir');
        $newProjectDir = $input->getArgument('new-project-dir');

        if (!is_dir($oldProjectDir)) {
            $io->error('Le répertoire de l\'ancien projet n\'existe pas');
            return Command::FAILURE;
        }

        if (!is_dir($newProjectDir)) {
            $io->error('Le répertoire du nouveau projet n\'existe pas');
            return Command::FAILURE;
        }

        $migrationHelper = new MigrationHelper($oldProjectDir, $newProjectDir);

        try {
            $io->section('Migration des entités');
            $migrationHelper->migrateEntities();
            $io->success('Entités migrées avec succès');

            $io->section('Migration des contrôleurs');
            $migrationHelper->migrateControllers();
            $io->success('Contrôleurs migrés avec succès');

            $io->section('Migration des templates');
            $migrationHelper->migrateTemplates();
            $io->success('Templates migrés avec succès');

            $io->success('Migration terminée avec succès');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Une erreur est survenue pendant la migration : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 