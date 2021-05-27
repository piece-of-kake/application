<?php

namespace PoK\Migrations;

use PoK\DBQueryBuilder\DBClientInterface;
use PoK\DBQueryBuilder\Queries\TableExists;
use PoK\DBQueryBuilder\Queries\CreateTable;
use PoK\DBQueryBuilder\Queries\Select;
use PoK\ValueObject\Collection;
use PoK\Helpers\FileHelper;
use PoK\DBQueryBuilder\Queries\Insert;

class MigrationManager
{
    /**
     * @var DBClientInterface
     */
    private $client;

    private $migrationsPath;

    public function __construct(string $migrationsPath)
    {
        $this->migrationsPath = $migrationsPath;
    }

    public function runMigrations(DBClientInterface $client)
    {
        $this->client = $client;
        
        if (!$this->migrationsTableExists())
            $this->createMigrationsTable();
        
        $this->executeAvailableMigrations();
    }
    
    private function migrationsTableExists()
    {
        return $this->client->execute(new TableExists('migrations'));
    }
    
    private function createMigrationsTable()
    {
        $query = (new CreateTable('migrations'))
            ->fields(function(CreateTable $table) {
                $table->field('migration')->string()->size(255)->notNull();
            });
        $this->client->execute($query);
    }
    
    private function executeAvailableMigrations()
    {
        $migrationFiles = $this->excludeExecutedMigrationFiles(
            $this->getMigrationFileNames()
        );
            
        $migrationFiles
            ->each(function ($fileName) {
                require_once $fileName;
                $className = $this->getMigrationClassNameFromFileName($fileName);
                $migration = new $className($this->client);
                $migration->commit();
                $this->client->execute(
                    (new Insert('migrations'))
                        ->fields('migration')
                        ->addValueRow(basename($fileName, '.php'))
                );
            });
    }
    
    private function excludeExecutedMigrationFiles($migrationFiles)
    {
        $executedMigrations = $this->getExecutedMigrationNames();
            
        $executedMigrations
            ->map(function ($executedMigration) {
                return $executedMigration['migration'];
            })
            ->each(function ($executedMigration) use (&$migrationFiles) {
                $migrationFiles = $migrationFiles->skip(function ($migrationFile) use ($executedMigration) {
                    return $executedMigration === basename($migrationFile, '.php');
                });
            });
            
        return $migrationFiles;
    }
    
    private function getExecutedMigrationNames()
    {
        return $this->client->execute(new Select('migrations'));
    }
    
    private function getMigrationFileNames()
    {
        return new Collection(FileHelper::getDirectoryFiles($this->migrationsPath, '*.php'));
    }
    
    private function getMigrationClassNameFromFileName($fileName)
    {
        $fileName = basename($fileName, '.php');
        $nameParts = new Collection(explode('_', $fileName));
        
        $className = $nameParts
            ->skipFirst()
            ->map(function ($part) {
                return ucfirst($part);
            })
            ->implode('');
            
        return $className;
    }
}
