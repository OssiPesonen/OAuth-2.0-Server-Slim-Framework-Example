<?php

namespace App\Persistence;

use Doctrine\DBAL\Connection;
use Psr\Container\ContainerInterface;

abstract class BaseModel {
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Connection|mixed 
     */
    protected $connection;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->connection = $this->container->get(Connection::class);
    }
}
