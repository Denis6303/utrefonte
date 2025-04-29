<?php

namespace App\Migration;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class MigrationHelper
{
    private string $oldProjectDir;
    private string $newProjectDir;
    private Filesystem $filesystem;

    public function __construct(string $oldProjectDir, string $newProjectDir)
    {
        $this->oldProjectDir = $oldProjectDir;
        $this->newProjectDir = $newProjectDir;
        $this->filesystem = new Filesystem();
    }

    public function migrateEntities(): void
    {
        // Copier et adapter les entités
        $finder = new Finder();
        $finder->files()->in($this->oldProjectDir . '/src/utb/*/Entity')->name('*.php');

        foreach ($finder as $file) {
            $content = $file->getContents();
            
            // Adapter le namespace
            $content = $this->updateNamespace($content);
            
            // Mettre à jour les annotations vers les attributs
            $content = $this->convertAnnotationsToAttributes($content);
            
            // Sauvegarder dans le nouveau projet
            $newPath = $this->newProjectDir . '/src/Entity/' . $file->getFilename();
            $this->filesystem->dumpFile($newPath, $content);
        }
    }

    public function migrateControllers(): void
    {
        // Copier et adapter les contrôleurs
        $finder = new Finder();
        $finder->files()->in($this->oldProjectDir . '/src/utb/*/Controller')->name('*.php');

        foreach ($finder as $file) {
            $content = $file->getContents();
            
            // Adapter le namespace
            $content = $this->updateNamespace($content);
            
            // Mettre à jour les annotations vers les attributs
            $content = $this->convertAnnotationsToAttributes($content);
            
            // Sauvegarder dans le nouveau projet
            $newPath = $this->newProjectDir . '/src/Controller/' . $file->getFilename();
            $this->filesystem->dumpFile($newPath, $content);
        }
    }

    public function migrateTemplates(): void
    {
        // Copier les templates Twig
        $finder = new Finder();
        $finder->files()->in($this->oldProjectDir . '/src/utb/*/Resources/views')->name('*.html.twig');

        foreach ($finder as $file) {
            $content = $file->getContents();
            
            // Adapter les chemins des templates
            $content = $this->updateTemplatePaths($content);
            
            // Sauvegarder dans le nouveau projet
            $relativePath = $file->getRelativePath();
            $newPath = $this->newProjectDir . '/templates/' . $relativePath . '/' . $file->getFilename();
            $this->filesystem->dumpFile($newPath, $content);
        }
    }

    private function updateNamespace(string $content): string
    {
        // Remplacer les anciens namespaces par les nouveaux
        return preg_replace(
            '/namespace utb\\\\.*?\\\\/',
            'namespace App\\',
            $content
        );
    }

    private function convertAnnotationsToAttributes(string $content): string
    {
        // Convertir les annotations Doctrine en attributs PHP 8
        $replacements = [
            '/@ORM\\\\Entity/' => '#[ORM\Entity]',
            '/@ORM\\\\Table\((.*?)\)/' => '#[ORM\Table($1)]',
            '/@ORM\\\\Column\((.*?)\)/' => '#[ORM\Column($1)]',
            '/@ORM\\\\Id/' => '#[ORM\Id]',
            '/@ORM\\\\GeneratedValue/' => '#[ORM\GeneratedValue]',
            '/@Route\((.*?)\)/' => '#[Route($1)]'
        ];

        return preg_replace(
            array_keys($replacements),
            array_values($replacements),
            $content
        );
    }

    private function updateTemplatePaths(string $content): string
    {
        // Mettre à jour les chemins des templates pour la nouvelle structure
        return preg_replace(
            '/\@utb.*?\:/i',
            '',
            $content
        );
    }
} 