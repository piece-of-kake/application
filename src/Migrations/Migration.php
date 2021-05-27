<?php

namespace PoK\Migrations;

use PoK\DBQueryBuilder\DBClientInterface;
use PoK\DBQueryBuilder\Interfaces\CanCompile;

abstract class Migration
{

    /**
     * @var DBClientInterface
     */
    private $client;

    public function __construct(DBClientInterface $client)
    {
        $this->client = $client;
    }

    public function commit()
    {
        $this->client->execute($this->up());
    }

    public function rollback()
    {
        $this->client->execute($this->down());
    }

    protected abstract function up(): CanCompile;

    protected abstract function down(): CanCompile;
}
