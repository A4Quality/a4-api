<?php

namespace App\Config;
use App\Connection\Credentials;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use DoctrineExtensions\Query\Mysql\Acos;
use DoctrineExtensions\Query\Mysql\Cos;
use DoctrineExtensions\Query\Mysql\sin;
use DoctrineExtensions\Query\Mysql\Radians;
class Doctrine
{
    private $paths = [__DIR__ . '/../Basics'];
    private $isDevMode = true;
    private $dbParams;

    /**
     * Doctrine constructor.
     */
    public function __construct()
    {
        $this->dbParams = [
            'host' => Credentials::getHost(),
            'driver' => Credentials::getDriver(),
            'charset' => Credentials::getCharset(),
            'user' => Credentials::getUser(),
            'password' => Credentials::getPassword(),
            'dbname' => Credentials::getDbname(),
        ];
    }

    function getEntityManager()
    {
        $config = Setup::createAnnotationMetadataConfiguration(
            $this->paths,
            $this->isDevMode
        );
        $config->addCustomStringFunction('acos', Acos::class);
        $config->addCustomStringFunction('cos', Cos::class);
        $config->addCustomStringFunction('sin', Sin::class);
        $config->addCustomStringFunction('radians', Radians::class);
        $entityManager = EntityManager::create($this->dbParams, $config);
        return $entityManager;
    }
}
