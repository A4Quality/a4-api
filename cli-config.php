<?php
/**
 * Created by PhpStorm.
 * User: r.a.freitas
 * Date: 03/09/2018
 * Time: 14:52
 */

use App\Config\Doctrine;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once __DIR__ . '/src/Config/Doctrine.php';
$doctrine = new Doctrine();
$entityManager = $doctrine->getEntityManager();
return ConsoleRunner::createHelperSet($entityManager);